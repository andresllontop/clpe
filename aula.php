<!--
    Document   : app
    Created on : 4 junio. 2020, 10:38:18
    Author     : Andres Llontop diaz
-->

<!DOCTYPE html>
<html lang="es">

<head>
  <!-- Meta tags -->
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="APLICACIÓN <?php echo COMPANY; ?>">
  <meta name="author" content="<?php echo COMPANY; ?>">
  <meta name="copyright" content="<?php echo COMPANY; ?>">
  <meta name="keywords" content="CLPE,CLUB DE LECTURA,aplicacion club de lectura">
   <!-- /meta tags -->
    <title><?php echo COMPANY; ?></title>
  <link rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/sweet-alert.css">
  <link rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/material-design-iconic-font.min.css">
<!-- Fontawesome CSS styles -->
<link href="<?php echo SERVERURL; ?>vistas/publico/Dale/css/font-awesome.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/normalize.css">
  <link type="text/css" rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/publico/Dale/css/style.css?v=1.38" />
  <link rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/bootstrap.min.css?v=1.38">
  <link rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/jquery.mCustomScrollbar.css">
  <link rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/style1.css?v=1.38">
  <link type="text/css" rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/recorder.css?v=1.38" />



  <?php

$beanResource = $routes->getResourceForContainerApp();
//INCLUIMOS LOS STYLES
$array_styles = $beanResource->path_styles;
if ($array_styles != "") {
    foreach ($array_styles as $path_style) {
        echo '
                <link
                rel="stylesheet"
                href="' . $path_style . '"
              />

            ';
    }}
?>
</head>

<body>
<div class="dt-loader-container">
    <div class="dt-loader">
      <div class="loader">
        <div class="loader">
          <div class="loader">
            <div class="loader">

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Root -->
  <div class="dt-root">
    <div class="dt-root__inner">

      <div class="container-wrapper navigation">
        <nav class="navbar-user-top full-reset">
          <ul class="list-unstyled full-reset" >
                <figure class="rounded-circle" style="display: contents;">
                <img src="" alt="user-picture" class="rounded-circle center-box dt-avatar" />
              </figure>
                <li style="color:#fff;" class="tooltips-general aula-perfil" data-placement="bottom" title="Administrar Perfil">
                  <a href="javascript:void(0);" style="color:#fff;"> <span
                      class="all-tittles name-user-session text-truncate">Alumno</span> </a>

                </li>
                <li class="tooltips-general a-close-session" data-placement="bottom"
                  title="Salir del sistema">
                  <i class="ml-1 zmdi zmdi-power"></i>
                </li>

                <li class="tooltips-general a-help d-none" data-placement="bottom" title="Ayuda">
                  <i class="zmdi zmdi-help-outline zmdi-hc-fw"></i>
                </li>

                <figure style="float: left !important;">
                  <img src="<?php echo SERVERURL; ?>adjuntos/logoHeader.jpg" alt="user-picture"
                    class="img-responsive rounded-circle" />
                </figure>
                <li class="mobile-menu-button-navbar visible-xs2">
                  <i class="zmdi zmdi-menu"></i>
                </li>
                <div class="navbar-mobile" id="menus_aula" style="    overflow-y: auto;">
                </div>
          </ul>

        </nav>
      </div>
      <!-- Site Main -->
      <main class="dt-main">



        <!-- Site Content Wrapper -->
        <div class="dt-content-wrapper">

          <!-- Site Content -->
          <div class="dt-content">

            <?php
//INCLUIMOS LOS HTML
$array_resource = $beanResource->path_resource;
if ($array_resource != "") {
    foreach ($array_resource as $path_resources) {
        include $path_resources;
    }}

?>
          </div>
          <!-- /site content -->
        </div>
        <!-- /site content wrapper -->
      </main>
      <!-- /main -->
    </div>
  </div>
  <!-- /root -->

  <!-- Flowplayer library -->
  <!--script src="<?php echo SERVERURL; ?>vistas/js/flowplayer.min.js?v=1.38"></script -->
  <script src="<?php echo SERVERURL; ?>vistas/js/jquery-1.11.2.min.js?v=1.38"></script>
  <script src="<?php echo SERVERURL; ?>vistas/js/sweet-alert.min.js?v=1.38"></script>

  <script src="<?php echo SERVERURL; ?>vistas/js/modernizr.js?v=1.38"></script>
  <script src="<?php echo SERVERURL; ?>vistas/js/bootstrap.min.js?v=1.38"></script>
  <!-- Easing core JavaScript -->
  <script language="javascript" src="<?php echo (SERVERURL); ?>vistas/publico/Dale/js/jquery.easing.1.3.js?v=0.23"></script>
  <script src="<?php echo SERVERURL; ?>vistas/js/jquery.mCustomScrollbar.concat.min.js?v=1.38"></script>

  <script language="Javascript">
// document.oncontextmenu = function(){return false}
  </script>
  <script type="text/javascript"
    src="<?php echo (SERVERURL); ?>plugins/jquery-pagination/jquery.Pagination.min.js?v=1.38"></script>

  <script type="text/javascript" src="<?php echo (SERVERURL); ?>vistas/scripts/util/functions.js?v=1.38"></script>
  <script type="text/javascript" src="<?php echo (SERVERURL); ?>vistas/scripts/util/functions_alerts.js?v=1.38"></script>
  <script language="javascript" src="<?php echo (SERVERURL); ?>vistas/scripts/util/configuration_api.js?v=1.38"></script>
  <script language="javascript" src="<?php echo (SERVERURL); ?>vistas/scripts/util/functions_operational.js?v=1.38"></script>
  <script language="javascript" src="<?php echo (SERVERURL); ?>vistas/scripts/init_parameters.js?v=1.38"></script>

  <!--Scripts SESION-->

  <script type="text/javascript" src="<?php echo (SERVERURL); ?>vistas/scripts/session/js.cookie.js?v=1.38"></script>
  <script type="text/javascript"
    src="<?php echo (SERVERURL); ?>vistas/scripts/session/session.validate.js?v=1.38"></script>
  <script type="text/javascript"
    src="<?php echo (SERVERURL); ?>vistas/scripts/session/session.validate.init.js?v=1.38"></script>
    <script language="javascript" src="<?php echo (SERVERURL); ?>vistas/publico/Dale/js/main.js?v=1.38"></script>
  <!--Scripts -->

  <?php
//INCLUIMOS LOS SCRIPTS
$array_scripts = $beanResource->path_scripts;if ($array_scripts !=
    "") {foreach ($array_scripts as $path_script) {echo '
    <script type="text/javascript" src="' . $path_script . '"></script>
    ';}}
?>
  <!--Scripts MOLINO-->

</body>

</html>