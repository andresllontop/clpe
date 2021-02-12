var beanPaginationDetalle;
var subtituloSelected;
var beanRequestDetalle = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestDetalle.entity_api = 'detalles/test';
    beanRequestDetalle.operation = 'paginate';
    beanRequestDetalle.type_request = 'GET';

    $('#sizePageDetalle').change(function () {
        beanRequestDetalle.type_request = 'GET';
        beanRequestDetalle.operation = 'paginate';
        $('#modalCargandoDetalle').modal('show');
    });


    $("#modalCargandoDetalle").on('shown.bs.modal', function () {
        processAjaxDetalle();
    });
    $("#ventanaModalDetalle").on('hide.bs.modal', function () {
        beanRequestDetalle.type_request = 'GET';
        beanRequestDetalle.operation = 'paginate';
    });

    $("#btnAbrirDetalle").click(function () {
        beanRequestDetalle.operation = 'add';
        beanRequestDetalle.type_request = 'POST';
        $("#tituloModalManDetalle").html('REGISTRAR PREGUNTAS <p>' + testSelected.titulo.nombre + '</p>');
        addDetalle();
        $("#ventanaModalDetalle").modal("show");


    });
    $("#formularioDetalle").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (validarDormularioVideo()) {
            $('#modalCargandoDetalle').modal('show');
        }
    });

});

function processAjaxDetalle() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestDetalle.operation == 'update' ||
        beanRequestDetalle.operation == 'add'
    ) {

        json = {
            descripcion: document.querySelector("#txtNombreDetalle").value,
            subtitulo: subtituloSelected.codigo,
            test: testSelected.idtest
        };


    } else {
        form_data = null;
    }

    switch (beanRequestDetalle.operation) {
        case 'delete':
            parameters_pagination = '?id=' + detalleSelected.iddetalletest;
            break;

        case 'update':
            json.iddetalletest = detalleSelected.iddetalletest;
            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':

            form_data.append("class", JSON.stringify(json));
            break;

        default:

            parameters_pagination +=
                '?filtro=' + testSelected.idtest;
            parameters_pagination +=
                '&pagina=1';
            parameters_pagination +=
                '&registros=100';
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestDetalle.entity_api + "/" + beanRequestDetalle.operation +
            parameters_pagination,
        type: beanRequestDetalle.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestDetalle.operation == 'update' || beanRequestDetalle.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json'
    }).done(function (beanCrudResponse) {

        $('#modalCargandoDetalle').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                $('#ventanaModalDetalle').modal('hide');
            } else {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationDetalle = beanCrudResponse.beanPagination;
            DetalleLista(beanPaginationDetalle);

        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoDetalle').modal("hide");
        showAlertErrorRequest();

    });

}

function DetalleLista(beanPagination) {
    if (beanPaginationSubtituloC == undefined) {
        $('#modalCargandoSubtituloC').modal('show');

    }
    beanPagination.list.forEach((detalle) => {
        listDetalleTest.push(new Detalle_Test(
            detalle.iddetalletest,
            detalle.descripcion,
            detalle.subtitulo.codigo,
            capituloSelected.codigo)

        );
    });

    toListTestDetalle(listDetalleTest);


}

