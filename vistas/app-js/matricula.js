$(document).ready(function() {
  let ajax_load = `<div class="progress">
    <div id="bulk-action-progbar" class="progress-bar progress-bar-striped active" role="progressbar"
    aria-valuenow="1" aria-valuemin="0" aria-valuemax="100" style="width:1%">                 
    cargando... </div>
    </div>`;
  //   listar();
  $("#btn-pregunta").click(function() {
    $("#pregunta").modal("show");
  });
  $(".FormuAjax").submit(function(e) {
    e.preventDefault();
    let form = $(this);
    let tipo = form.attr("data-form");
    let metodo = form.attr("method");
    let formdata = new FormData(this);
    formdata.append("accion", tipo);
    formdata.append("Tipo-reg", "Cliente");
    tipo;
    $("#cargarpagina").html(ajax_load);
    Procesar(metodo, formdata);
  });
  function Procesar(metodo, formdata) {
    $.ajax({
      type: metodo,
      url: url + "ajax/administradorAjax.php",
      data: formdata,
      cache: false,
      contentType: false,
      processData: false,
      // modificar el valor de xhr a nuestro gusto
      xhr: function() {
        var xhr = new window.XMLHttpRequest();
        //Upload progress, request sending to server
        xhr.upload.addEventListener(
          "progress",
          function(evt) {
            console.log("in Upload progress");
            console.log("Upload Done");
          },
          false
        );
        //Download progress, waiting for response from server
        xhr.addEventListener(
          "progress",
          function(e) {
            console.log("in Download progress");
            if (e.lengthComputable) {
              //percentComplete = (e.loaded / e.total) * 100;
              percentComplete = parseInt((e.loaded / e.total) * 100, 10);
              console.log(percentComplete);
              $("#bulk-action-progbar").data("aria-valuenow", percentComplete);
              $("#bulk-action-progbar").css("width", percentComplete + "%");
            } else {
              console.log("Length not computable.");
            }
          },
          false
        );
        return xhr;
      },
      success: function(data) {
        console.log(data);
        $("#cargarpagina").html("");

        swal({
          title: JSON.parse(data).Titulo,
          text: JSON.parse(data).Texto,
          type: JSON.parse(data).Tipo,
          confirmButtonText: "Aceptar"
        });
        if (!JSON.parse(data).Tipo == "Error") {
          $("#matricula").modal("hide");
          $(".FormuAjax")[0].reset();
        }
        if (
          JSON.parse(data).Texto ==
          "las contrase\u00f1as que acabas de ingresar no coinciden"
        ) {
          console.log("holiiii");
          $("#Password1-reg").val("");
          $("#Password2-reg").val("");
        } else {
        }
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
  function listar() {
    let list = "listar";
    $.get(url + "ajax/libroAjax.php", { acion: list }, function(respuesta) {
      respuesta;
      let libro = JSON.parse(respuesta);
      let html2 = "";
      let contador = 0;
      for (let key in libro) {
        contador++;

        html2 += `
        <div class="col-lg-12   col-md-12  feature anim fadeInLeft" data-wow-delay="0.25s"  >
        <img class="img-respons" src="${url}adjuntos/libros/${
          libro[key].desImagen
        }" >
            </div>
            <div class="col-lg-12  col-md-12  feature anim fadeInLeft" data-wow-delay="0.25s">
                        <div class="btn-sm" >
                        <p class="text-justify" style="font-size:22px;">${
                          libro[key].descripcion
                        }
            </p>
                        </div>
            </div>
                    
        `;
        $(".RespuestaDescripcion").html(html2);
      }
      addEventsButtonsAdmin();
    });
  }
  function addEventsButtonsAdmin() {
    $(".btn-matricula").each(function(index, value) {
      $(this).click(function() {
        $("#matricula").modal("show");
        $("#formularioAdmin").attr("data-form", "save");
        $("#Codigo-reg").val(
          $(this.parentElement.parentElement).attr("codigo")
        );
      });
    });
  }
});
