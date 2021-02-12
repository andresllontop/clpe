<?php
$clave = $_SESSION["password"];
$user = $_SESSION["user"];
$tipo = $_SESSION["tipo"];
if ($tipo != 'Cliente') {
    session_destroy();
    echo '<script> window.location.href="' . SERVERURL . 'login" </script>';
}
?>
<nav class="navbar-user-top full-reset">
  <ul class="list-unstyled full-reset">
    <figure>
             <img
        src="<?php echo (SERVERURL . "adjuntos/clientes/" . $_SESSION['foto']); ?>"
        alt="user-picture"
        class="img-responsive img-circle center-box"
      />
    </figure>
    <li style="color:#fff;  "
    class="tooltips-general " data-placement="bottom"
      title="Administrar Perfil">
      <a href="<?php echo SERVERURL; ?>perfil" style="color:#fff;">  <span class="all-tittles"><?php echo ($user); ?></span> </a>

    </li>
    <li
      class="tooltips-general exit-system-button"
      data-href="<?php echo SERVERURL; ?>index"
      data-placement="bottom"
      title="Salir del sistema"
    >
      <i class="zmdi zmdi-power"></i>
    </li>

    <li class="tooltips-general btn-help" data-placement="bottom" title="Ayuda">
      <i class="zmdi zmdi-help-outline zmdi-hc-fw"></i>
    </li>

    <figure  style="float: left !important;">
      <img
        src="<?php echo SERVERURL; ?>adjuntos/logoHeader.jpg"
        alt="user-picture"
        class="img-responsive img-circle center-box"
      />
    </figure>
    <li class="mobile-menu-button-navbar visible-xs2" >
      <i class="zmdi zmdi-menu"></i>
    </li>
  <div class="navbar-mobile">
    <li class="tooltips-general "  style="float: left !important;">
      <a href="<?php echo SERVERURL; ?>catalog"><i class="zmdi zmdi-home zmdi-hc-fw"></i>INICIO</a>
    </li>
    <li class="tooltips-general "  style="float: left !important;">
      <a href="<?php echo SERVERURL; ?>recursos"><i class="zmdi zmdi-book zmdi-hc-fw"></i>RECURSOS</a>
    </li>
    <li class="tooltips-general "  style="float: left !important;">
      <a href="<?php echo SERVERURL; ?>lecciones"><i class="zmdi zmdi-videocam zmdi-hc-fw"></i>LECCIONES REALIZADAS</a>
    </li>
    <li class="tooltips-general "  style="float: left !important;">
      <a href="<?php echo SERVERURL; ?>contactar"><i class="zmdi zmdi-email zmdi-hc-fw"></i>CONSULTAS</a>
    </li>
    <li class="tooltips-general "  style="float: left !important;">
    <a href="https://web.whatsapp.com/send?phone=51924421734" target="blank">
              <i class="zmdi zmdi-whatsapp zmdi-hc-fw" ></i>
              &NonBreakingSpace; +51 924421734
            </a>
    </li>
  </div>
  </ul>

</nav>
<?php include 'vistas/modulos/ayuda.php';?>