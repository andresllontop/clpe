
var beanPaginationTestimonio, beanPaginationTestimonioUnico;
var testimonioSelected, contadorTestimonio = 2, valorHover;
var beanRequestTestimonio = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {

  beanRequestTestimonio.entity_api = 'testimonios';
  beanRequestTestimonio.operation = 'paginate';
  beanRequestTestimonio.type_request = 'GET';
  document.body.style.background = "#f7f7f7 url(" + getHostFrontEnd() + "vistas/subprojects/publico/blog/img/pattern.png) repeat top left";

  document.querySelector("#cargarTestimonio").onclick = (btn) => {
    if (contadorTestimonio == 0) {
      addClass(btn, "d-none");
    } else {
      document.querySelector("#pageTestimonio").value = contadorTestimonio++;
      processAjaxTestimonio();
    }

  };


  let fetOptions = {
    headers: {
      "Content-Type": 'application/json; charset=UTF-8',
      //"Authorization": "Bearer " + token
    },
    method: "GET",
  }
  /* PROMESAS LLAMAR A LAS API*/
  circleCargando.containerOcultar = $(document.querySelector("#cargarTestimonio"));
  circleCargando.container = $(document.querySelector("#cargarTestimonio").parentElement);
  circleCargando.createLoader();
  circleCargando.toggleLoader("show");
  Promise.all([
    fetch(getHostAPI() + beanRequestTestimonio.entity_api + "/" + beanRequestTestimonio.operation +
      "?filtro=" + '&pagina=' + document.querySelector("#pageTestimonio").value.trim() + '&registros=4', fetOptions),
    fetch(getHostAPI() + "subitems/obtener" +
      "?tipo=7", fetOptions),
    fetch(getHostAPI() + "empresa/obtener" +
      "?filtro=&pagina=1&registros=1", fetOptions)
  ])
    .then(responses => Promise.all(responses.map((res) => res.json())))
    .then(json => {
      circleCargando.toggleLoader("hide");
      if (json[0].beanPagination !== null) {
        beanPaginationTestimonio = json[0].beanPagination;
        beanPaginationTestimonioUnico = json[0].beanPagination;
        listaTestimonio(beanPaginationTestimonioUnico);
      }
      if (json[1].beanPagination !== null) {
        document.querySelector("#titleFraseTestimonio").innerHTML = json[1].beanPagination.list[0].detalle;
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

function processAjaxTestimonio() {
  let parameters_pagination = '';
  let json = '';
  circleCargando.containerOcultar = $(document.querySelector("#cargarTestimonio"));
  circleCargando.container = $(document.querySelector("#cargarTestimonio").parentElement);
  circleCargando.createLoader();
  circleCargando.toggleLoader("show");
  switch (beanRequestTestimonio.operation) {

    default:
      parameters_pagination +=
        '?filtro=';
      parameters_pagination +=
        '&pagina=' + document.querySelector("#pageTestimonio").value.trim();
      parameters_pagination +=
        '&registros=4';
      break;
  }
  $.ajax({
    url: getHostAPI() + beanRequestTestimonio.entity_api + "/" + beanRequestTestimonio.operation +
      parameters_pagination,
    type: beanRequestTestimonio.type_request,
    json: json,
    contentType: 'application/json; charset=UTF-8',
    dataType: 'json'
  }).done(function (beanCrudResponse) {
    circleCargando.toggleLoader("hide");

    if (beanCrudResponse.beanPagination !== null) {
      beanPaginationTestimonioUnico = beanCrudResponse.beanPagination;

      if (beanCrudResponse.beanPagination.list.length > 0) {
        beanPaginationTestimonio.list = (beanPaginationTestimonio.list).concat(beanCrudResponse.beanPagination.list);
      }

      listaTestimonio(beanPaginationTestimonioUnico);


      //document.querySelector('#btnAbrirArea').dispatchEvent(new Event('click'));
    }
  }).fail(function (jqXHR, textStatus, errorThrown) {
    $('#modalCargandoTestimonio').modal("hide");
    showAlertErrorRequest();

  });

}

function listaTestimonio(beanPagination) {
  let row = "", contador = 0;
  if (beanPagination.list.length == 0) {
    addClass(document.querySelector("#cargarTestimonio"), "d-none");
    contadorTestimonio = 0;
    return;
  }
  beanPagination.list.forEach((testimonio) => {
    if (contador % 3 == 0) {
      row += `  <hr style="border-color: #c100c1;border-width: 3px;width: 98%;">`;
    }
    contador++;
    row += ` 
                <div class="col-sm-4 ver-testimonio aula-cursor-mano" idtestimonio="${testimonio.idtestimonio}">
                    <div style="position:relative;">
                    <img class="w-100 col-6"
                        src="${getHostFrontEnd()}adjuntos/testimonio/${testimonio.imagen}"
                        alt="${testimonio.titulo}"><span style="position: absolute;right: 50%;top: 50%;" class="ver-testimonio" idtestimonio="${testimonio.idtestimonio}"><i class="zmdi zmdi-youtube-play text-danger zmdi-hc-2x" ></i></span>
                    </div>
                    <div class="aula-cursor-mano text-truncate">
                        <div class="content-inner">
                            <h5 class="text-purple">${testimonio.titulo}</h5>
                            <p>${testimonio.descripcion}</p>
                        </div>
                    </div>

                </div>
                
            `;

  });

  document.querySelector('#tbodyTestimonio').innerHTML += row;

  addEventsButtonsTestimonio();

}

function addEventsButtonsTestimonio() {
  document.querySelectorAll('.ver-testimonio').forEach((btn) => {
    //AGREGANDO EVENTO CLICK
    btn.onclick = function () {
      testimonioSelected = findByTestimonio(
        btn.getAttribute('idtestimonio')
      );

      if (testimonioSelected != undefined) {

        document.querySelector("#txtvideoTestimonio").innerHTML = testimonioSelected.enlaceYoutube;
        document.querySelector("#txtvideoTestimonio").firstChild.classList.add("img-respons-v");

        $("#modalVideoTestimonio").modal("show");

      } else {
        document.querySelector("#txtvideoTestimonio").innerHTML = "";
        console.log(
          'warning',
          'No se encontró el Almacen para poder editar'
        );
      }
    };
  });

}

function findIndexTestimonio(idbusqueda) {
  return beanPaginationTestimonio.list.findIndex(
    (Testimonio) => {
      if (Testimonio.idtestimonio == parseInt(idbusqueda))
        return Testimonio;


    }
  );
}

function findByTestimonio(idtestimonio) {
  return beanPaginationTestimonio.list.find(
    (Testimonio) => {
      if (parseInt(idtestimonio) == Testimonio.idtestimonio) {
        return Testimonio;
      }


    }
  );
}


