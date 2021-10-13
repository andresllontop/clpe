<?php

require_once './modelos/testModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';

require_once './classes/principal/titulo.php';

class testControlador extends testModelo
{
    public function agregar_test_controlador($BeanTest)
    {

        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Test = new Test();
            $Test->setDescripcion(mainModel::limpiar_cadena($BeanTest->getTest()->descripcion));
            $Test->setNombre(mainModel::limpiar_cadena($BeanTest->getTest()->nombre));
            $Test->setTitulo(mainModel::limpiar_cadena($BeanTest->getTest()->titulo));
            $Test->setTipo(mainModel::limpiar_cadena($BeanTest->getTest()->tipo));
            $Test->setCantidad(mainModel::limpiar_cadena($BeanTest->getTest()->cantidad));
            $Test->setSubCodigo(mainModel::limpiar_cadena($BeanTest->getTest()->subcodigo));
            $Test->setSub(mainModel::limpiar_cadena($BeanTest->getTest()->sub));

            #tipo=1 EXAMEN POR CAPITULO ; tipo=2 EXAMEN REFORSAMIENTO
            if (isset($_FILES['txtImagenTest'])) {
                $original = $_FILES['txtImagenTest'];
                $nombre = $original['name'];
                if ($Test->getTipo() == 1) {
                    $test = testModelo::datos_test_modelo($this->conexion_db, "tipo-titulo", $Test);
                } else {
                    $test = testModelo::datos_test_modelo($this->conexion_db, "tipo-titulo-interno", $Test);
                }
                if ($test["countFilter"] > 0) {
                    $insBeanCrud->setMessageServer("Ya se encuentra el capitulo asociado a un cuestionario");
                } else {
                    $resultado = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"), 5700, $original, $nombre, "./adjuntos/libros/subtitulos/");
                    if ($resultado != "") {
                        $Test->setImagen($resultado);
                        $stmt = testModelo::agregar_test_modelo($this->conexion_db, $Test);
                        if ($stmt->execute()) {
                            $stmt = $this->conexion_db->prepare("SELECT MAX(idtest) AS CONTADOR FROM `test`");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                if ($row['CONTADOR'] > 0) {
                                    $stmt = $this->conexion_db->prepare("INSERT INTO `detalle_test`(descripcion,idtest,codigo_subtitulo) VALUES(?,?,?)");
                                    $valor = false;
                                    foreach ($BeanTest->getListDetalle() as $lista) {
                                        $stmt->bindValue(1, $lista->descripcion, PDO::PARAM_STR);
                                        $stmt->bindValue(2, $row['CONTADOR'], PDO::PARAM_STR);
                                        $stmt->bindValue(3, $lista->subtitulo, PDO::PARAM_STR);
                                        $valor = $stmt->execute();
                                    }
                                    if ($valor) {
                                        $this->conexion_db->commit();
                                        $insBeanCrud->setMessageServer("ok");
                                        $insBeanCrud->setBeanPagination(self::paginador_test_controlador($this->conexion_db, 0, 20, $Test->getTipo()));
                                    }
                                }
                            }
                        } else {
                            $insBeanCrud->setMessageServer("No se puede registrar el cuestionario.");
                        }
                    } else {
                        $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido");
                    }

                }
            } else {
                if ($Test->getTipo() == 1) {
                    $test = testModelo::datos_test_modelo($this->conexion_db, "tipo-titulo", $Test);
                } else {
                    $test = testModelo::datos_test_modelo($this->conexion_db, "tipo-titulo-interno", $Test);
                }
                if ($test["countFilter"] > 0) {
                    $insBeanCrud->setMessageServer("Ya se encuentra el capitulo asociado a un cuestionario");
                } else {

                    $stmt = testModelo::agregar_test_modelo($this->conexion_db, $Test);
                    if ($stmt->execute()) {
                        $stmt = $this->conexion_db->prepare("SELECT MAX(idtest) AS CONTADOR FROM `test`");
                        $stmt->execute();
                        $datos = $stmt->fetchAll();
                        foreach ($datos as $row) {
                            if ($row['CONTADOR'] > 0) {
                                $stmt = $this->conexion_db->prepare("INSERT INTO `detalle_test`(descripcion,idtest,codigo_subtitulo) VALUES(?,?,?)");
                                $valor = false;
                                foreach ($BeanTest->getListDetalle() as $lista) {
                                    $stmt->bindValue(1, $lista->descripcion, PDO::PARAM_STR);
                                    $stmt->bindValue(2, $row['CONTADOR'], PDO::PARAM_STR);
                                    $stmt->bindValue(3, $lista->subtitulo, PDO::PARAM_STR);
                                    $valor = $stmt->execute();
                                }
                                if ($valor) {
                                    $this->conexion_db->commit();
                                    $insBeanCrud->setMessageServer("ok");
                                    $insBeanCrud->setBeanPagination(self::paginador_test_controlador($this->conexion_db, 0, 20, $Test->getTipo()));
                                }
                            }
                        }
                    } else {
                        $insBeanCrud->setMessageServer("No se puede registrar el cuestionario.");
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
    public function datos_test_controlador($tipo, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);

            $insBeanCrud->setBeanPagination(testModelo::datos_test_modelo($this->conexion_db, $tipo, $codigo));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();

    }
    public function paginador_test_controlador($conexion, $inicio, $registros, $tipo, $libro)
    {
        $insBeanPagination = new BeanPagination();
        try {
            $stmt = $conexion->prepare("SELECT COUNT(idtest) AS CONTADOR FROM `test` WHERE tipo=?  AND (codigotitulo LIKE CONCAT('%',?,'%'))");
            $stmt->bindValue(1, $tipo, PDO::PARAM_INT);
            $stmt->bindValue(2, $libro, PDO::PARAM_STR);
            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT t.*,ti.tituloNombre,ti.idtitulo FROM `test` as t inner join `titulo` as ti ON t.codigotitulo=ti.codigoTitulo  WHERE t.tipo=? AND (t.codigotitulo LIKE CONCAT('%',?,'%'))  ORDER BY t.codigotitulo ASC LIMIT ?,?");
                    $stmt->bindValue(1, $tipo, PDO::PARAM_INT);
                    $stmt->bindValue(2, $libro, PDO::PARAM_STR);
                    $stmt->bindValue(3, $inicio, PDO::PARAM_INT);
                    $stmt->bindValue(4, $registros, PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();

                    foreach ($datos as $row) {

                        $insTitulo = new Titulo();
                        $insTitulo->setIdTitulo($row['idtitulo']);
                        $insTitulo->setNombre($row['tituloNombre']);
                        $insTitulo->setCodigo($row['codigotitulo']);

                        $insTest = new Test();
                        $insTest->setIdTest($row['idtest']);
                        $insTest->setDescripcion($row['descripcion']);
                        $insTest->setCantidad($row['cantidad_preguntas']);
                        $insTest->setNombre($row['nombre']);
                        $insTest->setTipo($row['tipo']);
                        $insTest->setSubCodigo($row['subtitulo_codigo_test']);
                        $insTest->setSub($row['subtitulo_nombre_test']);
                        $insTest->setImagen($row['imagen']);

                        $insTest->setTitulo($insTitulo->__toString());
                        $insBeanPagination->setList($insTest->__toString());
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
    public function bean_paginador_test_controlador($pagina, $registros, $tipo, $libro)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $tipo = mainModel::limpiar_cadena($tipo);
            $libro = mainModel::limpiar_cadena($libro);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_test_controlador($this->conexion_db, $inicio, $registros, $tipo, $libro));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_test_controlador($Test)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Test->setIdTest(mainModel::limpiar_cadena($Test->getIdTest()));

            $lista = testModelo::datos_test_modelo($this->conexion_db, "cliente", $Test);
            if ($lista["countFilter"] > 0) {
                $insBeanCrud->setMessageServer("No podemos eliminar porque existen alumnos que respondieron a este cuestionario");
            } else {
                $lista = testModelo::datos_test_modelo($this->conexion_db, "unico", $Test);
                $stmt = $this->conexion_db->prepare("DELETE FROM `detalle_test` WHERE idtest=:IDtest");
                $stmt->bindValue(":IDtest", $Test->getIdTest());
                if ($stmt->execute()) {
                    $stmt = testModelo::eliminar_test_modelo($this->conexion_db, $Test->getIdTest());
                    if ($stmt->execute()) {
                        if ($lista["list"][0]['imagen'] != "" || $lista["list"][0]['imagen'] != null) {
                            unlink('./adjuntos/libros/subtitulos/' . $lista["list"][0]['imagen']);
                        }
                        $this->conexion_db->commit();
                        $insBeanCrud->setMessageServer("ok");
                        $insBeanCrud->setBeanPagination(self::paginador_test_controlador($this->conexion_db, 0, 20, $lista["list"][0]['tipo']));
                    } else {
                        $insBeanCrud->setMessageServer("No hemos podido eliminar el cuestionario");
                    }
                } else {
                    $insBeanCrud->setMessageServer("No hemos podido eliminar las preguntas del cuestionario");
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
    public function actualizar_test_controlador($BeanTest)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Test = new Test();
            $Test->setDescripcion(mainModel::limpiar_cadena($BeanTest->getTest()->descripcion));
            $Test->setNombre(mainModel::limpiar_cadena($BeanTest->getTest()->nombre));
            $Test->setTitulo(mainModel::limpiar_cadena($BeanTest->getTest()->titulo));
            $Test->setTipo(mainModel::limpiar_cadena($BeanTest->getTest()->tipo));
            $Test->setCantidad(mainModel::limpiar_cadena($BeanTest->getTest()->cantidad));
            $Test->setIdTest(mainModel::limpiar_cadena($BeanTest->getTest()->idtest));
            $Test->setSubCodigo(mainModel::limpiar_cadena($BeanTest->getTest()->subcodigo));
            $Test->setSub(mainModel::limpiar_cadena($BeanTest->getTest()->sub));
            $testunico = testModelo::datos_test_modelo($this->conexion_db, "unico", $Test);

            if ($testunico["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("no se encuentra el cuestionario");
            } else {
                if ($Test->getTipo() == 1) {
                    $test = testModelo::datos_test_modelo($this->conexion_db, "unico-update", $Test);
                } else {
                    $test = testModelo::datos_test_modelo($this->conexion_db, "unico-update-interno", $Test);
                }
                if (isset($_FILES['txtImagenTest'])) {
                    $original = $_FILES['txtImagenTest'];
                    $nombre = $original['name'];
                    if ($test["countFilter"] > 0) {
                        $insBeanCrud->setMessageServer("Ya se encuentra el capitulo asociado a un cuestionario");
                    } else {
                        $resultado = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"), 5700, $original, $nombre, "./adjuntos/libros/subtitulos/");
                        if ($resultado != "") {
                            $Test->setImagen($resultado);

                            $stmt = testModelo::actualizar_test_modelo($this->conexion_db, $Test);
                            if ($stmt->execute()) {
                                $stmt = $this->conexion_db->prepare("DELETE FROM `detalle_test` WHERE idtest=:IDtest ");
                                $stmt->bindValue(":IDtest", $Test->getIdTest(), PDO::PARAM_INT);
                                if ($stmt->execute()) {
                                    $valor = false;
                                    $stmt = $this->conexion_db->prepare("INSERT INTO `detalle_test`(descripcion,idtest,codigo_subtitulo) VALUES(?,?,?)");
                                    $valor = false;
                                    foreach ($BeanTest->getListDetalle() as $lista) {
                                        $stmt->bindValue(1, $lista->descripcion, PDO::PARAM_STR);
                                        $stmt->bindValue(2, $Test->getIdTest(), PDO::PARAM_INT);
                                        $stmt->bindValue(3, $lista->subtitulo, PDO::PARAM_STR);
                                        $valor = $stmt->execute();
                                    }
                                    if ($valor) {
                                        if ($testunico["list"][0]['imagen'] != "" || $testunico["list"][0]['imagen'] != null) {
                                            unlink('./adjuntos/libros/subtitulos/' . $testunico["list"][0]['imagen']);
                                        }
                                        $this->conexion_db->commit();
                                        $insBeanCrud->setMessageServer("ok");
                                        $insBeanCrud->setBeanPagination(self::paginador_test_controlador($this->conexion_db, 0, 20, $Test->getTipo()));
                                    }
                                } else {
                                    $insBeanCrud->setMessageServer("No hemos podido actualizar las preguntas");
                                }

                            } else {
                                $insBeanCrud->setMessageServer("No hemos podido actualizar el cuestionario");
                            }

                        } else {
                            $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido");
                        }
                    }
                } else {
                    $Test->setImagen($testunico["list"][0]['imagen']);
                    if ($test["countFilter"] > 0) {
                        $insBeanCrud->setMessageServer("Ya se encuentra el capitulo asociado a un cuestionario");
                    } else {
                        $stmt = testModelo::actualizar_test_modelo($this->conexion_db, $Test);
                        if ($stmt->execute()) {
                            $stmt = $this->conexion_db->prepare("DELETE FROM `detalle_test` WHERE idtest=:IDtest ");
                            $stmt->bindValue(":IDtest", $Test->getIdTest(), PDO::PARAM_INT);
                            if ($stmt->execute()) {
                                $valor = false;
                                $stmt = $this->conexion_db->prepare("INSERT INTO `detalle_test`(descripcion,idtest,codigo_subtitulo) VALUES(?,?,?)");
                                $valor = false;
                                foreach ($BeanTest->getListDetalle() as $lista) {
                                    $stmt->bindValue(1, $lista->descripcion, PDO::PARAM_STR);
                                    $stmt->bindValue(2, $Test->getIdTest(), PDO::PARAM_INT);
                                    $stmt->bindValue(3, $lista->subtitulo, PDO::PARAM_STR);
                                    $valor = $stmt->execute();
                                }
                                if ($valor) {
                                    $this->conexion_db->commit();
                                    $insBeanCrud->setMessageServer("ok");
                                    $insBeanCrud->setBeanPagination(self::paginador_test_controlador($this->conexion_db, 0, 20, $Test->getTipo()));
                                }
                            } else {
                                $insBeanCrud->setMessageServer("No hemos podido actualizar las preguntas");
                            }

                        } else {
                            $insBeanCrud->setMessageServer("No hemos podido actualizar el cuestionario");
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
