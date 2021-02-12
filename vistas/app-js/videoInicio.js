$(document).ready(function() {
  let ajax_load =
    "<div class='progress'>" +
    "<div id='progress_id' class='progress-bar progress-bar-striped active' " +
    "role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width: 45%'>" +
    "n/100</div></div>";
  let pag = 1;
  let total = 5;
  listar(pag, total);
  function filePreview(input, imagen) {
    if (input.files && input.files[0]) {
      let reader = new FileReader();
      reader.onload = function(e) {
        $(imagen).html(
          "<img alt='user-picture' class='img-responsive center-box'style='width:400px;height:226px;' src='" +
            e.target.result +
            "' />"
        );
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
  $("#Imagen-reg").change(function() {
    filePreview(this, "#imagePreview");
  });
  $("#Video-reg").change(function() {
    videoPreview(this, "#videoPreview");
  });
  function videoPreview(input, imagen) {
    if (input.files && input.files[0]) {
      let reader = new FileReader();
      reader.onload = function(e) {
        $(imagen).html(
          "<video alt='user-picture' class='img-responsive center-box'style='width:400px;height:226px;' controls ><source src='" +
            e.target.result +
            "' type='video/mp4'></video>"
        );
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
  $(".FormularioAjax").submit(function(e) {
    $("#insertarModal").modal("hide");
    $("#modificarModal").modal("hide");
    e.preventDefault();
    let form = $(this);
    let metodo = form.attr("method");
    let formdata = new FormData(this);
    formdata.append("accion", "updateInicio");
    // let file1 = $("#Imagen-reg")[0].files[0];
    let file2 = $("#Video-reg")[0].files[0];
    if (file2) {
      $("#cargarpagina").html(ajax_load);
      ProcesarAjax(metodo, formdata);
    } else {
      swal(
        "Ocurrió un error inesperado",
        "No seleccionaste ningun archivo",
        "error"
      );
    }
  });
  function ProcesarAjax(metodo, formdata) {
    $.ajax({
      type: metodo,
      url: url + "ajax/videosAjax.php",
      data: formdata,
      cache: false,
      contentType: false,
      processData: false,
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
      success: function(data) {
        console.log(data);
        swal({
          title: JSON.parse(data).Titulo,
          text: JSON.parse(data).Texto,
          type: JSON.parse(data).Tipo,
          confirmButtonText: "Aceptar"
        });
        listar(pag, total);
        $(".FormularioAjax")[0].reset();
        $("#cargarpagina").html("");
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
  function listar(paginas, registrototal) {
    $.ajax({
      type: "GET",
      url: url + "ajax/videosAjax.php",
      data: {
        acion: "listar",
        pagina: paginas,
        registros: registrototal,
        ubica: "1"
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
        respuesta;
        if (respuesta != "ninguno") {
          let videos = JSON.parse(respuesta);
          for (let key in videos) {
            $("#ID-reg").val(videos[key]["idvideos"]);
            $("#imagePreview").html(
              `<img  alt='user-picture' class='img-responsive 
                 center-box'style='width:400px;height:226px;' src='${url +
                   "adjuntos/video-imagenes/" +
                   videos[key].imagen}' />`
            );
            $("#videoPreview").html(
              `<video  alt='user-picture' 
                class='img-responsive center-box' style='width:400px;height:226px;'controls >
                <source src='${url +
                  "adjuntos/videos/" +
                  videos[key].video}' type='video/mp4'></video>`
            );
          }
        } else {
          let html = `No hay registros`;
          $(".RespuestaLista").html(html);
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
});
