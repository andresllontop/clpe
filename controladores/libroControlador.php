<?php

require_once './modelos/libroModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';
class libroControlador extends libroModelo
{

    public function agregar_libro_controlador($Libro)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Libro->setNombre(mainModel::limpiar_cadena($Libro->getNombre()));
            $Libro->setCodigo(mainModel::limpiar_cadena($Libro->getCodigo()));

            if (isset($_FILES['txtImagenLibro'])) {
                $original = $_FILES['txtImagenLibro'];
                $nombre = $original['name'];

                if ($original['error'] > 0) {
                    $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                } else {
                    $resultado = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"), 3700, $original, $nombre, "./adjuntos/libros/");
                    if ($resultado != "") {
                        $Libro->setImagen($resultado);
                        $Libro->setImagenOtro("");
                        $stmt = libroModelo::agregar_libro_modelo($this->conexion_db, $Libro);
                        if ($stmt->execute()) {
                            $this->conexion_db->commit();
                            $insBeanCrud->setMessageServer("ok");
                            $insBeanCrud->setBeanPagination(self::paginador_libro_controlador($this->conexion_db, 0, 5));

                        } else {

                            $insBeanCrud->setMessageServer("error en el servidor, No hemos podido registrar el libro");
                        }

                    } else {
                        $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido");

                    }
                }

            } else {
                $insBeanCrud->setMessageServer("Ocurrio un error inesperado, selecciona la imagen");
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
    public function datos_libro_controlador($tipo, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);

            $insBeanCrud->setBeanPagination(libroModelo::datos_libro_modelo($this->conexion_db, $tipo, $codigo));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();

    }
    public function paginador_libro_controlador($conexion, $inicio, $registros)
    {
        $insBeanPagination = new BeanPagination();
        try {

            $stmt = $conexion->query("SELECT COUNT(idlibro) AS CONTADOR FROM `libro` as b  inner join `categoria` as cat WHERE b.idcategoria=cat.idcategoria");
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT * FROM `libro` as b
                    inner join categoria as cat WHERE b.idcategoria=cat.idcategoria ORDER BY codigo  ASC LIMIT ?,? ");
                    $stmt->bindValue(1, $inicio, PDO::PARAM_INT);
                    $stmt->bindValue(2, $registros, PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insCategoria = new Categoria();
                        $insCategoria->setIdcategoria($row['idcategoria']);
                        $insCategoria->setNombre($row['nombreCategoria']);
                        $insLibro = new Libro();
                        $insLibro->setIdLibro($row['idlibro']);
                        $insLibro->setCodigo($row['codigo']);
                        $insLibro->setNombre($row['nombre']);
                        $insLibro->setImagenOtro($row['desImagen']);
                        $insLibro->setImagen($row['imagen']);
                        $insLibro->setEstado($row['estado']);
                        $insLibro->setDescripcion($row['descripcion']);

                        $insLibro->setCategoria($insCategoria->__toString());
                        $insBeanPagination->setList($insLibro->__toString());
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
    public function bean_paginador_libro_controlador($pagina, $registros)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_libro_controlador($this->conexion_db, $inicio, $registros));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_libro_controlador($Libro)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $libro = libroModelo::datos_libro_modelo($this->conexion_db, "unico", $Libro);
            if ($libro["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No se encuentra el libro");
            } else {
                $stmt = libroModelo::eliminar_libro_modelo($this->conexion_db, mainModel::limpiar_cadena($Libro->getIdlibro()));

                if ($stmt->execute()) {

                    if ($libro["list"][0]['imagen'] != "") {
                        unlink('./adjuntos/libros/' . $libro["list"][0]['imagen']);
                    }
                    if ($libro["list"][0]['imagenOtro'] != "") {
                        unlink('./adjuntos/libros/' . $libro["list"][0]['imagenOtro']);
                    }
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_libro_controlador($this->conexion_db, 1, 5));

                } else {
                    $insBeanCrud->setMessageServer("No se eliminó el libro");
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
    public function actualizar_libro_controlador($Libro)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Libro->setNombre(mainModel::limpiar_cadena($Libro->getNombre()));
            $Libro->setCodigo(mainModel::limpiar_cadena($Libro->getCodigo()));
            $Libro->setIdLibro(mainModel::limpiar_cadena($Libro->getIdLibro()));

            if (isset($_FILES['txtImagenLibro'])) {
                $original = $_FILES['txtImagenLibro'];
                $nombre = $original['name'];
                if ($original['error'] > 0) {
                    $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                } else {
                    $resultado = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"), 3700, $original, $nombre, "./adjuntos/libros/");
                    if ($resultado == "") {
                        $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido");
                    } else {
                        $libro = libroModelo::datos_libro_modelo($this->conexion_db, "unico", $Libro);
                        if ($libro["countFilter"] == 0) {
                            $insBeanCrud->setMessageServer("No se encuentra el libro");
                        } else {
                            $Libro->setImagen($resultado);
                            $stmt = libroModelo::actualizar_libro_modelo($this->conexion_db, $Libro);
                            if ($stmt->execute()) {
                                if ($libro["list"][0]['imagen'] != "") {
                                    unlink('./adjuntos/libros/' . $libro["list"][0]['imagen']);
                                }
                                $this->conexion_db->commit();
                                $insBeanCrud->setMessageServer("ok");
                                $insBeanCrud->setBeanPagination(self::paginador_libro_controlador($this->conexion_db, 0, 5));

                            } else {
                                $insBeanCrud->setMessageServer("error en el servidor, No hemos podido actualizar el libro");
                            }
                        }
                    }
                }
            } else {

                $listalibro = libroModelo::datos_libro_modelo($this->conexion_db, "unico", $Libro);
                if ($listalibro["countFilter"] == 0) {
                    $insBeanCrud->setMessageServer("No se encuentra el libro");
                } else {
                    $Libro->setImagen($listalibro["list"][0]['imagen']);
                    $stmt = libroModelo::actualizar_libro_modelo($this->conexion_db, $Libro);
                    if ($stmt->execute()) {
                        $this->conexion_db->commit();
                        $insBeanCrud->setMessageServer("ok");
                        $insBeanCrud->setBeanPagination(self::paginador_libro_controlador($this->conexion_db, 0, 5));

                    } else {
                        $insBeanCrud->setMessageServer("error en el servidor, No hemos podido actualizar el libro");
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
