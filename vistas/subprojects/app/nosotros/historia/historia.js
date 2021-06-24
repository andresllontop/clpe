var beanPaginationHistoria;
var historiaSelected;
var beanRequestHistoria = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestHistoria.entity_api = 'empresa';
    beanRequestHistoria.operation = 'obtener';
    beanRequestHistoria.type_request = 'GET';

    $('#modalCargandoHistoria').modal('show');

    $("#modalCargandoHistoria").on('shown.bs.modal', function () {
        processAjaxHistoria();
    });

    $("#formularioHistoria").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();
        beanRequestHistoria.type_request = 'POST';
        beanRequestHistoria.operation = 'update';
        if (validarDormularioVideo()) {
            $('#modalCargandoHistoria').modal('show');
        }

    });
    $("#txtDescripcionHistoria").Editor();

});

function processAjaxHistoria() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestHistoria.operation == 'update' ||
        beanRequestHistoria.operation == 'add'
    ) {

        json = {
            idempresa: (historiaSelected == undefined ? 0 : historiaSelected.idempresa),
            descripcion: (historiaSelected == undefined ? "" : $("#txtDescripcionHistoria").Editor("getText")),
            direccion: (historiaSelected == undefined ? "" : historiaSelected.direccion),
            email: (historiaSelected == undefined ? "" : historiaSelected.email),
            enlace: (historiaSelected == undefined ? "" : historiaSelected.enlace),
            facebook: (historiaSelected == undefined ? "" : historiaSelected.facebook),
            logo: (historiaSelected == undefined ? "" : historiaSelected.logo),
            mision: (historiaSelected == undefined ? "" : historiaSelected.mision),
            nombre: (historiaSelected == undefined ? "" : historiaSelected.nombre),
            precio: (historiaSelected == undefined ? "" : historiaSelected.precio),
            telefono: (historiaSelected == undefined ? "" : historiaSelected.telefono),
            telefonoSegundo: (historiaSelected == undefined ? "" : historiaSelected.telefonoSegundo),
            vision: (historiaSelected == undefined ? "" : historiaSelected.vision),
            youtube: (historiaSelected == undefined ? "" : historiaSelected.youtube),
            instagram: (historiaSelected == undefined ? "" : historiaSelected.instagram),
            frase: (historiaSelected == undefined ? "" : historiaSelected.frase),
        };
        form_data.append("class", JSON.stringify(json));

    } else {
        form_data = null;
    }

    switch (beanRequestHistoria.operation) {
        case 'update':

            break;

        default:

            parameters_pagination +=
                '?filtro=';
            parameters_pagination +=
                '&pagina=1&registros=1';
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestHistoria.entity_api + "/" + beanRequestHistoria.operation +
            parameters_pagination,
        type: beanRequestHistoria.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestHistoria.operation == 'update' || beanRequestHistoria.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json'
    }).done(function (beanCrudResponse) {

        $('#modalCargandoHistoria').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");

                $('#ventanaModalManHistoria').modal('hide');
            } else {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationHistoria = beanCrudResponse.beanPagination;
            listaHistoria(beanPaginationHistoria);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoHistoria').modal("hide");
        showAlertErrorRequest();

    });

}

function listaHistoria(beanPagination) {
    let row = "";
    $("#txtDescripcionHistoria").Editor("setText", row);
    $("#txtDescripcionHistoria").Editor("getText");
    beanPagination.list.forEach((empresa) => {
        historiaSelected = empresa;
        row += empresa.descripcion;
    });
    $("#txtDescripcionHistoria").Editor("setText", row);
    $("#txtDescripcionHistoria").Editor("getText");

}

function findIndexHistoria(idbusqueda) {
    return beanPaginationHistoria.list.findIndex(
        (Historia) => {
            if (Historia.idvideo == parseInt(idbusqueda))
                return Historia;


        }
    );
}

function findByHistoria(idvideo) {
    return beanPaginationHistoria.list.find(
        (Historia) => {
            if (parseInt(idvideo) == Historia.idvideo) {
                return Historia;
            }


        }
    );
}

var validarDormularioVideo = () => {


    return true;
}
