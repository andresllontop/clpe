<?php
require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();
if (!empty($RESULTADO_token)) {
    require_once './classes/principal/detalletest.php';
    require_once './controladores/detalletestControlador.php';
    $insdetalletest = new detalletestControlador();
    $accion = $RESULTADO_token->accion;
    if (isset($RESULTADO_token->tipo)) {
        if ($RESULTADO_token->tipo == 1) {

            switch ($_SERVER['REQUEST_METHOD']) {

                case 'POST':

                    if ($accion == "add") {
                        $personData = json_decode($_POST['class']);
                        $insDetalleTestClass = new DetalleTest();
                        $insDetalleTestClass->setSubtitulo($personData->subtitulo);
                        $insDetalleTestClass->setTest($personData->test);
                        $insDetalleTestClass->setDescripcion($personData->descripcion);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $insdetalletest->agregar_detalletest_controlador($insDetalleTestClass);
                    } else if ($accion == "update") {

                        $personData = json_decode($_POST['class']);
                        $insDetalleTestClass = new DetalleTest();
                        $insDetalleTestClass->setSubtitulo($personData->subtitulo);
                        $insDetalleTestClass->setTest($personData->test);
                        $insDetalleTestClass->setDescripcion($personData->descripcion);
                        $insDetalleTestClass->setIdDetalleTest($personData->iddetalletest);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $insdetalletest->actualizar_detalletest_controlador($insDetalleTestClass);
                    } else {
                        header("HTTP/1.1 500");
                    }

                    break;

                case 'GET':
                    if ($accion == "delete") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        $insDetalleTestClass = new DetalleTest();
                        $insDetalleTestClass->setIdDetalleTest($_GET['id']);
                        echo $insdetalletest->eliminar_detalletest_controlador($insDetalleTestClass);
                    } else if ($accion == "paginate") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insdetalletest->bean_paginador_detalletest_controlador($_GET['pagina'], $_GET['registros'], $_GET['filtro']));
                    } else if ($accion == "obtener") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insdetalletest->datos_detalletest_controlador("conteo", 0));
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
                case 'GET':
                    if ($accion == "paginate") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insdetalletest->bean_paginador_detalletest_controlador($_GET['pagina'], $_GET['registros'], $_GET['filtro']));
                    } else if ($accion == "obtener") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insdetalletest->datos_detalletest_controlador("conteo", 0));
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
