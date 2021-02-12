<?php

require_once './core/mainModel.php';

class detalletestModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }
    protected function agregar_detalletest_modelo($conexion, $datos)
    {
        $sql = $conexion->prepare("INSERT INTO `detalle_test`(descripcion,codigo_subtitulo,idtest) VALUES(:Descripcion,:Subtitulo,:Test)");
        $sql->bindValue(":Descripcion", $datos->getDescripcion(), PDO::PARAM_STR);
        $sql->bindValue(":Subtitulo", $datos->getSubtitulo(), PDO::PARAM_STR);
        $sql->bindValue(":Test", $datos->getTest(), PDO::PARAM_INT);
        return $sql;
    }
    protected function datos_detalletest_modelo($conexion, $tipo, $codigo)
    {

        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(iddetalle_test) AS CONTADOR FROM `detalle_test` WHERE iddetalle_test=:ID");
                    $stmt->bindValue(":ID", $codigo->getIdDetalleTest(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `detalle_test` WHERE iddetalle_test=:Codigo");
                            $stmt->bindValue(":Codigo", $codigo->getIdDetalleTest(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insDetalleTest = new DetalleTest();
                                $insDetalleTest->setIdDetalleTest($row['iddetalle_test']);
                                $insDetalleTest->setSubtitulo($row['codigo_subtitulo']);
                                $insDetalleTest->setDescripcion($row['descripcion']);
                                $insDetalleTest->setTest($row['idtest']);

                                $insBeanPagination->setList($insDetalleTest->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "cliente":
                    $stmt = $conexion->prepare("SELECT COUNT(id_test) AS CONTADOR FROM `test_cliente` WHERE id_test=:Codigo");
                    $stmt->bindValue(":Codigo", $codigo->getIdTest(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT r.* FROM `test_cliente` AS c
                                INNER JOIN `test` AS r ON c.id_test=r.idtest WHERE c.id_test=:Codigo");
                            $stmt->bindValue(":Codigo", $codigo->getIdTest(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insDetalleTest = new DetalleTest();
                                $insDetalleTest->setIdDetalleTest($row['iddetalle_test']);
                                $insDetalleTest->setTipo($row['tipo']);
                                $insDetalleTest->setArchivo($row['archivo']);
                                $insDetalleTest->setDescripcion($row['descripcion']);
                                $insDetalleTest->setTest($row['idtest']);

                                $insBeanPagination->setList($insDetalleTest->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                default:
                    # code...
                    break;
            }
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        }
        return $insBeanPagination->__toString();

    }
    protected function eliminar_detalletest_modelo($conexion, $codigo)
    {
        $sql = $conexion->prepare("DELETE FROM `detalle_test` WHERE iddetalle_test=:ID ");
        $sql->bindValue(":ID", $codigo, PDO::PARAM_INT);
        return $sql;
    }
    protected function actualizar_detalletest_modelo($conexion, $datos)
    {

        $sql = $conexion->prepare("UPDATE `detalle_test` SET
        descripcion=:Descripcion,codigo_subtitulo=:Subtitulo,idtest=:Test
         WHERE iddetalle_test=:ID ");
        $sql->bindValue(":ID", $datos->getIdDetalleTest(), PDO::PARAM_INT);
        $sql->bindValue(":Descripcion", $datos->getDescripcion(), PDO::PARAM_STR);
        $sql->bindValue(":Subtitulo", $datos->getSubtitulo(), PDO::PARAM_STR);
        $sql->bindValue(":Test", $datos->getTest(), PDO::PARAM_INT);

        return $sql;
    }

}
