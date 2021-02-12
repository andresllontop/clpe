var beanPaginationMensaje;
var mensajeSelected;
var capituloSelected;
var beanRequestMensaje = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestMensaje.entity_api = 'mensajes';
    beanRequestMensaje.operation = 'home';
    beanRequestMensaje.type_request = 'GET';

    $('#sizePageMensaje').change(function () {
        beanRequestMensaje.type_request = 'GET';
        beanRequestMensaje.operation = 'home';
        $('#modalCargandoMensaje').modal('show');
    });

    $('#modalCargandoMensaje').modal('show');

    $("#modalCargandoMensaje").on('shown.bs.modal', function () {
        processAjaxMensaje();
    });
    $("#ventanaModalManMensaje").on('hide.bs.modal', function () {
        beanRequestMensaje.type_request = 'GET';
        beanRequestMensaje.operation = 'home';
    });

});

function processAjaxMensaje() {
    let parameters_pagination = '';
    switch (beanRequestMensaje.operation) {
        default:

            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestMensaje.entity_api + "/" + beanRequestMensaje.operation +
            parameters_pagination,
        type: beanRequestMensaje.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: null,
        cache: false,
        contentType: 'application/json; charset=UTF-8',
        processData: false,
        dataType: 'json'
    }).done(function (beanCrudResponse) {

        $('#modalCargandoMensaje').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
            } else {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationMensaje = beanCrudResponse.beanPagination;
            listaMensaje(beanPaginationMensaje);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoMensaje').modal("hide");
        showAlertErrorRequest();

    });

}


function listaMensaje(beanPagination) {
    beanPagination.list.forEach((mensaje) => {
        if (mensaje.countMensaje) {
            document.querySelector('#countMensaje').innerHTML = mensaje.countMensaje;
        } else if (mensaje.countAlumno) {
            document.querySelector('#countAlumno').innerHTML = mensaje.countAlumno;
        } else if (mensaje.countAlumnoActivo) {
            document.querySelector('#countAlumnoActivo').innerHTML = mensaje.countAlumnoActivo;
        } else {
            document.querySelector('#countTarea').innerHTML = mensaje.countTarea;
        }


    });

}

