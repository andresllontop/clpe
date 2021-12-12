<!--
    Document   : app
    Created on : 4 junio. 2020, 10:38:18
    Author     : Andres Llontop diaz
    Número de Contacto :+51 985-726-371
    email: llontopdiazandres@gmail.com
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
    <meta name="keywords" content="CLPE5,CLUB DE LECTURA PARA EMPRENDEDORES,aplicacion club de lectura
              ">

    <!-- /meta tags -->
    <title><?php echo COMPANY; ?></title>
    <link rel="Shortcut Icon" type="image/x-icon" href="<?php echo SERVERURL; ?>adjuntos/logos.png" />

    <link type="text/css" rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Google+Sans">
    <!-- /load styles -->
    <link rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/material-design-iconic-font.min.css">

    <link rel="stylesheet" href="<?php echo SERVERURL; ?>vistas/css/sweet-alert.css">
    <!-- Lightbox CSS Styles -->


    <link href="<?php echo SERVERURL; ?>vistas/publico/Dale/css/styleSocial.css" rel="stylesheet" />

     <!-- Bootstrap core CSS -->
     <link href="<?php echo SERVERURL; ?>vistas/publico/Dale/css/bootstrap.min.css?v=1.38" rel="stylesheet" />

    <!-- Fontawesome CSS styles -->
    <link href="<?php echo SERVERURL; ?>vistas/publico/Dale/css/font-awesome.min.css" rel="stylesheet" />
    <!-- Aniamte CSS styles -->
    <link href="<?php echo SERVERURL; ?>vistas/publico/Dale/css/animate.css" rel="stylesheet" />
    <!-- Nivo Slider CSS Styles -->
    <link href="<?php echo SERVERURL; ?>vistas/publico/Dale/css/nivo-slider.css" rel="stylesheet" />
    <!-- Owl Carousel CSS Styles -->
    <link href="<?php echo SERVERURL; ?>vistas/publico/Dale/css/owl.carousel.css" rel="stylesheet" />
    <link href="<?php echo SERVERURL; ?>vistas/publico/Dale/css/owl.transitions.css" rel="stylesheet" />
    <!-- Lightbox CSS Styles -->
    <link href="<?php echo SERVERURL; ?>vistas/publico/Dale/css/lightbox.css" rel="stylesheet" />
     <!-- Masterslider CSS styles -->
     <link href="<?php echo SERVERURL; ?>vistas/publico/Dale/css/masterslider/masterslider.css?v=1.38" rel="stylesheet" />
    <link href="<?php echo SERVERURL; ?>vistas/publico/Dale/css/masterslider/skins/black-1/style.css?v=1.38" rel="stylesheet" />
    <!-- Primary CSS styles -->
    <link href="<?php echo SERVERURL; ?>vistas/publico/Dale/css/style.css?v=1.38" rel="stylesheet" />
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

<div class="dt-loader-container" >
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
                <nav class="navbar navbar-default" role="navigation">
                    <div class="container px-0">
                        <div class="navbar-header m-0">

                                <a class="navbar-toggle border-hover" href="<?php echo (SERVERURL . 'index'); ?>" style="float: left;">
                                        <img src="<?php echo SERVERURL; ?>adjuntos/logoHeader.jpg"
                                            style="margin-top:-14px ; width:50px;height:50px;" />
                                </a>

                            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="">
                                <i class="fa fa-bars"></i>
                            </button>

                            <ul class="mini"></ul><!-- mobile navigation -->
                        </div><!-- .navbar-header -->
                        <div class="collapse navbar-collapse">
                            <div class="left">
                                <ul class="navbar-nav anime-height border-hover" style="width: 60px; height: 60px;margin: 0px;padding: 1px;">
                                   <a class="navbar-brand" target="_top" href="<?php echo (SERVERURL . 'index'); ?>"
                                   style="margin: 0px; height: 100%;padding: 9px;">
                                        <img src="<?php echo SERVERURL; ?>adjuntos/logoHeader.jpg"
                                            style="width:100%;height:100%;" />
                                    </a>
                                </ul>
                            </div>
                            <div class="left">
                                <ul class="nav navbar-nav">
                                    <li><a href="<?php echo (SERVERURL . 'matricula'); ?>"  target="_top">Curso</a></li>
                                    <li><a target="_top" href="<?php echo (SERVERURL . 'nosotros'); ?>">Nosotros</a></li>
                                    <li><a target="_top" href="<?php echo (SERVERURL . 'testimonios'); ?>">Testimonios</a></li>
                                    <li><a target="_top" href="<?php echo (SERVERURL . 'blog'); ?>">Blog</a></li>
                                </ul>
                            </div>
                            <div class="right">
                                <ul class="nav anime-height" >
                                        <button class="btn btn-bordered white px-sm-2 py-2 my-1" type="button"  id="btn-Register"   style="border-radius: 17px; font-size: 15px;color: #404040;background: #fff;">Registro</button>
                                    <button class="btn btn-bordered white px-sm-2 py-2 my-1 mx-0" type="button"  id="btn-logear"   style="border-radius: 17px; font-size: 15px;"> Iniciar
                                            Sesión</button>
                                </ul>
                            </div>
                        </div><!-- /.navbar-collapse -->
                    </div><!-- /.container-fluid -->
                </nav>
            </div>
                        <?php
