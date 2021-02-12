<?php

require_once './modelos/detalletestModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';

require_once './classes/principal/test.php';
require_once './classes/principal/subtitulo.php';
class detalletestControlador extends detalletestModelo
{
    public function agregar_detalletest_controlador($DetalleTest)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $DetalleTest->setDescripcion(mainModel::limpiar_cadena($DetalleTest->getDescripcion()));
            $DetalleTest->setSubtitulo(mainModel::limpiar_cadena($DetalleTest->getSubtitulo()));
            $DetalleTest->setTest(mainModel::limpiar_cadena($DetalleTest->getTest()));

            $stmt = detalletestModelo::agregar_detalletest_modelo($this->conexion_db, $DetalleTest);
            if ($stmt->execute()) {
                $this->conexion_db->commit();
                $insBeanCrud->setMessageServer("ok");
                $insBeanCrud->setBeanPagination(self::paginador_detalletest_controlador($this->conexion_db, 0, 5, $DetalleTest->getTest()));

            } else {
                $insBeanCrud->setMessageServer("error en el servidor, No hemos podido registrar la pregunta");
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
    public function datos_detalletest_controlador($tipo, $codigo)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);
            $insBeanCrud->setBeanPagination(detalletestModelo::datos_detalletest_modelo($this->conexion_db, $tipo, $codigo));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();

    }
    public function paginador_detalletest_controlador($conexion, $inicio, $registros, $test)
    {
        $insBeanPagination = new BeanPagination();
        try {
            $stmt = $conexion->prepare("SELECT COUNT(iddetalle_test) AS CONTADOR FROM `detalle_test` WHERE idtest=?");
            $stmt->bindValue(1, $test, PDO::PARAM_INT);
            $stmt->execute();

            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT DET.*,SUB.nombre FROM `detalle_test` AS DET INNER JOIN `subtitulo` AS SUB ON SUB.codigo_subtitulo=DET.codigo_subtitulo WHERE DET.idtest=? ORDER BY DET.codigo_subtitulo ASC LIMIT ?,?");
                    $stmt->bindValue(1, $test, PDO::PARAM_INT);
                    $stmt->bindValue(2, $inicio, PDO::PARAM_INT);
                    $stmt->bindValue(3, $registros, PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();

                    foreach ($datos as $row) {
                        $insSubTitulo = new SubTitulo();
                        $insSubTitulo->setCodigo($row['codigo_subtitulo']);
                        $insSubTitulo->setNombre($row['nombre']);

                        $insDetalleTest = new DetalleTest();
                        $insDetalleTest->setIdDetalleTest($row['iddetalle_test']);
                        $insDetalleTest->setSubtitulo($insSubTitulo->__toString());
                        $insDetalleTest->setDescripcion($row['descripcion']);
                        $insDetalleTest->setTest($row['idtest']);

                        $insBeanPagination->setList($insDetalleTest->__toString());
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
    public function bean_paginador_detalletest_controlador($pagina, $registros, $test)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $test = mainModel::limpiar_cadena($test);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_detalletest_controlador($this->conexion_db, $inicio, $registros, $test));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_detalletest_controlador($DetalleTest)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $DetalleTest->setIdDetalleTest(mainModel::limpiar_cadena($DetalleTest->getIdDetalleTest()));

            $detalletest = detalletestModelo::datos_detalletest_modelo($this->conexion_db, "unico", $DetalleTest);
            if ($detalletest["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("error en el servidor, No se encuentra la pregunta");

            } else {
                $stmt = detalletestModelo::eliminar_detalletest_modelo($this->conexion_db, $DetalleTest->getIdDetalleTest());
                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_detalletest_controlador($this->conexion_db, 0, 5, $detalletest["list"][0]['test']));

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
    public function actualizar_detalletest_controlador($DetalleTest)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $DetalleTest->setDescripcion(mainModel::limpiar_cadena($DetalleTest->getDescripcion()));
            $DetalleTest->setSubtitulo(mainModel::limpiar_cadena($DetalleTest->getSubtitulo()));
            $DetalleTest->setTest(mainModel::limpiar_cadena($DetalleTest->getTest()));

            $DetalleTest->setIdDetalleTest(mainModel::limpiar_cadena($DetalleTest->getIdDetalleTest()));

            $detalletest = detalletestModelo::datos_detalletest_modelo($this->conexion_db, "unico", $DetalleTest);
            if ($detalletest["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("error en el servidor, No se encuentra la pregunta");
            } else {
                $stmt = detalletestModelo::actualizar_detalletest_modelo($this->conexion_db, $DetalleTest);
                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_detalletest_controlador($this->conexion_db, 0, 5, $DetalleTest->getTest()));

                } else {

                    $insBeanCrud->setMessageServer("error en el servidor, No hemos podido actualizar la pregunta");
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
