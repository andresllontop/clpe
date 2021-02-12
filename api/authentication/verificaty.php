<?php
require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->HeaderToken();
if (!empty($RESULTADO_token)) {
    require_once './controladores/loginControlador.php';
    require_once './classes/principal/cuenta.php';
    require_once './classes/principal/usuario.php';
    require_once './api/security/auth.php';

    $insBeanCrud = new BeanCrud();
    $insCuenta = new Cuenta();
    $inslogin = new loginControlador();
    $insToken = new Auth();
    $accion = $RESULTADO_token->accion;

    if (isset($RESULTADO_token->tipo)) {

        switch ($_SERVER['REQUEST_METHOD']) {

            case 'POST':
                if ($accion == "token") {
                    require_once './classes/principal/cliente.php';
                    $personData = json_decode($_POST['class']);
                    $insCuenta->setCuentaCodigo($RESULTADO_token->codigo);
                    $insCuenta->setEstado(1);
                    $insCuenta->setVerificacion($personData->codigoverificacion);

                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    $cuentaUsuario = $inslogin->actualizar_login_controlador($insCuenta);

                    if ($cuentaUsuario['beanPagination'] != null) {
                        $insUser = new Usuario();
                        $insUser->setId($cuentaUsuario['beanPagination']['principal']['list'][0]['idcuenta']);
                        $insUser->setUsuario($cuentaUsuario['beanPagination']['principal']['list'][0]['usuario']);
                        $insUser->setEmail($cuentaUsuario['beanPagination']['principal']['list'][0]['email']);
                        $insUser->setTipo($cuentaUsuario['beanPagination']['principal']['list'][0]['tipo']);
                        $insUser->setCodigo($cuentaUsuario['beanPagination']['principal']['list'][0]['cuentaCodigo']);
                        $insUser->setFoto($cuentaUsuario['beanPagination']['principal']['list'][0]['foto']);

                        if ($insUser->getTipo() == 2) {
                            $insUser->setEmpresa($cuentaUsuario['beanPagination']['empresa']);
                        }

                        $respuestaToken = $insToken->autenticar($insUser);
                        $insBeanCrud->setMessageServer("ok");
                        $insBeanCrud->setBeanPagination($respuestaToken);

                    } else {
                        $insBeanCrud->setMessageServer($cuentaUsuario['messageServer']);
                    }

                    echo json_encode($insBeanCrud->__toString());

                } else {
                    header("HTTP/1.1 500");
                }

                break;
            case 'GET':
                if ($accion == "obtener") {
                    $insCuenta->setCuentaCodigo($RESULTADO_token->codigo);
                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode($inslogin->datos_obtener_controlador("obtener", $insCuenta));
                } else {
                    header("HTTP/1.1 404");
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
