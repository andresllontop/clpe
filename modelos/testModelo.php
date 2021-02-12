<?php

require_once './core/mainModel.php';

class testModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }
    protected function agregar_test_modelo($conexion, $Test)
    {
        $sql = $conexion->prepare("INSERT INTO `test` (descripcion,nombre,codigotitulo,tipo,cantidad_preguntas,subtitulo_nombre_test,subtitulo_codigo_test,imagen) VALUES(:Descripcion,:Nombre,:IDtitulo,:Tipo,:Cantidad,:Sub,:SubCodigo,:Imagen)");

        $sql->bindValue(":Descripcion", $Test->getDescripcion(), PDO::PARAM_STR);
        $sql->bindValue(":Sub", $Test->getSub(), PDO::PARAM_STR);
        $sql->bindValue(":SubCodigo", $Test->getSubCodigo(), PDO::PARAM_STR);
        $sql->bindValue(":Nombre", $Test->getNombre(), PDO::PARAM_STR);
        $sql->bindValue(":IDtitulo", $Test->getTitulo(), PDO::PARAM_STR);
        $sql->bindValue(":Imagen", $Test->getImagen(), PDO::PARAM_STR);
        $sql->bindValue(":Tipo", $Test->getTipo(), PDO::PARAM_INT);
        $sql->bindValue(":Cantidad", $Test->getCantidad(), PDO::PARAM_INT);

        return $sql;
    }
    protected function datos_test_modelo($conexion, $tipo, $Test)
    {
        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(idtest) AS CONTADOR FROM `test` WHERE idtest=:IDtest");
                    $stmt->bindValue(":IDtest", $Test->getIdTest(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `test` WHERE idtest=:IDtest");
                            $stmt->bindValue(":IDtest", $Test->getIdTest(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {

                                $insTest = new Test();
                                $insTest->setIdTest($row['idtest']);
                                $insTest->setDescripcion($row['descripcion']);
                                $insTest->setTipo($row['tipo']);
                                $insTest->setImagen($row['imagen']);
                                $insTest->setTitulo($row['codigotitulo']);
                                $insBeanPagination->setList($insTest->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":
                    $stmt = $conexion->prepare("SELECT COUNT(idtest) AS CONTADOR FROM `test` ");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `test`");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insTest = new Test();
                                $insTest->setIdTest($row['idtest']);
                                $insTest->setDescripcion($row['descripcion']);
                                $insTest->setTipo($row['tipo']);

                                $insTest->setTitulo($row['codigotitulo']);
                                $insBeanPagination->setList($insTest->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "unico-update":
                    $stmt = $conexion->prepare("SELECT COUNT(idtest) AS CONTADOR FROM `test` WHERE idtest!=:IDtest and codigotitulo=:IDtitulo and tipo=:Tipo");
                    $stmt->bindValue(":Tipo", $Test->getTipo(), PDO::PARAM_INT);
                    $stmt->bindValue(":IDtest", $Test->getIdTest(), PDO::PARAM_INT);
                    $stmt->bindValue(":IDtitulo", $Test->getTitulo(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `test` WHERE idtest!=:IDtest and codigotitulo=:IDtitulo and tipo=:Tipo");
                            $stmt->bindValue(":Tipo", $Test->getTipo(), PDO::PARAM_INT);
                            $stmt->bindValue(":IDtest", $Test->getIdTest(), PDO::PARAM_INT);
                            $stmt->bindValue(":IDtitulo", $Test->getTitulo(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insTest = new Test();
                                $insTest->setIdTest($row['idtest']);
                                $insTest->setDescripcion($row['descripcion']);
                                $insTest->setTipo($row['tipo']);

                                $insTest->setTitulo($row['codigotitulo']);
                                $insBeanPagination->setList($insTest->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "unico-update-interno":
                    $stmt = $conexion->prepare("SELECT COUNT(idtest) AS CONTADOR FROM `test` WHERE idtest!=:IDtest and subtitulo_codigo_test=:IDtitulo and tipo=:Tipo");
                    $stmt->bindValue(":Tipo", $Test->getTipo(), PDO::PARAM_INT);
                    $stmt->bindValue(":IDtest", $Test->getIdTest(), PDO::PARAM_INT);
                    $stmt->bindValue(":IDtitulo", $Test->getSubCodigo(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `test` WHERE idtest!=:IDtest and subtitulo_codigo_test=:IDtitulo and tipo=:Tipo");
                            $stmt->bindValue(":Tipo", $Test->getTipo(), PDO::PARAM_INT);
                            $stmt->bindValue(":IDtest", $Test->getIdTest(), PDO::PARAM_INT);
                            $stmt->bindValue(":IDtitulo", $Test->getSubCodigo(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insTest = new Test();
                                $insTest->setIdTest($row['idtest']);
                                $insTest->setDescripcion($row['descripcion']);
                                $insTest->setTipo($row['tipo']);

                                $insTest->setTitulo($row['codigotitulo']);
                                $insBeanPagination->setList($insTest->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "cliente":
                    $stmt = $conexion->prepare("SELECT COUNT(idrespuesta) AS CONTADOR FROM `respuesta` WHERE idtest=:IDtest");
                    $stmt->bindValue(":IDtest", $Test->getIdTest(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT t.* FROM `respuesta` AS r  INNER JOIN `test` AS t ON r.idtest=t.idtest WHERE r.idtest=:IDtest");
                            $stmt->bindValue(":IDtest", $Test->getIdTest(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {

                                $insTest = new Test();
                                $insTest->setIdTest($row['idtest']);
                                $insTest->setDescripcion($row['descripcion']);
                                $insTest->setTipo($row['tipo']);

                                $insTest->setTitulo($row['codigotitulo']);
                                $insBeanPagination->setList($insTest->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "titulo":
                    $stmt = $conexion->prepare("SELECT COUNT(idtest) AS CONTADOR FROM `test` WHERE codigotitulo=:IDtitulo");
                    $stmt->bindValue(":IDtitulo", $Test->getTitulo(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `test` WHERE codigotitulo=:IDtitulo");
                            $stmt->bindValue(":IDtitulo", $Test->getTitulo(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {

                                $insTest = new Test();
                                $insTest->setIdTest($row['idtest']);
                                $insTest->setDescripcion($row['descripcion']);
                                $insTest->setTipo($row['tipo']);

                                $insTest->setTitulo($row['codigotitulo']);
                                $insBeanPagination->setList($insTest->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "tipo-titulo":
                    $stmt = $conexion->prepare("SELECT COUNT(idtest) AS CONTADOR FROM `test` WHERE tipo=:Tipo and codigotitulo=:IDtitulo");
                    $stmt->bindValue(":Tipo", $Test->getTipo(), PDO::PARAM_INT);
                    $stmt->bindValue(":IDtitulo", $Test->getTitulo(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `test` WHERE tipo=:Tipo and codigotitulo=:IDtitulo");
                            $stmt->bindValue(":Tipo", $Test->getTipo(), PDO::PARAM_INT);
                            $stmt->bindValue(":IDtitulo", $Test->getTitulo(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {

                                $insTest = new Test();
                                $insTest->setIdTest($row['idtest']);
                                $insTest->setDescripcion($row['descripcion']);
                                $insTest->setTipo($row['tipo']);

                                $insTest->setTitulo($row['codigotitulo']);
                                $insBeanPagination->setList($insTest->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "tipo-titulo-interno":
                    $stmt = $conexion->prepare("SELECT COUNT(idtest) AS CONTADOR FROM `test` WHERE tipo=:Tipo and subtitulo_codigo_test=:IDtitulo");
                    $stmt->bindValue(":Tipo", $Test->getTipo(), PDO::PARAM_INT);
                    $stmt->bindValue(":IDtitulo", $Test->getSubCodigo(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `test` WHERE tipo=:Tipo and subtitulo_codigo_test=:IDtitulo");
                            $stmt->bindValue(":Tipo", $Test->getTipo(), PDO::PARAM_INT);
                            $stmt->bindValue(":IDtitulo", $Test->getSubCodigo(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {

                                $insTest = new Test();
                                $insTest->setIdTest($row['idtest']);
                                $insTest->setDescripcion($row['descripcion']);
                                $insTest->setTipo($row['tipo']);
                                $insTest->setTitulo($row['codigotitulo']);
                                $insBeanPagination->setList($insTest->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "titulo-test":
                    $stmt = $conexion->prepare("SELECT COUNT(idtest) AS CONTADOR FROM `test` WHERE codigotitulo=:IDtitulo  AND idtest=:ID");
                    $stmt->bindValue(":IDtitulo", $Test->getTitulo(), PDO::PARAM_INT);
                    $stmt->bindValue(":ID", $Test->getIdTest(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `test` WHERE codigotitulo=:IDtitulo  AND idtest=:ID");
                            $stmt->bindValue(":IDtitulo", $Test->getTitulo(), PDO::PARAM_INT);
                            $stmt->bindValue(":ID", $Test->getIdTest(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {

                                $insTitulo = new Titulo();
                                $insTitulo->setCodigo($row['codigotitulo']);
                                if (isset($row['tituloNombre'])) {
                                    $insTitulo->setNombre($row['tituloNombre']);
                                }

                                $insTest = new Test();
                                $insTest->setIdTest($row['idtest']);
                                $insTest->setDescripcion($row['descripcion']);
                                $insTest->setTipo($row['tipo']);

                                $insTest->setTitulo($insTitulo->__toString());
                                $insBeanPagination->setList($insTest->__toString());
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
    protected function eliminar_test_modelo($conexion, $codigo)
    {

        $sql = $conexion->prepare("DELETE FROM `test`
        WHERE idtest=:IDtest ");
        $sql->bindValue(":IDtest", $codigo);
        return $sql;
    }
    protected function actualizar_test_modelo($conexion, $Test)
    {
        $sql = $conexion->prepare("UPDATE `test` SET descripcion=:Descripcion,nombre=:Nombre,codigotitulo=:IDtitulo,tipo=:Tipo,imagen=:Imagen,subtitulo_nombre_test=:Sub,subtitulo_codigo_test=:SubCodigo,cantidad_preguntas=:Cantidad WHERE idtest=:ID");
        $sql->bindValue(":ID", $Test->getIdTest(), PDO::PARAM_INT);
        $sql->bindValue(":Sub", $Test->getSub(), PDO::PARAM_STR);
        $sql->bindValue(":SubCodigo", $Test->getSubCodigo(), PDO::PARAM_STR);
        $sql->bindValue(":Nombre", $Test->getNombre(), PDO::PARAM_STR);
        $sql->bindValue(":Descripcion", $Test->getDescripcion(), PDO::PARAM_STR);
        $sql->bindValue(":Imagen", $Test->getImagen(), PDO::PARAM_STR);
        $sql->bindValue(":IDtitulo", $Test->getTitulo(), PDO::PARAM_STR);
        $sql->bindValue(":Tipo", $Test->getTipo(), PDO::PARAM_INT);
        $sql->bindValue(":Cantidad", $Test->getCantidad(), PDO::PARAM_INT);

        return $sql;
    }

}
