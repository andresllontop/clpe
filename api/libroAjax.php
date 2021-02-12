<?php
require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();

if (!empty($RESULTADO_token)) {
    require_once './classes/principal/libro.php';
    require_once './classes/principal/categoria.php';
    require_once './controladores/libroControlador.php';
    $inslibro = new libroControlador();
    $accion = $RESULTADO_token->accion;
    if (isset($RESULTADO_token->tipo)) {
        if ($RESULTADO_token->tipo == 1) {
            switch ($_SERVER['REQUEST_METHOD']) {

                case 'POST':

                    if ($accion == "add") {
                        $personData = json_decode($_POST['class']);
                        $insLibroClass = new Libro();
                        $insLibroClass->setCodigo($personData->codigo);
                        $insLibroClass->setDescripcion($personData->descripcion);
                        $insLibroClass->setCategoria($personData->categoria);
                        $insLibroClass->setNombre($personData->nombre);
                        $insLibroClass->setEstado($personData->estado);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $inslibro->agregar_libro_controlador($insLibroClass);
                    } else if ($accion == "update") {

                        $personData = json_decode($_POST['class']);
                        $insLibroClass = new Libro();
                        $insLibroClass->setCodigo($personData->codigo);
                        $insLibroClass->setIdLibro($personData->idlibro);
                        $insLibroClass->setDescripcion($personData->descripcion);
                        $insLibroClass->setCategoria($personData->categoria);
                        $insLibroClass->setNombre($personData->nombre);
                        $insLibroClass->setEstado($personData->estado);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $inslibro->actualizar_libro_controlador($insLibroClass);
                    } else {
                        header("HTTP/1.1 500");
                    }

                    break;

                case 'GET':
                    if ($accion == "delete") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        $insLibroClass = new Libro();
                        $insLibroClass->setIdLibro($_GET['id']);
                        echo $inslibro->eliminar_libro_controlador($insLibroClass);
                    } else if ($accion == "paginate") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($inslibro->bean_paginador_libro_controlador($_GET['pagina'], $_GET['registros']));
                    } else if ($accion == "obtener") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($inslibro->datos_libro_controlador("conteo", 0));
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
                        echo json_encode($inslibro->bean_paginador_libro_controlador($_GET['pagina'], $_GET['registros']));
                    } else if ($accion == "cuenta") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        $insLibroClass = new Libro();
                        $insLibroClass->setCuenta($RESULTADO_token->codigo);
                        echo json_encode($inslibro->datos_libro_controlador("cuenta", $insLibroClass));
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
            return header("HTTP/1.1 403");
        }

    } else {
        return header("HTTP/1.1 403");
    }

} else {
    return header("HTTP/1.1 403");
}
