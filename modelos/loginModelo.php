<?php

require_once './core/mainModel.php';

class loginModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }

    protected function agregar_login_modelo($conexion, $datos)
    {
        $sql = $conexion->prepare("INSERT INTO `cuenta`
        (AdminDNI,AdminNombre,AdminApellido,AdminTelefono,CuentaCodigo)
         VALUES(:DNI,:Nombre,:Apellido,:Telefono,:Codigo)");
        $sql->bindValue(":DNI", $datos['Dni']);
        $sql->bindValue(":Nombre", $datos['Nombre']);
        $sql->bindValue(":Apellido", $datos['Apellido']);
        $sql->bindValue(":Telefono", $datos['Telefono']);
        $sql->bindValue(":Codigo", $datos['Codigo']);

        $sql->execute();
        $resultado = $sql->rowCount();
        $sql->closeCursor(); //cerrar tabla virtual
        $this->conexion_db = null; //cerrar la conexion
        return $resultado;

    }
    protected function eliminar_login_modelo($conexion, $codigo)
    {
        $sql = $conexion->prepare("DELETE FROM `cuenta`
        WHERE  CuentaCodigo=:Codigo ");
        $sql->bindValue(":Codigo", $codigo);
        $sql->execute();
        $resultado = $sql->rowCount();
        $sql->closeCursor(); //cerrar tabla virtual
        return $resultado;
        $this->conexion_db = null; //cerrar la conexion
    }
    protected function datos_login_modelo($conexion, $Cuenta)
    {
        //   print_r($Cuenta);
        $insBeanPagination = new BeanPagination();
        $resultado = 0;
        try {
            $stmt = $conexion->prepare("SELECT COUNT(idcuenta) AS CONTADOR FROM `cuenta` WHERE email=:Email and clave=:Clave and estado=1 ");
            $stmt->bindValue(":Email", $Cuenta->getEmail(), PDO::PARAM_STR);
            $stmt->bindValue(":Clave", $Cuenta->getClave(), PDO::PARAM_STR);
            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
            }
            if ($insBeanPagination->getCountFilter() > 0) {
                $stmt = $conexion->prepare("SELECT * FROM `cuenta` WHERE email=:Email and clave=:Clave and estado=1");
                $stmt->bindValue(":Email", $Cuenta->getEmail(), PDO::PARAM_STR);
                $stmt->bindValue(":Clave", $Cuenta->getClave(), PDO::PARAM_STR);

                $stmt->execute();
                $datos = $stmt->fetchAll();
                foreach ($datos as $row) {

                    $insCuenta = new Cuenta();
                    $insCuenta->setEmail($row['email']);
                    $insCuenta->setIdCuenta($row['idcuenta']);
                    $insCuenta->setTipo($row['tipo']);
                    $insCuenta->setCuentaCodigo($row['CuentaCodigo']);
                    $insCuenta->setUsuario($row['usuario']);
                    $insCuenta->setEstado($row['estado']);
                    $insCuenta->setVerificacion($row['codigo_verificacion']);
                    $insCuenta->setFoto($row['foto']);
                    $insCuenta->setPerfil($row['perfil']);
                    $insBeanPagination->setList($insCuenta->__toString());
                }
            }

            $stmt->closeCursor();
            $stmt = null;

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        }
        return $insBeanPagination->__toString();

    }
    protected function datos_login_tipo_modelo($conexion, $tipo, $Cuenta)
    {

        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "email":
                    $stmt = $conexion->prepare("SELECT COUNT(idcuenta) AS CONTADOR FROM `cuenta` WHERE email=:Email and estado=1 ");
                    $stmt->bindValue(":Email", $Cuenta->getEmail(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `administrador` AS adm INNER JOIN `cuenta` AS cuen ON cuen.CuentaCodigo=adm.Cuenta_Codigo  WHERE cuen.email=:Email and cuen.estado=1");
                            $stmt->bindValue(":Email", $Cuenta->getEmail(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insCuenta = new Cuenta();
                                $insCuenta->setIdCuenta($row['idcuenta']);
                                $insCuenta->setCuentaCodigo($row['Cuenta_Codigo']);
                                $insCuenta->setUsuario($row['usuario']);
                                $insCuenta->setEmail($row['email']);
                                $insCuenta->setTipo($row['tipo']);

                                $insCliente = new Cliente();
                                $insCliente->setNombre($row['AdminNombre']);
                                $insCliente->setApellido($row['AdminApellido']);
                                $insCliente->setTelefono($row['AdminTelefono']);
                                $insCliente->setOcupacion($row['AdminOcupacion']);
                                $insCliente->setPais($row['pais']);
                                $insCliente->setCuenta($insCuenta->__toString());
                                $insBeanPagination->setList($insCliente->__toString());
                            }
                        }
                    }

                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "obtener":
                    $stmt = $conexion->prepare("SELECT COUNT(idcuenta) AS CONTADOR FROM `cuenta` WHERE CuentaCodigo=:Codigo and estado=1 and (codigo_verificacion!='' or codigo_verificacion!=null )");
                    $stmt->bindValue(":Codigo", $Cuenta->getCuentaCodigo(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);

                    }

                    $stmt->closeCursor();
                    $stmt = null;
                    break;
                case "empresa":
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
                                $insEmpresa->setEmail($row['EmpresaEmail']);
                                $insEmpresa->setDescripcion($row['descripcion']);
                                $insEmpresa->setDireccion($row['EmpresaDireccion']);
                                $insEmpresa->setLogo($row['EmpresaLogo']);
                                $insEmpresa->setEnlace($row['Enlace']);
                                $insEmpresa->setTelefonoSegundo($row['EmpresaTelefono2']);
                                $insEmpresa->setFacebook($row['facebook']);
                                $insEmpresa->setPrecio($row['precio']);

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

}
