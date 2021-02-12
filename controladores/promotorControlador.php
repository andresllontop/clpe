<?php

require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';
require_once './modelos/promotorModelo.php';

class promotorControlador extends promotorModelo
{
    public function agregar_promotor_controlador($Promotor)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Promotor->setNombre(mainModel::limpiar_cadena($Promotor->getNombre()));
            $Promotor->setApellido(mainModel::limpiar_cadena($Promotor->getApellido()));
            $Promotor->setEmail(mainModel::limpiar_cadena($Promotor->getEmail()));
            $Promotor->setYoutube(mainModel::limpiar_cadena($Promotor->getYoutube()));
            $original = $_FILES['txtFotoPromotor'];
            $nombre = $original['name'];
            $originalPortada = $_FILES['txtFotoPortadaPromotor'];
            $nombrePortada = $originalPortada['name'];
            if ($original['error'] > 0 || $originalPortada['error'] > 0) {
                $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
            } else {

                $resultado_guardado = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"),
                    3700, $original, $nombre, "./adjuntos/team/");
                $resultado_guardado2 = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"),
                    3700, $originalPortada, $nombrePortada, "./adjuntos/team/");
                if ($resultado_guardado != "" && $resultado_guardado2 != "") {
                    $Promotor->setFoto($resultado_guardado);
                    $Promotor->setFotoPortada($resultado_guardado2);
                    $stmt = promotorModelo::agregar_promotor_modelo($this->conexion_db, $Promotor);
                    if ($stmt->execute()) {
                        $this->conexion_db->commit();
                        $insBeanCrud->setMessageServer("ok");
                        $insBeanCrud->setBeanPagination(self::paginador_promotor_controlador($this->conexion_db, 0, 5));

                    } else {
                        $insBeanCrud->setMessageServer("No hemos podido registrar el promotor");
                    }
                } else {
                    $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido");
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
    public function datos_promotor_controlador($tipo, $Promotor)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);
            $insBeanCrud->setBeanPagination(promotorModelo::datos_promotor_modelo($this->conexion_db, $tipo, $Promotor));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function paginador_promotor_controlador($conexion, $inicio, $registros)
    {
        $insBeanPagination = new BeanPagination();
        try {
            $stmt = $conexion->query("SELECT COUNT(iddocente) AS CONTADOR FROM `docente`");
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT * FROM `docente` ORDER BY nombres ASC LIMIT ?,? ");
                    $stmt->bindParam(1, $inicio, PDO::PARAM_INT);
                    $stmt->bindParam(2, $registros, PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();

                    foreach ($datos as $row) {
                        $insPromotor = new Promotor();
                        $insPromotor->setIdPromotor($row['iddocente']);
                        $insPromotor->setNombre($row['nombres']);
                        $insPromotor->setApellido($row['apellidos']);
                        $insPromotor->setEmail($row['email']);
                        $insPromotor->setYoutube($row['youtube']);
                        $insPromotor->setTelefono($row['celular']);
                        $insPromotor->setOcupacion($row['especialidad']);
                        $insPromotor->setDescripcion($row['descripcion']);
                        $insPromotor->setHistoria($row['historia']);
                        $insPromotor->setFoto($row['foto']);
                        $insPromotor->setFotoPortada($row['fotoPortada']);
                        $insBeanPagination->setList($insPromotor->__toString());
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
    public function bean_paginador_promotor_controlador($pagina, $registros)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_promotor_controlador($this->conexion_db, $inicio, $registros));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_promotor_controlador($Promotor)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $promotor = promotorModelo::datos_promotor_modelo($this->conexion_db, "unico", $Promotor);
            if ($promotor["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra el promotor");
            } else {
                $stmt = promotorModelo::eliminar_promotor_modelo($this->conexion_db, mainModel::limpiar_cadena($Promotor->getIdPromotor()));
                if ($stmt->execute()) {
                    unlink('./adjuntos/team/' . $promotor["list"][0]['foto']);
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_promotor_controlador($this->conexion_db, 0, 5));

                } else {
                    $insBeanCrud->setMessageServer("No se eliminó el promotor");
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
    public function actualizar_promotor_controlador($Promotor)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Promotor->setNombre(mainModel::limpiar_cadena($Promotor->getNombre()));
            $Promotor->setApellido(mainModel::limpiar_cadena($Promotor->getApellido()));
            $Promotor->setEmail(mainModel::limpiar_cadena($Promotor->getEmail()));
            $Promotor->setYoutube(mainModel::limpiar_cadena($Promotor->getYoutube()));
            $promotor = promotorModelo::datos_promotor_modelo($this->conexion_db, "unico", $Promotor);

            if ($promotor["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra el Promotor");
            } else {
                if (isset($_FILES['txtFotoPromotor']) && !isset($_FILES['txtFotoPortadaPromotor'])) {
                    $original = $_FILES['txtFotoPromotor'];
                    $nombre = $original['name'];
                    if ($original['error'] > 0) {
                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                    } else {

                        $resultado_guardado = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"),
                            3700, $original, $nombre, "./adjuntos/team/");
                        if ($resultado_guardado != "") {
                            $Promotor->setFoto($resultado_guardado);
                            $Promotor->setFotoPortada($promotor["list"][0]['fotoPortada']);
                            $stmt = promotorModelo::actualizar_promotor_modelo($this->conexion_db, $Promotor);
                            if ($stmt->execute()) {

                                $this->conexion_db->commit();
                                $insBeanCrud->setMessageServer("ok");
                                $insBeanCrud->setBeanPagination(self::paginador_promotor_controlador($this->conexion_db, 0, 5));
                                if ($promotor["list"][0]['foto'] != null || $promotor["list"][0]['foto'] != "") {
                                    unlink('./adjuntos/team/' . $promotor["list"][0]['foto']);
                                }
                            } else {
                                $insBeanCrud->setMessageServer("No hemos podido actualizar el promotor");
                            }
                        } else {
                            $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido");

                        }

                    }

                } else if (isset($_FILES['txtFotoPortadaPromotor']) && !isset($_FILES['txtFotoPromotor'])) {
                    $originalPortada = $_FILES['txtFotoPortadaPromotor'];
                    $nombrePortada = $originalPortada['name'];
                    if ($originalPortada['error'] > 0) {
                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                    } else {
                        $resultado_guardado = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"),
                            3700, $originalPortada, $nombrePortada, "./adjuntos/team/");

                        if ($resultado_guardado != "") {
                            $Promotor->setFoto($promotor["list"][0]['foto']);
                            $Promotor->setFotoPortada($resultado_guardado);
                            $stmt = promotorModelo::actualizar_promotor_modelo($this->conexion_db, $Promotor);
                            if ($stmt->execute()) {

                                $this->conexion_db->commit();
                                $insBeanCrud->setMessageServer("ok");
                                $insBeanCrud->setBeanPagination(self::paginador_promotor_controlador($this->conexion_db, 0, 5));
                                if ($promotor["list"][0]['fotoPortada'] != null || $promotor["list"][0]['fotoPortada'] != "") {
                                    unlink('./adjuntos/team/' . $promotor["list"][0]['fotoPortada']);
                                }
                            } else {
                                $insBeanCrud->setMessageServer("No hemos podido actualizar el promotor");
                            }
                        } else {
                            $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido");
                        }

                    }
                } else if (isset($_FILES['txtFotoPortadaPromotor']) && isset($_FILES['txtFotoPromotor'])) {
                    $original = $_FILES['txtFotoPromotor'];
                    $nombre = $original['name'];
                    $originalPortada = $_FILES['txtFotoPortadaPromotor'];
                    $nombrePortada = $originalPortada['name'];

                    if ($originalPortada['error'] > 0) {
                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen del promotor");
                    } elseif ($original['error'] > 0) {
                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen de  portada");
                    } else {
                        $resultado_guardado = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"),
                            3700, $original, $nombre, "./adjuntos/team/");

                        $resultado_guardado2 = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"),
                            3700, $originalPortada, $nombrePortada, "./adjuntos/team/");

                        if ($resultado_guardado != "" && $resultado_guardado2 != "") {
                            $Promotor->setFoto($resultado_guardado);
                            $Promotor->setFotoPortada($resultado_guardado2);
                            $stmt = promotorModelo::actualizar_promotor_modelo($this->conexion_db, $Promotor);
                            if ($stmt->execute()) {

                                $this->conexion_db->commit();
                                $insBeanCrud->setMessageServer("ok");
                                $insBeanCrud->setBeanPagination(self::paginador_promotor_controlador($this->conexion_db, 0, 5));
                                if ($promotor["list"][0]['fotoPortada'] != null || $promotor["list"][0]['fotoPortada'] != "") {
                                    unlink('./adjuntos/team/' . $promotor["list"][0]['fotoPortada']);
                                }
                                if ($promotor["list"][0]['foto'] != null || $promotor["list"][0]['foto'] != "") {
                                    unlink('./adjuntos/team/' . $promotor["list"][0]['foto']);
                                }
                            } else {
                                $insBeanCrud->setMessageServer("No hemos podido actualizar el promotor");
                            }
                        } else {
                            $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido");
                        }

                    }
                } else {
                    $Promotor->setFoto($promotor["list"][0]['foto']);
                    $Promotor->setFotoPortada($promotor["list"][0]['fotoPortada']);
                    $stmt = promotorModelo::actualizar_promotor_modelo($this->conexion_db, $Promotor);
                    if ($stmt->execute()) {
                        $this->conexion_db->commit();
                        $insBeanCrud->setMessageServer("ok");
                        $insBeanCrud->setBeanPagination(self::paginador_promotor_controlador($this->conexion_db, 0, 5));

                    } else {
                        $insBeanCrud->setMessageServer("No hemos podido actualizar el promotor");

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
