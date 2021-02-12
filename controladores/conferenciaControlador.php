<?php

require_once './modelos/conferenciaModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';

class conferenciaControlador extends conferenciaModelo
{

    public function agregar_conferencia_controlador($Conferencia)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Conferencia->setDescripcion(mainModel::limpiar_cadena($Conferencia->getDescripcion()));
            $Conferencia->setTitulo(mainModel::limpiar_cadena($Conferencia->getTitulo()));
            $Conferencia->setLink(mainModel::limpiar_cadena($Conferencia->getLink()));
            $Conferencia->setFecha(mainModel::limpiar_cadena($Conferencia->getFecha()));
            $Conferencia->setEstado(mainModel::limpiar_cadena($Conferencia->getEstado()));

            if (isset($_FILES['txtImagenConferencia'])) {

                $lconferencia = conferenciaModelo::datos_conferencia_modelo($this->conexion_db, "fecha-add", $Conferencia);
                if ($lconferencia["countFilter"] > 0) {
                    $insBeanCrud->setMessageServer("ya se encuentra una conferencia con la fecha ingresada");
                } else {
                    $original = $_FILES['txtImagenConferencia'];
                    $nombre = $original['name'];
                    $permitido = array("image/png", "image/jpg", "image/jpeg");

                    $limit_kb = 1700;
                    if ($original['error'] > 0) {
                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                    } else {
                        $resultado = mainModel::archivo($permitido, $limit_kb, $original, $nombre, "./adjuntos/conferencia/");
                        if ($resultado != "") {
                            $Conferencia->setImagen($resultado);
                            $stmt = conferenciaModelo::agregar_conferencia_modelo($this->conexion_db, $Conferencia);
                            if ($stmt->execute()) {

                                $stmt = $this->conexion_db->prepare("INSERT INTO `notificacion`
                                (descripcion,rango_inicial,rango_final,fecha,tipo)
                                 VALUES(?,?,?,?,?)");
                                $stmt->bindValue(1, $Conferencia->getDescripcion(), PDO::PARAM_STR);
                                $stmt->bindValue(2, $Conferencia->getTitulo(), PDO::PARAM_STR);
                                $stmt->bindValue(3, $Conferencia->getLink(), PDO::PARAM_STR);
                                $stmt->bindValue(4, $Conferencia->getFecha(), PDO::PARAM_STR);
                                $stmt->bindValue(5, 2, PDO::PARAM_INT);

                                if ($stmt->execute()) {

                                    $this->conexion_db->commit();
                                    $insBeanCrud->setMessageServer("ok");
                                    $insBeanCrud->setBeanPagination(self::paginador_conferencia_controlador($this->conexion_db, 0, 20));
                                } else {
                                    $insBeanCrud->setMessageServer("No hemos podido registrar la notificacion");
                                }

                            } else {
                                unlink('./adjuntos/conferencia/' . $resultado);
                                $insBeanCrud->setMessageServer("No hemos podido registrar el conferencia");
                            }
                            $stmt->closeCursor();
                            $stmt = null;
                        } else {
                            $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido, cambie el nombre de la imagen o seleccione otra imagen");
                        }

                    }

                }

            } else {
                $insBeanCrud->setMessageServer("Seccione imagen");
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
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());
    }
    public function datos_conferencia_controlador($tipo, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);
            $insBeanCrud->setBeanPagination(conferenciaModelo::datos_conferencia_modelo($this->conexion_db, $tipo, $codigo));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();

    }
    public function paginador_conferencia_controlador($conexion, $inicio, $registros)
    {
        $insBeanPagination = new BeanPagination();
        try {

            $stmt = $conexion->query("SELECT COUNT(idconferencia) AS CONTADOR FROM `conferencia`");
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT * FROM `conferencia` ORDER BY idconferencia DESC LIMIT ?,?");
                    $stmt->bindValue(1, $inicio, PDO::PARAM_INT);
                    $stmt->bindValue(2, $registros, PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insConferencia = new Conferencia();
                        $insConferencia->setIdconferencia($row['idconferencia']);
                        $insConferencia->setTitulo($row['titulo']);
                        $insConferencia->setLink($row['link']);
                        $insConferencia->setFecha($row['fecha']);
                        $insConferencia->setDescripcion($row['descripcion']);
                        $insConferencia->setEstado($row['estado']);
                        $insConferencia->setImagen($row['imagen']);
                        $insBeanPagination->setList($insConferencia->__toString());
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
    public function bean_paginador_conferencia_controlador($pagina, $registros)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_conferencia_controlador($this->conexion_db, $inicio, $registros));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_conferencia_controlador($Conferencia)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $conferencia = conferenciaModelo::datos_conferencia_modelo($this->conexion_db, "unico", $Conferencia);
            if ($conferencia["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra el conferencia");
            } else {
                $stmt = conferenciaModelo::eliminar_conferencia_modelo($this->conexion_db, mainModel::limpiar_cadena($Conferencia->getIdconferencia()));

                if ($stmt->execute()) {
                    if (($conferencia["list"][0]['imagen'] != "") || ($conferencia["list"][0]['imagen'] != null)) {
                        unlink('./adjuntos/conferencia/' . $conferencia["list"][0]['imagen']);
                    }
                    $stmt = $this->conexion_db->prepare("DELETE FROM `notificacion` WHERE fecha=:Codigo and tipo=2");
                    $stmt->bindValue(":Codigo", $conferencia["list"][0]['fecha']);
                    if ($stmt->execute()) {

                        $this->conexion_db->commit();
                        $insBeanCrud->setMessageServer("ok");

                    } else {
                        $insBeanCrud->setMessageServer("No hemos podido eliminar la notificación");
                    }

                    //  $insBeanCrud->setBeanPagination(self::paginador_conferencia_controlador($this->conexion_db, 0, 20));

                } else {
                    $insBeanCrud->setMessageServer("No se eliminó el conferencia");
                }
                $stmt->closeCursor();
                $stmt = null;
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
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());

    }
    public function actualizar_conferencia_controlador($Conferencia)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Conferencia->setDescripcion(mainModel::limpiar_cadena($Conferencia->getDescripcion()));
            $Conferencia->setLink(mainModel::limpiar_cadena($Conferencia->getLink()));
            $Conferencia->setTitulo(mainModel::limpiar_cadena($Conferencia->getTitulo()));
            $Conferencia->setFecha(mainModel::limpiar_cadena($Conferencia->getFecha()));
            $Conferencia->setEstado(mainModel::limpiar_cadena($Conferencia->getEstado()));

            $Conferencia->setIdconferencia(mainModel::limpiar_cadena($Conferencia->getIdconferencia()));
            $lconferencia = conferenciaModelo::datos_conferencia_modelo($this->conexion_db, "unico", $Conferencia);
            if ($lconferencia["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra el conferencia");
            } else {
                $xconferencia = conferenciaModelo::datos_conferencia_modelo($this->conexion_db, "fecha-update", $Conferencia);
                if ($xconferencia["countFilter"] > 0) {
                    $insBeanCrud->setMessageServer("ya se encuentra una conferencia con la fecha ingresada");
                } else {
                    if (isset($_FILES['txtImagenConferencia'])) {
                        $original = $_FILES['txtImagenConferencia'];
                        $nombre = $original['name'];
                        $permitido = array("image/png", "image/jpg", "image/jpeg");

                        $limit_kb = 1700;

                        if ($original['error'] > 0) {
                            $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                        } else {
                            $resultado = mainModel::archivo($permitido, $limit_kb, $original, $nombre, "./adjuntos/conferencia/");
                            if ($resultado != "") {
                                $Conferencia->setImagen($resultado);
                                $stmt = conferenciaModelo::actualizar_conferencia_modelo($this->conexion_db, $Conferencia);

                                if ($stmt->execute()) {

                                    if (($lconferencia["list"][0]['imagen'] != "") || ($lconferencia["list"][0]['imagen'] != null)) {
                                        unlink('./adjuntos/conferencia/' . $lconferencia["list"][0]['imagen']);
                                    }

                                    $stmt = $this->conexion_db->prepare("DELETE FROM `notificacion` WHERE fecha=:Codigo and tipo=2");
                                    $stmt->bindValue(":Codigo", $lconferencia["list"][0]['fecha']);
                                    if ($stmt->execute()) {
                                        $stmt = $this->conexion_db->prepare("INSERT INTO `notificacion`
                                    (descripcion,rango_inicial,rango_final,fecha,tipo)
                                     VALUES(?,?,?,?,?)");
                                        $stmt->bindValue(1, $Conferencia->getDescripcion(), PDO::PARAM_STR);
                                        $stmt->bindValue(2, $Conferencia->getTitulo(), PDO::PARAM_STR);
                                        $stmt->bindValue(3, $Conferencia->getLink(), PDO::PARAM_STR);
                                        $stmt->bindValue(4, $Conferencia->getFecha(), PDO::PARAM_STR);
                                        $stmt->bindValue(5, 2, PDO::PARAM_INT);

                                        if ($stmt->execute()) {
                                            $this->conexion_db->commit();
                                            $insBeanCrud->setMessageServer("ok");

                                        } else {
                                            $insBeanCrud->setMessageServer("No hemos podido actualizar la notificación");
                                        }
                                    } else {
                                        $insBeanCrud->setMessageServer("No hemos podido eliminar la notificación");
                                    }

                                    //  $insBeanCrud->setBeanPagination(self::paginador_conferencia_controlador($this->conexion_db, 0, 20));

                                } else {
                                    unlink('./adjuntos/conferencia/' . $resultado);
                                    $insBeanCrud->setMessageServer("No hemos podido actualizar el conferencia");
                                }
                                $stmt->closeCursor();
                                $stmt = null;
                            } else {
                                $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido");

                            }
                        }
                    } else {
                        $Conferencia->setImagen($lconferencia["list"][0]['imagen']);
                        $stmt = conferenciaModelo::actualizar_conferencia_modelo($this->conexion_db, $Conferencia);
                        if ($stmt->execute()) {
                            $stmt = $this->conexion_db->prepare("DELETE FROM `notificacion` WHERE fecha=:Codigo and tipo=2");
                            $stmt->bindValue(":Codigo", $lconferencia["list"][0]['fecha']);
                            if ($stmt->execute()) {
                                $stmt = $this->conexion_db->prepare("INSERT INTO `notificacion`
                            (descripcion,rango_inicial,rango_final,fecha,tipo)
                             VALUES(?,?,?,?,?)");
                                $stmt->bindValue(1, $Conferencia->getDescripcion(), PDO::PARAM_STR);
                                $stmt->bindValue(2, $Conferencia->getTitulo(), PDO::PARAM_STR);
                                $stmt->bindValue(3, $Conferencia->getLink(), PDO::PARAM_STR);
                                $stmt->bindValue(4, $Conferencia->getFecha(), PDO::PARAM_STR);
                                $stmt->bindValue(5, 2, PDO::PARAM_INT);

                                if ($stmt->execute()) {
                                    $this->conexion_db->commit();
                                    $insBeanCrud->setMessageServer("ok");
                                    # code...
                                } else {
                                    $insBeanCrud->setMessageServer("No hemos podido actualizar la notificación");
                                }
                            } else {
                                $insBeanCrud->setMessageServer("No hemos podido eliminar la notificación");
                            }
                        } else {
                            $insBeanCrud->setMessageServer("No hemos podido actualizar el conferencia");
                        }
                        $stmt->closeCursor();
                        $stmt = null;
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
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());
    }
}
