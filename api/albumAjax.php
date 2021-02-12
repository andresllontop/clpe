<?php
require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();

if (!empty($RESULTADO_token)) {
    require_once './classes/principal/album.php';
    require_once './controladores/albumControlador.php';
    $insalbum = new albumControlador();
    $accion = $RESULTADO_token->accion;
    if (isset($RESULTADO_token->tipo)) {
        if ($RESULTADO_token->tipo == 1) {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    if ($accion == "add") {
                        $personData = json_decode($_POST['class']);
                        $insAlbumClass = new Album();
                        $insAlbumClass->setDesde($personData->desde);
                        $insAlbumClass->setHasta($personData->hasta);
                        $insAlbumClass->setTipo($personData->tipo);
                        $insAlbumClass->setNombre($personData->nombre);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $insalbum->agregar_album_controlador($insAlbumClass);
                    } else if ($accion == "update") {

                        $personData = json_decode($_POST['class']);
                        $insAlbumClass = new Album();
                        $insAlbumClass->setIdAlbum($personData->idalbum);
                        $insAlbumClass->setDesde($personData->desde);
                        $insAlbumClass->setHasta($personData->hasta);
                        $insAlbumClass->setTipo($personData->tipo);
                        $insAlbumClass->setNombre($personData->nombre);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $insalbum->actualizar_album_controlador($insAlbumClass);
                    } else {
                        header("HTTP/1.1 500");
                    }

                    break;

                case 'GET':
                    if ($accion == "delete") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        $insAlbumClass = new Album();
                        $insAlbumClass->setIdalbum($_GET['id']);
                        echo $insalbum->eliminar_album_controlador($insAlbumClass);
                    } else if ($accion == "paginate") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insalbum->bean_paginador_album_controlador($_GET['pagina'], $_GET['registros']));
                    } else if ($accion == "obtener") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insalbum->datos_album_controlador("conteo", 0));
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

                case 'GET':
                    if ($accion == "paginate") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insalbum->bean_paginador_album_controlador($_GET['pagina'], $_GET['registros'], $RESULTADO_token->codigo, $_GET['filtro']));
                    } else if ($accion == "obtener") {
                        $insAlbumClass = new Album();
                        $insAlbumClass->setCuenta($RESULTADO_token->codigo);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insalbum->obtener_album_alumno_controlador($insAlbumClass));
                    } else {
                        header("HTTP/1.1 500");
                    }

                    break;
                default:
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
