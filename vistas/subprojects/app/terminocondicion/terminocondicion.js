var beanPaginationTerminocondicion;
var terminocondicionSelected;
var beanRequestTerminocondicion = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestTerminocondicion.entity_api = 'empresa';
    beanRequestTerminocondicion.operation = 'obtener';
    beanRequestTerminocondicion.type_request = 'GET';

    $('#modalCargandoTerminocondicion').modal('show');

    $("#modalCargandoTerminocondicion").on('shown.bs.modal', function () {
        processAjaxTerminocondicion();
    });

    $("#formularioTerminocondicion").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();
        beanRequestTerminocondicion.type_request = 'POST';
        beanRequestTerminocondicion.operation = 'updateterminocondicion';

        $('#modalCargandoTerminocondicion').modal('show');


    });
    $("#txtDescripcionTerminocondicion").Editor();

});

function processAjaxTerminocondicion() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestTerminocondicion.operation == 'updateterminocondicion'
    ) {

        json = {
            idempresa: (terminocondicionSelected == undefined ? 0 : terminocondicionSelected.idempresa),
            terminoCondicion: (terminocondicionSelected == undefined ? "" : $("#txtDescripcionTerminocondicion").Editor("getText")),

        };
        form_data.append("class", JSON.stringify(json));

    } else {
        form_data = null;
    }

    switch (beanRequestTerminocondicion.operation) {
        case 'updateterminocondicion':

            break;

        default:

            parameters_pagination +=
                '?filtro=';
            parameters_pagination +=
                '&pagina=1&registros=1';
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestTerminocondicion.entity_api + "/" + beanRequestTerminocondicion.operation +
            parameters_pagination,
        type: beanRequestTerminocondicion.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },

        data: form_data,
        cache: false,
        contentType: ((beanRequestTerminocondicion.operation == 'updateterminocondicion') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',
    }).done(function (beanCrudResponse) {

        $('#modalCargandoTerminocondicion').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");

                $('#ventanaModalManTerminocondicion').modal('hide');
            } else {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationTerminocondicion = beanCrudResponse.beanPagination;
            listaTerminocondicion(beanPaginationTerminocondicion);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoTerminocondicion').modal("hide");
        showAlertErrorRequest();

    });

}

function listaTerminocondicion(beanPagination) {
    let row = "";
    $("#txtDescripcionTerminocondicion").Editor("setText", row);
    $("#txtDescripcionTerminocondicion").Editor("getText");
    beanPagination.list.forEach((empresa) => {
        terminocondicionSelected = empresa;
        row += empresa.terminoCondicion;
    });
    $("#txtDescripcionTerminocondicion").Editor("setText", row);
    $("#txtDescripcionTerminocondicion").Editor("getText");

}



