<?php

require_once './modelos/testimonioModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';
class testimonioControlador extends testimonioModelo
{

    public function agregar_testimonio_controlador($Testimonio)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Testimonio->setTitulo(mainModel::limpiar_cadena($Testimonio->getTitulo()));

            $original = $_FILES['txtImagenTestimonio'];
            $nombre = $original['name'];

            if ($original['error'] > 0) {
                $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
            } else {
                $resultado = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"), 1700, $original, $nombre, "./adjuntos/testimonio/");
                if ($resultado != "") {
                    $Testimonio->setImagen($resultado);
                    $stmt = testimonioModelo::agregar_testimonio_modelo($this->conexion_db, $Testimonio);
                    if ($stmt->execute()) {
                        $this->conexion_db->commit();
                        $insBeanCrud->setMessageServer("ok");
                        $insBeanCrud->setBeanPagination(self::paginador_testimonio_controlador($this->conexion_db, 0, 20));
                    } else {
                        $insBeanCrud->setMessageServer("No hemos podido registrar el testimonio");
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
    public function datos_testimonio_controlador($tipo, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);
            $insBeanCrud->setBeanPagination(testimonioModelo::datos_testimonio_modelo($this->conexion_db, $tipo, $codigo));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function paginador_testimonio_controlador($conexion, $inicio, $registros)
    {
        $insBeanPagination = new BeanPagination();
        try {

            $stmt = $conexion->query("SELECT COUNT(idtestimonio) AS CONTADOR FROM `testimonio`");
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT * FROM `testimonio`
                    ORDER BY titulo ASC LIMIT ?,?");
                    $stmt->bindParam(1, $inicio, PDO::PARAM_INT);
                    $stmt->bindParam(2, $registros, PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();

                    foreach ($datos as $row) {
                        $insTestimonio = new Testimonio();
                        $insTestimonio->setIdtestimonio($row['idtestimonio']);
                        $insTestimonio->setTitulo($row['titulo']);
                        $insTestimonio->setImagen($row['imagen']);
                        $insTestimonio->setDescripcion($row['descripcion']);
                        $insTestimonio->setArchivo($row['archivo']);
                        $insTestimonio->setEnlaceYoutube($row['enlaceYoutube']);
                        $insTestimonio->setEstado($row['estado']);
                        $insBeanPagination->setList($insTestimonio->__toString());
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
    public function bean_paginador_testimonio_controlador($pagina, $registros)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_testimonio_controlador($this->conexion_db, $inicio, $registros));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_testimonio_controlador($Testimonio)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $testimonio = testimonioModelo::datos_testimonio_modelo($this->conexion_db, "unico", $Testimonio);
            if ($testimonio["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra el testimonio");
            } else {
                $stmt = testimonioModelo::eliminar_testimonio_modelo($this->conexion_db, mainModel::limpiar_cadena($Testimonio->getIdtestimonio()));
                if ($stmt->execute()) {
                    unlink('./adjuntos/testimonio/' . $testimonio["list"][0]['imagen']);
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_testimonio_controlador($this->conexion_db, 0, 20));

                } else {
                    $insBeanCrud->setMessageServer("No se eliminó el testimonio");
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
    public function actualizar_testimonio_controlador($Testimonio)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Testimonio->setTitulo(mainModel::limpiar_cadena($Testimonio->getTitulo()));
            if (isset($_FILES['txtImagenTestimonio'])) {
                $original = $_FILES['txtImagenTestimonio'];
                $nombre = $original['name'];

                if ($original['error'] > 0) {
                    $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                } else {
                    $resultado = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"), 1700, $original, $nombre, "./adjuntos/testimonio/");
                    if ($resultado != "") {
                        $ltestimonio = testimonioModelo::datos_testimonio_modelo($this->conexion_db, "unico", $Testimonio);
                        $Testimonio->setImagen($resultado);
                        $stmt = testimonioModelo::actualizar_testimonio_modelo($this->conexion_db, $Testimonio);
                        if ($stmt->execute()) {

                            $this->conexion_db->commit();
                            $insBeanCrud->setMessageServer("ok");
                            $insBeanCrud->setBeanPagination(self::paginador_testimonio_controlador($this->conexion_db, 0, 20));
                            unlink('./adjuntos/testimonio/' . $ltestimonio["list"][0]['imagen']);
                        } else {
                            $insBeanCrud->setMessageServer("No hemos podido actualizar el testimonio");
                        }
                    } else {
                        $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido");

                    }
                }

            } else {
                $ltestimonio = testimonioModelo::datos_testimonio_modelo($this->conexion_db, "unico", $Testimonio);
                $Testimonio->setImagen($ltestimonio["list"][0]['imagen']);
                $stmt = testimonioModelo::actualizar_testimonio_modelo($this->conexion_db, $Testimonio);
                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_testimonio_controlador($this->conexion_db, 0, 20));
                } else {
                    $insBeanCrud->setMessageServer("No hemos podido actualizar el testimonio");
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
