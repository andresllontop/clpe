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
                        // Cargamos Requests y Culqi PHP
                        require './plugins/plugins-culqui/requests/library/Requests.php';
                        Requests::register_autoloader();
                        require './plugins/plugins-culqui/culqi/lib/culqi.php';
                        require_once './controladores/empresaControlador.php';

                        $insempresa = new empresaControlador();
                        // Configurar tu API Key y autenticación
                        //$SECRET_KEY = "sk_live_d9a2e00a6758a93a";
                        $SECRET_KEY = "sk_test_JNrOjdt65NMO8Eef";
                        $culqi = new Culqi\Culqi(array('api_key' => $SECRET_KEY));

                        $compraData = json_decode($_POST['class']);
                        $empresa = $insempresa->datos_empresa_controlador("conteo-publico", 0)['beanPagination']['list']['0'];
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        $respuestaValidar = json_decode(ValidaUsuario($compraData));
                        if ($respuestaValidar->messageServer == "ok") {
                            // Creando Cargo a una tarjeta
                            $charge = $culqi->Charges->create(
                                array(
                                    //"amount" => $empresa['precio'] * 100,
                                    "amount" => $compraData->precio * 100,
                                    "antifraud_details" => array(
                                        "country_code" => $compraData->country_code,
                                        "first_name" => $compraData->nombre,
                                        "last_name" => $compraData->apellido,
                                        "phone_number" => $compraData->telefono,
                                    ),
                                    "capture" => true,
                                    "currency_code" => $compraData->currency,
                                    "description" => "Venta en Producción",
                                    "installments" => 0,
                                    "email" => $compraData->email,
                                    "source_id" => $compraData->token,
                                )
                            );

                            $repuestaCulqi = json_decode(json_encode($charge));

                            /*
                            print_r($repuestaCulqi);
                            //MONTO A DEPOSITAR
                            echo ($repuestaCulqi->transfer_amount) / 100;
                            // MONTO DE COBRO DE CULQI
                            echo ($repuestaCulqi->total_fee) / 100;
                            //MONTO DE COMPRA
                            echo ($repuestaCulqi->current_amount) / 100;
                            //TIPO DE MONEDA
                            echo ($repuestaCulqi->currency_code);
                            //nOMBRE DEL BANCO
                            echo ($repuestaCulqi->source->iin->issuer->name);
                            //date
                            echo 'DATE:' . date('d/m/Y H:i:s', intval($repuestaCulqi->capture_date/1000)) . PHP_EOL;
                             */
                            CreateUsuario($compraData, json_decode(json_encode(array(
                                "nombre_banco" => $repuestaCulqi->source->iin->issuer->name,
                                "comision" => ($repuestaCulqi->total_fee) / 100,
                                "moneda" => $repuestaCulqi->currency_code,
                                "precio" => ($repuestaCulqi->current_amount) / 100,
                                "tipo" => 1,
                                "fecha" => date('Y-m-d H:i:s', intval(($repuestaCulqi->capture_date) / 1000)))
                            )));
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
        header("HTTP/1.1 403");
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
