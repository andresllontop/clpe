
<script src="<?php echo SERVERURL; ?>vistas/app-js/mensaje.js"></script>
    <div class="padding-top content-page-container full-reset custom-scroll-containers mCustomScrollbar _mCS_2 mCS-autoHide content-page-container2">
      <div id="mCSB_2" class="padding-top mCustomScrollBox mCS-dark-thin mCSB_vertical mCSB_inside" tabindex="0"style="max-height: none;">
        <div id="mCSB_2_container" class="padding-top mCSB_container" style="position: relative; top: 0px; left: 0px;"dir="ltr">
          <?php include 'vistas/modulos/navbarAdmin.php';?>
          <!--contenido de la pagina  -->
        <div class="container padding-top">
            <div class="page-header">
              <h3 class="all-tittles"><?php echo(COMPANY);?> <small> Mensajes de Alumnos</small></h3>
            </div>
        </div>
        <!-- boton registrar -->
        <!-- <div class="container-fluid">
            <div class="container-flat">
              <button class="btn btn-primary" id="btnAbrirSubCapitulo">
                <i class="zmdi zmdi-plus-square"></i> &nbsp;&nbsp;Registrar
              </button>
            </div>
        </div> -->
        <!-- tabla -->
        <div class="container-fluid" style="padding-bottom:8%;">
            <div class="container-flat-form">
            <h2 class="text-center all-tittles">Lista de mensaje</h2>
            <div id="cargarpaginalista"></div>
            <!-- Example single danger button -->
            <div class="btn-group" style="padding-left: 2%;">
              <button type="button" class="btn btn-info dropdown-toggle"
              data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Cantidad&nbsp;&nbsp;
                <i class="zmdi zmdi-caret-down"></i>
              </button>
              <div class="dropdown-menu"style="min-width:0;padding:0;margin:0;margin-left:39%;">
              <button type="button" class="dropdown-item btn btn-primary"style="width:60px;">5</button>
              <div class="dropdown-divider"></div>
              <button type="button" class="dropdown-item btn btn-primary"style="width:60px;">10</button>
              <div class="dropdown-divider"></div>
              <button type="button" class="dropdown-item btn btn-primary"style="width:60px;">15</button>
              </div>
            </div>

            <div class="table-responsive">
              <table class="table table-hover text-center">
                <thead>
                  <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">CUENTA CODIGO</th>
                    <th class="text-center">USUARIO</th>
                    <th class="text-center">CORREO</th>
                    <th class="text-center">ASUNTO</th>
                    <th class="text-center">MENSAJE</th>
                    <th class="text-center">LEIDO ?</th>
                    <th class="text-center">ELIMINAR</th>
                  </tr>
                </thead>
                <tbody class="RespuestaLista"></tbody>
              </table>
            </div>
            <!-- <div id="cargarpagina"></div> -->
            <nav id="paginador"class="text-center" aria-label="...">
              <ul class="pagination "style="cursor:pointer;">
                <li class="page-item ">
                  <span class="page-link">Anterior</span>
                </li>
                <li class="page-item active"><span class="page-link" >1</span></li>
                <li class="page-item">
                  <span class="page-link">2</span>
                </li>
                <li class="page-item"><span class="page-link" >3</span></li>
                <li class="page-item">
                  <span class="page-link" >Siguiente</span>
                </li>
              </ul>
            </nav>
            </div>
        </div>
        <!-- modal -->
  

          <!--final del contenido de la pagina  -->

           
          
        </div>

      </div>
    </div>

