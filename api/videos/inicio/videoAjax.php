<?php
require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();
if (!empty($RESULTADO_token)) {
    require_once './classes/principal/video.php';
    require_once './controladores/videosControlador.php';
    $insvideos = new videosControlador();
    $accion = $RESULTADO_token->accion;
    if (!isset($RESULTADO_token->tipo)) {
        if ($accion == "ubicacion" || $accion == "paginate") {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    if ($accion == "paginate") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insvideos->bean_paginador_videos_controlador($_GET['pagina'], $_GET['registros'], 1));
                    } else if ($accion == "ubicacion") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insvideos->datos_videos_ubicacion_controlador($_GET['tipo']));
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
        switch ($_SERVER['REQUEST_METHOD']) {

            case 'POST':
                if ($accion == "update") {

                    $personData = json_decode($_POST['class']);
                    $insVideoClass = new Video();
                    $insVideoClass->setIdvideo($personData->idvideo);
                    $insVideoClass->setNombre(null);
                    $insVideoClass->setEnlace(null);
                    $insVideoClass->setUbicacion(1);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $insvideos->actualizarInicio_videos_controlador($insVideoClass);

                } else {
                    header("HTTP/1.1 500");
                }

                break;

            case 'GET':
                if ($accion == "paginate") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($insvideos->bean_paginador_videos_controlador($_GET['pagina'], $_GET['registros'], 1));
                } else if ($accion == "ubicacion") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($insvideos->datos_videos_ubicacion_controlador($_GET['tipo']));
                } else {
                    header("HTTP/1.1 500");
                }

                break;
            default:
                session_start();
                session_destroy();
                header("HTTP/1.1 404");
                // echo '<script> window.location.href="' . SERVERURL . 'login" </script>';
                break;
        }
    }
} else {
    return header("HTTP/1.1 403");
}
