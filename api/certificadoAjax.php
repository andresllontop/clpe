<?php
require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();

if (!empty($RESULTADO_token)) {
    require_once './classes/principal/certificado.php';
    require_once './controladores/certificadoControlador.php';
    $inscertificado = new certificadoControlador();
    $accion = $RESULTADO_token->accion;
    if (isset($RESULTADO_token->tipo)) {
        if ($RESULTADO_token->tipo == 1) {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    if ($accion == "add") {
                        $personData = json_decode($_POST['class']);
                        $insCertificadoClass = new Certificado();
                        $insCertificadoClass->setNombre($personData->desde);
                        $insCertificadoClass->setCuenta($personData->hasta);
                        $insCertificadoClass->setTipo($personData->tipo);
                        $insCertificadoClass->setNombre($personData->nombre);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $inscertificado->agregar_certificado_controlador($insCertificadoClass);
                    } else if ($accion == "update") {

                        $personData = json_decode($_POST['class']);
                        $insCertificadoClass = new Certificado();
                        $insCertificadoClass->setIdCertificado($personData->idcertificado);
                        $insCertificadoClass->setDesde($personData->desde);
                        $insCertificadoClass->setHasta($personData->hasta);
                        $insCertificadoClass->setTipo($personData->tipo);
                        $insCertificadoClass->setNombre($personData->nombre);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $inscertificado->actualizar_certificado_controlador($insCertificadoClass);
                    } else {
                        header("HTTP/1.1 500");
                    }

                    break;

                case 'GET':
                    if ($accion == "delete") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        $insCertificadoClass = new Certificado();
                        $insCertificadoClass->setIdcertificado($_GET['id']);
                        echo $inscertificado->eliminar_certificado_controlador($insCertificadoClass);
                    } else if ($accion == "paginate") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($inscertificado->bean_paginador_certificado_controlador($_GET['pagina'], $_GET['registros'], $_GET['estado'], $_GET['filtro']));
                    } else if ($accion == "obtener") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($inscertificado->datos_certificado_controlador("conteo", 0));
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
        } elseif ($RESULTADO_token->tipo == 2) {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    if ($accion == "add") {
                        $personData = json_decode($_POST['class']);
                        $insCertificadoClass = new Certificado();
                        $insCertificadoClass->setCuenta($RESULTADO_token->codigo);
                        $insCertificadoClass->setIndicador($personData->indicador);
                        $insCertificadoClass->setNombre($personData->nombre);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $inscertificado->agregar_certificado_controlador($insCertificadoClass);
                    } else if ($accion == "update") {
                        $personData = json_decode($_POST['class']);
                        $insCertificadoClass = new Certificado();
                        $insCertificadoClass->setCuenta($RESULTADO_token->codigo);
                        $insCertificadoClass->setIdcertificado($personData->idcertificado);
                        $insCertificadoClass->setIndicador($personData->indicador);
                        $insCertificadoClass->setNombre($personData->nombre);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $inscertificado->actualizar_certificado_controlador($insCertificadoClass);
                    } else {
                        header("HTTP/1.1 500");
                    }

                    break;
                case 'GET':
                    if ($accion == "obtener") {
                        $insCertificadoClass = new Certificado();
                        $insCertificadoClass->setCuenta($RESULTADO_token->codigo);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($inscertificado->datos_certificado_controlador("unico-alumno", $insCertificadoClass));
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
