<?php
require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();

if (!empty($RESULTADO_token)) {
    require_once './classes/principal/bitacora.php';
    require_once './classes/principal/administrador.php';
    require_once './classes/principal/cuenta.php';

    require_once './controladores/bitacoraControlador.php';

    $insbitacora = new bitacoraControlador();
    $accion = $RESULTADO_token->accion;
    if (isset($RESULTADO_token->tipo)) {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                if ($accion == "update") {
                    $personData = json_decode($_POST['class']);
                    $insBitacoraClass = new Bitacora();
                    $insBitacoraClass->setIdBitacora($RESULTADO_token->idbitacora);
                    $insBitacoraClass->setCuenta($RESULTADO_token->codigo);
                    $insBitacoraClass->setFecha_Fin($personData->fecha);
                    $insBitacoraClass->setEstado(0);

                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $insbitacora->actualizar_bitacora_controlador($insBitacoraClass);
                } else {
                    header("HTTP/1.1 500");
                }

                break;

            case 'GET':
                if ($accion == "delete") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    $insBitacoraClass = new Bitacora();
                    $insBitacoraClass->setIdBitacora($_GET['id']);
                    echo $insbitacora->eliminar_bitacora_controlador($insBitacoraClass);
                } else if ($accion == "paginate") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($insbitacora->bean_paginador_bitacora_controlador($_GET['pagina'], $_GET['registros'], $_GET['estado'], $_GET['filter']));
                } else if ($accion == "obtener") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($insbitacora->datos_bitacora_controlador("conteo", 0));
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
