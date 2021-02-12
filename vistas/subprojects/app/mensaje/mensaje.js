var beanPaginationMensaje;
var mensajeSelected;
var capituloSelected;
var beanRequestMensaje = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestMensaje.entity_api = 'mensajes';
    beanRequestMensaje.operation = 'paginate';
    beanRequestMensaje.type_request = 'GET';

    $('#sizePageMensaje').change(function () {
        beanRequestMensaje.type_request = 'GET';
        beanRequestMensaje.operation = 'paginate';
        $('#modalCargandoMensaje').modal('show');
    });

    $('#modalCargandoMensaje').modal('show');

    $("#modalCargandoMensaje").on('shown.bs.modal', function () {
        processAjaxMensaje();
    });
    $("#ventanaModalManMensaje").on('hide.bs.modal', function () {
        beanRequestMensaje.type_request = 'GET';
        beanRequestMensaje.operation = 'paginate';
    });

    $("#formularioMensaje").submit(function (event) {

        if (validarDormularioVideo()) {
            $('#modalCargandoMensaje').modal('show');
        }
        event.preventDefault();
        event.stopPropagation();

    });

});

function processAjaxMensaje() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestMensaje.operation == 'update' ||
        beanRequestMensaje.operation == 'add'
    ) {

        json = {
            estado: 1,
            codigo: mensajeSelected.cuenta.cuenta.cuentaCodigo
        };


    } else {
        form_data = null;
    }

    switch (beanRequestMensaje.operation) {
        case 'delete':
            parameters_pagination = '?id=' + mensajeSelected.idmensaje;
            break;
        case 'update':
            json.idmensaje = mensajeSelected.idmensaje;
            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':

            form_data.append("class", JSON.stringify(json));
            break;

        default:

            parameters_pagination +=
                '?filtro=';
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pageMensaje").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePageMensaje").value.trim();
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
        contentType: ((beanRequestMensaje.operation == 'update' || beanRequestMensaje.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json'
    }).done(function (beanCrudResponse) {

        $('#modalCargandoMensaje').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                document.querySelector("#pageMensaje").value = 1;
                document.querySelector("#sizePageMensaje").value = 5;
                $('#ventanaModalManMensaje').modal('hide');
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

function addMensaje(mensaje = undefined) {
    //LIMPIAR LOS CAMPOS

    document.querySelector('#txtNombreMensaje').value = (mensaje == undefined) ? '' : mensaje.nombre;

    capituloSelected = (mensaje == undefined) ? undefined : mensaje.titulo;
    document.querySelector('#txtCapituloMensaje').value = (mensaje == undefined) ? '' : mensaje.titulo.codigo + " - " + mensaje.titulo.nombre;
    document.querySelector('#tbodyPreguntas').innerHTML = "";
    let row = "";
    for (let index = 1; index <= 10; index++) {
        row += `<label for="txtPregunta${index}Mensaje">Pregunta N° ${index}</label>
         <div class="group-material">
        <textarea class="material-control w-100" data-toggle="tooltip" required="" data-placement="top" title="" data-original-title="Escribe la url de youtube" id="txtPregunta${index}Mensaje" rows="3"></textarea></div>`;
    }

    document.querySelector('#tbodyPreguntas').innerHTML += row;
    if (mensaje != undefined) {
        for (let index = 1; index <= 10; index++) {
            document.querySelector("#txtPregunta" + index + "Mensaje").value = mensaje['pregunta_P' + index];

        }
    }


}

function listaMensaje(beanPagination) {
    document.querySelector('#tbodyMensaje').innerHTML = '';
    document.querySelector('#titleManagerMensaje').innerHTML =
        '[ ' + beanPagination.countFilter + ' ] MENSAJES';
    let row = "";
    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationMensaje'));
        row += `<tr>
        <td class="text-center" colspan="7">NO HAY MENSAJES</td>
        </tr>`;

        document.querySelector('#tbodyMensaje').innerHTML += row;
        return;
    }
    beanPagination.list.forEach((mensaje) => {

        row += `<tr  idmensaje="${mensaje.idmensaje}">
<td class="text-center">${mensaje.cuenta.cuenta.cuentaCodigo}</td>
<td class="text-center">${mensaje.cuenta.apellido + " " + mensaje.cuenta.nombre} </td>
<td class="text-center">${mensaje.cuenta.cuenta.email}</td>
<td class="text-center">${mensaje.titulo}</td>
<td class="text-center">${mensaje.descripcion}</td>
<td class="text-center">
<button class="btn ${mensaje.estado == 1 ? "btn-success" : "btn-default"} editar-mensaje" ><i class="zmdi ${mensaje.estado == 1 ? "zmdi-check-all" : "zmdi-check"}"></i> </button>
</td>
<td class="text-center d-none">
<button class="btn btn-warning ver-mensaje" ><i class="zmdi zmdi-eye"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-mensaje"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;

        // $('[data-toggle="tooltip"]').tooltip();
    });


    document.querySelector('#tbodyMensaje').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePageMensaje").value),
        document.querySelector("#pageMensaje"),
        $('#modalCargandoMensaje'),
        $('#paginationMensaje'));
    addEventsButtonsAdmin();


}

function addEventsButtonsAdmin() {
    document.querySelectorAll('.ver-mensaje').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            mensajeSelected = findByMensaje(
                btn.parentElement.parentElement.getAttribute('idmensaje')
            );
            if (mensajeSelected != undefined) {
                $("#ventanaModalVerPreguntas").modal("show");
                let row = ``;
                document.querySelector('#tbodyVerPreguntas').innerHTML = "";
                document.querySelector('#tituloModalVerPreguntas').innerHTML = mensajeSelected.nombre;
                for (let index = 1; index <= 10; index++) {
                    row += `<h4 class="text-info f-weight-600">Pregunta N° ${index}</h4><p>${mensajeSelected['pregunta_P' + index]}</p><br>`;
                }

                document.querySelector('#tbodyVerPreguntas').innerHTML += row;

            } else {
                console.log(
                    'warning => ',
                    'No se encontró el capitulo para poder ver'
                );
            }
        };
    });

    document.querySelectorAll('.editar-mensaje').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            mensajeSelected = findByMensaje(
                btn.parentElement.parentElement.getAttribute('idmensaje')
            );

            if (mensajeSelected != undefined) {
                //  addMensaje(mensajeSelected);
                // $("#tituloModalManMensaje").html("EDITAR RESTRICCIONES");
                // $("#ventanaModalManMensaje").modal("show");
                beanRequestMensaje.type_request = 'POST';
                beanRequestMensaje.operation = 'update';
                $('#modalCargandoMensaje').modal('show');
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
    document.querySelectorAll('.eliminar-mensaje').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            mensajeSelected = findByMensaje(
                btn.parentElement.parentElement.getAttribute('idmensaje')
            );

            if (mensajeSelected != undefined) {
                beanRequestMensaje.type_request = 'GET';
                beanRequestMensaje.operation = 'delete';
                $('#modalCargandoMensaje').modal('show');
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
}

function downloadURL(url) {
    var hiddenIFrameID = 'hiddenDownloader',
        iframe = document.getElementById(hiddenIFrameID);
    if (iframe === null) {
        iframe = document.createElement('iframe');
        iframe.id = hiddenIFrameID;
        iframe.style.width = '100%';
        iframe.style.height = '100%';
        document.querySelector('#modalFrameContenidoMensaje').appendChild(iframe);
    }
    iframe.src = url;
};

function findIndexMensaje(idbusqueda) {
    return beanPaginationMensaje.list.findIndex(
        (Mensaje) => {
            if (Mensaje.idmensaje == parseInt(idbusqueda))
                return Mensaje;


        }
    );
}

function findByMensaje(idmensaje) {
    return beanPaginationMensaje.list.find(
        (Mensaje) => {
            if (parseInt(idmensaje) == Mensaje.idmensaje) {
                return Mensaje;
            }


        }
    );
}

var validarDormularioVideo = () => {
    if (document.querySelector("#txtNombreMensaje").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Nombre",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (capituloSelected == undefined) {
        swal({
            title: "Vacío",
            text: "Selecciona Capítulo",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    for (let index = 1; index <= 10; index++) {

        if (document.querySelector("#txtPregunta" + index + "Mensaje").value == "") {
            swal({
                title: "Vacío",
                text: "Ingrese datos a la Pregunta N° " + index,
                type: "warning",
                timer: 1200,
                showConfirmButton: false
            });
            return false;
        }
    }



    return true;
}