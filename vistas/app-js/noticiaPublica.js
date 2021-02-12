$(document).ready(function() {
  //   listar();
  let ajax_load =
    "<div class='progress'>" +
    "<div id='progress_id' class='progress-bar progress-bar-striped active' " +
    "role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width: 45%'>" +
    "n/100</div></div>";

  function listar() {
    $.ajax({
      type: "GET",
      url: url + "ajax/noticiaAjax.php",
      data: {
        acion: "listar"
      },
      xhr: function() {
        let xhr = $.ajaxSettings.xhr();
        xhr.onprogress = function(evt) {
          let porcentaje = Math.floor((evt.loaded / evt.total) * 100);
          $("#progress_id").text(porcentaje + "/100");
          $("#progress_id").attr("aria-valuenow", porcentaje);
          $("#progress_id").css("width", porcentaje + "%");
        };
        return xhr;
      },
      success: function(respuesta) {
        console.log(respuesta);
        let admin = JSON.parse(respuesta);
        let html = "";
        let contador = 0;
        for (var key in admin) {
          contador++;
          html += ` <div class="ms-slide slide-${contador}" style="z-index: 10" data-delay="${contador}">
          <!-- slide background -->
          <img  src="${url}adjuntos/slider/${admin[key].imagen}"
          />
          <h2 class="ms-layer center" style="left:0; top:200px;background: rgba(85, 70, 96, 0.32); "
              data-effect="rotatetop(-40,60,l)"
              data-duration="3500"
              data-delay="0"
              data-ease="easeOutExpo"
          >${admin[key].titulo}</h2>
          <h3 class="ms-layer center"  style="left:0;color: white; top:300px ;width:100%;"
              data-effect="left(short)"
              data-duration="3500"
              data-delay="0"
              data-ease="easeOutExpo"
          >${admin[key].descripcion}</h3>

      </div>`;
        }
        $(".RespuestaNoticia").html(html);
      },
      error: function(e) {
        swal(
          "Ocurrió un error inesperado",
          "Por favor recargue la página",
          "error"
        );
      }
    });
    return false;
  }
});
