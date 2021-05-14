<?php

require_once './modelos/blogModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';

class blogControlador extends blogModelo
{

    public function agregar_blog_controlador($Blog)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Blog->setDescripcion(mainModel::limpiar_cadena($Blog->getDescripcion()));
            $Blog->setResumen(mainModel::limpiar_cadena($Blog->getResumen()));
            $Blog->setAutor(mainModel::limpiar_cadena($Blog->getAutor()));
            $Blog->setComentario(mainModel::limpiar_cadena($Blog->getComentario()));
            $Blog->setDescripcionAutor(mainModel::limpiar_cadena($Blog->getDescripcionAutor()));

            switch ((int) $Blog->getTipoArchivo()) {
                case 1:
                    $original = $_FILES['txtImagenBlog'];
                    $nombre = $original['name'];
                    $permitido = array("image/png", "image/jpg", "image/jpeg");
                    $destino = "IMAGENES";
                    $limit_kb = 4 * 1024;
                    break;
                case 2:
                    $original = $_FILES['txtVideoBlog'];
                    $nombre = $original['name'];
                    $permitido = array("video/mp4");
                    $destino = "VIDEOS";
                    $limit_kb = (17 * 1024);
                    break;
            }
            if (!isset($_FILES['txtImagenAutorBlog'])) {
                $insBeanCrud->setMessageServer("Ingrese foto del Autor");
            } else {
                $originalAutor = $_FILES['txtImagenAutorBlog'];
                $nombreAutor = $originalAutor['name'];
                if ($original['error'] > 0 || $originalAutor['error'] > 0) {
                    if ($originalAutor['error'] > 0) {

                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra foto del autor");
                    } else {

                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                    }

                } else {
                    $resultado = mainModel::archivo($permitido, $limit_kb, $original, $nombre, "./adjuntos/blog/" . $destino . "/");
                    if ($resultado != "") {
                        $Blog->setArchivo($resultado);
                        $resultadoAutor = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"), 4 * 1024, $originalAutor, $nombreAutor, "./adjuntos/blog/IMAGENES/");
                        if ($resultadoAutor != "") {
                            $Blog->setFoto($resultadoAutor);
                            $stmt = blogModelo::agregar_blog_modelo($this->conexion_db, $Blog);
                            if ($stmt->execute()) {
                                $this->conexion_db->commit();
                                $insBeanCrud->setMessageServer("ok");
                                $insBeanCrud->setBeanPagination(self::paginador_blog_controlador($this->conexion_db, 0, 20));
                            } else {
                                $insBeanCrud->setMessageServer("No hemos podido registrar el blog");
                            }
                            $stmt->closeCursor();
                            $stmt = null;
                        } else {
                            $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido, cambie el nombre de la imagen o seleccione otra foto del autor");
                        }

                    } else {
                        $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido, cambie el nombre de la imagen o seleccione otra imagen");
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
    public function datos_blog_controlador($tipo, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);
            $insBeanCrud->setBeanPagination(blogModelo::datos_blog_modelo($this->conexion_db, $tipo, $codigo));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();

    }
    public function paginador_blog_controlador($conexion, $inicio, $registros)
    {
        $insBeanPagination = new BeanPagination();
        try {

            $stmt = $conexion->query("SELECT COUNT(idblog) AS CONTADOR FROM `blog`");
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT * FROM `blog` ORDER BY idblog DESC LIMIT ?,?");
                    $stmt->bindValue(1, $inicio, PDO::PARAM_INT);
                    $stmt->bindValue(2, $registros, PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBlog = new Blog();
                        $insBlog->setIdBlog($row['idblog']);
                        $insBlog->setTitulo($row['titulo']);
                        $insBlog->setResumen($row['resumen']);
                        $insBlog->setAutor($row['autor']);
                        $insBlog->setFoto($row['foto']);
                        $insBlog->setDescripcion($row['descripcion']);
                        $insBlog->setDescripcionAutor($row['autordescripcion']);
                        $insBlog->setArchivo($row['archivo']);
                        $insBlog->setTipoArchivo($row['tipoArchivo']);
                        $insBlog->setComentario($row['comentario']);

                        $insBeanPagination->setList($insBlog->__toString());
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
    public function bean_paginador_blog_controlador($pagina, $registros)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_blog_controlador($this->conexion_db, $inicio, $registros));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_blog_controlador($Blog)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $blog = blogModelo::datos_blog_modelo($this->conexion_db, "unico", $Blog);
            if ($blog["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra el blog");
            } else {
                $stmt = blogModelo::eliminar_blog_modelo($this->conexion_db, mainModel::limpiar_cadena($Blog->getIdblog()));

                if ($stmt->execute()) {

                    switch ($blog["list"][0]['tipoArchivo']) {
                        case '1':
                            if ($blog["list"][0]['archivo'] != "") {
                                unlink('./adjuntos/blog/IMAGENES/' . $blog["list"][0]['archivo']);
                            }

                            break;
                        case '2':
                            if ($blog["list"][0]['archivo'] != "") {
                                unlink('./adjuntos/blog/VIDEOS/' . $blog["list"][0]['archivo']);
                            }

                            break;
                    }
                    if ($blog["list"][0]['foto'] != "" || $blog["list"][0]['foto'] != null) {
                        unlink('./adjuntos/blog/IMAGENES/' . $blog["list"][0]['foto']);
                    }
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_blog_controlador($this->conexion_db, 0, 20));

                } else {
                    $insBeanCrud->setMessageServer("No se eliminó el blog");
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
    public function actualizar_blog_controlador($Blog)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Blog->setDescripcion(mainModel::limpiar_cadena($Blog->getDescripcion()));
            $Blog->setResumen(mainModel::limpiar_cadena($Blog->getResumen()));
            $Blog->setAutor(mainModel::limpiar_cadena($Blog->getAutor()));
            $Blog->setComentario(mainModel::limpiar_cadena($Blog->getComentario()));
            $Blog->setDescripcionAutor(mainModel::limpiar_cadena($Blog->getDescripcionAutor()));
            switch ((int) $Blog->getTipoArchivo()) {
                case 1:
                    if (isset($_FILES['txtImagenBlog'])) {
                        $original = $_FILES['txtImagenBlog'];
                        $nombre = $original['name'];
                        $permitido = array("image/png", "image/jpg", "image/jpeg");
                        $destino = "IMAGENES";
                        $limit_kb = 4 * 1024;
                    } else {
                        $nombre = "";
                    }

                    break;
                case 2:
                    if (isset($_FILES['txtVideoBlog'])) {
                        $original = $_FILES['txtVideoBlog'];
                        $nombre = $original['name'];
                        $permitido = array("video/mp4");
                        $destino = "VIDEOS";
                        $limit_kb = (17 * 1024);
                    } else {
                        $nombre = "";
                    }

                    break;
            }

            $lblog = blogModelo::datos_blog_modelo($this->conexion_db, "unico", $Blog);
            if ($lblog["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra el blog");
            } else {
                if (isset($_FILES['txtImagenAutorBlog'])) {
                    $valorDefault = true;
                    $originalAutor = $_FILES['txtImagenAutorBlog'];
                    $nombreAutor = $originalAutor['name'];
                    if ($originalAutor['error'] > 0) {
                        $valorDefault = false;
                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra foto del autor");
                    } else {
                        $resultadoAutor = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"), 4 * 1024, $originalAutor, $nombreAutor, "./adjuntos/blog/IMAGENES/");
                        if ($resultadoAutor != "") {
                            $Blog->setFoto($resultadoAutor);
                        } else {
                            $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido, cambie el nombre de la imagen o seleccione otra foto del autor");
                            $valorDefault = false;
                        }

                    }

                    if ($valorDefault) {
                        if ($nombre != "") {
                            if ($original['error'] > 0) {
                                $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                            } else {
                                $resultado = mainModel::archivo($permitido, $limit_kb, $original, $nombre, "./adjuntos/blog/" . $destino . "/");
                                if ($resultado != "") {
                                    $Blog->setArchivo($resultado);
                                    $stmt = blogModelo::actualizar_blog_modelo($this->conexion_db, $Blog);

                                    if ($stmt->execute()) {
                                        switch ($Blog->getTipoArchivo()) {
                                            case '1':
                                                unlink('./adjuntos/blog/IMAGENES/' . $lblog["list"][0]['archivo']);
                                                break;
                                            case '2':
                                                unlink('./adjuntos/blog/VIDEOS/' . $lblog["list"][0]['archivo']);
                                                break;
                                        }
                                        $this->conexion_db->commit();
                                        $insBeanCrud->setMessageServer("ok");
                                        $insBeanCrud->setBeanPagination(self::paginador_blog_controlador($this->conexion_db, 0, 20));

                                    } else {
                                        $insBeanCrud->setMessageServer("No hemos podido actualizar el blog");
                                    }
                                    $stmt->closeCursor();
                                    $stmt = null;
                                } else {
                                    $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido");

                                }
                            }
                        } else {
                            $Blog->setArchivo($lblog["list"][0]['archivo']);
                            $stmt = blogModelo::actualizar_blog_modelo($this->conexion_db, $Blog);
                            if ($stmt->execute()) {
                                $this->conexion_db->commit();
                                $insBeanCrud->setMessageServer("ok");
                                $insBeanCrud->setBeanPagination(self::paginador_blog_controlador($this->conexion_db, 0, 20));
                            } else {
                                $insBeanCrud->setMessageServer("No hemos podido actualizar el blog");
                            }
                            $stmt->closeCursor();
                            $stmt = null;
                        }
                    }

                } else {
                    $Blog->setFoto($lblog["list"][0]['foto']);
                    if ($nombre != "") {
                        if ($original['error'] > 0) {
                            $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                        } else {
                            $resultado = mainModel::archivo($permitido, $limit_kb, $original, $nombre, "./adjuntos/blog/" . $destino . "/");
                            if ($resultado != "") {
                                $Blog->setArchivo($resultado);
                                $stmt = blogModelo::actualizar_blog_modelo($this->conexion_db, $Blog);

                                if ($stmt->execute()) {
                                    switch ($Blog->getTipoArchivo()) {
                                        case '1':
                                            unlink('./adjuntos/blog/IMAGENES/' . $lblog["list"][0]['archivo']);
                                            break;
                                        case '2':
                                            unlink('./adjuntos/blog/VIDEOS/' . $lblog["list"][0]['archivo']);
                                            break;
                                    }
                                    $this->conexion_db->commit();
                                    $insBeanCrud->setMessageServer("ok");
                                    $insBeanCrud->setBeanPagination(self::paginador_blog_controlador($this->conexion_db, 0, 20));

                                } else {
                                    $insBeanCrud->setMessageServer("No hemos podido actualizar el blog");
                                }
                                $stmt->closeCursor();
                                $stmt = null;
                            } else {
                                $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido");

                            }
                        }
                    } else {
                        $Blog->setArchivo($lblog["list"][0]['archivo']);
                        $stmt = blogModelo::actualizar_blog_modelo($this->conexion_db, $Blog);
                        if ($stmt->execute()) {
                            $this->conexion_db->commit();
                            $insBeanCrud->setMessageServer("ok");
                            $insBeanCrud->setBeanPagination(self::paginador_blog_controlador($this->conexion_db, 0, 20));
                        } else {
                            $insBeanCrud->setMessageServer("No hemos podido actualizar el blog");
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
