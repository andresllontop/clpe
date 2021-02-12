var beanPaginationRespuesta;
var respuestaSelected;
var beanRequestRespuesta = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestRespuesta.entity_api = 'convocatoria';
    beanRequestRespuesta.operation = 'paginaterespuesta';
    beanRequestRespuesta.type_request = 'GET';

    $('#sizePageRespuesta').change(function () {
        beanRequestRespuesta.type_request = 'GET';
        beanRequestRespuesta.operation = 'paginaterespuesta';
        $('#modalCargandoRespuesta').modal('show');
    });

    $('#modalCargandoRespuesta').modal('show');

    $("#modalCargandoRespuesta").on('shown.bs.modal', function () {
        processAjaxRespuesta();
    });

    $("#ventanaModalManRespuesta").on('hide.bs.modal', function () {
        beanRequestRespuesta.type_request = 'GET';
        beanRequestRespuesta.operation = 'paginate';
    });

    $("#formularioCuestionarioSearch").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();

        beanRequestRespuesta.type_request = 'GET';
        beanRequestRespuesta.operation = 'paginaterespuesta';
        $('#modalCargandoRespuesta').modal('show');



    });

});

function processAjaxRespuesta() {
    let parameters_pagination = '';

    switch (beanRequestRespuesta.operation) {
        case 'detallerespuesta':
            parameters_pagination = '?id=' + respuestaSelected.idpersonaconvocatoria;
            break;

        default:

            parameters_pagination +=
                '?filtro=' + document.querySelector("#txtSearchCuestionario").value.trim();
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pageRespuesta").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePageRespuesta").value.trim();
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestRespuesta.entity_api + "/" + beanRequestRespuesta.operation +
            parameters_pagination,
        type: beanRequestRespuesta.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },

        data: null,
        cache: false,
        contentType: 'application/json; charset=UTF-8',
        processData: false,
        dataType: 'json',
    }).done(function (beanCrudResponse) {

        $('#modalCargandoRespuesta').modal('hide');

        if (beanCrudResponse.beanPagination !== null) {
            if (beanRequestRespuesta.operation == 'detallerespuesta') {
                $("#tituloModalManRespuesta").html("RESPUESTAS");
                $("#ventanaModalManRespuesta").modal("show");
                addRespuesta(beanCrudResponse.beanPagination);
            } else {
                beanPaginationRespuesta = beanCrudResponse.beanPagination;
                listaRespuesta(beanPaginationRespuesta);
            }

        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoRespuesta').modal("hide");
        showAlertErrorRequest();

    });

}

function addRespuesta(beanPagination) {
    //LIMPIAR LOS CAMPOS
    document.querySelector('#detalleRespuesta').innerHTML = "";
    let row = "";

    beanPagination.list.forEach((detalle) => {
        row += `<div class="col-12 col-sm-6 mx-auto">
        <div class="group-material">
          <input type="text" class="material-control"value="${detalle.respuesta}" />
          <span class="highlight"></span>
          <span class="bar"></span>
          <label>${detalle.pregunta}</label>
        </div>
      </div>`;

    });
    document.querySelector('#detalleRespuesta').innerHTML += row;

}

