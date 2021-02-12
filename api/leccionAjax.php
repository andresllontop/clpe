<?php
require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();

if (!empty($RESULTADO_token)) {
    require_once './classes/principal/leccion.php';

    require_once './controladores/leccionesControlador.php';

    $insleccion = new leccionesControlador();
    $accion = $RESULTADO_token->accion;
    if (isset($RESULTADO_token->tipo)) {
        if ($RESULTADO_token->tipo == 1) {
            switch ($_SERVER['REQUEST_METHOD']) {

                case 'POST':

                    if ($accion == "add") {
                        $personData = json_decode($_POST['class']);
                        $insLeccionClass = new Leccion();
                        $insLeccionClass->setNombre($personData->nombre);
                        $insLeccionClass->setTitulo($personData->titulo);
                        $insLeccionClass->setEstado($personData->estado);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $insleccion->agregar_lecciones_controlador($insLeccionClass);
                    } else if ($accion == "update") {
                        $personData = json_decode($_POST['class']);
                        $insLeccionClass = new Leccion();
                        $insLeccionClass->setIdLeccion($personData->idleccion);
                        $insLeccionClass->setCuenta($personData->cuenta);
                        $insLeccionClass->setEstado($personData->estado);

                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $insleccion->actualizar_lecciones_controlador($insLeccionClass);
                    } else {
                        header("HTTP/1.1 500");
                    }

                    break;

                case 'GET':
                    if ($accion == "delete") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        $insLeccionClass = new Leccion();
                        $insLeccionClass->setIdleccion($_GET['id']);
                        echo $insleccion->eliminar_lecciones_controlador($insLeccionClass);
                    } else if ($accion == "deletevideo") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        $insLeccionClass = new Leccion();
                        $insLeccionClass->setIdleccion($_GET['id']);
                        echo $insleccion->eliminar_video_lecciones_controlador($insLeccionClass);
                    } else if ($accion == "paginate") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        $insLeccionClass = new Leccion();
                        $insLeccionClass->setCuenta($_GET['cuenta']);
                        $insLeccionClass->setPagina($_GET['pagina']);
                        $insLeccionClass->setRegistro($_GET['registros']);
                        echo json_encode($insleccion->datos_lecciones_controlador("conteo", $insLeccionClass));
                    } else if ($accion == "excel") {
                        header("Content-Type: application/vnd.ms-excel; charset=UTF-16LE");
                        header("Expires: 0");
                        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                        header("Cache-Control: private", false);
                        echo mb_convert_encoding($insleccion->excel_lecciones_controlador("excel-lecciones", 0), 'UTF-16LE', 'UTF-8');
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
        } elseif ($RESULTADO_token->tipo == 2) {
            switch ($_SERVER['REQUEST_METHOD']) {

                case 'POST':

                    if ($accion == "add") {
                        $personData = json_decode($_POST['class']);
                        $insLeccionClass = new Leccion();
                        $insLeccionClass->setSubTitulo($personData->subtitulo);
                        $insLeccionClass->setCuenta($RESULTADO_token->codigo);
                        $insLeccionClass->setComentario($personData->comentario);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $insleccion->agregar_lecciones_controlador($insLeccionClass);
                    } else if ($accion == "update") {
                        $personData = json_decode($_POST['class']);
                        $insLeccionClass = new Leccion();
                        $insLeccionClass->setIdLeccion($personData->idleccion);
                        $insLeccionClass->setCuenta($personData->cuenta);
                        $insLeccionClass->setEstado($personData->estado);

                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $insleccion->actualizar_lecciones_controlador($insLeccionClass);
                    } else if ($accion == "updatestado") {
                        $personData = json_decode($_POST['class']);
                        $insLeccionClass = new Leccion();
                        $insLeccionClass->setCuenta($RESULTADO_token->codigo);
                        $insLeccionClass->setEstado($personData->estado);
                        $insLeccionClass->setSubtitulo($personData->subtitulo);

                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $insleccion->actualizar_lecciones_estado_controlador($insLeccionClass);
                    } else {
                        header("HTTP/1.1 500");
                    }

                    break;

                case 'GET':
                    if ($accion == "paginate") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insleccion->bean_paginador_lecciones_controlador($_GET['pagina'], $_GET['registros'], $RESULTADO_token->codigo, $_GET['filtro']));
                    } else if ($accion == "obtener") {
                        $insLeccionClass = new Leccion();
                        $insLeccionClass->setCuenta($RESULTADO_token->codigo);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insleccion->obtener_lecciones_alumno_siguiente_controlador($insLeccionClass));
                    } else if ($accion == "subtitulotitulo") {
                        $insLeccionClass = new Leccion();
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        $insLeccionClass->setCuenta($RESULTADO_token->codigo);
                        echo json_encode($insleccion->datos_lecciones_controlador("subtitulo-titulo", $insLeccionClass));
                    } else if ($accion == "anteriorleccion") {
                        //seleccionar leccion del contenido del curso

                        $insLeccionClass = new Leccion();
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        $insLeccionClass->setCuenta($RESULTADO_token->codigo);
                        $insLeccionClass->setSubTitulo($_GET['subtitulo']);
                        echo json_encode($insleccion->datos_lecciones_controlador("anteriorleccion", $insLeccionClass));
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
            # code...
        }

    } else {
        return header("HTTP/1.1 403");
    }

} else {
    return header("HTTP/1.1 403");
}
