<?php
require_once './api/security/filter.php';
$insFilter = new SecurityFilter();
$RESULTADO_token = $insFilter->DecryptionToken($_GET['token']);
// Cargamos la librería dompdf que hemos instalado en la carpeta dompdf
require_once './plugins/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
// Instanciamos un objeto de la clase DOMPDF.
$pdf = new DOMPDF($options);
// Definimos el tamaño y orientación del papel que queremos.

//$pdf->set_paper("A4", "landscape");
//$pdf->set_paper(array(0,0,104,250));
//HACEMOS UN SPLIT PARA DEJAR EL PATH SIN PARAMETROS
$values_path = explode("/", $_SERVER['REDIRECT_URL']);
$accion = $values_path[sizeof($values_path) - 1];
$alumno = json_decode($RESULTADO_token);
if ($alumno->tipo == 1) {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if ($accion == "certificado") {

                require_once './classes/principal/leccion.php';
                require_once './controladores/leccionesControlador.php';
                $insleccion = new leccionesControlador();
                $insLeccionClass = new Leccion();
                $insLeccionClass->setCuenta($_GET['cuenta']);
                // Introducimos HTML de prueba
                //  $html = file_get_contents_curl(SERVERURL . "vistas/subprojects/pdf/certificado.html");
                //  $html = html_entity_decode($insleccion->reporte_lecciones_controlador($insLeccionClass));
                // echo ($insleccion->reporte_certificado_controlador($insLeccionClass));
                $html = mb_convert_encoding($insleccion->reporte_certificado_controlador($insLeccionClass), 'UTF-8', 'HTML-ENTITIES');
                $pdf->set_paper("letter", "landscape");
                // Cargamos el contenido HTML.
                $pdf->load_html(utf8_decode($html));
                // Renderizamos el documento PDF.
                $pdf->render();
                // Enviamos el fichero PDF al navegador.
                //$pdf->stream('reportePdf.pdf');
                header("Content-type: application/pdf");
                header("Content-Disposition: inline; filename=certificado.pdf");
                echo $pdf->output();
            } else if ($accion == "leccion") {

                require_once './classes/principal/leccion.php';
                require_once './controladores/leccionesControlador.php';
                $insleccion = new leccionesControlador();
                $insLeccionClass = new Leccion();
                $insLeccionClass->setCuenta($_GET['cuenta']);
                $insLeccionClass->setSubTitulo($_GET['subtitulo']);
                $pdf->set_paper("A4", "portrait");
                // Introducimos HTML de prueba
                // $html = file_get_contents_curl(SERVERURL . "vistas/subprojects/pdf/certificado.html");
                $html = html_entity_decode($insleccion->reporte_lecciones_controlador($insLeccionClass));
                // $html = mb_convert_encoding($insleccion->reporte_lecciones_controlador($insLeccionClass), 'UTF-8', 'HTML-ENTITIES');

                // Cargamos el contenido HTML.
                $pdf->load_html($html);
                // Renderizamos el documento PDF.
                $pdf->render();
                // Enviamos el fichero PDF al navegador.
                //$pdf->stream('reportePdf.pdf');
                header("Content-type: application/pdf");
                header("Content-Disposition: inline; filename=comentario.pdf");
                echo $pdf->output();
            } else if ($accion == "respuestas") {
                require_once './classes/principal/respuesta.php';
                require_once './controladores/respuestaControlador.php';
                $insrespuesta = new respuestaControlador();
                $insRespuestaClass = new Respuesta();
                $insRespuestaClass->setCuenta($_GET['cuenta']);
                $insRespuestaClass->setTitulo($_GET['codigo']);
                $insRespuestaClass->setTipo($_GET['tipo']);

                $html = mb_convert_encoding($insrespuesta->reporte_respuestas_controlador($insRespuestaClass), 'UTF-8', 'HTML-ENTITIES');
                $pdf->set_paper("A4", "portrait");
                $pdf->load_html(utf8_decode($html));
                // Renderizamos el documento PDF.
                $pdf->render();
                // Enviamos el fichero PDF al navegador.
                //$pdf->stream('reportePdf.pdf');
                header("Content-type: application/pdf");
                header("Content-Disposition: inline; filename=respuesta.pdf");
                echo $pdf->output();
            }
            break;
        default:
            header("HTTP/1.1 404");
            break;
    }
} elseif ($alumno->tipo == 2) {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':

            if ($accion == "leccion") {
                require_once './classes/principal/leccion.php';
                require_once './controladores/leccionesControlador.php';
                $insleccion = new leccionesControlador();
                $insLeccionClass = new Leccion();
                $insLeccionClass->setCuenta($alumno->codigo);
                $insLeccionClass->setSubTitulo($_GET['subtitulo']);
                // Introducimos HTML de prueba
                //$html = file_get_contents_curl(SERVERURL . "vistas/subprojects/pdf/comentario.html");
                //  $html = html_entity_decode($insleccion->reporte_lecciones_controlador($insLeccionClass));
                $html = mb_convert_encoding($insleccion->reporte_lecciones_controlador($insLeccionClass), 'UTF-8', 'HTML-ENTITIES');
                $pdf->set_paper("A4", "portrait");
                // Cargamos el contenido HTML.
                $pdf->load_html(utf8_decode($html));
                // Renderizamos el documento PDF.
                $pdf->render();
                // Enviamos el fichero PDF al navegador.
                //$pdf->stream('reportePdf.pdf');
                header("Content-type: application/pdf");
                header("Content-Disposition: inline; filename=comentario.pdf");
                echo $pdf->output();
            } else if ($accion == "respuestas") {
                require_once './classes/principal/respuesta.php';
                require_once './controladores/respuestaControlador.php';
                $insrespuesta = new respuestaControlador();
                $insRespuestaClass = new Respuesta();
                $insRespuestaClass->setCuenta($alumno->codigo);
                $insRespuestaClass->setTitulo($_GET['codigo']);
                $insRespuestaClass->setTipo($_GET['tipo']);

                $html = mb_convert_encoding($insrespuesta->reporte_respuestas_controlador($insRespuestaClass), 'UTF-8', 'HTML-ENTITIES');
                $pdf->set_paper("A4", "portrait");
                $pdf->load_html(utf8_decode($html));
                // Renderizamos el documento PDF.
                $pdf->render();
                // Enviamos el fichero PDF al navegador.
                //$pdf->stream('reportePdf.pdf');
                header("Content-type: application/pdf");
                header("Content-Disposition: inline; filename=respuesta.pdf");
                echo $pdf->output();
            } elseif ($accion == "certificado") {

                require_once './classes/principal/leccion.php';
                require_once './controladores/leccionesControlador.php';
                $insleccion = new leccionesControlador();
                $insLeccionClass = new Leccion();
                $insLeccionClass->setCuenta($alumno->codigo);
                // Introducimos HTML de prueba
                //  $html = file_get_contents_curl(SERVERURL . "vistas/subprojects/pdf/certificado.html");
                //  $html = html_entity_decode($insleccion->reporte_lecciones_controlador($insLeccionClass));
                // echo ($insleccion->reporte_certificado_controlador($insLeccionClass));
                $html = mb_convert_encoding($insleccion->reporte_certificado_controlador($insLeccionClass), 'UTF-8', 'HTML-ENTITIES');
                $pdf->set_paper("letter", "landscape");
                // Cargamos el contenido HTML.
                $pdf->load_html(utf8_decode($html));
                // Renderizamos el documento PDF.
                $pdf->render();
                // Enviamos el fichero PDF al navegador.
                //$pdf->stream('reportePdf.pdf');
                header("Content-type: application/pdf");
                header("Content-Disposition: inline; filename=certificado.pdf");
                echo $pdf->output();
            } else {
                header("HTTP/1.1 404");
            }
            break;
        default:
            header("HTTP/1.1 404");
            break;
    }
} else {
    header("HTTP/1.1 404");
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
