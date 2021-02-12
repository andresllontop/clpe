var beanPaginationVerificaty;
var verificatySelected;
var token_id = null;
var beanRequestVerificaty = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    let GETsearch = window.location.search;

    token_id = getParameterByName("id", GETsearch);
    if (token_id == null) {
        window.location.href = getHostFrontEnd();
    }
    beanRequestVerificaty.entity_api = 'authentication/verificaty';
    beanRequestVerificaty.operation = 'obtener';
    beanRequestVerificaty.type_request = 'GET';

    $('#modalCargandoVerificaty').modal('show');
    $("#modalCargandoVerificaty").on('shown.bs.modal', function () {
        processAjaxVerificaty();
    });

    $("#formularioVerificaty").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();
        beanRequestVerificaty.operation = 'token';
        beanRequestVerificaty.type_request = 'POST';
        if (validarFormularioVerificaty()) {
            $('#modalCargandoVerificaty').modal('show');
        }
    });

});

function getParameterByName(name, url = window.location.href) {
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

function processAjaxVerificaty() {
    let form_data = new FormData();
    let parameters_pagination = '';
    switch (beanRequestVerificaty.operation) {
        case 'token':
            let json = '';
            json = {
                codigoverificacion: document.querySelector("#codigoVerificaty").value

            };
            form_data.append("class", JSON.stringify(json));
            break;
        default:
            form_data = null;
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestVerificaty.entity_api + "/" + beanRequestVerificaty.operation +
            parameters_pagination,
        type: beanRequestVerificaty.type_request,
        headers: {
            'Authorization': 'Bearer ' + token_id
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestVerificaty.operation == 'token') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',
    }).done(function (jsonResponse) {
        $('#modalCargandoVerificaty').modal('hide');
        if (jsonResponse.messageServer !== null) {
            if (jsonResponse.messageServer.toLowerCase() == 'ok') {
                swal("Logeado Existosamente!", "Espere redireccionando...", "success");
                setCookieSession(jsonResponse.beanPagination.token, jsonResponse.beanPagination.usuario);
                sendIndex();
            } else {
                swal(
                    "Ocurrió un error inesperado",
                    jsonResponse.messageServer,
                    "info"
                );
            }
        }
        if (jsonResponse.beanPagination !== null) {
            if (jsonResponse.beanPagination.countFilter == 0) {
                window.location.href = getHostFrontEnd();
            }
        }

    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoVerificaty').modal("hide");

        swal(
            "Ocurrió un error inesperado 500",
            "",
            "error"
        );

    });

}

var validarFormularioVerificaty = () => {
    let numero = numero_campo(
        document.querySelector('#codigoVerificaty')

    );
    if (numero != undefined) {
        if (numero.value == '') {
            swal("Campo Vacío!", 'Por favor ingrese ' + numero.labels[0].innerText, 'info');
        } else {
            swal(
                "Formato Incorrecto",
                'Por favor ingrese sólo números, ' + numero.labels[0].innerText, 'info'
            );
        }

        return false;
    }



    return true;
}