<?php
if (!empty($_POST)) {
    $peticionAJAX = true;
    require_once '../core/configGeneral.php';
    require_once '../controladores/administradorControlador.php';
    require_once '../controladores/empresaControlador.php';
    $insadministrador = new administradorControlador();
    $insempresa = new empresaControlador();
    $to= @trim(stripslashes($_POST['email']));
    $lista = $insadministrador->reporte_administrador_controlador("email", $to);
    if (count($lista)>0) {
    $listaempresa = $insempresa->datos_empresa_controlador("conteo", 0);
    // $to = 'llontopdiazandres@gmail.com'; //replace with your email
    $from = $listaempresa[0]['EmpresaEmail']; //replace with your email
    // print_r($listaempresa[0]['EmpresaEmail']);
    // print_r($lista);
    $name = $listaempresa[0]['EmpresaNombre'];
    $subject = @trim(stripslashes($lista[0]['AdminNombre']));
    $message = "Restablecer contraseña\n";
    $message .= "Selecciona la siguiente url para que cambies de contraseña: ".SERVERURL."restablecerPassword/".$lista[0]['clave'];
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
        "Tipo" => "error"

    ];
    }
    echo json_encode($alerta); 

} else {
    echo '<script> window.location.href="' . SERVERURL . 'login" </script>';
}
