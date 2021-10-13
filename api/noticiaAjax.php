<?php

require_once './api/security/filter.php';
$insFilter = new SecurityFilter();

$RESULTADO_token = $insFilter->HeaderToken();

if (!empty($RESULTADO_token)) {
    require_once './classes/principal/noticia.php';
    require_once './controladores/noticiaControlador.php';
    $insnoticia = new noticiaControlador();
    $accion = $RESULTADO_token->accion;
    if (!isset($RESULTADO_token->tipo)) {
        if ($accion == "obtener" || $accion == "paginate") {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    if ($accion == "paginate") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insnoticia->bean_paginador_noticia_controlador($_GET['pagina'], $_GET['registros']));
                    } else if ($accion == "obtener") {

                        require_once './classes/utilities/plataforma.php';
                        require_once './classes/principal/visita.php';
                        require_once './controladores/visitaControlador.php';
                        $insVisita = new visitaControlador();
                        $insPlataforma = new Plataforma();
                        $datosPlataforma = $insPlataforma->write_visita();
                        $visita = new Visita();
                        $visita->setIp($datosPlataforma['ip']);
                        $visita->setPagina("index");
                        $visita->setFecha($datosPlataforma['fecha']);
                        $visita->setContador(1);
                        $visita->setInfo($datosPlataforma['info']);
                        $vistResultado = $insVisita->agregar_visita_controlador($visita);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insnoticia->datos_noticia_controlador("conteo", 0));
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

                if ($accion == "add") {
                    $personData = json_decode($_POST['class']);
                    $insNoticiaClass = new Noticia();
                    $insNoticiaClass->setTitulo($personData->titulo);
                    $insNoticiaClass->setResumen($personData->resumen);
                    $insNoticiaClass->setDescripcion($personData->descripcion);
                    $insNoticiaClass->setArchivo($personData->archivo);
                    $insNoticiaClass->setTipoArchivo($personData->tipo_archivo);
                    $insNoticiaClass->setComentario($personData->comentario);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $insnoticia->agregar_noticia_controlador($insNoticiaClass);
                } else if ($accion == "update") {
                    try {
                        $personData = json_decode($_POST['class']);
                        $insNoticiaClass = new Noticia();
                        $insNoticiaClass->setIdnoticia($personData->idnoticia);
                        $insNoticiaClass->setTitulo($personData->titulo);
                        $insNoticiaClass->setDescripcion($personData->descripcion);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $insnoticia->actualizar_noticia_controlador($insNoticiaClass);
                    } catch (\Throwable $th) {
                        //throw $th;
                        header("HTTP/1.1 500");
                    }
                } else {
                    header("HTTP/1.1 500");
                }

                break;

            case 'GET':
                if ($accion == "delete") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    $insNoticiaClass = new Noticia();
                    $insNoticiaClass->setIdNoticia($_GET['id']);
                    echo $insnoticia->eliminar_noticia_controlador($insNoticiaClass);
                } else if ($accion == "paginate") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($insnoticia->bean_paginador_noticia_controlador($_GET['pagina'], $_GET['registros']));
                } else if ($accion == "obtener") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($insnoticia->datos_noticia_controlador("conteo", 0));
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
