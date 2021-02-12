var beanPaginationPromotor;
var promotorSelected;
var beanRequestPromotor = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    document.body.style.background = "#f7f7f7 url(" + getHostFrontEnd() + "vistas/subprojects/publico/blog/img/pattern.png) repeat top left";
    beanRequestPromotor.entity_api = 'promotor';
    beanRequestPromotor.operation = 'paginate';
    beanRequestPromotor.type_request = 'GET';


    let fetOptions = {
        headers: {
            "Content-Type": 'application/json; charset=UTF-8',
            //"Authorization": "Bearer " + token
        },
        method: "GET",
    }
    /* PROMESAS LLAMAR A LAS API*/
    circleCargando.containerOcultar = $(document.querySelector("#tbodyPromotor"));
    circleCargando.container = $(document.querySelector("#tbodyPromotor").parentElement);
    circleCargando.createLoader();
    circleCargando.toggleLoader("show");
    Promise.all([
        fetch(getHostAPI() + beanRequestPromotor.entity_api + "/" + beanRequestPromotor.operation +
            "?filtro=0&pagina=1&registros=10", fetOptions),
        fetch(getHostAPI() + "video/promotores/ubicacion" +
            "?tipo=3", fetOptions),
        fetch(getHostAPI() + "empresa/obtener" +
            "?filtro=&pagina=1&registros=1", fetOptions)
    ])
        .then(responses => Promise.all(responses.map((res) => res.json())))
        .then(json => {
            circleCargando.toggleLoader("hide");
            if (json[0].beanPagination !== null) {
                beanPaginationPromotor = json[0].beanPagination;
                listaPromotor(beanPaginationPromotor);
            }
            if (json[1].beanPagination !== null) {
                beanPaginationVideo = json[1].beanPagination;
                listaVideoPromotor(beanPaginationVideo);
            }
            if (json[2].beanPagination !== null) {
                beanPaginationFooterPublico = json[2].beanPagination;
                listaFooterPublico(beanPaginationFooterPublico);
            }

        })
        .catch(err => {
            showAlertErrorRequest();
        });
    /* */



});



function listaPromotor(beanPagination) {
    document.querySelector('#tbodyPromotor').innerHTML = '';
    let row = "";

    beanPagination.list.forEach((promotor) => {

        row += ` 
      <div class="col-sm-6  col-xs-12 member-wrapper">
      <div class="ms-slide mx-10 mx-xs-0" style="width:auto;">
        <span>
          <img style="z-index:1;" src="${getHostFrontEnd() + "adjuntos/team/" + promotor.foto}" />
        </span>
        <div class="btn-purple mr-3 ml-3 mb-3" style="margin-top:-100px;padding-top:70px;border-radius: 24px;-webkit-box-shadow: 0 6px 7px #777777b5;
        -moz-box-shadow: 0 6px 7px #777777b5;
        box-shadow: 0 6px 7px #777777b5;
       ">
          <h4 class="text-center f-weight-700" style="font-size: 22px;">${promotor.nombre + " " + promotor.apellido}</h4>
          <div style="font-size:15px;line-height: 23px;text-align: justify;"> ${promotor.descripcion}
          <ul class="social-media pt-2">
            <li><a class="youtube" href="${promotor.youtube}" target="_blank"><i
                  class="fa fa-youtube"></i></a></li>
            <li>
            <li>
              <div class="w-auto my-auto">
              <button  idpromotor="${promotor.idpromotor}" class="btn btn-warning ver-promotor " type="button"> Leer más... </button>
              </div>
            </li>
            <li><a href="${promotor.email}" class="facebook" target="blank"><i
                  class="fa fa-facebook "></i></a></li>
          </ul>
          </div>
        </div>
      </div>
    </div>
      `;

        // $('[data-toggle="tooltip"]').tooltip();
    });


    document.querySelector('#tbodyPromotor').innerHTML += row;

    addEventsButtonsPromotor();


}

function addEventsButtonsPromotor() {
    document.querySelectorAll('.ver-promotor').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            promotorSelected = findByPromotor(
                btn.getAttribute('idpromotor')
            );

            if (promotorSelected != undefined) {

                document.querySelector("#imagenPortadaPromotor").innerHTML = `<img style="margin-top: 0;width: 100%;height: 300px;" src="${getHostFrontEnd() + "adjuntos/team/" + promotorSelected.fotoPortada}">`;
                document.querySelector("#descripcionPromotor").innerHTML = promotorSelected.descripcion;
                document.querySelector("#nombrePromotor").innerHTML = promotorSelected.nombre + " " + promotorSelected.apellido;
                document.querySelector("#historiaPromotor").innerHTML = promotorSelected.historia;

                $("#modalHistoriaPromotor").modal("show");

            } else {
                document.querySelector("#Modal-video").innerHTML = "";
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
}

function findIndexPromotor(idbusqueda) {
    return beanPaginationPromotor.list.findIndex(
        (Promotor) => {
            if (Promotor.idpromotor == parseInt(idbusqueda))
                return Promotor;


        }
    );
}

function findByPromotor(idpromotor) {
    return beanPaginationPromotor.list.find(
        (Promotor) => {
            if (parseInt(idpromotor) == Promotor.idpromotor) {
                return Promotor;
            }


        }
    );
}


