<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Andres">
    <title><?php echo (COMPANY); ?></title>
    <link href="<?php echo (SERVERURL); ?>vistas/publico/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo (SERVERURL); ?>vistas/publico/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?php echo (SERVERURL); ?>vistas/publico/css/main.css" rel="stylesheet">
    <link href="<?php echo (SERVERURL); ?>vistas/publico/css/responsive.css" rel="stylesheet">

    <!--[if lt IE 9]>
        <script src="js/html5shiv.js"></script>
        <script src="js/respond.min.js"></script>
    <![endif]-->
    <link rel="shortcut icon" href="images/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo (SERVERURL); ?>vistas/publico/images/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo (SERVERURL); ?>vistas/images/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo (SERVERURL); ?>vistas/publico/images/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="<?php echo (SERVERURL); ?>vistas/publico/images/ico/apple-touch-icon-57-precomposed.png">
</head><!--/head-->

<body>
    <div class="logo-image">
       <a href="index"><img class="img-responsive" style="width:200px;"src="<?php echo (SERVERURL); ?>vistas/publico/images/loogoo.png"> </a>
    </div>
     <section id="coming-soon" >
         <div class="container">
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2">
                    <div class=" coming-content text-center">
                        <h1>EN CONSTRUCCIÓN</h1>
                        <p>Hemos pasado largas horas para lanzar nuestro nuevo sitio web.
                            Ofreceremos muchas novedades y contenido destacado de nuestro último trabajo.
                             Únase a nuestra lista de correo o síganos en
                            <br /> Facebook o whatsapp para mantenerse al día.</p>
                        <div class="social-link">
                            <span><a href="#"><i class="fa fa-facebook"></i></a></span>
                            <span><a href="clpecom@gmail.com"><i class="fa fa-google-plus"></i></a></span>
                            <a target="_blank" href="https://web.whatsapp.com/send?phone=51924421734"><i class="fa fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12" style="padding-bottom:5%;">
                    <div class="time-count">
                        <ul id="countdown">
                            <li class="angle-one">
                                <span class="days time-font">04</span>
                                <p>Dias</p>
                            </li>
                            <li class="angle-two">
                                <span class="hours time-font">12</span>
                                <p>Horas</p>
                            </li>
                            <li class="angle-three">
                                <span class="minutes time-font">56</span>
                                <p class="minute">Minutos</p>
                            </li>
                            <li class="angle-four">
                                <span class="seconds time-font">30</span>
                                <p>Segundos</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="subscribe" style="display:none;">
        <div class="container">
            <div class="row">
                <div class="col-sm-10 col-sm-offset-1">
                    <div class="row">
                        <div class="col-sm-6">
                            <h2><i class="fa fa-envelope-o"></i> SUBSCRIBE TO OUR NEWSLETTER</h2>
                            <p>Quis filet mignon proident, laboris venison tri-tip commodo brisket aute ut. Tail salami pork belly, flank ullamco bacon bresaola do beef<br /> laboris venison tri-tip.</p>
                        </div>
                        <div class="col-sm-6 newsletter">
                            <form id="newsletter">
                                <input class="form-control" type="email" name="email"  value="" placeholder="Enter Your email">
                                <i class="fa fa-check"></i>
                            </form>
                            <p>Don't worry we will not use your email for spam</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="coming-soon-footer">
        <div class="row">
            <div class="col-sm-12">
                <div class="text-center">
                    <p>&copy; Tu compañia 2019. Todos los derechos reservados.</p>
                    <p>Creado Por <span style="color:#C39BD3;">Andres Llontop Diaz</span><a target="_blank"href="https://web.whatsapp.com/send?phone=51961594461"> &nbsp;&nbsp;<i class="fa fa-whatsapp"></i></a>&nbsp;
                    &nbsp;&nbsp;<a href="mailto:llontopdiazandres@gmail.com"><i class="fa fa-google-plus"></i></a>
                </p>
                </div>
            </div>
        </div>
    </section>


    <script type="text/javascript" src="<?php echo (SERVERURL); ?>vistas/publico/js/jquery.js"></script>
    <script type="text/javascript" src="<?php echo (SERVERURL); ?>vistas/publico/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo (SERVERURL); ?>vistas/publico/js/coundown-timer.js"></script>
    <script type="text/javascript" src="<?php echo (SERVERURL); ?>vistas/publico/js/wow.min.js"></script>
    <script type="text/javascript" src="<?php echo (SERVERURL); ?>vistas/publico/js/main.js"></script>
    <script type="text/javascript">
            //Countdown js
         $("#countdown").countdown({
                date: "10 march 2015 12:00:00",
                format: "on"
            },
            function() {
                // callback function
        });
    </script>

</body>
</html>