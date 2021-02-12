<?php
require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();
if (!empty($RESULTADO_token)) {
    require_once './classes/principal/librocuenta.php';
    require_once './classes/principal/cuenta.php';
    require_once './controladores/librocuentaControlador.php';
    $inslibrocuenta = new librocuentaControlador();
    $accion = $RESULTADO_token->accion;
    if (isset($RESULTADO_token->tipo)) {
        if ($RESULTADO_token->tipo == 1) {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    if ($accion == "add") {
                        $personData = json_decode($_POST['class']);
                        $insLibroCuentaClass = new LibroCuenta();
                        $insLibroCuentaClass->setNombre($personData->nombre);
                        $insLibroCuentaClass->setApellido($personData->apellido);
                        $insLibroCuentaClass->setTelefono($personData->telefono);
                        $insLibroCuentaClass->setOcupacion($personData->ocupacion);
                        $insLibroCuentaClass->setPais($personData->pais);
                        $insLibroCuentaClass->setCuenta($personData->cuenta);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $inslibrocuenta->agregar_librocuenta_controlador(1, $insLibroCuentaClass);
                    } else if ($accion == "update") {

                        require_once './classes/principal/usuario.php';
                        require_once './classes/principal/empresa.php';
                        require_once './api/security/auth.php';
                        $insToken = new Auth();
                        $insUser = new Usuario();
                        $personData = json_decode($_POST['class']);
                        $insLibroCuentaClass = new LibroCuenta();
                        $insLibroCuentaClass->setIdLibroCuenta($personData->idlibrocuenta);
                        $insLibroCuentaClass->setNombre($personData->nombre);
                        $insLibroCuentaClass->setApellido($personData->apellido);
                        $insLibroCuentaClass->setTelefono($personData->telefono);
                        $insLibroCuentaClass->setOcupacion($personData->ocupacion);
                        $insLibroCuentaClass->setPais($personData->pais);
                        $insLibroCuentaClass->setCuenta($personData->cuenta);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');

                        $insUser->setId($personData->cuenta->idcuenta);
                        $insUser->setUsuario($personData->cuenta->usuario);
                        $insUser->setEmail($personData->cuenta->email);
                        $insUser->setTipo(2);
                        $insUser->setCodigo($personData->cuenta->codigo);
                        $respuestaToken = $insToken->autenticar($insUser);
                        echo ($inslibrocuenta->actualizar_datos_email_librocuenta_controlador(1, $insLibroCuentaClass, $respuestaToken['token']));
                    } else if ($accion == "updateestado") {

                        $personData = json_decode($_POST['class']);
                        $insCuentaClass = new Cuenta();
                        $insCuentaClass->setIdCuenta($personData->idcuenta);
                        $insCuentaClass->setCuentaCodigo($personData->codigo);
                        $insCuentaClass->setEstado($personData->estado);

                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $inslibrocuenta->actualizar_cuenta_estado_controlador($insCuentaClass);
                    } else {
                        header("HTTP/1.1 500");
                    }

                    break;

                case 'GET':
                    if ($accion == "delete") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        $insLibroCuentaClass = new LibroCuenta();
                        $insLibroCuentaClass->setIdLibroCuenta($_GET['id']);
                        echo $inslibrocuenta->eliminar_librocuenta_controlador($insLibroCuentaClass);
                    } else if ($accion == "paginate") {
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($inslibrocuenta->bean_paginador_librocuenta_controlador($_GET['pagina'], $_GET['registros'], $_GET['estado'], $_GET['filtro']));
                    } else {
                        header("HTTP/1.1 500");
                    }
                    break;
                default:

                    header("HTTP/1.1 404");

                    break;
            }

        } elseif ($RESULTADO_token->tipo == 2) {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    if ($accion == "update") {

                        $personData = json_decode($_POST['class']);
                        $insLibroCuentaClass = new LibroCuenta();
                        $insLibroCuentaClass->setNombre($personData->nombre);
                        $insLibroCuentaClass->setApellido($personData->apellido);
                        $insLibroCuentaClass->setTelefono($personData->telefono);
                        $insLibroCuentaClass->setOcupacion($personData->ocupacion);
                        $insLibroCuentaClass->setPais($personData->pais);
                        $insCuenta = new Cuenta();
                        $insCuenta->setUsuario($personData->cuenta->usuario);
                        $insCuenta->setEmail($personData->cuenta->email);
                        $insCuenta->setClave($personData->cuenta->clave);
                        $insCuenta->setCuentaCodigo($RESULTADO_token->codigo);
                        $insLibroCuentaClass->setCuenta($insCuenta->__toString());
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $inslibrocuenta->actualizar_librocuenta_controlador(0, $insLibroCuentaClass);
                    } else {
                        header("HTTP/1.1 500");
                    }
                    break;
                case 'GET':
                    if ($accion == "obtener") {
                        $insLibroCuentaClass = new LibroCuenta();
                        $insLibroCuentaClass->setCuenta($RESULTADO_token->codigo);
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo json_encode($inslibrocuenta->datos_librocuenta_controlador("perfil", $insLibroCuentaClass));
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
