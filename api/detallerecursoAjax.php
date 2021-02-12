<?php
require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();

if (!empty($RESULTADO_token)) {
    require_once './classes/principal/detallerecurso.php';
    require_once './controladores/detallerecursoControlador.php';
    $insdetallerecurso = new detallerecursoControlador();
    $accion = $RESULTADO_token->accion;
    if (isset($RESULTADO_token->tipo)) {
        switch ($_SERVER['REQUEST_METHOD']) {

            case 'POST':

                if ($accion == "add") {
                    $personData = json_decode($_POST['class']);
                    $insDetalleRecursoClass = new DetalleRecurso();
                    $insDetalleRecursoClass->setRecurso($personData->recurso);
                    $insDetalleRecursoClass->setDescripcion($personData->nombre);
                    $insDetalleRecursoClass->setTipo($personData->tipo);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $insdetallerecurso->agregar_detallerecurso_controlador($insDetalleRecursoClass);
                } else if ($accion == "update") {

                    $personData = json_decode($_POST['class']);
                    $insDetalleRecursoClass = new DetalleRecurso();
                    $insDetalleRecursoClass->setRecurso($personData->recurso);
                    $insDetalleRecursoClass->setDescripcion($personData->nombre);
                    $insDetalleRecursoClass->setTipo($personData->tipo);
                    $insDetalleRecursoClass->setIdDetalleRecurso($personData->iddetallerecurso);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $insdetallerecurso->actualizar_detallerecurso_controlador($insDetalleRecursoClass);
                } else {
                    header("HTTP/1.1 500");
                }

                break;

            case 'GET':
                if ($accion == "delete") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    $insDetalleRecursoClass = new DetalleRecurso();
                    $insDetalleRecursoClass->setIdDetalleRecurso($_GET['id']);
                    echo $insdetallerecurso->eliminar_detallerecurso_controlador($insDetalleRecursoClass);
                } else if ($accion == "paginate") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($insdetallerecurso->bean_paginador_detallerecurso_controlador((int) $_GET['pagina'], (int) $_GET['registros'], $_GET['filtro']));
                } else if ($accion == "obtener") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($insdetallerecurso->datos_detallerecurso_controlador("conteo", 0));
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
