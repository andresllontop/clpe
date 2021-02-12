<?php
require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();

if (!empty($RESULTADO_token)) {
    require_once './classes/principal/visita.php';

    require_once './controladores/visitaControlador.php';

    $insvisita = new visitaControlador();
    $accion = $RESULTADO_token->accion;
    if (isset($RESULTADO_token->tipo)) {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                if ($accion == "update") {
                    $personData = json_decode($_POST['class']);
                    $insVisitaClass = new Visita();
                    $insVisitaClass->setIdVisita($RESULTADO_token->idvisita);
                    $insVisitaClass->setCuenta($RESULTADO_token->codigo);
                    $insVisitaClass->setFecha_Fin($personData->fecha);
                    $insVisitaClass->setEstado(0);

                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $insvisita->actualizar_visita_controlador($insVisitaClass);
                } else {
                    header("HTTP/1.1 500");
                }

                break;

            case 'GET':
                if ($accion == "delete") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    $insVisitaClass = new Visita();
                    $insVisitaClass->setIdVisita($_GET['id']);
                    echo $insvisita->eliminar_visita_controlador($insVisitaClass);
                } else if ($accion == "paginate") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');

                    echo json_encode($insvisita->bean_paginador_visita_controlador($_GET['pagina'], $_GET['registros'], array(
                        "f_inicial" => $_GET['f_inicial'],
                        "f_final" => $_GET['f_final'],
                        "pagina" => $_GET['f_pagina'],
                        "pais" => $_GET['filter'],

                    )));
                } else if ($accion == "reporte") {
                    header("Content-Type: application/vnd.ms-excel");
                    echo ($insvisita->reporte_visita_controlador("conteo", null));
                } else if ($accion == "obtener") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($insvisita->datos_visita_controlador("conteo", 0));
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
