<?php

require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();
if (!empty($RESULTADO_token)) {
    require_once './classes/principal/promotor.php';
    require_once './controladores/promotorControlador.php';
    $inspromotor = new promotorControlador();
    $accion = $RESULTADO_token->accion;
    if (!isset($RESULTADO_token->tipo)) {
        if ($accion == "paginate") {
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
                        $visita->setPagina("nosotros");
                        $visita->setFecha($datosPlataforma['fecha']);
                        $visita->setContador(1);
                        $visita->setInfo($datosPlataforma['info']);
                        $vistResultado = $insVisita->agregar_visita_controlador($visita);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($inspromotor->bean_paginador_promotor_controlador($_GET['pagina'], $_GET['registros']));
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
                    $insPromotorClass = new Promotor();
                    $insPromotorClass->setTelefono($personData->telefono);
                    $insPromotorClass->setOcupacion($personData->ocupacion);
                    $insPromotorClass->setYoutube($personData->youtube);
                    $insPromotorClass->setNombre($personData->nombre);
                    $insPromotorClass->setApellido($personData->apellido);
                    $insPromotorClass->setEmail($personData->email);
                    $insPromotorClass->setDescripcion($personData->descripcion);
                    $insPromotorClass->setHistoria($personData->historia);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $inspromotor->agregar_promotor_controlador($insPromotorClass);
                } else if ($accion == "update") {

                    $personData = json_decode($_POST['class']);
                    $insPromotorClass = new Promotor();
                    $insPromotorClass->setIdPromotor($personData->idpromotor);
                    $insPromotorClass->setTelefono($personData->telefono);
                    $insPromotorClass->setOcupacion($personData->ocupacion);
                    $insPromotorClass->setYoutube($personData->youtube);
                    $insPromotorClass->setNombre($personData->nombre);
                    $insPromotorClass->setApellido($personData->apellido);
                    $insPromotorClass->setEmail($personData->email);
                    $insPromotorClass->setDescripcion($personData->descripcion);
                    $insPromotorClass->setHistoria($personData->historia);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $inspromotor->actualizar_promotor_controlador($insPromotorClass);
                } else {
                    header("HTTP/1.1 500");
                }

                break;

            case 'GET':
                if ($accion == "delete") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    $insPromotorClass = new Promotor();
                    $insPromotorClass->setIdPromotor($_GET['id']);
                    echo $inspromotor->eliminar_promotor_controlador($insPromotorClass);
                } else if ($accion == "paginate") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($inspromotor->bean_paginador_promotor_controlador($_GET['pagina'], $_GET['registros']));
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
