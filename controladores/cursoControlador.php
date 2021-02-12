<?php

require_once './modelos/cursoModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';

class cursoControlador extends cursoModelo
{

    public function agregar_curso_controlador($Curso)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Curso->setDescripcion(mainModel::limpiar_cadena($Curso->getDescripcion()));
            $Curso->setTitulo(mainModel::limpiar_cadena($Curso->getTitulo()));
            $Curso->setPrecio(mainModel::limpiar_cadena($Curso->getPrecio()));
            $Curso->setDescuento(mainModel::limpiar_cadena($Curso->getDescuento()));
            $Curso->setTipo(mainModel::limpiar_cadena($Curso->getTipo()));

            if (isset($_FILES['txtImagenCurso']) && isset($_FILES['txtImagenPortadaCurso']) && isset($_FILES['txtImagenPresentacionCurso'])) {
                $original = $_FILES['txtImagenCurso'];
                $nombre = $original['name'];
                $originalPortada = $_FILES['txtImagenPortadaCurso'];
                $nombrePortada = $originalPortada['name'];
                $originalPresentacion = $_FILES['txtImagenPresentacionCurso'];
                $nombrePresentacion = $originalPresentacion['name'];
                $permitido = array("image/png", "image/jpg", "image/jpeg");
                $limit_kb = 1700;
                if ($original['error'] > 0 && $originalPortada['error'] > 0 && $originalPresentacion['error'] > 0) {
                    $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                } else {
                    $resultado = mainModel::archivo($permitido, $limit_kb, $original, $nombre, "./adjuntos/libros/");
                    if ($resultado != "") {
                        $Curso->setImagen($resultado);
                        $resultadoPortada = mainModel::archivo($permitido, $limit_kb, $originalPortada, $nombrePortada, "./adjuntos/libros/");
                        if ($resultadoPortada != "") {
                            $Curso->setPortada($resultadoPortada);

                            $resultadoPresentacion = mainModel::archivo($permitido, $limit_kb, $originalPresentacion, $nombrePresentacion, "./adjuntos/libros/");
                            if ($resultadoPresentacion != "") {
                                $Curso->setPresentacion($resultadoPresentacion);

                                $stmt = cursoModelo::agregar_curso_modelo($this->conexion_db, $Curso);
                                if ($stmt->execute()) {
                                    $this->conexion_db->commit();
                                    $insBeanCrud->setMessageServer("ok");
                                    $insBeanCrud->setBeanPagination(self::paginador_curso_controlador($this->conexion_db, 0, 20));
                                } else {
                                    $insBeanCrud->setMessageServer("No hemos podido registrar el curso");
                                }
                                $stmt->closeCursor();
                                $stmt = null;
                            } else {
                                $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido, cambie el nombre de la presentacion o seleccione otra imagen");
                            }

                        } else {
                            $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido, cambie el nombre de la portada o seleccione otra imagen");
                        }

                    } else {
                        $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido, cambie el nombre de la imagen o seleccione otra imagen");
                    }

                }

            } else {
                # code...
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
    public function datos_curso_controlador($tipo, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);
            $insBeanCrud->setBeanPagination(cursoModelo::datos_curso_modelo($this->conexion_db, $tipo, $codigo));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();

    }
    public function paginador_curso_controlador($conexion, $inicio, $registros)
    {
        $insBeanPagination = new BeanPagination();
        try {

            $stmt = $conexion->query("SELECT COUNT(idcurso) AS CONTADOR FROM `curso`");
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT * FROM `curso` ORDER BY idcurso ASC LIMIT ?,?");
                    $stmt->bindValue(1, $inicio, PDO::PARAM_INT);
                    $stmt->bindValue(2, $registros, PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insCurso = new Curso();
                        $insCurso->setIdCurso($row['idcurso']);
                        $insCurso->setTitulo($row['titulo']);
                        $insCurso->setPrecio($row['precio']);
                        $insCurso->setPresentacion($row['presentacion']);
                        $insCurso->setDescripcion($row['descripcion']);
                        $insCurso->setDescuento($row['precio_descuento']);
                        //TIPO=1 PAGADO ; TIPO=2 MEDIANTE ZOOM;
                        $insCurso->setTipo($row['tipo']);
                        $insCurso->setImagen($row['imagen']);
                        $insCurso->setPortada($row['portada']);
                        $insBeanPagination->setList($insCurso->__toString());
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
    public function bean_paginador_curso_controlador($pagina, $registros)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_curso_controlador($this->conexion_db, $inicio, $registros));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_curso_controlador($Curso)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $curso = cursoModelo::datos_curso_modelo($this->conexion_db, "unico", $Curso);
            if ($curso["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra el curso");
            } else {
                $stmt = cursoModelo::eliminar_curso_modelo($this->conexion_db, mainModel::limpiar_cadena($Curso->getIdcurso()));

                if ($stmt->execute()) {
                    if ($curso["list"][0]['imagen'] != "") {
                        unlink('./adjuntos/libros/' . $curso["list"][0]['imagen']);
                    }
                    if ($curso["list"][0]['portada'] != "") {
                        unlink('./adjuntos/libros/' . $curso["list"][0]['portada']);
                    }
                    if ($curso["list"][0]['presentacion'] != "") {
                        unlink('./adjuntos/libros/' . $curso["list"][0]['presentacion']);
                    }

                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_curso_controlador($this->conexion_db, 0, 20));

                } else {
                    $insBeanCrud->setMessageServer("No se eliminó el curso");
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
    public function actualizar_curso_controlador($Curso)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Curso->setDescripcion(mainModel::limpiar_cadena($Curso->getDescripcion()));
            $Curso->setTitulo(mainModel::limpiar_cadena($Curso->getTitulo()));
            $Curso->setPrecio(mainModel::limpiar_cadena($Curso->getPrecio()));
            $Curso->setDescuento(mainModel::limpiar_cadena($Curso->getDescuento()));
            $Curso->setTipo(mainModel::limpiar_cadena($Curso->getTipo()));
            $Curso->setIdcurso(mainModel::limpiar_cadena($Curso->getIdcurso()));
            $lcurso = cursoModelo::datos_curso_modelo($this->conexion_db, "unico", $Curso);
            if ($lcurso["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra el curso");
            } else {
                if (isset($_FILES['txtImagenCurso']) && isset($_FILES['txtImagenPortadaCurso']) && isset($_FILES['txtImagenPresentacionCurso'])) {
                    $original = $_FILES['txtImagenCurso'];
                    $nombre = $original['name'];
                    $originalPortada = $_FILES['txtImagenPortadaCurso'];
                    $nombrePortada = $originalPortada['name'];
                    $originalPresentacion = $_FILES['txtImagenPresentacionCurso'];
                    $nombrePresentacion = $originalPresentacion['name'];
                    $permitido = array("image/png", "image/jpg", "image/jpeg");
                    $limit_kb = 1700;
                    if ($original['error'] > 0 && $originalPortada['error'] > 0 && $originalPresentacion['error'] > 0) {
                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                    } else {
                        $resultado = mainModel::archivo($permitido, $limit_kb, $original, $nombre, "./adjuntos/libros/");
                        if ($resultado != "") {
                            $Curso->setImagen($resultado);
                            $resultadoPortada = mainModel::archivo($permitido, $limit_kb, $originalPortada, $nombrePortada, "./adjuntos/libros/");
                            if ($resultadoPortada != "") {
                                $Curso->setPortada($resultadoPortada);

                                $resultadoPresentacion = mainModel::archivo($permitido, $limit_kb, $originalPresentacion, $nombrePresentacion, "./adjuntos/libros/");
                                if ($resultadoPresentacion != "") {
                                    $Curso->setPresentacion($resultadoPresentacion);
                                    $stmt = cursoModelo::actualizar_curso_modelo($this->conexion_db, $Curso);
                                    if ($stmt->execute()) {

                                        $this->conexion_db->commit();
                                        if ($lcurso["list"][0]['imagen'] != "") {
                                            unlink('./adjuntos/libros/' . $lcurso["list"][0]['imagen']);
                                        }
                                        if ($lcurso["list"][0]['portada'] != "") {
                                            unlink('./adjuntos/libros/' . $lcurso["list"][0]['portada']);
                                        }
                                        if ($lcurso["list"][0]['presentacion'] != "") {
                                            unlink('./adjuntos/libros/' . $lcurso["list"][0]['presentacion']);
                                        }
                                        $insBeanCrud->setMessageServer("ok");
                                        $insBeanCrud->setBeanPagination(self::paginador_curso_controlador($this->conexion_db, 0, 20));
                                    } else {
                                        $insBeanCrud->setMessageServer("No hemos podido actualizar el curso");
                                    }
                                    $stmt->closeCursor();
                                    $stmt = null;
                                } else {
                                    $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido, cambie el nombre de la presentacion o seleccione otra imagen");
                                }

                            } else {
                                $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido, cambie el nombre de la portada o seleccione otra imagen");
                            }

                        } else {
                            $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido, cambie el nombre de la imagen o seleccione otra imagen");
                        }

                    }
                } else if (isset($_FILES['txtImagenCurso']) && !isset($_FILES['txtImagenPortadaCurso']) && !isset($_FILES['txtImagenPresentacionCurso'])) {
                    $original = $_FILES['txtImagenCurso'];
                    $nombre = $original['name'];
                    $permitido = array("image/png", "image/jpg", "image/jpeg");
                    $limit_kb = 1700;
                    if ($original['error'] > 0) {
                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                    } else {
                        $resultado = mainModel::archivo($permitido, $limit_kb, $original, $nombre, "./adjuntos/libros/");
                        if ($resultado != "") {
                            $Curso->setImagen($resultado);
                            $Curso->setPresentacion($lcurso["list"][0]['presentacion']);
                            $Curso->setPortada($lcurso["list"][0]['portada']);
                            $stmt = cursoModelo::actualizar_curso_modelo($this->conexion_db, $Curso);

                            if ($stmt->execute()) {

                                if ($lcurso["list"][0]['imagen'] != "") {
                                    unlink('./adjuntos/libros/' . $lcurso["list"][0]['imagen']);
                                }
                                $this->conexion_db->commit();
                                $insBeanCrud->setMessageServer("ok");
                                $insBeanCrud->setBeanPagination(self::paginador_curso_controlador($this->conexion_db, 0, 20));

                            } else {
                                $insBeanCrud->setMessageServer("No hemos podido actualizar el curso");
                            }
                            $stmt->closeCursor();
                            $stmt = null;
                        } else {
                            $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido");

                        }
                    }
                } else if (!isset($_FILES['txtImagenCurso']) && isset($_FILES['txtImagenPortadaCurso']) && !isset($_FILES['txtImagenPresentacionCurso'])) {
                    $originalPortada = $_FILES['txtImagenPortadaCurso'];
                    $nombrePortada = $originalPortada['name'];
                    $permitido = array("image/png", "image/jpg", "image/jpeg");
                    $limit_kb = 1700;
                    if ($originalPortada['error'] > 0) {
                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                    } else {
                        $resultado = mainModel::archivo($permitido, $limit_kb, $originalPortada, $nombrePortada, "./adjuntos/libros/");
                        if ($resultado != "") {
                            $Curso->setPortada($resultado);
                            $Curso->setPresentacion($lcurso["list"][0]['presentacion']);
                            $Curso->setImagen($lcurso["list"][0]['imagen']);
                            $stmt = cursoModelo::actualizar_curso_modelo($this->conexion_db, $Curso);

                            if ($stmt->execute()) {

                                if ($lcurso["list"][0]['portada'] != "") {
                                    unlink('./adjuntos/libros/' . $lcurso["list"][0]['portada']);
                                }
                                $this->conexion_db->commit();
                                $insBeanCrud->setMessageServer("ok");
                                $insBeanCrud->setBeanPagination(self::paginador_curso_controlador($this->conexion_db, 0, 20));

                            } else {
                                $insBeanCrud->setMessageServer("No hemos podido actualizar el curso");
                            }
                            $stmt->closeCursor();
                            $stmt = null;
                        } else {
                            $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido");

                        }
                    }

                } else if (!isset($_FILES['txtImagenCurso']) && !isset($_FILES['txtImagenPortadaCurso']) && isset($_FILES['txtImagenPresentacionCurso'])) {

                    $originalPresentacion = $_FILES['txtImagenPresentacionCurso'];
                    $nombrePresentacion = $originalPresentacion['name'];
                    $permitido = array("image/png", "image/jpg", "image/jpeg");
                    $limit_kb = 1700;
                    if ($originalPresentacion['error'] > 0) {
                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                    } else {
                        $resultadoPresentacion = mainModel::archivo($permitido, $limit_kb, $originalPresentacion, $nombrePresentacion, "./adjuntos/libros/");
                        if ($resultadoPresentacion != "") {
                            $Curso->setPresentacion($resultadoPresentacion);
                            $Curso->setPortada($lcurso["list"][0]['portada']);
                            $Curso->setImagen($lcurso["list"][0]['imagen']);
                            $stmt = cursoModelo::actualizar_curso_modelo($this->conexion_db, $Curso);
                            if ($stmt->execute()) {

                                $this->conexion_db->commit();
                                if ($lcurso["list"][0]['presentacion'] != "") {
                                    unlink('./adjuntos/libros/' . $lcurso["list"][0]['presentacion']);
                                }
                                $insBeanCrud->setMessageServer("ok");
                                $insBeanCrud->setBeanPagination(self::paginador_curso_controlador($this->conexion_db, 0, 20));
                            } else {
                                $insBeanCrud->setMessageServer("No hemos podido actualizar el curso");
                            }
                            $stmt->closeCursor();
                            $stmt = null;
                        } else {
                            $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido, cambie el nombre de la presentacion o seleccione otra imagen");
                        }
                    }
                } elseif (isset($_FILES['txtImagenCurso']) && isset($_FILES['txtImagenPortadaCurso']) && !isset($_FILES['txtImagenPresentacionCurso'])) {
                    $original = $_FILES['txtImagenCurso'];
                    $nombre = $original['name'];
                    $originalPortada = $_FILES['txtImagenPortadaCurso'];
                    $nombrePortada = $originalPortada['name'];

                    $permitido = array("image/png", "image/jpg", "image/jpeg");
                    $limit_kb = 1700;
                    if ($original['error'] > 0 && $originalPortada['error'] > 0) {
                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                    } else {
                        $resultado = mainModel::archivo($permitido, $limit_kb, $original, $nombre, "./adjuntos/libros/");
                        if ($resultado != "") {
                            $Curso->setImagen($resultado);
                            $Curso->setPresentacion($lcurso["list"][0]['presentacion']);
                            $resultadoPortada = mainModel::archivo($permitido, $limit_kb, $originalPortada, $nombrePortada, "./adjuntos/libros/");
                            if ($resultadoPortada != "") {
                                $Curso->setPortada($resultadoPortada);
                                $this->conexion_db->commit();
                                if ($lcurso["list"][0]['imagen'] != "") {
                                    unlink('./adjuntos/libros/' . $lcurso["list"][0]['imagen']);
                                }
                                if ($lcurso["list"][0]['portada'] != "") {
                                    unlink('./adjuntos/libros/' . $lcurso["list"][0]['portada']);
                                }
                                $insBeanCrud->setMessageServer("ok");
                                $insBeanCrud->setBeanPagination(self::paginador_curso_controlador($this->conexion_db, 0, 20));
                                $stmt->closeCursor();
                                $stmt = null;

                            } else {
                                $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido, cambie el nombre de la portada o seleccione otra imagen");
                            }

                        } else {
                            $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido, cambie el nombre de la imagen o seleccione otra imagen");
                        }

                    }
                } elseif (isset($_FILES['txtImagenCurso']) && !isset($_FILES['txtImagenPortadaCurso']) && isset($_FILES['txtImagenPresentacionCurso'])) {
                    $original = $_FILES['txtImagenCurso'];
                    $nombre = $original['name'];
                    $originalPresentacion = $_FILES['txtImagenPresentacionCurso'];
                    $nombrePresentacion = $originalPresentacion['name'];

                    $permitido = array("image/png", "image/jpg", "image/jpeg");
                    $limit_kb = 1700;
                    if ($original['error'] > 0 && $originalPresentacion['error'] > 0) {
                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                    } else {
                        $resultado = mainModel::archivo($permitido, $limit_kb, $original, $nombre, "./adjuntos/libros/");
                        if ($resultado != "") {
                            $Curso->setImagen($resultado);
                            $Curso->setPortada($lcurso["list"][0]['portada']);
                            $resultadoPresentacion = mainModel::archivo($permitido, $limit_kb, $originalPresentacion, $nombrePresentacion, "./adjuntos/libros/");
                            if ($resultadoPresentacion != "") {
                                $Curso->setPresentacion($resultadoPresentacion);
                                $this->conexion_db->commit();
                                if ($lcurso["list"][0]['imagen'] != "") {
                                    unlink('./adjuntos/libros/' . $lcurso["list"][0]['imagen']);
                                }
                                if ($lcurso["list"][0]['presentacion'] != "") {
                                    unlink('./adjuntos/libros/' . $lcurso["list"][0]['presentacion']);
                                }
                                $insBeanCrud->setMessageServer("ok");
                                $insBeanCrud->setBeanPagination(self::paginador_curso_controlador($this->conexion_db, 0, 20));
                                $stmt->closeCursor();
                                $stmt = null;

                            } else {
                                $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido, cambie el nombre de la portada o seleccione otra imagen");
                            }

                        } else {
                            $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido, cambie el nombre de la imagen o seleccione otra imagen");
                        }

                    }
                } elseif (!isset($_FILES['txtImagenCurso']) && isset($_FILES['txtImagenPortadaCurso']) && isset($_FILES['txtImagenPresentacionCurso'])) {
                    $originalPortada = $_FILES['txtImagenCurso'];
                    $nombrePortada = $originalPortada['name'];
                    $originalPresentacion = $_FILES['txtImagenPresentacionCurso'];
                    $nombrePresentacion = $originalPresentacion['name'];

                    $permitido = array("image/png", "image/jpg", "image/jpeg");
                    $limit_kb = 1700;
                    if ($originalPortada['error'] > 0 && $originalPresentacion['error'] > 0) {
                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                    } else {
                        $resultadoPortada = mainModel::archivo($permitido, $limit_kb, $originalPortada, $nombrePortada, "./adjuntos/libros/");
                        if ($resultadoPortada != "") {
                            $Curso->setPortada($resultadoPortada);
                            $Curso->setImagen($lcurso["list"][0]['imagen']);
                            $resultadoPresentacion = mainModel::archivo($permitido, $limit_kb, $originalPresentacion, $nombrePresentacion, "./adjuntos/libros/");
                            if ($resultadoPresentacion != "") {
                                $Curso->setPresentacion($resultadoPresentacion);
                                $this->conexion_db->commit();
                                if ($lcurso["list"][0]['portada'] != "") {
                                    unlink('./adjuntos/libros/' . $lcurso["list"][0]['portada']);
                                }
                                if ($lcurso["list"][0]['presentacion'] != "") {
                                    unlink('./adjuntos/libros/' . $lcurso["list"][0]['presentacion']);
                                }
                                $insBeanCrud->setMessageServer("ok");
                                $insBeanCrud->setBeanPagination(self::paginador_curso_controlador($this->conexion_db, 0, 20));
                                $stmt->closeCursor();
                                $stmt = null;

                            } else {
                                $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido, cambie el nombre de la portada o seleccione otra imagen");
                            }

                        } else {
                            $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido, cambie el nombre de la imagen o seleccione otra imagen");
                        }

                    }
                } else {
                    $Curso->setPortada($lcurso["list"][0]['portada']);
                    $Curso->setImagen($lcurso["list"][0]['imagen']);
                    $Curso->setPresentacion($lcurso["list"][0]['presentacion']);
                    $stmt = cursoModelo::actualizar_curso_modelo($this->conexion_db, $Curso);
                    if ($stmt->execute()) {

                        $this->conexion_db->commit();
                        $insBeanCrud->setMessageServer("ok");
                        $insBeanCrud->setBeanPagination(self::paginador_curso_controlador($this->conexion_db, 0, 20));
                    } else {
                        $insBeanCrud->setMessageServer("No hemos podido actualizar el curso");
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
