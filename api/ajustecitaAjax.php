<?php

require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();
if (!empty($RESULTADO_token)) {
    require_once './classes/principal/ajustecita.php';
    require_once './controladores/ajustecitaControlador.php';
    $insajustecita = new ajustecitaControlador();
    $accion = $RESULTADO_token->accion;
    if (isset($RESULTADO_token->tipo)) {
        if ($RESULTADO_token->tipo == 1) {
            switch ($_SERVER['REQUEST_METHOD']) {

                case 'POST':

                    if ($accion == "add") {

                        $personData = json_decode($_POST['class']);
                        $insAjustecitaClass = new Ajustecita();
                        $insAjustecitaClass->setSubtitulo($personData->subtitulo);
                        $insAjustecitaClass->setTipo($personData->tipo);

                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $insajustecita->agregar_ajustecita_controlador($insAjustecitaClass);
                    } else if ($accion == "update") {
                        try {
                            $personData = json_decode($_POST['class']);
                            $insAjustecitaClass = new Ajustecita();
                            $insAjustecitaClass->setIdajusteCita($personData->idajusteCita);
                            $insAjustecitaClass->setSubtitulo($personData->subtitulo);
                            $insAjustecitaClass->setTipo($personData->tipo);
                            header("HTTP/1.1 200");
                            header('Content-Type: application/json; charset=utf-8');
                            echo $insajustecita->actualizar_ajustecita_controlador($insAjustecitaClass);
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
                        $insAjustecitaClass = new Ajustecita();
                        $insAjustecitaClass->setIdAjustecita($_GET['id']);
                        echo $insajustecita->eliminar_ajustecita_controlador($insAjustecitaClass);
                    } else if ($accion == "paginate") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');

                        echo json_encode($insajustecita->bean_paginador_ajustecita_controlador($_GET['pagina'], $_GET['registros'], $_GET['filtro']));
                    } else if ($accion == "obtener") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insajustecita->datos_ajustecita_controlador("conteo", 0));
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
} else {
    return header("HTTP/1.1 403");
}
