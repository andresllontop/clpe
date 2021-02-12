var beanPaginationConferencia;
var conferenciaSelected;
var beanRequestConferencia = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestConferencia.entity_api = 'conferencia';
    beanRequestConferencia.operation = 'paginate';
    beanRequestConferencia.type_request = 'GET';

    $('#sizePageConferencia').change(function () {
        beanRequestConferencia.type_request = 'GET';
        beanRequestConferencia.operation = 'paginate';
        $('#modalCargandoConferencia').modal('show');
    });

    $('#modalCargandoConferencia').modal('show');

    $("#modalCargandoConferencia").on('shown.bs.modal', function () {
        processAjaxConferencia();
    });

    $("#ventanaModalManConferencia").on('hide.bs.modal', function () {
        beanRequestConferencia.type_request = 'GET';
        beanRequestConferencia.operation = 'paginate';
    });

    $("#txtDescripcionConferencia").Editor();
    // $("#txtResumenConferencia").Editor();

    $("#txtTipoArchivoConferencia").change(function () {
        tipo($(this).val());
    });

    $("#btnAbrirbook").click(function () {
        beanRequestConferencia.operation = 'add';
        beanRequestConferencia.type_request = 'POST';
        $("#imagePreview").html("");
        $("#tituloModalManConferencia").html("REGISTRAR CONFERENCIA");
        addConferencia();
        $("#ventanaModalManConferencia").modal("show");


    });
    $("#formularioConferencia").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (validateFormConferencia()) {
            $('#modalCargandoConferencia').modal('show');
        }
    });

});

function processAjaxConferencia() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestConferencia.operation == 'update' ||
        beanRequestConferencia.operation == 'add'
    ) {

        json = {
            link: document.querySelector("#txtLinkConferencia").value,
            titulo: document.querySelector("#txtTituloConferencia").value,
            fecha: document.querySelector("#txtFechaConferencia").value,
            descripcion: $("#txtDescripcionConferencia").Editor("getText"),
            estado: 1

        };


    } else {
        form_data = null;
    }

    switch (beanRequestConferencia.operation) {
        case 'delete':
            parameters_pagination = '?id=' + conferenciaSelected.idconferencia;
            break;

        case 'update':
            json.idconferencia = conferenciaSelected.idconferencia;
            if (document.querySelector("#txtImagenConferencia").files.length !== 0) {
                let dataFoto = $("#txtImagenConferencia").prop("files")[0];
                form_data.append("txtImagenConferencia", dataFoto);
            }
            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':
            let data = $("#txtImagenConferencia").prop("files")[0];
            form_data.append("txtImagenConferencia", data);

            form_data.append("class", JSON.stringify(json));
            break;

        default:

            parameters_pagination +=
                '?filtro=';
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pageConferencia").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePageConferencia").value.trim();
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestConferencia.entity_api + "/" + beanRequestConferencia.operation +
            parameters_pagination,
        type: beanRequestConferencia.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },

        data: form_data,
        cache: false,
        contentType: ((beanRequestConferencia.operation == 'update' || beanRequestConferencia.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',
    }).done(function (beanCrudResponse) {

        $('#modalCargandoConferencia').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");


                if (beanRequestConferencia.operation == 'update') {
                    conferenciaSelected.descripcion = json.descripcion;
                    conferenciaSelected.estado = json.estado;
                    conferenciaSelected.fecha = json.fecha;
                    conferenciaSelected.link = json.link;
                    conferenciaSelected.titulo = json.titulo;
                    updatelistConferencia(conferenciaSelected);
                    listaConferencia(beanPaginationConferencia);
                    $('#ventanaModalManConferencia').modal('hide');
                } else if (beanRequestConferencia.operation == 'delete') {

                    eliminarlistConferencia(conferenciaSelected.idconferencia);
                    listaConferencia(beanPaginationConferencia);
                    beanRequestConferencia.operation = 'paginate';
                    beanRequestConferencia.type_request = 'GET';
                } else {
                    document.querySelector("#pageConferencia").value = 1;
                    document.querySelector("#sizePageConferencia").value = 20;
                    $('#ventanaModalManConferencia').modal('hide');
                }

            } else {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationConferencia = beanCrudResponse.beanPagination;
            listaConferencia(beanPaginationConferencia);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoConferencia').modal("hide");
        showAlertErrorRequest();

    });

}

