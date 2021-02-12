<?php
require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();

if (!empty($RESULTADO_token)) {
    require_once './classes/principal/mensaje.php';
    require_once './controladores/mensajeControlador.php';

    $insmensaje = new mensajeControlador();
    $accion = $RESULTADO_token->accion;
    if (isset($RESULTADO_token->tipo)) {
        if ((int) $RESULTADO_token->tipo == 1) {
            switch ($_SERVER['REQUEST_METHOD']) {

                case 'POST':
                    if ($accion == "add") {
                        $personData = json_decode($_POST['class']);
                        $insMensajeClass = new Mensaje();
                        $insMensajeClass->setDescripcion($personData->descripcion);
                        $insMensajeClass->setTitulo($personData->titulo);
                        $insMensajeClass->setEstado($personData->estado);
                        $insMensajeClass->setCuenta($RESULTADO_token->codigo);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $insmensaje->agregar_mensaje_controlador($insMensajeClass);
                    } else if ($accion == "update") {

                        $personData = json_decode($_POST['class']);
                        $insMensajeClass = new Mensaje();
                        $insMensajeClass->setIdMensaje($personData->idmensaje);
                        //  $insMensajeClass->setDescripcion($personData->descripcion);
                        //$insMensajeClass->setTitulo($personData->titulo);
                        $insMensajeClass->setEstado($personData->estado);
                        $insMensajeClass->setCuenta($personData->codigo);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $insmensaje->actualizar_mensaje_controlador($insMensajeClass);
                    } else {
                        header("HTTP/1.1 500");
                    }

                    break;

                case 'GET':
                    if ($accion == "delete") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        $insMensajeClass = new Mensaje();
                        $insMensajeClass->setIdMensaje($_GET['id']);
                        echo $insmensaje->eliminar_mensaje_controlador($insMensajeClass);
                    } else if ($accion == "paginate") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insmensaje->bean_paginador_mensaje_controlador($_GET['pagina'], $_GET['registros']));
                    } else if ($accion == "obtener") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insmensaje->datos_mensaje_controlador("conteo", 0));
                    } else if ($accion == "home") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insmensaje->datos_mensaje_controlador("home", 0));
                    } else {
                        header("HTTP/1.1 500");
                    }

                    break;
                default:
                    header("HTTP/1.1 404");
                    break;
            }
        } else if ((int) $RESULTADO_token->tipo == 2) {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'POST':

                    if ($accion == "add") {
                        $personData = json_decode($_POST['class']);
                        $insMensajeClass = new Mensaje();
                        $insMensajeClass->setDescripcion($personData->descripcion);
                        $insMensajeClass->setTitulo($personData->asunto);
                        $insMensajeClass->setEstado($personData->nombre);
                        $insMensajeClass->setEmail($personData->email);
                        $insMensajeClass->setCuenta($RESULTADO_token->codigo);
                        //  header("HTTP/1.1 200");
                        //header('Content-Type: application/json; charset=utf-8');
                        echo $insmensaje->enviar_mensaje_controlador($insMensajeClass);
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
