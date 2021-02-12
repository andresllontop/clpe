var beanPaginationnotificacion;
var notificacionSelected;
var beanRequestNotificacion = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestNotificacion.entity_api = 'notificacion';
    beanRequestNotificacion.operation = 'obtener';
    beanRequestNotificacion.type_request = 'GET';



});

function processAjaxNotificacion() {


    let parameters_pagination = '';
    $.ajax({
        url: getHostAPI() + beanRequestNotificacion.entity_api + "/" + beanRequestNotificacion.operation +
            parameters_pagination,
        type: beanRequestNotificacion.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: null,
        cache: false,
        contentType: ('application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',
    }).done(function (beanCrudResponse) {
        $('#modalCargandoNotificacion').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
            } else {
                showAlertTopEnd("info", "VERIFICACIÓN!", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {
            beanPaginationNotificacion = beanCrudResponse.beanPagination;
            addNotificacion(beanPaginationNotificacion);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoNotificacion').modal("hide");
        showAlertErrorRequest();

    });

}

function addNotificacion(beanPagination) {
    document.querySelector('#notificacionMensaje').innerHTML = '';
    if (beanPagination.list.length == 0) {
        document.getElementsByTagName("svg")[0].innerHTML = "";
        return;
    }
    let row = "", contador = 1;
    beanPagination.list.forEach((notificacion) => {
        contador++;
        row += `<div class="cuadrado-2 p-1 text-center ${contador % 2 == 0 ? "wufoo-letter-1" : "wufoo-letter-2"} my-2" >
        <button type="button" class="close close-notifi">
        <span>&times;</span>
    </button>
        <p>${notificacion.tipo == 1 ? notificacion.descripcion : notificacion.rangoInicial + ", conferencia online el día " + notificacion.fecha.split(" ")[0].split("-")[2] + "-" + notificacion.fecha.split(" ")[0].split("-")[1] + "-" + notificacion.fecha.split(" ")[0].split("-")[0] + " hora " + notificacion.fecha.split(" ")[1].split(":")[0] + ":" + notificacion.fecha.split(" ")[1].split(":")[1] + ', para acceder ingresa al siguiente link : <a href="' + notificacion.rangoFinal + '" target="_blank"  ><i class="zmdi zmdi-videocam mx-1"></i>' + notificacion.rangoFinal + '</a>'}</p>
    </div>` ;

    });
    document.querySelector('#notificacionMensaje').innerHTML = row;

    document.querySelectorAll(".close-notifi").forEach((btn) => {
        btn.onclick = function () {
            console.log(btn);
            console.log(btn.tagName);
            if (btn.tagName == "BUTTON") {
                btn.parentElement.parentElement.innerHTML = "";

            } else {
                btn.parentElement.parentElement.parentElement.innerHTML = "";
            }
            if (document.querySelector("#notificacionMensaje").innerHTML == "") {
                document.getElementsByTagName("svg")[0].innerHTML = "";
            }
        };

    });

}

