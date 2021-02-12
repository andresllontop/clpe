<?php
$peticionAJAX = true;
require_once '../core/configGeneral.php';
require_once '../controladores/videosControlador.php';
$insAdmin = new videosControlador();

$insvideos = new videosControlador();

if (isset($_POST['accion']) || isset($_GET['acion'])) {
    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'save':
                echo $insvideos->agregar_videos_controlador();
                break;
            case 'delete':
                echo $insvideos->eliminar_videos_controlador();
                break;
            case 'update':
                echo $insvideos->actualizar_videos_controlador();
                break;
            case 'updateInicio':
                echo $insvideos->actualizarInicio_videos_controlador();
                break;
            case 'updateNosotros':
                echo $insvideos->actualizarNosotros_videos_controlador();
                break;
            default:
                echo $insvideos->bean_paginador_videos_controlador(1, 0);
                break;
        }
    } else if ($_GET['acion'] == 'listar') {
        if (isset($_GET['ubica'])) {
            echo $insvideos->bean_paginador_videos_controlador($_GET['pagina'], $_GET['registros'],$_GET['ubica']);
        } else {
            echo $insvideos->bean_paginador_videos_controlador($_GET['pagina'], $_GET['registros'],0);
        }
        
    } else if ($_GET['acion'] == 'listando') {
        echo json_encode($insvideos->datos_videos_controlador("conteo", 0));
    }
} else {
    session_start();
    session_destroy();
    echo '<script> window.location.href="' . SERVERURL . 'login" </script>';
}
