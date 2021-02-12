<?php

if (isset($_POST['accion']) || isset($_GET['acion'])) {
    require_once '../core/configGeneral.php';
    require_once '../controladores/arbolControlador.php';
    $insarbol = new arbolControlador();
    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'save':
                echo $insarbol->agregar_arbol_controlador();
                break;
            case 'delete':
                echo $insarbol->eliminar_arbol_controlador();
                break;
            case 'update':
                echo $insarbol->actualizar_arbol_controlador();
                break;
            case 'datos':
                echo json_encode($insarbol->datos_arbol_controlador("cuentadmin", $_POST["codigo"]));
                break;
            case 'listarDato':
                echo $insarbol->paginador_arbol_controlador($_POST["codigo"]);
                break;
            case 'listarEconomico':
                echo json_encode($insarbol->datos_arbol_controlador("cuentapadre", $_POST["codigo"]));
                break;
            default:
                echo 'holi Hacker .l.';
                break;
        }
    } else if ($_GET['acion'] == 'listar') {
        echo $insarbol->paginador_arbol_controlador();
    } else if ($_GET['acion'] == 'datos') {
        echo json_encode($insarbol->datos_arbol_controlador("conteo", 0));
    }
} else {
    session_start();
    session_destroy();
    echo '<script> window.location.href="' . SERVERURL . 'login" </script>';
}
