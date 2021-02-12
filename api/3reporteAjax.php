<?php
require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->DecryptionToken($_GET['token']);
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function

require './plugins/PHPMailer/src/PHPMailer.php';
require './plugins/PHPMailer/src/SMTP.php';
require './plugins/PHPMailer/src/Exception.php';

$values_path = explode("/", $_SERVER['REDIRECT_URL']);
$accion = $values_path[sizeof($values_path) - 1];
$alumno = json_decode($RESULTADO_token);

if ($alumno->tipo == 2) {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':

            if ($accion == "certificado") {

                try {
                    $mail = new \PHPMailer\PHPMailer\PHPMailer();
                    $mail->Host = "clpe5.com"; // Sets SMTP server
                    $mail->SMTPDebug = 2; // 2 to enable SMTP debug information
                    $mail->SMTPAuth = true; // enable SMTP authentication
                    $mail->SMTPSecure = "tls"; //Secure conection
                    $mail->Port = 465; // set the SMTP port
                    $mail->Username = "club_lectura@clpe5.com"; // SMTP account username
                    $mail->Password = "epLVP0)^w32C"; // SMTP account password
                    $mail->Priority = 1; // Highest priority - Email priority (1 = High, 3 = Normal, 5 = low)
                    $mail->CharSet = 'UTF-8';
                    $mail->Encoding = '8bit';
                    $mail->Subject = 'SUB';
                    $mail->ContentType = 'text/html; charset=utf-8\r\n';
                    $mail->From = "club_lectura@clpe5.com";
                    $mail->FromName = 'CLUB DE LECTURA PARA EMPRENDEDORES';

                    $mail->AddAddress("alex_34_96@hotmail.com"); // To:
                    $mail->isHTML(true);
                    $mail->Body = "Hi";
                    $mail->AltBody = "Hi";
                    $mail->Send();
                    $mail->SmtpClose();
                } catch (phpmailerException $e) {
                    echo $e->errorMessage();
                } catch (Exception $e) {
                    echo $e->getMessa();}

            } else {
                header("HTTP/1.1 404");
            }
            break;
        case 'POST':
            if ($accion == "certificado") {
                include './plugins/Mail/mime.php';
                include './plugins/Mail/mimePart.php';
                require_once './classes/principal/leccion.php';
                require_once './controladores/leccionesControlador.php';
                $insleccion = new leccionesControlador();
                $insLeccionClass = new Leccion();
                $insLeccionClass->setCuenta($alumno->codigo);
                // Introducimos HTML de prueba
                //  $html = file_get_contents_curl(SERVERURL . "vistas/subprojects/pdf/certificado.html");
                //  $html = html_entity_decode($insleccion->reporte_lecciones_controlador($insLeccionClass));
                // echo ($insleccion->reporte_certificado_controlador($insLeccionClass));
                $subject = "CERTIFICADO CLPE";
                $from = "clpe137@gmail.com";
                $to = "llontopdiazandres@gmail.com";
                $htmlEmail = '<html><body>HTML version of email</body></html>';
                //$html = mb_convert_encoding($insleccion->reporte_certificado_controlador($insLeccionClass), 'UTF-8', 'HTML-ENTITIES');

                // $pdf->set_paper("letter", "landscape");
                // Cargamos el contenido HTML.
                //$pdf->load_html(utf8_decode($html));
                // Renderizamos el documento PDF.
                //$pdf->render();
                // Enviamos el fichero PDF al navegador.
                //$output = $pdf->output();
                $mm = new Mail_mime("\n");
                // $mm->setTxtBody($body);
                $mm->setHTMLBody($htmlEmail);
                //$mm->addAttachment($output, 'application/pdf', 'CERTIFICADO-CLPE.pdf', false);
                $body = $mm->get();
                $headers = $mm->headers(array('From' => $from,
                    'To' => $to,
                    'Subject' => $subject,
                    'Content-Type: multipart/mixed',
                    'MIME-Version: 1.0'));
                $mail = &Mail::factory('mail');
                if ($mail->send($to, $headers, $body)) {
                    echo "enviado exitosamente.";
                } else {
                    echo "no se envió.";
                }
            } else {
                header("HTTP/1.1 404");
            }
            break;
        default:
            header("HTTP/1.1 404");
            break;
    }
} else {
    # code...
}

function file_get_contents_curl($url)
{
    $crl = curl_init();
    $timeout = 5;
    curl_setopt($crl, CURLOPT_URL, $url);
    curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
    $ret = curl_exec($crl);
    curl_close($crl);
    return $ret;
}
