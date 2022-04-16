<?php

require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();

if (!empty($RESULTADO_token)) {
    require_once './classes/principal/prospecto.php';
    require_once './controladores/prospectoControlador.php';
    $insprospecto = new prospectoControlador();
    $accion = $RESULTADO_token->accion;
    if (isset($RESULTADO_token->tipo)) {

        if ($RESULTADO_token->tipo == 1) {
            switch ($_SERVER['REQUEST_METHOD']) {

                case 'POST':

                    if ($accion == "add") {
                        $personData = json_decode($_POST['class']);
                        $insProspectoClass = new Prospecto();
                        $insProspectoClass->setNombre($personData->nombre);
                        $insProspectoClass->setCuenta($personData->cuenta);
                        $insProspectoClass->setDocumento($personData->documento);
                        $insProspectoClass->setPais($personData->pais);
                        $insProspectoClass->setTelefono($personData->telefono);
                        $insProspectoClass->setIdFatherProspecto($personData->idFatherProspecto);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $insprospecto->agregar_prospecto_controlador($insProspectoClass);
                    } else if ($accion == "update") {
                        try {
                            $personData = json_decode($_POST['class']);
                            $insProspectoClass = new Prospecto();
                            $insProspectoClass->setIdprospecto($personData->idprospecto);
                            $insProspectoClass->setNombre($personData->nombre);
                            $insProspectoClass->setCuenta($personData->cuenta);
                            $insProspectoClass->setDocumento($personData->documento);
                            $insProspectoClass->setPais($personData->pais);
                            $insProspectoClass->setTelefono($personData->telefono);
                            $insProspectoClass->setIdFatherProspecto($personData->idFatherProspecto);
                            header("HTTP/1.1 200");
                            header('Content-Type: application/json; charset=utf-8');
                            echo $insprospecto->actualizar_prospecto_controlador($insProspectoClass);
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
                        $insProspectoClass = new Prospecto();
                        $insProspectoClass->setIdprospecto($_GET['id']);
                        echo $insprospecto->eliminar_prospecto_controlador($insProspectoClass);
                    } else if ($accion == "paginate") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');

                        echo json_encode($insprospecto->bean_paginador_prospecto_controlador($_GET['pagina'], $_GET['registros'], $_GET['filtro']));
                    } else if ($accion == "obtener") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insprospecto->datos_prospecto_controlador("conteo", 0));
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
