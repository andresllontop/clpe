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
        if (preg_match('/application\/json/i', $_SERVER['CONTENT_TYPE'])) {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    if ($accion == "sesion") {
                        if (isset($_GET['amount']) && isset($_GET['channel'])) {
                            require_once './core/functions.php';

                            $amount = $_GET['amount'];
                            $channel = $_GET['channel'];

                            $token = generateToken();
                            $sesion = generateSesion($amount, $token, $channel);
                            if (isset($sesion['sessionMessage'])) {
                                $data = array(
                                    "sessionMessage" => $sesion['sessionMessage'],
                                );
                            } else {
                                $purchaseNumber = generatePurchaseNumber();
                                header("HTTP/1.1 200");
                                header('Content-Type: application/json; charset=utf-8');
                                $data = array(
                                    "sesionKey" => $sesion['sessionKey'],
                                    "merchantId" => VISA_MERCHANT_ID,
                                    "purchaseNumber" => $purchaseNumber,
                                    "amount" => $amount,
                                    "channel" => $channel,
                                    "expirationTime" => $sesion['expirationTime'],
                                );
                            }
                            echo json_encode($data);
                        } else {
                            header("HTTP/1.1 500");

                        }

                    } else {
                        header("HTTP/1.1 500");
                    }

                    break;

                default:
                    header("HTTP/1.1 500");
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
