<?php

require_once './modelos/detallerecursoModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';

require_once './classes/principal/recurso.php';
class detallerecursoControlador extends detallerecursoModelo
{
    public function agregar_detallerecurso_controlador($DetalleRecurso)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $DetalleRecurso->setDescripcion(mainModel::limpiar_cadena($DetalleRecurso->getDescripcion()));
            $DetalleRecurso->setTipo(mainModel::limpiar_cadena($DetalleRecurso->getTipo()));
            $DetalleRecurso->setRecurso(mainModel::limpiar_cadena($DetalleRecurso->getRecurso()));

            switch ($DetalleRecurso->getTipo()) {
                case '1':
                    $original = $_FILES['txtImagenDetalle'];
                    $nombre = $original['name'];
                    $permitido = array("image/png", "image/jpg", "image/jpeg");
                    $destino = "IMAGENES";
                    $Limit_KB = 4 * 1024;
                    break;
                case '2':
                    $original = $_FILES['txtVideoDetalle'];
                    $nombre = $original['name'];
                    $permitido = array("video/mp4");
                    $destino = "VIDEOS";
                    $Limit_KB = 17 * 1024;
                    break;
                case '3':
                    $original = $_FILES['txtPdfDetalle'];
                    $nombre = $original['name'];
                    $permitido = array("application/pdf");
                    $destino = "PDF";
                    $Limit_KB = 5 * 1024;
                    break;
            }

