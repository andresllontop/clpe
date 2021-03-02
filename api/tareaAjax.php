<?php
require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();

if (!empty($RESULTADO_token)) {
    require_once './classes/principal/tarea.php';

    require_once './controladores/tareaControlador.php';

    $instarea = new tareaControlador();
    $accion = $RESULTADO_token->accion;
    if (isset($RESULTADO_token->tipo)) {
        if ($RESULTADO_token->tipo == 1) {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    if ($accion == "delete") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        $insTareaClass = new Tarea();
                        $insTareaClass->setIdTarea($_GET['id']);
                        echo $instarea->eliminar_tarea_controlador($insTareaClass);
                    } else if ($accion == "paginate") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        $insTareaClass = new Tarea();
                        $insTareaClass->setCuenta($_GET['cuenta']);
                        $insTareaClass->setPagina($_GET['pagina']);
                        $insTareaClass->setRegistro($_GET['registros']);
                        echo json_encode($instarea->datos_tarea_controlador("conteo", $insTareaClass));
                    } else if ($accion == "obtener") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($instarea->datos_tarea_controlador("conteo", 0));
                    } else {
                        header("HTTP/1.1 500");
                    }

                    break;
                default:
                    header("HTTP/1.1 404");
                    // echo '<script> window.location.href="' . SERVERURL . 'login" </script>';
                    break;
            }
        } elseif ($RESULTADO_token->tipo == 2) {
            switch ($_SERVER['REQUEST_METHOD']) {

                case 'GET':
                    if ($accion == "paginate") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($instarea->bean_paginador_tarea_controlador($_GET['pagina'], $_GET['registros'], $RESULTADO_token->codigo, $_GET['filtro']));
                    } else if ($accion == "titulo") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        $insTareaClass = new Tarea();
                        $insTareaClass->setCuenta($RESULTADO_token->codigo);
                        $insTareaClass->setPagina($_GET['pagina']);
                        $insTareaClass->setRegistro($_GET['registros']);
                        echo json_encode($instarea->datos_tarea_controlador("titulo", $insTareaClass));
                    } else if ($accion == "obtener") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        $insTareaClass = new Tarea();
                        $insTareaClass->setCuenta($RESULTADO_token->codigo);
                        echo json_encode($instarea->datos_tarea_controlador("tarea-cantidad", $insTareaClass));
                    } else {
                        header("HTTP/1.1 500");
                    }

                    break;
                default:
                    header("HTTP/1.1 404");
                    // echo '<script> window.location.href="' . SERVERURL . 'login" </script>';
                    break;
            }
        } else {
            # code...
        }

    } else {
        return header("HTTP/1.1 403");
    }

} else {
    return header("HTTP/1.1 403");
}
