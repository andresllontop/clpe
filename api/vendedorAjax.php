<?php

require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();
if (!empty($RESULTADO_token)) {
    require_once './classes/principal/vendedor.php';
    require_once './controladores/vendedorControlador.php';
    $insvendedor = new vendedorControlador();
    $accion = $RESULTADO_token->accion;
    if (!isset($RESULTADO_token->tipo)) {

        return header("HTTP/1.1 403");

    } else {
        if ($RESULTADO_token->tipo == 1) {switch ($_SERVER['REQUEST_METHOD']) {

            case 'POST':

                if ($accion == "add") {
                    $personData = json_decode($_POST['class']);
                    $insVendedorClass = new Vendedor();
                    $insVendedorClass->setTelefono($personData->telefono);
                    $insVendedorClass->setTipo($personData->tipo);
                    $insVendedorClass->setEmpresa($personData->empresa);
                    $insVendedorClass->setNombre($personData->nombre);
                    $insVendedorClass->setApellido($personData->apellido);
                    $insVendedorClass->setPais($personData->pais);
                    $insVendedorClass->setCodigo($personData->codigo);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $insvendedor->agregar_vendedor_controlador($insVendedorClass);
                } else if ($accion == "update") {

                    $personData = json_decode($_POST['class']);
                    $insVendedorClass = new Vendedor();
                    $insVendedorClass->setIdVendedor($personData->idvendedor);
                    $insVendedorClass->setTelefono($personData->telefono);
                    $insVendedorClass->setTipo($personData->tipo);
                    $insVendedorClass->setEmpresa($personData->empresa);
                    $insVendedorClass->setNombre($personData->nombre);
                    $insVendedorClass->setApellido($personData->apellido);
                    $insVendedorClass->setPais($personData->pais);
                    $insVendedorClass->setCodigo($personData->codigo);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo $insvendedor->actualizar_vendedor_controlador($insVendedorClass);
                } else {
                    header("HTTP/1.1 500");
                }

                break;

            case 'GET':
                if ($accion == "delete") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    $insVendedorClass = new Vendedor();
                    $insVendedorClass->setIdVendedor($_GET['id']);
                    echo $insvendedor->eliminar_vendedor_controlador($insVendedorClass);
                } else if ($accion == "paginate") {
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($insvendedor->bean_paginador_vendedor_controlador($_GET['pagina'], $_GET['registros']));
                } else {
                    header("HTTP/1.1 500");
                }

                break;
            default:
                header("HTTP/1.1 404");
                // echo '<script> window.location.href="' . SERVERURL . 'login" </script>';
                break;
        }}

    }

} else {
    return header("HTTP/1.1 403");
}
