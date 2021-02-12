<?php

require_once 'api/security/filter.php';

$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();

if (!empty($RESULTADO_token)) {
    require_once './controladores/testimonioControlador.php';
    require_once './classes/principal/testimonio.php';
    $instestimonio = new testimonioControlador();
    $accion = $RESULTADO_token->accion;

    if (!isset($RESULTADO_token->tipo)) {
        if ($accion == "obtener" || $accion == "paginate") {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    if ($accion == "paginate") {
                        require_once './classes/utilities/plataforma.php';
                        require_once './classes/principal/visita.php';
                        require_once './controladores/visitaControlador.php';
                        $insVisita = new visitaControlador();
                        $insPlataforma = new Plataforma();
                        $datosPlataforma = $insPlataforma->write_visita();
                        $visita = new Visita();
                        $visita->setIp($datosPlataforma['ip']);
                        $visita->setPagina("testimonio");
                        $visita->setFecha($datosPlataforma['fecha']);
                        $visita->setContador(1);
                        $visita->setInfo($datosPlataforma['info']);
                        $vistResultado = $insVisita->agregar_visita_controlador($visita);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($instestimonio->bean_paginador_testimonio_controlador($_GET['pagina'], $_GET['registros']));
                    } else if ($accion == "obtener") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($instestimonio->datos_testimonio_controlador("conteo", 0));
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
                    $insTestimonioClass = new Testimonio();
                    $insTestimonioClass->setTitulo($personData->titulo);
                    $insTestimonioClass->setDescripcion($personData->descripcion);
                    $insTestimonioClass->setEnlaceYoutube($personData->enlace);
                    $insTestimonioClass->setEstado($personData->estado);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $instestimonio->agregar_testimonio_controlador($insTestimonioClass);
                } else if ($accion == "update") {

                    $personData = json_decode($_POST['class']);
                    $insTestimonioClass = new Testimonio();
                    $insTestimonioClass->setIdtestimonio($personData->idtestimonio);
                    $insTestimonioClass->setTitulo($personData->titulo);
                    $insTestimonioClass->setDescripcion($personData->descripcion);
                    $insTestimonioClass->setEnlaceYoutube($personData->enlace);
                    $insTestimonioClass->setEstado($personData->estado);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $instestimonio->actualizar_testimonio_controlador($insTestimonioClass);
                } else {
                    header("HTTP/1.1 500");
                }

                break;

            case 'GET':
                if ($accion == "delete") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    $insTestimonioClass = new Testimonio();
                    $insTestimonioClass->setIdtestimonio($_GET['id']);
                    echo $instestimonio->eliminar_testimonio_controlador($insTestimonioClass);
                } else if ($accion == "paginate") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($instestimonio->bean_paginador_testimonio_controlador($_GET['pagina'], $_GET['registros']));
                } else if ($accion == "obtener") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($instestimonio->datos_testimonio_controlador("conteo", 0));
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
