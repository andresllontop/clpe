var beanPaginationCurso_c;
var curso_cSelected;
var beanRequestCurso_c = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestCurso_c.entity_api = 'cursos';
    beanRequestCurso_c.operation = 'obtener';
    beanRequestCurso_c.type_request = 'GET';

    $('#sizePageCurso_c').change(function () {
        beanRequestCurso_c.type_request = 'GET';
        beanRequestCurso_c.operation = 'obtener';
        $('#modalCargandoCurso_c').modal('show');
    });

    $('#modalCargandoCurso_c').modal('show');



    $("#modalCargandoCurso_c").on('shown.bs.modal', function () {
        processAjaxCurso_c();
    });

    $("#ventanaModalManCurso_c").on('hide.bs.modal', function () {
        beanRequestCurso_c.type_request = 'GET';
        beanRequestCurso_c.operation = 'obtener';
    });


});

function processAjaxCurso_c() {
    let parameters_pagination = '';
    switch (beanRequestCurso_c.operation) {
        case 'delete':
            parameters_pagination = '?id=' + curso_cSelected.idcurso;
            break;
        default:

            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestCurso_c.entity_api + "/" + beanRequestCurso_c.operation +
            parameters_pagination,
        type: beanRequestCurso_c.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },

        data: null,
        cache: false,
        contentType: 'application/json; charset=UTF-8',
        processData: false,
        dataType: 'json',
    }).done(function (beanCrudResponse) {

        $('#modalCargandoCurso_c').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
            } else {
                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationCurso_c = beanCrudResponse.beanPagination;
            listaCurso_c(beanPaginationCurso_c);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoCurso_c').modal("hide");
        showAlertErrorRequest();

    });

}

function listaCurso_c(beanPagination) {
    document.querySelector('#tbodyCurso_c').innerHTML = '';
    document.querySelector('#titleManagerCurso_c').innerHTML =
        '[ ' + beanPagination.countFilter + ' ] CURSOS';
    let row = "";

    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationCurso_c'));
        row += `<tr>
        <td class="text-center" colspan="6">NO HAY CURSOS</td>
        </tr>`;

        document.querySelector('#tbodyCurso_c').innerHTML += row;
        return;
    }

    document.querySelector('#tbodyCurso_c').innerHTML += row;
    beanPagination.list.forEach((curso_c) => {

        row += `<tr  idcurso="${curso_c.idcurso}">
<td class="text-center">${curso_c.titulo}</td>
<td class="text-center d-none">${curso_c.descripcion}</td>
<td class="text-center d-none">${curso_c.tipo == 1 ? "CURSO ONLINE" : "CURSO POR ZOOM"}</td>
<td class="text-center d-none">${curso_c.precio}</td>
<td class="text-center d-none">${curso_c.descuento}</td>
<td  class="text-center"><img  
  src="${getHostFrontEnd()}adjuntos/libros/${curso_c.imagen}"
  alt="${curso_c.imagen}"
  class="img-responsive center-box"style="width:65px;height:75px;"
  /></td>
  <td  class="text-center d-none"><img  
  src="${getHostFrontEnd()}adjuntos/libros/${curso_c.portada}"
  alt="${curso_c.portada}"
  class="img-responsive center-box"style="width:100px;height:60px;"
  /></td>
<td class="text-center">
<button class="btn btn-info beneficio-curso" ><i class="zmdi zmdi-refresh"></i> </button>
</td>

</tr>`;

        // $('[data-toggle="tooltip"]').tooltip();
    });
    document.querySelector('#tbodyCurso_c').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePageCurso_c").value),
        document.querySelector("#pageCurso_c"),
        $('#modalCargandoCurso_c'),
        $('#paginationCurso_c'));
    addEventsButtonsCurso_c();


}


function findIndexCurso_c(idbusqueda) {
    return beanPaginationCurso_c.list.findIndex(
        (Curso_c) => {
            if (Curso_c.idcurso == parseInt(idbusqueda))
                return Curso_c;


        }
    );
}

function findByCurso_c(idcurso) {
    return beanPaginationCurso_c.list.find(
        (Curso_c) => {
            if (parseInt(idcurso) == Curso_c.idcurso) {
                return Curso_c;
            }


        }
    );
}
