<?php
require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();
if (!empty($RESULTADO_token)) {
    require_once './classes/principal/subitem.php';

    require_once './controladores/subitemControlador.php';

    $inssubitem = new subitemControlador();
    $accion = $RESULTADO_token->accion;
    if (!isset($RESULTADO_token->tipo)) {
        if ($accion == "obtener" || $accion == "paginate") {
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
                        $visita->setPagina("matricula");
                        $visita->setFecha($datosPlataforma['fecha']);
                        $visita->setContador(1);
                        $visita->setInfo($datosPlataforma['info']);
                        $vistResultado = $insVisita->agregar_visita_controlador($visita);
                        $idcurso = -1;
                        if (isset($_GET['idcurso'])) {
                            $idcurso = $_GET['idcurso'];
                        }

                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($inssubitem->bean_paginador_subitem_controlador($_GET['pagina'], $_GET['registros'], $_GET['tipo'], $idcurso));
                    } else if ($accion == "obtener") {
                        $insSubItemClass = new SubItem();
                        $insSubItemClass->setTipo($_GET['tipo']);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($inssubitem->datos_subitem_controlador("tipo", $insSubItemClass));
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
                    $insSubItemClass = new SubItem();
                    $insSubItemClass->setTitulo($personData->titulo);
                    $insSubItemClass->setDetalle($personData->detalle);
                    $insSubItemClass->setTipo($personData->tipo);
                    if (isset($personData->curso)) {
                        $insSubItemClass->setCurso($personData->curso);
                    } else {
                        $insSubItemClass->setCurso(null);
                    }
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $inssubitem->agregar_subitem_controlador($insSubItemClass);
                } else if ($accion == "update") {

                    $personData = json_decode($_POST['class']);
                    $insSubItemClass = new SubItem();
                    $insSubItemClass->setIdsubitem($personData->idsubitem);
                    $insSubItemClass->setTitulo($personData->titulo);
                    $insSubItemClass->setDetalle($personData->detalle);
                    $insSubItemClass->setTipo($personData->tipo);
                    if (isset($personData->curso)) {
                        $insSubItemClass->setCurso($personData->curso);
                    } else {
                        $insSubItemClass->setCurso(null);
                    }
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $inssubitem->actualizar_subitem_controlador($insSubItemClass);
                } else {
                    header("HTTP/1.1 500");
                }

                break;

            case 'GET':
                if ($accion == "delete") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    $insSubItemClass = new SubItem();
                    $insSubItemClass->setIdSubItem($_GET['id']);
                    echo $inssubitem->eliminar_subitem_controlador($insSubItemClass);
                } else if ($accion == "paginate") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');

                    echo json_encode($inssubitem->bean_paginador_subitem_controlador($_GET['pagina'], $_GET['registros'], $_GET['tipo']));
                } else if ($accion == "obtener") {
                    $insSubItemClass = new SubItem();
                    $insSubItemClass->setTipo($_GET['tipo']);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($inssubitem->datos_subitem_controlador("tipo", $insSubItemClass));
                } else if ($accion == "curso") {
                    $insSubItemClass = new SubItem();
                    $insSubItemClass->setTipo($_GET['tipo']);
                    $insSubItemClass->setCurso($_GET['idcurso']);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($inssubitem->datos_subitem_controlador("curso", $insSubItemClass));
                } else {
                    header("HTTP/1.1 500");
                }

                break;
            default:

                header("HTTP/1.1 404");
                // echo '<script> window.location.href="' . SERVERURL . 'login" </script>';
                break;
        }
    }

} else {
    return header("HTTP/1.1 403");
}