function listaRespuesta(beanPagination) {
    document.querySelector('#tbodyRespuesta').innerHTML = '';
    document.querySelector('#titleManagerRespuesta').innerHTML =
        'RESPUESTAS DEL PÚBLICO';
    document.querySelector("#txtCountCuestionario").value = beanPagination.countFilter;
    let row = "";

    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationRespuesta'));
        row += `<tr>
        <td class="text-center" colspan="6">NO HAY RESPUESTAS DEL PÚBLICO</td>
        </tr>`;

        document.querySelector('#tbodyRespuesta').innerHTML += row;
        return;
    }

    document.querySelector('#tbodyRespuesta').innerHTML += row;
    beanPagination.list.forEach((respuesta) => {
        row += `<tr  idpersonaconvocatoria="${respuesta.idpersonaconvocatoria}">
<td class="text-center">${respuesta.codigo}</td>
<td class="text-center">${respuesta.ip}</td>
<td class="text-center">${respuesta.fecha}</td>
<td class="text-center"><button class="btn btn-info ver-respuesta" >${respuesta.cantidad}</button></td>
<td class="text-center">
<button class="btn btn-danger eliminar-respuesta"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;

        // $('[data-toggle="tooltip"]').tooltip();
    });
    document.querySelector('#tbodyRespuesta').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePageRespuesta").value),
        document.querySelector("#pageRespuesta"),
        $('#modalCargandoRespuesta'),
        $('#paginationRespuesta'));
    addEventsButtonsAdmin();


}

function addEventsButtonsAdmin() {
    document.querySelectorAll('.ver-respuesta').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            respuestaSelected = findByRespuesta(
                btn.parentElement.parentElement.getAttribute('idpersonaconvocatoria')
            );

            if (respuestaSelected != undefined) {
                beanRequestRespuesta.type_request = 'GET';
                beanRequestRespuesta.operation = 'detallerespuesta';
                $('#modalCargandoRespuesta').modal('show');
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });


    document.querySelectorAll('.eliminar-respuesta').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            respuestaSelected = findByRespuesta(
                btn.parentElement.parentElement.getAttribute('idpersonaconvocatoria')
            );

            if (respuestaSelected != undefined) {
                beanRequestRespuesta.type_request = 'GET';
                beanRequestRespuesta.operation = 'deleterespuesta';
                $('#modalCargandoRespuesta').modal('show');
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
}

function tipo(params) {
    switch (parseInt(params)) {
        case 1:
            $("#TipoArchivoRespuesta").html(`<div id="imagePreview" class="py-2 text-center"> </div>
    <input id="txtImagenRespuesta" type="file"accept="image/png, image/jpeg, image/png"
    class="material-control tooltips-general input-check-user"
    placeholder="Selecciona Imagen" data-toggle="tooltip"
    data-placement="top" title="" 
    data-original-title="Selecciona la Imagen de tu escritorio">
    <span class="highlight"></span>
    <span class="bar"></span>
    <label>Selecciona Imagen</label>
    <small>Tamaño Máximo Permitido: 1700 KB</small>
    <br>
    <small>Formatos Permitido:JPG, PNG, JPEG</small>`);
            addViewArchivosPrevius();
            break;
        case 2:
            $(
                "#TipoArchivoRespuesta"
            ).html(`<div id="videoPreview" class="py-2 text-center"></div><input id="txtVideoRespuesta" type="file"
    class="material-control tooltips-general input-check-user"
    placeholder="Selecciona Video" data-toggle="tooltip"
    data-placement="top" title="" accept="video/mp4"
    data-original-title="Selecciona el Video de tu escritorio">
    <span class="highlight"></span>
    <span class="bar"></span>
    <label>Selecciona el Video</label>
    <small>Tamaño Máximo Permitido: 17 MB</small>
    <br>
    <small>Formatos Permitido:MP4</small>`);
            addViewArchivosPrevius();
            break;
        case 3:
            $("#TipoArchivoRespuesta").html(`<input id="PDF" type="file"
        class="material-control tooltips-general input-check-user"
        placeholder="Selecciona PDF" data-toggle="tooltip"
        data-placement="top" title="" 
        data-original-title="Selecciona el PDF de tu escritorio">
        <span class="highlight"></span>
        <span class="bar"></span>
        <label>Sube el Archivo</label>`);
            break;
        default:
            $("#TipoArchivoRespuesta").html("");
            break;
    }
}
function addViewArchivosPrevius() {

    $("#txtImagenRespuesta").change(function () {
        filePreview(this, "#imagePreview");
    });
    $("#txtVideoRespuesta").change(function () {
        videoPreview(this, "#videoPreview");
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

function videoPreview(input, imagen) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        imagen;
        reader.onload = function (e) {
            $(imagen).html(
                "<video width='244' alt='user-picture' class='img-responsive center-box' controls ><source src='" +
                e.target.result +
                "' type='video/mp4'></video>"
            );
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function findIndexRespuesta(idbusqueda) {
    return beanPaginationRespuesta.list.findIndex(
        (Respuesta) => {
            if (Respuesta.idpersonaconvocatoria == parseInt(idbusqueda))
                return Respuesta;


        }
    );
}

function findByRespuesta(idpersonaconvocatoria) {
    return beanPaginationRespuesta.list.find(
        (Respuesta) => {
            if (parseInt(idpersonaconvocatoria) == Respuesta.idpersonaconvocatoria) {
                return Respuesta;
            }


        }
    );
}
var validateFormRespuesta = () => {
    if (document.querySelector("#txtTituloRespuesta").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese titulo",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtResumenRespuesta").value == "") {
        swal({
            title: "Vacío",
            text: "Selecciona Resumen",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if ($("#txtDescripcionRespuesta").Editor("getText") == "") {
        swal({
            title: "Vacío",
            text: "Selecciona Descripción",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtTipoArchivoRespuesta").value == 0) {
        swal({
            title: "Vacío",
            text: "Selecciona Tipo",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (beanRequestRespuesta.operation == 'add') {

        switch (parseInt(document.querySelector("#txtTipoArchivoRespuesta").value)) {
            case 1:
                if (document.querySelector("#txtImagenRespuesta").files.length == 0) {
                    swal({
                        title: "Vacío",
                        text: "Ingrese Imagen",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }
                if (!(document.querySelector("#txtImagenRespuesta").files[0].type == "image/png" || document.querySelector("#txtImagenRespuesta").files[0].type == "image/jpg" || document.querySelector("#txtImagenRespuesta").files[0].type == "image/jpeg")) {
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
                if (document.querySelector("#txtImagenRespuesta").files[0].size > (4 * 1024 * 1024)) {
                    swal({
                        title: "Tamaño excedido",
                        text: "el tamaño del archivo tiene que ser menor a 900 KB",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }

                break;
            default:
                //video
                if (document.querySelector("#txtVideoRespuesta").files.length == 0) {
                    swal({
                        title: "Vacío",
                        text: "Ingrese Video",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }
                if (document.querySelector("#txtVideoRespuesta").files[0].type !== "video/mp4") {
                    swal({
                        title: "Formato Incorrecto",
                        text: "Ingrese tipo de arhivo MP4 ",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }
                //menor a   17 MB
                if (document.querySelector("#txtVideoRespuesta").files[0].size > (17 * 1024 * 1024)) {
                    swal({
                        title: "Tamaño excedido",
                        text: "el tamaño del archivo tiene que ser menor a 5120 KB",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }
                break;

        }

    } else {

        switch (parseInt(document.querySelector("#txtTipoArchivoRespuesta").value)) {
            case 1:
                if (document.querySelector("#txtImagenRespuesta").files.length != 0) {
                    if (document.querySelector("#txtImagenRespuesta").files.length == 0) {
                        swal({
                            title: "Vacío",
                            text: "Ingrese Imagen",
                            type: "warning",
                            timer: 1200,
                            showConfirmButton: false
                        });
                        return false;
                    }
                    if (!(document.querySelector("#txtImagenRespuesta").files[0].type == "image/png" || document.querySelector("#txtImagenRespuesta").files[0].type == "image/jpg" || document.querySelector("#txtImagenRespuesta").files[0].type == "image/jpeg")) {
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
                    if (document.querySelector("#txtImagenRespuesta").files[0].size > (1700 * 1024)) {
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

                break;

            default:
                if (document.querySelector("#txtVideoRespuesta").files.length != 0) {  //video
                    if (document.querySelector("#txtVideoRespuesta").files.length == 0) {
                        swal({
                            title: "Vacío",
                            text: "Ingrese Video",
                            type: "warning",
                            timer: 1200,
                            showConfirmButton: false
                        });
                        return false;
                    }
                    if (document.querySelector("#txtVideoRespuesta").files[0].type !== "video/mp4") {
                        swal({
                            title: "Formato Incorrecto",
                            text: "Ingrese tipo de arhivo MP4 ",
                            type: "warning",
                            timer: 1200,
                            showConfirmButton: false
                        });
                        return false;
                    }
                    //menor a   17 MB
                    if (document.querySelector("#txtVideoRespuesta").files[0].size > (17 * 1024 * 1024)) {
                        swal({
                            title: "Tamaño excedido",
                            text: "el tamaño del archivo tiene que ser menor a 5120 KB",
                            type: "warning",
                            timer: 1200,
                            showConfirmButton: false
                        });
                        return false;
                    }
                }

                break;

        }



    }

    return true;
}