<?php
/**
 * Ejemplo 2
 * Como crear un charge a una tarjeta usando Culqi PHP.
 */

try {

    $values_path = $_SERVER['REDIRECT_URL'];
    //HACEMOS UN SPLIT PARA DEJAR EL PATH SIN PARAMETROS
    $values_path = explode("/", $values_path);
    $accion = $values_path[sizeof($values_path) - 1];
    if (isset($_SERVER['CONTENT_TYPE'])) {
        if (preg_match('/multipart\/form-data/i', $_SERVER['CONTENT_TYPE'])) {
            require_once './classes/principal/empresa.php';
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    if ($accion == "charge") {

                        require_once './controladores/empresaControlador.php';
                        require_once './core/functions.php';
                        $insempresa = new empresaControlador();

                        $compraData = json_decode($_POST['class']);
                        $empresa = $insempresa->datos_empresa_controlador("conteo-publico", 0)['beanPagination']['list']['0'];
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        $respuestaValidar = json_decode(ValidaUsuario($compraData));
                        if ($respuestaValidar->messageServer == "ok") {
                            $authorization = generateAuthorization($empresa['precio'], $compraData->purchase, $compraData->transactionToken, generateToken());

                            if (isset($authorization->errorCode)) {
                                $respuestaValidar->messageServer = 'Pago denegado: ' . $authorization->data->ACTION_DESCRIPTION . ' -- Fecha :' . date('Y-m-d H:i:s', intval(($authorization->data->TRANSACTION_DATE) / 1000)) . ' -- N° Pedido :' . $authorization->data->TRACE_NUMBER;
                                echo (json_encode($respuestaValidar));
                            } else {
                                if (isset($authorization->dataMap)) {
                                    if ($authorization->dataMap->ACTION_CODE == "000") {
                                        CreateUsuario($compraData, json_decode(json_encode(array(
                                            "nombre_banco" => $authorization->dataMap->BRAND,
                                            //"comision" => (($authorization->dataMap->AMOUNT) * 26.05) / 100,
                                            "comision" => 0,
                                            "moneda" => $authorization->order->currency,
                                            "precio" => $authorization->dataMap->AMOUNT,
                                            "tipo" => 1,
                                            "requestNiubiz" => $authorization,
                                            "fecha" => date('Y-m-d H:i:s', intval(($authorization->dataMap->TRANSACTION_DATE) / 1000)),
                                        )
                                        )));

                                    }
                                } else if (isset($authorization->data)) {

                                    if ($authorization->data->ACTION_CODE != "000") {
                                        $respuestaValidar->messageServer = 'Pago denegado: ' . $authorization->data->ACTION_DESCRIPTION . ' -- Fecha :' . date('Y-m-d H:i:s', intval(($authorization->data->TRANSACTION_DATE) / 1000)) . ' -- N° Pedido :' . $authorization->data->TRACE_NUMBER;
                                        echo (json_encode($respuestaValidar));
                                    }
                                }
                            }

                        } else {
                            echo (json_encode($respuestaValidar));

                        }

                    } elseif ($accion == "clasico") {

                        require_once './plugins/PHPMailer/src/PHPMailer.php';
                        require_once './plugins/PHPMailer/src/SMTP.php';
                        require_once './plugins/PHPMailer/src/Exception.php';
                        $compraData = json_decode($_POST['class']);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        $respuestaValidar = json_decode(ValidaUsuario($compraData));
                        if ($respuestaValidar->messageServer == "ok") {
                            $inscliente = new clienteControlador();
                            $insClienteClass = new Cliente();

                            $insClienteClass->setNombre($compraData->nombre);
                            $insClienteClass->setApellido($compraData->apellido);
                            $insClienteClass->setTelefono($compraData->telefono);
                            $insClienteClass->setOcupacion($compraData->profesion);
                            $insClienteClass->setPais($compraData->pais);
                            $insClienteClass->setVendedor($compraData->vendedor);
                            $insClienteClass->setTipoMedio($compraData->tipomedio);
                            $insCuentaClass = new Cuenta();
                            $insCuentaClass->setEmail($compraData->address);
                            $insCuentaClass->setUsuario($compraData->nombre);
                            $insCuentaClass->setClave($compraData->pass);
                            $insCuentaClass->setPrecio(0);
                            $insClienteClass->setCuenta((object) $insCuentaClass->__toString());
                            echo $inscliente->agregar_publico_cliente_otro_medio_controlador($insClienteClass);
                        } else {
                            echo (json_encode($respuestaValidar));
                        }

                    } else {
                        header("HTTP/1.1 500");
                    }

                    break;
            }
        } else {
            header("HTTP/1.1 500");
        }
    } else {

        header("HTTP/1.1 500");

    }

} catch (Exception $e) {
    echo json_encode($e->getMessage());
}

function CreateUsuario($Data, $repuestaCulqi)
{
    if (isset($Data->telefono) && isset($Data->address) && isset($Data->nombre) && isset($Data->apellido) && isset($Data->profesion) && isset($Data->pass) && isset($Data->pais)) {
        //
        require_once './plugins/PHPMailer/src/PHPMailer.php';
        require_once './plugins/PHPMailer/src/SMTP.php';
        require_once './plugins/PHPMailer/src/Exception.php';
        require_once './classes/principal/usuario.php';
        require_once './api/security/auth.php';
        //
        $inscliente = new clienteControlador();
        $insClienteClass = new Cliente();
        $insClienteClass->setNombre($Data->nombre);
        $insClienteClass->setApellido($Data->apellido);
        $insClienteClass->setTelefono($Data->telefono);
        $insClienteClass->setOcupacion($Data->profesion);
        $insClienteClass->setPais($Data->pais);
        $insCuentaClass = new Cuenta();
        $insCuentaClass->setEmail($Data->address);
        $insCuentaClass->setUsuario($Data->nombre);
        $insCuentaClass->setClave($Data->pass);
        $insCuentaClass->setPrecio($Data->precio);
        $insClienteClass->setCuenta((object) $insCuentaClass->__toString());
        //
        echo $inscliente->agregar_publico_culqui_cliente_controlador($insClienteClass, $repuestaCulqi);
    } else {
        header("HTTP/1.1 401");
    }
}
function ValidaUsuario($Data)
{
    if (isset($Data->telefono) && isset($Data->address) && isset($Data->nombre) && isset($Data->apellido) && isset($Data->profesion) && isset($Data->pass) && isset($Data->pais)) {
        require_once './core/configAPP.php';
        require_once './classes/principal/cliente.php';
        require_once './classes/principal/cuenta.php';
        require_once './controladores/clienteControlador.php';

        //
        $inscliente = new clienteControlador();
        $insClienteClass = new Cliente();
        $insClienteClass->setNombre($Data->nombre);
        $insClienteClass->setApellido($Data->apellido);
        $insClienteClass->setTelefono($Data->telefono);
        $insClienteClass->setOcupacion($Data->profesion);
        $insClienteClass->setPais($Data->pais);
        $insCuentaClass = new Cuenta();
        $insCuentaClass->setEmail($Data->address);
        $insCuentaClass->setUsuario($Data->nombre);
        $insCuentaClass->setClave($Data->pass);
        $insCuentaClass->setPrecio($Data->precio);
        $insClienteClass->setCuenta((object) $insCuentaClass->__toString());
        //
        return $inscliente->validar_cliente_controlador($insClienteClass);

    } else {
        return json_encode(array("messageServer" => "no tienes acceso",
            "beanPagination" => null));
    }
}
