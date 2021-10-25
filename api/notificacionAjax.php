<?php
require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();

if (!empty($RESULTADO_token)) {
    require_once './classes/principal/notificacion.php';
    require_once './controladores/notificacionControlador.php';
    $insnotificacion = new notificacionControlador();
    $accion = $RESULTADO_token->accion;
    if (isset($RESULTADO_token->tipo)) {
        if ($RESULTADO_token->tipo == 1) {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    if ($accion == "add") {
                        $personData = json_decode($_POST['class']);
                        $insNotificacionClass = new Notificacion();
                        $insNotificacionClass->setRangoInicial($personData->rango_inicial);
                        $insNotificacionClass->setRangoFinal($personData->rango_final);

                        $insNotificacionClass->setDescripcion($personData->descripcion);
                        $insNotificacionClass->setTipo($personData->tipo);
                        $insNotificacionClass->setLibro($personData->libro);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $insnotificacion->agregar_notificacion_controlador($insNotificacionClass);
                    } else if ($accion == "update") {

                        $personData = json_decode($_POST['class']);
                        $insNotificacionClass = new Notificacion();
                        $insNotificacionClass->setIdNotificacion($personData->idnotificacion);
                        $insNotificacionClass->setRangoInicial($personData->rango_inicial);
                        $insNotificacionClass->setRangoFinal($personData->rango_final);
                        $insNotificacionClass->setLibro($personData->libro);
                        $insNotificacionClass->setDescripcion($personData->descripcion);
                        $insNotificacionClass->setTipo($personData->tipo);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $insnotificacion->actualizar_notificacion_controlador($insNotificacionClass);
                    } else {
                        header("HTTP/1.1 500");
                    }

                    break;

                case 'GET':
                    if ($accion == "delete") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        $insNotificacionClass = new Notificacion();
                        $insNotificacionClass->setIdNotificacion($_GET['id']);
                        echo $insnotificacion->eliminar_notificacion_controlador($insNotificacionClass);
                    } else if ($accion == "paginate") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insnotificacion->bean_paginador_notificacion_controlador($_GET['pagina'], $_GET['registros'], $_GET['libro']));
                    } else if ($accion == "obtener") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insnotificacion->datos_notificacion_controlador("conteo", 0));
                    } else if ($accion == "get") {

                        $insNotificacionClass = new Notificacion();
                        $insNotificacionClass->setIdNotificacion($_GET['id']);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insnotificacion->datos_notificacion_controlador("unico", $insNotificacionClass));
                    } else {
                        header("HTTP/1.1 500");
                    }

                    break;
                default:

                    header("HTTP/1.1 404");

                    break;
            }
        } else if ($RESULTADO_token->tipo == 2) {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    if ($accion == "obtener") {
                        $insNotificacionClass = new Notificacion();
                        $insNotificacionClass->setCuenta($RESULTADO_token->codigo);
                        $insNotificacionClass->setLibro($RESULTADO_token->libro);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insnotificacion->datos_notificacion_controlador("tarea", $insNotificacionClass));
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
