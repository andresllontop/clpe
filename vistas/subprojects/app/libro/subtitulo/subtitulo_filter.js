/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var beanPaginationSubtituloC;
var subtituloCSelected;
var beanRequestSubtituloC = new BeanRequest();

document.addEventListener('DOMContentLoaded', function () {
  //INICIALIZANDO VARIABLES DE SOLICITUD
  beanRequestSubtituloC.entity_api = 'subtitulos';
  beanRequestSubtituloC.operation = 'paginate';
  beanRequestSubtituloC.type_request = 'GET';

  $("#modalCargandoSubtituloC").on('shown.bs.modal', function () {
    processAjaxSubtituloC();
  });
  $('#modalCargandoSubtituloC').on('hidden.bs.modal', function () {
    beanRequestSubtituloC.operation = 'paginate';
    beanRequestSubtituloC.type_request = 'GET';
  });


});

function processAjaxSubtituloC() {
  let parameters_pagination = '';
  switch (beanRequestSubtituloC.operation) {
    case 'paginate':
      parameters_pagination +=
        '?filtro=&capitulo=' + capituloSelected.idtitulo;
      parameters_pagination +=
        '&pagina=1';
      parameters_pagination +=
        '&registros=500';
      break;
    default:
      parameters_pagination +=
        '?filtro=';
      parameters_pagination +=
        '&pagina=1';
      parameters_pagination +=
        '&registros=500';


      break;
  }
  $.ajax({
    url: getHostAPI() + beanRequestSubtituloC.entity_api + "/" + beanRequestSubtituloC.operation +
      parameters_pagination,
    type: beanRequestSubtituloC.type_request,
    headers: {
      'Authorization': 'Bearer ' + Cookies.get("clpe_token")
    },

    cache: false,
    contentType: 'application/json; charset=UTF-8',
    data: null,
    cache: false,
    processData: false,
  }).done(function (beanCrudResponse) {
    $('#modalCargandoSubtituloC').modal('hide');
    if (beanCrudResponse.messageServer !== null) {
      if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
        swal({
          title: "Realizado",
          text: "Acción realizada existosamente!",
          type: "success",
          timer: 1200,
          showConfirmButton: false
        });
        document.querySelector("#pageSubtituloC").value = 1;
        document.querySelector("#sizePageSubtituloC").value = 5;
        $('#ventanaModalManSubtitulo').modal('hide');
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

      beanPaginationSubtituloC = beanCrudResponse.beanPagination;
      // toListSubtituloC(beanPaginationSubtituloC);
      if (document.querySelector("#txtNombreTest")) {
        if (beanRequestTest.operation == 'add') {
          addDetalle();
        } else {
          toListTestDetalle(listDetalleTest);
        }
      } else {
        toListSubtituloC(beanPaginationSubtituloC);
      }


    }
  }).fail(function (jqXHR, textStatus, errorThrown) {
    $('#modalCargandoSubtituloC').modal("hide");
    showAlertErrorRequest();

  });

}

function toListSubtituloC(beanPagination) {
  document.querySelector('#txtSubtitulo').innerHTML = '';

  let row = '<option value="0">SIN DEFINIR</option>';
  if (beanPagination.list.length == 0) {
    document.querySelector('#txtSubtitulo').innerHTML += row;
    return;
  }
  beanPagination.list.forEach((subtitulo) => {

    row += `<option value="${subtitulo.codigo}">${subtitulo.codigo} - ${subtitulo.nombre}</option>
`;
    // $('[data-toggle="tooltip"]').tooltip();
  });

  document.querySelector('#txtSubtitulo').innerHTML += row;

  addEventsSubtitulosC();

}

function addEventsSubtitulosC() {
  document
    .querySelectorAll('.click-selection-subtitulo')
    .forEach(function (element) {
      element.onclick = function () {
        subtituloCSelected = findBySubtituloC(
          this.getAttribute('idsubTitulo')
        );
        if (subtituloCSelected != undefined) {
          if (document.querySelector('#txtSubTituloAlbum')) {
            switch (tipoSelected) {
              case 1:
                //ALBUM HASTA
                subtituloHastaSelected = subtituloCSelected;
                document.querySelector('#txtSubTituloAlbum').value = subtituloCSelected.codigo + " - " + subtituloCSelected.nombre.toUpperCase();
                $('#ventanaModalSelectedSubtituloC').modal('hide');

                break;

              default:
                //ALBUM DESDE
                subtituloSelected = subtituloCSelected;
                document.querySelector('#txtSubTituloRecurso').value = subtituloCSelected.codigo + " - " + subtituloCSelected.nombre.toUpperCase();
                $('#ventanaModalSelectedSubtituloC').modal('hide');
                break;
            }
            tipoSelected = 0;

          } else {

            subtituloSelected = subtituloCSelected;
            document.querySelector('#txtSubTituloRecurso').value = subtituloCSelected.codigo + " - " + subtituloCSelected.nombre.toUpperCase();
            $('#ventanaModalSelectedSubtituloC').modal('hide');
          }


        }
      };
    });
}

function findBySubtituloC(idsubTitulo) {
  let subtitulo_;
  beanPaginationSubtituloC.list.forEach((subtitulo) => {
    if (parseInt(idsubTitulo) == parseInt(subtitulo.idsubTitulo)) {
      subtitulo_ = subtitulo;
      return;
    }
  });
  return subtitulo_;
}
function validateFormSubtitulo() {
  if (
    limpiar_campo(document.querySelector('#txtNombreSubtitulo').value) == ''
  ) {
    showAlertTopEnd('warning', 'Por favor ingrese nombre');
    document.querySelector('#txtNombrenSubtitulo').focus();
    return false;
  }
  return true;
}
