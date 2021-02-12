<?php
if (isset($_SERVER['CONTENT_TYPE'])) {
    if ($_SERVER['CONTENT_TYPE'] == "application/x-www-form-urlencoded; charset=UTF-8") {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                if (isset($_POST["contactAddressRecovery"])) {
                    require_once './core/configAPP.php';
                    require_once './controladores/loginControlador.php';
                    require_once './classes/principal/cuenta.php';
                    require_once './classes/principal/cliente.php';
                    require_once './classes/principal/usuario.php';
                    require_once './api/security/auth.php';
                    $insBeanCrud = new BeanCrud();
                    $insCuenta = new Cuenta();
                    $inslogin = new loginControlador();
                    $insToken = new Auth();

                    $insCuenta->setEmail($_POST["contactAddressRecovery"]);

                    header("HTTP/1.1 200");
                    header('Content-Type: application/json; charset=utf-8');

                    echo json_encode($inslogin->datos_login_tipo_controlador("email", $insCuenta, $insToken));
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
