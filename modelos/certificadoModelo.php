<?php

require_once './core/mainModel.php';

class certificadoModelo extends mainModel
{

    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }
    protected function agregar_certificado_modelo($conexion, $certificado)
    {
        $sql = $conexion->prepare("INSERT INTO `certificado` (nombre,indicador,cuenta,fecha,estado)  VALUES(:Nombre,:Indicador,:Cuenta,:Fecha,1)");
        $sql->bindValue(":Nombre", $certificado->getNombre(), PDO::PARAM_STR);
        $sql->bindValue(":Fecha", $certificado->getFecha());
        $sql->bindValue(":Indicador", $certificado->getIndicador(), PDO::PARAM_INT);
        $sql->bindValue(":Cuenta", $certificado->getCuenta(), PDO::PARAM_STR);
        return $sql;
    }
    protected function datos_certificado_modelo($conexion, $tipo, $certificado)
    {
        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(idcertificado) AS CONTADOR FROM `certificado` WHERE idcertificado=:IDcertificado");
                    $stmt->bindValue(":IDcertificado", $certificado->getIdcertificado(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `certificado` WHERE idcertificado=:IDcertificado");
                            $stmt->bindValue(":IDcertificado", $certificado->getIdcertificado(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insCertificado = new Certificado();
                                $insCertificado->setIdCertificado($row['idcertificado']);
                                $insCertificado->setIndicador($row['indicador']);
                                $insCertificado->setNombre($row['nombre']);
                                $insBeanPagination->setList($insCertificado->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "unico-alumno":
                    $stmt = $conexion->prepare("SELECT COUNT(idcertificado) AS CONTADOR FROM `certificado` WHERE cuenta=:Cuenta");
                    $stmt->bindValue(":Cuenta", $certificado->getCuenta(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `certificado` WHERE cuenta=:Cuenta");
                            $stmt->bindValue(":Cuenta", $certificado->getCuenta(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insCertificado = new Certificado();
                                $insCertificado->setIdCertificado($row['idcertificado']);
                                $insCertificado->setIndicador($row['indicador']);
                                $insCertificado->setNombre($row['nombre']);
                                $insCertificado->setFecha($row['fecha']);
                                $insBeanPagination->setList($insCertificado->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":
                    $stmt = $conexion->prepare("SELECT COUNT(idcertificado) AS CONTADOR FROM `certificado`");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `certificado` ");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insBeanPagination->setCountFilter($row['CONTADOR']);
                                if ($row['CONTADOR'] > 0) {
                                    $stmt = $conexion->prepare("SELECT * FROM `certificado` WHERE idcertificado=:IDcertificado");
                                    $stmt->bindValue(":IDcertificado", $certificado->getIdcertificado(), PDO::PARAM_INT);
                                    $stmt->execute();
                                    $datos = $stmt->fetchAll();
                                    foreach ($datos as $row) {
                                        $insCertificado->setIdCertificado($row['idcertificado']);
                                        $insCertificado->setIndicador($row['indicador']);
                                        $insCertificado->setNombre($row['nombre']);
                                        $insBeanPagination->setList($insCertificado->__toString());
                                    }
                                }
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                default:
                    break;
            }
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        }
        return $insBeanPagination->__toString();
    }
    protected function eliminar_certificado_modelo($conexion, $id)
    {
        $sql = $conexion->prepare("DELETE FROM `certificado` WHERE idcertificado=:IDcertificado");
        $sql->bindValue(":IDcertificado", $id, PDO::PARAM_INT);

        return $sql;
    }
    protected function actualizar_certificado_modelo($conexion, $certificado)
    {
        $sql = $conexion->prepare("UPDATE `certificado` SET nombre=:Nombre, indicador=:Indicador WHERE cuenta=:Cuenta and idcertificado=:ID");
        $sql->bindValue(":Nombre", $certificado->getNombre(), PDO::PARAM_STR);
        $sql->bindValue(":Indicador", $certificado->getIndicador(), PDO::PARAM_INT);
        $sql->bindValue(":Cuenta", $certificado->getCuenta(), PDO::PARAM_STR);
        $sql->bindValue(":ID", $certificado->getIdcertificado(), PDO::PARAM_INT);

        return $sql;
    }

}
