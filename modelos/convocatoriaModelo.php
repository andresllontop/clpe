<?php

require_once './core/mainModel.php';

class convocatoriaModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }
    protected function agregar_convocatoria_modelo($conexion, $Convocatoria)
    {
        $sql = $conexion->prepare("INSERT INTO `convocatoria` (codigo,descripcion,estado,cantidad,fecha,imagen) VALUES(:Codigo,:Descripcion,:Estado,:Cantidad,:Fecha,:Imagen)");

        $sql->bindValue(":Codigo", $Convocatoria->getCodigo(), PDO::PARAM_STR);
        $sql->bindValue(":Descripcion", $Convocatoria->getDescripcion(), PDO::PARAM_STR);
        $sql->bindValue(":Fecha", $Convocatoria->getFecha());
        $sql->bindValue(":Imagen", $Convocatoria->getImagen(), PDO::PARAM_STR);
        $sql->bindValue(":Estado", $Convocatoria->getEstado(), PDO::PARAM_INT);
        $sql->bindValue(":Cantidad", $Convocatoria->getCantidad(), PDO::PARAM_INT);

        return $sql;
    }
    protected function datos_convocatoria_modelo($conexion, $tipo, $Convocatoria)
    {
        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(idconvocatoria) AS CONTADOR FROM `convocatoria` WHERE idconvocatoria=:IDconvocatoria");
                    $stmt->bindValue(":IDconvocatoria", $Convocatoria->getIdConvocatoria(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `convocatoria` WHERE idconvocatoria=:IDconvocatoria");
                            $stmt->bindValue(":IDconvocatoria", $Convocatoria->getIdConvocatoria(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {

                                $insConvocatoria = new Convocatoria();
                                $insConvocatoria->setIdConvocatoria($row['idconvocatoria']);
                                $insConvocatoria->setCodigo($row['codigo']);
                                $insConvocatoria->setDescripcion($row['descripcion']);
                                $insConvocatoria->setEstado($row['estado']);
                                $insConvocatoria->setImagen($row['imagen']);
                                $insConvocatoria->setFecha($row['fecha']);
                                $insConvocatoria->setCantidad($row['cantidad']);
                                $insBeanPagination->setList($insConvocatoria->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "unico-estado":
                    $stmt = $conexion->prepare("SELECT COUNT(idconvocatoria) AS CONTADOR FROM `convocatoria` WHERE idconvocatoria=:IDconvocatoria AND estado=:Estado");
                    $stmt->bindValue(":IDconvocatoria", $Convocatoria->getIdConvocatoria(), PDO::PARAM_INT);
                    $stmt->bindValue(":Estado", $Convocatoria->getEstado(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT convo.*,detalle.descripcion as detalle_descripcion,detalle.tipo as detalle_tipo FROM `detalle_convocatoria` AS detalle INNER JOIN `convocatoria` AS convo ON convo.idconvocatoria=detalle.idconvocatoria WHERE convo.idconvocatoria=:IDconvocatoria AND convo.estado=:Estado");
                            $stmt->bindValue(":IDconvocatoria", $Convocatoria->getIdConvocatoria(), PDO::PARAM_INT);
                            $stmt->bindValue(":Estado", $Convocatoria->getEstado(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insDetalleConvocatoria = new DetalleConvocatoria();
                                $insDetalleConvocatoria->setDescripcion($row['detalle_descripcion']);
                                $insDetalleConvocatoria->setTipo($row['detalle_tipo']);
                                $insConvocatoria = new Convocatoria();
                                $insConvocatoria->setIdConvocatoria($row['idconvocatoria']);
                                $insConvocatoria->setDescripcion($row['descripcion']);
                                $insConvocatoria->setEstado($row['estado']);
                                $insConvocatoria->setCodigo($row['codigo']);
                                $insConvocatoria->setImagen($row['imagen']);
                                $insConvocatoria->setFecha($row['fecha']);
                                $insConvocatoria->setCantidad($row['cantidad']);
                                $insDetalleConvocatoria->setConvocatoria($insConvocatoria->__toString());
                                $insBeanPagination->setList($insDetalleConvocatoria->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":
                    $stmt = $conexion->prepare("SELECT COUNT(idconvocatoria) AS CONTADOR FROM `convocatoria` ");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `convocatoria`");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insConvocatoria = new Convocatoria();
                                $insConvocatoria->setIdConvocatoria($row['idconvocatoria']);
                                $insConvocatoria->setDescripcion($row['descripcion']);
                                $insConvocatoria->setCodigo($row['codigo']);
                                $insConvocatoria->setEstado($row['estado']);
                                $insConvocatoria->setImagen($row['imagen']);
                                $insConvocatoria->setFecha($row['fecha']);
                                $insConvocatoria->setCantidad($row['cantidad']);
                                $insBeanPagination->setList($insConvocatoria->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "estado":
                    $stmt = $conexion->prepare("SELECT COUNT(idconvocatoria) AS CONTADOR FROM `convocatoria` WHERE estado=:Estado");
                    $stmt->bindValue(":Estado", $Convocatoria->getEstado(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT convo.*,detalle.descripcion as detalle_descripcion FROM `detalle_convocatoria` AS detalle INNER JOIN `convocatoria` AS convo ON convo.idconvocatoria=detalle.idconvocatoria WHERE estado=:Estado ");
                            $stmt->bindValue(":Estado", $Convocatoria->getEstado(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insDetalleConvocatoria = new DetalleConvocatoria();
                                $insDetalleConvocatoria->setDescripcion($row['detalle_descripcion']);

                                $insConvocatoria = new Convocatoria();
                                $insConvocatoria->setIdConvocatoria($row['idconvocatoria']);
                                $insConvocatoria->setDescripcion($row['descripcion']);
                                $insConvocatoria->setEstado($row['estado']);
                                $insConvocatoria->setCodigo($row['codigo']);
                                $insConvocatoria->setImagen($row['imagen']);
                                $insConvocatoria->setFecha($row['fecha']);
                                $insConvocatoria->setCantidad($row['cantidad']);
                                $insDetalleConvocatoria->setConvocatoria($insConvocatoria->__toString());
                                $insBeanPagination->setList($insDetalleConvocatoria->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "detalle":
                    $stmt = $conexion->prepare("SELECT COUNT(iddetalle_convocatoria) AS CONTADOR FROM `detalle_convocatoria` WHERE idconvocatoria=:ID");
                    $stmt->bindValue(":ID", $Convocatoria->getIdConvocatoria(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `detalle_convocatoria` WHERE idconvocatoria=:ID");
                            $stmt->bindValue(":ID", $Convocatoria->getIdConvocatoria(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insDetalleConvocatoria = new DetalleConvocatoria();
                                $insDetalleConvocatoria->setIdDetalleConvocatoria($row['iddetalle_convocatoria']);
                                $insDetalleConvocatoria->setDescripcion($row['descripcion']);
                                $insDetalleConvocatoria->setTipo($row['tipo']);
                                $insDetalleConvocatoria->setConvocatoria($row['idconvocatoria']);
                                $insBeanPagination->setList($insDetalleConvocatoria->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;

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
    protected function eliminar_convocatoria_modelo($conexion, $codigo)
    {

        $sql = $conexion->prepare("DELETE FROM `convocatoria`
        WHERE idconvocatoria=:IDconvocatoria ");
        $sql->bindValue(":IDconvocatoria", $codigo);
        return $sql;
    }
    protected function actualizar_convocatoria_modelo($conexion, $Convocatoria)
    {
        $sql = $conexion->prepare("UPDATE `convocatoria` SET codigo=:Codigo,descripcion=:Descripcion,fecha=:Fecha,estado=:Estado,imagen=:Imagen,cantidad=:Cantidad WHERE idconvocatoria=:ID");
        $sql->bindValue(":ID", $Convocatoria->getIdConvocatoria(), PDO::PARAM_INT);
        $sql->bindValue(":Codigo", $Convocatoria->getCodigo(), PDO::PARAM_STR);
        $sql->bindValue(":Descripcion", $Convocatoria->getDescripcion(), PDO::PARAM_STR);
        $sql->bindValue(":Fecha", $Convocatoria->getFecha(), PDO::PARAM_STR);
        $sql->bindValue(":Imagen", $Convocatoria->getImagen(), PDO::PARAM_STR);
        $sql->bindValue(":Estado", $Convocatoria->getEstado(), PDO::PARAM_INT);
        $sql->bindValue(":Cantidad", $Convocatoria->getCantidad(), PDO::PARAM_INT);

        return $sql;
    }
    protected function actualizar_estado_convocatoria_modelo($conexion, $Convocatoria)
    {
        $sql = $conexion->prepare("UPDATE `convocatoria` SET estado=:Estado WHERE idconvocatoria=:ID");
        $sql->bindValue(":ID", $Convocatoria->getIdConvocatoria(), PDO::PARAM_INT);
        $sql->bindValue(":Estado", $Convocatoria->getEstado(), PDO::PARAM_INT);

        return $sql;
    }

}
