<?php
require_once './modelos/subitemModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';

class subitemControlador extends subitemModelo
{
    public function agregar_subitem_controlador($Subitem)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Subitem->setTitulo(mainModel::limpiar_cadena($Subitem->getTitulo()));
            $Subitem->setTipo(mainModel::limpiar_cadena($Subitem->getTipo()));
            $Subitem->setCurso(mainModel::limpiar_cadena($Subitem->getCurso()));
            if (isset($_FILES['txtImagenObjetivo'])) {
                $original = $_FILES['txtImagenObjetivo'];
                $nombre = $original['name'];

                if ($original['error'] > 0) {
                    $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                } else {
                    //10 MB
                    $resultado = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"), 10 * 1024, $original, $nombre, "./adjuntos/slider/");
                    if ($resultado != "") {
                        $Subitem->setImagen($resultado);
                        $stmt = subitemModelo::agregar_subitem_modelo($this->conexion_db, $Subitem);
                        if ($stmt->execute()) {
                            $this->conexion_db->commit();
                            $insBeanCrud->setMessageServer("ok");
                            if ($Subitem->getCurso() == "") {
                                $insBeanCrud->setBeanPagination(self::paginador_subitem_controlador($this->conexion_db, 0, 5, $Subitem->getTipo()));
                            } else {
                                $insBeanCrud->setBeanPagination(subitemModelo::datos_subitem_modelo($this->conexion_db, $tipo, $codigo));
                            }

                        } else {
                            $insBeanCrud->setMessageServer("error en el servidor, No hemos podido registrar los datos");
                        }
                    } else {
                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, No hemos podido subir la imagen");
                    }
                }

            } else {
                $Subitem->setImagen("");
                $stmt = subitemModelo::agregar_subitem_modelo($this->conexion_db, $Subitem);
                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    if ($Subitem->getCurso() == "") {
                        $insBeanCrud->setBeanPagination(self::paginador_subitem_controlador($this->conexion_db, 0, 20, (int) $Subitem->getTipo()));
                    } else {
                        $insBeanCrud->setBeanPagination(subitemModelo::datos_subitem_modelo($this->conexion_db, "curso", $Subitem));
                    }

                } else {
                    $insBeanCrud->setMessageServer("error en el servidor, No hemos podido registrar los datos");
                }
            }
        } catch (Exception $th) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
                $stmt = null;
            }
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());
    }
    public function datos_subitem_controlador($tipo, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);

            $insBeanCrud->setBeanPagination(subitemModelo::datos_subitem_modelo($this->conexion_db, $tipo, $codigo));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function paginador_subitem_controlador($conexion, $inicio, $registros, $codigo, $curso = -1)
    {
        $insBeanPagination = new BeanPagination();
        try {

            if ($codigo == 0) {
                if ($curso > 0) {
                    $stmt = $conexion->prepare("SELECT COUNT(idsubitem) AS CONTADOR  FROM `subitem` where idcurso=?");
                    $stmt->bindParam(1, $curso, PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        $insBean = array();
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT sub.titulo as sub_titulo,sub.detalle,sub.item,sub.idsubitem,cur.* FROM `subitem` AS sub INNER JOIN `curso` AS cur ON cur.idcurso=sub.idcurso where sub.idcurso=? ORDER BY sub.idsubitem ASC LIMIT ?,?");
                            $stmt->bindParam(1, $curso, PDO::PARAM_INT);
                            $stmt->bindParam(2, $inicio, PDO::PARAM_INT);
                            $stmt->bindParam(3, $registros, PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();

                            foreach ($datos as $row) {

                                $insSubItem = new SubItem();
                                $insSubItem->setIdsubitem($row['idsubitem']);
                                $insSubItem->setTitulo($row['sub_titulo']);
                                $insSubItem->setDetalle($row['detalle']);
                                $insSubItem->setImagen($row['presentacion']);
                                $insSubItem->setVideo($row['video']);
                                $insSubItem->setTipo($row['item']);
                                $insBeanPagination->setList($insSubItem->__toString());
                                $insBean = array("descuento" => $row['precio_descuento'],
                                    "tipo" => $row['tipo'],
                                    "imagenlibro" => $row['imagen'],
                                    "precio" => $row['precio'],
                                    "descripcion" => $row['descripcion'],
                                    "idcurso" => $row['idcurso'],
                                    "titulo" => $row['titulo'],
                                );
                            }
                            $insBeanPagination->setList($insBean);
                        }
                    }
                } else {
                    $stmt = $conexion->query("SELECT COUNT(idsubitem) AS CONTADOR  FROM `subitem` ");
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `subitem` ORDER BY idsubitem ASC LIMIT ?,?");
                            $stmt->bindParam(1, $inicio, PDO::PARAM_INT);
                            $stmt->bindParam(2, $registros, PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();

                            foreach ($datos as $row) {

                                $insSubItem = new SubItem();
                                $insSubItem->setIdsubitem($row['idsubitem']);
                                $insSubItem->setTitulo($row['titulo']);
                                $insSubItem->setDetalle($row['detalle']);
                                $insSubItem->setImagen($row['imagen']);
                                $insSubItem->setTipo($row['item']);
                                $insBeanPagination->setList($insSubItem->__toString());
                            }
                        }
                    }
                }

            } else {
                $stmt = $conexion->prepare("SELECT COUNT(idsubitem) AS CONTADOR  FROM `subitem` WHERE item=? ");
                $stmt->bindParam(1, $codigo, PDO::PARAM_INT);
                $stmt->execute();
                $datos = $stmt->fetchAll();
                foreach ($datos as $row) {
                    $insBeanPagination->setCountFilter($row['CONTADOR']);

                    if ($row['CONTADOR'] > 0) {

                        $stmt = $conexion->prepare("SELECT * FROM `subitem` WHERE item=?  ORDER BY idsubitem ASC LIMIT ?,?");
                        $stmt->bindParam(1, $codigo, PDO::PARAM_INT);
                        $stmt->bindParam(2, $inicio, PDO::PARAM_INT);
                        $stmt->bindParam(3, $registros, PDO::PARAM_INT);
                        $stmt->execute();
                        $datos = $stmt->fetchAll();

                        foreach ($datos as $row) {

                            $insSubItem = new SubItem();
                            $insSubItem->setIdsubitem($row['idsubitem']);
                            $insSubItem->setTitulo($row['titulo']);
                            $insSubItem->setDetalle($row['detalle']);
                            $insSubItem->setImagen($row['imagen']);
                            $insSubItem->setTipo($row['item']);
                            $insBeanPagination->setList($insSubItem->__toString());
                        }

                    }
                }

            }

            $stmt->closeCursor(); // this is not even required
            $stmt = null; // doing this is mandatory for connection to get closed

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";
        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";
        }
        return $insBeanPagination->__toString();

    }
    public function bean_paginador_subitem_controlador($pagina, $registros, $codigo, $curso = -1)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $codigo = mainModel::limpiar_cadena($codigo);
            $curso = mainModel::limpiar_cadena($curso);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_subitem_controlador($this->conexion_db, $inicio, $registros, $codigo, $curso));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_subitem_controlador($Subitem)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Subitem->setIdsubitem(mainModel::limpiar_cadena($Subitem->getIdsubitem()));

            $libro = subitemModelo::datos_subitem_modelo($this->conexion_db, "unico", $Subitem);
            if ($libro["countFilter"] == 0) {

                $insBeanCrud->setMessageServer("error en el servidor, No hemos encontrado el dato");
            } else {
                $stmt = subitemModelo::eliminar_subitem_modelo($this->conexion_db, $Subitem->getIdsubitem());
                if ($stmt->execute()) {
                    if ($libro["list"][0]['imagen'] != "") {
                        unlink('./adjuntos/slider/' . $libro["list"][0]['imagen']);
                    }

                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_subitem_controlador($this->conexion_db, 0, 20, $libro["list"][0]['tipo']));

                    if ($libro["list"][0]['curso'] == "" || $libro["list"][0]['curso'] == null) {
                        $insBeanCrud->setBeanPagination(self::paginador_subitem_controlador($this->conexion_db, 0, 20, $libro["list"][0]['tipo']));
                    } else {
                        $insSubItemClass = new SubItem();
                        $insSubItemClass->setTipo($libro["list"][0]['tipo']);
                        $insSubItemClass->setCurso($libro["list"][0]['curso']);
                        $insBeanCrud->setBeanPagination(subitemModelo::datos_subitem_modelo($this->conexion_db, "curso", $insSubItemClass));
                    }

                } else {
                    $insBeanCrud->setMessageServer("Ocurrio un error inesperado, No hemos podido eliminar el dato");
                }
            }
        } catch (Exception $th) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
                $stmt = null;
            }
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());

    }
    public function actualizar_subitem_controlador($Subitem)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Subitem->setTitulo(mainModel::limpiar_cadena($Subitem->getTitulo()));
            $Subitem->setTipo(mainModel::limpiar_cadena($Subitem->getTipo()));
            $Subitem->setIdsubitem(mainModel::limpiar_cadena($Subitem->getIdsubitem()));
            $Subitem->setCurso(mainModel::limpiar_cadena($Subitem->getCurso()));
            $libro = subitemModelo::datos_subitem_modelo($this->conexion_db, "unico", $Subitem);

            if ($libro["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("error en el servidor, No hemos encontrado el dato");
            } else {
                if (isset($_FILES['txtImagenObjetivo'])) {
                    $original = $_FILES['txtImagenObjetivo'];
                    $nombre = $original['name'];

                    if ($original['error'] > 0) {
                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                    } else {
                        //10 MB
                        $resultado = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"), 10 * 1024, $original, $nombre, "./adjuntos/slider/");
                        if ($resultado != "") {
                            $Subitem->setImagen($resultado);
                            $stmt = subitemModelo::actualizar_subitem_modelo($this->conexion_db, $Subitem);
                            if ($stmt->execute()) {
                                unlink('./adjuntos/slider/' . $libro["list"][0]['imagen']);
                                $this->conexion_db->commit();
                                $insBeanCrud->setMessageServer("ok");

                                if ($Subitem->getCurso() == "") {
                                    $insBeanCrud->setBeanPagination(self::paginador_subitem_controlador($this->conexion_db, 0, 20, $Subitem->getTipo(), -1));
                                } else {
                                    $insBeanCrud->setBeanPagination(subitemModelo::datos_subitem_modelo($this->conexion_db, "curso", $Subitem));
                                }
                            } else {
                                $insBeanCrud->setMessageServer("error en el servidor, No hemos podido actualizar los datos");
                            }
                        } else {
                            $insBeanCrud->setMessageServer("Ocurrio un error inesperado, No hemos podido subir la imagen");
                        }
                    }

                } else {
                    $Subitem->setImagen($libro["list"][0]['imagen']);
                    $stmt = subitemModelo::actualizar_subitem_modelo($this->conexion_db, $Subitem);
                    if ($stmt->execute()) {
                        $this->conexion_db->commit();
                        $insBeanCrud->setMessageServer("ok");

                        if ($Subitem->getCurso() == "") {
                            $insBeanCrud->setBeanPagination(self::paginador_subitem_controlador($this->conexion_db, 0, 20, $Subitem->getTipo(), -1));
                        } else {
                            $insBeanCrud->setBeanPagination(subitemModelo::datos_subitem_modelo($this->conexion_db, "curso", $Subitem));
                        }

                    } else {
                        $insBeanCrud->setMessageServer("error en el servidor, No hemos podido actualizar los datos");
                    }
                }
            }
        } catch (Exception $th) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
                $stmt = null;
            }
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());

    }

}
