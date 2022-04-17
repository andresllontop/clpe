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

        $sql = $conexion->prepare("INSERT INTO `historial_economico`(nombres,apellidos,telefono,pais,nombre_banco,moneda,comision,precio,tipo,fecha,voucher,codelibro) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
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
        $sql->bindValue(12, $culqi->libro);

        return $sql;

    }

    protected function agregar_cliente_modelo($conexion, $cliente)
    {

        $sql = $conexion->prepare("INSERT INTO `administrador`(AdminNombre,AdminApellido,AdminTelefono,Cuenta_Codigo,AdminOcupacion,pais,codigo_vendedor,tipo_medio,fecha)
         VALUES(?,?,?,?,?,?,?,?,?)");
        $sql->bindValue(1, $cliente->getNombre(), PDO::PARAM_STR);
        $sql->bindValue(2, $cliente->getApellido(), PDO::PARAM_STR);
        $sql->bindValue(3, $cliente->getTelefono(), PDO::PARAM_STR);
        $sql->bindValue(4, $cliente->getCuenta(), PDO::PARAM_STR);
        $sql->bindValue(5, $cliente->getOcupacion(), PDO::PARAM_STR);
        $sql->bindValue(6, $cliente->getPais(), PDO::PARAM_STR);
        $sql->bindValue(7, $cliente->getVendedor(), PDO::PARAM_STR);
        $sql->bindValue(8, $cliente->getTipoMedio(), PDO::PARAM_INT);
        $sql->bindValue(9, $cliente->getFecha());
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
                case "cuenta-libro":
                    $stmt = $conexion->prepare("SELECT COUNT(idlibroCuenta) AS CONTADOR FROM `librocuenta` WHERE cuenta_codigocuenta=:Cuenta and libro_codigoLibro=:Libro");
                    $stmt->bindValue(":Cuenta", $cliente->getCuenta()['cuentaCodigo'], PDO::PARAM_STR);
                    $stmt->bindValue(":Libro", $cliente->getLibro(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `administrador` AS adm INNER JOIN `cuenta` AS cuen ON cuen.CuentaCodigo=adm.Cuenta_Codigo INNER JOIN `librocuenta` AS libcuen ON libcuen.cuenta_codigocuenta=adm.Cuenta_Codigo  WHERE adm.Cuenta_Codigo=:Cuenta and libcuen.libro_codigoLibro=:Libro");
                            $stmt->bindValue(":Cuenta", $cliente->getCuenta()['cuentaCodigo'], PDO::PARAM_STR);
                            $stmt->bindValue(":Libro", $cliente->getLibro(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insCuenta = new Cuenta();
                                $insCuenta->setIdCuenta($row['idcuenta']);
                                $insCuenta->setFoto($row['foto']);
                                $insCuenta->setCuentaCodigo($row['Cuenta_Codigo']);
                                $insCuenta->setUsuario($row['usuario']);
                                $insCuenta->setEmail($row['email']);
                                $insCuenta->setEstado($row['estado']);
                                $insCuenta->setClave($row['clave']);
                                $insLibroCuenta = new LibroCuenta();
                                $insLibroCuenta->setIdlibroCuenta($row['idlibroCuenta']);
                                $insLibroCuenta->setLibro($row['libro_codigoLibro']);
                                $insLibroCuenta->setMonto($row['monto']);
                                $insLibroCuenta->setImagen($row['imagen']);
                                $insLibroCuenta->setCuenta($insCuenta->__toString());
                                $insCliente = new Cliente();
                                $insCliente->setNombre($row['AdminNombre']);
                                $insCliente->setApellido($row['AdminApellido']);
                                $insCliente->setTelefono($row['AdminTelefono']);
                                $insCliente->setOcupacion($row['AdminOcupacion']);
                                $insCliente->setPais($row['pais']);

                                $insCliente->setCuenta($insLibroCuenta->__toString());
                                $insBeanPagination->setList($insCliente->__toString());
                            }
                        }
                    }
                    $stmt->closeCursor();
                    $stmt = null;
                    break;

                case "cuenta-libro-other":
                    $stmt = $conexion->prepare("SELECT COUNT(idlibroCuenta) AS CONTADOR FROM `librocuenta` WHERE cuenta_codigocuenta=:Cuenta");
                    $stmt->bindValue(":Cuenta", $cliente->getCuenta()['cuentaCodigo'], PDO::PARAM_STR);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `administrador` AS adm INNER JOIN `cuenta` AS cuen ON cuen.CuentaCodigo=adm.Cuenta_Codigo INNER JOIN `librocuenta` AS libcuen ON libcuen.cuenta_codigocuenta=adm.Cuenta_Codigo  WHERE adm.Cuenta_Codigo=:Cuenta ");
                            $stmt->bindValue(":Cuenta", $cliente->getCuenta()['cuentaCodigo'], PDO::PARAM_STR);

                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insCuenta = new Cuenta();
                                $insCuenta->setIdCuenta($row['idcuenta']);
                                $insCuenta->setFoto($row['foto']);
                                $insCuenta->setCuentaCodigo($row['Cuenta_Codigo']);
                                $insCuenta->setUsuario($row['usuario']);
                                $insCuenta->setEmail($row['email']);
                                $insCuenta->setEstado($row['estado']);
                                $insCuenta->setClave($row['clave']);
                                $insLibroCuenta = new LibroCuenta();
                                $insLibroCuenta->setIdlibroCuenta($row['idlibroCuenta']);
                                $insLibroCuenta->setLibro($row['libro_codigoLibro']);
                                $insLibroCuenta->setMonto($row['monto']);
                                $insLibroCuenta->setImagen($row['imagen']);
                                $insLibroCuenta->setCuenta($insCuenta->__toString());
                                $insCliente = new Cliente();
                                $insCliente->setNombre($row['AdminNombre']);
                                $insCliente->setApellido($row['AdminApellido']);
                                $insCliente->setTelefono($row['AdminTelefono']);
                                $insCliente->setOcupacion($row['AdminOcupacion']);
                                $insCliente->setPais($row['pais']);

                                $insCliente->setCuenta($insLibroCuenta->__toString());
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
                case "cliente-libro":

                    $stmt = $conexion->prepare("SELECT COUNT(cuen.idlibroCuenta) AS CONTADOR FROM `librocuenta` AS cuen WHERE cuen.libro_codigoLibro=:Codigo");
                    $stmt->bindValue(":Codigo", $cliente->getCuentaCodigo(), PDO::PARAM_STR);

                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT adm.*,cuen.* FROM `administrador` AS adm INNER JOIN `cuenta` AS cuen ON cuen.CuentaCodigo=adm.Cuenta_Codigo INNER JOIN `librocuenta` AS libcuen ON cuen.CuentaCodigo=libcuen.cuenta_codigocuenta WHERE libcuen.libro_codigoLibro=:Codigo and cuen.idcuenta!=1 and cuen.tipo=2");
                            $stmt->bindValue(":Codigo", $cliente->getCuentaCodigo(), PDO::PARAM_STR);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();

                            foreach ($datos as $row) {
                                $insCuenta = new Cuenta();
                                $insCuenta->setIdCuenta($row['idcuenta']);
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

                case "libro":
                    $stmt = $conexion->prepare("SELECT * FROM `libro` WHERE codigo=:Codigo");
                    $stmt->bindValue(":Codigo", $cliente->getCuentaCodigo(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos2 = $stmt->fetchAll();
                    foreach ($datos2 as $row2) {
                        $insBeanPagination->setList(array(
                            "libro" => $row2['nombre'],
                        ));
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
                    $stmt = $conexion->prepare("SELECT COUNT(id) AS CONTADOR FROM `administrador`");
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insBeanPagination->setCountFilter($row['CONTADOR']);
                        if ($row['CONTADOR'] > 0) {
                            $stmt = $conexion->prepare("SELECT * FROM `administrador` ");
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                $insCliente = new Cliente();
                                $insCliente->setNombre($row['AdminNombre']);
                                $insCliente->setApellido($row['AdminApellido']);
                                $insCliente->setTelefono($row['AdminTelefono']);
                                $insCliente->setOcupacion($row['AdminOcupacion']);
                                $insCliente->setPais($row['pais']);
                                $insCliente->setCuenta($row['Cuenta_Codigo']);
                                $insBeanPagination->setList($insCliente->__toString());

                            }
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
    protected function actualizar_libro_cuenta_estado_modelo($conexion, $Cuenta)
    {
        $sql = $conexion->prepare("UPDATE `librocuenta` SET estado=? WHERE cuenta_codigocuenta=? and libro_codigoLibro=?");
        $sql->bindValue(1, $Cuenta->getEstado(), PDO::PARAM_INT);
        $sql->bindValue(2, $Cuenta->getCuentaCodigo(), PDO::PARAM_STR);
        $sql->bindValue(3, $Cuenta->getClave(), PDO::PARAM_STR);
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
        $sql = $conexion->prepare("UPDATE `cuenta` SET usuario=?,email=?,clave=?,foto=?,estado=?,codigo_verificacion=? WHERE CuentaCodigo=? and idcuenta=?");
        $sql->bindValue(1, $cuenta->getUsuario(), PDO::PARAM_STR);
        $sql->bindValue(2, $cuenta->getEmail(), PDO::PARAM_STR);
        $sql->bindValue(3, $cuenta->getClave(), PDO::PARAM_STR);
        $sql->bindValue(4, $cuenta->getFoto(), PDO::PARAM_STR);
        $sql->bindValue(5, $cuenta->getEstado(), PDO::PARAM_INT);
        $sql->bindValue(6, $cuenta->getVerificacion(), PDO::PARAM_INT);
        $sql->bindValue(7, $cuenta->getCuentaCodigo(), PDO::PARAM_STR);
        $sql->bindValue(8, $cuenta->getIdCuenta(), PDO::PARAM_INT);
        return $sql;

    }
    protected function actualizar_datos_cuenta_libro_modelo($conexion, $cuenta)
    {

        $sql = $conexion->prepare("UPDATE `librocuenta` SET imagen=?,monto=?,estado=?,libro_codigoLibro=? WHERE idlibroCuenta=?");
        $sql->bindValue(1, $cuenta->getImagen(), PDO::PARAM_STR);
        $sql->bindValue(2, $cuenta->getMonto(), PDO::PARAM_INT);
        $sql->bindValue(3, $cuenta->getEstado(), PDO::PARAM_STR);
        $sql->bindValue(4, $cuenta->getLibro(), PDO::PARAM_STR);
        $sql->bindValue(5, $cuenta->getIdlibroCuenta(), PDO::PARAM_INT);
        return $sql;

    }
    protected function agregar_cuenta_modelo($conexion, $cuenta)
    {
   

        $sql = $conexion->prepare("INSERT INTO `cuenta` (CuentaCodigo,usuario,clave,email,estado,tipo)
         VALUES(?,?,?,?,?,?)");
        $sql->bindValue(1, $cuenta->getCuentaCodigo(), PDO::PARAM_STR);
        $sql->bindValue(2, $cuenta->getUsuario(), PDO::PARAM_STR);
        $sql->bindValue(3, $cuenta->getClave(), PDO::PARAM_STR);

        $sql->bindValue(4, $cuenta->getEmail(), PDO::PARAM_STR);
        $sql->bindValue(5, $cuenta->getEstado(), PDO::PARAM_STR);
        $sql->bindValue(6, $cuenta->getTipo(), PDO::PARAM_STR);
       
        return $sql;

    }
    protected function agregar_cuenta_culqui_modelo($conexion, $cuenta)
    {

        $sql = $conexion->prepare("INSERT INTO `cuenta` (CuentaCodigo,usuario,clave,email,estado,tipo,codigo_verificacion)
         VALUES(?,?,?,?,?,?,?)");
        $sql->bindValue(1, $cuenta->getCuentaCodigo(), PDO::PARAM_STR);
        $sql->bindValue(2, $cuenta->getUsuario(), PDO::PARAM_STR);
        $sql->bindValue(3, $cuenta->getClave(), PDO::PARAM_STR);

        $sql->bindValue(4, $cuenta->getEmail(), PDO::PARAM_STR);
        $sql->bindValue(5, $cuenta->getEstado(), PDO::PARAM_STR);
        $sql->bindValue(6, $cuenta->getTipo(), PDO::PARAM_STR);
        $sql->bindValue(7, $cuenta->getVerificacion(), PDO::PARAM_STR);

        return $sql;

    }

    protected function agregar_libro_cuenta_modelo($conexion, $cuenta)
    {

        $sql = $conexion->prepare("INSERT INTO `librocuenta`(libro_codigoLibro,cuenta_codigocuenta,imagen,monto,fecha_compra,estado) VALUES(?,?,?,?,?,?)");
        $sql->bindValue(1, $cuenta->getLibro(), PDO::PARAM_STR);
        $sql->bindValue(2, $cuenta->getCuenta(), PDO::PARAM_STR);
        $sql->bindValue(3, $cuenta->getImagen(), PDO::PARAM_STR);
        $sql->bindValue(4, $cuenta->getMonto(), PDO::PARAM_STR);
        $sql->bindValue(5, $cuenta->getFecha(), PDO::PARAM_STR);
        $sql->bindValue(6, $cuenta->getEstado(), PDO::PARAM_STR);

        return $sql;

    }

    protected function eliminar_cuenta_modelo($conexion, $codigo)
    {
        $sql = $conexion->prepare("DELETE FROM `cuenta` WHERE
         idcuenta=:Codigo");
        $sql->bindValue(":Codigo", $codigo, PDO::PARAM_INT);
        return $sql;
    }
    protected function eliminar_libro_cuenta_modelo($conexion, $cuenta, $libro)
    {

        $sql = $conexion->prepare("DELETE FROM `librocuenta` WHERE cuenta_codigocuenta=:Codigo and libro_codigoLibro=:Libro");
        $sql->bindValue(":Codigo", $cuenta, PDO::PARAM_STR);
        $sql->bindValue(":Libro", $libro, PDO::PARAM_STR);
        return $sql;
    }

}
