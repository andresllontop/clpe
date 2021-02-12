$(document).ready(function() {
  listar();

  function listar() {
    // $("#cargarpagina").html(ajax_load);
    $.ajax({
      type: "GET",
      url: url + "ajax/videosAjax.php",
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
        let capitulo = JSON.parse(respuesta);
        let html = "";
        let contador = 0;
        let con = 0;
        for (var key in capitulo) {
          contador++;
          if (capitulo[key]["ubicacion"] == 3) {
            con++;
            if (con <= 3) {
              if (con == 1) {
                html += `<div class="item active">`;
                html += ` <div class="col-sm-4 col-xs-12 videoTest" numero="${
                  capitulo[key].enlace
                }">
                <div class="team-single-wrapper">
                    <div class="portfolio-wrapper">
                     <div class="portfolio-single">
                        <div class="portfolio-thumb">
                         <img width="315px" height="160px" src="${url}adjuntos/video-imagenes/${
                  capitulo[key].imagen
                }">
                        </div>
                        <div class="portfolio-view">
                          <ul class="nav nav-pills" id="btn-video">
                          <li><a  class="editar-Admin"><i  class="fa fa-youtube-play"></i>
                          </a></li>
                          </ul>
                        </div>
                      </div>
                  
                    <div class="person-info">
                        <h2>${capitulo[key].nombre}</h2>
                        
                    </div>
                   
                </div>
            </div>`;
              } else {
                html += ` <div class="col-sm-4 col-xs-12 videoTest" numero="${
                  capitulo[key].enlace
                }">
                <div class="team-single-wrapper">
                    <div class="portfolio-wrapper">
                     <div class="portfolio-single">
                        <div class="portfolio-thumb">
                        <img width="315px" height="160px" src="${url}adjuntos/video-imagenes/${
                  capitulo[key].imagen
                }">
                        </div>
                        <div class="portfolio-view">
                          <ul class="nav nav-pills" id="btn-video">
                          <li><a  class="editar-Admin"><i  class="fa fa-youtube-play"></i>
                         </a></li>
                          </ul>
                          </div>
                          </div>
                          </div>
                          <div class="person-info">
                          <h2>${capitulo[key].nombre}</h2>
                         
                    </div>
                </div>
            </div>`;
              }
              if (capitulo[key]["ubicacion"].length == con || con == 3) {
                html += `</div>`;
              }
            } else {
              if (con == 3 * 1 + 1 || con == 3 * 2 + 1 || con == 3 * 3 + 1) {
                html += `<div class="item ">`;
                html += ` <div class="col-sm-4 col-xs-12 videoTest" numero="${
                  capitulo[key].enlace
                }">
                <div class="team-single-wrapper">
                    <div class="portfolio-wrapper">
                     <div class="portfolio-single">
                        <div class="portfolio-thumb">
                        <img width="315px" height="160px" src="${url}adjuntos/video-imagenes/${
                  capitulo[key].imagen
                }">
                        </div>
                        <div class="portfolio-view">
                          <ul class="nav nav-pills" id="btn-video">
                            <li><a  class="editar-Admin"><i  class="fa fa-youtube-play"></i>
                            </a></li>
                            </ul>
                            </div>
                            </div>
                            </div>
                            <div class="person-info">
                            <h2>${capitulo[key].nombre}</h2>
                          </div>
                </div>
            </div>`;
              } else {
                html += ` <div class="col-sm-4 col-xs-12 videoTest" numero="${
                  capitulo[key].enlace
                }">
                <div class="team-single-wrapper">
                    <div class="portfolio-wrapper">
                     <div class="portfolio-single">
                        <div class="portfolio-thumb">
                        <img width="315px" height="160px" src="${url}adjuntos/video-imagenes/${
                  capitulo[key].imagen
                }">
                        </div>
                        <div class="portfolio-view">
                          <ul class="nav nav-pills" id="btn-video">
                          <li><a  class="editar-Admin"><i  class="fa fa-youtube-play"></i>
                          </a></li>
                          </ul>
                        </div>
                      </div>
                    </div>
                    <div class="person-info">
                        <h2>${capitulo[key].nombre}</h2>
                    </div>
                </div>
            </div>`;
              }
              if (
                capitulo[key]["ubicacion"].length == con ||
                con == 3 * 2 ||
                con == 3 * 3 ||
                con == 3 * 4
              ) {
                html += `</div>`;
              }
            }
          }
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
            .parentElement.parentElement.parentElement
        ).attr("numero");
        $("#ventanaModalvideotest").modal("show");
        $("#Modal-video").html(
          `<div
          class="flowplayer-embed-container"
          style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width:100%;">
          <iframe
            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
            webkitAllowFullScreen mozallowfullscreen allowfullscreen
            src="${indice}"
            title="0" byline="0" portrait="0"
            width="100%" height="100%"
            frameborder="0"
            allow="autoplay">
          </iframe>
        </div>`
        );
      });
    });
  }
});
