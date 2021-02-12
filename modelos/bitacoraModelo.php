<?php

require_once './core/mainModel.php';

class bitacoraModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }
    protected function agregar_bitacora_modelo($conexion, $bitacora)
    {
        $sql = $conexion->prepare("INSERT INTO `bitacora`(estado,fecha_inicio,tipo,cuenta_codigocuenta)
         VALUES(?,?,?,?)");
        $sql->bindValue(1, $bitacora->getEstado(), PDO::PARAM_INT);
        $sql->bindValue(2, $bitacora->getFecha_Inicio());
        $sql->bindValue(3, $bitacora->getTipo(), PDO::PARAM_INT);
        $sql->bindValue(4, $bitacora->getCuenta(), PDO::PARAM_STR);
        return $sql;
    }
    protected function datos_bitacora_modelo($conexion, $tipo, $bitacora)
    {
        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(id) AS CONTADOR FROM `bitacora` WHERE id=:IDbitacora");
                    $stmt->bindValue(":IDbitacora", $bitacora->getIdbitacora(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `bitacora`
                            WHERE id=:IDbitacora");
                            $stmt->bindValue(":IDbitacora", $bitacora->getIdbitacora(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {

                                $insBitacora = new Bitacora();
                                $insBitacora->setIdbitacora($row['id']);
                                $insBitacora->setEstado($row['estado']);
                                $insBitacora->setFecha_Inicio($row['fecha_inicio']);
                                $insBitacora->setFecha_Fin($row['fecha_fin']);
                                $insBitacora->setTipo($row['tipo']);
                                $insBitacora->setCuenta($row['cuenta_codigoCuenta']);

                                $insBeanPagination->setList($insBitacora->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":
                    $stmt = $conexion->prepare("SELECT COUNT(id) AS CONTADOR FROM `bitacora`");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `bitacora`");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insBitacora = new Bitacora();
                                $insBitacora->setIdbitacora($row['id']);
                                $insBitacora->setEstado($row['estado']);
                                $insBitacora->setFecha_Inicio($row['fecha_inicio']);
                                $insBitacora->setFecha_Fin($row['fecha_fin']);
                                $insBitacora->setTipo($row['tipo']);
                                $insBitacora->setCuenta($row['cuenta_codigoCuenta']);
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
    protected function eliminar_bitacora_modelo($conexion, $bitacora)
    {
        $sql = $conexion->prepare("DELETE FROM `bitacora` WHERE  id=? ");
        $sql->bindValue(1, $bitacora, PDO::PARAM_INT);
        return $sql;
    }
    protected function eliminar_bitacoraCuenta_modelo($conexion, $bitacora)
    {
        $sql = $conexion->prepare("DELETE FROM `bitacora` WHERE cuenta_codigoCuenta=:IDbitacora ");
        $sql->bindValue(":IDbitacora", $codigo);
        return $sql;
    }
    protected function actualizar_bitacora_modelo($conexion, $bitacora)
    {
        $sql = $conexion->prepare("UPDATE `bitacora` SET estado=?, fecha_fin=? WHERE cuenta_codigocuenta=? and id=?");
        $sql->bindValue(1, $bitacora->getEstado(), PDO::PARAM_INT);
        $sql->bindValue(2, $bitacora->getFecha_Fin());
        $sql->bindValue(3, $bitacora->getCuenta(), PDO::PARAM_STR);
        $sql->bindValue(4, $bitacora->getIdbitacora(), PDO::PARAM_INT);
        return $sql;
    }

}
