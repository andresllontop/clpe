<?php
if (!empty($_POST)) {
    $peticionAJAX = true;
    require_once '../core/configGeneral.php';
    require_once '../controladores/empresaControlador.php';
    require_once '../controladores/administradorControlador.php';
    $insadministrador = new administradorControlador();
    $insempresa = new empresaControlador();
    $lista = $insadministrador->datos_cuenta_controlador("unico", $_POST['ID-reg']);
    if (count($lista) > 0) {
        $listaempresa = $insempresa->datos_empresa_controlador("conteo", 0);
        $from = $lista[0]['email'];
        // $to = 'llontopdiazandres@gmail.com'; //replace with your email
        $to = $listaempresa[0]['EmpresaEmail']; //replace with your email
        // print_r($listaempresa[0]['EmpresaEmail']);
        // print_r($lista);
        $name = $listaempresa[0]['EmpresaNombre'];
        $subject = @trim(stripslashes($lista[0]['usuario']));
        $message = "FELICIDADES!! Usted ya se encuentra matriculado al CLUB DE LECTURA PARA EMPRENDEDORES\n";
        $message .= "Para poder acceder al Sistema ingresa tu correo electronico y contraseña que se describen a continuación.\n";
        $message .= "CORREO ELECTRONICO :" . $lista[1] . "\n";
        $message .= "CONTRASEÑA :" . $lista[1] . "\n";
        $message .= "Ingresa a la siguiente URL para poder iniciar seccion : " . SERVERURL;
        // $message .= "";
        $headers = array();
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-type: text/plain; charset=iso-8859-1";
        $headers[] = "From: {$name} <{$from}>";
        $headers[] = "Reply-To: <{$from}>";
        $headers[] = "Subject: {$subject}";
        $headers[] = "X-Mailer: PHP/" . phpversion();

        if (mail($to, $subject, $message, implode("\r\n", $headers))) {
            $alerta = [
                "Alerta" => "limpiar",
                "Titulo" => "Enviado",
                "Texto" => "Revisa tu email",
                "Tipo" => "success",
            ];
        } else {
            $alerta = [
                "Alerta" => "simple",
                "Titulo" => "Ocurrio un error inesperado",
                "Texto" => "No se envio el mensaje",
                "Tipo" => "error",

            ];
        }

    } else {
        $alerta = [
            "Alerta" => "simple",
            "Titulo" => "Ocurrio un error inesperado",
            "Texto" => "el email que acabas de ingresar no coinciden",
            "Tipo" => "error",

        ];
    }
    echo json_encode($alerta);

} else {
    echo '<script> window.location.href="' . SERVERURL . 'login" </script>';
}
