<?php
require_once './core/configGeneral.php';
require_once './core/configApp.php';
require_once './api/security/securityFilter.php';
require_once './classes/principal/cuestionario.php';

require_once './controladores/cuestionarioControlador.php';

$insFilter = new SecurityFilter();
$inscuestionario = new cuestionarioControlador();
//echo ($insFilter->validarBearerToken());
$values_path = $_SERVER['REDIRECT_URL'];
//HACEMOS UN SPLIT PARA DEJAR EL PATH SIN PARAMETROS
$values_path = explode("/", $values_path);
$accion = $values_path[sizeof($values_path) - 1];
switch ($_SERVER['REQUEST_METHOD']) {

    case 'POST':

        if ($accion == "add") {
            $personData = json_decode($_POST['class']);
            $insCuestionarioClass = new Cuestionario();
            $insCuestionarioClass->setTitulo($personData->titulo);
            $insCuestionarioClass->setResumen($personData->resumen);
            $insCuestionarioClass->setDescripcion($personData->descripcion);
            $insCuestionarioClass->setArchivo($personData->archivo);
            $insCuestionarioClass->setTipoArchivo($personData->tipo_archivo);
            $insCuestionarioClass->setComentario($personData->comentario);
            header("HTTP/1.1 200");
            header('Content-Type: application/json; charset=utf-8');
            echo $inscuestionario->agregar_cuestionario_controlador($insCuestionarioClass);
        } else if ($accion == "update") {

            $personData = json_decode($_POST['class']);
            $insCuestionarioClass = new Cuestionario();
            $insCuestionarioClass->setIdCuestionario($personData->idcuestionario);
            $insCuestionarioClass->setTitulo($personData->titulo);
            $insCuestionarioClass->setResumen($personData->resumen);
            $insCuestionarioClass->setDescripcion($personData->descripcion);
            $insCuestionarioClass->setArchivo($personData->archivo);
            $insCuestionarioClass->setTipoArchivo($personData->tipo_archivo);
            $insCuestionarioClass->setComentario($personData->comentario);
            header("HTTP/1.1 200");
            header('Content-Type: application/json; charset=utf-8');
            echo $inscuestionario->actualizar_cuestionario_controlador($insCuestionarioClass);
        } else {
            header("HTTP/1.1 500");
        }

        break;

    case 'GET':
        if ($accion == "delete") {
            header("HTTP/1.1 200");
            header('Content-Type: application/json; charset=utf-8');
            $insCuestionarioClass = new Cuestionario();
            $insCuestionarioClass->setIdCuestionario($_GET['id']);
            echo $inscuestionario->eliminar_cuestionario_controlador($insCuestionarioClass);
        } else if ($accion == "paginate") {
            header("HTTP/1.1 200");
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($inscuestionario->bean_paginador_cuestionario_controlador($_GET['pagina'], $_GET['registros']));
        } else if ($accion == "obtener") {
            header("HTTP/1.1 200");
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($inscuestionario->datos_cuestionario_controlador("conteo", 0));
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
