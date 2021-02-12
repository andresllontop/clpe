var beanPaginationDetalle;
var detalleSelected;
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
                '&pagina=' + document.querySelector("#pageDetalle").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePageDetalle").value.trim();
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
                document.querySelector("#pageDetalle").value = 1;
                document.querySelector("#sizePageDetalle").value = 5;
                $('#ventanaModalDetalle').modal('hide');
            } else {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationDetalle = beanCrudResponse.beanPagination;
            listaDetalle(beanPaginationDetalle);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoDetalle').modal("hide");
        showAlertErrorRequest();

    });

}

function addDetalle(detalle = undefined) {
    //LIMPIAR LOS CAMPOS
    document.querySelector('#txtNombreDetalle').value = (detalle == undefined) ? '' : detalle.descripcion;
    subtituloSelected = (detalle == undefined) ? undefined : detalle.subtitulo;
    document.querySelector('#txtSubTituloRecurso').value = (detalle == undefined) ? '' : detalle.subtitulo.codigo + " - " + detalle.subtitulo.nombre;

}

function listaDetalle(beanPagination) {
    document.querySelector('#tbodyDetalle').innerHTML = '';
    document.querySelector('#titleManagerDetalle').innerHTML =
        '[ ' + beanPagination.countFilter + ' ] ' + testSelected.titulo.codigo;
    let row = "";
    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationDetalle'));
        return;
    }
    beanPagination.list.forEach((detalle) => {

        row += `<tr iddetalletest="${detalle.iddetalletest}">
<td class="text-center">${detalle.subtitulo.nombre} <p>${detalle.subtitulo.codigo}</p></td>
<td class="text-center">${detalle.descripcion}</td>
<td class="text-center">
<button class="btn btn-info editar-detalle" ><i class="zmdi zmdi-refresh"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-detalle"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;

        // $('[data-toggle="tooltip"]').tooltip();
    });


    document.querySelector('#tbodyDetalle').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePageDetalle").value),
        document.querySelector("#pageDetalle"),
        $('#modalCargandoDetalle'),
        $('#paginationDetalle'));
    addEventsButtonsDetalle();


}

function addEventsButtonsDetalle() {
    document.querySelectorAll('.editar-detalle').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            detalleSelected = findByDetalle(
                btn.parentElement.parentElement.getAttribute('iddetalletest')
            );
            if (detalleSelected != undefined) {
                addDetalle(detalleSelected);
                $("#tituloModalManDetalle").html('EDITAR PREGUNTAS <p>' + testSelected.titulo.nombre + '</p>');
                $("#ventanaModalDetalle").modal("show");
                beanRequestDetalle.type_request = 'POST';
                beanRequestDetalle.operation = 'update';
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
    document.querySelectorAll('.eliminar-detalle').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            detalleSelected = findByDetalle(
                btn.parentElement.parentElement.getAttribute('iddetalletest')
            );

            if (detalleSelected != undefined) {
                beanRequestDetalle.type_request = 'GET';
                beanRequestDetalle.operation = 'delete';
                $('#modalCargandoDetalle').modal('show');
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
        document.querySelector('#modalFrameContenidoDetalle').appendChild(iframe);
    }
    iframe.src = url;
};

function findIndexDetalle(idbusqueda) {
    return beanPaginationDetalle.list.findIndex(
        (Detalle) => {
            if (Detalle.iddetalletest == parseInt(idbusqueda))
                return Detalle;


        }
    );
}

function findByDetalle(iddetalletest) {
    return beanPaginationDetalle.list.find(
        (Detalle) => {
            if (parseInt(iddetalletest) == Detalle.iddetalletest) {
                return Detalle;
            }


        }
    );
}

var validarDormularioVideo = () => {
    if (document.querySelector("#txtNombreDetalle").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Nombre",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (subtituloSelected == undefined) {
        swal({
            title: "Vacío",
            text: "Selecciona Capítulo",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }




    return true;
}