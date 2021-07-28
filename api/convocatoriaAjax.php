<?php
require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();
if (!empty($RESULTADO_token)) {

    $accion = $RESULTADO_token->accion;

    if (isset($RESULTADO_token->tipo)) {

        switch ($_SERVER['REQUEST_METHOD']) {

            case 'POST':
                ObtenerClasesConvocatoria();
                $insconvocatoria = new convocatoriaControlador();
                if ($accion == "add") {

                    $personData = json_decode($_POST['class']);
                    $insConvocatoriaClass = new BeanDetalleConvocatoria();
                    $insConvocatoriaClass->setConvocatoria($personData->convocatoria);
                    $insConvocatoriaClass->setListDetalle($personData->lista);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $insconvocatoria->agregar_convocatoria_controlador($insConvocatoriaClass);
                } else if ($accion == "update") {

                    $personData = json_decode($_POST['class']);
                    $insConvocatoriaClass = new BeanDetalleConvocatoria();
                    $insConvocatoriaClass->setConvocatoria($personData->convocatoria);
                    $insConvocatoriaClass->setListDetalle($personData->lista);

                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $insconvocatoria->actualizar_convocatoria_controlador($insConvocatoriaClass);
                } else if ($accion == "estado") {
                    $personData = json_decode($_POST['class']);
                    $insConvocatoriaClass = new Convocatoria();
                    $insConvocatoriaClass->setIdConvocatoria($personData->idconvocatoria);
                    $insConvocatoriaClass->setEstado($personData->estado);

                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $insconvocatoria->estado_convocatoria_controlador($insConvocatoriaClass);
                } else {
                    header("HTTP/1.1 500");
                }

                break;

            case 'GET':
                if ($accion == "delete") {
                    ObtenerClasesConvocatoria();
                    $insconvocatoria = new convocatoriaControlador();
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    $insConvocatoriaClass = new Convocatoria();
                    $insConvocatoriaClass->setIdConvocatoria($_GET['id']);
                    echo $insconvocatoria->eliminar_convocatoria_controlador($insConvocatoriaClass);
                } else if ($accion == "paginate") {
                    ObtenerClasesConvocatoria();
                    $insconvocatoria = new convocatoriaControlador();
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($insconvocatoria->bean_paginador_convocatoria_controlador($_GET['pagina'], $_GET['registros'], $_GET['filtro']));
                } else if ($accion == "detalle") {
                    ObtenerClasesConvocatoria();
                    $insconvocatoria = new convocatoriaControlador();
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    $insConvocatoriaClass = new Convocatoria();
                    $insConvocatoriaClass->setIdConvocatoria($_GET['id']);
                    echo json_encode($insconvocatoria->datos_convocatoria_controlador("detalle", $insConvocatoriaClass));
                } else if ($accion == "obtener") {
                    ObtenerClasesConvocatoria();
                    $insconvocatoria = new convocatoriaControlador();
                    $Convocatoria = new Convocatoria();
                    $Convocatoria->setEstado(1);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($insconvocatoria->datos_convocatoria_controlador("estado", $Convocatoria));
                } else if ($accion == "paginaterespuesta") {
                    ObtenerClasesRespuestaConvocatoria();
                    $insconvocatoria = new respuestaconvocatoriaControlador();
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($insconvocatoria->bean_paginador_respuestaconvocatoria_controlador($_GET['pagina'], $_GET['registros'], $_GET['filtro']));
                } else if ($accion == "detallerespuesta") {
                    ObtenerClasesRespuestaConvocatoria();
                    $insconvocatoria = new respuestaconvocatoriaControlador();
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    $insConvocatoriaClass = new PersonaConvocatoria();
                    $insConvocatoriaClass->setIdPersonaConvocatoria($_GET['id']);
                    echo json_encode($insconvocatoria->datos_respuestaconvocatoria_controlador("detalle", $insConvocatoriaClass));
                } else if ($accion == "deleterespuesta") {
                    ObtenerClasesRespuestaConvocatoria();
                    $insconvocatoria = new respuestaconvocatoriaControlador();
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    $insConvocatoriaClass = new PersonaConvocatoria();
                    $insConvocatoriaClass->setIdPersonaConvocatoria($_GET['id']);
                    echo $insconvocatoria->eliminar_respuestaconvocatoria_controlador($insConvocatoriaClass);
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
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':

                if ($accion == "add") {
                    ObtenerClasesRespuestaConvocatoria();
                    $personData = json_decode($_POST['class']);

                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    $insconvocatoria = new respuestaconvocatoriaControlador();
                    echo $insconvocatoria->agregar_respuestaconvocatoria_controlador($personData->lista);
                } else {
                    header("HTTP/1.1 500");
                }

                break;

            case 'GET':
                if ($accion == "obtener") {
                    ObtenerClasesConvocatoria();
                    $insconvocatoria = new convocatoriaControlador();
                    $Convocatoria = new Convocatoria();
                    $Convocatoria->setEstado(1);
                    $Convocatoria->setIdConvocatoria($_GET['id']);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($insconvocatoria->datos_convocatoria_controlador("unico-estado", $Convocatoria));
                } else {
                    header("HTTP/1.1 500");
                }

                break;
            default:
                header("HTTP/1.1 500");
                break;
        }
    }

} else {

    header("HTTP/1.1 500");
}
function ObtenerClasesConvocatoria()
{
    require_once './classes/principal/convocatoria.php';
    require_once './classes/principal/detalleconvocatoria.php';
    require_once './classes/utilities/beandetalleconvocatoria.php';
    require_once './controladores/convocatoriaControlador.php';

}
function ObtenerClasesRespuestaConvocatoria()
{
    require_once './classes/principal/respuestaconvocatoria.php';
    require_once './controladores/respuestaconvocatoriaControlador.php';

}
