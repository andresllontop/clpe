<?php

require_once './core/mainModel.php';

class detallerecursoModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }
    protected function agregar_detallerecurso_modelo($conexion, $datos)
    {
        $sql = $conexion->prepare("INSERT INTO `detalle_recurso`(descripcion,tipo,archivo,idrecurso) VALUES(:Descripcion,:Tipo,:Archivo,:Recurso)");
        $sql->bindValue(":Descripcion", $datos->getDescripcion(), PDO::PARAM_STR);
        $sql->bindValue(":Tipo", $datos->getTipo(), PDO::PARAM_INT);
        $sql->bindValue(":Archivo", $datos->getArchivo(), PDO::PARAM_STR);
        $sql->bindValue(":Recurso", $datos->getRecurso(), PDO::PARAM_INT);
        return $sql;
    }
    protected function datos_detallerecurso_modelo($conexion, $tipo, $codigo)
    {

        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(iddetalle_recurso) AS CONTADOR FROM `detalle_recurso` WHERE iddetalle_recurso=:ID");
                    $stmt->bindValue(":ID", $codigo->getIdDetalleRecurso(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `detalle_recurso` WHERE iddetalle_recurso=:Codigo");
                            $stmt->bindValue(":Codigo", $codigo->getIdDetalleRecurso(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insDetalleRecurso = new DetalleRecurso();
                                $insDetalleRecurso->setIdDetalleRecurso($row['iddetalle_recurso']);
                                $insDetalleRecurso->setTipo($row['tipo']);
                                $insDetalleRecurso->setArchivo($row['archivo']);
                                $insDetalleRecurso->setDescripcion($row['descripcion']);
                                $insDetalleRecurso->setRecurso($row['idrecurso']);

                                $insBeanPagination->setList($insDetalleRecurso->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "cliente":
                    $stmt = $conexion->prepare("SELECT COUNT(id_recurso) AS CONTADOR FROM `recurso_cliente` WHERE id_recurso=:Codigo");
                    $stmt->bindValue(":Codigo", $codigo->getIdRecurso(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT r.* FROM `recurso_cliente` AS c
                                INNER JOIN `recurso` AS r ON c.id_recurso=r.idrecurso WHERE c.id_recurso=:Codigo");
                            $stmt->bindValue(":Codigo", $codigo->getIdRecurso(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insDetalleRecurso = new DetalleRecurso();
                                $insDetalleRecurso->setIdDetalleRecurso($row['iddetalle_recurso']);
                                $insDetalleRecurso->setTipo($row['tipo']);
                                $insDetalleRecurso->setArchivo($row['archivo']);
                                $insDetalleRecurso->setDescripcion($row['descripcion']);
                                $insDetalleRecurso->setRecurso($row['idrecurso']);

                                $insBeanPagination->setList($insDetalleRecurso->__toString());
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
    protected function eliminar_detallerecurso_modelo($conexion, $codigo)
    {
        $sql = $conexion->prepare("DELETE FROM `detalle_recurso` WHERE iddetalle_recurso=:ID ");
        $sql->bindValue(":ID", $codigo, PDO::PARAM_INT);
        return $sql;
    }
    protected function actualizar_detallerecurso_modelo($conexion, $datos)
    {

        $sql = $conexion->prepare("UPDATE `detalle_recurso` SET
        descripcion=:Descripcion,tipo=:Tipo,archivo=:Archivo,idrecurso=:Recurso
         WHERE iddetalle_recurso=:ID ");
        $sql->bindValue(":ID", $datos->getIdDetalleRecurso(), PDO::PARAM_INT);
        $sql->bindValue(":Descripcion", $datos->getDescripcion(), PDO::PARAM_STR);
        $sql->bindValue(":Tipo", $datos->getTipo(), PDO::PARAM_INT);
        $sql->bindValue(":Archivo", $datos->getArchivo(), PDO::PARAM_STR);
        $sql->bindValue(":Recurso", $datos->getRecurso(), PDO::PARAM_INT);

        return $sql;
    }

}
