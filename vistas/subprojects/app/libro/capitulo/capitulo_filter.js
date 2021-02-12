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


});

function processAjaxCapituloC() {
  let parameters_pagination = '';
  switch (beanRequestCapituloC.operation) {
    default:
      parameters_pagination +=
        '?filtro=';
      parameters_pagination +=
        '&pagina=1';
      parameters_pagination +=
        '&registros=100';
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

  document.querySelector('#txtCapitulo').innerHTML = "";
  let row = '<option value="0">SIN DEFINIR</option>';
  if (beanPagination.list.length == 0) {

    document.querySelector('#txtCapitulo').innerHTML += row;
    return;
  }
  beanPagination.list.forEach((capitulo) => {

    row += `
<option value="${capitulo.idtitulo}">${capitulo.codigo} - ${capitulo.nombre}</option>
`;
    // $('[data-toggle="tooltip"]').tooltip();
  });


  document.querySelector('#txtCapitulo').innerHTML += row;



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
