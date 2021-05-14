<?php

require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();

if (!empty($RESULTADO_token)) {
    require_once './classes/principal/blog.php';
    require_once './controladores/blogControlador.php';
    $insblog = new blogControlador();
    $accion = $RESULTADO_token->accion;
    if (!isset($RESULTADO_token->tipo)) {
        if ($accion == "get" || $accion == "paginate") {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    if ($accion == "paginate") {
                        require_once './classes/utilities/plataforma.php';
                        require_once './classes/principal/visita.php';
                        require_once './controladores/visitaControlador.php';
                        $insVisita = new visitaControlador();
                        $insPlataforma = new Plataforma();
                        $datosPlataforma = $insPlataforma->write_visita();
                        //print_r($datosPlataforma);
                        $visita = new Visita();
                        $visita->setIp($datosPlataforma['ip']);
                        $visita->setPagina("blog");
                        $visita->setFecha($datosPlataforma['fecha']);
                        $visita->setContador(1);
                        $visita->setInfo($datosPlataforma['info']);
                        $vistResultado = $insVisita->agregar_visita_controlador($visita);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insblog->bean_paginador_blog_controlador($_GET['pagina'], $_GET['registros']));
                    } else if ($accion == "obtener") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insblog->datos_blog_controlador("conteo", 0));
                    } else if ($accion == "get") {

                        $insBlogClass = new Blog();
                        $insBlogClass->setIdBlog($_GET['id']);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insblog->datos_blog_controlador("unico", $insBlogClass));
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
                    $insBlogClass = new Blog();
                    $insBlogClass->setTitulo($personData->titulo);
                    $insBlogClass->setResumen($personData->resumen);
                    $insBlogClass->setDescripcion($personData->descripcion);
                    $insBlogClass->setArchivo($personData->archivo);
                    $insBlogClass->setTipoArchivo($personData->tipo_archivo);
                    $insBlogClass->setComentario($personData->comentario);
                    $insBlogClass->setAutor($personData->autor);
                    $insBlogClass->setDescripcionAutor($personData->descripcionAutor);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $insblog->agregar_blog_controlador($insBlogClass);
                } else if ($accion == "update") {

                    $personData = json_decode($_POST['class']);
                    $insBlogClass = new Blog();
                    $insBlogClass->setIdBlog($personData->idblog);
                    $insBlogClass->setTitulo($personData->titulo);
                    $insBlogClass->setResumen($personData->resumen);
                    $insBlogClass->setDescripcion($personData->descripcion);
                    $insBlogClass->setArchivo($personData->archivo);
                    $insBlogClass->setAutor($personData->autor);
                    $insBlogClass->setTipoArchivo($personData->tipo_archivo);
                    $insBlogClass->setComentario($personData->comentario);
                    $insBlogClass->setDescripcionAutor($personData->descripcionAutor);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $insblog->actualizar_blog_controlador($insBlogClass);
                } else {
                    header("HTTP/1.1 500");
                }

                break;

            case 'GET':
                if ($accion == "delete") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    $insBlogClass = new Blog();
                    $insBlogClass->setIdBlog($_GET['id']);
                    echo $insblog->eliminar_blog_controlador($insBlogClass);
                } else if ($accion == "paginate") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($insblog->bean_paginador_blog_controlador($_GET['pagina'], $_GET['registros']));
                } else if ($accion == "obtener") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($insblog->datos_blog_controlador("conteo", 0));
                } else if ($accion == "get") {

                    $insBlogClass = new Blog();
                    $insBlogClass->setIdBlog($_GET['id']);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($insblog->datos_blog_controlador("unico", $insBlogClass));
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
