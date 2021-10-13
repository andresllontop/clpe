<?php

if (isset($_SERVER['CONTENT_TYPE'])) {

    if ($_SERVER['CONTENT_TYPE'] == "application/x-www-form-urlencoded; charset=UTF-8") {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                if (isset($_POST["login"]) && isset($_POST["password"])) {

                    require_once './core/configAPP.php';
                    require_once './controladores/loginControlador.php';
                    require_once './classes/principal/cuenta.php';
                    require_once './classes/principal/usuario.php';
                    require_once './api/security/auth.php';

                    $insBeanCrud = new BeanCrud();
                    $insCuenta = new Cuenta();
                    $inslogin = new loginControlador();
                    $insToken = new Auth();

                    $insCuenta->setEmail($_POST["login"]);
                    $insCuenta->setClave($_POST["password"]);

                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');

                    $cuentaUsuario = $inslogin->datos_login_controlador($insCuenta);

                    if ($cuentaUsuario['beanPagination'] != null) {
                        $insUser = new Usuario();
                        $insUser->setId($cuentaUsuario['beanPagination']['cuenta']['idcuenta']);
                        $insUser->setUsuario($cuentaUsuario['beanPagination']['cuenta']['usuario']);
                        $insUser->setEmail($cuentaUsuario['beanPagination']['cuenta']['email']);
                        $insUser->setTipo($cuentaUsuario['beanPagination']['cuenta']['tipo']);
                        $insUser->setPerfil($cuentaUsuario['beanPagination']['cuenta']['perfil']);
                        $insUser->setCodigo($cuentaUsuario['beanPagination']['cuenta']['cuentaCodigo']);
                        $insUser->setFoto($cuentaUsuario['beanPagination']['cuenta']['foto']);
                        if ($insUser->getTipo() == 2) {
                            $insUser->setEmpresa($cuentaUsuario['beanPagination']['empresa']);
                            $insUser->setLibroCode($cuentaUsuario['beanPagination']['libros'][0]);
                        }

                        $respuestaToken = $insToken->autenticar($insUser);
                        $insBeanCrud->setMessageServer("ok");
                        $insBeanCrud->setBeanPagination($respuestaToken);

                    } else {
                        $insBeanCrud->setMessageServer($cuentaUsuario['messageServer']);
                    }

                    echo json_encode($insBeanCrud->__toString());
                } else {
                    header("HTTP/1.1 403");
                }

                break;
            default:
                header("HTTP/1.1 403");
                break;
        }
    } else {
        header("HTTP/1.1 403");
    }
} else {
    header("HTTP/1.1 403");
}
