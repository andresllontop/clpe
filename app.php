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
  <meta name="keywords" content="
    CLPE,
              CLUB DE LECTURA,
               aplicacion club de lectura
              ">
  <!-- /meta tags -->
  <title><?php echo COMPANY; ?></title>
  <link rel="Shortcut Icon" type="image/x-icon" href="<?php echo SERVERURL; ?>adjuntos/logos.png" />
  <link rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/material-design-iconic-font.min.css">
  <link rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/normalize.css?v=0.49">
  <link rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/bootstrap.min.css?v=0.49">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" />

  <link type="text/css" rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/recorder.css?v=0.49" />
  <link type="text/css" rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/editor.css?v=0.49" />
  <link rel="stylesheet" href="https://releases.flowplayer.org/7.2.7/skin/skin.css" />
  <link rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/jquery.mCustomScrollbar.css">
  <link rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/sweet-alert.css?v=0.49">
  <!-- /load styles -->
  <link rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/style.css?v=0.49">
  <!-- /load styles -->
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
  <div class="dt-root">
    <div class="navbar-lateral full-reset" style="position:fixed;">
      <div class="visible-xs font-movile-menu mobile-menu-button"></div>
      <div class="full-reset container-menu-movile custom-scroll-containers
    mCustomScrollbar _mCS_1 mCS-autoHide">
        <div id="mCSB_1" class="mCustomScrollBox mCS-dark-thin mCSB_vertical mCSB_inside" tabindex="0"
          style="max-height: none;">
          <div id="mCSB_1_container" class="mCSB_container"
            style="position: relative; top: 0px; left: 0px; height:100%;" dir="ltr">
            <div class="logo full-reset all-tittles text-center">
              <i class="visible-xs zmdi zmdi-close pull-left mobile-menu-button"
                style="line-height: 55px; cursor: pointer; padding: 0 10px; margin-left: 7px;"></i>
              Sistema de Libro
            </div>
            <div class="full-reset" style="background-color:#2B3D51; color:#fff;">
              <a href="<?php echo SERVERURL; ?>app/index">
              <figure class="px-6 pt-2 p-sm-10"><img src="<?php echo SERVERURL; ?>adjuntos/logoHeader.jpg" alt="Biblioteca"
                  class="img-responsive rounded-circle w-100"  /> </figure></a>
              <p class="text-center">
                <?php echo (COMPANY); ?>
              </p>
            </div>
            <div class="full-reset nav-lateral-list-menu">
              <ul class="list-unstyled dt-side-nav" id="menus_clpe">

              </ul>
            </div>
          </div>
        </div>
      </div>
      <!-- /srcroll -->
      <div id="mCSB_1_scrollbar_vertical"
        class="mCSB_scrollTools mCSB_1_scrollbar mCS-dark-thin mCSB_scrollTools_vertical" style="display: block;">
        <a href="#" class="mCSB_buttonUp" oncontextmenu="return false;" style="display: block;">
        </a>
        <div class="mCSB_draggerContainer"></div>
        <a href="#" class="mCSB_buttonDown" oncontextmenu="return false;" style="display: block;"></a>
      </div>

      <!-- /scrooll -->
    </div>
    <div
      class="content-page-container pt-10">
          <!--NAVBAR de la pagina  -->
          <nav class="navbar-user-top full-reset" style="position:fixed;background-color: #2d3945;">
            <ul class="list-unstyled full-reset">
              <figure class="rounded-circle" style="display: contents;">
                <img src="" alt="user-picture" class="rounded-circle center-box dt-avatar" />
              </figure>
              <li style="color:#fff;" class="tooltips-general a-perfil" data-placement="bottom" title="Administrar Perfil">
                <a href="javascript:void(0);" style="color:#fff;">
                  <span class="all-tittles name-user-session"></span> </a>

              </li>
              <li class="tooltips-general a-close-session" data-href="<?php echo SERVERURL; ?>index"
                data-placement="bottom" title="Salir del sistema">
                <i class="zmdi zmdi-power"></i>
              </li>

              <li class="tooltips-general a-help" data-placement="bottom" title="Ayuda">
                <i class="zmdi zmdi-help-outline zmdi-hc-fw"></i>
              </li>

              <li class="mobile-menu-button visible-xs" style="float: left !important;">
                <i class="zmdi zmdi-menu"></i>
              </li>

            </ul>

          </nav>
          <!--NAVBAR de la FIN  -->
          <!--contenido de la pagina  -->

          <?php
$array_resource = $beanResource->path_resource;
if ($array_resource != "") {
    foreach ($array_resource as $path_resources) {
        include $path_resources;
    }}

?>
          <!--final del contenido de la pagina  -->


    </div>

  </div>





  <script>window.jQuery || document.write('<script src="<?php echo SERVERURL; ?>vistas/js/jquery-1.11.2.min.js?v=0.49"><\/script>')</script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
  <script src="<?php echo SERVERURL; ?>vistas/js/bootstrap.min.js?v=0.49"></script>
  <script src="<?php echo SERVERURL; ?>vistas/js/sweet-alert.min.js?v=0.49"></script>
  <script src="<?php echo SERVERURL; ?>vistas/js/modernizr.js?v=0.49"></script>


  <script src="<?php echo SERVERURL; ?>vistas/js/jquery.mCustomScrollbar.concat.min.js?v=0.49"></script>
  <script type="text/javascript"
    src="<?php echo (SERVERURL); ?>plugins/jquery-pagination/jquery.Pagination.min.js?v=0.49"></script>

  <!--Scripts -->
  <!--script src="<%out.print(request.getContextPath());%>vistas/scripts/session/change.cookie.js"></script-->
  <script type="text/javascript" src="<?php echo (SERVERURL); ?>vistas/scripts/util/functions.js?v=0.49"></script>
  <script type="text/javascript"
    src="<?php echo (SERVERURL); ?>vistas/scripts/util/functions_alerts.js?v=0.49"></script>
  <script language="javascript"
    src="<?php echo (SERVERURL); ?>vistas/scripts/util/configuration_api.js?v=0.49"></script>
  <script language="javascript"
    src="<?php echo (SERVERURL); ?>vistas/scripts/util/functions_operational.js?v=0.49"></script>
  <script language="javascript" src="<?php echo (SERVERURL); ?>vistas/scripts/init_parameters.js?v=0.49"></script>
  <!--Scripts -->
  <script type="text/javascript" src="<?php echo (SERVERURL); ?>vistas/scripts/session/js.cookie.js?v=0.49"></script>
  <script type="text/javascript"
    src="<?php echo (SERVERURL); ?>vistas/scripts/session/session.validate.js?v=0.49"></script>
  <script type="text/javascript"
    src="<?php echo (SERVERURL); ?>vistas/scripts/session/session.validate.init.js?v=0.49"></script>

  <!--Scripts -->
  <?php
//INCLUIMOS LOS SCRIPTS
$array_scripts = $beanResource->path_scripts;if ($array_scripts !=
    "") {foreach ($array_scripts as $path_script) {echo '
    <script type="text/javascript" src="' . SERVERURL . $path_script . '"></script>
    ';}}
?>



</body>

</html>