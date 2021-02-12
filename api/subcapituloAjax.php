<?php
require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();

if (!empty($RESULTADO_token)) {
    require_once './classes/principal/subtitulo.php';
    require_once './classes/principal/titulo.php';
    require_once './controladores/subcapituloControlador.php';
    $inssubcapitulo = new subcapituloControlador();
    $accion = $RESULTADO_token->accion;
    if (isset($RESULTADO_token->tipo)) {
        switch ($_SERVER['REQUEST_METHOD']) {

            case 'POST':

                if ($accion == "add") {
                    $personData = json_decode($_POST['class']);
                    $insCapituloClass = new SubTitulo();
                    $insCapituloClass->setCodigo($personData->codigo);
                    $insCapituloClass->setTitulo($personData->titulo);
                    $insCapituloClass->setNombre($personData->nombre);
                    $insCapituloClass->setEstado($personData->estado);

                    header("HTTP/1.1 200");

                    header('Content-Type: application/json; charset=utf-8');
                    echo $inssubcapitulo->agregar_subcapitulo_controlador($insCapituloClass);
                } else if ($accion == "update") {

                    $personData = json_decode($_POST['class']);
                    $insCapituloClass = new SubTitulo();
                    $insCapituloClass->setIdSubTitulo($personData->idsubTitulo);
                    $insCapituloClass->setCodigo($personData->codigo);
                    $insCapituloClass->setTitulo($personData->titulo);
                    $insCapituloClass->setNombre($personData->nombre);
                    $insCapituloClass->setEstado($personData->estado);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $inssubcapitulo->actualizar_subcapitulo_controlador($insCapituloClass);
                } else {
                    header("HTTP/1.1 500");
                }

                break;

            case 'GET':

                if ($accion == "delete") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    $insCapituloClass = new SubTitulo();
                    $insCapituloClass->setIdSubTitulo($_GET['id']);
                    echo $inssubcapitulo->eliminar_subcapitulo_controlador($insCapituloClass);
                } else if ($accion == "paginate") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($inssubcapitulo->bean_paginador_subcapitulo_controlador((int) $_GET['pagina'], (int) $_GET['registros'], $_GET['capitulo']));
                } else if ($accion == "obtener") {
                    $insCapituloClass = new SubTitulo();
                    $insCapituloClass->setPagina($_GET['pagina']);
                    $insCapituloClass->setRegistro($_GET['registros']);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($inssubcapitulo->datos_subcapitulo_controlador("conteo", $insCapituloClass));
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
