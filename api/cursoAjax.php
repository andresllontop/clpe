<?php

require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();

if (!empty($RESULTADO_token)) {
    require_once './classes/principal/curso.php';
    require_once './controladores/cursoControlador.php';
    $inscurso = new cursoControlador();
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
                        $visita->setPagina("curso");
                        $visita->setFecha($datosPlataforma['fecha']);
                        $visita->setContador(1);
                        $visita->setInfo($datosPlataforma['info']);
                        $vistResultado = $insVisita->agregar_visita_controlador($visita);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($inscurso->bean_paginador_curso_controlador($_GET['pagina'], $_GET['registros']));
                    } else if ($accion == "obtener") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($inscurso->datos_curso_controlador("conteo", 0));
                    } else if ($accion == "get") {

                        $insCursoClass = new Curso();
                        $insCursoClass->setIdCurso($_GET['id']);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($inscurso->datos_curso_controlador("unico", $insCursoClass));
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
                    $insCursoClass = new Curso();
                    $insCursoClass->setTitulo($personData->titulo);
                    $insCursoClass->setPrecio($personData->precio);
                    $insCursoClass->setDescripcion($personData->descripcion);
                    $insCursoClass->setDescuento($personData->descuento);
                    //TIPO=1 PAGADO ; TIPO=2 MEDIANTE ZOOM;
                    $insCursoClass->setTipo($personData->tipo);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $inscurso->agregar_curso_controlador($insCursoClass);
                } else if ($accion == "update") {

                    $personData = json_decode($_POST['class']);
                    $insCursoClass = new Curso();
                    $insCursoClass->setIdCurso($personData->idcurso);
                    $insCursoClass->setTitulo($personData->titulo);
                    $insCursoClass->setPrecio($personData->precio);
                    $insCursoClass->setDescripcion($personData->descripcion);
                    $insCursoClass->setDescuento($personData->descuento);
                    //TIPO=1 PAGADO ; TIPO=2 MEDIANTE ZOOM;
                    $insCursoClass->setTipo($personData->tipo);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $inscurso->actualizar_curso_controlador($insCursoClass);
                } else {
                    header("HTTP/1.1 500");
                }

                break;

            case 'GET':
                if ($accion == "delete") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    $insCursoClass = new Curso();
                    $insCursoClass->setIdCurso($_GET['id']);
                    echo $inscurso->eliminar_curso_controlador($insCursoClass);
                } else if ($accion == "paginate") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($inscurso->bean_paginador_curso_controlador($_GET['pagina'], $_GET['registros']));
                } else if ($accion == "obtener") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($inscurso->datos_curso_controlador("conteo", 0));
                } else if ($accion == "get") {

                    $insCursoClass = new Curso();
                    $insCursoClass->setIdCurso($_GET['id']);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($inscurso->datos_curso_controlador("unico", $insCursoClass));
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
