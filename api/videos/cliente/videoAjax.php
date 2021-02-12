<?php
require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();
if (!empty($RESULTADO_token)) {
    require_once './classes/principal/videousuario.php';
    require_once './controladores/videousuarioControlador.php';

    $insvideousuario = new videousuarioControlador();
    $accion = $RESULTADO_token->accion;
    if (!isset($RESULTADO_token->tipo)) {
        if ($accion == "obtener" || $accion == "paginate") {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    if ($accion == "paginate") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insvideousuario->bean_paginador_videousuario_controlador($_GET['pagina'], $_GET['registros'], $_GET['cuenta']));
                    } else if ($accion == "obtener") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insvideousuario->datos_videousuario_controlador("conteo", 0));
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
                    $insVideoUsuarioClass = new VideoUsuario();
                    $insVideoUsuarioClass->setTitulo($personData->titulo);
                    $insVideoUsuarioClass->setResumen($personData->resumen);
                    $insVideoUsuarioClass->setDescripcion($personData->descripcion);
                    $insVideoUsuarioClass->setArchivo($personData->archivo);
                    $insVideoUsuarioClass->setTipoArchivo($personData->tipo_archivo);
                    $insVideoUsuarioClass->setComentario($personData->comentario);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $insvideousuario->agregar_videousuario_controlador($insVideoUsuarioClass);
                } else if ($accion == "update") {

                    $personData = json_decode($_POST['class']);
                    $insVideoUsuarioClass = new VideoUsuario();
                    $insVideoUsuarioClass->setIdVideoUsuario($personData->idvideousuario);
                    $insVideoUsuarioClass->setTitulo($personData->titulo);
                    $insVideoUsuarioClass->setResumen($personData->resumen);
                    $insVideoUsuarioClass->setDescripcion($personData->descripcion);
                    $insVideoUsuarioClass->setArchivo($personData->archivo);
                    $insVideoUsuarioClass->setTipoArchivo($personData->tipo_archivo);
                    $insVideoUsuarioClass->setComentario($personData->comentario);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $insvideousuario->actualizar_videousuario_controlador($insVideoUsuarioClass);
                } else {
                    header("HTTP/1.1 500");
                }

                break;

            case 'GET':
                if ($accion == "delete") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    $insVideoUsuarioClass = new VideoUsuario();
                    $insVideoUsuarioClass->setIdVideoUsuario($_GET['id']);
                    echo $insvideousuario->eliminar_videousuario_controlador($insVideoUsuarioClass);
                } else if ($accion == "paginate") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($insvideousuario->bean_paginador_videousuario_controlador($_GET['pagina'], $_GET['registros'], $_GET['cuenta']));
                } else if ($accion == "obtener") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($insvideousuario->datos_videousuario_controlador("conteo", 0));
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
