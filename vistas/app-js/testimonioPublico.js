$(document).ready(function() {
  listar();
  function listar() {
    // $("#cargarpagina").html(ajax_load);
    $.ajax({
      type: "GET",
      url: url + "ajax/testimonioAjax.php",
      data: { acion: "listando" },
      // modificar el valor de xhr a nuestro gusto
      xhr: function() {
        // obtener el objeto XmlHttpRequest nativo
        let xhr = $.ajaxSettings.xhr();
        // añadirle un controlador para el evento onprogress
        xhr.onprogress = function(evt) {
          // calculamos el porcentaje y nos quedamos sólo con la parte entera
          let porcentaje = Math.floor((evt.loaded / evt.total) * 100);
          // actualizamos el texto con el porcentaje mostrado
          $("#progress_id").text(porcentaje + "/100");
          // actualizamos la cantidad avanzada en la barra de progreso
          $("#progress_id").attr("aria-valuenow", porcentaje);
          $("#progress_id").css("width", porcentaje + "%");
        };
        // devolvemos el objeto xhr modificado
        return xhr;
      },
      success: function(respuesta) {
        // (respuesta);

        let capitulo = JSON.parse(respuesta);
        let html = "";
        let contador = 0;
        for (var key in capitulo) {
          contador++;
          html += `<div numero="${contador}" class="col-xs-6 col-sm-4 col-md-3 portfolio-item ">
          <div class="portfolio-wrapper">
            <div class="portfolio-single">
              <div class="portfolio-thumb">
                <img
                  width="300px"
                  height="280px"
                  src="${url}adjuntos/testimonio/${capitulo[key].imagen}"
                  alt=""
                />
                
              </div>
              <div class="portfolio-descripcion" style="display:none;">
              ${capitulo[key].descripcion}
              </div>
              <div class="portfolio-view">
                <ul class="nav nav-pills">
                  <li>
                    <a class="editar-Admin"><i class="fa fa-link"></i
                    ></a>
                  </li>
                </ul>
              </div>
            </div>
            <div class="portfolio-info ">
              <h2>${capitulo[key].titulo}</h2>
              <h2 style="display:none;">${capitulo[key].imagen}</h2>
            </div>
          </div>
        </div>`;
          $(".RespuestaLista").html(html);
        }
        addEventsButtonsAdmin();
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
  function addEventsButtonsAdmin() {
    $(".editar-Admin").each(function(index, value) {
      $(this).click(function() {
        var indice = $(
          this.parentElement.parentElement.parentElement.parentElement
            .parentElement.parentElement
        ).attr("numero");
        var rowvalue = [];
        var rowvalue2 = [];
        $(".portfolio-item").each(function(i, v) {
          rowvalue[i] = $(
            ".portfolio-wrapper > .portfolio-single > .portfolio-descripcion",
            this
          )
            .map(function() {
              return $(this).html();
            })
            .get();
          rowvalue2[i] = $(".portfolio-wrapper > .portfolio-info > h2", this)
            .map(function() {
              return $(this).text();
            })
            .get();
        });
        for (let index = 0; index < rowvalue.length; index++) {
          if (rowvalue[index][0] == indice) {
            indice = index + 1;
          }
        }

        $("#Modaldescripcion").modal("show");
        $("#Modal-Nombre").html(rowvalue2[indice - 1][0]);

        $("#Modal-Imagen").html(`<img
        src="${url}adjuntos/testimonio/${rowvalue2[indice - 1][1]}"
        class="img-responsive"
       
      />`);
        $("#Modal-Descripcion").html(rowvalue[indice - 1][0]);
      });
    });
  }
});
