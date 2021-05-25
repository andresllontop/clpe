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
    <link href="<?php echo SERVERURL; ?>vistas/publico/Dale/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Fontawesome CSS styles -->
    <link href="<?php echo SERVERURL; ?>vistas/publico/Dale/css/font-awesome.min.css" rel="stylesheet" />
    <!-- Aniamte CSS styles -->
    <link href="<?php echo SERVERURL; ?>vistas/publico/Dale/css/animate.css" rel="stylesheet" />
    <!-- Nivo Slider CSS Styles -->
    <link href="<?php echo SERVERURL; ?>vistas/publico/Dale/css/nivo-slider.css" rel="stylesheet" />
    <link href="<?php echo SERVERURL; ?>vistas/publico/Dale/css/owl.transitions.css" rel="stylesheet" />
    <!-- Lightbox CSS Styles -->
    <link href="<?php echo SERVERURL; ?>vistas/publico/Dale/css/lightbox.css" rel="stylesheet" />
    <!-- Masterslider CSS styles -->
    <link href="<?php echo SERVERURL; ?>vistas/publico/Dale/css/masterslider/masterslider.css?v=0.23"
        rel="stylesheet" />
    <link href="<?php echo SERVERURL; ?>vistas/publico/Dale/css/masterslider/skins/black-1/style.css?v=0.23"
        rel="stylesheet" />
    <!-- Primary CSS styles -->
    <link href="<?php echo SERVERURL; ?>vistas/publico/Dale/css/style.css?v=0.23" rel="stylesheet" />
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

    <script language="javascript"
        src="<?php echo (SERVERURL); ?>vistas/publico/Dale/js/jquery-1.11.0.min.js?v=0.23"></script>
    <!-- jQuery color core JavaScript -->
    <script language="javascript"
        src="<?php echo (SERVERURL); ?>vistas/publico/Dale/js/jquery.animation.js?v=0.23"></script>
    <!-- Stellar core JavaScript -->
    <script language="javascript"
        src="<?php echo (SERVERURL); ?>vistas/publico/Dale/js/jquery.stellar.min.js?v=0.23"></script>

    <!-- Retina core JavaScript -->
    <script language="javascript"
        src="<?php echo (SERVERURL); ?>vistas/publico/Dale/js/retina-1.1.0.min.js?v=0.23"></script>
    <!-- Nivo Slider JavaScript -->
    <script language="javascript"
        src="<?php echo (SERVERURL); ?>vistas/publico/Dale/js/jquery.nivo.slider.pack.js?v=0.23"></script>
    <!-- Video core JavaScript -->
    <!-- OWL Carousel core JavaScript -->
    <script language="javascript"
        src="<?php echo (SERVERURL); ?>vistas/publico/Dale/js/owl.carousel.min.js?v=0.23"></script>
    <!-- CountUp core JavaScript -->
    <script language="javascript" src="<?php echo (SERVERURL); ?>vistas/publico/Dale/js/countUp.min.js?v=0.23"></script>
    <!-- EasypieChart core JavaScript -->
    <script language="javascript"
        src="<?php echo (SERVERURL); ?>vistas/publico/Dale/js/jquery.easypiechart.min.js?v=0.23"></script>

    <!-- REDES SOCIALES core JavaScript -->
    <script language="javascript" src="<?php echo (SERVERURL); ?>vistas/publico/Dale/js/functions.js?v=0.23"></script>
    <!-- Everything else -->

    <script language="javascript" src="<?php echo (SERVERURL); ?>vistas/publico/Dale/js/main.js?v=0.23"></script>
    <!-- scrip API -->
    <!-- Bootstrap core JavaScript -->
    <script language="javascript" src="<?php echo SERVERURL; ?>vistas/publico/js/bootstrap.min.js?v=0.23"></script>
    <script src="<?php echo SERVERURL; ?>vistas/js/sweet-alert.min.js?v=0.23"></script>
    <script type="text/javascript"
        src="<?php echo (SERVERURL); ?>plugins/jquery-pagination/jquery.Pagination.min.js?v=0.23"></script>
    <script type="text/javascript" src="<?php echo (SERVERURL); ?>plugins/sticky/stickybits.min.js?v=0.23"></script>

    <!-- scrip EDIT -->
    <script type="text/javascript" src="<?php echo (SERVERURL); ?>vistas/scripts/util/functions.js?v=0.23"></script>
    <script language="javascript"
        src="<?php echo (SERVERURL); ?>vistas/scripts/util/configuration_api.js?v=0.23"></script>
    <script language="javascript"
        src="<?php echo (SERVERURL); ?>vistas/scripts/util/functions_operational.js?v=0.23"></script>
    <script language="javascript" src="<?php echo (SERVERURL); ?>vistas/scripts/init_parameters.js?v=0.23"></script>
    <!--Scripts -->
    <script type="text/javascript" src="<?php echo (SERVERURL); ?>vistas/scripts/session/js.cookie.js?v=0.23"></script>
    <script type="text/javascript"
        src="<?php echo (SERVERURL); ?>vistas/scripts/session/session.validate.login.js?v=0.23"></script>

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