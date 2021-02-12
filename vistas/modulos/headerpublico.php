<?php

require './controladores/empresaControlador.php';
$Iempresa = new empresaControlador();
$dataempresa = $Iempresa->datos_empresa_controlador("conteo", 0);

?>
<div class="container-wrapper navigation">
    	<nav class="navbar navbar-default" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="">
                        <i class="fa fa-bars"></i>
                    </button>
                    <a class="navbar-brand" href="<?php echo(SERVERURL.'index'); ?>">
                        <img src="<?php echo SERVERURL; ?>adjuntos/logoHeader.jpg" style="margin-top:-14px ; width:50px;height:50px;"/>
                    </a>
                    <ul class="mini"></ul><!-- mobile navigation -->
            	</div><!-- .navbar-header -->
                <div class="collapse navbar-collapse">
                	<div class="left">
                        <ul class="nav navbar-nav">
                            <li><a href="<?php echo(SERVERURL.'matricula'); ?>">Curso</a></li>
                            <li><a href="<?php echo(SERVERURL.'nosotros'); ?>">Nosotros</a></li>
                            <li><a href="<?php echo(SERVERURL.'testimonios'); ?>">Testimonios</a></li>
                            <li><a href="<?php echo(SERVERURL.'blog'); ?>">Blog</a></li>
                        </ul>
                    </div>
                    <div class="right">
                        <ul style="padding-top:11px ;list-style:none;">
                            <li><button class="btn btn-bordered white" type="button" id="btn-logear" style="border-radius: 17px; font-size: 15px; padding: 5px 20px;"> Iniciar Sesión</button> </li>
                        </ul>
                    </div>
            	</div><!-- /.navbar-collapse -->
       		</div><!-- /.container-fluid -->
   		</nav>
</div>
<!-- header -->
<!--/ modal inciar seccion -->
<div class="modal fade " id="logear" style="top:70px;"
tabindex="-1" role="dialog" aria-labelledby="logearTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form id="" method="POST" action="" class="FormularioAjaxLogear"
    >
      <div class="modal-content"style=" width: 70%;  left: 20%;">
        <div class="modal-header">
        <button type="button" class="close"
        data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h2 class="fluid-list text-center title-m  ">Inicia sesión</h2>

        </div>
        <div class="modal-body">
          <div class="fluid-list">
            <div class="form-group">
              <label class="t-form">Correo electrónico</label>
              <input name="email" type="text" class="form-control" id="userL">
            </div>
            <div class="form-group">
              <label class="t-form">Contraseña</label>
              <input name="password" type="password" class="form-control" id="passL">
            </div>
            <div class="fluid-list text-olv">
              <p><a id="btn-restablecer"
              class="ref-f clickModalRecuperar">¿Olvidé mi contraseña?</a></p>
            </div>
            <div class="form-group">
            <input type="hidden" name="accion" value="buscar">
              <button type="submit" class="btn btn-purple-o"
              style="width: 100%; ">Iniciar
                sesión</button>
            </div>
            <div class="RespuestaAjax fluid-list text-center">
            </div>
            <!-- <div class="fluid-list text-content">
              <p>¿No tienes una cuenta?
                <a id="btn-registrar"class="ref-f clickModalRegister">Registrarme</a></p>
            </div> -->
          </div>
        </div>
      </div>

    </form>

  </div>
</div>
<!--/ modal regitrar -->
<div class="modal fade" id="registrar" style="top:70px;"
tabindex="-1" role="dialog" aria-labelledby="registrarTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form  method="POST" action="" class="FormularioAjaxRegistrar"
    >
      <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close"
        data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <div class="fluid-list text-center title-m ">Registrate Para Recibir Información Gratuita</div>

        </div>
        <div class="modal-body ">

          <div class="fluid-list " style="padding-top:30px;">
          <div class="group-material">
              <input type="text" class="material-control tooltips-general " placeholder="E-mail"
              name="nombre-reg" maxlength="50" data-toggle="tooltip"
              pattern="[a-zA-Z-áéíóúÁÉÍÓÚñÑ\s]{1,40}"
                                    required="" data-placement="top" title="Escribe Tu email">
                                <span class="highlight"></span>
                                <span class="bar"></span>
            <label>Nombre</label>
            </div>
            <!-- <div class="form-group">
              <label class="t-form">Correo electrónico</label>
              <input name="email-reg" type="text" class="form-control" >
            </div> -->
            <div class="group-material">
            <input type="email" class="material-control tooltips-general " placeholder="E-mail"
            name="email-reg" maxlength="50" data-toggle="tooltip" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                                    required="" data-placement="top" title="Escribe Tu email">
                                <span class="highlight"></span>
                                <span class="bar"></span>
            <label>Correo electrónico</label>
            </div>
            <div class="form-group">
            <input type="hidden" name="accion" value="buscar">
              <button type="submit" class="btn btn-purple"
              style="width: 100%; ">Registrar</button>
            </div>

          </div>
        </div>
      </div>

    </form>

  </div>
</div>
<!-- contraseña restablecer -->
<div class="modal fade" id="restabler-datos" style="top:70px;"
tabindex="-1" role="dialog" aria-labelledby="registrarTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form  method="POST"
    action="" class="FormularioAjaxRestablecer"
    >
      <div class="modal-content">
        <div class="modal-header">
        <button type="button" class="close"
        data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <div class="fluid-list text-center title-m ">Se Enviará un Mensaje a Tu correo</div>
        </div>
        <div class="modal-body">
          <div class="fluid-list">

            <div class="form-group">
              <label class="t-form">Correo electrónico</label>
              <input name="email" type="text" class="form-control" >
            </div>
            <div class="form-group">
            <input type="hidden" name="accion" value="buscar">
              <button type="submit" class="btn btn-purple"
              style="width: 100%; ">Enviar</button>
            </div>

          </div>
        </div>
      </div>

    </form>

  </div>
</div>

<div id="cargar" ></div>
