<?php
require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();

if (!empty($RESULTADO_token)) {
    require_once './classes/principal/titulo.php';
    require_once './classes/principal/libro.php';
    require_once './controladores/capituloControlador.php';
    $inscapitulo = new capituloControlador();
    $insFilter = new SecurityFilter();
    $accion = $RESULTADO_token->accion;
    if (isset($RESULTADO_token->tipo)) {
        switch ($_SERVER['REQUEST_METHOD']) {

            case 'POST':

                if ($accion == "add") {
                    $personData = json_decode($_POST['class']);
                    $insCapituloClass = new Titulo();
                    $insCapituloClass->setNombre($personData->nombre);
                    $insCapituloClass->setCodigo($personData->codigo);
                    $insCapituloClass->setDescripcion($personData->descripcion);
                    $insCapituloClass->setLibro($personData->libro);
                    $insCapituloClass->setEstado($personData->estado);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $inscapitulo->agregar_capitulo_controlador($insCapituloClass);
                } else if ($accion == "update") {

                    $personData = json_decode($_POST['class']);
                    $insCapituloClass = new Titulo();
                    $insCapituloClass->setIdTitulo($personData->idcapitulo);
                    $insCapituloClass->setNombre($personData->nombre);
                    $insCapituloClass->setCodigo($personData->codigo);
                    $insCapituloClass->setDescripcion($personData->descripcion);
                    $insCapituloClass->setLibro($personData->libro);
                    $insCapituloClass->setEstado($personData->estado);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $inscapitulo->actualizar_capitulo_controlador($insCapituloClass);
                } else {
                    header("HTTP/1.1 500");
                }

                break;

            case 'GET':
                if ($accion == "delete") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    $insCapituloClass = new Titulo();
                    $insCapituloClass->setIdTitulo($_GET['id']);
                    echo $inscapitulo->eliminar_capitulo_controlador($insCapituloClass);
                } else if ($accion == "paginate") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($inscapitulo->bean_paginador_capitulo_controlador($_GET['pagina'], $_GET['registros'], $_GET['libro']));
                } else if ($accion == "obtener") {
                    $insCapituloClass = new Titulo();
                    $insCapituloClass->setPagina($_GET['pagina']);
                    $insCapituloClass->setRegistro($_GET['registros']);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($inscapitulo->datos_capitulo_controlador("conteo", $insCapituloClass));
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
