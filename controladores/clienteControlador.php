<?php

require_once './modelos/clienteModelo.php';
require_once './classes/other/beanCrud.php';
require_once './classes/other/beanPagination.php';
require_once './classes/principal/librocuenta.php';
class clienteControlador extends clienteModelo
{

    public function agregar_cliente_controlador($tipo, $Cliente)
    {

        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Cliente->setTelefono(mainModel::limpiar_cadena($Cliente->getTelefono()));
            $Cliente->setOcupacion(mainModel::limpiar_cadena($Cliente->getOcupacion()));
            $Cliente->setNombre(mainModel::limpiar_cadena($Cliente->getNombre()));
            $Cliente->setApellido(mainModel::limpiar_cadena($Cliente->getApellido()));
            $Cliente->setPais(mainModel::limpiar_cadena($Cliente->getPais()));
            $Cliente->setVendedor(mainModel::limpiar_cadena($Cliente->getVendedor()));
            $Cliente->setTipoMedio(mainModel::limpiar_cadena($Cliente->getTipoMedio()));
            $insCuenta = new Cuenta();
            $insCuenta->setPrecio(0);
            $insCuenta->setUsuario(mainModel::limpiar_cadena($Cliente->getCuenta()->usuario));
            $insCuenta->setClave(mainModel::encryption(mainModel::limpiar_cadena($Cliente->getCuenta()->clave)));
            $insCuenta->setEmail(mainModel::limpiar_cadena($Cliente->getCuenta()->email));
            $insCuenta->setEstado(0);
            $insCuenta->setTipo(2);

            $stmt = $this->conexion_db->prepare("SELECT COUNT(idcuenta) AS CONTADOR FROM `cuenta` WHERE email=?");
            $stmt->bindValue(1, $insCuenta->getEmail(), PDO::PARAM_STR);
            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                if ($row['CONTADOR'] > 0) {
                    $insBeanCrud->setMessageServer("Se encuentra Registrado el Usuario, cambie de Email");
                } else {
                    $stmt = $this->conexion_db->prepare("SELECT COUNT(id) AS CONTADOR FROM `administrador` WHERE AdminNombre=? and AdminApellido=?");
                    $stmt->bindValue(1, $Cliente->getNombre(), PDO::PARAM_STR);
                    $stmt->bindValue(2, $Cliente->getApellido(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos2 = $stmt->fetchAll();
                    foreach ($datos2 as $row2) {
                        if ($row2['CONTADOR'] > 0) {
                            $insBeanCrud->setMessageServer("Ya se encuentra registrado, cambie de Nombres y Apellidos");
                        } else {
                            $stmt = $this->conexion_db->query("SELECT MAX(idcuenta) AS MAXIMO FROM `cuenta`");
                            $datos3 = $stmt->fetchAll();
                            foreach ($datos3 as $row3) {
                                if ($row3['MAXIMO'] > 0) {
                                    $cuentacodigo = mainModel::generar_codigo_aleatorio("AC", 7, $row3['MAXIMO'] + 1);
                                    $insCuenta->setCuentaCodigo($cuentacodigo);

                                    $insCuenta->setVoucher("");
                                    $stmt = clienteModelo::agregar_cuenta_modelo($this->conexion_db, $insCuenta);
                                    if ($stmt->execute()) {
                                        $Cliente->setCuenta($cuentacodigo);
                                        $stmt = clienteModelo::agregar_cliente_modelo($this->conexion_db, $Cliente);
                                        if ($stmt->execute()) {
                                            $stmt = $this->conexion_db->query("SELECT COUNT(idlibro) AS CONTADOR FROM `libro`");
                                            $datos4 = $stmt->fetchAll();
                                            foreach ($datos4 as $row4) {
                                                if ($row4['CONTADOR'] > 0) {
                                                    $stmt = $this->conexion_db->query("SELECT MIN(codigo) AS MINIMO FROM `libro`");
                                                    $datos5 = $stmt->fetchAll();
                                                    foreach ($datos5 as $row5) {
                                                        $insLibroCuenta = new LibroCuenta();
                                                        $insLibroCuenta->setCuenta($cuentacodigo);
                                                        $insLibroCuenta->setLibro($row5['MINIMO']);
                                                        $stmt = clienteModelo::agregar_libro_cuenta_modelo($this->conexion_db, $insLibroCuenta);
                                                        if ($stmt->execute()) {
                                                            $this->conexion_db->commit();
                                                            $insBeanCrud->setMessageServer("ok");
                                                            if ($tipo == 1) {
                                                                $insBeanCrud->setBeanPagination(self::paginador_cliente_controlador($this->conexion_db, 0, 5, 0, ""));
                                                            }

                                                        } else {
                                                            $insBeanCrud->setMessageServer("No hemos podido registrar los datos");
                                                        }

                                                    }

                                                } else {
                                                    $insBeanCrud->setMessageServer("No se encuentra libro para registrar al usuario");
                                                }
                                            }

                                        } else {
                                            $insBeanCrud->setMessageServer("No hemos podido registrar los datos");
                                        }
                                    } else {
                                        $insBeanCrud->setMessageServer("No hemos podido registrar la cuenta");
                                    }

                                }

                            }

                        }
                    }
                }
            }

            /*
        if (isset($_FILES['txtImagenVoucher'])) {
        $original = $_FILES['txtImagenVoucher'];
        $nombre = $original['name'];
        $stmt = $this->conexion_db->prepare("SELECT COUNT(idcuenta) AS CONTADOR FROM `cuenta` WHERE email=?");
        $stmt->bindValue(1, $insCuenta->getEmail(), PDO::PARAM_STR);
        $stmt->execute();
        $datos = $stmt->fetchAll();
        foreach ($datos as $row) {
        if ($row['CONTADOR'] > 0) {
        $insBeanCrud->setMessageServer("Se encuentra Registrado el Usuario, cambie de Email");
        } else {
        $stmt = $this->conexion_db->prepare("SELECT COUNT(id) AS CONTADOR FROM `administrador` WHERE AdminNombre=? and AdminApellido=?");
        $stmt->bindValue(1, $Cliente->getNombre(), PDO::PARAM_STR);
        $stmt->bindValue(2, $Cliente->getApellido(), PDO::PARAM_STR);
        $stmt->execute();
        $datos2 = $stmt->fetchAll();
        foreach ($datos2 as $row2) {
        if ($row2['CONTADOR'] > 0) {
        $insBeanCrud->setMessageServer("Ya se encuentra registrado, cambie de Nombres y Apellidos");
        } else {
        $stmt = $this->conexion_db->query("SELECT MAX(idcuenta) AS MAXIMO FROM `cuenta`");
        $datos3 = $stmt->fetchAll();
        foreach ($datos3 as $row3) {
        if ($row3['MAXIMO'] > 0) {
        $cuentacodigo = mainModel::generar_codigo_aleatorio("AC", 7, $row3['MAXIMO'] + 1);
        $insCuenta->setCuentaCodigo($cuentacodigo);
        if ($original['error'] > 0) {
        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
        } else {
        $resultado = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"), 3700, $original, $nombre, "./adjuntos/clientes/comprobante/");
        if ($resultado != "") {
        $insCuenta->setVoucher($resultado);
        $stmt = clienteModelo::agregar_cuenta_modelo($this->conexion_db, $insCuenta);
        if ($stmt->execute()) {
        $Cliente->setCuenta($cuentacodigo);
        $stmt = clienteModelo::agregar_cliente_modelo($this->conexion_db, $Cliente);
        if ($stmt->execute()) {
        $stmt = $this->conexion_db->query("SELECT COUNT(idlibro) AS CONTADOR FROM `libro`");
        $datos4 = $stmt->fetchAll();
        foreach ($datos4 as $row4) {
        if ($row4['CONTADOR'] > 0) {
        $stmt = $this->conexion_db->query("SELECT MIN(codigo) AS MINIMO FROM `libro`");
        $datos5 = $stmt->fetchAll();
        foreach ($datos5 as $row5) {
        $insLibroCuenta = new LibroCuenta();
        $insLibroCuenta->setCuenta($cuentacodigo);
        $insLibroCuenta->setLibro($row5['MINIMO']);
        $stmt = clienteModelo::agregar_libro_cuenta_modelo($this->conexion_db, $insLibroCuenta);
        if ($stmt->execute()) {
        $this->conexion_db->commit();
        $insBeanCrud->setMessageServer("ok");
        if ($tipo == 1) {
        $insBeanCrud->setBeanPagination(self::paginador_cliente_controlador($this->conexion_db, 0, 5, 0, ""));
        }

        } else {
        $insBeanCrud->setMessageServer("No hemos podido registrar los datos");
        }

        }

        } else {
        $insBeanCrud->setMessageServer("No se encuentra libro para registrar al usuario");
        }
        }

        } else {
        $insBeanCrud->setMessageServer("No hemos podido registrar los datos");
        }
        } else {
        $insBeanCrud->setMessageServer("No hemos podido registrar la cuenta");
        }
        } else {
        $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido");

        }
        }

        }

        }

        }
        }
        }
        }
        } else {
        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, no ingresaste la imagen del voucher");
        } */

        } catch (Exception $th) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
                $stmt = null;
            }
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());
    }
    public function agregar_publico_cliente_controlador($Cliente)
    {

        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Cliente->setTelefono(mainModel::limpiar_cadena($Cliente->getTelefono()));
            $Cliente->setOcupacion(mainModel::limpiar_cadena($Cliente->getOcupacion()));
            $Cliente->setNombre(mainModel::limpiar_cadena($Cliente->getNombre()));
            $Cliente->setApellido(mainModel::limpiar_cadena($Cliente->getApellido()));
            $Cliente->setPais(mainModel::limpiar_cadena($Cliente->getPais()));
            $Cliente->setVendedor(mainModel::limpiar_cadena($Cliente->getVendedor()));
            $Cliente->setTipoMedio(mainModel::limpiar_cadena($Cliente->getTipoMedio()));
            $insCuenta = new Cuenta();
            $insCuenta->setPrecio(mainModel::limpiar_cadena($Cliente->getCuenta()->precio));
            $insCuenta->setUsuario(mainModel::limpiar_cadena($Cliente->getCuenta()->usuario));
            $insCuenta->setClave(mainModel::encryption(mainModel::limpiar_cadena($Cliente->getCuenta()->clave)));
            $insCuenta->setEmail(mainModel::limpiar_cadena($Cliente->getCuenta()->email));
            $insCuenta->setEstado(0);
            $insCuenta->setTipo(2);
            if (($Cliente->getTipoMedio()) < 0 && ($Cliente->getTipoMedio()) > 5) {
                $insBeanCrud->setMessageServer("Formato de datos incorrectos");

            } else {
                $stmt = $this->conexion_db->prepare("SELECT COUNT(idcuenta) AS CONTADOR FROM `cuenta` WHERE email=?");
                $stmt->bindValue(1, $insCuenta->getEmail(), PDO::PARAM_STR);
                $stmt->execute();
                $datos = $stmt->fetchAll();
                foreach ($datos as $row) {
                    if ($row['CONTADOR'] > 0) {
                        $insBeanCrud->setMessageServer("Se encuentra Registrado el Usuario, cambie de Email");
                    } else {
                        $stmt = $this->conexion_db->prepare("SELECT COUNT(id) AS CONTADOR FROM `administrador` WHERE AdminNombre=? and AdminApellido=?");
                        $stmt->bindValue(1, $Cliente->getNombre(), PDO::PARAM_STR);
                        $stmt->bindValue(2, $Cliente->getApellido(), PDO::PARAM_STR);
                        $stmt->execute();
                        $datos2 = $stmt->fetchAll();
                        foreach ($datos2 as $row2) {
                            if ($row2['CONTADOR'] > 0) {
                                $insBeanCrud->setMessageServer("Ya se encuentra registrado, cambie de Nombres y Apellidos");
                            } else {
                                $stmt = $this->conexion_db->query("SELECT MAX(idcuenta) AS MAXIMO FROM `cuenta`");
                                $datos3 = $stmt->fetchAll();
                                foreach ($datos3 as $row3) {
                                    if ($row3['MAXIMO'] > 0) {
                                        $cuentacodigo = mainModel::generar_codigo_aleatorio("AC", 7, $row3['MAXIMO'] + 1);
                                        $insCuenta->setCuentaCodigo($cuentacodigo);
                                        $insCuenta->setVoucher("");
                                        $stmt = clienteModelo::agregar_cuenta_modelo($this->conexion_db, $insCuenta);
                                        if ($stmt->execute()) {
                                            $Cliente->setCuenta($cuentacodigo);
                                            $stmt = clienteModelo::agregar_cliente_modelo($this->conexion_db, $Cliente);
                                            if ($stmt->execute()) {
                                                $stmt = $this->conexion_db->query("SELECT COUNT(idlibro) AS CONTADOR FROM `libro`");
                                                $datos4 = $stmt->fetchAll();
                                                foreach ($datos4 as $row4) {
                                                    if ($row4['CONTADOR'] > 0) {
                                                        $stmt = $this->conexion_db->query("SELECT MIN(codigo) AS MINIMO FROM `libro`");
                                                        $datos5 = $stmt->fetchAll();
                                                        foreach ($datos5 as $row5) {
                                                            $insLibroCuenta = new LibroCuenta();
                                                            $insLibroCuenta->setCuenta($cuentacodigo);
                                                            $insLibroCuenta->setLibro($row5['MINIMO']);
                                                            $stmt = clienteModelo::agregar_libro_cuenta_modelo($this->conexion_db, $insLibroCuenta);
                                                            if ($stmt->execute()) {
                                                                $this->conexion_db->commit();
                                                                $insBeanCrud->setMessageServer("ok");

                                                            } else {
                                                                $insBeanCrud->setMessageServer("No hemos podido registrar los datos");
                                                            }

                                                        }

                                                    } else {
                                                        $insBeanCrud->setMessageServer("No se encuentra libro para registrar al usuario");
                                                    }
                                                }

                                            } else {
                                                $insBeanCrud->setMessageServer("No hemos podido registrar los datos");
                                            }
                                        } else {
                                            $insBeanCrud->setMessageServer("No hemos podido registrar la cuenta");
                                        }

                                    }

                                }

                            }
                        }
                    }
                }

            }

        } catch (Exception $th) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
                $stmt = null;
            }
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());
    }
    public function agregar_publico_cliente_otro_medio_controlador($Cliente)
    {

        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Cliente->setTelefono(mainModel::limpiar_cadena($Cliente->getTelefono()));
            $Cliente->setOcupacion(mainModel::limpiar_cadena($Cliente->getOcupacion()));
            $Cliente->setNombre(mainModel::limpiar_cadena($Cliente->getNombre()));
            $Cliente->setApellido(mainModel::limpiar_cadena($Cliente->getApellido()));
            $Cliente->setPais(mainModel::limpiar_cadena($Cliente->getPais()));
            $Cliente->setVendedor(mainModel::limpiar_cadena($Cliente->getVendedor()));
            $Cliente->setTipoMedio(mainModel::limpiar_cadena($Cliente->getTipoMedio()));
            $insCuenta = new Cuenta();
            $insCuenta->setPrecio(mainModel::limpiar_cadena($Cliente->getCuenta()->precio));
            $insCuenta->setUsuario(mainModel::limpiar_cadena($Cliente->getCuenta()->usuario));
            $insCuenta->setClave(mainModel::encryption(mainModel::limpiar_cadena($Cliente->getCuenta()->clave)));
            $insCuenta->setEmail(mainModel::limpiar_cadena($Cliente->getCuenta()->email));
            $insCuenta->setEstado(0);
            $insCuenta->setTipo(2);
            if (($Cliente->getTipoMedio()) < 0 && ($Cliente->getTipoMedio()) > 5) {
                $insBeanCrud->setMessageServer("Formato de datos incorrectos");

            } else {
                if (isset($_FILES['txtImagenVoucher'])) {
                    $original = $_FILES['txtImagenVoucher'];
                    $nombre = $original['name'];
                    if ($original['error'] > 0) {
                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                    } else {
                        $resultado = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"), (5 * 1024), $original, $nombre, "./adjuntos/clientes/comprobante/");
                        if ($resultado != "") {
                            $insCuenta->setVoucher($resultado);

                            $stmt = $this->conexion_db->query("SELECT MAX(idcuenta) AS MAXIMO FROM `cuenta`");
                            $datos3 = $stmt->fetchAll();
                            foreach ($datos3 as $row3) {
                                if ($row3['MAXIMO'] > 0) {
                                    $cuentacodigo = mainModel::generar_codigo_aleatorio("AC", 7, $row3['MAXIMO'] + 1);
                                    $insCuenta->setCuentaCodigo($cuentacodigo);

                                    $stmt = clienteModelo::agregar_cuenta_modelo($this->conexion_db, $insCuenta);
                                    if ($stmt->execute()) {
                                        $Cliente->setCuenta($cuentacodigo);
                                        $stmt = clienteModelo::agregar_cliente_modelo($this->conexion_db, $Cliente);
                                        if ($stmt->execute()) {
                                            $stmt = $this->conexion_db->query("SELECT COUNT(idlibro) AS CONTADOR FROM `libro`");
                                            $datos4 = $stmt->fetchAll();
                                            foreach ($datos4 as $row4) {
                                                if ($row4['CONTADOR'] > 0) {
                                                    $stmt = $this->conexion_db->query("SELECT MIN(codigo) AS MINIMO FROM `libro`");
                                                    $datos5 = $stmt->fetchAll();
                                                    foreach ($datos5 as $row5) {
                                                        $insLibroCuenta = new LibroCuenta();
                                                        $insLibroCuenta->setCuenta($cuentacodigo);
                                                        $insLibroCuenta->setLibro($row5['MINIMO']);
                                                        $stmt = clienteModelo::agregar_libro_cuenta_modelo($this->conexion_db, $insLibroCuenta);
                                                        if ($stmt->execute()) {
                                                            $this->conexion_db->commit();
                                                            $insBeanCrud->setMessageServer("ok");
                                                            if (self::enviar_mensaje_clpe_controlador($this->conexion_db, $Cliente, $insCuenta)) {
                                                                $insBeanCrud->setMessageServer("ok");
                                                            } else {
                                                                $insBeanCrud->setMessageServer("registrado, no se envió un email a CLPE");
                                                            }

                                                        } else {
                                                            $insBeanCrud->setMessageServer("No hemos podido registrar los datos");
                                                        }

                                                    }

                                                } else {
                                                    $insBeanCrud->setMessageServer("No se encuentra libro para registrar al usuario");
                                                }
                                            }

                                        } else {
                                            $insBeanCrud->setMessageServer("No hemos podido registrar los datos");
                                        }
                                    } else {
                                        $insBeanCrud->setMessageServer("No hemos podido registrar la cuenta");
                                    }

                                }

                            }

                        }
                    }
                } else {
                    $insBeanCrud->setMessageServer("Ingrese voucher");
                }
            }
        } catch (Exception $th) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
                $stmt = null;
            }
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());
    }
    public function agregar_publico_culqui_cliente_controlador($Cliente, $culqi)
    {

        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Cliente->setTelefono(mainModel::limpiar_cadena($Cliente->getTelefono()));
            $Cliente->setOcupacion(mainModel::limpiar_cadena($Cliente->getOcupacion()));
            $Cliente->setNombre(mainModel::limpiar_cadena($Cliente->getNombre()));
            $Cliente->setApellido(mainModel::limpiar_cadena($Cliente->getApellido()));
            $Cliente->setPais(mainModel::limpiar_cadena($Cliente->getPais()));
            $Cliente->setVendedor(mainModel::limpiar_cadena($Cliente->getVendedor()));
            $Cliente->setTipoMedio(mainModel::limpiar_cadena($Cliente->getTipoMedio()));
            $insCuenta = new Cuenta();
            $insCuenta->setPrecio(mainModel::limpiar_cadena($Cliente->getCuenta()->precio));
            $insCuenta->setUsuario(mainModel::limpiar_cadena($Cliente->getCuenta()->usuario));
            $insCuenta->setClave(mainModel::encryption(mainModel::limpiar_cadena($Cliente->getCuenta()->clave)));
            $insCuenta->setEmail(mainModel::limpiar_cadena($Cliente->getCuenta()->email));
            $insCuenta->setVerificacion(rand(1000, 9999));
            $insCuenta->setEstado(1);
            $insCuenta->setTipo(2);
            if (($Cliente->getTipoMedio()) < 0 && ($Cliente->getTipoMedio()) > 5) {
                $insBeanCrud->setMessageServer("Formato de datos incorrectos");

            } else {
                //
                $stmt = $this->conexion_db->query("SELECT MAX(idcuenta) AS MAXIMO FROM `cuenta`");
                $datos3 = $stmt->fetchAll();
                foreach ($datos3 as $row3) {
                    if ($row3['MAXIMO'] > 0) {
                        $cuentacodigo = mainModel::generar_codigo_aleatorio("AC", 7, $row3['MAXIMO'] + 1);
                        $insCuenta->setCuentaCodigo($cuentacodigo);
                        $insCuenta->setVoucher("CULQI");
                        $stmt = clienteModelo::agregar_cuenta_culqui_modelo($this->conexion_db, $insCuenta);
                        if ($stmt->execute()) {
                            $Cliente->setCuenta($cuentacodigo);
                            $stmt = clienteModelo::agregar_cliente_modelo($this->conexion_db, $Cliente);
                            if ($stmt->execute()) {
                                $stmt = $this->conexion_db->query("SELECT COUNT(idlibro) AS CONTADOR FROM `libro`");
                                $datos4 = $stmt->fetchAll();
                                foreach ($datos4 as $row4) {
                                    if ($row4['CONTADOR'] > 0) {
                                        $stmt = $this->conexion_db->query("SELECT MIN(codigo) AS MINIMO FROM `libro`");
                                        $datos5 = $stmt->fetchAll();
                                        foreach ($datos5 as $row5) {
                                            $insLibroCuenta = new LibroCuenta();
                                            $insLibroCuenta->setCuenta($cuentacodigo);
                                            $insLibroCuenta->setLibro($row5['MINIMO']);
                                            $stmt = clienteModelo::agregar_libro_cuenta_modelo($this->conexion_db, $insLibroCuenta);
                                            if ($stmt->execute()) {
                                                $stmt = clienteModelo::agregar_historial_economico_modelo($this->conexion_db, $Cliente, $culqi);
                                                if ($stmt->execute()) {
                                                    $this->conexion_db->commit();
                                                    // $insBeanCrud->setMessageServer("ok");
                                                    //obtener la cuenta registrada
                                                    $insToken = new Auth();
                                                    $insUser = new Usuario();
                                                    $clienteunico = clienteModelo::datos_cliente_modelo($this->conexion_db, "cuenta", $insCuenta);
                                                    if ($clienteunico["countFilter"] == 0) {
                                                        $insBeanCrud->setMessageServer("no se encuentra el usuario");
                                                    } else {
                                                        $insUser->setId($clienteunico["list"][0]['cuenta']['idcuenta']);
                                                        $insUser->setUsuario($clienteunico["list"][0]['cuenta']['usuario']);
                                                        $insUser->setEmail($clienteunico["list"][0]['cuenta']['email']);
                                                        $insUser->setTipo(2);
                                                        $insUser->setCodigo($clienteunico["list"][0]['cuenta']['cuentaCodigo']);
                                                        //

                                                        $Cliente->setCuenta((object) array("codigo" => $insCuenta->getCuentaCodigo(),
                                                            "usuario" => $insCuenta->getUsuario(),
                                                            "clave" => $insCuenta->getClave(),
                                                            "email" => $insCuenta->getEmail(),
                                                            "perfil" => $insCuenta->getPerfil(),
                                                            "cuentaverificacion" => $insCuenta->getVerificacion(),
                                                            "precio" => $insCuenta->getPrecio(),
                                                            "estado" => $insCuenta->getEstado(),
                                                            "voucher" => $insCuenta->getVoucher(),
                                                            "tipo" => $insCuenta->getTipo(),
                                                            "foto" => $insCuenta->getFoto(),
                                                            "idcuenta" => $clienteunico["list"][0]['cuenta']['idcuenta'],

                                                        ));
                                                        $responsemensaje = self::enviar_mensaje_controlador($this->conexion_db, $Cliente, $insToken->autenticar($insUser)['token']);

                                                        $insBeanCrud->setMessageServer($responsemensaje['messageServer']);
                                                        $insBeanCrud->setBeanClass(array("nPedido" => $culqi->requestNiubiz->order->purchaseNumber,
                                                            "nombre" => ($Cliente->getApellido() . " " . $Cliente->getNombre()),
                                                            "fecha" => $culqi->fecha,
                                                            "importe" => $culqi->requestNiubiz->dataMap->AMOUNT,
                                                            "tipoCurrency" => $culqi->requestNiubiz->order->currency,
                                                            "descripcionProducto" => "CLUB DE LECTURA",
                                                            "tarjeta" => $culqi->requestNiubiz->dataMap->CARD,
                                                            "marcaTarjeta" => $culqi->nombre_banco,
                                                        ));
                                                    }
                                                } else {
                                                    $insBeanCrud->setMessageServer("No hemos podido registrar el historial económico.");
                                                }

                                            } else {
                                                $insBeanCrud->setMessageServer("No hemos podido registrar los datos");
                                            }

                                        }

                                    } else {
                                        $insBeanCrud->setMessageServer("No se encuentra libro para registrar al usuario");
                                    }
                                }

                            } else {
                                $insBeanCrud->setMessageServer("No hemos podido registrar los datos");
                            }
                        } else {
                            $insBeanCrud->setMessageServer("No hemos podido registrar la cuenta");
                        }

                    }

                }
            }
        } catch (Exception $th) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
                $stmt = null;
            }
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());
    }
    public function validar_cliente_controlador($Cliente)
    {

        $insBeanCrud = new BeanCrud();
        try {
            $Cliente->setTelefono(mainModel::limpiar_cadena($Cliente->getTelefono()));
            $Cliente->setOcupacion(mainModel::limpiar_cadena($Cliente->getOcupacion()));
            $Cliente->setNombre(mainModel::limpiar_cadena($Cliente->getNombre()));
            $Cliente->setApellido(mainModel::limpiar_cadena($Cliente->getApellido()));
            $Cliente->setPais(mainModel::limpiar_cadena($Cliente->getPais()));
            $insCuenta = new Cuenta();
            $insCuenta->setEmail(mainModel::limpiar_cadena($Cliente->getCuenta()->email));

            $stmt = $this->conexion_db->prepare("SELECT COUNT(idcuenta) AS CONTADOR FROM `cuenta` WHERE email=?");
            $stmt->bindValue(1, $insCuenta->getEmail(), PDO::PARAM_STR);
            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                if ($row['CONTADOR'] > 0) {
                    $insBeanCrud->setMessageServer("Ya se encuentra Registrado el Usuario, cambie de Email");
                } else {
                    $stmt = $this->conexion_db->prepare("SELECT COUNT(id) AS CONTADOR FROM `administrador` WHERE AdminNombre=? and AdminApellido=?");
                    $stmt->bindValue(1, $Cliente->getNombre(), PDO::PARAM_STR);
                    $stmt->bindValue(2, $Cliente->getApellido(), PDO::PARAM_STR);
                    $stmt->execute();
                    $datos2 = $stmt->fetchAll();
                    foreach ($datos2 as $row2) {
                        if ($row2['CONTADOR'] > 0) {
                            $insBeanCrud->setMessageServer("Ya se encuentra registrado, este usuario");
                        } else {
                            $insBeanCrud->setMessageServer("ok");

                        }
                    }
                }
            }

        } catch (Exception $th) {

            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {

            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
                $stmt = null;
            }
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());
    }
    public function datos_cliente_controlador($tipo, $codigo)
    {

        $insBeanCrud = new BeanCrud();
        try {
            $tipo = mainModel::limpiar_cadena($tipo);
            $insBeanCrud->setBeanPagination(clienteModelo::datos_cliente_modelo($this->conexion_db, $tipo, $codigo));
        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }

    public function reporte_cliente_controlador($tipo, $codigo)
    {
        $row = "";

        try {
            $variable = clienteModelo::datos_cliente_modelo($this->conexion_db, mainModel::limpiar_cadena($tipo), $codigo);
            if ($variable['countFilter'] > 0) {
                $titulo = "";
                switch ($codigo->getCuenta()->getTipo()) {
                    case 2:
                        if ($codigo->getCuenta()->getEstado() == 0) {
                            $titulo = "ALUMNOS NO MATRICULADOS";
                        } else if ($codigo->getCuenta()->getEstado() == 1) {
                            $titulo = "ALUMNOS MATRICULADOS";
                        } else {
                            $titulo = "ALUMNOS";
                        }

                        break;

                    default:
                        $titulo = "PERSONAL ADMINISTRATIVO";
                        break;
                }
                $row = "<table border='1'>
        <thead>
        <tr>
        <th colspan='8'> LISTA DE $titulo </th>
        </tr>
        <tr>
          <th ></th>
          <th >NOMBRES</th>
          <th >APELLIDOS</th>
          <th >OCUPACION/OFICIO</th>
          <th >PAIS</th>
          <th>TELEFONO</th>
          <th >EMAIL</th>
          <th >MONTO</th>
        </tr>
      </thead>
      <tbody> ";
                $contador = 1;
                foreach ($variable['list'] as $value) {
                    $row = $row . "
            <tr>
            <td>" . ($contador++) . "</td>
              <td>" . $value['nombre'] . "</td>
              <td>" . $value['apellido'] . "</td>
              <td>" . $value['ocupacion'] . "</td>
              <td>" . $value['pais'] . "</td>
              <td>" . $value['telefono'] . "</td>
              <td>" . $value['cuenta']['email'] . "</td>
              <td>" . $value['cuenta']['voucher'] . "</td>
            </tr>";
                }
                $row = $row . "</tbody> </table>";

                header("Content-Disposition:attachment;filename=$titulo.xls");

            }

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $row;
    }
    public function paginador_cliente_controlador($conexion, $inicio, $registros, $estado, $filtro)
    {
        $insBeanPagination = new BeanPagination();
        try {
            if ((int) $estado > -1) {
                $stmt = $conexion->prepare("SELECT COUNT(idcuenta) AS CONTADOR FROM `cuenta` WHERE estado=? and tipo=2 and idcuenta!=1");
                $stmt->bindValue(1, $estado, PDO::PARAM_INT);
            } else {
                $stmt = $conexion->prepare("SELECT COUNT(idcuenta) AS CONTADOR FROM `cuenta` WHERE tipo=2 and idcuenta!=1");
            }

            $stmt->execute();
            $datos = $stmt->fetchAll();
            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
                if ($row['CONTADOR'] > 0) {
                    if ((int) $estado > -1) {
                        $stmt = $conexion->prepare("SELECT * FROM `administrador`  as admmini inner join `cuenta` as cuent ON cuent.CuentaCodigo=admmini.Cuenta_Codigo WHERE cuent.estado=? and cuent.tipo=2 and cuent.idcuenta!=1 and (admmini.AdminNombre like concat('%',?,'%') OR admmini.AdminApellido like concat('%',?,'%') OR admmini.AdminTelefono like concat('%',?,'%') OR cuent.email like concat('%',?,'%')) ORDER BY admmini.id DESC LIMIT ?,?");
                        $stmt->bindValue(1, $estado, PDO::PARAM_INT);
                        $stmt->bindValue(2, $filtro, PDO::PARAM_STR);
                        $stmt->bindValue(3, $filtro, PDO::PARAM_STR);
                        $stmt->bindValue(4, $filtro, PDO::PARAM_STR);
                        $stmt->bindValue(5, $filtro, PDO::PARAM_STR);
                        $stmt->bindValue(6, $inicio, PDO::PARAM_INT);
                        $stmt->bindValue(7, $registros, PDO::PARAM_INT);
                    } else {
                        $stmt = $conexion->prepare("SELECT * FROM `administrador`  as admmini inner join `cuenta` as cuent ON cuent.CuentaCodigo=admmini.Cuenta_Codigo WHERE cuent.tipo=2 and cuent.idcuenta!=1 and (admmini.AdminNombre like concat('%',?,'%') OR admmini.AdminApellido like concat('%',?,'%') OR admmini.AdminTelefono like concat('%',?,'%') OR cuent.email like concat('%',?,'%'))  ORDER BY admmini.id DESC LIMIT ?,?");
                        $stmt->bindValue(1, $filtro, PDO::PARAM_STR);
                        $stmt->bindValue(2, $filtro, PDO::PARAM_STR);
                        $stmt->bindValue(3, $filtro, PDO::PARAM_STR);
                        $stmt->bindValue(4, $filtro, PDO::PARAM_STR);
                        $stmt->bindValue(5, $inicio, PDO::PARAM_INT);
                        $stmt->bindValue(6, $registros, PDO::PARAM_INT);
                    }

                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        $insCliente = new Cliente();
                        $insCuenta = new Cuenta();
                        $insCuenta->setIdCuenta($row['idcuenta']);
                        $insCuenta->setCuentaCodigo($row['CuentaCodigo']);
                        $insCuenta->setUsuario($row['usuario']);
                        $insCuenta->setClave(mainModel::decryption($row['clave']));
                        $insCuenta->setEmail($row['email']);
                        $insCuenta->setEstado($row['estado']);
                        $insCuenta->setTipo($row['tipo']);
                        $insCuenta->setFoto($row['foto']);
                        $insCuenta->setPrecio($row['precio_curso']);
                        $insCuenta->setVoucher($row['voucher']);

                        $insCliente->setIdCliente($row['id']);
                        $insCliente->setNombre($row['AdminNombre']);
                        $insCliente->setTelefono($row['AdminTelefono']);
                        $insCliente->setApellido($row['AdminApellido']);
                        $insCliente->setOcupacion($row['AdminOcupacion']);
                        $insCliente->setPais($row['pais']);
                        $insCliente->setTipoMedio($row['tipo_medio']);
                        $insCliente->setVendedor($row['codigo_vendedor']);
                        $insCliente->setCuenta($insCuenta->__toString());
                        $insBeanPagination->setList($insCliente->__toString());

                    }

                }
            }

            $stmt->closeCursor(); // this is not even required
            $stmt = null; // doing this is mandatory for connection to get closed

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";
        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";
        }
        return $insBeanPagination->__toString();

    }
    public function bean_paginador_cliente_controlador($pagina, $registros, $estado, $filtro)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $estado = mainModel::limpiar_cadena($estado);
            $filtro = mainModel::limpiar_cadena($filtro);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_cliente_controlador($this->conexion_db, $inicio, $registros, $estado, $filtro));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function eliminar_cliente_controlador($Cliente)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Cliente->setIdCliente(mainModel::limpiar_cadena($Cliente->getIdCliente()));

            $lista = clienteModelo::datos_cliente_modelo($this->conexion_db, "unico", $Cliente);
            if ($lista["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("No podemos eliminar porque no existe el alumno registrado");
            } else {
                $Cliente->setCuenta($lista["list"][0]['cuenta']['cuentaCodigo']);
                $stmt = $this->conexion_db->prepare("SELECT COUNT(idlecciones) AS CONTADOR FROM `lecciones` WHERE cuenta_codigocuenta=?");
                $stmt->bindValue(1, $Cliente->getCuenta(), PDO::PARAM_STR);
                $stmt->execute();
                $datos = $stmt->fetchAll();
                foreach ($datos as $row) {
                    if ($row['CONTADOR'] == 0) {
                        $stmt = $this->conexion_db->prepare("SELECT MAX(idlibroCuenta) AS CONTADOR FROM `librocuenta` WHERE cuenta_codigocuenta=?");
                        $stmt->bindValue(1, $Cliente->getCuenta(), PDO::PARAM_STR);
                        $stmt->execute();
                        $datos2 = $stmt->fetchAll();
                        foreach ($datos2 as $row2) {
                            if ($row2['CONTADOR'] > 0) {
                                $idcuentalibr = $row2['CONTADOR'];
                                $stmt = clienteModelo::eliminar_libro_cuenta_modelo($this->conexion_db, (int) $idcuentalibr);
                                if ($stmt->execute()) {
                                    $stmt = clienteModelo::eliminar_cliente_modelo($this->conexion_db, $Cliente->getIdCliente());
                                    if ($stmt->execute()) {
                                        $stmt = $this->conexion_db->prepare("DELETE FROM `bitacora` WHERE cuenta_codigoCuenta=:Codigo");
                                        $stmt->bindValue(":Codigo", $lista["list"][0]['cuenta']['cuentaCodigo'], PDO::PARAM_STR);
                                        if ($stmt->execute()) {
                                            $stmt = clienteModelo::eliminar_cuenta_modelo($this->conexion_db, $lista["list"][0]['cuenta']['idcuenta']);
                                            if ($stmt->execute()) {

                                                if ($lista["list"][0]['cuenta']['foto'] != "") {
                                                    unlink('./adjuntos/clientes/' . $lista["list"][0]['cuenta']['foto']);
                                                }
                                                /*
                                                if ($lista["list"][0]['cuenta']['voucher'] != "") {
                                                if ($lista["list"][0]['cuenta']['voucher'] != "CULQI") {
                                                unlink('./adjuntos/clientes/comprobante/' . $lista["list"][0]['cuenta']['voucher']);
                                                }

                                                }*/
                                                $this->conexion_db->commit();
                                                $insBeanCrud->setMessageServer("ok");
                                                $insBeanCrud->setBeanPagination(self::paginador_cliente_controlador($this->conexion_db, 0, 20, $lista["list"][0]['cuenta']['estado'], ""));
                                            }
                                        } else {
                                            $insBeanCrud->setMessageServer("no se eliminó el alumno");
                                        }
                                    } else {
                                        $insBeanCrud->setMessageServer("no se eliminó el alumno");
                                    }
                                } else {
                                    $insBeanCrud->setMessageServer("no se eliminó el alumno");
                                }
                            } else {
                                $insBeanCrud->setMessageServer("no se eliminó el alumno, no esta asociado a un libro");
                            }
                        }
                    } else {
                        $insBeanCrud->setMessageServer("no se eliminó, el alumno tiene lecciones realizadas");
                    }

                }

            }

        } catch (Exception $th) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
                $stmt = null;
            }
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());

    }
    public function actualizar_cliente_controlador($tipo, $Cliente)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Cliente->setTelefono(mainModel::limpiar_cadena($Cliente->getTelefono()));
            $Cliente->setOcupacion(mainModel::limpiar_cadena($Cliente->getOcupacion()));
            $Cliente->setNombre(mainModel::limpiar_cadena($Cliente->getNombre()));
            $Cliente->setApellido(mainModel::limpiar_cadena($Cliente->getApellido()));
            $Cliente->setPais(mainModel::limpiar_cadena($Cliente->getPais()));
            $insCuenta = new Cuenta();
            //  $insCuenta->setVoucher(mainModel::limpiar_cadena($Cliente->getCuenta()->voucher));
            $insCuenta->setUsuario(mainModel::limpiar_cadena($Cliente->getCuenta()["usuario"]));

            $insCuenta->setEmail(mainModel::limpiar_cadena($Cliente->getCuenta()["email"]));
            $insCuenta->setCuentaCodigo(mainModel::limpiar_cadena($Cliente->getCuenta()["cuentaCodigo"]));
            $insCuenta->setClave($Cliente->getCuenta()["clave"]);
            // $insCuenta->setEstado(0);
            // $insCuenta->setTipo(2);
            $clienteunico = clienteModelo::datos_cliente_modelo($this->conexion_db, "cuenta", $insCuenta);
            if ($clienteunico["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("no se encuentra el usuario");
            } else {
                if (isset($_FILES['txtFoto'])) {
                    $original = $_FILES['txtFoto'];
                    $nombre = $original['name'];
                    if ($original['error'] > 0) {
                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                    } else {
                        $resultado = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"), (5 * 1024), $original, $nombre, "./adjuntos/clientes/");
                        if ($resultado != "") {
                            $insCuenta->setFoto($resultado);
                            $insCuenta->setIdCuenta($clienteunico["list"][0]['cuenta']['idcuenta']);
                            $stmt = $this->conexion_db->prepare("SELECT COUNT(idcuenta) AS CONTADOR FROM `cuenta` WHERE email=? and idcuenta!=?");
                            $stmt->bindValue(1, $insCuenta->getEmail(), PDO::PARAM_STR);
                            $stmt->bindValue(2, $insCuenta->getIdCuenta(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                if ($row['CONTADOR'] == 0) {
                                    if ($insCuenta->getClave() == "") {
                                        $insCuenta->setClave($clienteunico["list"][0]['cuenta']['clave']);
                                    } else {
                                        $insCuenta->setClave(mainModel::encryption(mainModel::limpiar_cadena($insCuenta->getClave())));
                                    }

                                    $stmt = clienteModelo::actualizar_cuenta_modelo($this->conexion_db, $insCuenta);

                                    if ($stmt->execute()) {

                                        $stmt = clienteModelo::actualizar_cliente_modelo($this->conexion_db, $Cliente);

                                        if ($stmt->execute()) {
                                            if ($clienteunico["list"][0]['cuenta']['foto'] != "") {
                                                unlink('./adjuntos/clientes/' . $clienteunico["list"][0]['cuenta']['foto']);
                                            }
                                            $this->conexion_db->commit();
                                            $insBeanCrud->setMessageServer("ok");
                                            if ($tipo == 1) {
                                                $insBeanCrud->setBeanPagination(self::paginador_cliente_controlador($this->conexion_db, 0, 5, $clienteunico["list"][0]['cuenta']['estado'], ""));
                                            } else {
                                                $insCuenta = new Cuenta();
                                                $insCuenta->setFoto($resultado);
                                                $insBeanCrud->setBeanPagination($insCuenta->__toString());
                                            }
                                        } else {
                                            $insBeanCrud->setMessageServer("No hemos podido actualizar sus datos");
                                        }
                                    } else {
                                        $insBeanCrud->setMessageServer("No hemos podido actualizar al Usuario");
                                    }

                                } else {
                                    $insBeanCrud->setMessageServer("Ya se encuentra un usuario registrado con los datos, cambie de Correo Electrónico");
                                }
                            }
                        } else {
                            $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido");

                        }
                    }
                } else {
                    $insCuenta->setFoto($clienteunico["list"][0]['cuenta']['foto']);
                    $insCuenta->setIdCuenta($clienteunico["list"][0]['cuenta']['idcuenta']);
                    $stmt = $this->conexion_db->prepare("SELECT COUNT(idcuenta) AS CONTADOR FROM `cuenta` WHERE email=? and idcuenta!=?");
                    $stmt->bindValue(1, $insCuenta->getEmail(), PDO::PARAM_STR);
                    $stmt->bindValue(2, $insCuenta->getIdCuenta(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        if ($row['CONTADOR'] == 0) {
                            if ($insCuenta->getClave() == "") {
                                $insCuenta->setClave($clienteunico["list"][0]['cuenta']['clave']);
                            } else {
                                $insCuenta->setClave(mainModel::encryption(mainModel::limpiar_cadena($insCuenta->getClave())));
                            }
                            $stmt = clienteModelo::actualizar_cuenta_modelo($this->conexion_db, $insCuenta);
                            if ($stmt->execute()) {
                                $stmt = clienteModelo::actualizar_cliente_modelo($this->conexion_db, $Cliente);
                                if ($stmt->execute()) {
                                    $this->conexion_db->commit();
                                    $insBeanCrud->setMessageServer("ok");
                                    if ($tipo == 1) {
                                        $insBeanCrud->setBeanPagination(self::paginador_cliente_controlador($this->conexion_db, 0, 5, $clienteunico["list"][0]['cuenta']['estado'], ""));
                                    } else {
                                        $insCuenta = new Cuenta();
                                        $insCuenta->setFoto($clienteunico["list"][0]['cuenta']['foto']);
                                        $insBeanCrud->setBeanPagination($insCuenta->__toString());
                                    }
                                } else {
                                    $insBeanCrud->setMessageServer("No hemos podido actualizar sus datos");
                                }
                            } else {
                                $insBeanCrud->setMessageServer("No hemos podido actualizar al Usuario");
                            }

                        } else {
                            $insBeanCrud->setMessageServer("Ya se encuentra un usuario registrado con los datos, cambie de Correo Electrónico");
                        }
                    }
                }

            }

        } catch (Exception $th) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
                $stmt = null;
            }
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());

    }
    public function actualizar_datos_cliente_controlador($tipo, $Cliente)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();

            $Cliente->setTelefono(mainModel::limpiar_cadena($Cliente->getTelefono()));
            $Cliente->setOcupacion(mainModel::limpiar_cadena($Cliente->getOcupacion()));
            $Cliente->setNombre(mainModel::limpiar_cadena($Cliente->getNombre()));
            $Cliente->setApellido(mainModel::limpiar_cadena($Cliente->getApellido()));
            $Cliente->setPais(mainModel::limpiar_cadena($Cliente->getPais()));
            $insCuenta = new Cuenta();
            $insCuenta->setPrecio(mainModel::limpiar_cadena($Cliente->getCuenta()->precio));
            $insCuenta->setUsuario(mainModel::limpiar_cadena($Cliente->getCuenta()->usuario));
            $insCuenta->setEmail(mainModel::limpiar_cadena($Cliente->getCuenta()->email));
            $insCuenta->setEstado(mainModel::limpiar_cadena($Cliente->getCuenta()->estado));
            $insCuenta->setCuentaCodigo(mainModel::limpiar_cadena($Cliente->getCuenta()->codigo));
            $insCuenta->setClave($Cliente->getCuenta()->clave);
            $insCuenta->setVerificacion($Cliente->getCuenta()->cuentaverificacion);
            // $insCuenta->setEstado(0);
            // $insCuenta->setTipo(2);
            $clienteunico = clienteModelo::datos_cliente_modelo($this->conexion_db, "cuenta", $insCuenta);
            if ($clienteunico["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("no se encuentra el usuario");
            } else {
                if (isset($_FILES['txtImagenVoucher'])) {
                    $original = $_FILES['txtImagenVoucher'];
                    $nombre = $original['name'];
                    if ($original['error'] > 0) {
                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                    } else {
                        $resultado = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"), (5 * 1024), $original, $nombre, "./adjuntos/clientes/comprobante/");
                        if ($resultado != "") {
                            $insCuenta->setVoucher($resultado);
                            $insCuenta->setFoto($clienteunico["list"][0]['cuenta']['foto']);
                            $insCuenta->setIdCuenta($clienteunico["list"][0]['cuenta']['idcuenta']);
                            $stmt = $this->conexion_db->prepare("SELECT COUNT(idcuenta) AS CONTADOR FROM `cuenta` WHERE email=? and idcuenta!=?");
                            $stmt->bindValue(1, $insCuenta->getEmail(), PDO::PARAM_STR);
                            $stmt->bindValue(2, $insCuenta->getIdCuenta(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                if ($row['CONTADOR'] == 0) {
                                    if ($insCuenta->getClave() == "") {
                                        $insCuenta->setClave($clienteunico["list"][0]['cuenta']['clave']);
                                    } else {
                                        $insCuenta->setClave(mainModel::encryption(mainModel::limpiar_cadena($insCuenta->getClave())));
                                    }
                                    $stmt = clienteModelo::actualizar_datos_cuenta_modelo($this->conexion_db, $insCuenta);
                                    if ($stmt->execute()) {
                                        $stmt = clienteModelo::actualizar_cliente_modelo($this->conexion_db, $Cliente);
                                        if ($stmt->execute()) {
                                            if ($clienteunico["list"][0]['cuenta']['voucher'] != "") {
                                                unlink('./adjuntos/clientes/comprobante/' . $clienteunico["list"][0]['cuenta']['voucher']);
                                            }
                                            $this->conexion_db->commit();
                                            $insBeanCrud->setMessageServer("ok");
                                            if ($tipo == 1) {
                                                $insBeanCrud->setBeanPagination(self::paginador_cliente_controlador($this->conexion_db, 0, 5, $clienteunico["list"][0]['cuenta']['estado'], ""));
                                            } else {
                                                $insCuenta = new Cuenta();
                                                $insCuenta->setFoto($resultado);
                                                $insBeanCrud->setBeanPagination($insCuenta->__toString());
                                            }
                                        } else {
                                            $insBeanCrud->setMessageServer("No hemos podido actualizar sus datos");
                                        }
                                    } else {
                                        $insBeanCrud->setMessageServer("No hemos podido actualizar al Usuario");
                                    }

                                } else {
                                    $insBeanCrud->setMessageServer("Ya se encuentra un usuario registrado con los datos, cambie de Correo Electrónico");
                                }
                            }
                        } else {
                            $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido");

                        }
                    }
                } else {
                    $insCuenta->setFoto($clienteunico["list"][0]['cuenta']['foto']);
                    $insCuenta->setVoucher($clienteunico["list"][0]['cuenta']['voucher']);
                    $insCuenta->setIdCuenta($clienteunico["list"][0]['cuenta']['idcuenta']);
                    $stmt = $this->conexion_db->prepare("SELECT COUNT(idcuenta) AS CONTADOR FROM `cuenta` WHERE email=? and idcuenta!=?");
                    $stmt->bindValue(1, $insCuenta->getEmail(), PDO::PARAM_STR);
                    $stmt->bindValue(2, $insCuenta->getIdCuenta(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        if ($row['CONTADOR'] == 0) {
                            if ($insCuenta->getClave() == "") {
                                $insCuenta->setClave($clienteunico["list"][0]['cuenta']['clave']);
                            } else {
                                $insCuenta->setClave(mainModel::encryption(mainModel::limpiar_cadena($insCuenta->getClave())));
                            }
                            $stmt = clienteModelo::actualizar_datos_cuenta_modelo($this->conexion_db, $insCuenta);
                            if ($stmt->execute()) {
                                $stmt = clienteModelo::actualizar_cliente_modelo($this->conexion_db, $Cliente);
                                if ($stmt->execute()) {
                                    $this->conexion_db->commit();
                                    $insBeanCrud->setMessageServer("ok");
                                    if ($tipo == 1) {
                                        $insBeanCrud->setBeanPagination(self::paginador_cliente_controlador($this->conexion_db, 0, 5, $clienteunico["list"][0]['cuenta']['estado'], ""));
                                    } else {
                                        $insCuenta = new Cuenta();
                                        $insCuenta->setFoto($clienteunico["list"][0]['cuenta']['foto']);
                                        $insBeanCrud->setBeanPagination($insCuenta->__toString());
                                    }
                                } else {
                                    $insBeanCrud->setMessageServer("No hemos podido actualizar sus datos");
                                }
                            } else {
                                $insBeanCrud->setMessageServer("No hemos podido actualizar al Usuario");
                            }

                        } else {
                            $insBeanCrud->setMessageServer("Ya se encuentra un usuario registrado con los datos, cambie de Correo Electrónico");
                        }
                    }
                }

            }

        } catch (Exception $th) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
                $stmt = null;
            }
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());

    }
    public function actualizar_cuenta_estado_controlador($Cuenta)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();
            $Cuenta->setCuentaCodigo(mainModel::limpiar_cadena($Cuenta->getCuentaCodigo()));
            $Cuenta->setEstado(mainModel::limpiar_cadena($Cuenta->getEstado()));
            $Cuenta->setIdCuenta(mainModel::limpiar_cadena($Cuenta->getIdCuenta()));
            $Cuenta->setVerificacion(mainModel::limpiar_cadena($Cuenta->getVerificacion()));

            $cuentaunico = clienteModelo::datos_cliente_modelo($this->conexion_db, "cuenta-unico", $Cuenta);
            if ($cuentaunico["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("no se encuentra el usuario");
            } else {

                $stmt = clienteModelo::actualizar_cuenta_estado_modelo($this->conexion_db, $Cuenta);
                if ($stmt->execute()) {
                    $this->conexion_db->commit();
                    $insBeanCrud->setMessageServer("ok");
                    $insBeanCrud->setBeanPagination(self::paginador_cliente_controlador($this->conexion_db, 0, 5, $cuentaunico["list"][0]['cuenta']['estado'], ""));
                } else {
                    $insBeanCrud->setMessageServer("No hemos podido cambiar de estado al Usuario");
                }

            }

        } catch (Exception $th) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
                $stmt = null;
            }
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());
    }

    public function paginador_cliente_tarea_controlador($conexion, $inicio, $registros, $filtro)
    {
        $insBeanPagination = new BeanPagination();
        try {
            $stmt = $conexion->prepare("SELECT tar.cuenta FROM `tarea` as tar inner join `administrador` as admmini ON tar.cuenta=admmini.Cuenta_Codigo inner join `cuenta` as cuent ON cuent.CuentaCodigo=admmini.Cuenta_Codigo left join `certificado` as cer ON cer.cuenta=admmini.Cuenta_Codigo WHERE cer.idcertificado is null and tar.tipo=0 and cuent.tipo=2 and cuent.idcuenta!=1 and (admmini.AdminNombre like concat('%',?,'%') OR admmini.AdminApellido like concat('%',?,'%') OR cuent.email like concat('%',?,'%') OR admmini.AdminTelefono like concat('%',?,'%')OR admmini.pais like concat('%',?,'%')) GROUP BY tar.cuenta ");
            $stmt->bindValue(1, $filtro, PDO::PARAM_STR);
            $stmt->bindValue(2, $filtro, PDO::PARAM_STR);
            $stmt->bindValue(3, $filtro, PDO::PARAM_STR);
            $stmt->bindValue(4, $filtro, PDO::PARAM_STR);
            $stmt->bindValue(5, $filtro, PDO::PARAM_STR);
            $stmt->execute();
            $datos = $stmt->fetchAll();
            $insBeanPagination->setCountFilter(count($datos));
            if (count($datos) > 0) {
                $stmt = $conexion->prepare("SELECT admmini.*,cuent.*,sum(CASE WHEN tar.estado = 0 THEN 1 ELSE 0 end) as totalestado,sum(tar.estado) as totalnoestado FROM `tarea` as tar inner join `administrador` as admmini ON tar.cuenta=admmini.Cuenta_Codigo inner join `cuenta` as cuent ON cuent.CuentaCodigo=admmini.Cuenta_Codigo left join `certificado` as cer ON cer.cuenta=admmini.Cuenta_Codigo WHERE cer.idcertificado is null and tar.tipo=0 and cuent.tipo=2 and cuent.idcuenta!=1 and (admmini.AdminNombre like concat('%',?,'%') OR admmini.AdminApellido like concat('%',?,'%') OR cuent.email like concat('%',?,'%') OR admmini.AdminTelefono like concat('%',?,'%')OR admmini.pais like concat('%',?,'%')) GROUP BY tar.cuenta ORDER BY max(tar.fecha) DESC LIMIT ?,? ");
                $stmt->bindValue(1, $filtro, PDO::PARAM_STR);
                $stmt->bindValue(2, $filtro, PDO::PARAM_STR);
                $stmt->bindValue(3, $filtro, PDO::PARAM_STR);
                $stmt->bindValue(4, $filtro, PDO::PARAM_STR);
                $stmt->bindValue(5, $filtro, PDO::PARAM_STR);
                $stmt->bindValue(6, $inicio, PDO::PARAM_INT);
                $stmt->bindValue(7, $registros, PDO::PARAM_INT);

                $stmt->execute();
                $datos = $stmt->fetchAll();

                foreach ($datos as $row) {

                    $insCliente = new Cliente();
                    $insCuenta = new Cuenta();
                    $insCuenta->setIdCuenta($row['idcuenta']);
                    $insCuenta->setCuentaCodigo($row['CuentaCodigo']);
                    //$insCuenta->setUsuario($row['usuario']);
                    //$insCuenta->setClave(mainModel::decryption($row['clave']));
                    $insCuenta->setEmail($row['email']);
                    $insCuenta->setEstado($row['estado']);
                    // $insCuenta->setTipo($row['tipo']);
                    $insCuenta->setFoto($row['foto']);
                    //$insCuenta->setPrecio($row['precio_curso']);
                    // $insCuenta->setVoucher($row['voucher']);

                    $insCliente->setIdCliente($row['id']);
                    $insCliente->setNombre($row['AdminNombre']);
                    $insCliente->setTelefono($row['AdminTelefono']);
                    $insCliente->setApellido($row['AdminApellido']);
                    //$insCliente->setOcupacion($row['AdminOcupacion']);
                    $insCliente->setPais($row['pais']);
                    $insCliente->setTarea(array("totalestado" => $row['totalestado'],
                        "totalnoestado" => $row['totalnoestado'],
                    ));
                    $insCliente->setCuenta($insCuenta->__toString());
                    $insBeanPagination->setList($insCliente->__toString());

                }

            }

            $stmt->closeCursor(); // this is not even required
            $stmt = null; // doing this is mandatory for connection to get closed

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";
        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";
        }
        return $insBeanPagination->__toString();

    }
    public function bean_paginador_tarea_cliente_controlador($pagina, $registros, $filtro)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $filtro = mainModel::limpiar_cadena($filtro);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_cliente_tarea_controlador($this->conexion_db, $inicio, $registros, $filtro));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function paginador_cliente_terminado_controlador($conexion, $inicio, $registros, $filtro)
    {
        $insBeanPagination = new BeanPagination();
        try {
            $contador = 0;
            $stmt = $conexion->prepare("SELECT COUNT(idtarea) AS CONTADOR FROM `tarea` WHERE tipo=0 ");
            $stmt->execute();
            $datos = $stmt->fetchAll();

            foreach ($datos as $row) {
                $insBeanPagination->setCountFilter($row['CONTADOR']);
                if ($row['CONTADOR'] > 0) {
                    $stmt = $conexion->prepare("SELECT admmini.*,cuent.*,sum(CASE WHEN tar.estado = 0 THEN 1 ELSE 0 end) as totalestado,sum(tar.estado) as totalnoestado FROM `tarea` as tar inner join `administrador` as admmini ON tar.cuenta=admmini.Cuenta_Codigo inner join `cuenta` as cuent ON cuent.CuentaCodigo=admmini.Cuenta_Codigo inner join `certificado` as cer ON cer.cuenta=admmini.Cuenta_Codigo WHERE  tar.tipo=0 and cuent.tipo=2 and cuent.idcuenta!=1 and (admmini.AdminNombre like concat('%',?,'%') OR admmini.AdminApellido like concat('%',?,'%') OR cuent.email like concat('%',?,'%') OR admmini.AdminTelefono like concat('%',?,'%')OR admmini.pais like concat('%',?,'%')) GROUP BY tar.cuenta ORDER BY max(tar.fecha) DESC LIMIT ?,? ");
                    $stmt->bindValue(1, $filtro, PDO::PARAM_STR);
                    $stmt->bindValue(2, $filtro, PDO::PARAM_STR);
                    $stmt->bindValue(3, $filtro, PDO::PARAM_STR);
                    $stmt->bindValue(4, $filtro, PDO::PARAM_STR);
                    $stmt->bindValue(5, $filtro, PDO::PARAM_STR);
                    $stmt->bindValue(6, $inicio, PDO::PARAM_INT);
                    $stmt->bindValue(7, $registros, PDO::PARAM_INT);

                    $stmt->execute();
                    $datos = $stmt->fetchAll();

                    foreach ($datos as $row) {
                        $contador++;
                        $insCliente = new Cliente();
                        $insCuenta = new Cuenta();
                        $insCuenta->setIdCuenta($row['idcuenta']);
                        $insCuenta->setCuentaCodigo($row['CuentaCodigo']);
                        $insCuenta->setEmail($row['email']);
                        $insCuenta->setEstado($row['estado']);
                        $insCuenta->setFoto($row['foto']);

                        $insCliente->setIdCliente($row['id']);
                        $insCliente->setNombre($row['AdminNombre']);
                        $insCliente->setTelefono($row['AdminTelefono']);
                        $insCliente->setApellido($row['AdminApellido']);
                        $insCliente->setPais($row['pais']);
                        $insCliente->setTarea(array("totalestado" => $row['totalestado'],
                            "totalnoestado" => $row['totalnoestado'],
                        ));
                        $insCliente->setCuenta($insCuenta->__toString());
                        $insBeanPagination->setList($insCliente->__toString());

                    }

                }
                $insBeanPagination->setCountFilter($contador);
            }

            $stmt->closeCursor(); // this is not even required
            $stmt = null; // doing this is mandatory for connection to get closed

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";
        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";
        }
        return $insBeanPagination->__toString();

    }
    public function bean_paginador_terminado_cliente_controlador($pagina, $registros, $filtro)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $pagina = mainModel::limpiar_cadena($pagina);
            $registros = mainModel::limpiar_cadena($registros);
            $filtro = mainModel::limpiar_cadena($filtro);
            $pagina = (isset($pagina) && $pagina > 0) ? (int) $pagina : 1;
            $inicio = ($pagina) ? (($pagina * $registros) - $registros) : 0;
            $insBeanCrud->setBeanPagination(self::paginador_cliente_terminado_controlador($this->conexion_db, $inicio, $registros, $filtro));

        } catch (Exception $th) {
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            $this->conexion_db = null;
        }
        return $insBeanCrud->__toString();
    }
    public function actualizar_datos_email_cliente_controlador($tipo, $Cliente, $Economico, $token)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();

            $Cliente->setTelefono(mainModel::limpiar_cadena($Cliente->getTelefono()));
            $Cliente->setOcupacion(mainModel::limpiar_cadena($Cliente->getOcupacion()));
            $Cliente->setNombre(mainModel::limpiar_cadena($Cliente->getNombre()));
            $Cliente->setApellido(mainModel::limpiar_cadena($Cliente->getApellido()));
            $Cliente->setPais(mainModel::limpiar_cadena($Cliente->getPais()));
            $insCuenta = new Cuenta();
            $insCuenta->setPrecio(mainModel::limpiar_cadena($Cliente->getCuenta()->precio));
            $insCuenta->setUsuario(mainModel::limpiar_cadena($Cliente->getCuenta()->usuario));
            $insCuenta->setEmail(mainModel::limpiar_cadena($Cliente->getCuenta()->email));
            $insCuenta->setEstado(mainModel::limpiar_cadena($Cliente->getCuenta()->estado));
            $insCuenta->setCuentaCodigo(mainModel::limpiar_cadena($Cliente->getCuenta()->codigo));
            $insCuenta->setClave($Cliente->getCuenta()->clave);
            $insCuenta->setVerificacion($Cliente->getCuenta()->cuentaverificacion);
            // $insCuenta->setEstado(0);
            // $insCuenta->setTipo(2);
            $clienteunico = clienteModelo::datos_cliente_modelo($this->conexion_db, "cuenta", $insCuenta);
            if ($clienteunico["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("no se encuentra el usuario");
            } else {
                if (isset($_FILES['txtImagenVoucher'])) {
                    $original = $_FILES['txtImagenVoucher'];
                    $nombre = $original['name'];
                    if ($original['error'] > 0) {
                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                    } else {
                        $resultado = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"), (5 * 1024), $original, $nombre, "./adjuntos/clientes/comprobante/");
                        if ($resultado != "") {
                            $insCuenta->setVoucher("");
                            $insCuenta->setFoto($clienteunico["list"][0]['cuenta']['foto']);
                            $insCuenta->setIdCuenta($clienteunico["list"][0]['cuenta']['idcuenta']);
                            $stmt = $this->conexion_db->prepare("SELECT COUNT(idcuenta) AS CONTADOR FROM `cuenta` WHERE email=? and idcuenta!=?");
                            $stmt->bindValue(1, $insCuenta->getEmail(), PDO::PARAM_STR);
                            $stmt->bindValue(2, $insCuenta->getIdCuenta(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                if ($row['CONTADOR'] == 0) {
                                    if ($insCuenta->getClave() == "") {
                                        $insCuenta->setClave($clienteunico["list"][0]['cuenta']['clave']);
                                    } else {
                                        $insCuenta->setClave(mainModel::encryption(mainModel::limpiar_cadena($insCuenta->getClave())));
                                    }
                                    $stmt = clienteModelo::actualizar_datos_cuenta_modelo($this->conexion_db, $insCuenta);
                                    if ($stmt->execute()) {
                                        $stmt = clienteModelo::actualizar_cliente_modelo($this->conexion_db, $Cliente);
                                        if ($stmt->execute()) {
                                            if ($clienteunico["list"][0]['cuenta']['voucher'] != "") {
                                                unlink('./adjuntos/clientes/comprobante/' . $clienteunico["list"][0]['cuenta']['voucher']);
                                            }

                                            $stmt = clienteModelo::agregar_historial_economico_modelo($this->conexion_db, $Cliente, $Economico, $resultado);
                                            if ($stmt->execute()) {

                                                $this->conexion_db->commit();
                                                $insBeanCrud->setMessageServer("ok");
                                                if ($tipo == 1) {
                                                    $insBeanCrud->setBeanPagination(self::paginador_cliente_controlador($this->conexion_db, 0, 5, $clienteunico["list"][0]['cuenta']['estado'], ""));
                                                } else {
                                                    $insCuenta = new Cuenta();
                                                    $insCuenta->setFoto($resultado);
                                                    $insBeanCrud->setBeanPagination($insCuenta->__toString());
                                                }
                                                $responsemensaje = self::enviar_mensaje_controlador($this->conexion_db, $Cliente, $token);

                                                $insBeanCrud->setMessageServer($responsemensaje['messageServer']);

                                            } else {
                                                $insBeanCrud->setMessageServer("No hemos podido registrar el historial economico");
                                            }

                                        } else {
                                            $insBeanCrud->setMessageServer("No hemos podido actualizar sus datos");
                                        }
                                    } else {
                                        $insBeanCrud->setMessageServer("No hemos podido actualizar al Usuario");
                                    }

                                } else {
                                    $insBeanCrud->setMessageServer("Ya se encuentra un usuario registrado con los datos, cambie de Correo Electrónico");
                                }
                            }
                        } else {
                            $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido");

                        }
                    }
                } else {
                    $insCuenta->setFoto($clienteunico["list"][0]['cuenta']['foto']);
                    $insCuenta->setVoucher($clienteunico["list"][0]['cuenta']['voucher']);
                    $insCuenta->setIdCuenta($clienteunico["list"][0]['cuenta']['idcuenta']);
                    $stmt = $this->conexion_db->prepare("SELECT COUNT(idcuenta) AS CONTADOR FROM `cuenta` WHERE email=? and idcuenta!=?");
                    $stmt->bindValue(1, $insCuenta->getEmail(), PDO::PARAM_STR);
                    $stmt->bindValue(2, $insCuenta->getIdCuenta(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        if ($row['CONTADOR'] == 0) {
                            if ($insCuenta->getClave() == "") {
                                $insCuenta->setClave($clienteunico["list"][0]['cuenta']['clave']);
                            } else {
                                $insCuenta->setClave(mainModel::encryption(mainModel::limpiar_cadena($insCuenta->getClave())));
                            }
                            $stmt = clienteModelo::actualizar_datos_cuenta_modelo($this->conexion_db, $insCuenta);
                            if ($stmt->execute()) {
                                $stmt = clienteModelo::actualizar_cliente_modelo($this->conexion_db, $Cliente);
                                if ($stmt->execute()) {

                                    $stmt = clienteModelo::agregar_historial_economico_modelo($this->conexion_db, $Cliente, $Economico, $insCuenta->getVoucher());
                                    if ($stmt->execute()) {
                                        $this->conexion_db->commit();
                                        $insBeanCrud->setMessageServer("ok");
                                        if ($tipo == 1) {
                                            $insBeanCrud->setBeanPagination(self::paginador_cliente_controlador($this->conexion_db, 0, 5, $clienteunico["list"][0]['cuenta']['estado'], ""));
                                        } else {
                                            $insCuenta = new Cuenta();
                                            $insCuenta->setFoto($clienteunico["list"][0]['cuenta']['foto']);
                                            $insBeanCrud->setBeanPagination($insCuenta->__toString());
                                        }
                                        $responsemensaje = self::enviar_mensaje_controlador($this->conexion_db, $Cliente, $token);
                                        $insBeanCrud->setMessageServer($responsemensaje['messageServer']);
                                    } else { $insBeanCrud->setMessageServer("No se registro el historial economico");}
                                } else {
                                    $insBeanCrud->setMessageServer("No hemos podido actualizar sus datos");
                                }
                            } else {
                                $insBeanCrud->setMessageServer("No hemos podido actualizar al Usuario");
                            }

                        } else {
                            $insBeanCrud->setMessageServer("Ya se encuentra un usuario registrado con los datos, cambie de Correo Electrónico");
                        }
                    }
                }

            }

        } catch (Exception $th) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
                $stmt = null;
            }
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());

    }
    public function actualizar_datos_tipo_inscripcion_cliente_controlador($tipo, $Cliente, $Economico)
    {
        $insBeanCrud = new BeanCrud();
        try {
            $this->conexion_db->beginTransaction();

            $Cliente->setTelefono(mainModel::limpiar_cadena($Cliente->getTelefono()));
            $Cliente->setOcupacion(mainModel::limpiar_cadena($Cliente->getOcupacion()));
            $Cliente->setNombre(mainModel::limpiar_cadena($Cliente->getNombre()));
            $Cliente->setApellido(mainModel::limpiar_cadena($Cliente->getApellido()));
            $Cliente->setPais(mainModel::limpiar_cadena($Cliente->getPais()));
            $insCuenta = new Cuenta();
            $insCuenta->setPrecio(mainModel::limpiar_cadena($Cliente->getCuenta()->precio));
            $insCuenta->setUsuario(mainModel::limpiar_cadena($Cliente->getCuenta()->usuario));
            $insCuenta->setEmail(mainModel::limpiar_cadena($Cliente->getCuenta()->email));
            $insCuenta->setEstado(mainModel::limpiar_cadena($Cliente->getCuenta()->estado));
            $insCuenta->setCuentaCodigo(mainModel::limpiar_cadena($Cliente->getCuenta()->codigo));
            $insCuenta->setClave($Cliente->getCuenta()->clave);
            // $insCuenta->setEstado(0);
            // $insCuenta->setTipo(2);
            $clienteunico = clienteModelo::datos_cliente_modelo($this->conexion_db, "cuenta", $insCuenta);
            if ($clienteunico["countFilter"] == 0) {
                $insBeanCrud->setMessageServer("no se encuentra el usuario");
            } else {
                if (isset($_FILES['txtImagenVoucher'])) {
                    $original = $_FILES['txtImagenVoucher'];
                    $nombre = $original['name'];
                    if ($original['error'] > 0) {
                        $insBeanCrud->setMessageServer("Ocurrio un error inesperado, Se encontro un error al subir el archivo, seleccione otra imagen");
                    } else {
                        $resultado = mainModel::archivo(array("image/png", "image/jpg", "image/jpeg"), (5 * 1024), $original, $nombre, "./adjuntos/clientes/comprobante/");
                        if ($resultado != "") {
                            $insCuenta->setVoucher("");
                            $insCuenta->setFoto($clienteunico["list"][0]['cuenta']['foto']);
                            $insCuenta->setIdCuenta($clienteunico["list"][0]['cuenta']['idcuenta']);
                            $stmt = $this->conexion_db->prepare("SELECT COUNT(idcuenta) AS CONTADOR FROM `cuenta` WHERE email=? and idcuenta!=?");
                            $stmt->bindValue(1, $insCuenta->getEmail(), PDO::PARAM_STR);
                            $stmt->bindValue(2, $insCuenta->getIdCuenta(), PDO::PARAM_INT);
                            $stmt->execute();
                            $datos = $stmt->fetchAll();
                            foreach ($datos as $row) {
                                if ($row['CONTADOR'] == 0) {
                                    if ($insCuenta->getClave() == "") {
                                        $insCuenta->setClave($clienteunico["list"][0]['cuenta']['clave']);
                                    } else {
                                        $insCuenta->setClave(mainModel::encryption(mainModel::limpiar_cadena($insCuenta->getClave())));
                                    }
                                    $stmt = clienteModelo::actualizar_datos_cuenta_modelo($this->conexion_db, $insCuenta);
                                    if ($stmt->execute()) {
                                        $stmt = clienteModelo::actualizar_cliente_modelo($this->conexion_db, $Cliente);
                                        if ($stmt->execute()) {
                                            if ($clienteunico["list"][0]['cuenta']['voucher'] != "") {
                                                unlink('./adjuntos/clientes/comprobante/' . $clienteunico["list"][0]['cuenta']['voucher']);
                                            }

                                            $stmt = clienteModelo::agregar_historial_economico_modelo($this->conexion_db, $Cliente, $Economico, $resultado);
                                            if ($stmt->execute()) {

                                                $this->conexion_db->commit();
                                                $insBeanCrud->setMessageServer("ok");
                                                if ($tipo == 1) {
                                                    $insBeanCrud->setBeanPagination(self::paginador_cliente_controlador($this->conexion_db, 0, 5, $clienteunico["list"][0]['cuenta']['estado'], ""));
                                                } else {
                                                    $insCuenta = new Cuenta();
                                                    $insCuenta->setFoto($resultado);
                                                    $insBeanCrud->setBeanPagination($insCuenta->__toString());
                                                }

                                            } else {
                                                $insBeanCrud->setMessageServer("No hemos podido registrar el historial economico");
                                            }

                                        } else {
                                            $insBeanCrud->setMessageServer("No hemos podido actualizar sus datos");
                                        }
                                    } else {
                                        $insBeanCrud->setMessageServer("No hemos podido actualizar al Usuario");
                                    }

                                } else {
                                    $insBeanCrud->setMessageServer("Ya se encuentra un usuario registrado con los datos, cambie de Correo Electrónico");
                                }
                            }
                        } else {
                            $insBeanCrud->setMessageServer("Hubo un error al guardar la imagen,formato no permitido o tamaño excedido");

                        }
                    }
                } else {
                    $insCuenta->setFoto($clienteunico["list"][0]['cuenta']['foto']);
                    $insCuenta->setVoucher($clienteunico["list"][0]['cuenta']['voucher']);
                    $insCuenta->setIdCuenta($clienteunico["list"][0]['cuenta']['idcuenta']);
                    $stmt = $this->conexion_db->prepare("SELECT COUNT(idcuenta) AS CONTADOR FROM `cuenta` WHERE email=? and idcuenta!=?");
                    $stmt->bindValue(1, $insCuenta->getEmail(), PDO::PARAM_STR);
                    $stmt->bindValue(2, $insCuenta->getIdCuenta(), PDO::PARAM_INT);
                    $stmt->execute();
                    $datos = $stmt->fetchAll();
                    foreach ($datos as $row) {
                        if ($row['CONTADOR'] == 0) {
                            if ($insCuenta->getClave() == "") {
                                $insCuenta->setClave($clienteunico["list"][0]['cuenta']['clave']);
                            } else {
                                $insCuenta->setClave(mainModel::encryption(mainModel::limpiar_cadena($insCuenta->getClave())));
                            }
                            $stmt = clienteModelo::actualizar_datos_cuenta_modelo($this->conexion_db, $insCuenta);
                            if ($stmt->execute()) {
                                $stmt = clienteModelo::actualizar_cliente_modelo($this->conexion_db, $Cliente);
                                if ($stmt->execute()) {

                                    $stmt = clienteModelo::agregar_historial_economico_modelo($this->conexion_db, $Cliente, $Economico, $insCuenta->getVoucher());
                                    if ($stmt->execute()) {
                                        $this->conexion_db->commit();
                                        $insBeanCrud->setMessageServer("ok");
                                        if ($tipo == 1) {
                                            $insBeanCrud->setBeanPagination(self::paginador_cliente_controlador($this->conexion_db, 0, 5, $clienteunico["list"][0]['cuenta']['estado'], ""));
                                        } else {
                                            $insCuenta = new Cuenta();
                                            $insCuenta->setFoto($clienteunico["list"][0]['cuenta']['foto']);
                                            $insBeanCrud->setBeanPagination($insCuenta->__toString());
                                        }

                                    } else { $insBeanCrud->setMessageServer("No se registro el historial economico");}
                                } else {
                                    $insBeanCrud->setMessageServer("No hemos podido actualizar sus datos");
                                }
                            } else {
                                $insBeanCrud->setMessageServer("No hemos podido actualizar al Usuario");
                            }

                        } else {
                            $insBeanCrud->setMessageServer("Ya se encuentra un usuario registrado con los datos, cambie de Correo Electrónico");
                        }
                    }
                }

            }

        } catch (Exception $th) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error!: " . $th->getMessage() . "<br/>";

        } catch (PDOException $e) {
            if ($this->conexion_db->inTransaction()) {
                $this->conexion_db->rollback();
            }
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        } finally {
            if (isset($stmt)) {
                $stmt->closeCursor();
                $stmt = null;
            }
            $this->conexion_db = null;
        }
        return json_encode($insBeanCrud->__toString());

    }
    public function enviar_mensaje_controlador($conexion, $Cliente, $token)
    {
        $insBeanCrud = new BeanCrud();
        $mail = new \PHPMailer\PHPMailer\PHPMailer();
        try {

            $Cliente->setTelefono(mainModel::limpiar_cadena($Cliente->getTelefono()));
            $Cliente->setOcupacion(mainModel::limpiar_cadena($Cliente->getOcupacion()));
            $Cliente->setNombre(mainModel::limpiar_cadena($Cliente->getNombre()));
            $Cliente->setApellido(mainModel::limpiar_cadena($Cliente->getApellido()));
            $Cliente->setPais(mainModel::limpiar_cadena($Cliente->getPais()));
            $insCuenta = new Cuenta();
            $insCuenta->setPrecio(mainModel::limpiar_cadena($Cliente->getCuenta()->precio));
            $insCuenta->setUsuario(mainModel::limpiar_cadena($Cliente->getCuenta()->usuario));
            $insCuenta->setEmail(mainModel::limpiar_cadena($Cliente->getCuenta()->email));
            $insCuenta->setEstado(mainModel::limpiar_cadena($Cliente->getCuenta()->estado));
            $insCuenta->setCuentaCodigo(mainModel::limpiar_cadena($Cliente->getCuenta()->codigo));
            $insCuenta->setClave($Cliente->getCuenta()->clave);
            $insCuenta->setVerificacion($Cliente->getCuenta()->cuentaverificacion);

            $empresa = clienteModelo::datos_cliente_modelo($conexion, 'empresa', 0);
            if ($empresa['countFilter'] > 0) {

                $ServerUrl = SERVERURL;
                // $to = $insCuenta->getEmail(); //EMAIL DESTINO
                // $from = $empresa['list'][0]['email']; //EMAIL  REMITENTE
                $name = $empresa['list'][0]['nombre']; //NOMBRE DE LA EMPRESA
                $subject = $insCuenta->getVerificacion();
                $VeriUrl = $ServerUrl . "auth/verification";
                $codigo = $subject;
                $subject = $subject . " – Tu Código de Verificación de " . $empresa['list'][0]['nombre']; //ASUNTO
                $Alumno = $Cliente->getNombre() . " " . $Cliente->getApellido();

                $mail->Host = "clpe5.com"; // Sets SMTP server
                $mail->SMTPDebug = 2; // 2 to enable SMTP debug information
                $mail->SMTPAuth = true; // enable SMTP authentication
                $mail->SMTPSecure = "tls"; //Secure conection
                $mail->Port = 465; // set the SMTP port
                $mail->Username = "club_lectura@clpe5.com"; // SMTP account username
                $mail->Password = "epLVP0)^w32C"; // SMTP account password
                $mail->Priority = 1; // Highest priority - Email priority (1 = High, 3 = Normal, 5 = low)
                $mail->CharSet = 'UTF-8';
                $mail->Encoding = '8bit';
                $mail->Subject = $subject;
                $mail->ContentType = 'text/html; charset=utf-8\r\n';
                $mail->From = "club_lectura@clpe5.com";
                $mail->FromName = $name;
                //$mail->WordWrap = 900; // RFC 2822 Compliant for Max 998 characters per line

                $mail->AddAddress($insCuenta->getEmail()); // To:
                $mail->isHTML(true);

                $message = "<table
                style='Margin:0;background:#e5e5e5!important;border-collapse:collapse;border-spacing:0;color:#e5e5e5;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;height:100%;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;width:100%'>
                <tbody>
                    <tr style='padding:0;text-align:left;vertical-align:top'>
                        <td align='center' valign='top'
                            style='Margin:0;border-collapse:collapse!important;color:#e5e5e5;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word'>
                            <center style='min-width:580px;width:100%'>
                                <table
                                    style='Margin:0 auto;border-collapse:collapse;border-spacing:0;float:none;margin:0 auto;padding:0;text-align:center;vertical-align:top;width:100%'>
                                    <tbody>
                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                            <td height='15px'
                                                style='Margin:0;border-collapse:collapse!important;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:10px;font-weight:400;line-height:10px;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word'>
                                                &nbsp;</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table align='center'
                                    style='Margin:0 auto;background:#fff;border-collapse:collapse;border-spacing:0;float:none;margin:0 auto;padding:10px;text-align:center;vertical-align:top;width:580px;margin-left:10px!important;margin-right:10px!important'>
                                    <tbody>
                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                            <td
                                                style='Margin:0;border-collapse:collapse!important;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word'>
                                                <table
                                                    style='background-color:#fff;background-image:none;background-position:top left;background-repeat:repeat;border-bottom:1px solid #efeef1;border-collapse:collapse;border-spacing:0;display:table;margin:10px 0 15px 0;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                    <tbody>
                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                            <th
                                                                style='Margin:0 auto;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0!important;padding-left:20px;padding-right:20px;padding-top:0!important;text-align:left;width:560px'>
                                                                <table
                                                                    style='border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                                    <tbody>
                                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left'>
                                                                                <a href='$ServerUrl'
                                                                                    style='Margin:0;color:#9147ff;font-family:Helvetica,Arial,sans-serif;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left;text-decoration:none'
                                                                                    target='_blank'><img
                                                                                        src='$ServerUrl/adjuntos/logoHeader.jpg'
                                                                                        alt='$name'
                                                                                        style='Margin:0 auto;border:none;border-bottom:1px solid #9147ff;clear:both;display:block;float:none;margin:0 auto;max-width:100%;outline:0;padding:25px 0;text-align:center;text-decoration:none;width:114px!important'
                                                                                        class='CToWUd'></a></th>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0!important;text-align:left;width:0'>
                                                                            </th>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </th>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table
                                                    style='border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                    <tbody>
                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                            <td height='15px'
                                                                style='Margin:0;border-collapse:collapse!important;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:15px;font-weight:400;line-height:15px;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word'>
                                                                &nbsp;</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table
                                                    style='border-collapse:collapse;border-spacing:0;display:table;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                    <tbody>
                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                            <th
                                                                style='Margin:0 auto;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:500;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0!important;padding-left:20px;padding-right:20px;padding-top:0!important;text-align:left;width:560px'>
                                                                <table
                                                                    style='border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                                    <tbody>
                                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:500;line-height:1.3;margin:0;padding:0;text-align:left'>
                                                                                <small>

                                                                                    <h6
                                                                                        style='Margin:0;Margin-bottom:10px;color:inherit;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:500;line-height:1.3;margin:0;margin-bottom:0;padding:0;padding-bottom:0;text-align:center;word-wrap:normal;color:#9147ff'>
                                                                                        Hola, $Alumno :</h6>
                                                                                </small>

                                                                            </th>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0!important;text-align:left;width:0'>
                                                                            </th>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </th>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table
                                                    style='border-collapse:collapse;border-spacing:0;display:table;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                    <tbody>
                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                            <th
                                                                style='Margin:0 auto;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:16px;padding-left:20px;padding-right:20px;padding-top:10px;text-align:left;width:560px'>
                                                                <p></p>
                                                                <table
                                                                    style='border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                                    <tbody>
                                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left'>
                                                                                <p style='Margin:0;Margin-bottom:10px;text-align: center;font-family:Helvetica,Arial,Verdana,'
                                                                                    Trebuchet
                                                                                    MS';font-size:16px;font-weight:300;line-height:24px;margin:0;margin-bottom:0;padding:0;padding-bottom:0;text-align:center'>
                                                                                    Por favor verifica tu cuenta de $name</p>
                                                                                <table
                                                                                    style='border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                                                    <tbody>
                                                                                        <tr
                                                                                            style='padding:0;text-align:left;vertical-align:top'>
                                                                                            <td height='20px'
                                                                                                style='Margin:0;border-collapse:collapse!important;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:10px;font-weight:400;line-height:10px;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word'>
                                                                                                &nbsp;</td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                                <center style='min-width:520px;width:100%'>
                                                                                    <table
                                                                                        style='Margin:0 0 16px 0;border-collapse:collapse;border-spacing:0;float:none;font-weight:600;margin:0 0 16px 0;margin-bottom:0;padding:0;padding-bottom:0;text-align:center;vertical-align:top;width:auto'>
                                                                                        <tbody>
                                                                                            <tr
                                                                                                style='padding:0;text-align:left;vertical-align:top'>
                                                                                                <td
                                                                                                    style='Margin:0;border-collapse:collapse!important;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:600;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word'>
                                                                                                    <table
                                                                                                        style='border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%;border:none'>
                                                                                                        <tbody>
                                                                                                            <tr
                                                                                                                style='padding:0;text-align:left;vertical-align:top'>
                                                                                                                <td
                                                                                                                    style='Margin:0;background:#9147ff;border:2px solid #9147ff;border-collapse:collapse!important;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:600;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word;border-radius:2px;overflow:hidden'>
                                                                                                    <a href='$VeriUrl?id=$token'
                                                                                                                        style='Margin:0;border:0 solid #9147ff;border-radius:3px;color:#fff;display:inline-block;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:600;line-height:1.3;margin:0;padding:10px 55px 10px 55px;text-align:left;text-decoration:none'
                                                                                                                        target='_blank'>Ir a la página</a>
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        </tbody>
                                                                                                    </table>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </center><br>
                                                                            </th>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.3;margin:0;padding:0!important;text-align:left;width:0'>
                                                                            </th>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </th>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table
                                                    style='border-collapse:collapse;border-spacing:0;border-top:#efeef1 1px solid;font-size:0;line-height:0;max-height:0;overflow:hidden;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                    <tbody>
                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                            <th
                                                                style='Margin:0 auto;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:16px;padding-left:20px;padding-right:20px;padding-top:27px;text-align:left;width:560px'>
                                                                <table
                                                                    style='border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                                    <tbody>
                                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:left'>
                                                                                <p
                                                                                    style='Margin:0;Margin-bottom:10px;font-family:Helvetica,Arial,Verdana,'Trebuchet MS';font-size:14px;font-weight:300;line-height:24px;margin:0;margin-bottom:0;padding:0;padding-bottom:10px;text-align:center'>
                                                                                    Ingresando este
                                                                                    código de
                                                                                    verificación:
                                                                                </p>
                                                                                <div
                                                                                    style='Margin:0;Margin-bottom:10px;color:#322f37;font-family:Helvetica,Arial,Verdana,'Trebuchet MS';font-size:24px;font-weight:400;line-height:24px;margin:0;margin-bottom:0;padding:0;padding-bottom:0;text-align:center;padding-top:5px'>
                                                                                    <p
                                                                                        style='background:#faf9fa;border:1px solid;border-style:solid;border-color:#dad8de;display:inline;padding-bottom:5px;padding-left:5px;padding-right:5px;padding-top:5px'>
                                                                                        $codigo</p>
                                                                                </div>
                                                                                <table
                                                                                    style='border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                                                    <tbody>
                                                                                        <tr
                                                                                            style='padding:0;text-align:left;vertical-align:top'>
                                                                                            <td height='10px'
                                                                                                style='Margin:0;border-collapse:collapse!important;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:10px;font-weight:400;line-height:10px;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word'>
                                                                                                &nbsp;
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                                <p></p>
                                                                            </th>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </th>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <table align='center'
                                    style='Margin:0 auto;background:0 0!important;border-collapse:collapse;border-spacing:0;float:none;margin:0 auto;padding:0;text-align:center;vertical-align:top;width:580px'>
                                    <tbody>
                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                            <td
                                                style='Margin:0;border-collapse:collapse!important;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:12px;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word'>
                                                <table
                                                    style='border-collapse:collapse;border-spacing:0;display:table;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                    <tbody>
                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                            <th
                                                                style='Margin:0 auto;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:12px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:16px;padding-left:20px;padding-right:20px;padding-top:28px;text-align:left;width:560px'>
                                                                <table
                                                                    style='border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                                    <tbody>
                                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:12px;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:left'>
                                                                            </th>
                                                                            <td align='center' valign='top'>
                                                                                <table
                                                                                    style='border-collapse:collapse;border-spacing:0;display:table;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                                                    <tbody>
                                                                                        <tr
                                                                                            style='padding:0;text-align:left;vertical-align:top'>
                                                                                            <th
                                                                                                style='Margin:0 auto;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:16px;padding-left:20px;padding-right:20px;padding-top:20px;text-align:left;width:560px'>
                                                                                                <p></p>
                                                                                                <p></p>

                                                                                            </th>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>

                                                                            </td>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0!important;text-align:left;width:0'>
                                                                            </th>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>

                                                            </th>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                            </center>
                        </td>
                    </tr>
                </tbody>
            </table>";

                $mail->Body = $message;
                $mail->AltBody = $message;
                //$mail->AddAttachment('images/phpmailer.gif');
                if ($mail->Send()) {
                    $insBeanCrud->setMessageServer('ok');
                } else {

                    $insBeanCrud->setMessageServer('El mensaje no se envió');
                }
                $mail->SmtpClose();
            } else {

            }

        } catch (phpmailerException $e) {
            echo $e->errorMessage();
        } catch (Exception $e) {
            echo $e->getMessa();

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        }
        return $insBeanCrud->__toString();
    }

    public function enviar_mensaje_clpe_controlador($conexion, $Cliente, $Cuenta)
    {
        $insBeanCrud = false;
        $mail = new \PHPMailer\PHPMailer\PHPMailer();
        try {

            $empresa = clienteModelo::datos_cliente_modelo($conexion, 'empresa', 0);
            if ($empresa['countFilter'] > 0) {
                $name = $Cliente->getNombre() . " " . $Cliente->getApellido();
                $correo = $Cuenta->getEmail();
                $telefono = $Cliente->getTelefono();
                $VeriUrl = SERVERURL . "app/clientes/inactivo";
                //$to = "llontopdiazandres@gmail.com";
                $to = $empresa['list'][0]['email']; //EMAIL  DESTINO
                // $name = $empresa['list'][0]['nombre']; //NOMBRE DE LA EMPRESA
                $subject = "REGISTRO DE USUARIO CLPE"; //ASUNTO

                $mail->Host = "clpe5.com"; // Sets SMTP server
                $mail->SMTPDebug = 2; // 2 to enable SMTP debug information
                $mail->SMTPAuth = true; // enable SMTP authentication
                $mail->SMTPSecure = "tls"; //Secure conection
                $mail->Port = 465; // set the SMTP port
                $mail->Username = "club_lectura@clpe5.com"; // SMTP account username
                $mail->Password = "epLVP0)^w32C"; // SMTP account password
                $mail->Priority = 1; // Highest priority - Email priority (1 = High, 3 = Normal, 5 = low)
                $mail->CharSet = 'UTF-8';
                $mail->Encoding = '8bit';
                $mail->Subject = $subject;
                $mail->ContentType = 'text/html; charset=utf-8\r\n';
                $mail->From = "club_lectura@clpe5.com";
                $mail->FromName = $name;
                //$mail->WordWrap = 900; // RFC 2822 Compliant for Max 998 characters per line

                $mail->AddAddress($to); // To:
                $mail->isHTML(true);

                $message = "<table
                style='Margin:0;background:#e5e5e5!important;border-collapse:collapse;border-spacing:0;color:#e5e5e5;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;height:100%;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;width:100%'>
                <tbody>
                    <tr style='padding:0;text-align:left;vertical-align:top'>
                        <td align='center' valign='top'
                            style='Margin:0;border-collapse:collapse!important;color:#e5e5e5;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word'>
                            <center style='min-width:580px;width:100%'>
                                <table
                                    style='Margin:0 auto;border-collapse:collapse;border-spacing:0;float:none;margin:0 auto;padding:0;text-align:center;vertical-align:top;width:100%'>
                                    <tbody>
                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                            <td height='15px'
                                                style='Margin:0;border-collapse:collapse!important;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:10px;font-weight:400;line-height:10px;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word'>
                                                &nbsp;</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table align='center'
                                    style='Margin:0 auto;background:#fff;border-collapse:collapse;border-spacing:0;float:none;margin:0 auto;padding:10px;text-align:center;vertical-align:top;width:580px;margin-left:10px!important;margin-right:10px!important'>
                                    <tbody>
                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                            <td
                                                style='Margin:0;border-collapse:collapse!important;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word'>
                                                <table
                                                    style='background-color:#fff;background-image:none;background-position:top left;background-repeat:repeat;border-bottom:1px solid #efeef1;border-collapse:collapse;border-spacing:0;display:table;margin:10px 0 15px 0;padding:0;text-align:left;vertical-align:top;width:100%'>

                                                </table>
                                                <table
                                                    style='border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                    <tbody>
                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                            <td height='15px'
                                                                style='Margin:0;border-collapse:collapse!important;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:15px;font-weight:400;line-height:15px;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word'>
                                                                &nbsp;</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table
                                                    style='border-collapse:collapse;border-spacing:0;display:table;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                    <tbody>
                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                            <th
                                                                style='Margin:0 auto;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:500;line-height:1.3;margin:0 auto;padding:0;padding-bottom:0!important;padding-left:20px;padding-right:20px;padding-top:0!important;text-align:left;width:560px'>
                                                                <table
                                                                    style='border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                                    <tbody>
                                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:500;line-height:1.3;margin:0;padding:0;text-align:left'>
                                                                                <small>

                                                                                    <h6
                                                                                        style='Margin:0;Margin-bottom:10px;color:inherit;font-family:Helvetica,Arial,sans-serif;font-size:18px;font-weight:500;line-height:1.3;margin:0;margin-bottom:0;padding:0;padding-bottom:0;text-align:center;word-wrap:normal;color:#9147ff'>
                                                                                        CLUB DE LECTURA PARA EMPRENDEDORES
                                                                                    </h6>
                                                                                </small>

                                                                            </th>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0!important;text-align:left;width:0'>
                                                                            </th>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </th>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table
                                                    style='border-collapse:collapse;border-spacing:0;display:table;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                    <tbody>
                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                            <th
                                                                style='Margin:0 auto;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:16px;padding-left:20px;padding-right:20px;padding-top:10px;text-align:left;width:560px'>
                                                                <p></p>
                                                                <table
                                                                    style='border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                                    <tbody>
                                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0;text-align:left'>
                                                                                <p
                                                                                    style='Margin:0;Margin-bottom:10px;text-align: left;font-family:Helvetica,Arial,Verdana;font-size:16px;font-weight:bold;line-height:24px;margin:0;margin-bottom:0;padding:0;padding-bottom:0;'>
                                                                                    Se registró un nuevo usuario, verifica su
                                                                                    voucher del proceso de compra.</p>
                                 <p style='Margin:0;Margin-bottom:10px;text-align: left;font-family:Helvetica,Arial,Verdana;font-size:16px;font-weight:300;line-height:24px;margin:0;margin-bottom:0;padding:0;padding-bottom:0;'>
                                        Nombre Completo: <span>$name</span></p>
                                                                                <p
                                                                                    style='Margin:0;Margin-bottom:10px;text-align: left;font-family:Helvetica,Arial,Verdana;font-size:16px;font-weight:300;line-height:24px;margin:0;margin-bottom:0;padding:0;padding-bottom:0;'>
                                        Email: <span>$correo</span></p>
                                                                                <p
                                                                                    style='Margin:0;Margin-bottom:10px;text-align: left;font-family:Helvetica,Arial,Verdana;font-size:16px;font-weight:300;line-height:24px;margin:0;margin-bottom:0;padding:0;padding-bottom:0;'>
                                Teléfono: <span>$telefono</span></p>
                                                                                <table
                                                                                    style='border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                                                    <tbody>
                                                                                        <tr
                                                                                            style='padding:0;text-align:left;vertical-align:top'>
                                                                                            <td height='20px'
                                                                                                style='Margin:0;border-collapse:collapse!important;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:10px;font-weight:400;line-height:10px;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word'>
                                                                                                &nbsp;</td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                                <center style='min-width:520px;width:100%'>
                                                                                    <table
                                                                                        style='Margin:0 0 16px 0;border-collapse:collapse;border-spacing:0;float:none;font-weight:600;margin:0 0 16px 0;margin-bottom:0;padding:0;padding-bottom:0;text-align:center;vertical-align:top;width:auto'>
                                                                                        <tbody>
                                                                                            <tr
                                                                                                style='padding:0;text-align:left;vertical-align:top'>
                                                                                                <td
                                                                                                    style='Margin:0;border-collapse:collapse!important;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:600;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word'>
                                                                                                    <table
                                                                                                        style='border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%;border:none'>
                                                                                                        <tbody>
                                                                                                            <tr
                                                                                                                style='padding:0;text-align:left;vertical-align:top'>
                                                                                                                <td
                                                                                                                    style='Margin:0;background:#9147ff;border:2px solid #9147ff;border-collapse:collapse!important;color:#fff;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:600;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word;border-radius:2px;overflow:hidden'>
                                                                                                                    <a href='$VeriUrl'
                                                                                                                        style='Margin:0;border:0 solid #9147ff;border-radius:3px;color:#fff;display:inline-block;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:600;line-height:1.3;margin:0;padding:10px 55px 10px 55px;text-align:left;text-decoration:none'
                                                                                                                        target='_blank'>Ir
                                                                                                                        a la
                                                                                                                        página</a>
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        </tbody>
                                                                                                    </table>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </center><br>
                                                                            </th>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:1.3;margin:0;padding:0!important;text-align:left;width:0'>
                                                                            </th>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </th>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <table align='center'
                                    style='Margin:0 auto;background:0 0!important;border-collapse:collapse;border-spacing:0;float:none;margin:0 auto;padding:0;text-align:center;vertical-align:top;width:580px'>
                                    <tbody>
                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                            <td
                                                style='Margin:0;border-collapse:collapse!important;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:12px;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:break-word'>
                                                <table
                                                    style='border-collapse:collapse;border-spacing:0;display:table;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                    <tbody>
                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                            <th
                                                                style='Margin:0 auto;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:12px;font-weight:300;line-height:1.3;margin:0 auto;padding:0;padding-bottom:16px;padding-left:20px;padding-right:20px;padding-top:28px;text-align:left;width:560px'>
                                                                <table
                                                                    style='border-collapse:collapse;border-spacing:0;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                                    <tbody>
                                                                        <tr style='padding:0;text-align:left;vertical-align:top'>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:12px;font-weight:300;line-height:1.3;margin:0;padding:0;text-align:left'>
                                                                            </th>
                                                                            <td align='center' valign='top'>
                                                                                <table
                                                                                    style='border-collapse:collapse;border-spacing:0;display:table;padding:0;text-align:left;vertical-align:top;width:100%'>
                                                                                    <tbody>
                                                                                        <tr
                                                                                            style='padding:0;text-align:left;vertical-align:top'>
                                                                                            <th
                                                                                                style='Margin:0 auto;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0 auto;padding:0;padding-bottom:16px;padding-left:20px;padding-right:20px;padding-top:20px;text-align:left;width:560px'>
                                                                                                <p></p>
                                                                                                <p></p>

                                                                                            </th>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>

                                                                            </td>
                                                                            <th
                                                                                style='Margin:0;color:#322f37;font-family:Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:1.3;margin:0;padding:0!important;text-align:left;width:0'>
                                                                            </th>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>

                                                            </th>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                            </center>
                        </td>
                    </tr>
                </tbody>
            </table>";

                $mail->Body = $message;
                $mail->AltBody = $message;
                //$mail->AddAttachment('images/phpmailer.gif');
                if ($mail->Send()) {
                    $insBeanCrud = true;
                }
                $mail->SmtpClose();
            }

        } catch (phpmailerException $e) {
            echo $e->errorMessage();
        } catch (Exception $e) {
            echo $e->getMessa();

        } catch (PDOException $e) {
            print "¡Error Processing Request!: " . $e->getMessage() . "<br/>";

        }
        return $insBeanCrud;
    }
}
