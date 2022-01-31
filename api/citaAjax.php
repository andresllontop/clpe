<?php

require_once './api/security/filter.php';
$insFilter = new SecurityFilter();

$RESULTADO_token = $insFilter->HeaderToken();

if (!empty($RESULTADO_token)) {
    require_once './classes/principal/cita.php';
    require_once './controladores/citaControlador.php';
    $inscita = new citaControlador();
    $accion = $RESULTADO_token->accion;
    if (isset($RESULTADO_token->tipo)) {
        if ($RESULTADO_token->tipo == 2) {

            switch ($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    if ($accion == "paginate") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($inscita->bean_paginador_cita_controlador($_GET['pagina'], $_GET['registros']));
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
                        echo json_encode($inscita->datos_cita_controlador("conteo", 0));
                    } else {
                        header("HTTP/1.1 500");
                    }

                    break;
                default:
                    header("HTTP/1.1 404");
                    break;
            }

        } else {
            switch ($_SERVER['REQUEST_METHOD']) {

                case 'POST':

                    if ($accion == "add") {
                        $personData = json_decode($_POST['class']);
                        $insCitaClass = new Cita();
                        $insCitaClass->setCliente($personData->cliente);
                        $insCitaClass->setSubtitulo($personData->subtitulo);
                        $insCitaClass->setEstadoSolicitud($personData->estadoSolicitud);
                        $insCitaClass->setAsunto($personData->asunto);
                        $insCitaClass->setTipo($personData->tipo);

                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $inscita->agregar_cita_controlador($insCitaClass);
                    } else if ($accion == "update") {
                        try {
                            $personData = json_decode($_POST['class']);
                            $insCitaClass = new Cita();
                            $insCitaClass->setIdcita($personData->idcita);
                            $insCitaClass->setEstadoSolicitud($personData->estadoSolicitud);
                            $insCitaClass->setFechaAtendida($personData->fechaAtendida);
                                                        header("HTTP/1.1 200");
                            header('Content-Type: application/json; charset=utf-8');
                            echo $inscita->actualizar_cita_controlador($insCitaClass);
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
                        $insCitaClass = new Cita();
                        $insCitaClass->setIdCita($_GET['id']);
                        echo $inscita->eliminar_cita_controlador($insCitaClass);
                    } else if ($accion == "paginate") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($inscita->bean_paginador_cita_controlador($_GET['pagina'], $_GET['registros'], $_GET['filter']));
                    } else if ($accion == "obtener") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($inscita->datos_cita_controlador("conteo", 0));
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
} else {
    return header("HTTP/1.1 403");
}
