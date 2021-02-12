<?php

require_once './modelos/subcapituloModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';
class subcapituloControlador extends subcapituloModelo
{
    public function agregar_subcapitulo_controlador($SubTitulo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();

            $SubTitulo->setNombre(mainModel::limpiar_cadena($SubTitulo->getNombre()));
            $SubTitulo->setCodigo(mainModel::limpiar_cadena($SubTitulo->getCodigo()));

            $SubTitulo->setTitulo(mainModel::limpiar_cadena($SubTitulo->getTitulo()));

            if (!isset($_FILES['txtPdfSubtitulo']) && !isset($_FILES['txtImagenSubtituloLeccion'])) {
                if (!isset($_FILES['txtPdfSubtitulo'])) {
                    $insBeanCrud->setMessageServer("vacío, selecciona pdf");
                } elseif (!isset($_FILES['txtImagenSubtituloLeccion'])) {
                    $insBeanCrud->setMessageServer("vacío, selecciona Imagen para la lección");
                }

            } else {
                $original = $_FILES['txtPdfSubtitulo'];
                $nombre = $SubTitulo->getCodigo() . ".pdf";

                //
                $original_imagen_leccion = $_FILES['txtImagenSubtituloLeccion'];
                $nombre_imagen_leccion = $original_imagen_leccion['name'];

                $array = explode('.', $SubTitulo->getCodigo());
                $arrayName = explode('.', $original['name']);
                $exten = array_pop($arrayName);
                $consulta4 = subcapituloModelo::datos_subcapitulo_modelo($this->conexion_db, "codigo", $SubTitulo);
                if ($original['error'] > 0) {
                    $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione nuevamente el PDF");
                } elseif ($original_imagen_leccion['error'] > 0) {
                    $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otro imagen para la leccion");
                } else if ($consulta4['countFilter'] > 0) {
                    $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Ya se encuentra un subtitulo con el mismo código");
                } else {
                    //5120 KB
                    $resultado_guardado = self::archivo(array("application/pdf"), 5120, $original, $nombre,
                        "./adjuntos/archivos/" . $array[0] . "/" . $array[0] . "." . $array[1] . "." . $array[2] . "/PDF/");
                    if ($resultado_guardado != "") {
                        $resultado3 = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"), 1700, $original_imagen_leccion, $nombre_imagen_leccion, "./adjuntos/libros/subtitulos/");
                        if ($resultado3 != "") {
                            $SubTitulo->setImagen($resultado3);
                            $SubTitulo->setPdf($resultado_guardado);
                            $stmt = subcapituloModelo::agregar_subcapitulo_modelo($this->conexion_db, $SubTitulo);
                            if ($stmt->execute()) {
                                $this->conexion_db->commit();
                                $insBeanCrud->setMessageServer("ok");
                                $insBeanCrud->setBeanPagination(self::paginador_subcapitulo_controlador($this->conexion_db, 0, 20, $SubTitulo->getTitulo()));

                            } else {

                                $insBeanCrud->setMessageServer("error en el servidor, No hemos podido registrar el subtitulo");
                            }

                        } else {
                            $insBeanCrud->setMessageServer("error en el servidor, Hubo un error al guardar la imagen del subtitulo para la lección");
                        }

                    } else {

                        $insBeanCrud->setMessageServer("error en el servidor, Hubo un error al guardar el PDF");
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
    public function datos_subcapitulo_controlador($tipo, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);
            $insBeanCrud->setBeanPagination(subcapituloModelo::datos_subcapitulo_modelo($this->conexion_db, $tipo, $codigo));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function paginador_subcapitulo_controlador($conexion, $inicio, $registros, $codigo)
    {
        $insBeanPagination = new BeanPagination();
        try {
            $stmt = $conexion->prepare("SELECT COUNT(idsubtitulo) AS CONTADOR  FROM `subtitulo` WHERE titulo_idtitulo=? ");
            $stmt->bindValue(1, $codigo, PDO::PARAM_INT);
            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT * FROM `subtitulo` as s inner join `titulo` as t ON s.titulo_idtitulo=t.idtitulo WHERE s.titulo_idtitulo=? ORDER BY s.codigo_subtitulo ASC LIMIT ?,?");
                    $stmt->bindValue(1, $codigo, PDO::PARAM_INT);
                    $stmt->bindValue(2, $inicio, PDO::PARAM_INT);
                    $stmt->bindValue(3, $registros, PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();

                    foreach ($datos as $row) {
                        $insTitulo = new Titulo();
                        $insTitulo->setIdTitulo($row['idtitulo']);
                        $insTitulo->setCodigo($row['codigoTitulo']);
                        $insTitulo->setPdf($row['PDF']);
                        $insTitulo->setNombre($row['tituloNombre']);
                        $insTitulo->setEstado($row['TituloEstado']);
                        $insTitulo->setDescripcion($row['tituloDescripcion']);

                        $insSubTitulo = new SubTitulo();
                        $insSubTitulo->setIdSubTitulo($row['idsubtitulo']);
                        $insSubTitulo->setCodigo($row['codigo_subtitulo']);
                        $insSubTitulo->setPdf($row['subtituloPDF']);
                        $insSubTitulo->setDescripcion($row['descripcion']);
                        $insSubTitulo->setNombre($row['nombre']);
                        $insSubTitulo->setImagen($row['subtitulo_imagen']);
                        $insSubTitulo->setTitulo($insTitulo->__toString());
                        $insBeanPagination->setList($insSubTitulo->__toString());
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
    public function bean_paginador_subcapitulo_controlador($pagina, $registros, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $codigo = mainModel::limpiar_cadena($codigo);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_subcapitulo_controlador($this->conexion_db, $inicio, $registros, $codigo));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_subcapitulo_controlador($SubTitulo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $SubTitulo->setIdSubTitulo(mainModel::limpiar_cadena($SubTitulo->getIdSubTitulo()));

            $subcapitulo = subcapituloModelo::datos_subcapitulo_modelo($this->conexion_db, "unico", $SubTitulo);

            if ($subcapitulo["countFilter"] == 0) {

                $insBeanCrud->setMessageServer("error en el servidor,No se encuentra el subtitulo registrado");
            } else {
                $SubTitulo->setCodigo($subcapitulo["list"][0]['codigo']);
                $lista = subcapituloModelo::datos_subcapitulo_modelo($this->conexion_db, "restriccion", $SubTitulo);
                if ($lista["countFilter"] > 0) {
                    $insBeanCrud->setMessageServer("error en el servidor,No se ha podido Eliminar el subTitulo porque esta asociado a un recurso");
                } else {
                    $stmt = $this->conexion_db->prepare("SELECT COUNT(idvideo_subtitulo) AS CONTADOR FROM `video_subtitulo` WHERE subtitulo_codigosubtitulo=:Codigo");
                    $stmt->bindValue(":Codigo", $SubTitulo->getCodigo(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {

                        if ($row['CONTADOR'] == 0) {
                            $stmt = subcapituloModelo::eliminar_subcapitulo_modelo($this->conexion_db, $SubTitulo->getIdSubTitulo());

                            if ($stmt->execute()) {
                                $this->conexion_db->commit();
                                $insBeanCrud->setMessageServer("ok");
                                if ($subcapitulo["list"][0]['pdf'] != "" || $subcapitulo["list"][0]['pdf'] != null) {
                                    $array = explode('.', $subcapitulo["list"][0]['pdf']);
                                    unlink('./adjuntos/archivos/' . $array[0] . '/' . $array[0] . '.' . $array[1] . '.' . $array[2] . "/PDF/" . $subcapitulo["list"][0]['pdf']);
                                }

                                if ($subcapitulo["list"][0]['imagen'] != "" || $subcapitulo["list"][0]['imagen'] != null) {
                                    unlink('./adjuntos/libros/subtitulos/' . $subcapitulo["list"][0]['imagen']);
                                }

                                $insBeanCrud->setBeanPagination(self::paginador_subcapitulo_controlador($this->conexion_db, 0, 20, $subcapitulo["list"][0]['titulo']['idtitulo']));

                            } else {

                                $insBeanCrud->setMessageServer("error en el servidor,No hemos podido Eliminar el subtitulo");
                            }
                        } else {
                            $insBeanCrud->setMessageServer("Primero elimina los PARRAFOS del subtitulo");
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
    public function actualizar_subcapitulo_controlador($SubTitulo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $SubTitulo->setNombre(mainModel::limpiar_cadena($SubTitulo->getNombre()));
            $SubTitulo->setCodigo(mainModel::limpiar_cadena($SubTitulo->getCodigo()));
            $SubTitulo->setTitulo(mainModel::limpiar_cadena($SubTitulo->getTitulo()));
            $SubTitulo->setIdSubTitulo(mainModel::limpiar_cadena($SubTitulo->getIdSubTitulo()));

            $consulta5 = subcapituloModelo::datos_subcapitulo_modelo($this->conexion_db, "unico", $SubTitulo);

            if ($consulta5["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("Ocurrio un error inesperado,No se encuentra el subtitulo");
            } else {
                $consulta4 = subcapituloModelo::datos_subcapitulo_modelo($this->conexion_db, "codigo-actualizar", $SubTitulo);
                if ($consulta4["countFilter"] > 0) {
                    $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Ya se encuentra un subtitulo con el mismo código");
                } else {
                    if (isset($_FILES['txtPdfSubtitulo']) && !(isset($_FILES['txtImagenSubtituloLeccion']))) {

                        $original = $_FILES['txtPdfSubtitulo'];
                        $nombre = $SubTitulo->getCodigo() . ".pdf";
                        $array = explode('.', $SubTitulo->getCodigo());
                        $arrayName = explode('.', $original['name']);
                        $exten = array_pop($arrayName);

                        if ($original['error'] > 0) {
                            $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione nuevamente el PDF");
                        } else {
                            $resultado_guardado = self::archivo(array("application/pdf"), 5120, $original, $nombre,
                                "./adjuntos/archivos/" . $array[0] . "/" . $array[0] . "." . $array[1] . "." . $array[2] . "/PDF/");
                            if ($resultado_guardado != "") {
                                $SubTitulo->setPdf($resultado_guardado);
                                $SubTitulo->setImagen($consulta5["list"][0]['imagen']);
                                $stmt = subcapituloModelo::actualizar_subcapitulo_modelo($this->conexion_db, $SubTitulo);
                                if ($stmt->execute()) {
                                    $this->conexion_db->commit();
                                    $insBeanCrud->setMessageServer("ok");
                                    $insBeanCrud->setBeanPagination(self::paginador_subcapitulo_controlador($this->conexion_db, 0, 20, $SubTitulo->getTitulo()));
                                } else {

                                    $insBeanCrud->setMessageServer("error en el servidor, No hemos podido actualizar el subtitulo");
                                }
                            } else {
                                $insBeanCrud->setMessageServer("error en el servidor, Hubo un error al guardar el PDF");

                            }

                        }

                    } else if (isset($_FILES['txtImagenSubtituloLeccion']) && !(isset($_FILES['txtPdfSubtitulo']))) {
                        $original_imagen = $_FILES['txtImagenSubtituloLeccion'];
                        $nombre_imagen = $original_imagen['name'];

                        if ($original_imagen['error'] > 0) {
                            $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                        } else {
                            $resultado2 = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"), 1700, $original_imagen, $nombre_imagen, "./adjuntos/libros/subtitulos/");
                            if ($resultado2 != "") {
                                $SubTitulo->setPdf($consulta5["list"][0]['pdf']);
                                $SubTitulo->setImagen($resultado2);
                                $stmt = subcapituloModelo::actualizar_subcapitulo_modelo($this->conexion_db, $SubTitulo);
                                if ($stmt->execute()) {

                                    if ($consulta5["list"][0]['imagen'] != "" || $consulta5["list"][0]['imagen'] != null) {
                                        unlink('./adjuntos/libros/subtitulos/' . $consulta5["list"][0]['imagen']);
                                    }
                                    $this->conexion_db->commit();
                                    $insBeanCrud->setMessageServer("ok");
                                    $insBeanCrud->setBeanPagination(self::paginador_subcapitulo_controlador($this->conexion_db, 0, 20, $SubTitulo->getTitulo()));
                                } else {
                                    $insBeanCrud->setMessageServer("error en el servidor, No hemos podido actualizar el subtitulo");
                                }
                            } else {
                                $insBeanCrud->setMessageServer("error en el servidor, Hubo un error al guardar el PDF");
                            }
                        }

                    } else if (isset($_FILES['txtImagenSubtituloLeccion']) && isset($_FILES['txtPdfSubtitulo'])) {
                        $original_imagen = $_FILES['txtImagenSubtituloLeccion'];
                        $nombre_imagen = $original_imagen['name'];

                        $original = $_FILES['txtPdfSubtitulo'];
                        $nombre = $SubTitulo->getCodigo() . ".pdf";
                        $array = explode('.', $SubTitulo->getCodigo());
                        $arrayName = explode('.', $original['name']);
                        $exten = array_pop($arrayName);

                        if ($original_imagen['error'] > 0) {
                            $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                        } elseif ($original['error'] > 0) {
                            $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione nuevamente el PDF");
                        } else {
                            //5120  KB
                            $resultado_guardado = self::archivo(array("application/pdf"), 5120, $original, $nombre,
                                "./adjuntos/archivos/" . $array[0] . "/" . $array[0] . "." . $array[1] . "." . $array[2] . "/PDF/");
                            if ($resultado_guardado != "") {
                                $resultado2 = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"), 1700, $original_imagen, $nombre_imagen, "./adjuntos/libros/subtitulos/");
                                if ($resultado2 != "") {
                                    $SubTitulo->setPdf($resultado_guardado);
                                    $SubTitulo->setImagen($resultado2);
                                    $stmt = subcapituloModelo::actualizar_subcapitulo_modelo($this->conexion_db, $SubTitulo);
                                    if ($stmt->execute()) {
                                        if ($consulta5["list"][0]['imagen'] != "" || $consulta5["list"][0]['imagen'] != null) {
                                            unlink('./adjuntos/libros/subtitulos/' . $consulta5["list"][0]['imagen']);
                                        }
                                        $this->conexion_db->commit();
                                        $insBeanCrud->setMessageServer("ok");
                                        $insBeanCrud->setBeanPagination(self::paginador_subcapitulo_controlador($this->conexion_db, 0, 20, $SubTitulo->getTitulo()));
                                    } else {
                                        $insBeanCrud->setMessageServer("error en el servidor, No hemos podido actualizar el subtitulo");
                                    }
                                } else {
                                    $insBeanCrud->setMessageServer("error en el servidor, Hubo un error al guardar la Imagen para la leccion");
                                }
                            } else {
                                $insBeanCrud->setMessageServer("error en el servidor, Hubo un error al guardar el PDF");
                            }

                        }

                    } else {
                        $SubTitulo->setPdf($consulta5["list"][0]['pdf']);
                        $SubTitulo->setImagen($consulta5["list"][0]['imagen']);
                        $stmt = subcapituloModelo::actualizar_subcapitulo_modelo($this->conexion_db, $SubTitulo);
                        // print_r($guardarsubcapitulo);
                        if ($stmt->execute()) {
                            $this->conexion_db->commit();
                            $insBeanCrud->setMessageServer("ok");
                            $insBeanCrud->setBeanPagination(self::paginador_subcapitulo_controlador($this->conexion_db, 0, 20, $SubTitulo->getTitulo()));

                        } else {
                            $insBeanCrud->setMessageServer("error en el servidor, No hemos podido actualizar el subtitulo");
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
    protected function archivo($permitidos, $limite_KB, $original, $nombre, $destino)
    {
        //file Size Bytes
        if (in_array($original['type'], $permitidos) && ($original['size'] <= $limite_KB * 1024)) {
            $array_nombre = explode('.', $nombre);
            $extension = array_pop($array_nombre);
            $name = $array_nombre[0] . "." . $array_nombre[1] . "." . $array_nombre[2] . "." . $array_nombre[3];
            if (!file_exists('./adjuntos/archivos/' . $array_nombre[0])) {
                mkdir('./adjuntos/archivos/' . $array_nombre[0], 0777, true);
                mkdir('./adjuntos/archivos/' . $array_nombre[0] . '/' . $array_nombre[0] . '.' . $array_nombre[1] . '.' . $array_nombre[2], 0777, true);
                mkdir('./adjuntos/archivos/' . $array_nombre[0] . '/' . $array_nombre[0] . '.' . $array_nombre[1] . '.' . $array_nombre[2] . '/PDF', 0777, true);
            } elseif (!file_exists('./adjuntos/archivos/' . $array_nombre[0] . '/' . $array_nombre[0] . '.' . $array_nombre[1] . '.' . $array_nombre[2])) {
                mkdir('./adjuntos/archivos/' . $array_nombre[0] . '/' . $array_nombre[0] . '.' . $array_nombre[1] . '.' . $array_nombre[2], 0777, true);
                mkdir('./adjuntos/archivos/' . $array_nombre[0] . '/' . $array_nombre[0] . '.' . $array_nombre[1] . '.' . $array_nombre[2] . '/PDF', 0777, true);
            } else if (!file_exists('./adjuntos/archivos/' . $array_nombre[0] . '/' . $array_nombre[0] . '.' . $array_nombre[1] . '.' . $array_nombre[2] . '/PDF')) {
                mkdir('./adjuntos/archivos/' . $array_nombre[0] . '/' . $array_nombre[0] . '.' . $array_nombre[1] . '.' . $array_nombre[2] . '/PDF', 0777, true);
            }
            $array = glob($destino . $name . "*." . $extension);
            $cantidad = count($array);
            if ($cantidad > 0) {
                unlink($destino . $name . "." . $extension);
            }
            $nombreImagen = $name . "." . $extension;
            $resultado_guardado = move_uploaded_file($original['tmp_name'], $destino . $nombreImagen);

            if ($resultado_guardado) {
                return $nombreImagen;
            } else {
                return "";
            }

        } else {
            return "";
        }

    }
}
