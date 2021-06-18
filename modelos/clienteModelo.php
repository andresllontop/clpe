<?php

require_once './core/mainModel.php';

class clienteModelo extends mainModel
{
    protected $conexion_db;
    public function __construct()
    {
        $this->conexion_db = parent::__construct();
    }

    protected function agregar_historial_economico_modelo($conexion, $cliente, $culqi, $imagen = null)
    {

        $sql = $conexion->prepare("INSERT INTO `historial_economico`(nombres,apellidos,telefono,pais,nombre_banco,moneda,comision,precio,tipo,fecha,voucher) VALUES(?,?,?,?,?,?,?,?,?,?,?)");
        $sql->bindValue(1, $cliente->getNombre(), PDO::PARAM_STR);
        $sql->bindValue(2, $cliente->getApellido(), PDO::PARAM_STR);
        $sql->bindValue(3, $cliente->getTelefono(), PDO::PARAM_STR);
        $sql->bindValue(4, $cliente->getPais(), PDO::PARAM_STR);
        $sql->bindValue(5, $culqi->nombre_banco, PDO::PARAM_STR);
        $sql->bindValue(6, $culqi->moneda);
        $sql->bindValue(7, $culqi->comision);
        $sql->bindValue(8, $culqi->precio);
        $sql->bindValue(9, $culqi->tipo, PDO::PARAM_INT);
        $sql->bindValue(10, $culqi->fecha);
        $sql->bindValue(11, $imagen);

        return $sql;

    }

