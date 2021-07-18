<?php

require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();

if (!empty($RESULTADO_token)) {
    require_once './classes/principal/social.php';
    require_once './controladores/socialControlador.php';
    $inssocial = new socialControlador();
    $accion = $RESULTADO_token->accion;
    if (!isset($RESULTADO_token->tipo)) {
        if ($accion == "get" || $accion == "paginate") {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    if ($accion == "paginate") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($inssocial->bean_paginador_social_controlador($_GET['pagina'], $_GET['registros']));
                    } else if ($accion == "obtener") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($inssocial->datos_social_controlador("conteo", 0));
                    } else if ($accion == "get") {

                        $insSocialClass = new Social();
                        $insSocialClass->setIdSocial($_GET['id']);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($inssocial->datos_social_controlador("unico", $insSocialClass));
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
                    $insSocialClass = new Social();
                    $insSocialClass->setTitulo($personData->titulo);
                    $insSocialClass->setParametroCurso($personData->parametro_curso);
                    $insSocialClass->setDescripcion($personData->descripcion);

                    $insSocialClass->setTipoArchivo($personData->tipo_archivo);
                    $insSocialClass->setFraseCurso($personData->frase_curso);
                    $insSocialClass->setFraseTestimonio($personData->prase_testimonio);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $inssocial->agregar_social_controlador($insSocialClass);
                } else if ($accion == "update") {

                    $personData = json_decode($_POST['class']);
                    $insSocialClass = new Social();
                    $insSocialClass->setIdSocial($personData->idsocial);
                    $insSocialClass->setTitulo($personData->titulo);
                    $insSocialClass->setParametroCurso($personData->parametro_curso);
                    $insSocialClass->setDescripcion($personData->descripcion);
                    $insSocialClass->setFraseTestimonio($personData->prase_testimonio);
                    $insSocialClass->setTipoArchivo($personData->tipo_archivo);
                    $insSocialClass->setFraseCurso($personData->frase_curso);

                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $inssocial->actualizar_social_controlador($insSocialClass);
                } else {
                    header("HTTP/1.1 500");
                }

                break;

            case 'GET':
                if ($accion == "delete") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    $insSocialClass = new Social();
                    $insSocialClass->setIdSocial($_GET['id']);
                    echo $inssocial->eliminar_social_controlador($insSocialClass);
                } else if ($accion == "paginate") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($inssocial->bean_paginador_social_controlador($_GET['pagina'], $_GET['registros']));
                } else if ($accion == "obtener") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($inssocial->datos_social_controlador("conteo", 0));
                } else if ($accion == "get") {

                    $insSocialClass = new Social();
                    $insSocialClass->setIdSocial($_GET['id']);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($inssocial->datos_social_controlador("unico", $insSocialClass));
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
