
<footer class="classic " id="footer">
    <section class="content-section parallax-bg-3" data-stellar-background-ratio=".15" style="padding:0 20px;">
        <div class="foot-wrapper">
            <!-- <div class="container"> -->
            <div class="row">

                <div class="col-md-2 col-sm-2 anim fadeInLeft">
                    <span class="logo">
                        <img src="<?php echo (SERVERURL."adjuntos/".$dataempresa[0]['EmpresaLogo']); ?>" alt="Light logo"
                            style="width: 69px; height: 110px;" />
                    </span><!-- .logo -->
                </div><!-- .col-lg-3 -->

                <div class="col-md-3 col-sm-3 anim fadeInRight">
                    <h5 style="font-weight: bold;text-align: center;">PONGASE EN CONTACTO</h5>

                    <div class="contact-info" style="text-align: center;">
                        <span>
                            <i class="fa fa-city"></i>
                            Lambayeque - Perú
                        </span>
                        <span>
                            <i class="fa fa-phone"></i>
                            +(51) <?php echo ($dataempresa[0]['EmpresaTelefono']); ?>
                        </span>
                        <span>
                            <i class="fa fa-phone"></i>
                            +(51) <?php echo ($dataempresa[0]['EmpresaTelefono2']); ?>
                        </span>

                    </div>
                </div><!-- .col-lg-3 -->
                <!-- .col-lg-3 -->

                <div class="col-md-3 col-sm-3 anim fadeInRight">
                    <h5 style="font-weight: bold;text-align: center;">HAS TUS SUEÑOS REALIDAD Y SE FELIZ</h5>
                    <div class="contact-info" style="text-align: center;">
                        <span>
                        <i class="fa fa-comment"></i> <?php echo ($dataempresa[0]['EmpresaEmail']); ?>
                        </span>
                        <span>
                            <a href="<?php echo ($dataempresa[0]['Enlace']); ?>" style="color:white;" target="_blank"><?php echo ($dataempresa[0]['Enlace']); ?></a>
                        </span>


                    </div>
                </div><!-- .col-lg-3 -->

                <div class="col-md-4 col-sm-4 anim fadeInRight">
                    <h5 style="font-weight: bold;text-align: center;">TERMINOS Y CONDICIONES DE PRIVACIDAD</h5>
                    <div class="contact-info" style="    margin-left: 40%;">
                        <ul class="social-media " data-wow-delay="0.25s">
                            <li><a href="<?php echo ($dataempresa[0]['facebook']); ?>" class="facebook" target="_blank"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="<?php echo ($dataempresa[0]['youtube']); ?>" class="youtube" target="_blank"><i class="fa fa-youtube"></i></a></li>
                        </ul><!-- .social-media -->
                    </div>
                </div><!-- .col-lg-3 -->

            </div><!-- .row -->

            <div class="row">
                <div class="col-lg-12">
                    <span class="copyright">Copyright 2019 - All RIghts Reserved</span>
                </div><!-- .col-lg-12 -->
            </div><!-- .row -->
            <!-- </div>.container -->
        </div><!-- .foot-wrapper -->
        <div class="feature-list " style="background-color: transparent;padding-bottom: 0px;">
            <div class="bootRegistrar ">
                <form method="POST" action="" class="FormularioAjaxRegistrar">
                    <div class="row">
                            <div class="col-lg-10 col-sm-10 col-xs-10">
                                <h4 class="text-center" style="font-size:22px;">Registrate</h4>
                            </div>
                        <div class="col-lg-2 col-sm-2 col-xs-2"><button type="button" class="close" style="cursor:pointer; width:30px;height:30px; padding:3px; float: right;">
                        <span >&times;</span>
                    </button></div>

                    </div>
                    
                    
                    <div class="fluid-list "style=" padding-top: 17px; ">
                        <div class="group-material">
                            <input type="text" class="material-control tooltips-general "style="font-size:13px;" placeholder="E-mail"
                                name="nombre-reg" maxlength="50" data-toggle="tooltip"
                                pattern="[a-zA-Z-áéíóúÁÉÍÓÚñÑ\s]{1,40}" required="" data-placement="top"
                                title="Escribe Tu Nombre">
                            <span class="highlight"></span>
                            <span class="bar"></span>
                            <label style="font-size:13px;">Nombre</label>
                        </div>
                        <div class="group-material">
                            <input type="email" class="material-control tooltips-general "style="font-size:13px;" placeholder="E-mail"
                                name="email-reg" maxlength="50" data-toggle="tooltip"
                                pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" required="" data-placement="top"
                                title="Escribe Tu email">
                            <span class="highlight"></span>
                            <span class="bar"></span>
                            <label style="font-size:13px;">Correo electrónico</label>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="accion" value="buscar">
                            <button type="submit" class="btn btn-purple" style="width: 100%;padding: 7px 12px ">Registrar</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="boot">
                <a href="#first"><i class="fa fa-weixin" id="go-down"></i></a>
            </div>
        </div><!-- .container -->



    </section><!-- .content-section -->

</footer>