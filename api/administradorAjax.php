<?php
require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();

if (!empty($RESULTADO_token)) {
    require_once './classes/principal/administrador.php';
    require_once './classes/principal/cuenta.php';
    require_once './controladores/administradorControlador.php';
    $insadministrador = new administradorControlador();
    $accion = $RESULTADO_token->accion;
    if (isset($RESULTADO_token->tipo)) {
        if ($RESULTADO_token->tipo == 1) {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    if ($accion == "add") {
                        $personData = json_decode($_POST['class']);
                        $insAdministradorClass = new Administrador();
                        $insAdministradorClass->setNombre($personData->nombre);
                        $insAdministradorClass->setApellido($personData->apellido);
                        $insAdministradorClass->setTelefono($personData->telefono);
                        $insAdministradorClass->setPais($personData->pais);
                        $insAdministradorClass->setCuenta($personData->cuenta);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $insadministrador->agregar_administrador_controlador(1, $insAdministradorClass);
                    } else if ($accion == "update") {

                        $personData = json_decode($_POST['class']);
                        $insAdministradorClass = new Administrador();
                        $insAdministradorClass->setIdAdministrador($personData->idadministrador);
                        $insAdministradorClass->setNombre($personData->nombre);
                        $insAdministradorClass->setApellido($personData->apellido);
                        $insAdministradorClass->setTelefono($personData->telefono);
                        $insAdministradorClass->setOcupacion($personData->ocupacion);
                        $insAdministradorClass->setPais($personData->pais);
                        $insAdministradorClass->setCuenta($personData->cuenta);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $insadministrador->actualizar_administrador_controlador(1, $insAdministradorClass);
                    } else if ($accion == "updateestado") {

                        $personData = json_decode($_POST['class']);
                        $insCuentaClass = new Cuenta();
                        $insCuentaClass->setIdCuenta($personData->idcuenta);
                        $insCuentaClass->setCuentaCodigo($personData->codigo);
                        $insCuentaClass->setEstado($personData->estado);

                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $insadministrador->actualizar_cuenta_estado_controlador($insCuentaClass);
                    } else {
                        header("HTTP/1.1 500");
                    }

                    break;

                case 'GET':
                    if ($accion == "delete") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        $insAdministradorClass = new Administrador();
                        $insAdministradorClass->setIdAdministrador($_GET['id']);
                        echo $insadministrador->eliminar_administrador_controlador($insAdministradorClass);
                    } else if ($accion == "paginate") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insadministrador->bean_paginador_administrador_controlador($_GET['pagina'], $_GET['registros'], $_GET['estado']));
                    } else if ($accion == "obtener") {
                        $insAdministradorClass = new Administrador();
                        $insAdministradorClass->setCuenta($RESULTADO_token->codigo);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($insadministrador->datos_administrador_controlador("perfil", $insAdministradorClass));
                    } else if ($accion == "reporte") {
                        $insCuentaClass = new Cuenta();
                        $insCuentaClass->setTipo(1);
                        $insCuentaClass->setEstado($_GET['estado']);
                        $insAdministradorClass = new Administrador();
                        $insAdministradorClass->setCuenta($insCuentaClass);
                        header("Content-Type: application/vnd.ms-excel");
                        echo ($insadministrador->reporte_administrador_controlador("tipo-cuenta", $insAdministradorClass));
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
        return header("HTTP/1.1 403");
    }

} else {
    return header("HTTP/1.1 403");
}
