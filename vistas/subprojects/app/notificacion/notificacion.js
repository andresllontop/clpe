var beanPaginationNotificacion;
var notificacionSelected;
var beanRequestNotificacion = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestNotificacion.entity_api = 'notificacion';
    beanRequestNotificacion.operation = 'paginate';
    beanRequestNotificacion.type_request = 'GET';

    $('#sizePageNotificacion').change(function () {
        beanRequestNotificacion.type_request = 'GET';
        beanRequestNotificacion.operation = 'paginate';
        $('#modalCargandoNotificacion').modal('show');
    });

    $('#modalCargandoNotificacion').modal('show');

    $("#modalCargandoNotificacion").on('shown.bs.modal', function () {
        processAjaxNotificacion();
    });

    $("#ventanaModalManNotificacion").on('hide.bs.modal', function () {
        beanRequestNotificacion.type_request = 'GET';
        beanRequestNotificacion.operation = 'paginate';
    });

    $("#txtDescripcionNotificacion").Editor();
    // $("#txtResumenNotificacion").Editor();

    $("#txtTipoArchivoNotificacion").change(function () {
        tipo($(this).val());
    });

    $("#btnAbrirbook").click(function () {
        beanRequestNotificacion.operation = 'add';
        beanRequestNotificacion.type_request = 'POST';
        $("#imagePreview").html("");
        $("#tituloModalManNotificacion").html("REGISTRAR NOTIFICACIÓN");
        addNotificacion();
        $("#ventanaModalManNotificacion").modal("show");


    });
    $("#formularioNotificacion").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (validateFormNotificacion()) {
            $('#modalCargandoNotificacion').modal('show');
        }
    });

});

function processAjaxNotificacion() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestNotificacion.operation == 'update' ||
        beanRequestNotificacion.operation == 'add'
    ) {

        json = {
            rango_inicial: document.querySelector("#textRangoInicialNotificacion").value,
            rango_final: document.querySelector("#textRangoFinalNotificacion").value,
            descripcion: $("#txtDescripcionNotificacion").Editor("getText"),
            tipo: 1

        };


    } else {
        form_data = null;
    }

    switch (beanRequestNotificacion.operation) {
        case 'delete':
            parameters_pagination = '?id=' + notificacionSelected.idnotificacion;
            break;

        case 'update':
            json.idnotificacion = notificacionSelected.idnotificacion;
            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':


            form_data.append("class", JSON.stringify(json));
            break;

        default:

            parameters_pagination +=
                '?filtro=';
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pageNotificacion").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePageNotificacion").value.trim();
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestNotificacion.entity_api + "/" + beanRequestNotificacion.operation +
            parameters_pagination,
        type: beanRequestNotificacion.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },

        data: form_data,
        cache: false,
        contentType: ((beanRequestNotificacion.operation == 'update' || beanRequestNotificacion.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',
    }).done(function (beanCrudResponse) {

        $('#modalCargandoNotificacion').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");


                if (beanRequestNotificacion.operation == 'update') {
                    notificacionSelected.rangoInicial = json.rango_inicial;
                    notificacionSelected.rangoFinal = json.rango_final;
                    notificacionSelected.descripcion = json.descripcion;
                    updatelistNotificacion(notificacionSelected);
                    listaNotificacion(beanPaginationNotificacion);
                    $('#ventanaModalManNotificacion').modal('hide');
                } else if (beanRequestNotificacion.operation == 'delete') {

                    eliminarlistNotificacion(notificacionSelected.idnotificacion);
                    listaNotificacion(beanPaginationNotificacion);
                    beanRequestNotificacion.operation = 'paginate';
                    beanRequestNotificacion.type_request = 'GET';
                } else {
                    document.querySelector("#pageNotificacion").value = 1;
                    document.querySelector("#sizePageNotificacion").value = 20;
                    $('#ventanaModalManNotificacion').modal('hide');
                }

            } else {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationNotificacion = beanCrudResponse.beanPagination;
            listaNotificacion(beanPaginationNotificacion);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoNotificacion').modal("hide");
        showAlertErrorRequest();

    });

}

