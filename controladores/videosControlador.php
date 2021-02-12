<?php

require_once './modelos/videosModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';
class videosControlador extends videosModelo
{
    public function agregar_videos_controlador($Video)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Video->setNombre(mainModel::limpiar_cadena($Video->getNombre()));
            $Video->setEnlace(mainModel::limpiar_cadena($Video->getEnlace()));

            $originalI = $_FILES['txtImagenPromotorVideo'];
            $nombreI = $originalI['name'];

            if ($originalI['error'] > 0) {
                $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");

            } else {
                $resultadoI = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"), 3700, $originalI, $nombreI, "./adjuntos/video-imagenes/");

                if ($resultadoI != "") {
                    $Video->setArchivo("ninguno");
                    $Video->setImagen($resultadoI);
                    $stmt = videosModelo::agregar_videos_modelo($this->conexion_db, $Video);
                    if ($stmt->execute()) {
                        $this->conexion_db->commit();
                        $insBeanCrud->setMessageServer("ok");
                        $insBeanCrud->setBeanPagination(self::paginador_videos_controlador($this->conexion_db, 0, 5, 3));
                    } else {

                        $insBeanCrud->setMessageServer("No hemos podido registrar el video");
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
    public function datos_videos_controlador($tipo, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);
            $insBeanCrud->setBeanPagination(videosModelo::datos_videos_modelo($this->conexion_db, $tipo, $codigo));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();

    }
    public function paginador_videos_controlador($conexion, $inicio, $registros, $ubica)
    {
        $insBeanPagination = new BeanPagination();
        try {

            if ($ubica > 0) {
                $stmt = $conexion->prepare("SELECT COUNT(idvideos) AS CONTADOR FROM `videos` WHERE ubicacion=? ");
                $stmt->bindParam(1, $ubica, PDO::PARAM_INT);
                $stmt->execute();
                $datos = $stmt->fetchAll();
                foreach ($datos as $row) {
                    $insBeanPagination->setCountFilter($row['CONTADOR']);
                    if ($row['CONTADOR'] > 0) {
                        $stmt = $conexion->prepare("SELECT * FROM videos
                        WHERE ubicacion=? ORDER BY ubicacion  ASC LIMIT ?,?");
                        $stmt->bindParam(1, $ubica, PDO::PARAM_INT);
                        $stmt->bindParam(2, $inicio, PDO::PARAM_INT);
                        $stmt->bindParam(3, $registros, PDO::PARAM_INT);
                        $stmt->execute();
                        $datos = $stmt->fetchAll();
                        foreach ($datos as $row) {
                            $insVideo = new Video();
                            $insVideo->setIdvideo($row['idvideos']);
                            $insVideo->setNombre($row['nombre']);
                            $insVideo->setImagen($row['imagen']);
                            $insVideo->setEnlace($row['enlace']);
                            $insVideo->setArchivo($row['video']);
                            $insVideo->setUbicacion($row['ubicacion']);

                            $insBeanPagination->setList($insVideo->__toString());
                        }
                    }

                }

            } else {
                $stmt = $conexion->query("SELECT COUNT(idvideos) AS CONTADOR FROM `videos` ");
                $datos = $stmt->fetchAll();
                foreach ($datos as $row) {
                    $insBeanPagination->setCountFilter($row['CONTADOR']);
                    if ($row['CONTADOR'] > 0) {
                        $stmt = $conexion->prepare("SELECT * FROM videos
                        WHERE ORDER BY ubicacion  ASC LIMIT ?,?");
                        $stmt->bindParam(1, $inicio, PDO::PARAM_INT);
                        $stmt->bindParam(2, $registros, PDO::PARAM_INT);
                        $stmt->execute();
                        $datos = $stmt->fetchAll();
                        foreach ($datos as $row) {
                            $insVideo = new Video();
                            $insVideo->setIdvideo($row['idvideos']);
                            $insVideo->setNombre($row['nombre']);
                            $insVideo->setImagen($row['imagen']);
                            $insVideo->setEnlace($row['enlace']);
                            $insVideo->setArchivo($row['video']);
                            $insVideo->setUbicacion($row['ubicacion']);

                            $insBeanPagination->setList($insVideo->__toString());
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
    public function bean_paginador_videos_controlador($pagina, $registros, $ubica)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $ubica = mainModel::limpiar_cadena($ubica);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_videos_controlador($this->conexion_db, $inicio, $registros, $ubica));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_videos_controlador($Video)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $listVideo = videosModelo::datos_videos_modelo($this->conexion_db, "unico", $Video);
            if ($listVideo["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra el video");
            } else {
                $stmt = videosModelo::eliminar_videos_modelo($this->conexion_db, $Video->getIdvideo());
                if ($stmt->execute()) {
                    unlink("./adjuntos/video-imagenes/" . $listVideo["list"][0]['imagen']);
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_videos_controlador($this->conexion_db, 0, 5, 3));

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
    public function actualizar_videos_controlador($Video)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Video->setNombre(mainModel::limpiar_cadena($Video->getNombre()));
            $Video->setEnlace(mainModel::limpiar_cadena($Video->getEnlace()));
            if (isset($_FILES['txtImagenPromotorVideo'])) {
                $originalI = $_FILES['txtImagenPromotorVideo'];
                $nombreI = $originalI['name'];
                if ($originalI['error'] > 0) {
                    $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                } else {
                    //  $resultadoV=mainModel::archivo(array("video/mp4"),(17*1024),$originalV, $nombreV,"../adjuntos/videos/");
                    $resultadoI = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"), 1700, $originalI, $nombreI, "./adjuntos/video-imagenes/");
                    if ($resultadoI != "") {
                        $listVideo = videosModelo::datos_videos_modelo($this->conexion_db, "unico", $Video);
                        $Video->setImagen($resultadoI);
                        $stmt = videosModelo::actualizar_videos_modelo($this->conexion_db, $Video);
                        if ($stmt->execute()) {
                            unlink('./adjuntos/video-imagenes/' . $listVideo["list"][0]['imagen']);
                            $this->conexion_db->commit();
                            $insBeanCrud->setMessageServer("ok");
                            $insBeanCrud->setBeanPagination(self::paginador_videos_controlador($this->conexion_db, 0, 5, 3));

                        } else {

                            $insBeanCrud->setMessageServer("No hemos podido actualizar el video");
                        }
                    } else {
                        $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido");

                    }
                }
            } else {
                $listVideo = videosModelo::datos_videos_modelo($this->conexion_db, "unico", $Video);
                $Video->setImagen($listVideo["list"][0]['imagen']);
                $stmt = videosModelo::actualizar_videos_modelo($this->conexion_db, $Video);
                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_videos_controlador($this->conexion_db, 0, 5, 3));

                } else {

                    $insBeanCrud->setMessageServer("No hemos podido actualizar el video");
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
    public function actualizarInicio_videos_controlador($Video)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $originalV = $_FILES['txtVideoInicio'];
            $nombreV = $originalV['name'];

            if ($originalV['error'] > 0) {
                $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir los archivos seleccione otro Video");
            } else {
                $resultadoV = mainModel::archivo(array("video/mp4", "video/3gpp", "video/ogg"), (512 * 1024), $originalV, $nombreV, "./adjuntos/videos/");
                if ($resultadoV != "") {
                    $Video->setNombre($resultadoV);
                    $Video->setArchivo($resultadoV);
                    if ($Video->getIdvideo() == 0) {
                        $stmt = videosModelo::agregar_videos_modelo($this->conexion_db, $Video);
                    } else {
                        $stmt = videosModelo::actualizar_videos_modelo($this->conexion_db, $Video);

                    }
                    if ($stmt->execute()) {
                        if ($Video->getIdvideo() > 0) {
                            $listVideo = videosModelo::datos_videos_modelo($this->conexion_db, "unico", $Video);
                            unlink('./adjuntos/videos/' . $listVideo["list"][0]['video']);
                        }
                        $this->conexion_db->commit();
                        $insBeanCrud->setMessageServer("ok");
                        $insBeanCrud->setBeanPagination(self::paginador_videos_controlador($this->conexion_db, 0, 5, 1));
                    } else {
                        $insBeanCrud->setMessageServer("No hemos podido actualizar el video");
                    }
                } else {
                    $insBeanCrud->setMessageServer("Hubo un error al guardar el video,formato no permitido o tamaño excedido");

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

    public function datos_videos_ubicacion_controlador($ubicacion)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $ubicacion = mainModel::limpiar_cadena($ubicacion);
            $insBeanCrud->setBeanPagination(videosModelo::datos_videos_tipo_modelo($this->conexion_db, $ubicacion));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();

    }

}
