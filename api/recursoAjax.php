<?php

require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();

if (!empty($RESULTADO_token)) {
    require_once './classes/principal/recurso.php';
    require_once './controladores/recursoControlador.php';
    $insrecurso = new recursoControlador();
    $accion = $RESULTADO_token->accion;
    if (isset($RESULTADO_token->tipo)) {
        if ($RESULTADO_token->tipo == 1) {
            switch ($_SERVER['REQUEST_METHOD']) {

                case 'POST':

                    if ($accion == "add") {
                        $personData = json_decode($_POST['class']);
                        $insRecursoClass = new Recurso();
                        $insRecursoClass->setSubTitulo($personData->subTitulo);
                        $insRecursoClass->setNombre($personData->nombre);
                        $insRecursoClass->setDisponible($personData->disponible);

                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $insrecurso->agregar_recurso_controlador($insRecursoClass);
                    } else if ($accion == "update") {

                        $personData = json_decode($_POST['class']);
                        $insRecursoClass = new Recurso();
                        $insRecursoClass->setSubTitulo($personData->subTitulo);
                        $insRecursoClass->setNombre($personData->nombre);
                        $insRecursoClass->setDisponible($personData->disponible);
                        $insRecursoClass->setIdRecurso($personData->idrecurso);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $insrecurso->actualizar_recurso_controlador($insRecursoClass);
                    } else {
                        header("HTTP/1.1 500");
                    }

                    break;

                case 'GET':
                    if ($accion == "delete") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        $insRecursoClass = new Recurso();
                        $insRecursoClass->setIdRecurso($_GET['id']);
                        echo $insrecurso->eliminar_recurso_controlador($insRecursoClass);
                    } else if ($accion == "paginate") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insrecurso->bean_paginador_recurso_controlador($_GET['pagina'], $_GET['registros'], $_GET['libro']));
                    } else if ($accion == "obtener") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insrecurso->datos_recurso_controlador("conteo", 0));
                    } else {
                        header("HTTP/1.1 500");
                    }

                    break;
                default:

                    header("HTTP/1.1 404");

                    break;
            }
        } elseif ($RESULTADO_token->tipo == 2 && $RESULTADO_token->libro != "") {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    if ($accion == "alumno") {
                        $insRecursoClass = new Recurso();
                        $insRecursoClass->setCuenta($RESULTADO_token->codigo);
                        $insRecursoClass->setPagina($_GET['pagina']);
                        $insRecursoClass->setRegistro($_GET['registros']);
                        $insRecursoClass->setSubTitulo($RESULTADO_token->libro);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insrecurso->datos_recurso_controlador("conteo", $insRecursoClass));
                    } else {
                        header("HTTP/1.1 500");
                    }

                    break;
                default:

                    header("HTTP/1.1 404");

                    break;
            }
        } else {
            header("HTTP/1.1 404");
        }

    } else {
        return header("HTTP/1.1 403");
    }

} else {
    return header("HTTP/1.1 403");
}