function addNotificacion(notificacion = undefined) {
    //LIMPIAR LOS CAMPOS


    document.querySelector('#textRangoInicialNotificacion').value = (notificacion == undefined) ? '' : notificacion.rangoInicial;
    document.querySelector('#textRangoFinalNotificacion').value = (notificacion == undefined) ? '' : notificacion.rangoFinal;


    $("#txtDescripcionNotificacion").Editor("setText", (notificacion == undefined) ? '<p style="color:black"></p>' : notificacion.descripcion);
    $("#txtDescripcionNotificacion").Editor("getText");

    //$("#txtResumenNotificacion").Editor("setText", (notificacion == undefined) ? '<p /style="color:black"></p>' : notificacion.resumen);
    // $("#txtResumenNotificacion").Editor("getText");



}

function listaNotificacion(beanPagination) {
    document.querySelector('#tbodyNotificacion').innerHTML = '';
    document.querySelector('#titleManagerNotificacion').innerHTML =
        '[ ' + beanPagination.countFilter + ' ] MENSAJES DE NOTIFICACIONES';
    let row = "";

    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationNotificacion'));
        row += `<tr>
        <td class="text-center" colspan="6">NO HAY MENSAJES DE NOTIFICACIÓN</td>
        </tr>`;

        document.querySelector('#tbodyNotificacion').innerHTML += row;
        return;
    }

    document.querySelector('#tbodyNotificacion').innerHTML += row;
    beanPagination.list.forEach((notificacion) => {

        row += `<tr  idnotificacion="${notificacion.idnotificacion}">
<td class="text-center">${notificacion.descripcion}</td>
<td class="text-center">${notificacion.rangoInicial}</td>
<td class="text-center">${notificacion.rangoFinal}</td>
<td class="text-center">
<button class="btn btn-info editar-notificacion" ><i class="zmdi zmdi-refresh"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-notificacion"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;

        // $('[data-toggle="tooltip"]').tooltip();
    });
    document.querySelector('#tbodyNotificacion').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePageNotificacion").value),
        document.querySelector("#pageNotificacion"),
        $('#modalCargandoNotificacion'),
        $('#paginationNotificacion'));
    addEventsButtonsNotificacion();


}

function addEventsButtonsNotificacion() {


    document.querySelectorAll('.editar-notificacion').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            notificacionSelected = findByNotificacion(
                btn.parentElement.parentElement.getAttribute('idnotificacion')
            );

            if (notificacionSelected != undefined) {
                addNotificacion(notificacionSelected);
                $("#tituloModalManNotificacion").html("EDITAR NOTIFICACIÓN");
                $("#ventanaModalManNotificacion").modal("show");
                beanRequestNotificacion.type_request = 'POST';
                beanRequestNotificacion.operation = 'update';
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
    document.querySelectorAll('.eliminar-notificacion').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            notificacionSelected = findByNotificacion(
                btn.parentElement.parentElement.getAttribute('idnotificacion')
            );

            if (notificacionSelected != undefined) {
                beanRequestNotificacion.type_request = 'GET';
                beanRequestNotificacion.operation = 'delete';
                $('#modalCargandoNotificacion').modal('show');
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
}





function findIndexNotificacion(idbusqueda) {
    return beanPaginationNotificacion.list.findIndex(
        (Notificacion) => {
            if (Notificacion.idnotificacion == parseInt(idbusqueda))
                return Notificacion;


        }
    );
}

function findByNotificacion(idnotificacion) {
    return beanPaginationNotificacion.list.find(
        (Notificacion) => {
            if (parseInt(idnotificacion) == Notificacion.idnotificacion) {
                return Notificacion;
            }


        }
    );
}
var validateFormNotificacion = () => {
    if (document.querySelector("#textRangoInicialNotificacion").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Rango Inicial",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#textRangoFinalNotificacion").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Rango Final",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if ($("#txtDescripcionNotificacion").Editor("getText") == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Mensaje",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }


    return true;
}
function eliminarlistNotificacion(idbusqueda) {
    beanPaginationNotificacion.count_filter--;
    beanPaginationNotificacion.list.splice(findIndexNotificacion(parseInt(idbusqueda)), 1);
}
function updatelistNotificacion(classBean) {
    beanPaginationNotificacion.list.splice(findIndexNotificacion(classBean.idnotificacion), 1, classBean);
}
