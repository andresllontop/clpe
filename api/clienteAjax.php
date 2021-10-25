<?php
require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();
if (!empty($RESULTADO_token)) {
    require_once './classes/principal/cliente.php';
    require_once './classes/principal/cuenta.php';

    require_once './controladores/clienteControlador.php';
    $inscliente = new clienteControlador();
    $accion = $RESULTADO_token->accion;
    if (isset($RESULTADO_token->tipo)) {
        if ($RESULTADO_token->tipo == 1) {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    if ($accion == "add") {
                        $personData = json_decode($_POST['class']);
                        $insClienteClass = new Cliente();
                        $insClienteClass->setNombre($personData->nombre);
                        $insClienteClass->setApellido($personData->apellido);
                        $insClienteClass->setTelefono($personData->telefono);
                        $insClienteClass->setOcupacion($personData->ocupacion);
                        $insClienteClass->setPais($personData->pais);
                        $insClienteClass->setCuenta($personData->cuenta);
                        $insClienteClass->setVendedor($personData->vendedor);
                        $insClienteClass->setTipoMedio($personData->tipomedio);

                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');

                        echo $inscliente->agregar_cliente_controlador(1, $insClienteClass);
                    } else if ($accion == "add-libro") {
                        $personData = json_decode($_POST['class']);
                        $insClienteClass = new Cliente();
                        $insClienteClass->setTipoMedio($personData->email);
                        $insClienteClass->setVendedor($personData->libro);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $inscliente->agregar_cliente_libro_controlador(1, $insClienteClass);
                    } else if ($accion == "update") {

                        require_once './classes/principal/empresa.php';

                        $personData = json_decode($_POST['class']);
                        $insClienteClass = new Cliente();
                        $insClienteClass->setIdCliente($personData->idcliente);
                        $insClienteClass->setNombre($personData->nombre);
                        $insClienteClass->setApellido($personData->apellido);
                        $insClienteClass->setTelefono($personData->telefono);
                        $insClienteClass->setOcupacion($personData->ocupacion);
                        $insClienteClass->setPais($personData->pais);
                        $insClienteClass->setCuenta($personData->cuenta);

                        if (isset($personData->tipo_inscripcion)) {

                            if ((int) $personData->tipo_inscripcion == 1) {

                                echo ($inscliente->actualizar_datos_tipo_inscripcion_cliente_controlador(1, $insClienteClass, $personData->economico));
                            } else {
                                require_once './plugins/PHPMailer/src/PHPMailer.php';
                                require_once './plugins/PHPMailer/src/SMTP.php';
                                require_once './plugins/PHPMailer/src/Exception.php';
                                require_once './classes/principal/usuario.php';
                                require_once './api/security/auth.php';
                                $insToken = new Auth();
                                $insUser = new Usuario();
                                $insUser->setId($personData->cuenta->idcuenta);
                                $insUser->setUsuario($personData->cuenta->usuario);
                                $insUser->setEmail($personData->cuenta->email);
                                $insUser->setTipo(2);
                                $insUser->setCodigo($personData->cuenta->codigo);
                                $respuestaToken = $insToken->autenticar($insUser);
                                header("HTTP/1.1 200");
                                header('Content-Type: application/json; charset=utf-8');

                                echo ($inscliente->actualizar_datos_email_cliente_controlador(1, $insClienteClass, $personData->economico, $respuestaToken['token']));
                            }
                        } else {
                            require_once './plugins/PHPMailer/src/PHPMailer.php';
                            require_once './plugins/PHPMailer/src/SMTP.php';
                            require_once './plugins/PHPMailer/src/Exception.php';
                            require_once './classes/principal/usuario.php';
                            require_once './api/security/auth.php';
                            $insToken = new Auth();
                            $insUser = new Usuario();
                            $insUser->setId($personData->cuenta->idcuenta);
                            $insUser->setUsuario($personData->cuenta->usuario);
                            $insUser->setEmail($personData->cuenta->email);
                            $insUser->setTipo(2);
                            $insUser->setCodigo($personData->cuenta->codigo);
                            $respuestaToken = $insToken->autenticar($insUser);
                            header("HTTP/1.1 200");
                            header('Content-Type: application/json; charset=utf-8');

                            echo ($inscliente->actualizar_datos_email_cliente_controlador(1, $insClienteClass, $personData->economico, $respuestaToken['token']));
                        }

                    } else if ($accion == "updateestado") {

                        $personData = json_decode($_POST['class']);
                        $insCuentaClass = new Cuenta();
                        $insCuentaClass->setIdCuenta($personData->idcuenta);
                        $insCuentaClass->setCuentaCodigo($personData->codigo);
                        $insCuentaClass->setEstado($personData->estado);
                        //libro
                        $insCuentaClass->setClave($personData->libro);

                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $inscliente->actualizar_cuenta_estado_controlador($insCuentaClass);
                    } else if ($accion == "updateestadocliente") {

                        $personData = json_decode($_POST['class']);
                        $insClienteClass = new Cliente();
                        $insClienteClass->setIdCliente($personData->idcliente);
                        $insClienteClass->setEstado($personData->estado);
                        //libro
                        $insClienteClass->setvendedor($personData->libro);

                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $inscliente->actualizar_cliente_estado_controlador($insClienteClass);
                    } else {
                        header("HTTP/1.1 500");
                    }

                    break;

                case 'GET':
                    if ($accion == "delete") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        $insClienteClass = new Cliente();
                        $insClienteClass->setIdCliente($_GET['id']);
                        $insClienteClass->setTipoMedio($_GET['libro']);
                        $insClienteClass->setEstado($_GET['estado']);
                        echo $inscliente->eliminar_cliente_controlador($insClienteClass);
                    } else if ($accion == "paginate") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($inscliente->bean_paginador_cliente_controlador($_GET['pagina'], $_GET['registros'], $_GET['estado'], $_GET['filtro'], $_GET['libro']));
                    } else if ($accion == "tarea") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($inscliente->bean_paginador_tarea_cliente_controlador($_GET['pagina'], $_GET['registros'], $_GET['filtro'], $_GET['libro']));
                    } else if ($accion == "terminado") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($inscliente->bean_paginador_terminado_cliente_controlador($_GET['pagina'], $_GET['registros'], $_GET['filtro'], $_GET['libro']));
                    } else if ($accion == "obtener") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($inscliente->datos_reporte_controlador("tipo", 0));
                    } else if ($accion == "libroreport") {
                        $insCuentaClass = new Cuenta();
                        $insCuentaClass->setCuentaCodigo($_GET['libro']);
                        header("Content-Type: application/vnd.ms-excel; charset=UTF-16LE");
                        header("Expires: 0");
                        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                        header("Cache-Control: private", false);
                        echo mb_convert_encoding($inseconomico->reporte_cliente_libro_controlador("cliente-libro", $insCuentaClass), 'UTF-16LE', 'UTF-8');
                    } else if ($accion == "reporte") {
                        $insCuentaClass = new Cuenta();
                        $insCuentaClass->setTipo(2);
                        $insCuentaClass->setEstado($_GET['estado']);
                        $insClienteClass = new Cliente();
                        $insClienteClass->setCuenta($insCuentaClass);
                        header("Content-Type: application/vnd.ms-excel; charset=UTF-16LE");
                        header("Expires: 0");
                        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                        header("Cache-Control: private", false);
                        echo mb_convert_encoding($inscliente->reporte_cliente_controlador("tipo-cuenta", $insClienteClass), 'UTF-16LE', 'UTF-8');
                    } else {
                        header("HTTP/1.1 500");
                    }
                    break;
                default:

                    header("HTTP/1.1 404");

                    break;
            }

        } elseif ($RESULTADO_token->tipo == 2) {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    if ($accion == "update") {

                        $personData = json_decode($_POST['class']);
                        $insClienteClass = new Cliente();
                        $insClienteClass->setNombre($personData->nombre);
                        $insClienteClass->setApellido($personData->apellido);
                        $insClienteClass->setTelefono($personData->telefono);
                        $insClienteClass->setOcupacion($personData->ocupacion);
                        $insClienteClass->setPais($personData->pais);
                        $insCuenta = new Cuenta();
                        $insCuenta->setUsuario($personData->cuenta->usuario);
                        $insCuenta->setEmail($personData->cuenta->email);
                        $insCuenta->setClave($personData->cuenta->clave);
                        $insCuenta->setCuentaCodigo($RESULTADO_token->codigo);
                        $insClienteClass->setCuenta($insCuenta->__toString());
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $inscliente->actualizar_cliente_controlador(0, $insClienteClass);
                    } else {
                        header("HTTP/1.1 500");
                    }
                    break;
                case 'GET':
                    if ($accion == "obtener") {
                        $insClienteClass = new Cliente();
                        $insClienteClass->setCuenta($RESULTADO_token->codigo);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($inscliente->datos_cliente_controlador("perfil", $insClienteClass));
                    } else {
                        header("HTTP/1.1 500");
                    }
                    break;
                default:
                    header("HTTP/1.1 404");
                    break;
            }
        } else {
            return header("HTTP/1.1 403");
        }

    } else {
        return header("HTTP/1.1 403");
    }

} else {
    return header("HTTP/1.1 403");
}
