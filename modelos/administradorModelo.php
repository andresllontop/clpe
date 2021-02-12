<?php

require_once './core/mainModel.php';

class administradorModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }

    protected function agregar_administrador_modelo($conexion, $administrador)
    {

        $sql = $conexion->prepare("INSERT INTO `administrador`(AdminNombre,AdminApellido,AdminTelefono,Cuenta_Codigo,AdminOcupacion,pais)
         VALUES(?,?,?,?,?,?)");
        $sql->bindValue(1, $administrador->getNombre(), PDO::PARAM_STR);
        $sql->bindValue(2, $administrador->getApellido(), PDO::PARAM_STR);
        $sql->bindValue(3, $administrador->getTelefono(), PDO::PARAM_STR);
        $sql->bindValue(4, $administrador->getCuenta(), PDO::PARAM_STR);
        $sql->bindValue(5, "", PDO::PARAM_STR);
        $sql->bindValue(6, "", PDO::PARAM_STR);
        return $sql;

    }
    protected function eliminar_administrador_modelo($conexion, $codigo)
    {
        $sql = $conexion->prepare("DELETE FROM `administrador` WHERE id=:Codigo");
        $sql->bindValue(":Codigo", $codigo, PDO::PARAM_INT);
        return $sql;
    }
    protected function datos_administrador_modelo($conexion, $tipo, $administrador)
    {

        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(id) AS CONTADOR FROM `administrador` WHERE id=:IDadministrador");
                    $stmt->bindValue(":IDadministrador", $administrador->getIdAdministrador(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `administrador` AS adm INNER JOIN `cuenta` AS cuen ON cuen.CuentaCodigo=adm.Cuenta_Codigo  WHERE adm.id=:IDadministrador and cuen.idcuenta!=1");
                            $stmt->bindValue(":IDadministrador", $administrador->getIdAdministrador(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insCuenta = new Cuenta();
                                $insCuenta->setIdCuenta($row['idcuenta']);
                                $insCuenta->setVoucher($row['voucher']);
                                //$insCuenta->setTipo($row['tipo']);
                                $insCuenta->setFoto($row['foto']);
                                $insCuenta->setCuentaCodigo($row['Cuenta_Codigo']);
                                $insCuenta->setUsuario($row['usuario']);
                                $insCuenta->setEmail($row['email']);
                                $insCuenta->setEstado($row['estado']);
                                $insCuenta->setClave($row['clave']);

                                $insAdministrador = new Administrador();
                                $insAdministrador->setNombre($row['AdminNombre']);
                                $insAdministrador->setApellido($row['AdminApellido']);
                                $insAdministrador->setTelefono($row['AdminTelefono']);
                                $insAdministrador->setOcupacion($row['AdminOcupacion']);
                                $insAdministrador->setPais($row['pais']);
                                $insAdministrador->setCuenta($insCuenta->__toString());
                                $insBeanPagination->setList($insAdministrador->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "perfil":
                    $stmt = $conexion->prepare("SELECT COUNT(idcuenta) AS CONTADOR FROM `cuenta` WHERE  CuentaCodigo=:IDadministrador");
                    $stmt->bindValue(":IDadministrador", $administrador->getCuenta(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `administrador` AS adm INNER JOIN `cuenta` AS cuen ON cuen.CuentaCodigo=adm.Cuenta_Codigo  WHERE cuen.CuentaCodigo=:IDadministrador and cuen.idcuenta!=1");
                            $stmt->bindValue(":IDadministrador", $administrador->getCuenta(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insCuenta = new Cuenta();
                                $insCuenta->setIdCuenta($row['idcuenta']);
                                $insCuenta->setVoucher($row['voucher']);
                                //$insCuenta->setTipo($row['tipo']);
                                $insCuenta->setFoto($row['foto']);
                                $insCuenta->setCuentaCodigo($row['Cuenta_Codigo']);
                                $insCuenta->setUsuario($row['usuario']);
                                $insCuenta->setEmail($row['email']);
                                $insCuenta->setEstado($row['estado']);
                                $insCuenta->setClave(mainModel::decryption($row['clave']));

                                $insAdministrador = new Administrador();
                                $insAdministrador->setIdAdministrador($row['id']);
                                $insAdministrador->setNombre($row['AdminNombre']);
                                $insAdministrador->setApellido($row['AdminApellido']);
                                $insAdministrador->setTelefono($row['AdminTelefono']);
                                $insAdministrador->setOcupacion($row['AdminOcupacion']);
                                $insAdministrador->setPais($row['pais']);
                                $insAdministrador->setCuenta($insCuenta->__toString());
                                $insBeanPagination->setList($insAdministrador->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "cuenta-unico":
                    $stmt = $conexion->prepare("SELECT COUNT(idcuenta) AS CONTADOR FROM `cuenta` WHERE idcuenta=:IDadministrador");
                    $stmt->bindValue(":IDadministrador", $administrador->getIdCuenta(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `administrador`  as admmini inner join `cuenta` as cuent ON cuent.CuentaCodigo=admmini.Cuenta_Codigo WHERE cuent.idcuenta=:IDadministrador and cuent.tipo=2 and cuent.idcuenta!=1 ");
                            $stmt->bindValue(":IDadministrador", $administrador->getIdCuenta(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insAdministrador = new Administrador();
                                $insCuenta = new Cuenta();
                                $insCuenta->setIdCuenta($row['idcuenta']);
                                $insCuenta->setCuentaCodigo($row['CuentaCodigo']);
                                $insCuenta->setUsuario($row['usuario']);
                                // $insCuenta->setClave($row['clave']);
                                $insCuenta->setEmail($row['email']);
                                $insCuenta->setEstado($row['estado']);
                                $insCuenta->setTipo($row['tipo']);
                                $insCuenta->setFoto($row['foto']);
                                $insCuenta->setVoucher($row['voucher']);

                                $insAdministrador->setIdAdministrador($row['id']);
                                $insAdministrador->setNombre($row['AdminNombre']);
                                $insAdministrador->setTelefono($row['AdminTelefono']);
                                $insAdministrador->setApellido($row['AdminApellido']);
                                $insAdministrador->setOcupacion($row['AdminOcupacion']);
                                $insAdministrador->setPais($row['pais']);
                                $insAdministrador->setCuenta($insCuenta->__toString());
                                $insBeanPagination->setList($insAdministrador->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "tipo-cuenta":
                    if ($administrador->getCuenta()->getEstado() > -1) {
                        $stmt = $conexion->prepare("SELECT COUNT(cuen.idcuenta) AS CONTADOR FROM `cuenta` AS cuen WHERE cuen.tipo=:Tipo  AND cuen.estado=:Estado and cuen.idcuenta!=1");
                        $stmt->bindValue(":Tipo", $administrador->getCuenta()->getTipo(), PDO::PARAM_INT);
                        $stmt->bindValue(":Estado", $administrador->getCuenta()->getEstado(), PDO::PARAM_INT);

                    } else {
                        $stmt = $conexion->prepare("SELECT COUNT(cuen.idcuenta) AS CONTADOR FROM `cuenta` AS cuen WHERE cuen.tipo=:Tipo and cuen.idcuenta!=1");
                        $stmt->bindValue(":Tipo", $administrador->getCuenta()->getTipo(), PDO::PARAM_INT);
                    }
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            if ($administrador->getCuenta()->getEstado() > -1) {
                                $stmt = $conexion->prepare("SELECT * FROM `administrador` AS adm INNER JOIN `cuenta` AS cuen ON cuen.CuentaCodigo=adm.Cuenta_Codigo  WHERE cuen.tipo=:Tipo  AND cuen.estado=:Estado and cuen.idcuenta!=1");
                                $stmt->bindValue(":Tipo", $administrador->getCuenta()->getTipo(), PDO::PARAM_INT);
                                $stmt->bindValue(":Estado", $administrador->getCuenta()->getEstado(), PDO::PARAM_INT);
                            } else {
                                $stmt = $conexion->prepare("SELECT * FROM `administrador` AS adm INNER JOIN `cuenta` AS cuen ON cuen.CuentaCodigo=adm.Cuenta_Codigo  WHERE cuen.tipo=:Tipo and cuen.idcuenta!=1");
                                $stmt->bindValue(":Tipo", $administrador->getCuenta()->getTipo(), PDO::PARAM_INT);

                            }
                            $stmt->execute();
                            $datos = $stmt->fetchAll();

                            foreach ($datos as $row) {
                                $insCuenta = new Cuenta();
                                $insCuenta->setIdCuenta($row['idcuenta']);
                                $insCuenta->setVoucher($row['voucher']);
                                //$insCuenta->setTipo($row['tipo']);
                                $insCuenta->setFoto($row['foto']);
                                $insCuenta->setCuentaCodigo($row['Cuenta_Codigo']);
                                $insCuenta->setUsuario($row['usuario']);
                                $insCuenta->setEmail($row['email']);
                                $insCuenta->setEstado($row['estado']);

                                $insAdministrador = new Administrador();
                                $insAdministrador->setNombre($row['AdminNombre']);
                                $insAdministrador->setApellido($row['AdminApellido']);
                                $insAdministrador->setTelefono($row['AdminTelefono']);
                                $insAdministrador->setOcupacion($row['AdminOcupacion']);
                                $insAdministrador->setPais($row['pais']);
                                $insAdministrador->setCuenta($insCuenta->__toString());
                                $insBeanPagination->setList($insAdministrador->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;

                case "conteo":
                    $stmt = $conexion->prepare("SELECT COUNT(idadministrador) AS CONTADOR FROM `administrador`");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `administrador` ");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            $insAdministrador = new Administrador();
                            $insAdministrador->setNombre($row['AdminNombre']);
                            $insAdministrador->setApellido($row['AdminApellido']);
                            $insAdministrador->setOcupacion($row['AdminOcupacion']);
                            $insAdministrador->setPais($row['pais']);
                            $insAdministrador->setCodigoCuenta($row['Cuenta_Codigo']);

                            $insBeanPagination->setList($insAdministrador->__toString());
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

    protected function actualizar_cuenta_estado_modelo($conexion, $Cuenta)
    {
        $sql = $conexion->prepare("UPDATE `cuenta` SET estado=? WHERE CuentaCodigo=? and idcuenta=?");
        $sql->bindValue(1, $Cuenta->getEstado(), PDO::PARAM_INT);
        $sql->bindValue(2, $Cuenta->getCuentaCodigo(), PDO::PARAM_STR);
        $sql->bindValue(3, $Cuenta->getIdCuenta(), PDO::PARAM_INT);
        return $sql;

    }
    protected function actualizar_administrador_modelo($conexion, $administrador)
    {
        $sql = $conexion->prepare("UPDATE `administrador` SET AdminNombre=?, AdminApellido=?, AdminTelefono=?, AdminOcupacion=?, pais=? WHERE id=?");
        $sql->bindValue(1, $administrador->getNombre(), PDO::PARAM_STR);
        $sql->bindValue(2, $administrador->getApellido(), PDO::PARAM_STR);
        $sql->bindValue(3, $administrador->getTelefono(), PDO::PARAM_INT);
        $sql->bindValue(4, $administrador->getOcupacion(), PDO::PARAM_STR);
        $sql->bindValue(5, $administrador->getPais(), PDO::PARAM_STR);
        $sql->bindValue(6, $administrador->getIdAdministrador(), PDO::PARAM_INT);
        return $sql;

    }
    protected function actualizar_cuenta_modelo($conexion, $cuenta)
    {
        $sql = $conexion->prepare("UPDATE `cuenta` SET usuario=:Usuario,email=:Email,clave=:Clave,foto=:Foto, perfil=:Perfil WHERE idcuenta=:ID");
        $sql->bindValue(":Usuario", $cuenta->getUsuario(), PDO::PARAM_STR);
        $sql->bindValue(":Email", $cuenta->getEmail(), PDO::PARAM_STR);
        $sql->bindValue(":Clave", $cuenta->getClave(), PDO::PARAM_STR);
        $sql->bindValue(":Foto", $cuenta->getFoto(), PDO::PARAM_STR);
        $sql->bindValue(":Perfil", $cuenta->getPerfil(), PDO::PARAM_STR);
        $sql->bindValue(":ID", $cuenta->getIdCuenta(), PDO::PARAM_INT);
        return $sql;

    }
    protected function agregar_cuenta_modelo($conexion, $cuenta)
    {
        $sql = $conexion->prepare("INSERT INTO `cuenta` (CuentaCodigo,usuario,clave,email,estado,tipo,voucher,perfil) VALUES(?,?,?,?,?,?,?,?)");
        $sql->bindValue(1, $cuenta->getCuentaCodigo(), PDO::PARAM_STR);
        $sql->bindValue(2, $cuenta->getUsuario(), PDO::PARAM_STR);
        $sql->bindValue(3, $cuenta->getClave(), PDO::PARAM_STR);

        $sql->bindValue(4, $cuenta->getEmail(), PDO::PARAM_STR);
        $sql->bindValue(5, $cuenta->getEstado(), PDO::PARAM_STR);
        $sql->bindValue(6, $cuenta->getTipo(), PDO::PARAM_STR);
        $sql->bindValue(7, "", PDO::PARAM_STR);
        $sql->bindValue(8, $cuenta->getPerfil(), PDO::PARAM_STR);
        return $sql;

    }

    protected function eliminar_cuenta_modelo($conexion, $codigo)
    {
        $sql = $conexion->prepare("DELETE FROM `cuenta` WHERE
         idcuenta=:Codigo");
        $sql->bindValue(":Codigo", $codigo, PDO::PARAM_INT);
        return $sql;
    }
    protected function eliminar_libro_cuenta_modelo($conexion, $codigo)
    {

        $sql = $conexion->prepare("DELETE FROM `librocuenta` WHERE idlibroCuenta=:Codigo");
        $sql->bindValue(":Codigo", $codigo, PDO::PARAM_INT);
        return $sql;
    }

}
