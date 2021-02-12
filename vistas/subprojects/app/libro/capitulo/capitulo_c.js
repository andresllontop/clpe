/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var beanPaginationCapituloC;
var capituloCSelected;
var beanRequestCapituloC = new BeanRequest();

document.addEventListener('DOMContentLoaded', function () {
  //INICIALIZANDO VARIABLES DE SOLICITUD
  beanRequestCapituloC.entity_api = 'capitulos';
  beanRequestCapituloC.operation = 'obtener';
  beanRequestCapituloC.type_request = 'GET';

  $('#sizePageCapituloC').change(function () {
    beanRequestCapituloC.operation = 'obtener';
    beanRequestCapituloC.type_request = 'GET';
    $('#modalCargandoCapituloC').modal('show');

  });


  $('#FrmCapituloC').submit(function (event) {
    beanRequestCapituloC.operation = 'obtener';
    beanRequestCapituloC.type_request = 'GET';

    $('#modalCargandoCapituloC').modal('show');
    event.preventDefault();
    event.stopPropagation();
  });
  $("#modalCargandoCapituloC").on('shown.bs.modal', function () {
    processAjaxCapituloC();
  });
  $('#modalCargandoCapituloC').on('hidden.bs.modal', function () {
    beanRequestCapituloC.operation = 'obtener';
    beanRequestCapituloC.type_request = 'GET';
  });

  document.querySelector('#btnSeleccionarCapitulo').onclick = function () {
    if (beanPaginationCapituloC != undefined && document.querySelector('#tbodyCapituloC').innerHTML != "") {
      $('#ventanaModalSelectedCapituloC').modal('show');
    } else {
      $('#modalCargandoCapituloC').modal('show');
      $('#ventanaModalSelectedCapituloC').modal('show');
    }
  };
});

function processAjaxCapituloC() {
  let parameters_pagination = '';
  switch (beanRequestCapituloC.operation) {
    default:
      parameters_pagination +=
        '?filtro=';
      parameters_pagination +=
        '&pagina=' + document.querySelector("#pageCapituloC").value.trim();
      parameters_pagination +=
        '&registros=' + document.querySelector("#sizePageCapituloC").value.trim();
      break;
  }
  $.ajax({
    url: getHostAPI() + beanRequestCapituloC.entity_api + "/" + beanRequestCapituloC.operation +
      parameters_pagination,
    type: beanRequestCapituloC.type_request,
    headers: {
      'Authorization': 'Bearer ' + Cookies.get("clpe_token")
    },
    data: null,
    cache: false,
    contentType: 'application/json; charset=UTF-8',
    processData: false,
    dataType: 'json'
  }).done(function (beanCrudResponse) {
    $('#modalCargandoCapituloC').modal('hide');
    if (beanCrudResponse.messageServer !== null) {
      if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
        swal({
          title: "Realizado",
          text: "Acción realizada existosamente!",
          type: "success",
          timer: 1200,
          showConfirmButton: false
        });
        document.querySelector("#pageCapituloC").value = 1;
        document.querySelector("#sizePageCapituloC").value = 5;
        $('#ventanaModalManCapitulo').modal('hide');
      } else {

        swal({
          title: "Error",
          text: beanCrudResponse.messageServer,
          type: "error",
          timer: 1200,
          showConfirmButton: false
        });
      }
    }
    if (beanCrudResponse.beanPagination !== null) {

      beanPaginationCapituloC = beanCrudResponse.beanPagination;
      toListCapituloC(beanPaginationCapituloC);
    }
  }).fail(function (jqXHR, textStatus, errorThrown) {
    $('#modalCargandoCapituloC').modal("hide");
    showAlertErrorRequest();

  });

}


function toListCapituloC(beanPagination) {
  document.querySelector('#tbodyCapituloC').innerHTML = '';
  document.querySelector('#titleManagerCapituloC').innerHTML =
    '[ ' + beanPagination.countFilter + ' ] CAPITULOS';
  let row = "";
  if (beanPagination.list.length == 0) {
    destroyPagination($('#paginationCapituloC'));
    row += `<tr>
    <td class="text-center" colspan="2">NO HAY CAPITULOS</td>
    </tr>`;

    document.querySelector('#tbodyCapituloC').innerHTML += row;
    return;
  }
  beanPagination.list.forEach((capitulo) => {

    row += `<tr class="click-selection-capitulo"  idtitulo="${capitulo.idtitulo}">
<td class="text-center">${capitulo.codigo}</td>
<td class="text-center">${capitulo.nombre}</td>
`;
    // $('[data-toggle="tooltip"]').tooltip();
  });


  document.querySelector('#tbodyCapituloC').innerHTML += row;
  buildPagination(
    beanPagination.countFilter,
    parseInt(document.querySelector("#sizePageCapituloC").value),
    document.querySelector("#pageCapituloC"),
    $('#modalCargandoCapituloC'),
    $('#paginationCapituloC'));
  addEventsCapitulosC();

}

function addEventsCapitulosC() {
  document
    .querySelectorAll('.click-selection-capitulo')
    .forEach(function (element) {
      element.onclick = function () {
        capituloCSelected = findByCapituloC(
          this.getAttribute('idtitulo')
        );
        if (capituloCSelected != undefined) {
          capituloSelected = capituloCSelected;
          document.querySelector('#txtCapituloTest').value = capituloCSelected.codigo + " - " + capituloCSelected.nombre.toUpperCase();
          $('#ventanaModalSelectedCapituloC').modal('hide');

        }
      };
    });
}

function findByCapituloC(idtitulo) {
  let capitulo_;
  beanPaginationCapituloC.list.forEach((capitulo) => {
    if (parseInt(idtitulo) == parseInt(capitulo.idtitulo)) {
      capitulo_ = capitulo;
      return;
    }
  });
  return capitulo_;
}
function validateFormCapitulo() {
  if (
    limpiar_campo(document.querySelector('#txtNombreCapitulo').value) == ''
  ) {
    showAlertTopEnd('warning', 'Por favor ingrese nombre');
    document.querySelector('#txtNombrenCapitulo').focus();
    return false;
  }
  return true;
}
