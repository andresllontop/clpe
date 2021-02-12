<?php
require_once '../core/configGeneral.php';
require_once '../controladores/declaracionControlador.php';
$insdeclaracion = new declaracionControlador();

if (isset($_POST['accion']) || isset($_GET['acion'])) {

    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'save':
                echo $insdeclaracion->agregar_declaracion_controlador();
                break;
            case 'delete':
                echo $insdeclaracion->eliminar_declaracion_controlador();
                break;
            default:
                echo $insdeclaracion->paginador_declaracion_controlador(1, 0);
                break;
        }
    } else if ($_GET['acion'] == 'listar') {
        echo $insdeclaracion->paginador_declaracion_controlador($_GET['pagina'], $_GET['registros'], $_GET['busca']);
    }
} else {
    session_start();
    session_destroy();
    echo '<script> window.location.href="' . SERVERURL . 'login" </script>';
}
