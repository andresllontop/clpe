<?php

require_once './core/mainModel.php';

class empresaModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }

    protected function agregar_empresa_modelo($conexion, $datos)
    {
        return $sql;
    }
    protected function datos_empresa_modelo($conexion, $tipo, $empresa)
    {

        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(idempresa) AS CONTADOR FROM `empresa` WHERE idempresa=:IDempresa");
                    $stmt->bindValue(":IDempresa", $empresa->getIdEmpresa(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `empresa`
                            WHERE idempresa=:IDempresa");
                            $stmt->bindValue(":IDempresa", $empresa->getIdEmpresa(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {

                                $insEmpresa = new Empresa();
                                $insEmpresa->setIdEmpresa($row['idempresa']);
                                $insEmpresa->setTelefono($row['EmpresaTelefono']);
                                $insEmpresa->setVision($row['vision']);
                                $insEmpresa->setYoutube($row['youtube']);
                                $insEmpresa->setNombre($row['EmpresaNombre']);
                                $insEmpresa->setMision($row['mision']);
                                $insEmpresa->setInstagram($row['instagram']);
                                $insEmpresa->setEmail($row['EmpresaEmail']);
                                $insEmpresa->setDescripcion($row['descripcion']);
                                $insEmpresa->setDireccion($row['EmpresaDireccion']);
                                $insEmpresa->setLogo($row['EmpresaLogo']);
                                $insEmpresa->setEnlace($row['Enlace']);
                                $insEmpresa->setTelefonoSegundo($row['EmpresaTelefono2']);
                                $insEmpresa->setFacebook($row['facebook']);
                                $insEmpresa->setPrecio($row['precio']);
                                $insEmpresa->setFrase($row['EmpresaFrase']);

                                $insBeanPagination->setList($insEmpresa->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "conteo":
                    $stmt = $conexion->prepare("SELECT COUNT(idempresa) AS CONTADOR FROM `empresa`");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `empresa`");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {

                                $insEmpresa = new Empresa();
                                $insEmpresa->setIdEmpresa($row['idempresa']);
                                $insEmpresa->setTelefono($row['EmpresaTelefono']);
                                $insEmpresa->setVision($row['vision']);
                                $insEmpresa->setYoutube($row['youtube']);
                                $insEmpresa->setNombre($row['EmpresaNombre']);
                                $insEmpresa->setMision($row['mision']);
                                $insEmpresa->setInstagram($row['instagram']);
                                $insEmpresa->setEmail($row['EmpresaEmail']);
                                $insEmpresa->setDescripcion($row['descripcion']);
                                $insEmpresa->setDireccion($row['EmpresaDireccion']);
                                $insEmpresa->setLogo($row['EmpresaLogo']);
                                $insEmpresa->setEnlace($row['Enlace']);
                                $insEmpresa->setTelefonoSegundo($row['EmpresaTelefono2']);
                                $insEmpresa->setFacebook($row['facebook']);
                                $insEmpresa->setPrecio($row['precio']);
                                $insEmpresa->setFrase($row['EmpresaFrase']);
                                $insBeanPagination->setList($insEmpresa->__toString());
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
    protected function actualizar_empresa_modelo($conexion, $Empresa)
    {

        $sql = $conexion->prepare("UPDATE `empresa`
        SET EmpresaNombre=:EmNombre,EmpresaTelefono=:EmTelefono,
        EmpresaEmail=:EmEmail,EmpresaTelefono2=:EmTelefono2,
        EmpresaDireccion=:EmDireccion,descripcion=:EmDescripcion,youtube=:EmYoutube,precio=:Precio,mision=:EmMision,vision=:EmVision,instagram=:Instagram,
        Facebook=:EmFacebook,Enlace=:EmUrl,EmpresaLogo=:EmLogo,EmpresaFrase=:frase
        WHERE idempresa=:ID");
        $sql->bindValue(":EmNombre", $Empresa->getNombre(), PDO::PARAM_STR);
        $sql->bindValue(":Instagram", $Empresa->getInstagram(), PDO::PARAM_STR);
        $sql->bindValue(":EmTelefono", $Empresa->getTelefono(), PDO::PARAM_INT);
        $sql->bindValue(":EmTelefono2", $Empresa->getTelefonoSegundo(), PDO::PARAM_INT);
        $sql->bindValue(":EmEmail", $Empresa->getEmail(), PDO::PARAM_STR);
        $sql->bindValue(":EmDireccion", $Empresa->getDireccion(), PDO::PARAM_STR);
        $sql->bindValue(":EmDescripcion", $Empresa->getDescripcion(), PDO::PARAM_STR);
        $sql->bindValue(":EmFacebook", $Empresa->getFacebook(), PDO::PARAM_STR);
        $sql->bindValue(":Precio", $Empresa->getPrecio(), PDO::PARAM_STR);
        $sql->bindValue(":EmUrl", $Empresa->getEnlace(), PDO::PARAM_STR);
        $sql->bindValue(":EmLogo", $Empresa->getLogo(), PDO::PARAM_STR);
        $sql->bindValue(":EmYoutube", $Empresa->getYoutube(), PDO::PARAM_STR);
        $sql->bindValue(":EmMision", $Empresa->getMision(), PDO::PARAM_STR);
        $sql->bindValue(":EmVision", $Empresa->getVision(), PDO::PARAM_STR);
        $sql->bindValue(":frase", $Empresa->getFrase(), PDO::PARAM_STR);
        $sql->bindValue(":ID", $Empresa->getIdEmpresa(), PDO::PARAM_INT);
        return $sql;
    }
    protected function actualizar_mision_empresa_modelo($conexion, $Empresa)
    {

        $sql = $conexion->prepare("UPDATE `empresa` SET mision=:EmMision  WHERE idempresa=:ID");
        $sql->bindValue(":EmMision", $Empresa->getMision(), PDO::PARAM_STR);
        $sql->bindValue(":ID", $Empresa->getIdEmpresa(), PDO::PARAM_INT);
        return $sql;
    }

}
