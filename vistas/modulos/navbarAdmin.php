<?php
$clave = $_SESSION["password"];
$user = $_SESSION["user"];
$tipo = $_SESSION["tipo"];

if (!(($tipo == 'Cliente') || ($tipo == 'Administrador'))) {
    session_destroy();
    echo '<script> window.location.href="' . SERVERURL . 'login" </script>';

}
?>
<nav class="navbar-user-top full-reset" style="position:fixed;background-color: #2d3945;">
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
      <a href="<?php echo SERVERURL; ?>perfilAdmin" style="color:#fff;">
         <span class="all-tittles"><?php echo ($user); ?></span> </a>

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

    <li class="mobile-menu-button visible-xs" style="float: left !important;">
    <i class="zmdi zmdi-menu"></i>
    </li>

  </ul>

</nav>
<?php include 'vistas/modulos/ayuda.php';?>