    protected function agregar_cliente_modelo($conexion, $cliente)
    {

        $sql = $conexion->prepare("INSERT INTO `administrador`(AdminNombre,AdminApellido,AdminTelefono,Cuenta_Codigo,AdminOcupacion,pais,codigo_vendedor,tipo_medio)
         VALUES(?,?,?,?,?,?,?,?)");
        $sql->bindValue(1, $cliente->getNombre(), PDO::PARAM_STR);
        $sql->bindValue(2, $cliente->getApellido(), PDO::PARAM_STR);
        $sql->bindValue(3, $cliente->getTelefono(), PDO::PARAM_STR);
        $sql->bindValue(4, $cliente->getCuenta(), PDO::PARAM_STR);
        $sql->bindValue(5, $cliente->getOcupacion(), PDO::PARAM_STR);
        $sql->bindValue(6, $cliente->getPais(), PDO::PARAM_STR);
        $sql->bindValue(7, $cliente->getVendedor(), PDO::PARAM_STR);
        $sql->bindValue(8, $cliente->getTipoMedio(), PDO::PARAM_INT);
        return $sql;

    }
    protected function eliminar_cliente_modelo($conexion, $codigo)
    {
        $sql = $conexion->prepare("DELETE FROM `administrador` WHERE id=:Codigo");
        $sql->bindValue(":Codigo", $codigo, PDO::PARAM_INT);
        return $sql;
    }
    protected function datos_cliente_modelo($conexion, $tipo, $cliente)
    {

        $insBeanPagination = new BeanPagination();
        try {
            switch ($tipo) {
                case "unico":
                    $stmt = $conexion->prepare("SELECT COUNT(id) AS CONTADOR FROM `administrador` WHERE id=:IDadministrador");
                    $stmt->bindValue(":IDadministrador", $cliente->getIdCliente(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `administrador` AS adm INNER JOIN `cuenta` AS cuen ON cuen.CuentaCodigo=adm.Cuenta_Codigo  WHERE adm.id=:IDadministrador and cuen.idcuenta!=1");
                            $stmt->bindValue(":IDadministrador", $cliente->getIdCliente(), PDO::PARAM_INT);
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
                case "cuenta":
                    $stmt = $conexion->prepare("SELECT COUNT(id) AS CONTADOR FROM `administrador` WHERE Cuenta_Codigo=:Cuenta");
                    $stmt->bindValue(":Cuenta", $cliente->getCuentaCodigo(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `administrador` AS adm INNER JOIN `cuenta` AS cuen ON cuen.CuentaCodigo=adm.Cuenta_Codigo  WHERE adm.Cuenta_Codigo=:Cuenta");
                            $stmt->bindValue(":Cuenta", $cliente->getCuentaCodigo(), PDO::PARAM_STR);
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
                case "perfil":
                    $stmt = $conexion->prepare("SELECT COUNT(idcuenta) AS CONTADOR FROM `cuenta` WHERE  CuentaCodigo=:IDadministrador");
                    $stmt->bindValue(":IDadministrador", $cliente->getCuenta(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `administrador` AS adm INNER JOIN `cuenta` AS cuen ON cuen.CuentaCodigo=adm.Cuenta_Codigo  WHERE cuen.CuentaCodigo=:IDadministrador and cuen.idcuenta!=1");
                            $stmt->bindValue(":IDadministrador", $cliente->getCuenta(), PDO::PARAM_STR);
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
                case "cuenta-unico":
                    $stmt = $conexion->prepare("SELECT COUNT(idcuenta) AS CONTADOR FROM `cuenta` WHERE idcuenta=:IDadministrador");
                    $stmt->bindValue(":IDadministrador", $cliente->getIdCuenta(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `administrador`  as admmini inner join `cuenta` as cuent ON cuent.CuentaCodigo=admmini.Cuenta_Codigo WHERE cuent.idcuenta=:IDadministrador and cuent.tipo=2 and cuent.idcuenta!=1 ");
                            $stmt->bindValue(":IDadministrador", $cliente->getIdCuenta(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insCliente = new Cliente();
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

                                $insCliente->setIdCliente($row['id']);
                                $insCliente->setNombre($row['AdminNombre']);
                                $insCliente->setTelefono($row['AdminTelefono']);
                                $insCliente->setApellido($row['AdminApellido']);
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
                case "tipo-cuenta":
                    if ($cliente->getCuenta()->getEstado() > -1) {
                        $stmt = $conexion->prepare("SELECT COUNT(cuen.idcuenta) AS CONTADOR FROM `cuenta` AS cuen WHERE cuen.tipo=:Tipo  AND cuen.estado=:Estado and cuen.idcuenta!=1");
                        $stmt->bindValue(":Tipo", $cliente->getCuenta()->getTipo(), PDO::PARAM_INT);
                        $stmt->bindValue(":Estado", $cliente->getCuenta()->getEstado(), PDO::PARAM_INT);

                    } else {
                        $stmt = $conexion->prepare("SELECT COUNT(cuen.idcuenta) AS CONTADOR FROM `cuenta` AS cuen WHERE cuen.tipo=:Tipo and cuen.idcuenta!=1");
                        $stmt->bindValue(":Tipo", $cliente->getCuenta()->getTipo(), PDO::PARAM_INT);
                    }
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            if ($cliente->getCuenta()->getEstado() > -1) {
                                $stmt = $conexion->prepare("SELECT * FROM `administrador` AS adm INNER JOIN `cuenta` AS cuen ON cuen.CuentaCodigo=adm.Cuenta_Codigo  WHERE cuen.tipo=:Tipo  AND cuen.estado=:Estado and cuen.idcuenta!=1");
                                $stmt->bindValue(":Tipo", $cliente->getCuenta()->getTipo(), PDO::PARAM_INT);
                                $stmt->bindValue(":Estado", $cliente->getCuenta()->getEstado(), PDO::PARAM_INT);
                            } else {
                                $stmt = $conexion->prepare("SELECT * FROM `administrador` AS adm INNER JOIN `cuenta` AS cuen ON cuen.CuentaCodigo=adm.Cuenta_Codigo  WHERE cuen.tipo=:Tipo and cuen.idcuenta!=1");
                                $stmt->bindValue(":Tipo", $cliente->getCuenta()->getTipo(), PDO::PARAM_INT);

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

    protected function actualizar_cuenta_estado_modelo($conexion, $Cuenta)
    {
        $sql = $conexion->prepare("UPDATE `cuenta` SET estado=? WHERE CuentaCodigo=? and idcuenta=?");
        $sql->bindValue(1, $Cuenta->getEstado(), PDO::PARAM_INT);
        $sql->bindValue(2, $Cuenta->getCuentaCodigo(), PDO::PARAM_STR);
        $sql->bindValue(3, $Cuenta->getIdCuenta(), PDO::PARAM_INT);
        return $sql;

    }
    protected function actualizar_cliente_modelo($conexion, $cliente)
    {
        $sql = $conexion->prepare("UPDATE `administrador` SET AdminNombre=?, AdminApellido=?, AdminTelefono=?, AdminOcupacion=?, pais=? WHERE id=?");
        $sql->bindValue(1, $cliente->getNombre(), PDO::PARAM_STR);
        $sql->bindValue(2, $cliente->getApellido(), PDO::PARAM_STR);
        $sql->bindValue(3, $cliente->getTelefono(), PDO::PARAM_INT);
        $sql->bindValue(4, $cliente->getOcupacion(), PDO::PARAM_STR);
        $sql->bindValue(5, $cliente->getPais(), PDO::PARAM_STR);
        $sql->bindValue(6, $cliente->getIdCliente(), PDO::PARAM_INT);
        return $sql;

    }
    protected function actualizar_cliente_estado_modelo($conexion, $cliente)
    {
        $sql = $conexion->prepare("UPDATE `administrador` SET Estado=? WHERE id=?");
        $sql->bindValue(1, $cliente->getEstado(), PDO::PARAM_STR);
        $sql->bindValue(2, $cliente->getIdCliente(), PDO::PARAM_INT);
        return $sql;

    }
    protected function actualizar_cuenta_modelo($conexion, $cuenta)
    {

        $sql = $conexion->prepare("UPDATE `cuenta` SET usuario=?,email=?,clave=?,foto=? WHERE CuentaCodigo=? and idcuenta=?");
        $sql->bindValue(1, $cuenta->getUsuario(), PDO::PARAM_STR);
        $sql->bindValue(2, $cuenta->getEmail(), PDO::PARAM_STR);
        $sql->bindValue(3, $cuenta->getClave(), PDO::PARAM_STR);
        $sql->bindValue(4, $cuenta->getFoto(), PDO::PARAM_STR);
        $sql->bindValue(5, $cuenta->getCuentaCodigo(), PDO::PARAM_STR);
        $sql->bindValue(6, $cuenta->getIdCuenta(), PDO::PARAM_INT);
        return $sql;

    }
    protected function actualizar_datos_cuenta_modelo($conexion, $cuenta)
    {
        $sql = $conexion->prepare("UPDATE `cuenta` SET usuario=?,email=?,clave=?,foto=?,voucher=?,estado=?,precio_curso=?,codigo_verificacion=? WHERE CuentaCodigo=? and idcuenta=?");
        $sql->bindValue(1, $cuenta->getUsuario(), PDO::PARAM_STR);
        $sql->bindValue(2, $cuenta->getEmail(), PDO::PARAM_STR);
        $sql->bindValue(3, $cuenta->getClave(), PDO::PARAM_STR);
        $sql->bindValue(4, $cuenta->getFoto(), PDO::PARAM_STR);
        $sql->bindValue(5, $cuenta->getVoucher(), PDO::PARAM_STR);
        $sql->bindValue(6, $cuenta->getEstado(), PDO::PARAM_INT);
        $sql->bindValue(7, $cuenta->getPrecio(), PDO::PARAM_STR);
        $sql->bindValue(8, $cuenta->getVerificacion(), PDO::PARAM_INT);
        $sql->bindValue(9, $cuenta->getCuentaCodigo(), PDO::PARAM_STR);
        $sql->bindValue(10, $cuenta->getIdCuenta(), PDO::PARAM_INT);
        return $sql;

    }
    protected function agregar_cuenta_modelo($conexion, $cuenta)
    {

        $sql = $conexion->prepare("INSERT INTO `cuenta` (CuentaCodigo,usuario,clave,email,estado,tipo,precio_curso,voucher)
         VALUES(?,?,?,?,?,?,?,?)");
        $sql->bindValue(1, $cuenta->getCuentaCodigo(), PDO::PARAM_STR);
        $sql->bindValue(2, $cuenta->getUsuario(), PDO::PARAM_STR);
        $sql->bindValue(3, $cuenta->getClave(), PDO::PARAM_STR);

        $sql->bindValue(4, $cuenta->getEmail(), PDO::PARAM_STR);
        $sql->bindValue(5, $cuenta->getEstado(), PDO::PARAM_STR);
        $sql->bindValue(6, $cuenta->getTipo(), PDO::PARAM_STR);
        $sql->bindValue(7, $cuenta->getPrecio(), PDO::PARAM_STR);
        $sql->bindValue(8, $cuenta->getVoucher(), PDO::PARAM_STR);

        return $sql;

    }
    protected function agregar_cuenta_culqui_modelo($conexion, $cuenta)
    {

        $sql = $conexion->prepare("INSERT INTO `cuenta` (CuentaCodigo,usuario,clave,email,estado,tipo,precio_curso,voucher,codigo_verificacion)
         VALUES(?,?,?,?,?,?,?,?,?)");
        $sql->bindValue(1, $cuenta->getCuentaCodigo(), PDO::PARAM_STR);
        $sql->bindValue(2, $cuenta->getUsuario(), PDO::PARAM_STR);
        $sql->bindValue(3, $cuenta->getClave(), PDO::PARAM_STR);

        $sql->bindValue(4, $cuenta->getEmail(), PDO::PARAM_STR);
        $sql->bindValue(5, $cuenta->getEstado(), PDO::PARAM_STR);
        $sql->bindValue(6, $cuenta->getTipo(), PDO::PARAM_STR);
        $sql->bindValue(7, $cuenta->getPrecio(), PDO::PARAM_STR);
        $sql->bindValue(8, $cuenta->getVoucher(), PDO::PARAM_STR);
        $sql->bindValue(9, $cuenta->getVerificacion(), PDO::PARAM_STR);

        return $sql;

    }

    protected function agregar_libro_cuenta_modelo($conexion, $cuenta)
    {

        $sql = $conexion->prepare("INSERT INTO `librocuenta`(libro_codigoLibro,cuenta_codigocuenta) VALUES(?,?)");
        $sql->bindValue(1, $cuenta->getLibro(), PDO::PARAM_STR);
        $sql->bindValue(2, $cuenta->getCuenta(), PDO::PARAM_STR);

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
