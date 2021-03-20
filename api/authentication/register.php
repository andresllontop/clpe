<?php
if (isset($_SERVER['CONTENT_TYPE'])) {
    if ($_SERVER['CONTENT_TYPE'] == "application/x-www-form-urlencoded; charset=UTF-8") {
        $values_path = $_SERVER['REDIRECT_URL'];
        $values_path = explode("/", $values_path);
        $accion = $values_path[sizeof($values_path) - 1];
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                if ($accion == "register") {
                    if (isset($_POST["nombreRegister"]) && isset($_POST["apellidoRegister"]) && isset($_POST["oficioRegister"]) && isset($_POST["emailRegister"]) && isset($_POST["passRegister"]) && isset($_POST["telefonoRegister"]) && isset($_POST["countryRegister"])) {
                        require_once './core/configAPP.php';
                        require_once './classes/principal/cliente.php';
                        require_once './classes/principal/cuenta.php';
                        require_once './controladores/clienteControlador.php';
                        $inscliente = new clienteControlador();
                        $insClienteClass = new Cliente();
                        $insClienteClass->setNombre($_POST["nombreRegister"]);
                        $insClienteClass->setApellido($_POST["apellidoRegister"]);
                        $insClienteClass->setTelefono($_POST["telefonoRegister"]);
                        $insClienteClass->setOcupacion($_POST["oficioRegister"]);
                        $insClienteClass->setPais($_POST["countryRegister"]);
                        $insClienteClass->setTipoMedio($_POST["radioTipoComunicacionRegister"]);
                        $insClienteClass->setVendedor($_POST["codigoVendedorRegister"]);
                        $insCuentaClass = new Cuenta();
                        $insCuentaClass->setEmail($_POST["emailRegister"]);
                        $insCuentaClass->setUsuario($_POST["nombreRegister"]);
                        $insCuentaClass->setClave($_POST["passRegister"]);
                        $insCuentaClass->setPrecio(0);
                        $insClienteClass->setCuenta((object) $insCuentaClass->__toString());
                        header("HTTP/1.1 200");
                        header('Content-Type: application/json; charset=utf-8');
                        echo $inscliente->agregar_publico_cliente_controlador($insClienteClass);
                    } else {
                        header("HTTP/1.1 403");
                    }
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
