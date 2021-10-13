<?php
require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();

if (!empty($RESULTADO_token)) {
    require_once './classes/principal/test.php';
    require_once './classes/utilities/beandetalletest.php';

    require_once './controladores/testControlador.php';

    $instest = new testControlador();
    $accion = $RESULTADO_token->accion;
    if (isset($RESULTADO_token->tipo)) {
        switch ($_SERVER['REQUEST_METHOD']) {

            case 'POST':

                if ($accion == "add") {
                    $personData = json_decode($_POST['class']);
                    $insTestClass = new BeanDetalleTest();
                    $insTestClass->setTest($personData->test);
                    $insTestClass->setListDetalle($personData->lista);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $instest->agregar_test_controlador($insTestClass);
                } else if ($accion == "update") {

                    $personData = json_decode($_POST['class']);
                    $insTestClass = new BeanDetalleTest();
                    $insTestClass->setTest($personData->test);
                    $insTestClass->setListDetalle($personData->lista);

                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $instest->actualizar_test_controlador($insTestClass);
                } else {
                    header("HTTP/1.1 500");
                }

                break;

            case 'GET':
                if ($accion == "delete") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    $insTestClass = new Test();
                    $insTestClass->setIdTest($_GET['id']);
                    echo $instest->eliminar_test_controlador($insTestClass);
                } else if ($accion == "paginate") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($instest->bean_paginador_test_controlador($_GET['pagina'], $_GET['registros'], $_GET['filtro'], $_GET['libro']));
                } else if ($accion == "obtener") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($instest->datos_test_controlador("conteo", 0));
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
    } else {
        return header("HTTP/1.1 403");
    }

} else {
    return header("HTTP/1.1 403");
}
