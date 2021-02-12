<?php
require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();

if (!empty($RESULTADO_token)) {
    require_once './classes/principal/detallerespuesta.php';
    require_once './classes/utilities/beanrespuesta.php';
    require_once './classes/principal/respuesta.php';
    require_once './controladores/respuestaControlador.php';

    $insrespuesta = new respuestaControlador();
    $accion = $RESULTADO_token->accion;
    if (isset($RESULTADO_token->tipo)) {
        if ($RESULTADO_token->tipo == 1) {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    if ($accion == "update") {
                        $personData = json_decode($_POST['class']);
                        $insRespuestaClass = new Respuesta();
                        $insRespuestaClass->setIdRespuesta($personData->idrespuesta);
                        $insRespuestaClass->setNombre($personData->nombre);
                        $insRespuestaClass->setTitulo($personData->titulo);
                        $insRespuestaClass->setEstado($personData->estado);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $insrespuesta->actualizar_respuesta_controlador($insRespuestaClass);
                    } elseif ($accion == "updateestado") {

                        $personData = json_decode($_POST['class']);
                        $insRespuestaClass = new Respuesta();
                        $insRespuestaClass->setIdRespuesta($personData->idrespuesta);
                        $insRespuestaClass->setEstado($personData->estado);
                        $insRespuestaClass->setCuenta($personData->cuenta);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $insrespuesta->actualizar_respuesta_tarea_controlador($insRespuestaClass);
                    } else {
                        header("HTTP/1.1 500");
                    }

                    break;

                case 'GET':
                    if ($accion == "delete") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        $insRespuestaClass = new Respuesta();
                        $insRespuestaClass->setIdRespuesta($_GET['id']);
                        echo $insrespuesta->eliminar_respuesta_controlador($insRespuestaClass);
                    } else if ($accion == "obtener") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        $insRespuestaClass = new Respuesta();
                        $insRespuestaClass->setPagina($_GET['pagina']);
                        $insRespuestaClass->setRegistro($_GET['registros']);
                        $insRespuestaClass->setCuenta($_GET['cuenta']);
                        if ($_GET['tipo'] == 2) {
                            echo json_encode($insrespuesta->datos_respuesta_controlador("conteo-subtitulo", $insRespuestaClass));
                        } else if ($_GET['tipo'] == 1) {
                            echo json_encode($insrespuesta->datos_respuesta_controlador("conteo-titulo", $insRespuestaClass));
                        }

                    } else if ($accion == "paginate") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insrespuesta->bean_paginador_respuesta_controlador($_GET['pagina'], $_GET['registros'], $_GET['cuenta'], $_GET['tipo'], $_GET['codigo']));
                    } else {
                        header("HTTP/1.1 500");
                    }

                    break;
                default:
                    header("HTTP/1.1 404");
                    break;
            }
        } elseif ($RESULTADO_token->tipo == 2) {
            switch ($_SERVER['REQUEST_METHOD']) {

                case 'POST':

                    if ($accion == "add") {
                        $personData = json_decode($_POST['class']);
                        $insRespuestaClass = new Respuesta();
                        $insRespuestaClass->setCuenta($RESULTADO_token->codigo);
                        $insRespuestaClass->setEstado(0);
                        $insRespuestaClass->setTipo($personData->tipo);
                        $insRespuestaClass->setTitulo($personData->titulo);
                        $insRespuestaClass->setTest($personData->test);
                        $insBeanRespuestaClass = new BeanRespuesta();
                        $insBeanRespuestaClass->setRespuesta($insRespuestaClass);
                        $insBeanRespuestaClass->setListDetalle($personData->list);

                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $insrespuesta->agregar_respuesta_controlador($insBeanRespuestaClass);
                    } else if ($accion == "updatestado") {
                        $personData = json_decode($_POST['class']);
                        $insRespuestaClass = new Respuesta();
                        $insRespuestaClass->setCuenta($RESULTADO_token->codigo);
                        $insRespuestaClass->setEstado($personData->estado);
                        $insRespuestaClass->setTitulo($personData->titulo);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $insrespuesta->actualizar_respuesta_estado_controlador($insRespuestaClass);
                    } else {
                        header("HTTP/1.1 500");
                    }

                    break;

                case 'GET':
                    if ($accion == "paginate") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insrespuesta->bean_paginador_respuesta_controlador($_GET['pagina'], $_GET['registros'], $RESULTADO_token->codigo, $_GET['tipo'], $_GET['codigo']));
                    } else if ($accion == "obtener") {
                        $insRespuestaClass = new Respuesta();
                        $insRespuestaClass->setCuenta($RESULTADO_token->codigo);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insrespuesta->obtener_respuesta_alumno_controlador($insRespuestaClass));
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