function addConferencia(conferencia = undefined) {
    //LIMPIAR LOS CAMPOS


    document.querySelector('#txtLinkConferencia').value = (conferencia == undefined) ? '' : conferencia.link;
    document.querySelector('#txtTituloConferencia').value = (conferencia == undefined) ? '' : conferencia.titulo;
    document.querySelector('#txtFechaConferencia').value = (conferencia == undefined) ? '' : (conferencia.fecha.split(" ")[0]) + "T" + (conferencia.fecha.split(" ")[1]);


    $("#txtDescripcionConferencia").Editor("setText", (conferencia == undefined) ? '<p style="color:black"></p>' : conferencia.descripcion);
    $("#txtDescripcionConferencia").Editor("getText");

    if (conferencia != undefined) {

        document.querySelector("#imagePreview").innerHTML = `<img width='244' alt='user-picture' class='img-responsive text-center' src='${getHostFrontEnd() + 'adjuntos/conferencia/' + conferencia.imagen}' />`;

    } else {

        document.querySelector("#imagePreview").innerHTML = ``;
    }
    addViewArchivosPrevius();


}

function listaConferencia(beanPagination) {
    document.querySelector('#tbodyConferencia').innerHTML = '';
    document.querySelector('#titleManagerConferencia').innerHTML =
        '[ ' + beanPagination.countFilter + ' ] CONFERENCIAS';
    let row = "";

    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationConferencia'));
        row += `<tr>
        <td class="text-center" colspan="8">NO HAY  CONFERENCIAS</td>
        </tr>`;

        document.querySelector('#tbodyConferencia').innerHTML += row;
        return;
    }

    document.querySelector('#tbodyConferencia').innerHTML += row;
    let fecha1 = new Date();
    let fecha2 = new Date();


    beanPagination.list.forEach((conferencia) => {
        fecha2 = new Date(conferencia.fecha);
        row += `<tr idconferencia="${conferencia.idconferencia}">
<td class="text-center">${conferencia.titulo}</td>
<td class="text-center">${conferencia.link}</td>
<td class="text-center"><img  
src="${getHostFrontEnd()}adjuntos/conferencia/${conferencia.imagen}"
alt="${conferencia.imagen}"
class="img-responsive center-box"style="width:100px;height:60px;"
/></td>
<td class="text-center">${conferencia.descripcion}</td>
<td class="text-center">${conferencia.fecha}</td>
<td class="text-center">${((Math.round((fecha2.getTime() - fecha1.getTime()) / (1000 * 60 * 60))) < 24 && (Math.round((fecha2.getTime() - fecha1.getTime()) / (1000 * 60 * 60))) >= 0) ? "Faltan " + (Math.round((fecha2.getTime() - fecha1.getTime()) / (1000 * 60 * 60))) + " horas" : (Math.round((fecha2.getTime() - fecha1.getTime()) / (1000 * 60 * 60))) == 0 ? "En estos momentos" : (Math.round((fecha2.getTime() - fecha1.getTime()) / (1000 * 60 * 60))) < 0 ? "Realizado" : "Faltan " + Math.round((fecha2.getTime() - fecha1.getTime()) / (1000 * 60 * 60 * 24)) + " días"}</td>
<td class="text-center">
<button class="btn btn-info editar-conferencia" ><i class="zmdi zmdi-refresh"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-conferencia"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;

    });
    document.querySelector('#tbodyConferencia').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePageConferencia").value),
        document.querySelector("#pageConferencia"),
        $('#modalCargandoConferencia'),
        $('#paginationConferencia'));
    addEventsButtonsConferencia();


}

function addEventsButtonsConferencia() {


    document.querySelectorAll('.editar-conferencia').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            conferenciaSelected = findByConferencia(
                btn.parentElement.parentElement.getAttribute('idconferencia')
            );

            if (conferenciaSelected != undefined) {
                addConferencia(conferenciaSelected);
                $("#tituloModalManConferencia").html("EDITAR CONFERENCIA");
                $("#ventanaModalManConferencia").modal("show");
                beanRequestConferencia.type_request = 'POST';
                beanRequestConferencia.operation = 'update';
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
    document.querySelectorAll('.eliminar-conferencia').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            conferenciaSelected = findByConferencia(
                btn.parentElement.parentElement.getAttribute('idconferencia')
            );

            if (conferenciaSelected != undefined) {
                beanRequestConferencia.type_request = 'GET';
                beanRequestConferencia.operation = 'delete';
                $('#modalCargandoConferencia').modal('show');
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
}

function addViewArchivosPrevius() {

    $("#txtImagenConferencia").change(function () {
        filePreview(this, "#imagePreview");
    });

}

function filePreview(input, imagen) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        imagen;
        reader.onload = function (e) {
            $(imagen).html(
                "<img width='244' alt='user-picture' class='img-responsive center-box' src='" +
                e.target.result +
                "' />"
            );
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function findIndexConferencia(idbusqueda) {
    return beanPaginationConferencia.list.findIndex(
        (Conferencia) => {
            if (Conferencia.idconferencia == parseInt(idbusqueda))
                return Conferencia;


        }
    );
}

function findByConferencia(idconferencia) {
    return beanPaginationConferencia.list.find(
        (Conferencia) => {
            if (parseInt(idconferencia) == Conferencia.idconferencia) {
                return Conferencia;
            }


        }
    );
}

var validateFormConferencia = () => {
    if (document.querySelector("#txtLinkConferencia").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Link",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtTituloConferencia").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Titulo",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtFechaConferencia").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Fecha",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if ($("#txtDescripcionConferencia").Editor("getText") == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Descripción",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (beanRequestConferencia.operation == 'add') {

        if (document.querySelector("#txtImagenConferencia").files.length == 0) {
            swal({
                title: "Vacío",
                text: "Ingrese Imagen",
                type: "warning",
                timer: 1200,
                showConfirmButton: false
            });
            return false;
        }
        if (!(document.querySelector("#txtImagenConferencia").files[0].type == "image/png" || document.querySelector("#txtImagenConferencia").files[0].type == "image/jpg" || document.querySelector("#txtImagenConferencia").files[0].type == "image/jpeg")) {
            swal({
                title: "Formato Incorrecto",
                text: "Ingrese formato png, jpeg y jpg",
                type: "warning",
                timer: 1200,
                showConfirmButton: false
            });
            return false;
        }
        //menor a   4 MB
        if (document.querySelector("#txtImagenConferencia").files[0].size > (4 * 1024 * 1024)) {
            swal({
                title: "Tamaño excedido",
                text: "el tamaño del archivo tiene que ser menor a 900 KB",
                type: "warning",
                timer: 1200,
                showConfirmButton: false
            });
            return false;
        }

    } else {

        if (document.querySelector("#txtImagenConferencia").files.length != 0) {
            if (document.querySelector("#txtImagenConferencia").files.length == 0) {
                swal({
                    title: "Vacío",
                    text: "Ingrese Imagen",
                    type: "warning",
                    timer: 1200,
                    showConfirmButton: false
                });
                return false;
            }
            if (!(document.querySelector("#txtImagenConferencia").files[0].type == "image/png" || document.querySelector("#txtImagenConferencia").files[0].type == "image/jpg" || document.querySelector("#txtImagenConferencia").files[0].type == "image/jpeg")) {
                swal({
                    title: "Formato Incorrecto",
                    text: "Ingrese formato png, jpeg y jpg",
                    type: "warning",
                    timer: 1200,
                    showConfirmButton: false
                });
                return false;
            }
            //menor a   4 mb
            if (document.querySelector("#txtImagenConferencia").files[0].size > (1700 * 1024)) {
                swal({
                    title: "Tamaño excedido",
                    text: "el tamaño del archivo tiene que ser menor a 1700 KB",
                    type: "warning",
                    timer: 1200,
                    showConfirmButton: false
                });
                return false;
            }
        }



    }

    return true;
}

function eliminarlistConferencia(idbusqueda) {
    beanPaginationConferencia.count_filter--;
    beanPaginationConferencia.list.splice(findIndexConferencia(parseInt(idbusqueda)), 1);
}

function updatelistConferencia(classBean) {
    beanPaginationConferencia.list.splice(findIndexConferencia(classBean.idconferencia), 1, classBean);
}
