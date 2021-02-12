<?php
require_once '../core/configGeneral.php';
require_once '../controladores/cuentaControlador.php';
$inscuenta = new cuentaControlador();

if (isset($_POST['accion']) || isset($_GET['acion'])) {

    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'update':
                echo $inscuenta->actualizar_cuenta_controlador();
                break;
            default:
                echo ("");
                break;
        }
    } else if ($_GET['acion'] == 'listar') {
        echo '<script> window.location.href="' . SERVERURL . 'login" </script>';
    }
} else {
    session_start();
    session_destroy();
    echo '<script> window.location.href="' . SERVERURL . 'login" </script>';
}
