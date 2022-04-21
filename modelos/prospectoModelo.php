<?php

require_once './core/mainModel.php';

class prospectoModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }
    protected function agregar_prospecto_modelo($conexion, $Prospecto)
    {
        $sql = mainModel::__construct()->prepare("INSERT INTO
        `prospecto`(cuenta,documento,nombre,pais,telefono,email,especialidad,father_idprospecto)
        VALUES(:Cuenta,:Documento,:Nombre,:Pais,:Telefono,:Email,:Especialidad,:FatherProspecto)");
        $sql->bindValue(":Nombre", $Prospecto->getNombre(), PDO::PARAM_STR);
        $sql->bindValue(":Pais", $Prospecto->getPais(), PDO::PARAM_STR);
        $sql->bindValue(":Telefono", $Prospecto->getTelefono(), PDO::PARAM_STR);
        $sql->bindValue(":FatherProspecto", $Prospecto->getIdFatherProspecto(), PDO::PARAM_INT);
        $sql->bindValue(":Email", $Prospecto->getEmail(), PDO::PARAM_STR);
        $sql->bindValue(":Especialidad", $Prospecto->getEspecialidad(), PDO::PARAM_STR);
        $sql->bindValue(":Cuenta", $Prospecto->getCuenta() == "" || $Prospecto->getCuenta() == null ? null : $Prospecto->getCuenta());
        $sql->bindValue(":Documento", $Prospecto->getDocumento() == "" || $Prospecto->getDocumento() == null ? null : $Prospecto->getDocumento());
        return $sql;
    }
    protected function datos_prospecto_modelo($conexion, $tipo, $prospecto)
    {
        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(idprospecto) AS CONTADOR FROM `prospecto` WHERE idprospecto=:IDprospecto");
                    $stmt->bindValue(":IDprospecto", $prospecto->getIdprospecto(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `prospecto`
                            WHERE idprospecto=:IDprospecto");
                            $stmt->bindValue(":IDprospecto", $prospecto->getIdprospecto(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insProspecto = new Prospecto();
                                $insProspecto->setIdprospecto($row['idprospecto']);
                                $insProspecto->setDocumento($row['documento']);
                                $insProspecto->setCuenta($row['cuenta']);
                                $insProspecto->setNombre($row['nombre']);
                                $insProspecto->setPais($row['pais']);
                                $insProspecto->setTelefono($row['telefono']);
                                $insProspecto->setEmail($row['email']);
                                $insProspecto->setEspecialidad($row['especialidad']);
                                $insProspecto->setIdFatherProspecto($row['father_idprospecto']);

                                $insBeanPagination->setList($insProspecto->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "father":
                    $stmt = $conexion->prepare("SELECT COUNT(idprospecto) AS CONTADOR FROM `prospecto` WHERE father_idprospecto=:IDprospecto");
                    $stmt->bindValue(":IDprospecto", $prospecto->getIdprospecto(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `prospecto`
                                WHERE father_idprospecto=:IDprospecto");
                            $stmt->bindValue(":IDprospecto", $prospecto->getIdprospecto(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insProspecto = new Prospecto();
                                $insProspecto->setIdprospecto($row['idprospecto']);
                                $insProspecto->setDocumento($row['documento']);
                                $insProspecto->setCuenta($row['cuenta']);
                                $insProspecto->setNombre($row['nombre']);
                                $insProspecto->setPais($row['pais']);
                                $insProspecto->setTelefono($row['telefono']);
                                $insProspecto->setEmail($row['email']);
                                $insProspecto->setEspecialidad($row['especialidad']);
                                $insProspecto->setIdFatherProspecto($row['father_idprospecto']);

                                $insBeanPagination->setList($insProspecto->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "cuenta":
                    $stmt = $conexion->prepare("SELECT COUNT(idprospecto) AS CONTADOR FROM `prospecto` WHERE cuenta=:IDprospecto");
                    $stmt->bindValue(":IDprospecto", $prospecto->getCuenta(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `prospecto`
                                WHERE cuenta=:IDprospecto");
                            $stmt->bindValue(":IDprospecto", $prospecto->getCuenta(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insProspecto = new Prospecto();
                                $insProspecto->setIdprospecto($row['idprospecto']);
                                $insProspecto->setDocumento($row['documento']);
                                $insProspecto->setCuenta($row['cuenta']);
                                $insProspecto->setNombre($row['nombre']);
                                $insProspecto->setPais($row['pais']);
                                $insProspecto->setTelefono($row['telefono']);
                                $insProspecto->setEmail($row['email']);
                                $insProspecto->setEspecialidad($row['especialidad']);
                                $insProspecto->setIdFatherProspecto($row['father_idprospecto']);

                                $insBeanPagination->setList($insProspecto->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "getCuentaByNombre":
                    $stmt = $conexion->prepare("SELECT COUNT(id) AS CONTADOR FROM `administrador`");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT Cuenta_Codigo FROM `administrador`
                                WHERE CONCAT(AdminNombre,' ',AdminApellido)=:Nombre");
                            $stmt->bindValue(":Nombre", $prospecto->getNombre(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insProspecto = new Prospecto();
                                $insProspecto->setCuenta($row['Cuenta_Codigo']);

                                $insBeanPagination->setList($insProspecto->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":
                    $stmt = $conexion->prepare("SELECT COUNT(idprospecto) AS CONTADOR FROM `prospecto`");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `prospecto`");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insProspecto = new Prospecto();
                                $insProspecto->setIdprospecto($row['idprospecto']);
                                $insProspecto->setDocumento($row['documento']);
                                $insProspecto->setCuenta($row['cuenta']);
                                $insProspecto->setNombre($row['nombre']);
                                $insProspecto->setPais($row['pais']);
                                $insProspecto->setTelefono($row['telefono']);
                                $insProspecto->setEmail($row['email']);
                                $insProspecto->setEspecialidad($row['especialidad']);
                                $insProspecto->setIdFatherProspecto($row['father_idprospecto']);
                                $insBeanPagination->setList($insProspecto->__toString());
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
    protected function eliminar_prospecto_modelo($conexion, $id)
    {
        $sql = $conexion->prepare("DELETE FROM
     `prospecto` WHERE  idprospecto=:ID ");
        $sql->bindValue(":ID", $id, PDO::PARAM_INT);
        return $sql;
    }
    protected function actualizar_prospecto_modelo($conexion, $Prospecto)
    {

        $sql = $conexion->prepare("UPDATE `prospecto`
        SET nombre=:Nombre,cuenta=:Cuenta,documento=:Documento,email=:Email,
        especialidad=:Especialidad,pais=:Pais,telefono=:Telefono,
        father_idprospecto=:FatherProspecto
         WHERE idprospecto=:ID");
        $sql->bindValue(":Nombre", $Prospecto->getNombre(), PDO::PARAM_STR);
        $sql->bindValue(":Cuenta", $Prospecto->getCuenta() == "" || $Prospecto->getCuenta() == null ? null : $Prospecto->getCuenta());
        $sql->bindValue(":Documento", $Prospecto->getDocumento() == "" || $Prospecto->getDocumento() == null ? null : $Prospecto->getDocumento());
        $sql->bindValue(":Pais", $Prospecto->getPais(), PDO::PARAM_STR);
        $sql->bindValue(":Telefono", $Prospecto->getTelefono(), PDO::PARAM_STR);
        $sql->bindValue(":Email", $Prospecto->getEmail(), PDO::PARAM_STR);
        $sql->bindValue(":Especialidad", $Prospecto->getEspecialidad(), PDO::PARAM_STR);
        $sql->bindValue(":FatherProspecto", $Prospecto->getIdFatherProspecto() == "" || $Prospecto->getIdFatherProspecto() == null ? null : $Prospecto->getIdFatherProspecto());
        $sql->bindValue(":ID", $Prospecto->getIdprospecto(), PDO::PARAM_INT);
        return $sql;
    }

}
