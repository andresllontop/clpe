var beanPaginationFrase;
var fraseSelected;
var beanRequestFrase = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestFrase.entity_api = 'empresa';
    beanRequestFrase.operation = 'obtener';
    beanRequestFrase.type_request = 'GET';

    $('#modalCargandoFrase').modal('show');

    $("#modalCargandoFrase").on('shown.bs.modal', function () {
        processAjaxFrase();
    });

    $("#formularioFrase").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();
        beanRequestFrase.type_request = 'POST';
        beanRequestFrase.operation = 'updatefrase';

        $('#modalCargandoFrase').modal('show');


    });
    $("#txtDescripcionFrase").Editor();

});

function processAjaxFrase() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestFrase.operation == 'updatefrase'
    ) {

        json = {
            idempresa: (fraseSelected == undefined ? 0 : fraseSelected.idempresa),
            mision: (fraseSelected == undefined ? "" : $("#txtDescripcionFrase").Editor("getText")),

        };
        form_data.append("class", JSON.stringify(json));

    } else {
        form_data = null;
    }

    switch (beanRequestFrase.operation) {
        case 'updatefrase':

            break;

        default:

            parameters_pagination +=
                '?filtro=';
            parameters_pagination +=
                '&pagina=1&registros=1';
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestFrase.entity_api + "/" + beanRequestFrase.operation +
            parameters_pagination,
        type: beanRequestFrase.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },

        data: form_data,
        cache: false,
        contentType: ((beanRequestFrase.operation == 'updatefrase') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',
    }).done(function (beanCrudResponse) {

        $('#modalCargandoFrase').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");

                $('#ventanaModalManFrase').modal('hide');
            } else {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationFrase = beanCrudResponse.beanPagination;
            listaFrase(beanPaginationFrase);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoFrase').modal("hide");
        showAlertErrorRequest();

    });

}

function listaFrase(beanPagination) {
    let row = "";
    $("#txtDescripcionFrase").Editor("setText", row);
    $("#txtDescripcionFrase").Editor("getText");
    beanPagination.list.forEach((empresa) => {
        fraseSelected = empresa;
        row += empresa.mision;
    });
    $("#txtDescripcionFrase").Editor("setText", row);
    $("#txtDescripcionFrase").Editor("getText");

}