//INCLUIMOS LOS HTML
$array_resource = $beanResource->path_resource;
if ($array_resource != "") {
    foreach ($array_resource as $path_resources) {
        include $path_resources;
    }}

?>

        </div>
    </div>


    <!-- /root -->
    <?php include "./vistas/subprojects/publico/footer/footer.html";?>



    <!-- Load Facebook SDK for JavaScript -->
    <div id="fb-root"></div>
      <script>
        window.fbAsyncInit = function() {
          FB.init({
            autoLogAppEvents : true,
            status : true, // check login status
            cookie : true, // enable cookies to allow the server to access the session
            xfbml  : true,  // parse XFBML
            version          : 'v12.0'
          });
        };

        (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); 
        js.id = id;
        js.src = 'https://connect.facebook.net/es_LA/sdk/xfbml.customerchat.js';
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));</script>

      <!-- Your Chat Plugin code -->
      <div class="fb-customerchat" attribution=setup_tool page_id="1312732348808673" greeting_dialog_display="hide"  theme_color="#7646ff" logged_in_greeting="Hola soy el administrador del CLPE ¿En que puedo ayudarte?" logged_out_greeting="Hola soy el administrador del CLPE ¿En que puedo ayudarte?"></div>


    <script language="javascript" src="<?php echo (SERVERURL); ?>vistas/publico/Dale/js/jquery-1.11.0.min.js?v=1.38"></script>


    <!-- Waypoints core JavaScript -->
    <script language="javascript" src="<?php echo (SERVERURL); ?>vistas/publico/Dale/js/waypoints.min.js?v=1.38"></script>
    <!-- Underscore core JavaScript -->
    <script language="javascript" src="<?php echo (SERVERURL); ?>vistas/publico/Dale/js/underscore-min.js?v=1.38"></script>

    <!-- jQuery color core JavaScript -->
    <script language="javascript" src="<?php echo (SERVERURL); ?>vistas/publico/Dale/js/jquery.animation.js?v=1.38"></script>

    <!-- Stellar Matricula core JavaScript -->
    <script language="javascript" src="<?php echo (SERVERURL); ?>vistas/publico/Dale/js/jquery.stellar.min.js?v=1.38"></script>

    <!-- Nivo Matricula Slider JavaScript -->
    <script language="javascript" src="<?php echo (SERVERURL); ?>vistas/publico/Dale/js/jquery.nivo.slider.pack.js?v=1.38"></script>
    <!-- Video core JavaScript -->
    <script language="javascript" src="<?php echo (SERVERURL); ?>vistas/publico/Dale/js/jquery.easing.1.3.js?v=1.38"></script>
    <!-- Video core JavaScript -->


    <!-- REDES SOCIALES core JavaScript -->
    <script language="javascript" src="<?php echo (SERVERURL); ?>vistas/publico/Dale/js/functions.js?v=1.38"></script>
    <!-- Everything else -->
    <script language="javascript" src="<?php echo (SERVERURL); ?>vistas/publico/Dale/js/custom.js?v=1.38"></script>
    <script language="javascript" src="<?php echo (SERVERURL); ?>vistas/publico/Dale/js/main.js?v=1.38"></script>
    <!-- scrip API -->
    <!-- Bootstrap core JavaScript -->
    <script language="javascript" src="<?php echo SERVERURL; ?>vistas/publico/js/bootstrap.min.js?v=1.38"></script>
    <script src="<?php echo SERVERURL; ?>vistas/js/sweet-alert.min.js?v=1.38"></script>

    <script type="text/javascript"  src="<?php echo (SERVERURL); ?>plugins/sticky/stickybits.min.js?v=1.38"></script>

    <!-- scrip EDIT -->
    <script type="text/javascript" src="<?php echo (SERVERURL); ?>vistas/scripts/util/functions.js?v=1.38"></script>
    <script language="javascript" src="<?php echo (SERVERURL); ?>vistas/scripts/util/configuration_api.js?v=1.38"></script>
    <script language="javascript" src="<?php echo (SERVERURL); ?>vistas/scripts/util/functions_operational.js?v=1.38"></script>
    <script type="text/javascript"
    src="<?php echo (SERVERURL); ?>vistas/scripts/util/functions_alerts.js?v=1.38"></script>
    <script language="javascript" src="<?php echo (SERVERURL); ?>vistas/scripts/init_parameters.js?v=1.38"></script>
    <!--Scripts -->
    <script type="text/javascript" src="<?php echo (SERVERURL); ?>vistas/scripts/session/js.cookie.js?v=1.38"></script>
    <script type="text/javascript"
        src="<?php echo (SERVERURL); ?>vistas/scripts/session/session.validate.login.js?v=1.38"></script>

    <!--Scripts CLPE-->
    <?php
//INCLUIMOS LOS SCRIPTS
$array_scripts = $beanResource->path_scripts;
if ($array_scripts !=
    "") {foreach ($array_scripts as $path_script) {echo '
    <script type="text/javascript" src="' . $path_script . '"></script>
    ';}}
?>
    <!--Scripts CLPE-->

</body>

</html>