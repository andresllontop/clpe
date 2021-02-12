var beanPaginationMensaje;
var mensajeSelected;
var beanRequestMensaje = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestMensaje.entity_api = 'mensajes';
    beanRequestMensaje.operation = 'add';
    beanRequestMensaje.type_request = 'POST';
    document.querySelector("#contactAddress").value = user_session.email;
    document.querySelector("#contactName").value = user_session.usuario;
    $("#modalCargandoMensaje").on('shown.bs.modal', function () {
        processAjaxMensaje();
    });

    $("#formularioMensaje").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (validarFormularioMensaje()) {
            $('#modalCargandoMensaje').modal('show');
        }
    });


});

function processAjaxMensaje() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (

        beanRequestMensaje.operation == 'add'
    ) {

        json = {
            nombre: document.querySelector("#contactName").value,
            asunto: document.querySelector("#contactSubject").value,
            email: document.querySelector("#contactAddress").value,
            descripcion: document.querySelector("#contactMessage").value

        };
        form_data.append("class", JSON.stringify(json));

    } else {
        form_data = null;
    }

    switch (beanRequestMensaje.operation) {
        case 'add':

            break;
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
        data: form_data,
        cache: false,
        contentType: ((beanRequestMensaje.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',
    }).done(function (beanCrudResponse) {
        $('#modalCargandoMensaje').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "MENSAJE ENVIADO!", "Máximo en 24 horas se responderá su inquietud.");
            } else {
                showAlertTopEnd("info", "VERIFICACIÓN!", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {
            beanPaginationMensaje = beanCrudResponse.beanPagination;
            addMensaje(beanPaginationMensaje.list[0]);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoMensaje').modal("hide");
        showAlertErrorRequest();

    });

}

var validarFormularioMensaje = () => {
    let numero = letra_numero_campo(
        document.querySelector('#contactName'),
        document.querySelector("#contactSubject"),
        document.querySelector("#contactMessage")

    );
    if (numero != undefined) {
        if (numero.value == '') {
            showAlertTopEnd('info', "Campo Vacío!", 'Por favor ingrese ' + numero.labels[0].innerText);
        } else {
            showAlertTopEnd(
                'info', "Formato Incorrecto",
                'Por favor ingrese sólo números, ' + numero.labels[0].innerText
            );
        }

        return false;
    }

    let email = email_campo(
        document.querySelector('#contactAddress')

    );

    if (email != undefined) {
        if (email.value == '') {
            showAlertTopEnd('info', "Campo Vacío!", 'Por favor ingrese correo electrónico');
        } else {
            showAlertTopEnd(
                'info', "Formato Incorrecto",
                'Por favor ingrese un correo electrónico Válido'
            );
        }

        return false;
    }



    return true;
}