<?php

require_once './modelos/capituloModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';
class capituloControlador extends capituloModelo
{
    public function agregar_capitulo_controlador($Capitulo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Capitulo->setNombre(mainModel::limpiar_cadena($Capitulo->getNombre()));
            $Capitulo->setCodigo(mainModel::limpiar_cadena($Capitulo->getCodigo()));
            $Capitulo->setLibro(mainModel::limpiar_cadena($Capitulo->getLibro()));

            if (isset($_FILES['txtImagenCapitulo'])) {
                $original = $_FILES['txtImagenCapitulo'];
                $nombre = $original['name'];
                if ($original['error'] > 0) {
                    $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                } else {

                    $resultado = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"), 7700, $original, $nombre, "./adjuntos/libros/capitulos/");
                    if ($resultado != "") {
                        $Capitulo->setImagen($resultado);
                        $stmt = capituloModelo::agregar_capitulo_modelo($this->conexion_db, $Capitulo);
                        if ($stmt->execute()) {
                            $this->conexion_db->commit();
                            $insBeanCrud->setMessageServer("ok");
                            $insBeanCrud->setBeanPagination(self::paginador_capitulo_controlador($this->conexion_db, 0, 20, $Capitulo->getLibro()));

                        } else {
                            $insBeanCrud->setMessageServer("error en el servidor, No hemos podido registrar el capítulo");
                        }
                    } else {
                        $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido");
                    }
                }
            } else {
                $insBeanCrud->setMessageServer("Por favor, ingrese Imagen");
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
    public function datos_capitulo_controlador($tipo, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);
            $insBeanCrud->setBeanPagination(capituloModelo::datos_capitulo_modelo($this->conexion_db, $tipo, $codigo));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function paginador_capitulo_controlador($conexion, $inicio, $registros, $codigo)
    {
        $insBeanPagination = new BeanPagination();
        try {

            $stmt = $conexion->prepare("SELECT COUNT(idtitulo) AS CONTADOR  FROM `titulo` WHERE libro_codigoLibro=?");
            $stmt->bindParam(1, $codigo, PDO::PARAM_STR, 12);
            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT * FROM `titulo` as c
                    inner join `libro` as b ON c.libro_codigoLibro=b.codigo
                    WHERE c.libro_codigoLibro=? ORDER BY c.codigoTitulo ASC LIMIT ?,?");
                    $stmt->bindParam(1, $codigo, PDO::PARAM_STR, 12);
                    $stmt->bindParam(2, $inicio, PDO::PARAM_INT);
                    $stmt->bindParam(3, $registros, PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();

                    foreach ($datos as $row) {
                        $insLibro = new Libro();
                        $insLibro->setIdLibro($row['idlibro']);
                        $insLibro->setCodigo($row['codigo']);
                        $insLibro->setVideo($row['libroVideo']);
                        $insLibro->setNombre($row['nombre']);
                        $insLibro->setImagenOtro($row['desImagen']);
                        $insLibro->setImagen($row['imagen']);
                        $insLibro->setEstado($row['estado']);
                        $insLibro->setDescripcion($row['descripcion']);

                        $insCapitulo = new Titulo();
                        $insCapitulo->setIdTitulo($row['idtitulo']);
                        $insCapitulo->setCodigo($row['codigoTitulo']);
                        $insCapitulo->setPdf($row['PDF']);
                        $insCapitulo->setDescripcion($row['tituloDescripcion']);
                        $insCapitulo->setEstado($row['TituloEstado']);
                        $insCapitulo->setNombre($row['tituloNombre']);
                        $insCapitulo->setImagen($row['titulo_imagen']);

                        $insCapitulo->setLibro($insLibro->__toString());
                        $insBeanPagination->setList($insCapitulo->__toString());
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
    public function bean_paginador_capitulo_controlador($pagina, $registros, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $codigo = mainModel::limpiar_cadena($codigo);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_capitulo_controlador($this->conexion_db, $inicio, $registros, $codigo));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_capitulo_controlador($Capitulo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Capitulo->setIdTitulo(mainModel::limpiar_cadena($Capitulo->getIdTitulo()));
            $capitulo = capituloModelo::datos_capitulo_modelo($this->conexion_db, "unico", $Capitulo);
            if ($capitulo["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra el capítulo");
            } else {
                $stmt = capituloModelo::eliminar_capitulo_modelo($this->conexion_db, $Capitulo->getIdTitulo());
                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    if ($capitulo["list"][0]['imagen'] != "" || $capitulo["list"][0]['imagen'] != null) {
                        unlink('./adjuntos/libros/capitulos/' . $capitulo["list"][0]['imagen']);
                    }
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_capitulo_controlador($this->conexion_db, 0, 20, $capitulo["list"][0]['libro']['codigo']));
                } else {
                    $insBeanCrud->setMessageServer("error en el servidor, No hemos podido eliminar el capítulo");
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
    public function actualizar_capitulo_controlador($Capitulo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Capitulo->setNombre(mainModel::limpiar_cadena($Capitulo->getNombre()));
            $Capitulo->setCodigo(mainModel::limpiar_cadena($Capitulo->getCodigo()));
            $Capitulo->setLibro(mainModel::limpiar_cadena($Capitulo->getLibro()));
            $Capitulo->setIdTitulo(mainModel::limpiar_cadena($Capitulo->getIdTitulo()));

            $capitulo = capituloModelo::datos_capitulo_modelo($this->conexion_db, "unico", $Capitulo);
            if ($capitulo["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra el Capítulo");
            } else {
                if (isset($_FILES['txtImagenCapitulo'])) {
                    $original = $_FILES['txtImagenCapitulo'];
                    $nombre = $original['name'];
                    if ($original['error'] > 0) {
                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                    } else {

                        $resultado = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"), 7700, $original, $nombre, "./adjuntos/libros/capitulos/");
                        if ($resultado != "") {
                            $Capitulo->setImagen($resultado);
                            $stmt = capituloModelo::actualizar_capitulo_modelo($this->conexion_db, $Capitulo);
                            if ($stmt->execute()) {
                                $this->conexion_db->commit();
                                if ($capitulo["list"][0]['imagen'] != "" || $capitulo["list"][0]['imagen'] != null) {
                                    unlink('./adjuntos/libros/capitulos/' . $capitulo["list"][0]['imagen']);
                                }

                                $insBeanCrud->setMessageServer("ok");
                                $insBeanCrud->setBeanPagination(self::paginador_capitulo_controlador($this->conexion_db, 0, 20, $Capitulo->getLibro()));

                            } else {
                                $insBeanCrud->setMessageServer("error en el servidor, No hemos podido actualizar el capítulo");
                            }
                        } else {
                            $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido");
                        }
                    }
                } else {
                    $Capitulo->setImagen($capitulo["list"][0]['imagen']);
                    $stmt = capituloModelo::actualizar_capitulo_modelo($this->conexion_db, $Capitulo);
                    if ($stmt->execute()) {
                        $this->conexion_db->commit();
                        $insBeanCrud->setMessageServer("ok");
                        $insBeanCrud->setBeanPagination(self::paginador_capitulo_controlador($this->conexion_db, 0, 20, $Capitulo->getLibro()));

                    } else {
                        $insBeanCrud->setMessageServer("error en el servidor, No hemos podido actualizar el capítulo");
                    }
                }
            }

            $stmt->closeCursor();
            $stmt = null;
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
