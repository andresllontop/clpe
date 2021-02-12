<?php

require_once './modelos/convocatoriaModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';

require_once './classes/principal/titulo.php';

class convocatoriaControlador extends convocatoriaModelo
{
    public function agregar_convocatoria_controlador($BeanConvocatoria)
    {

        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Convocatoria = new Convocatoria();
            $Convocatoria->setDescripcion(mainModel::limpiar_cadena($BeanConvocatoria->getConvocatoria()->descripcion));
            $Convocatoria->setCodigo(mainModel::limpiar_cadena($BeanConvocatoria->getConvocatoria()->codigo));
            $Convocatoria->setEstado(mainModel::limpiar_cadena($BeanConvocatoria->getConvocatoria()->estado));
            $Convocatoria->setCantidad(mainModel::limpiar_cadena($BeanConvocatoria->getConvocatoria()->cantidad));
            $Convocatoria->setFecha(mainModel::limpiar_cadena($BeanConvocatoria->getConvocatoria()->fecha));

            if (isset($_FILES['txtImagenConvocatoria'])) {
                $original = $_FILES['txtImagenConvocatoria'];
                $nombre = $original['name'];
                if ($original['error'] > 0) {
                    $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                } else {
                    $resultado = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"), 5700, $original, $nombre, "./adjuntos/convocatoria/");
                    if ($resultado != "") {
                        $Convocatoria->setImagen($resultado);
                        $stmt = convocatoriaModelo::agregar_convocatoria_modelo($this->conexion_db, $Convocatoria);
                        if ($stmt->execute()) {
                            $stmt = $this->conexion_db->prepare("SELECT MAX(idconvocatoria) AS CONTADOR FROM `convocatoria`");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                if ($row['CONTADOR'] > 0) {
                                    $stmt = $this->conexion_db->prepare("INSERT INTO `detalle_convocatoria`(descripcion,idconvocatoria) VALUES(?,?)");
                                    $valor = false;
                                    foreach ($BeanConvocatoria->getListDetalle() as $lista) {
                                        $stmt->bindValue(1, $lista->descripcion, PDO::PARAM_STR);
                                        $stmt->bindValue(2, $row['CONTADOR'], PDO::PARAM_STR);
                                        $valor = $stmt->execute();
                                    }
                                    if ($valor) {
                                        $this->conexion_db->commit();
                                        $insBeanCrud->setMessageServer("ok");
                                        $insBeanCrud->setBeanPagination(self::paginador_convocatoria_controlador($this->conexion_db, 0, 20));
                                    }
                                }
                            }
                        } else {
                            $insBeanCrud->setMessageServer("No se puede registrar el cuestionario.");
                        }
                    } else {
                        $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido");
                    }
                }
            } else {
                $insBeanCrud->setMessageServer("no ingresaste Imagen");
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
    public function datos_convocatoria_controlador($tipo, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);

            $insBeanCrud->setBeanPagination(convocatoriaModelo::datos_convocatoria_modelo($this->conexion_db, $tipo, $codigo));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();

    }
    public function paginador_convocatoria_controlador($conexion, $inicio, $registros)
    {
        $insBeanPagination = new BeanPagination();
        try {
            $stmt = $conexion->prepare("SELECT COUNT(idconvocatoria) AS CONTADOR FROM `convocatoria`");
            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT * FROM `convocatoria`  ORDER BY fecha ASC LIMIT ?,?");
                    $stmt->bindValue(1, $inicio, PDO::PARAM_INT);
                    $stmt->bindValue(2, $registros, PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insConvocatoria = new Convocatoria();
                        $insConvocatoria->setIdConvocatoria($row['idconvocatoria']);
                        $insConvocatoria->setDescripcion($row['descripcion']);
                        $insConvocatoria->setCantidad($row['cantidad']);
                        $insConvocatoria->setCodigo($row['codigo']);
                        $insConvocatoria->setEstado($row['estado']);
                        $insConvocatoria->setFecha($row['fecha']);
                        $insConvocatoria->setImagen($row['imagen']);
                        $insBeanPagination->setList($insConvocatoria->__toString());
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
    public function bean_paginador_convocatoria_controlador($pagina, $registros)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_convocatoria_controlador($this->conexion_db, $inicio, $registros));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_convocatoria_controlador($Convocatoria)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Convocatoria->setIdConvocatoria(mainModel::limpiar_cadena($Convocatoria->getIdConvocatoria()));

            $lista = convocatoriaModelo::datos_convocatoria_modelo($this->conexion_db, "unico", $Convocatoria);
            $stmt = $this->conexion_db->prepare("DELETE FROM `detalle_convocatoria` WHERE idconvocatoria=:IDconvocatoria");
            $stmt->bindValue(":IDconvocatoria", $Convocatoria->getIdConvocatoria());
            if ($stmt->execute()) {
                $stmt = convocatoriaModelo::eliminar_convocatoria_modelo($this->conexion_db, $Convocatoria->getIdConvocatoria());
                if ($stmt->execute()) {
                    if ($lista["list"][0]['imagen'] != "" || $lista["list"][0]['imagen'] != null) {
                        unlink('./adjuntos/convocatoria/' . $lista["list"][0]['imagen']);
                    }
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_convocatoria_controlador($this->conexion_db, 0, 20));
                } else {
                    $insBeanCrud->setMessageServer("No hemos podido eliminar el cuestionario");
                }
            } else {
                $insBeanCrud->setMessageServer("No hemos podido eliminar las preguntas del cuestionario");
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
    public function actualizar_convocatoria_controlador($BeanConvocatoria)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Convocatoria = new Convocatoria();
            $Convocatoria->setDescripcion(mainModel::limpiar_cadena($BeanConvocatoria->getConvocatoria()->descripcion));
            $Convocatoria->setEstado(mainModel::limpiar_cadena($BeanConvocatoria->getConvocatoria()->estado));
            $Convocatoria->setCodigo(mainModel::limpiar_cadena($BeanConvocatoria->getConvocatoria()->codigo));
            $Convocatoria->setCantidad(mainModel::limpiar_cadena($BeanConvocatoria->getConvocatoria()->cantidad));
            $Convocatoria->setFecha(mainModel::limpiar_cadena($BeanConvocatoria->getConvocatoria()->fecha));

            $Convocatoria->setIdConvocatoria(mainModel::limpiar_cadena($BeanConvocatoria->getConvocatoria()->idconvocatoria));

            $convocatoriaunico = convocatoriaModelo::datos_convocatoria_modelo($this->conexion_db, "unico", $Convocatoria);

            if ($convocatoriaunico["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("no se encuentra el cuestionario");
            } else {
                if (isset($_FILES['txtImagenConvocatoria'])) {
                    $original = $_FILES['txtImagenConvocatoria'];
                    $nombre = $original['name'];
                    if ($original['error'] > 0) {
                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                    } else {
                        $resultado = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"), 5700, $original, $nombre, "./adjuntos/convocatoria/");
                        if ($resultado != "") {
                            $Convocatoria->setImagen($resultado);

                            $stmt = convocatoriaModelo::actualizar_convocatoria_modelo($this->conexion_db, $Convocatoria);
                            if ($stmt->execute()) {
                                $stmt = $this->conexion_db->prepare("DELETE FROM `detalle_convocatoria` WHERE idconvocatoria=:IDconvocatoria ");
                                $stmt->bindValue(":IDconvocatoria", $Convocatoria->getIdConvocatoria(), PDO::PARAM_INT);
                                if ($stmt->execute()) {
                                    $valor = false;
                                    $stmt = $this->conexion_db->prepare("INSERT INTO `detalle_convocatoria`(descripcion,idconvocatoria) VALUES(?,?)");
                                    $valor = false;
                                    foreach ($BeanConvocatoria->getListDetalle() as $lista) {
                                        $stmt->bindValue(1, $lista->descripcion, PDO::PARAM_STR);
                                        $stmt->bindValue(2, $Convocatoria->getIdConvocatoria(), PDO::PARAM_INT);
                                        $valor = $stmt->execute();
                                    }
                                    if ($valor) {
                                        if ($convocatoriaunico["list"][0]['imagen'] != "" || $convocatoriaunico["list"][0]['imagen'] != null) {
                                            unlink('./adjuntos/convocatoria/' . $convocatoriaunico["list"][0]['imagen']);
                                        }
                                        $this->conexion_db->commit();
                                        $insBeanCrud->setMessageServer("ok");
                                        $insBeanCrud->setBeanPagination(self::paginador_convocatoria_controlador($this->conexion_db, 0, 20));
                                    }
                                } else {
                                    $insBeanCrud->setMessageServer("No hemos podido actualizar las preguntas");
                                }

                            } else {
                                $insBeanCrud->setMessageServer("No hemos podido actualizar el cuestionario");
                            }

                        } else {
                            $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido");
                        }
                    }
                } else {
                    $Convocatoria->setImagen($convocatoriaunico["list"][0]['imagen']);

                    $stmt = convocatoriaModelo::actualizar_convocatoria_modelo($this->conexion_db, $Convocatoria);
                    if ($stmt->execute()) {
                        $stmt = $this->conexion_db->prepare("DELETE FROM `detalle_convocatoria` WHERE idconvocatoria=:IDconvocatoria ");
                        $stmt->bindValue(":IDconvocatoria", $Convocatoria->getIdConvocatoria(), PDO::PARAM_INT);
                        if ($stmt->execute()) {
                            $valor = false;
                            $stmt = $this->conexion_db->prepare("INSERT INTO `detalle_convocatoria`(descripcion,idconvocatoria) VALUES(?,?)");
                            $valor = false;
                            foreach ($BeanConvocatoria->getListDetalle() as $lista) {
                                $stmt->bindValue(1, $lista->descripcion, PDO::PARAM_STR);
                                $stmt->bindValue(2, $Convocatoria->getIdConvocatoria(), PDO::PARAM_INT);
                                $valor = $stmt->execute();
                            }
                            if ($valor) {
                                $this->conexion_db->commit();
                                $insBeanCrud->setMessageServer("ok");
                                $insBeanCrud->setBeanPagination(self::paginador_convocatoria_controlador($this->conexion_db, 0, 20));
                            }
                        } else {
                            $insBeanCrud->setMessageServer("No hemos podido actualizar las preguntas");
                        }

                    } else {
                        $insBeanCrud->setMessageServer("No hemos podido actualizar el cuestionario");
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
    public function estado_convocatoria_controlador($Convocatoria)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();

            $Convocatoria->setEstado(mainModel::limpiar_cadena($Convocatoria->getEstado()));
            $Convocatoria->setIdConvocatoria(mainModel::limpiar_cadena($Convocatoria->getIdConvocatoria()));

            $convocatoriaunico = convocatoriaModelo::datos_convocatoria_modelo($this->conexion_db, "unico", $Convocatoria);

            if ($convocatoriaunico["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("no se encuentra el cuestionario.");
            } else {

                $stmt = convocatoriaModelo::actualizar_estado_convocatoria_modelo($this->conexion_db, $Convocatoria);
                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_convocatoria_controlador($this->conexion_db, 0, 20));

                } else {
                    $insBeanCrud->setMessageServer("No hemos podido actualizar el cuestionario");
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
