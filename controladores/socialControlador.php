<?php

require_once './modelos/socialModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';

class socialControlador extends socialModelo
{

    public function agregar_social_controlador($Social)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Social->setDescripcion(mainModel::limpiar_cadena($Social->getDescripcion()));
            $Social->setTitulo(mainModel::limpiar_cadena($Social->getTitulo()));
            $Social->setParametroCurso(mainModel::limpiar_cadena($Social->getParametroCurso()));
            $Social->setFraseCurso(mainModel::limpiar_cadena($Social->getFraseCurso()));
            $Social->setFraseTestimonio(mainModel::limpiar_cadena($Social->getFraseTestimonio()));
            $Social->setTipoArchivo(mainModel::limpiar_cadena($Social->getTipoArchivo()));

            switch ((int) $Social->getTipoArchivo()) {
                case 1:
                    $original = $_FILES['txtImagenSocial'];
                    $nombre = $original['name'];
                    $permitido = array("image/png", "image/jpg", "image/jpeg");
                    $destino = "img";
                    $limit_kb = 4 * 1024;
                    break;
                case 2:
                    $original = $_FILES['txtVideoSocial'];
                    $nombre = $original['name'];
                    $permitido = array("video/mp4");
                    $destino = "video";
                    $limit_kb = (17 * 1024);
                    break;
            }
            if (!isset($_FILES['txtImagenFondoSocial'])) {
                $Social->setImagenFondo(null);
                if ($original['error'] > 0) {
                    $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");

                } else {
                    $resultado = mainModel::archivo($permitido, $limit_kb, $original, $nombre, "./adjuntos/social/" . $destino . "/");
                    if ($resultado != "") {
                        $Social->setArchivo($resultado);
                        $stmt = socialModelo::agregar_social_modelo($this->conexion_db, $Social);
                        if ($stmt->execute()) {
                            $this->conexion_db->commit();
                            $insBeanCrud->setMessageServer("ok");
                            $insBeanCrud->setBeanPagination(self::paginador_social_controlador($this->conexion_db, 0, 20));
                        } else {
                            $insBeanCrud->setMessageServer("No hemos podido registrar los datos de la publicidad");
                        }
                        $stmt->closeCursor();
                        $stmt = null;

                    } else {
                        $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido, cambie el nombre o seleccione otra imagen");
                    }

                }
            } else {
                $originalFondo = $_FILES['txtImagenFondoSocial'];
                $nombreFondo = $originalFondo['name'];
                if ($original['error'] > 0 || $originalFondo['error'] > 0) {
                    if ($originalFondo['error'] > 0) {

                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen de Fondo");
                    } else {

                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otro video");
                    }

                } else {
                    $resultado = mainModel::archivo($permitido, $limit_kb, $original, $nombre, "./adjuntos/social/" . $destino . "/");
                    if ($resultado != "") {
                        $Social->setArchivo($resultado);
                        $resultadoFondo = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"), 4 * 1024, $originalFondo, $nombreFondo, "./adjuntos/social/img/");
                        if ($resultadoFondo != "") {
                            $Social->setImagenFondo($resultadoFondo);
                            $stmt = socialModelo::agregar_social_modelo($this->conexion_db, $Social);
                            if ($stmt->execute()) {
                                $this->conexion_db->commit();
                                $insBeanCrud->setMessageServer("ok");
                                $insBeanCrud->setBeanPagination(self::paginador_social_controlador($this->conexion_db, 0, 20));
                            } else {
                                $insBeanCrud->setMessageServer("No hemos podido registrar los datos de la publicidad");
                            }
                            $stmt->closeCursor();
                            $stmt = null;
                        } else {
                            $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido, cambie el nombre de la imagen o seleccione otra imagen de fondo");
                        }

                    } else {
                        $insBeanCrud->setMessageServer("Hubo un error al guardar el video,formato no permitido o tamaño excedido, cambie el nombre o seleccione otro video");
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
    public function datos_social_controlador($tipo, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);
            $insBeanCrud->setBeanPagination(socialModelo::datos_social_modelo($this->conexion_db, $tipo, $codigo));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();

    }
    public function paginador_social_controlador($conexion, $inicio, $registros)
    {
        $insBeanPagination = new BeanPagination();
        try {

            $stmt = $conexion->query("SELECT COUNT(idsocial) AS CONTADOR FROM `social`");
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT * FROM `social` ORDER BY idsocial DESC LIMIT ?,?");
                    $stmt->bindValue(1, $inicio, PDO::PARAM_INT);
                    $stmt->bindValue(2, $registros, PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insSocial = new Social();
                        $insSocial->setIdSocial($row['idsocial']);
                        $insSocial->setTitulo($row['titulo']);
                        $insSocial->setDescripcion($row['descripcion']);
                        $insSocial->setArchivo($row['archivo']);
                        $insSocial->setTipoArchivo($row['tipo_archivo']);
                        $insSocial->setImagenFondo($row['imagen_fondo']);
                        $insSocial->setFraseCurso($row['frase_curso']);
                        $insSocial->setFraseTestimonio($row['frase_testimonio']);
                        $insSocial->setParametroCurso($row['parametro_curso']);
                        $insBeanPagination->setList($insSocial->__toString());
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
    public function bean_paginador_social_controlador($pagina, $registros)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_social_controlador($this->conexion_db, $inicio, $registros));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_social_controlador($Social)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $social = socialModelo::datos_social_modelo($this->conexion_db, "unico", $Social);
            if ($social["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra la publicidad seleccionada");
            } else {
                $stmt = socialModelo::eliminar_social_modelo($this->conexion_db, mainModel::limpiar_cadena($Social->getIdsocial()));

                if ($stmt->execute()) {
                    if ($social["list"][0]['imagenFondo'] != "" || $social["list"][0]['imagenFondo'] != null) {
                        unlink('./adjuntos/social/img/' . $social["list"][0]['imagenFondo']);
                    }
                    switch ($social["list"][0]['tipoArchivo']) {
                        case '1':
                            if ($social["list"][0]['archivo'] != "") {
                                unlink('./adjuntos/social/img/' . $social["list"][0]['archivo']);
                            }

                            break;
                        case '2':
                            if ($social["list"][0]['archivo'] != "") {
                                unlink('./adjuntos/social/video/' . $social["list"][0]['archivo']);
                            }

                            break;
                    }
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_social_controlador($this->conexion_db, 0, 20));

                } else {
                    $insBeanCrud->setMessageServer("No se eliminó el social");
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
    public function actualizar_social_controlador($Social)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Social->setDescripcion(mainModel::limpiar_cadena($Social->getDescripcion()));
            $Social->setTitulo(mainModel::limpiar_cadena($Social->getTitulo()));
            $Social->setParametroCurso(mainModel::limpiar_cadena($Social->getParametroCurso()));
            $Social->setFraseCurso(mainModel::limpiar_cadena($Social->getFraseCurso()));
            $Social->setFraseTestimonio(mainModel::limpiar_cadena($Social->getFraseTestimonio()));
            $Social->setTipoArchivo(mainModel::limpiar_cadena($Social->getTipoArchivo()));
            $nombre = false;
            switch ((int) $Social->getTipoArchivo()) {
                case 1:
                    if (isset($_FILES['txtImagenSocial'])) {
                        $original = $_FILES['txtImagenSocial'];
                        $nombre = $original['name'];
                        $permitido = array("image/png", "image/jpg", "image/jpeg");
                        $destino = "img";
                        $limit_kb = 4 * 1024;
                        $nombre = true;
                    }

                    break;
                case 2:
                    if (isset($_FILES['txtVideoSocial'])) {
                        $original = $_FILES['txtVideoSocial'];
                        $nombre = $original['name'];
                        $permitido = array("video/mp4");
                        $destino = "video";
                        $limit_kb = (17 * 1024);
                        $nombre = true;
                    }

                    break;
            }

            $lsocial = socialModelo::datos_social_modelo($this->conexion_db, "unico", $Social);
            if ($lsocial["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra el social");
            } else {
                $valueFondo = false;
                $valueFondoFile = false;
                if (isset($_FILES['txtImagenFondoSocial'])) {
                    $original_fondo = $_FILES['txtImagenFondoSocial'];
                    $nombre_fondo = $original_fondo['name'];
                    if ($original_fondo['error'] > 0) {
                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                    } else {
                        $resultado_fondo = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"), (4 * 1024), $original_fondo, $nombre_fondo, "./adjuntos/social/img/");
                        if ($resultado_fondo != "") {
                            $Social->setImagenFondo($resultado_fondo);
                            $valueFondo = true;
                            $valueFondoFile = true;
                        } else {
                            $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido");

                        }
                    }
                } else {
                    $Social->setImagenFondo($lsocial["list"][0]["imagenFondo"]);
                    $valueFondo = true;
                }

                if ($valueFondo) {
                    if ($nombre) {
                        if ($original['error'] > 0) {
                            $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                        } else {
                            $resultado = mainModel::archivo($permitido, $limit_kb, $original, $nombre, "./adjuntos/social/" . $destino . "/");
                            if ($resultado != "") {
                                $Social->setArchivo($resultado);
                                $stmt = socialModelo::actualizar_social_modelo($this->conexion_db, $Social);

                                if ($stmt->execute()) {
                                    switch ($Social->getTipoArchivo()) {
                                        case '1':
                                            unlink('./adjuntos/social/img/' . $lsocial["list"][0]['archivo']);
                                            break;
                                        case '2':
                                            unlink('./adjuntos/social/video/' . $lsocial["list"][0]['archivo']);
                                            if ($valueFondoFile) {
                                                unlink('./adjuntos/social/img/' . $lsocial["list"][0]['imagenFondo']);
                                            }
                                            break;
                                    }

                                    $this->conexion_db->commit();
                                    $insBeanCrud->setMessageServer("ok");
                                    $insBeanCrud->setBeanPagination(self::paginador_social_controlador($this->conexion_db, 0, 20));

                                } else {
                                    $insBeanCrud->setMessageServer("No hemos podido actualizar la publicidad");
                                }
                                $stmt->closeCursor();
                                $stmt = null;
                            } else {
                                $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido");

                            }
                        }
                    } else {
                        $Social->setArchivo($lsocial["list"][0]['archivo']);
                        $stmt = socialModelo::actualizar_social_modelo($this->conexion_db, $Social);
                        if ($stmt->execute()) {
                            if ($valueFondoFile) {
                                unlink('./adjuntos/social/img/' . $lsocial["list"][0]['imagenFondo']);
                            }
                            $this->conexion_db->commit();
                            $insBeanCrud->setMessageServer("ok");
                            $insBeanCrud->setBeanPagination(self::paginador_social_controlador($this->conexion_db, 0, 20));
                        } else {
                            $insBeanCrud->setMessageServer("No hemos podido actualizar el social");
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
