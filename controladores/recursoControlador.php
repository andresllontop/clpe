<?php

require_once './modelos/recursoModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';

require_once './classes/principal/subtitulo.php';
class recursoControlador extends recursoModelo
{
    public function agregar_recurso_controlador($Recurso)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Recurso->setNombre(mainModel::limpiar_cadena($Recurso->getNombre()));
            $Recurso->setSubTitulo(mainModel::limpiar_cadena($Recurso->getSubTitulo()));
            $Recurso->setDisponible(mainModel::limpiar_cadena($Recurso->getDisponible()));

            if (isset($_FILES['txtImagenRecurso'])) {
                $original = $_FILES['txtImagenRecurso'];
                $nombre = $original['name'];
                $permitido = array("image/png", "image/jpg", "image/jpeg");
                $destino = "IMAGENES";
                $Limit_KB = 900 * 1024;

                if ($original['error'] > 0) {
                    $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                } else {
                    $resultado_guardado = mainModel::archivo($permitido, $Limit_KB, $original, $nombre, "./adjuntos/recurso/" . $destino . "/");
                    if ($resultado_guardado != "") {
                        $Recurso->setImagen($resultado_guardado);
                        $stmt = recursoModelo::agregar_recurso_modelo($this->conexion_db, $Recurso);
                        if ($stmt->execute()) {
                            $this->conexion_db->commit();
                            $insBeanCrud->setMessageServer("ok");
                            $insBeanCrud->setBeanPagination(self::paginador_recurso_controlador($this->conexion_db, 0, 5));

                        } else {

                            $insBeanCrud->setMessageServer("error en el servidor, No hemos podido registrar el recurso");
                        }
                    } else {
                        $insBeanCrud->setMessageServer("error en el servidor, Hubo un error al guardar la imagen");

                    }

                }
            } else {
                $insBeanCrud->setMessageServer("error en el servidor, ingrese imagen");
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
    public function datos_recurso_controlador($tipo, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);
            $insBeanCrud->setBeanPagination(recursoModelo::datos_recurso_modelo($this->conexion_db, $tipo, $codigo));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();

    }
    public function paginador_recurso_controlador($conexion, $inicio, $registros, $libro)
    {
        $insBeanPagination = new BeanPagination();
        try {
            $stmt = $conexion->prepare("SELECT COUNT(idrecurso) AS CONTADOR FROM `recurso` WHERE  codigo_subtitulo LIKE CONCAT('%',?,'%')");
            $stmt->bindParam(1, $libro, PDO::PARAM_STR);
            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT b.*,s.nombre AS nombre_subtitulo,s.idsubtitulo FROM `recurso` AS b inner join `subtitulo` AS s WHERE b.codigo_subtitulo=s.codigo_subtitulo AND (b.codigo_subtitulo LIKE CONCAT('%',?,'%')) ORDER BY  b.codigo_subtitulo  ASC LIMIT ?,?");

                    $stmt->bindParam(1, $libro, PDO::PARAM_STR);
                    $stmt->bindParam(2, $inicio, PDO::PARAM_INT);
                    $stmt->bindParam(3, $registros, PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();

                    foreach ($datos as $row) {

                        $insSubTitulo = new SubTitulo();
                        $insSubTitulo->setIdSubTitulo($row['idsubtitulo']);
                        $insSubTitulo->setNombre($row['nombre_subtitulo']);
                        $insSubTitulo->setCodigo($row['codigo_subtitulo']);

                        $insRecurso = new Recurso();
                        $insRecurso->setIdRecurso($row['idrecurso']);
                        $insRecurso->setImagen($row['imagen']);
                        $insRecurso->setNombre($row['nombre']);
                        $insRecurso->setDisponible($row['disponible']);

                        $insRecurso->setSubTitulo($insSubTitulo->__toString());
                        $insBeanPagination->setList($insRecurso->__toString());
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
    public function bean_paginador_recurso_controlador($pagina, $registros, $libro)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $libro = mainModel::limpiar_cadena($libro);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_recurso_controlador($this->conexion_db, $inicio, $registros, $libro));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_recurso_controlador($Recurso)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Recurso->setIdRecurso(mainModel::limpiar_cadena($Recurso->getIdRecurso()));

            $listarecurso = recursoModelo::datos_recurso_modelo($this->conexion_db, "unico", $Recurso);
            if ($listarecurso["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("error en el servidor, No se encuentra registrado el recurso");

            } else {
                $recurso = recursoModelo::datos_recurso_modelo($this->conexion_db, "detalle", $Recurso);
                if ($recurso["countFilter"] == 0) {

                    $stmt = recursoModelo::eliminar_recurso_modelo($this->conexion_db, $Recurso->getIdRecurso());
                    if ($stmt->execute()) {
                        $this->conexion_db->commit();
                        unlink('./adjuntos/recurso/IMAGENES/' . $listarecurso["list"][0]['imagen']);

                        $insBeanCrud->setMessageServer("ok");
                        $insBeanCrud->setBeanPagination(self::paginador_recurso_controlador($this->conexion_db, 0, 5));

                    } else {
                        $insBeanCrud->setMessageServer("error en el servidor, no se puede elminar el recurso");
                    }

                } else {
                    $insBeanCrud->setMessageServer("No se puede eliminar el recurso porque contiene archivos, primero elimina los archivos.");
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
    public function actualizar_recurso_controlador($Recurso)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Recurso->setNombre(mainModel::limpiar_cadena($Recurso->getNombre()));
            $Recurso->setSubTitulo(mainModel::limpiar_cadena($Recurso->getSubTitulo()));
            $Recurso->setDisponible(mainModel::limpiar_cadena($Recurso->getDisponible()));
            $Recurso->setIdRecurso(mainModel::limpiar_cadena($Recurso->getIdRecurso()));

            $recurso = recursoModelo::datos_recurso_modelo($this->conexion_db, "unico", $Recurso);
            if ($recurso["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("error en el servidor, No se encuentra registrado el recurso");
            } else {
                if (isset($_FILES['txtImagenRecurso'])) {
                    $original = $_FILES['txtImagenRecurso'];
                    $nombre = $original['name'];
                    $permitido = array("image/png", "image/jpg", "image/jpeg");
                    $destino = "IMAGENES";
                    $Limit_KB = 4 * 1024;
                    if ($original['error'] > 0) {
                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir la imagen, seleccione otro imagen");
                    } else {
                        $resultado_guardado = mainModel::archivo($permitido, $Limit_KB, $original, $nombre, "./adjuntos/recurso/" . $destino . "/");
                        if ($resultado_guardado != "") {
                            $Recurso->setImagen($resultado_guardado);
                            $stmt = recursoModelo::actualizar_recurso_modelo($this->conexion_db, $Recurso);
                            if ($stmt->execute()) {

                                unlink('./adjuntos/recurso/IMAGENES/' . $recurso["list"][0]['imagen']);

                                $this->conexion_db->commit();
                                $insBeanCrud->setMessageServer("ok");
                                $insBeanCrud->setBeanPagination(self::paginador_recurso_controlador($this->conexion_db, 0, 5));

                            } else {

                                $insBeanCrud->setMessageServer("error en el servidor, No hemos podido actualizar el recurso");
                            }
                        } else {
                            $insBeanCrud->setMessageServer("error en el servidor, Hubo un error al guardar la imagen");

                        }

                    }
                } else {
                    $Recurso->setImagen($recurso["list"][0]['imagen']);
                    $stmt = recursoModelo::actualizar_recurso_modelo($this->conexion_db, $Recurso);
                    if ($stmt->execute()) {
                        $this->conexion_db->commit();
                        $insBeanCrud->setMessageServer("ok");
                        $insBeanCrud->setBeanPagination(self::paginador_recurso_controlador($this->conexion_db, 0, 5));

                    } else {

                        $insBeanCrud->setMessageServer("error en el servidor, No hemos podido actualizar el recurso");
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
