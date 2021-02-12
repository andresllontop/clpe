<?php
$peticionAJAX = false;
require './controladores/libroControlador.php';
require './controladores/cuentalibroControlador.php';
require './controladores/subcapituloControlador.php';
require './controladores/leccionesControlador.php';
require './controladores/cuestionarioclienteControlador.php';
require './controladores/restriccionControlador.php';
$Icuestionariocliente = new cuestionarioclienteControlador();
$Ilibro = new libroControlador();
$Ilibrocuenta = new cuentalibroControlador();
$Ilecciones = new leccionesControlador();
$Isubcapitulo = new subcapituloControlador();
$Irestriccion = new restriccionControlador();
$resultCuenta = $Ilibrocuenta->datos_cuentalibro_controlador("unico", $_SESSION["cuentaCodigo"]);
$resultcuestionariocliente = $Icuestionariocliente->datos_cuestionariocliente_controlador("ultimo", $_SESSION["cuentaCodigo"]);
$resultleccion = $Ilecciones->datos_lecciones_controlador("ultimo", $_SESSION["cuentaCodigo"]);
$vidio = 0;
if (!(is_null($resultleccion[0]['codigo_subtitulo']))) {
    $resultrestriccion = $Irestriccion->datos_restriccion_controlador("conteo", 0);
    if (count($resultrestriccion) > 0) {
        foreach ($resultrestriccion as $filare) {
            if ($filare['disponible'] == 1) {
                $resultcapitulo = $Isubcapitulo->datos_subcapitulo_controlador("siguiente", $resultleccion[0]['codigo_subtitulo']);
                if (count($resultcapitulo) > 0) {
                    if ($filare['codigo_subtitulo'] == $resultcapitulo[0]['codigo_subtitulo']) {
                        echo ($filare['video']);
                        $vidio = 1;
                        $vidioArchivo = $filare['video'];
                    }
                }
            }
        }
    }
}
?>

        <?php
if (count($resultCuenta) == 0) {?>
        <div class="container padding-top" style="padding-top:3%;">
          <div class="page-header">
            <h1 class="all-tittles text-center">
              <small>No tienes curso matriculado</small>
            </h1>
          </div>
        </div>
          <?php } else {
    foreach ($resultCuenta as $filacuenta) {
        $result = $Ilibro->datos_libro_controlador("unico", $filacuenta['libro_codigoLibro']);
    }?>
          <!--contenido de la pagina  -->
        <div id="vistaCatalogo" class="container-fluid" >
          <div class="row">
            <?php $contador = 0;
    foreach ($result as $fila) {
        $contador++;
        ?>
            <div class="media media-hover media-libro col-xs-4 col-xs-offset-4 col-sm-4 col-sm-offset-0 col-md-3 col-md-offset-0 col-lg-3 col-lg-offset-0" style="margin-top: 6%;" >
            <a href="<?php
echo SERVERURL . "videoSubtitulo/" . $resultcuestionariocliente[0]['id_titulo'];
        ?>">
              <div class="pull-center text-center">
                <img class="media-object"
                src="<?php echo SERVERURL . "adjuntos/libros/" . $fila['imagen']; ?>"
                alt="Libro"/>
              </div>
              

              <div class="media-body text-center">
                <h4 class="media-heading" >
                  <?php echo ($fila['nombre']); ?>
                </h4>
               
                <p class=" pull-center">
                    <button type="submit" class="btn btn-primary  btnAbrirLibro" >
                      <i class="zmdi zmdi-info-outline" ></i> &nbsp;&nbsp;
                      Acceder al Libro</button>
                </p>
              </div></a>
            </div>
            <?php if ($vidio == 1) {?>
              <div class="col-xs-12 col-xs-offset-0 col-sm-8 col-sm-offset-0 col-md-8 col-md-offset-0 col-lg-9 col-lg-offset-0">
                <h2 class="text-dp all-tittles text-center">
                  COMO DESARROLLAR LA DECLARACION
                </h2>
                <video controls width="100%" height="390">
                 <source type="video/mp4" src="<?php echo (SERVERURL . "adjuntos/videos/" . $vidioArchivo); ?>">
                </video>
              </div>
              <?php } else {?>
                <div class="col-xs-12 col-xs-offset-0 col-sm-8 col-sm-offset-0 col-md-8 col-md-offset-0 col-lg-9 col-lg-offset-0">
                  <h2 class="text-dp all-tittles text-center">
                    COMO DESARROLLAR EL CURSO
                  </h2>
                  <video id="video-libro" controls>
                   <source type="video/mp4" src="<?php echo (SERVERURL . "adjuntos/libros/" . $fila['libroVideo']); ?>">
                  </video>
                </div>
              <?php }
    }
    ?>
          </div>
        </div>
        <?php }?>
        <!--final del contenido de la pagina  -->

  