            if ($original['error'] > 0) {
                $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otro archivo");
            } else {
                $resultado_guardado = mainModel::archivo($permitido, $Limit_KB, $original, $nombre, "./adjuntos/recurso/" . $destino . "/");
                if ($resultado_guardado != "") {
                    $DetalleRecurso->setArchivo($resultado_guardado);
                    $stmt = detallerecursoModelo::agregar_detallerecurso_modelo($this->conexion_db, $DetalleRecurso);
                    if ($stmt->execute()) {
                        $this->conexion_db->commit();
                        $insBeanCrud->setMessageServer("ok");
                        $insBeanCrud->setBeanPagination(self::paginador_detallerecurso_controlador($this->conexion_db, 0, 5, $DetalleRecurso->getRecurso()));

                    } else {

                        $insBeanCrud->setMessageServer("error en el servidor, No hemos podido registrar el archivo");
                    }
                } else {
                    $insBeanCrud->setMessageServer("error en el servidor, Hubo un error al guardar el archivo");

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
    public function datos_detallerecurso_controlador($tipo, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);
            $insBeanCrud->setBeanPagination(detallerecursoModelo::datos_detallerecurso_modelo($this->conexion_db, $tipo, $codigo));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();

    }
    public function paginador_detallerecurso_controlador($conexion, $inicio, $registros, $recurso)
    {
        $insBeanPagination = new BeanPagination();
        try {
            $stmt = $conexion->prepare("SELECT COUNT(iddetalle_recurso) AS CONTADOR FROM `detalle_recurso` WHERE idrecurso=?");
            $stmt->bindValue(1, $recurso, PDO::PARAM_INT);
            $stmt->execute();

            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT * FROM `detalle_recurso` WHERE idrecurso=? ORDER BY tipo ASC LIMIT ?,?");
                    $stmt->bindValue(1, $recurso, PDO::PARAM_INT);
                    $stmt->bindValue(2, $inicio, PDO::PARAM_INT);
                    $stmt->bindValue(3, $registros, PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();

                    foreach ($datos as $row) {

                        $insDetalleRecurso = new DetalleRecurso();
                        $insDetalleRecurso->setIdDetalleRecurso($row['iddetalle_recurso']);
                        $insDetalleRecurso->setTipo($row['tipo']);
                        $insDetalleRecurso->setArchivo($row['archivo']);
                        $insDetalleRecurso->setDescripcion($row['descripcion']);
                        $insDetalleRecurso->setRecurso($row['idrecurso']);

                        $insBeanPagination->setList($insDetalleRecurso->__toString());
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
    public function bean_paginador_detallerecurso_controlador($pagina, $registros, $recurso)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $recurso = mainModel::limpiar_cadena($recurso);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_detallerecurso_controlador($this->conexion_db, $inicio, $registros, $recurso));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_detallerecurso_controlador($DetalleRecurso)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $DetalleRecurso->setIdDetalleRecurso(mainModel::limpiar_cadena($DetalleRecurso->getIdDetalleRecurso()));

            $detallerecurso = detallerecursoModelo::datos_detallerecurso_modelo($this->conexion_db, "unico", $DetalleRecurso);
            if ($detallerecurso["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("error en el servidor, No se encuentra registrado el archivo");

            } else {

                $stmt = detallerecursoModelo::eliminar_detallerecurso_modelo($this->conexion_db, $DetalleRecurso->getIdDetalleRecurso());
                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    switch ($detallerecurso["list"][0]['tipo']) {
                        case '1':
                            unlink('./adjuntos/recurso/IMAGENES/' . $detallerecurso["list"][0]['archivo']);
                            break;
                        case '2':
                            unlink('./adjuntos/recurso/VIDEOS/' . $detallerecurso["list"][0]['archivo']);
                            break;
                        case '3':
                            unlink('./adjuntos/recurso/PDF/' . $detallerecurso["list"][0]['archivo']);
                            break;
                    }
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_detallerecurso_controlador($this->conexion_db, 0, 5, $detallerecurso["list"][0]['recurso']));

                } else {
                    $insBeanCrud->setMessageServer("error en el servidor, no se puede elminar el archivo");
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
    public function actualizar_detallerecurso_controlador($DetalleRecurso)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $DetalleRecurso->setDescripcion(mainModel::limpiar_cadena($DetalleRecurso->getDescripcion()));
            $DetalleRecurso->setTipo(mainModel::limpiar_cadena($DetalleRecurso->getTipo()));
            $DetalleRecurso->setRecurso(mainModel::limpiar_cadena($DetalleRecurso->getRecurso()));

            $DetalleRecurso->setIdDetalleRecurso(mainModel::limpiar_cadena($DetalleRecurso->getIdDetalleRecurso()));

            switch ($DetalleRecurso->getTipo()) {
                case '1':
                    if (isset($_FILES['txtImagenDetalle'])) {
                        $original = $_FILES['txtImagenDetalle'];
                        $nombre = $original['name'];
                        $permitido = array("image/png", "image/jpg", "image/jpeg");
                        $destino = "IMAGENES";
                        $Limit_KB = 4 * 1024;
                    } else {
                        $original = -1;
                    }

                    break;
                case '2':
                    if (isset($_FILES['txtVideoDetalle'])) {
                        $original = $_FILES['txtVideoDetalle'];
                        $nombre = $original['name'];
                        $permitido = array("video/mp4");
                        $destino = "VIDEOS";
                        $Limit_KB = 17 * 1024;
                    } else {
                        $original = -1;
                    }

                    break;
                case '3':
                    if (isset($_FILES['txtPdfDetalle'])) {
                        $original = $_FILES['txtPdfDetalle'];
                        $nombre = $original['name'];
                        $permitido = array("application/pdf");
                        $destino = "PDF";
                        $Limit_KB = 5 * 1024;
                    } else {
                        $original = -1;
                    }

                    break;
                default:
                    break;
            }
            $detallerecurso = detallerecursoModelo::datos_detallerecurso_modelo($this->conexion_db, "unico", $DetalleRecurso);
            if ($detallerecurso["countFilter"] == 0) {

                $insBeanCrud->setMessageServer("error en el servidor, No se encuentra registrado el detallerecurso");
            } else {

                if ($original == -1) {
                    $DetalleRecurso->setArchivo($detallerecurso["list"][0]['archivo']);
                    $stmt = detallerecursoModelo::actualizar_detallerecurso_modelo($this->conexion_db, $DetalleRecurso);
                    if ($stmt->execute()) {
                        $this->conexion_db->commit();
                        $insBeanCrud->setMessageServer("ok");
                        $insBeanCrud->setBeanPagination(self::paginador_detallerecurso_controlador($this->conexion_db, 0, 5, $DetalleRecurso->getRecurso()));

                    } else {

                        $insBeanCrud->setMessageServer("error en el servidor, No hemos podido actualizar el archivo");
                    }

                } else {
                    if ($original['error'] > 0) {
                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otro archivo");
                    } else {
                        $resultado_guardado = mainModel::archivo($permitido, $Limit_KB, $original, $nombre, "./adjuntos/recurso/" . $destino . "/");
                        if ($resultado_guardado != "") {
                            $DetalleRecurso->setArchivo($resultado_guardado);
                            $stmt = detallerecursoModelo::actualizar_detallerecurso_modelo($this->conexion_db, $DetalleRecurso);
                            if ($stmt->execute()) {
                                switch ($detallerecurso["list"][0]['tipo']) {
                                    case '1':
                                        unlink('./adjuntos/recurso/IMAGENES/' . $detallerecurso["list"][0]['archivo']);
                                        break;
                                    case '2':
                                        unlink('./adjuntos/recurso/VIDEOS/' . $detallerecurso["list"][0]['archivo']);
                                        break;
                                    case '3':
                                        unlink('./adjuntos/recurso/PDF/' . $detallerecurso["list"][0]['archivo']);
                                        break;
                                }
                                $this->conexion_db->commit();
                                $insBeanCrud->setMessageServer("ok");
                                $insBeanCrud->setBeanPagination(self::paginador_detallerecurso_controlador($this->conexion_db, 0, 5, $DetalleRecurso->getRecurso()));

                            } else {

                                $insBeanCrud->setMessageServer("error en el servidor, No hemos podido actualizar el archivo");
                            }
                        } else {
                            $insBeanCrud->setMessageServer("error en el servidor, Hubo un error al guardar el archivo");

                        }

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
