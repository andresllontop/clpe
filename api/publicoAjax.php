<?php

require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();

if (!empty($RESULTADO_token)) {
    require_once './classes/principal/publico.php';
    require_once './controladores/publicoControlador.php';
    $inspublico = new publicoControlador();
    $accion = $RESULTADO_token->accion;
    if (!isset($RESULTADO_token->tipo)) {
        if ($accion == "add") {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    if ($accion == "add") {
                        $json = json_decode(file_get_contents("php://input"));
                        $insPublicoClass = new Publico();
                        $insPublicoClass->setNombre($json->nombre);
                        $insPublicoClass->setEmail($json->email);
                        $insPublicoClass->setFecha($json->fecha);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $inspublico->agregar_publico_controlador($insPublicoClass);
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
                    $insPublicoClass = new Publico();
                    $insPublicoClass->setTitulo($personData->titulo);
                    $insPublicoClass->setResumen($personData->resumen);
                    $insPublicoClass->setDescripcion($personData->descripcion);
                    $insPublicoClass->setArchivo($personData->archivo);
                    $insPublicoClass->setTipoArchivo($personData->tipo_archivo);
                    $insPublicoClass->setComentario($personData->comentario);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $inspublico->agregar_publico_controlador($insPublicoClass);
                } else if ($accion == "update") {

                    $personData = json_decode($_POST['class']);
                    $insPublicoClass = new Publico();
                    $insPublicoClass->setIdPublico($personData->idpublico);
                    $insPublicoClass->setTitulo($personData->titulo);
                    $insPublicoClass->setResumen($personData->resumen);
                    $insPublicoClass->setDescripcion($personData->descripcion);
                    $insPublicoClass->setArchivo($personData->archivo);
                    $insPublicoClass->setTipoArchivo($personData->tipo_archivo);
                    $insPublicoClass->setComentario($personData->comentario);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $inspublico->actualizar_publico_controlador($insPublicoClass);
                } else {
                    header("HTTP/1.1 500");
                }

                break;

            case 'GET':
                if ($accion == "delete") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    $insPublicoClass = new Publico();
                    $insPublicoClass->setIdPublico($_GET['id']);
                    echo $inspublico->eliminar_publico_controlador($insPublicoClass);
                } else if ($accion == "paginate") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($inspublico->bean_paginador_publico_controlador($_GET['pagina'], $_GET['registros']));
                } else if ($accion == "reporte") {
                    header("Content-Type: application/vnd.ms-excel");
                    echo ($inspublico->reporte_publico_controlador("conteo", null));
                } else if ($accion == "obtener") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($inspublico->datos_publico_controlador("conteo", 0));
                } else if ($accion == "get") {

                    $insPublicoClass = new Publico();
                    $insPublicoClass->setIdPublico($_GET['id']);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($inspublico->datos_publico_controlador("unico", $insPublicoClass));
                } else {
                    header("HTTP/1.1 500");
                }

                break;
            default:

                header("HTTP/1.1 404");

                break;
        }
    }

} else {
    return header("HTTP/1.1 403");
}
