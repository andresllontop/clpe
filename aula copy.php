<!--
    Document   : app
    Created on : 4 junio. 2020, 10:38:18
    Author     : Andres Llontop diaz
-->

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Meta tags -->
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="APLICACIÓN <?php echo COMPANY; ?>">
    <meta name="author" content="<?php echo COMPANY; ?>">
    <meta name="copyright" content="<?php echo COMPANY; ?>">
    <meta name="keywords" content="
    CLPE,
              CLUB DE LECTURA,
               aplicacion club de lectura
              ">
    <!-- /meta tags -->
    <title><?php echo COMPANY; ?></title>
  <link rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/sweet-alert.css">
  <link rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/material-design-iconic-font.min.css">
  <link rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/normalize.css">
  <link rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/jquery.mCustomScrollbar.css">
  <link rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/style1.css">
  <link type="text/css" rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/home.css" />
  <link type="text/css" rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/recorder.css" />

  <link type="text/css" rel="stylesheet" href="https://releases.flowplayer.org/7.2.7/skin/skin.css" />
  <!-- <link  type="text/css"rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/skin.css" /> -->
  <style>
    /* mixed playlist player */
    #mixed {
      width: 100%;
      height: 100%;
      background-color: #7030a0;
    }

    /* make cover image fill container width */
    #mixed.is-audio .fp-player {
      background-size: cover;
      /* default: contain */
      background-position: top center;
      /* default: center */
    }

    /* icecast player */
    .flowplayer.is-audio-only {
      max-width: 400px;
      background-color: #eee;
    }

    /* keep this controlbar-only player always at same height */
    .flowplayer.is-audio-only.is-small,
    .flowplayer.is-audio-only.is-tiny {
      font-size: 16px;
    }
  </style>
  <?php

$beanResource = $routes->getResourceForContainerApp();
//INCLUIMOS LOS STYLES
$array_styles = $beanResource->path_styles;
if ($array_styles != "") {
    foreach ($array_styles as $path_style) {
        echo '
                <link
                rel="stylesheet"
                href="' . SERVERURL . $path_style . '"
              />

            ';
    }}
?>
</head>

<body>


  <!-- Root -->
  <div class="dt-root">
    <div class="dt-root__inner">

      <div class="container-wrapper navigation">
        <nav class="navbar-user-top full-reset">
          <ul class="list-unstyled full-reset">
            <figure>
              <img class="foto-usuario" src="<?php echo (SERVERURL) ?>adjuntos/clientes/userclpe.png" alt="user-picture"
                class="img-responsive img-circle center-box" />
            </figure>
            <li style="color:#fff;  " class="tooltips-general " data-placement="bottom" title="Administrar Perfil">
              <a href="<?php echo (SERVERURL) ?>perfil" style="color:#fff;"> <span
                  class="all-tittles name-usuario">usuario</span> </a>

            </li>
            <li class="tooltips-general exit-system-button" data-href="<?php echo SERVERURL; ?>index"
              data-placement="bottom" title="Salir del sistema">
              <i class="zmdi zmdi-power"></i>
            </li>

            <li class="tooltips-general btn-help" data-placement="bottom" title="Ayuda">
              <i class="zmdi zmdi-help-outline zmdi-hc-fw"></i>
            </li>

            <figure style="float: left !important;">
              <img src="<?php echo SERVERURL; ?>adjuntos/logoHeader.jpg" alt="user-picture"
                class="img-responsive img-circle center-box" />
            </figure>
            <li class="mobile-menu-button-navbar visible-xs2">
              <i class="zmdi zmdi-menu"></i>
            </li>
            <div class="navbar-mobile">
              <li class="tooltips-general " style="float: left !important;">
                <a href="<?php echo SERVERURL; ?>catalog"><i class="zmdi zmdi-home zmdi-hc-fw"></i>INICIO</a>
              </li>
              <li class="tooltips-general " style="float: left !important;">
                <a href="<?php echo SERVERURL; ?>recursos"><i class="zmdi zmdi-book zmdi-hc-fw"></i>RECURSOS</a>
              </li>
              <li class="tooltips-general " style="float: left !important;">
                <a href="<?php echo SERVERURL; ?>lecciones"><i class="zmdi zmdi-videocam zmdi-hc-fw"></i>LECCIONES
                  REALIZADAS</a>
              </li>
              <li class="tooltips-general " style="float: left !important;">
                <a href="<?php echo SERVERURL; ?>contactar"><i class="zmdi zmdi-email zmdi-hc-fw"></i>CONSULTAS</a>
              </li>
              <li class="tooltips-general " style="float: left !important;">
                <a href="https://web.whatsapp.com/send?phone=51924421734" class="telefono-usuario" target="blank">
                  <i class="zmdi zmdi-whatsapp zmdi-hc-fw"></i>
                  &NonBreakingSpace; +51 924421734
                </a>
              </li>
            </div>
          </ul>

        </nav>
      </div>
      <!-- Site Main -->
      <main class="dt-main">



        <!-- Site Content Wrapper -->
        <div class="dt-content-wrapper">

          <!-- Site Content -->
          <div class="dt-content px-3">

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
  <script src="<?php echo SERVERURL; ?>vistas/js/flowplayer.min.js"></script>

  <script src="<?php echo SERVERURL; ?>vistas/js/sweet-alert.min.js"></script>
  <script>window.jQuery || document.write('<script src="<?php echo SERVERURL; ?>vistas/js/jquery-1.11.2.min.js"><\/script>')</script>
  <script src="<?php echo SERVERURL; ?>vistas/js/modernizr.js"></script>
  <script src="<?php echo SERVERURL; ?>vistas/js/bootstrap.min.js"></script>
  <script src="<?php echo SERVERURL; ?>vistas/js/jquery.mCustomScrollbar.concat.min.js"></script>

  <script language="Javascript">
// document.oncontextmenu = function(){return false}
  </script>
  <script type="text/javascript"
    src="<?php echo (SERVERURL); ?>plugins/jquery-pagination/jquery.Pagination.min.js"></script>

  <script type="text/javascript" src="<?php echo (SERVERURL); ?>vistas/scripts/util/functions.js"></script>
  <script type="text/javascript" src="<?php echo (SERVERURL); ?>vistas/scripts/util/functions_alerts.js"></script>
  <script language="javascript" src="<?php echo (SERVERURL); ?>vistas/scripts/util/configuration_api.js"></script>
  <script language="javascript" src="<?php echo (SERVERURL); ?>vistas/scripts/util/functions_operational.js"></script>
  <script language="javascript" src="<?php echo (SERVERURL); ?>vistas/scripts/init_parameters.js"></script>


  <!--Scripts SESION-->

  <script type="text/javascript" src="<?php echo (SERVERURL); ?>vistas/scripts/session/js.cookie.js?v=0.11"></script>
  <script type="text/javascript"
    src="<?php echo (SERVERURL); ?>vistas/scripts/session/session.validate.js?v=0.11"></script>
  <script type="text/javascript"
    src="<?php echo (SERVERURL); ?>vistas/scripts/session/session.validate.init.js?v=0.11"></script>

  <!--Scripts -->

  <?php
//INCLUIMOS LOS SCRIPTS
$array_scripts = $beanResource->path_scripts;if ($array_scripts !=
    "") {foreach ($array_scripts as $path_script) {echo '
    <script type="text/javascript" src="' . SERVERURL . $path_script . '"></script>
    ';}}
?>
  <!--Scripts MOLINO-->

</body>

</html>