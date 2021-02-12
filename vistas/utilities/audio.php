
<div class="modal" id="ventanaModalAudio" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog"
    aria-labelledby="logearTitle" aria-hidden="true" style="display:block">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <form id="" method="POST" action="" class="FormularioAjaxAudio">
        <div class="modal-content text-center">
        <div class="page-header ">
            <h3 class="all-tittles">
              Para poder ingresar a la leccion graba tu declaración y envialo.
            </h3>
            <button id="Abrir-VideoModal" type="button" class="btn btn-danger " >
              <i class="zmdi zmdi-videocam"></i> &nbsp;&nbsp; Grabar video
            </button>
          </div>
        <input type="hidden" name="Codigo" value="<?php echo ($_SESSION['cuentaCodigo']); ?>">
        <input type="hidden" name="Subtitulo" value="<?php echo ($resultcapitulo[0]['codigo_subtitulo']); ?>">
          <div class="modal-body">
          <div id="RespondeAudio" style="width:100%;padding-left:30%;"></div>
          <div id="cargarpagina" style="width:100%"></div>
            <div id="container2" class="videoRecorder">
              <div class="group-material text-center">
                <p>
                  El audio requiere Navegadores Firefox 29 o posterior, o Chrome 47 o
                  posterior con Habilitar las funciones de la plataforma web
                  experimental habilitada desde chrome.
                </p>
                <div class="row">
                  <div class="col-sm-3 col-sm-offset-4 col-xs-3 col-xs-offset-4 bg-danger efecto" style="padding:0;border-radius: 91px;margin-bottom:10px;">
                    <audio id="gum2" autoplay muted></audio>
                  <i class="zmdi zmdi-volume-up text-light" style="font-size:116px;"></i>
                  <button type="button" id="record2" class="btn btn-info"style="width:100%;margin:0;border-radius: 10px;">Comenzar Grabacion</button></div>

                  <div class="col-sm-7 col-sm-offset-2 col-xs-7 col-xs-offset-2">
                  <audio id="recorded2" autoplay loop></audio>
                  <button type="button" id="play2" disabled class="btn btn-primary">Play</button>
                  <button id="enviarAudio" type="button" class="btn btn-primary">Aceptar</button>
                  </div>
                </div>
                </div>
              </div>
            </div>
            <div class="RespuestaAjax fluid-list text-center"></div>
            <div class="modal-footer">
            <a href="<?php echo SERVERURL; ?>catalog"><button type="button" class="btn btn-success">Regresar</button>
            </a>
            <button type="submit" class="btn btn-primary">Guardar Audio</button>
          </div>
          </div>
        </div>
      </form>
    </div>
</div>