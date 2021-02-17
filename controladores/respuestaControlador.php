<?php

require_once './modelos/respuestaModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';
require_once './classes/principal/detalletest.php';
require_once './classes/principal/detallerespuesta.php';
require_once './classes/principal/respuesta.php';
require_once './classes/principal/subtitulo.php';
require_once './classes/principal/titulo.php';
require_once './classes/principal/empresa.php';

class respuestaControlador extends respuestaModelo
{
    public function agregar_respuesta_controlador($BeanRespuesta)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();

            $RespuestaClass = new Respuesta();
            $RespuestaClass->setEstado(mainModel::limpiar_cadena($BeanRespuesta->getRespuesta()->getEstado()));
            $RespuestaClass->setCuenta(mainModel::limpiar_cadena($BeanRespuesta->getRespuesta()->getCuenta()));
            $RespuestaClass->setTest(mainModel::limpiar_cadena($BeanRespuesta->getRespuesta()->getTest()));
            $RespuestaClass->setTipo(mainModel::limpiar_cadena($BeanRespuesta->getRespuesta()->getTipo()));
            $RespuestaClass->setTitulo(mainModel::limpiar_cadena($BeanRespuesta->getRespuesta()->getTitulo()));
            $RespuestaClass->setFecha(date("Y/m/d H:i:s"));
            $stmt = $this->conexion_db->prepare("SELECT count(idrespuesta) AS CONTADOR FROM `respuesta` WHERE respuesta_codigo=:IDtest and codigo_cuenta=:Cuenta");
            $stmt->bindValue(":IDtest", $RespuestaClass->getTitulo(), PDO::PARAM_STR);
            $stmt->bindValue(":Cuenta", $RespuestaClass->getCuenta(), PDO::PARAM_STR);
            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row2) {
                if ($row2['CONTADOR'] > 0) {
                    $stmt = $this->conexion_db->prepare("SELECT idrespuesta FROM `respuesta` WHERE respuesta_codigo=:IDtest and codigo_cuenta=:Cuenta");
                    $stmt->bindValue(":IDtest", $RespuestaClass->getTitulo(), PDO::PARAM_STR);
                    $stmt->bindValue(":Cuenta", $RespuestaClass->getCuenta(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row3) {
                        $stmt = $this->conexion_db->prepare("DELETE FROM `detalle_respuesta` WHERE idrespuesta=:Codigo");
                        $stmt->bindValue(":Codigo", $row3['idrespuesta']);
                        if ($stmt->execute()) {
                            $stmt = $this->conexion_db->prepare("DELETE FROM `respuesta` WHERE idrespuesta=:Codigo");
                            $stmt->bindValue(":Codigo", $row3['idrespuesta']);
                            if ($stmt->execute()) {
                                if ((int) $RespuestaClass->getTipo() == 1 || (int) $RespuestaClass->getTipo() == 2) {
                                    $stmt = $this->conexion_db->prepare("SELECT count(idrespuesta) AS CONTADOR FROM `respuesta` WHERE respuesta_codigo=:IDtest and codigo_cuenta=:Cuenta and tipo=:Tipo");
                                    $stmt->bindValue(":IDtest", $RespuestaClass->getTitulo(), PDO::PARAM_STR);
                                    $stmt->bindValue(":Tipo", $RespuestaClass->getTipo(), PDO::PARAM_INT);
                                    $stmt->bindValue(":Cuenta", $RespuestaClass->getCuenta(), PDO::PARAM_STR);
                                    $stmt->execute();
                                    $datos = $stmt->fetchAll();
                                    foreach ($datos as $row) {
                                        if ($row['CONTADOR'] == 0) {
                                            $stmt = respuestaModelo::agregar_respuesta_modelo($this->conexion_db, $RespuestaClass);
                                            if ($stmt->execute()) {
                                                $stmt = $this->conexion_db->prepare("SELECT MAX(idrespuesta) AS CONTADOR FROM `respuesta` WHERE idtest=:IDtest and codigo_cuenta=:Cuenta");
                                                $stmt->bindValue(":IDtest", $RespuestaClass->getTest(), PDO::PARAM_INT);
                                                $stmt->bindValue(":Cuenta", $RespuestaClass->getCuenta(), PDO::PARAM_STR);
                                                $stmt->execute();
                                                $datos = $stmt->fetchAll();
                                                foreach ($datos as $row) {
                                                    $stmt = $this->conexion_db->prepare("INSERT INTO `detalle_respuesta`(codigosubtitulo,idrespuesta,estado,descripcion,iddetalle_test,pregunta_descripcion) VALUES(?,?,?,?,?,?)");
                                                    $valor = false;
                                                    foreach ($BeanRespuesta->getListDetalle() as $lista) {
                                                        $stmt->bindValue(1, $lista->subtitulo, PDO::PARAM_STR);
                                                        $stmt->bindValue(2, $row['CONTADOR'], PDO::PARAM_INT);
                                                        $stmt->bindValue(3, 0, PDO::PARAM_INT);
                                                        $stmt->bindValue(4, $lista->descripcion, PDO::PARAM_STR);
                                                        $stmt->bindValue(5, $lista->test, PDO::PARAM_INT);
                                                        $stmt->bindValue(6, $lista->pregunta, PDO::PARAM_STR);
                                                        $valor = $stmt->execute();
                                                    }
                                                    if ($valor) {
                                                        //saber si es el examen del capitulo
                                                        if ($RespuestaClass->getTipo() == 1) {
                                                            //BUSCAR EL MAXIMO SUBTITULO DEL CAPITULO
                                                            $stmt = $this->conexion_db->prepare("SELECT MAX(codigo_subtitulo) AS MAXIMO FROM `subtitulo` WHERE codigo_subtitulo like concat('%',:IDlibro,'%')");
                                                            $stmt->bindValue(":IDlibro", $RespuestaClass->getTitulo(), PDO::PARAM_STR);
                                                            $stmt->execute();
                                                            $datos = $stmt->fetchAll();
                                                            foreach ($datos as $row) {
                                                                if ($row['MAXIMO'] != null || $row['MAXIMO'] != "") {
                                                                    $RespuestaClass->setTitulo($row['MAXIMO']);
                                                                }
                                                            }
                                                        }
                                                        $stmt = $this->conexion_db->prepare("UPDATE `tarea` SET fecha=:Fecha WHERE cuenta=:Cuenta AND tipo=:Tipo AND codigo_subtitulo=:Subtitulo");
                                                        $stmt->bindValue(":Cuenta", $RespuestaClass->getCuenta(), PDO::PARAM_STR);
                                                        $stmt->bindValue(":Subtitulo", $RespuestaClass->getTitulo(), PDO::PARAM_STR);
                                                        $stmt->bindValue(":Fecha", $RespuestaClass->getFecha());
                                                        $stmt->bindValue(":Tipo", $RespuestaClass->getTipo(), PDO::PARAM_INT);
                                                        if ($stmt->execute()) {
                                                            $this->conexion_db->commit();
                                                            $insBeanCrud->setMessageServer("ok");
                                                            $array = explode(".", $RespuestaClass->getTitulo());
                                                            // aumentar titulo
                                                            $numerofinal = ($array[2] + 1);
                                                            if (strlen($numerofinal) == 1) {
                                                                $numerofinal = "0" . $numerofinal;
                                                            }
                                                            $RespuestaClass->setTitulo($array[0] . "." . $array[1] . "." . $numerofinal);
                                                            $stmt = $this->conexion_db->prepare("SELECT count(idtitulo) AS CONTADOR FROM `titulo` WHERE codigoTitulo=:codigo");
                                                            $stmt->bindValue(":codigo", $RespuestaClass->getTitulo(), PDO::PARAM_STR);
                                                            $stmt->execute();
                                                            $datos = $stmt->fetchAll();
                                                            foreach ($datos as $row) {
                                                                if ($row["CONTADOR"] == 0) {
                                                                    $insBeanCrud->setBeanClass("fin");
                                                                    $insBeanCrud->setMessageServer("fin");
                                                                }
                                                            }
                                                            /* */
                                                        } else {
                                                            $insBeanCrud->setMessageServer("No se ha podido registrar la tarea.");
                                                        }

                                                    } else {
                                                        $insBeanCrud->setMessageServer("No se ha podido enviar las respuestas");
                                                    }

                                                }

                                            } else {
                                                $insBeanCrud->setMessageServer("No se ha podido enviar la respuesta");
                                            }
                                        } else {
                                            $insBeanCrud->setMessageServer("Ya registraste tu cuestionario, recarga la página");
                                        }
                                    }

                                } else {
                                    $insBeanCrud->setMessageServer("Datos incorrectos");
                                }
                            } else { $insBeanCrud->setMessageServer("no se elimino la repuesta");}
                        } else {
                            $insBeanCrud->setMessageServer("no se elimino el detalle de repuestas");
                        }
                    }
                } else {
                    if ((int) $RespuestaClass->getTipo() == 1 || (int) $RespuestaClass->getTipo() == 2) {
                        $stmt = $this->conexion_db->prepare("SELECT count(idrespuesta) AS CONTADOR FROM `respuesta` WHERE respuesta_codigo=:IDtest and codigo_cuenta=:Cuenta and tipo=:Tipo");
                        $stmt->bindValue(":IDtest", $RespuestaClass->getTitulo(), PDO::PARAM_STR);
                        $stmt->bindValue(":Tipo", $RespuestaClass->getTipo(), PDO::PARAM_INT);
                        $stmt->bindValue(":Cuenta", $RespuestaClass->getCuenta(), PDO::PARAM_STR);
                        $stmt->execute();
                        $datos = $stmt->fetchAll();
                        foreach ($datos as $row) {
                            if ($row['CONTADOR'] == 0) {
                                $stmt = respuestaModelo::agregar_respuesta_modelo($this->conexion_db, $RespuestaClass);
                                if ($stmt->execute()) {
                                    $stmt = $this->conexion_db->prepare("SELECT MAX(idrespuesta) AS CONTADOR FROM `respuesta` WHERE idtest=:IDtest and codigo_cuenta=:Cuenta");
                                    $stmt->bindValue(":IDtest", $RespuestaClass->getTest(), PDO::PARAM_INT);
                                    $stmt->bindValue(":Cuenta", $RespuestaClass->getCuenta(), PDO::PARAM_STR);
                                    $stmt->execute();
                                    $datos = $stmt->fetchAll();
                                    foreach ($datos as $row) {
                                        $stmt = $this->conexion_db->prepare("INSERT INTO `detalle_respuesta`(codigosubtitulo,idrespuesta,estado,descripcion,iddetalle_test,pregunta_descripcion) VALUES(?,?,?,?,?,?)");
                                        $valor = false;
                                        foreach ($BeanRespuesta->getListDetalle() as $lista) {
                                            $stmt->bindValue(1, $lista->subtitulo, PDO::PARAM_STR);
                                            $stmt->bindValue(2, $row['CONTADOR'], PDO::PARAM_INT);
                                            $stmt->bindValue(3, 0, PDO::PARAM_INT);
                                            $stmt->bindValue(4, $lista->descripcion, PDO::PARAM_STR);
                                            $stmt->bindValue(5, $lista->test, PDO::PARAM_INT);
                                            $stmt->bindValue(6, $lista->pregunta, PDO::PARAM_STR);
                                            $valor = $stmt->execute();
                                        }
                                        if ($valor) {
                                            //saber si es el examen del capitulo
                                            if ($RespuestaClass->getTipo() == 1) {
                                                //BUSCAR EL MAXIMO SUBTITULO DEL CAPITULO
                                                $stmt = $this->conexion_db->prepare("SELECT MAX(codigo_subtitulo) AS MAXIMO FROM `subtitulo` WHERE codigo_subtitulo like concat('%',:IDlibro,'%')");
                                                $stmt->bindValue(":IDlibro", $RespuestaClass->getTitulo(), PDO::PARAM_STR);
                                                $stmt->execute();
                                                $datos = $stmt->fetchAll();
                                                foreach ($datos as $row) {
                                                    if ($row['MAXIMO'] != null || $row['MAXIMO'] != "") {
                                                        $RespuestaClass->setTitulo($row['MAXIMO']);
                                                    }
                                                }
                                            }
                                            $stmt = $this->conexion_db->prepare("INSERT INTO `tarea`(cuenta,tipo,fecha,codigo_subtitulo,estado) VALUES(:Cuenta,:Tipo,:Fecha,:Subtitulo,:Estado)");
                                            $stmt->bindValue(":Cuenta", $RespuestaClass->getCuenta(), PDO::PARAM_STR);
                                            $stmt->bindValue(":Subtitulo", $RespuestaClass->getTitulo(), PDO::PARAM_STR);
                                            $stmt->bindValue(":Fecha", $RespuestaClass->getFecha());
                                            $stmt->bindValue(":Estado", 0);
                                            $stmt->bindValue(":Tipo", $RespuestaClass->getTipo(), PDO::PARAM_INT);
                                            if ($stmt->execute()) {
                                                $this->conexion_db->commit();
                                                $insBeanCrud->setMessageServer("ok");
                                                $array = explode(".", $RespuestaClass->getTitulo());
                                                // aumentar titulo
                                                $numerofinal = ($array[2] + 1);
                                                if (strlen($numerofinal) == 1) {
                                                    $numerofinal = "0" . $numerofinal;
                                                }
                                                $RespuestaClass->setTitulo($array[0] . "." . $array[1] . "." . $numerofinal);
                                                $stmt = $this->conexion_db->prepare("SELECT count(idtitulo) AS CONTADOR FROM `titulo` WHERE codigoTitulo=:codigo");

                                                $stmt->bindValue(":codigo", $RespuestaClass->getTitulo(), PDO::PARAM_STR);
                                                $stmt->execute();
                                                $datos = $stmt->fetchAll();
                                                foreach ($datos as $row) {
                                                    if ($row["CONTADOR"] == 0) {
                                                        $insBeanCrud->setBeanClass("fin");
                                                        $insBeanCrud->setMessageServer("fin");
                                                    }
                                                }
                                                /* */
                                            } else {
                                                $insBeanCrud->setMessageServer("No se ha podido registrar la tarea.");
                                            }

                                        } else {
                                            $insBeanCrud->setMessageServer("No se ha podido enviar las respuestas");
                                        }

                                    }

                                } else {
                                    $insBeanCrud->setMessageServer("No se ha podido enviar la respuesta");
                                }
                            } else {
                                $insBeanCrud->setMessageServer("Ya registraste tu cuestionario, recarga la página");
                            }
                        }

                    } else {
                        $insBeanCrud->setMessageServer("Datos incorrectos");
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
    public function datos_respuesta_controlador($tipo, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);
            $insBeanCrud->setBeanPagination(respuestaModelo::datos_respuesta_modelo($this->conexion_db, $tipo, $codigo));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();

    }
    public function paginador_respuesta_controlador($conexion, $inicio, $registros, $cuenta, $tipo, $codigo)
    {
        $insBeanPagination = new BeanPagination();
        try {

            $stmt = $conexion->prepare("SELECT COUNT(idrespuesta) AS CONTADOR FROM `respuesta` WHERE codigo_cuenta=:Cuenta and tipo=:Tipo and respuesta_codigo=:Codigo");
            $stmt->bindValue(":Cuenta", $cuenta, PDO::PARAM_STR);
            $stmt->bindValue(":Tipo", $tipo, PDO::PARAM_INT);
            $stmt->bindValue(":Codigo", $codigo, PDO::PARAM_STR);
            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT det.pregunta_descripcion,det.descripcion AS det_descripcion ,sub.nombre,sub.codigo_subtitulo,res.fecha,res.tipo FROM `respuesta` AS res INNER JOIN `detalle_respuesta` as det ON det.idrespuesta=res.idrespuesta INNER JOIN `subtitulo` AS sub ON sub.codigo_subtitulo=det.codigosubtitulo WHERE res.codigo_cuenta=:Cuenta  and res.tipo=:Tipo and res.respuesta_codigo=:Codigo ORDER BY det.codigosubtitulo ASC LIMIT :inicio,:regi");
                    $stmt->bindValue(":inicio", $inicio, PDO::PARAM_INT);
                    $stmt->bindValue(":regi", $registros, PDO::PARAM_INT);
                    $stmt->bindValue(":Cuenta", $cuenta, PDO::PARAM_STR);
                    $stmt->bindValue(":Tipo", $tipo, PDO::PARAM_INT);
                    $stmt->bindValue(":Codigo", $codigo, PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insRespuesta = new Respuesta();
                        $insRespuesta->setFecha($row['fecha']);
                        $insRespuesta->setTipo($row['tipo']);

                        $insSubTitulo = new SubTitulo();
                        $insSubTitulo->setCodigo($row['codigo_subtitulo']);
                        $insSubTitulo->setNombre($row['nombre']);

                        $insDetalleTest = new DetalleTest();
                        $insDetalleTest->setDescripcion($row['pregunta_descripcion']);

                        $insDetalleRespuesta = new DetalleRespuesta();
                        $insDetalleRespuesta->setDescripcion($row['det_descripcion']);
                        $insDetalleRespuesta->setSubtitulo($insSubTitulo->__toString());
                        $insDetalleRespuesta->setRespuesta($insRespuesta->__toString());
                        $insDetalleRespuesta->setTest($insDetalleTest->__toString());

                        $insBeanPagination->setList($insDetalleRespuesta->__toString());
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
    public function bean_paginador_respuesta_controlador($pagina, $registros, $cuenta, $tipo, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $cuenta = mainModel::limpiar_cadena($cuenta);
            $tipo = mainModel::limpiar_cadena($tipo);
            $codigo = mainModel::limpiar_cadena($codigo);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_respuesta_controlador($this->conexion_db, $inicio, $registros, $cuenta, $tipo, $codigo));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_respuesta_controlador($Respuesta)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Respuesta->setIdRespuesta(mainModel::limpiar_cadena($Respuesta->getIdRespuesta()));
            // $Respuesta->setCuenta(mainModel::limpiar_cadena($Respuesta->getCuenta()));
            $respuesta = respuestaModelo::datos_respuesta_modelo($this->conexion_db, "unico", $Respuesta);
            if ($respuesta["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra el respuesta");
            } else {
                $stmt = respuestaModelo::eliminar_respuesta_modelo($this->conexion_db, $Respuesta->getIdRespuesta());

                if ($stmt->execute()) {
                    $stmt = $this->conexion_db->prepare("DELETE FROM `detalle_respuesta` WHERE idrespuesta=:Codigo");
                    $stmt->bindValue(":Codigo", $Respuesta->getIdRespuesta());
                    if ($stmt->execute()) {

                        if ($respuesta["list"][0]['tipo'] == 1) {
                            $stmt = $this->conexion_db->prepare("SELECT MAX(codigo_subtitulo) AS CONTADOR FROM `subtitulo` WHERE codigo_subtitulo like concat('%',:Codigo,'%')");
                            $stmt->bindValue(":Codigo", $respuesta["list"][0]['titulo']);
                            $stmt->execute();
                            $datos1 = $stmt->fetchAll();

                            foreach ($datos1 as $row1) {
                                if ($row1['CONTADOR'] != null || $row1['CONTADOR'] != "") {
                                    $stmt = $this->conexion_db->prepare("DELETE FROM `tarea` WHERE codigo_subtitulo=:Codigo and tipo=:Tipo");
                                    $stmt->bindValue(":Codigo", $row1['CONTADOR']);
                                    $stmt->bindValue(":Tipo", $respuesta["list"][0]['tipo']);

                                }
                            }

                        } else {
                            $stmt = $this->conexion_db->prepare("DELETE FROM `tarea` WHERE codigo_subtitulo=:Codigo and tipo=:Tipo");
                            $stmt->bindValue(":Codigo", $respuesta["list"][0]['titulo']);
                            $stmt->bindValue(":Tipo", $respuesta["list"][0]['tipo']);
                        }

                        if ($stmt->execute()) {
                            $this->conexion_db->commit();
                            $insBeanCrud->setMessageServer("ok");
                            $insRespuestaClass = new Respuesta();
                            $insRespuestaClass->setPagina(1);
                            $insRespuestaClass->setRegistro(5);
                            $insRespuestaClass->setCuenta($respuesta["list"][0]['cuenta']);
                            if ($respuesta["list"][0]['tipo'] == 2) {
                                $insBeanCrud->setBeanPagination(respuestaModelo::datos_respuesta_modelo($this->conexion_db, "conteo-subtitulo", $insRespuestaClass));
                            } else if ($respuesta["list"][0]['tipo'] == 1) {
                                $insBeanCrud->setBeanPagination(respuestaModelo::datos_respuesta_modelo($this->conexion_db, "conteo-titulo", $insRespuestaClass));
                            }

                        } else {
                            $insBeanCrud->setMessageServer("No se eliminó el respuesta");
                        }

                    } else {
                        $insBeanCrud->setMessageServer("No se eliminó el respuesta");
                    }

                } else {
                    $insBeanCrud->setMessageServer("No se eliminó el respuesta");
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
    public function actualizar_respuesta_controlador($Respuesta)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Respuesta->setIdRespuesta(mainModel::limpiar_cadena($Respuesta->getIdRespuesta()));
            $Respuesta->setCuenta(mainModel::limpiar_cadena($Respuesta->getCuenta()));
            $Respuesta->setEstado(mainModel::limpiar_cadena($Respuesta->getEstado()));
            $respuesta = respuestaModelo::datos_respuesta_modelo($this->conexion_db, "unico", $Respuesta);
            if ($respuesta["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra el respuesta");
            } else {
                $stmt = respuestaModelo::actualizar_respuesta_modelo($this->conexion_db, $Respuesta);
                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_respuesta_controlador($this->conexion_db, 0, 5));

                } else {
                    $insBeanCrud->setMessageServer("No se actualizó el respuesta");
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
    public function actualizar_respuesta_estado_controlador($Respuesta)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Respuesta->setCuenta(mainModel::limpiar_cadena($Respuesta->getCuenta()));
            $Respuesta->setEstado(mainModel::limpiar_cadena($Respuesta->getEstado()));
            $Respuesta->setTitulo(mainModel::limpiar_cadena($Respuesta->getTitulo()));

            $stmt = $this->conexion_db->prepare("UPDATE `respuesta` SET tipo_estado=:Estado WHERE codigo_cuenta=:Cuenta and respuesta_codigo=:Subtitulo");
            $stmt->bindValue(":Estado", $Respuesta->getEstado(), PDO::PARAM_INT);
            $stmt->bindValue(":Subtitulo", $Respuesta->getTitulo(), PDO::PARAM_STR);
            $stmt->bindValue(":Cuenta", $Respuesta->getCuenta(), PDO::PARAM_STR);
            if ($stmt->execute()) {
                $this->conexion_db->commit();
                $insBeanCrud->setMessageServer("siguiente");

            } else {
                $insBeanCrud->setMessageServer("No se actualizó el estado de la respuesta");

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
    public function actualizar_respuesta_tarea_controlador($Respuesta)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Respuesta->setIdRespuesta(mainModel::limpiar_cadena($Respuesta->getIdRespuesta()));
            $Respuesta->setEstado(mainModel::limpiar_cadena($Respuesta->getEstado()));
            $respuesta = respuestaModelo::datos_respuesta_modelo($this->conexion_db, "unico", $Respuesta);
            if ($respuesta["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra el respuesta");
            } else {
                $stmt = respuestaModelo::actualizar_respuesta_tarea_modelo($this->conexion_db, $Respuesta);
                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insRespuestaClass = new Respuesta();
                    $insRespuestaClass->setPagina(1);
                    $insRespuestaClass->setRegistro(5);
                    $insRespuestaClass->setCuenta($respuesta["list"][0]['cuenta']);
                    if ($respuesta["list"][0]['tipo'] == 2) {
                        $insBeanCrud->setBeanPagination(respuestaModelo::datos_respuesta_modelo($this->conexion_db, "conteo-subtitulo", $insRespuestaClass));
                    } else if ($respuesta["list"][0]['tipo'] == 1) {
                        $insBeanCrud->setBeanPagination(respuestaModelo::datos_respuesta_modelo($this->conexion_db, "conteo-titulo", $insRespuestaClass));
                    }

                } else {
                    $insBeanCrud->setMessageServer("No se actualizó el respuesta");
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
    public function reporte_respuestas_controlador($Respuesta)
    {
        $insBeanCrud = null;
        try {
            $lista = respuestaModelo::datos_respuesta_modelo($this->conexion_db, "reporte", $Respuesta)['list'];
            $insEmpresa = new Empresa();
            $stmt = $this->conexion_db->prepare("SELECT * FROM `empresa`");
            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insEmpresa->setTelefono($row['EmpresaTelefono']);
                //$insEmpresa->setYoutube($row['youtube']);
                $insEmpresa->setNombre($row['EmpresaNombre']);
                $insEmpresa->setEmail($row['EmpresaEmail']);
                $insEmpresa->setDireccion($row['EmpresaDireccion']);
                $insEmpresa->setLogo($row['EmpresaLogo']);
                //$insEmpresa->setFacebook($row['facebook']);
            }
            if ($Respuesta->getTipo() == 1) {
                $libro = respuestaModelo::datos_respuesta_modelo($this->conexion_db, "reporte-titulo", $Respuesta)['list'];
                $insBeanCrud = self::HTML_reporte_Capitulo($insEmpresa->__toString(), $lista, $libro[0]);
            } else if ($Respuesta->getTipo() == 2) {
                $libro = respuestaModelo::datos_respuesta_modelo($this->conexion_db, "reporte-subtitulo", $Respuesta)['list'];
                $insBeanCrud = self::HTML_reporte($insEmpresa->__toString(), $lista, $libro[0]);
            }

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud;
    }
    public function HTML_reporte($empresa, $data, $libro)
    {
        // print_r($empresa);
        $html = '<!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="utf-8">
            <title>COMENTARIO</title>
            <link rel="stylesheet" href="' . SERVERURL . 'vistas/subprojects/pdf/comentario.css" media="all" />

        </head>

        <body>
        <header class="clearfix">
        <div id="logo">
           <img src="' . SERVERURL . 'adjuntos/logoHeader.jpg">
           </div>
            <div id="company">
           <h3 class="name">' . $empresa['nombre'] . '</h3>
           <div>' . $empresa['direccion'] . '</div>
           <div><a href="https://web.whatsapp.com/send?phone=' . $empresa['telefono'] . '" target="blank">' . $empresa['telefono'] . '</a></div>
           <div><a href="mailto:' . $empresa['email'] . '" target="blank">' . $empresa['email'] . '</a></div>
           </div>
            </div>
       </header>
       <main>
       <div id="details" class="clearfix">

           <div id="invoice">
               <h1>' . $libro['titulo']['libro'] . '</h1>
               <h2 class="name" style="font-weight: bold;">' . $libro['titulo']['titulo'] . '</h2>
               <div class="address">' . $libro['titulo']['subTitulo'] . '</div>
               <!--div class="date">Realizado: 01/06/2014</div-->
           </div>
           <div id="client">
                <div class="to">Alumno:</div>
                <h2 class="name"> ' . $libro['cuenta']['nombre_completo'] . '</h2>
                <div class="address">' . $libro['cuenta']['ocupacion'] . '</div>
                <div class="address">' . $libro['cuenta']['telefono'] . '</div>
                <div class="email"><a href="mailto:' . $libro['cuenta']['email'] . '">' . $libro['cuenta']['email'] . '</a></div>
            </div>
       </div>
       <table border="0" cellspacing="0" cellpadding="0">
           <thead>
               <tr>
                   <th class="no" style="padding-left: 0px;">#</th>
                   <th class="desc">PREGUNTAS</th>
                   <th class="unit">RESPUESTAS</th>
               </tr>
           </thead>
           <tbody>';
        $contador = 1;
        foreach ($data as $value) {
            // print_r($value['descripcion']);

            $html .= '
            <tr>
                <td class="no">' . ($contador++) . '</td>
                <td class="desc">
                ' . $value['test']['descripcion'] . '
                </td>
                <td class="unit"> ' . $value['descripcion'] . '</td>
            </tr>
         ';
        }

        $html .= '
           </tbody>

       </table>

   </main>
            <footer>
                Copyright CLPE © 2020 v 1.0
            </footer>
        </body>

        </html>';

        return $html;
    }

    public function HTML_reporte_Capitulo($empresa, $data, $libro)
    {
        // print_r($libro);
        // print_r($empresa);
        $html = '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <title>COMENTARIO</title>
            <link rel="stylesheet" href="' . SERVERURL . 'vistas/subprojects/pdf/comentario.css" media="all" />
        </head>

        <body>
        <header class="clearfix">
        <div id="logo">
           <img src="' . SERVERURL . 'adjuntos/logoHeader.jpg">
           </div>
            <div id="company">
           <h3 class="name">' . $empresa['nombre'] . '</h3>
           <div>' . $empresa['direccion'] . '</div>
           <div><a href="https://web.whatsapp.com/send?phone=' . $empresa['telefono'] . '" target="blank">' . $empresa['telefono'] . '</a></div>
           <div><a href="mailto:' . $empresa['email'] . '" target="blank">' . $empresa['email'] . '</a></div>
           </div>
            </div>
       </header>
       <main>
       <div id="details" class="clearfix">

           <div id="invoice">
               <h1>' . $libro['titulo']['libro'] . '</h1>
               <h2 class="name" style="font-weight: bold;">' . $libro['titulo']['titulo'] . '</h2>
               <div class="address">' . $libro['titulo']['subTitulo'] . '</div>
               <!--div class="date">Realizado: 01/06/2014</div-->
           </div>
           <div id="client">
                <div class="to">Alumno:</div>
                <h2 class="name"> ' . $libro['cuenta']['nombre_completo'] . '</h2>
                <div class="address">' . $libro['cuenta']['ocupacion'] . '</div>
                <div class="address">' . $libro['cuenta']['telefono'] . '</div>
                <div class="email"><a href="mailto:' . $libro['cuenta']['email'] . '">' . $libro['cuenta']['email'] . '</a></div>
            </div>
       </div>
       <table border="0" cellspacing="0" cellpadding="0">
           <thead>
               <tr>
                   <th class="no" style="padding-left: 0px;">#</th>
                   <th class="desc">PREGUNTAS</th>
                   <th class="unit">RESPUESTAS</th>
               </tr>
           </thead>
           <tbody>';
        $contador = 1;
        foreach ($data as $value) {

            $html .= '
            <tr>
                <td class="no">' . ($contador++) . '</td>
                <td class="desc"> <h3>' . $value['subtitulo']['nombre'] . '</h3>
                ' . $value['test']['descripcion'] . '
                </td>
                <td class="unit"> ' . $value['descripcion'] . '</td>
            </tr>
         ';
        }

        $html .= '
           </tbody>

       </table>

   </main>
            <footer>
                Copyright CLPE © 2020 v 1.0
            </footer>
        </body>

        </html>';

        return $html;
    }
}
