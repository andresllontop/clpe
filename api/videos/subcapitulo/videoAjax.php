<?php
require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();
if (!empty($RESULTADO_token)) {
    require_once './classes/principal/subtitulo.php';
    require_once './classes/principal/videosubtitulo.php';
    require_once './controladores/videosubcapituloControlador.php';
    $insvideosubcapitulo = new videosubcapituloControlador();
    $accion = $RESULTADO_token->accion;
    if (isset($RESULTADO_token->tipo)) {
        switch ($_SERVER['REQUEST_METHOD']) {

            case 'POST':

                if ($accion == "add") {
                    $personData = json_decode($_POST['bean']);
                    $insVideoSubTituloClass = new VideoSubTitulo();
                    $insVideoSubTituloClass->setCodigo($personData->codigo);
                    $insVideoSubTituloClass->setSubTitulo($personData->subTitulo);
                    $insVideoSubTituloClass->setNombre($personData->video);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $insvideosubcapitulo->agregar_videosubcapitulo_controlador($insVideoSubTituloClass);
                } else if ($accion == "update") {

                    $personData = json_decode($_POST['bean']);
                    $insVideoSubTituloClass = new VideoSubTitulo();
                    $insVideoSubTituloClass->setIdVideoSubTitulo($personData->idvideoSubTitulo);
                    $insVideoSubTituloClass->setCodigo($personData->codigo);
                    $insVideoSubTituloClass->setSubTitulo($personData->subTitulo);
                    $insVideoSubTituloClass->setNombre($personData->video);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $insvideosubcapitulo->actualizar_videosubcapitulo_controlador($insVideoSubTituloClass);
                } else {
                    header("HTTP/1.1 500");
                }

                break;

            case 'GET':
                if ($accion == "delete") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    $insVideoSubTituloClass = new VideoSubTitulo();
                    $insVideoSubTituloClass->setIdVideoSubTitulo($_GET['id']);
                    echo $insvideosubcapitulo->eliminar_videosubcapitulo_controlador($insVideoSubTituloClass);
                } else if ($accion == "paginate") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($insvideosubcapitulo->bean_paginador_videosubcapitulo_controlador($_GET['pagina'], $_GET['registros'], $_GET['subtitulo']));
                } else if ($accion == "obtener") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($insvideosubcapitulo->datos_videosubcapitulo_controlador("conteo", 0));
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
