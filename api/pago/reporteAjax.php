<?php

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

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':

        if ($accion == "compra") {

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
            header("Content-Disposition: inline; filename=voucher-compra.pdf");
            echo $pdf->output();
        } else {
            header("HTTP/1.1 404");
        }
        break;
    default:
        header("HTTP/1.1 404");
        break;
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
