<?php

require_once './modelos/albumModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';

class albumControlador extends albumModelo
{

    public function agregar_album_controlador($Album)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Album->setDesde(mainModel::limpiar_cadena($Album->getDesde()));
            $Album->setHasta(mainModel::limpiar_cadena($Album->getHasta()));
            $Album->setTipo(mainModel::limpiar_cadena($Album->getTipo()));
            $Album->setNombre(mainModel::limpiar_cadena($Album->getNombre()));

            if (isset($_FILES['txtVideoAlbum'])) {
                $original = $_FILES['txtVideoAlbum'];
                $nombre = $original['name'];
                switch ($Album->getTipo()) {
                    case '1':
                        $permitido = array("image/png", "image/jpg", "image/jpeg");
                        $limit_kb = 1700;
                        break;
                    case '2':
                        $permitido = array("video/mp4");
                        $limit_kb = (500 * 1024);
                        break;

                }
                if ($original['error'] > 0) {
                    $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otro video");
                } else {
                    $resultado = mainModel::archivo($permitido, $limit_kb, $original, $nombre, "./adjuntos/album/");
                    if ($resultado != "") {
                        $Album->setVideo($resultado);
                        $stmt = $this->conexion_db->prepare("SELECT COUNT(idalbum) AS CONTADOR FROM `album` WHERE desde=:IDalbum");
                        $stmt->bindValue(":IDalbum", $Album->getDesde(), PDO::PARAM_STR);
                        $stmt->execute();
                        $datos3 = $stmt->fetchAll();

                        foreach ($datos3 as $row3) {
                            if ($row3['CONTADOR'] == 0) {
                                $stmt = albumModelo::agregar_album_modelo($this->conexion_db, $Album);

                                if ($stmt->execute()) {
                                    $this->conexion_db->commit();
                                    $insBeanCrud->setMessageServer("ok");
                                    $insBeanCrud->setBeanPagination(self::paginador_album_controlador($this->conexion_db, 0, 5));

                                } else {
                                    $insBeanCrud->setMessageServer("No hemos podido registrar el archivo");
                                }
                                $stmt->closeCursor();
                                $stmt = null;

                            } else {
                                $insBeanCrud->setMessageServer("Ya ingresaste archivos para estos Subtítulos");
                            }
                        }

                    } else {
                        $insBeanCrud->setMessageServer("Hubo un error al guardar el video,formato no permitido o tamaño excedido, cambie el nombre del video o seleccione otro video");
                    }

                }
            } else {
                $insBeanCrud->setMessageServer("ingresa video");
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
    public function datos_album_controlador($tipo, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);
            $insBeanCrud->setBeanPagination(albumModelo::datos_album_modelo($this->conexion_db, $tipo, $codigo));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();

    }
    public function paginador_album_controlador($conexion, $inicio, $registros)
    {
        $insBeanPagination = new BeanPagination();
        try {

            $stmt = $conexion->query("SELECT COUNT(idalbum) AS CONTADOR FROM `album` where tipo=1");
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT * FROM `album` where tipo=1 ORDER BY desde ASC LIMIT ?,?");
                    $stmt->bindValue(1, $inicio, PDO::PARAM_INT);
                    $stmt->bindValue(2, $registros, PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insAlbum = new Album();
                        $insAlbum->setIdAlbum($row['idalbum']);
                        $insAlbum->setDesde($row['desde']);
                        $insAlbum->setHasta($row['hasta']);
                        $insAlbum->setVideo($row['video']);
                        $insAlbum->setTipo($row['tipo_archivo']);
                        $insAlbum->setNombre($row['nombre']);
                        $insBeanPagination->setList($insAlbum->__toString());
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
    public function bean_paginador_album_controlador($pagina, $registros)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_album_controlador($this->conexion_db, $inicio, $registros));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_album_controlador($Album)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $album = albumModelo::datos_album_modelo($this->conexion_db, "unico", $Album);
            if ($album["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra el album");
            } else {
                $stmt = albumModelo::eliminar_album_modelo($this->conexion_db, mainModel::limpiar_cadena($Album->getIdalbum()));

                if ($stmt->execute()) {
                    $stmt = $this->conexion_db->prepare("DELETE FROM `album` WHERE padre=:IDalbum");
                    $stmt->bindValue(":IDalbum", $Album->getIdalbum(), PDO::PARAM_INT);
                    if ($stmt->execute()) {
                        $this->conexion_db->commit();
                        unlink('./adjuntos/album/' . $album["list"][0]['video']);
                        $insBeanCrud->setMessageServer("ok");
                        $insBeanCrud->setBeanPagination(self::paginador_album_controlador($this->conexion_db, 0, 5));
                    }

                } else {
                    $insBeanCrud->setMessageServer("No se eliminó el video");
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
    public function actualizar_album_controlador($Album)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Album->setDesde(mainModel::limpiar_cadena($Album->getDesde()));
            $Album->setHasta(mainModel::limpiar_cadena($Album->getHasta()));
            $Album->setIdalbum(mainModel::limpiar_cadena($Album->getIdalbum()));
            $Album->setNombre(mainModel::limpiar_cadena($Album->getNombre()));
            $lalbum = albumModelo::datos_album_modelo($this->conexion_db, "unico", $Album);
            if ($lalbum["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra el album");
            } else {
                if (isset($_FILES['txtVideoAlbum'])) {
                    $original = $_FILES['txtVideoAlbum'];
                    $nombre = $original['name'];
                    switch ($Album->getTipo()) {
                        case '1':
                            $permitido = array("image/png", "image/jpg", "image/jpeg");
                            $limit_kb = 1700;
                            break;
                        case '2':
                            $permitido = array("video/mp4");
                            $limit_kb = (500 * 1024);
                            break;

                    }
                    if ($original['error'] > 0) {
                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                    } else {
                        $resultado = mainModel::archivo($permitido, $limit_kb, $original, $nombre, "./adjuntos/album/");
                        if ($resultado != "") {
                            $Album->setVideo($resultado);
                            $stmt = albumModelo::actualizar_album_modelo($this->conexion_db, $Album);

                            if ($stmt->execute()) {
                                $stmt = $this->conexion_db->prepare("DELETE FROM `album` WHERE padre=:IDalbum");
                                $stmt->bindValue(":IDalbum", $Album->getIdalbum(), PDO::PARAM_INT);
                                if ($stmt->execute()) {
                                    $stmt = $this->conexion_db->prepare("INSERT INTO `album` (desde,hasta,video,tipo,padre,tipo_archivo) VALUES(?,?,?,?,?,?)");
                                    $valor = false;
                                    $ArrayDesde = explode(".", $Album->getDesde());
                                    $ContadorDesde = $ArrayDesde[3];
                                    $ArrayHasta = explode(".", $Album->getHasta());

                                    for ($i = 0; $i <= ($ArrayHasta[3] - $ArrayDesde[3]); $i++) {
                                        $stmt->bindValue(1, $ArrayDesde[0] . '.' . $ArrayDesde[1] . '.' . $ArrayDesde[2] . '.' . $ContadorDesde, PDO::PARAM_STR);
                                        $stmt->bindValue(2, "", PDO::PARAM_STR);
                                        $stmt->bindValue(3, $resultado, PDO::PARAM_STR);
                                        $stmt->bindValue(4, 0, PDO::PARAM_INT);
                                        $stmt->bindValue(5, $Album->getIdalbum(), PDO::PARAM_INT);
                                        $stmt->bindValue(6, $Album->getTipo(), PDO::PARAM_INT);
                                        $ContadorDesde++;
                                        if (strlen($ContadorDesde) == 1) {
                                            $ContadorDesde = "0" . $ContadorDesde;
                                        }

                                        $valor = $stmt->execute();

                                    }
                                    if ($valor) {
                                        unlink('./adjuntos/album/' . $lalbum["list"][0]['video']);

                                        $this->conexion_db->commit();
                                        $insBeanCrud->setMessageServer("ok");
                                        $insBeanCrud->setBeanPagination(self::paginador_album_controlador($this->conexion_db, 0, 5));
                                    }
                                }

                            } else {
                                $insBeanCrud->setMessageServer("No hemos podido actualizar el video");
                            }
                            $stmt->closeCursor();
                            $stmt = null;
                        } else {
                            $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido");

                        }
                    }
                } else {
                    $Album->setVideo($lalbum["list"][0]['video']);
                    $stmt = albumModelo::actualizar_album_modelo($this->conexion_db, $Album);
                    if ($stmt->execute()) {
                        $stmt = $this->conexion_db->prepare("DELETE FROM `album` WHERE padre=:IDalbum");
                        $stmt->bindValue(":IDalbum", $Album->getIdalbum(), PDO::PARAM_INT);
                        if ($stmt->execute()) {
                            $stmt = $this->conexion_db->prepare("INSERT INTO `album` (desde,hasta,video,tipo,padre,tipo_archivo) VALUES(?,?,?,?,?,?)");
                            $valor = false;
                            $ArrayDesde = explode(".", $Album->getDesde());
                            $ContadorDesde = $ArrayDesde[3];
                            $ArrayHasta = explode(".", $Album->getHasta());

                            for ($i = 0; $i <= ($ArrayHasta[3] - $ArrayDesde[3]); $i++) {
                                $stmt->bindValue(1, $ArrayDesde[0] . '.' . $ArrayDesde[1] . '.' . $ArrayDesde[2] . '.' . $ContadorDesde, PDO::PARAM_STR);
                                $stmt->bindValue(2, "", PDO::PARAM_STR);
                                $stmt->bindValue(3, $Album->getVideo(), PDO::PARAM_STR);
                                $stmt->bindValue(4, 0, PDO::PARAM_INT);
                                $stmt->bindValue(5, $Album->getIdalbum(), PDO::PARAM_INT);
                                $stmt->bindValue(6, $Album->getTipo(), PDO::PARAM_INT);
                                $ContadorDesde++;
                                if (strlen($ContadorDesde) == 1) {
                                    $ContadorDesde = "0" . $ContadorDesde;
                                }

                                $valor = $stmt->execute();

                            }
                            if ($valor) {
                                $this->conexion_db->commit();
                                $insBeanCrud->setMessageServer("ok");
                                $insBeanCrud->setBeanPagination(self::paginador_album_controlador($this->conexion_db, 0, 5));
                            }
                        }

                    } else {
                        $insBeanCrud->setMessageServer("No hemos podido actualizar el video");
                    }
                    $stmt->closeCursor();
                    $stmt = null;
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
