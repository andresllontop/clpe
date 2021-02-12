<?php

require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();

if (!empty($RESULTADO_token)) {
    require_once './classes/principal/conferencia.php';
    require_once './controladores/conferenciaControlador.php';
    $insconferencia = new conferenciaControlador();
    $accion = $RESULTADO_token->accion;
    if (isset($RESULTADO_token->tipo)) {
        if ($RESULTADO_token->tipo == 1) {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    if ($accion == "add") {
                        $personData = json_decode($_POST['class']);
                        $insConferenciaClass = new Conferencia();
                        $insConferenciaClass->setLink($personData->link);
                        $insConferenciaClass->setTitulo($personData->titulo);
                        $insConferenciaClass->setFecha($personData->fecha);
                        $insConferenciaClass->setDescripcion($personData->descripcion);
                        $insConferenciaClass->setEstado($personData->estado);

                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $insconferencia->agregar_conferencia_controlador($insConferenciaClass);
                    } else if ($accion == "update") {

                        $personData = json_decode($_POST['class']);
                        $insConferenciaClass = new Conferencia();
                        $insConferenciaClass->setTitulo($personData->titulo);
                        $insConferenciaClass->setIdconferencia($personData->idconferencia);
                        $insConferenciaClass->setLink($personData->link);
                        $insConferenciaClass->setFecha($personData->fecha);
                        $insConferenciaClass->setDescripcion($personData->descripcion);
                        $insConferenciaClass->setEstado($personData->estado);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $insconferencia->actualizar_conferencia_controlador($insConferenciaClass);
                    } else {
                        header("HTTP/1.1 500");
                    }

                    break;

                case 'GET':
                    if ($accion == "delete") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        $insConferenciaClass = new Conferencia();
                        $insConferenciaClass->setIdconferencia($_GET['id']);
                        echo $insconferencia->eliminar_conferencia_controlador($insConferenciaClass);
                    } else if ($accion == "paginate") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insconferencia->bean_paginador_conferencia_controlador($_GET['pagina'], $_GET['registros']));
                    } else if ($accion == "obtener") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insconferencia->datos_conferencia_controlador("conteo", 0));
                    } else if ($accion == "get") {

                        $insConferenciaClass = new Conferencia();
                        $insConferenciaClass->setIdconferencia($_GET['id']);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insconferencia->datos_conferencia_controlador("unico", $insConferenciaClass));
                    } else {
                        header("HTTP/1.1 500");
                    }

                    break;
                default:

                    header("HTTP/1.1 404");

                    break;
            }
        } else if ($RESULTADO_token->tipo == 2) {
            if ($accion == "obtener") {
                switch ($_SERVER['REQUEST_METHOD']) {
                    case 'GET':
                        if ($accion == "obtener") {
                            header("HTTP/1.1 200");
                            header('Content-Type: application/json; charset=utf-8');
                            echo json_encode($insconferencia->datos_conferencia_controlador("alumno", 0));
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

} else {
    return header("HTTP/1.1 403");
}
