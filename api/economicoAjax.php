<?php

require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();

if (!empty($RESULTADO_token)) {
    require_once './classes/principal/economico.php';
    require_once './controladores/economicoControlador.php';
    $inseconomico = new economicoControlador();
    $accion = $RESULTADO_token->accion;
    if (isset($RESULTADO_token->tipo)) {
        if ($RESULTADO_token->tipo == 1) {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    if ($accion == "add") {
                        $personData = json_decode($_POST['class']);
                        $insEconomicoClass = new Economico();
                        $insEconomicoClass->setTitulo($personData->titulo);
                        $insEconomicoClass->setResumen($personData->resumen);
                        $insEconomicoClass->setDescripcion($personData->descripcion);
                        $insEconomicoClass->setArchivo($personData->archivo);
                        $insEconomicoClass->setTipoArchivo($personData->tipo_archivo);
                        $insEconomicoClass->setComentario($personData->comentario);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $inseconomico->agregar_economico_controlador($insEconomicoClass);
                    } else if ($accion == "update") {

                        $personData = json_decode($_POST['class']);
                        $insEconomicoClass = new Economico();
                        $insEconomicoClass->setIdEconomico($personData->ideconomico);
                        $insEconomicoClass->setTitulo($personData->titulo);
                        $insEconomicoClass->setResumen($personData->resumen);
                        $insEconomicoClass->setDescripcion($personData->descripcion);
                        $insEconomicoClass->setArchivo($personData->archivo);
                        $insEconomicoClass->setTipoArchivo($personData->tipo_archivo);
                        $insEconomicoClass->setComentario($personData->comentario);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $inseconomico->actualizar_economico_controlador($insEconomicoClass);
                    } else {
                        header("HTTP/1.1 500");
                    }

                    break;

                case 'GET':
                    if ($accion == "delete") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        $insEconomicoClass = new Economico();
                        $insEconomicoClass->setIdEconomico($_GET['id']);
                        echo $inseconomico->eliminar_economico_controlador($insEconomicoClass);
                    } else if ($accion == "paginate") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($inseconomico->bean_paginador_economico_controlador($_GET['pagina'], $_GET['registros']));
                    } else if ($accion == "general") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($inseconomico->datos_economico_controlador($accion, 0));
                    } else if ($accion == "reporte") {
                        $insEconomicoClass = new Economico();
                        $insEconomicoClass->setMoneda($_GET['moneda']);
                        header("Content-Type: application/vnd.ms-excel; charset=UTF-16LE");
                        header("Expires: 0");
                        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                        header("Cache-Control: private", false);
                        echo mb_convert_encoding($inseconomico->reporte_economico_controlador("moneda", $insEconomicoClass), 'UTF-16LE', 'UTF-8');
                    } else if ($accion == "generalfecha") {
                        $insEconomicoClass = new Economico();
                        $insEconomicoClass->setFecha(array("fechai" => $_GET['fechai'],
                            "fechaf" => $_GET['fechaf'],
                            "moneda" => $_GET['moneda'],
                        ));
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($inseconomico->datos_economico_controlador($accion, 0));
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
