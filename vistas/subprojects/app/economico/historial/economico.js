var beanPaginationEconomico;
var economicoSelected;
var beanRequestEconomico = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestEconomico.entity_api = 'economico';
    beanRequestEconomico.operation = 'paginate';
    beanRequestEconomico.type_request = 'GET';

    $('#sizePageEconomico').change(function () {
        beanRequestEconomico.type_request = 'GET';
        beanRequestEconomico.operation = 'paginate';
        $('#modalCargandoEconomico').modal('show');
    });

    $('#modalCargandoEconomico').modal('show');

    $("#modalCargandoEconomico").on('shown.bs.modal', function () {
        processAjaxEconomico();
    });

    $("#ventanaModalManEconomico").on('hide.bs.modal', function () {
        beanRequestEconomico.type_request = 'GET';
        beanRequestEconomico.operation = 'paginate';
    });




});

function processAjaxEconomico() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestEconomico.operation == 'update' ||
        beanRequestEconomico.operation == 'add'
    ) {

        json = {
            titulo: document.querySelector("#txtTituloEconomico").value,
            resumen: document.querySelector("#txtResumenEconomico").value,
            descripcion: $("#txtDescripcionEconomico").Editor("getText"),
            archivo: 0,
            tipo_archivo: parseInt(document.querySelector("#txtTipoArchivoEconomico").value),
            comentario: ""

        };


    } else {
        form_data = null;
    }

    switch (beanRequestEconomico.operation) {
        case 'delete':
            parameters_pagination = '?id=' + economicoSelected.ideconomico;
            break;

        case 'update':
            json.ideconomico = economicoSelected.ideconomico;
            if (parseInt(document.querySelector("#txtTipoArchivoEconomico").value) == 1) {
                if (document.querySelector("#txtImagenEconomico").files.length !== 0) {
                    let dataFoto = $("#txtImagenEconomico").prop("files")[0];
                    form_data.append("txtImagenEconomico", dataFoto);
                }
            } else {
                if (document.querySelector("#txtVideoEconomico").files.length !== 0) {
                    let dataFoto = $("#txtVideoEconomico").prop("files")[0];
                    form_data.append("txtVideoEconomico", dataFoto);
                }
            }

            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':
            if (parseInt(document.querySelector("#txtTipoArchivoEconomico").value) == 1) {
                let data = $("#txtImagenEconomico").prop("files")[0];
                form_data.append("txtImagenEconomico", data);
            } else {
                let data = $("#txtVideoEconomico").prop("files")[0];
                form_data.append("txtVideoEconomico", data);
            }

            form_data.append("class", JSON.stringify(json));
            break;

        default:

            parameters_pagination +=
                '?filtro=';
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pageEconomico").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePageEconomico").value.trim();
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestEconomico.entity_api + "/" + beanRequestEconomico.operation +
            parameters_pagination,
        type: beanRequestEconomico.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },

        data: form_data,
        cache: false,
        contentType: ((beanRequestEconomico.operation == 'update' || beanRequestEconomico.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',
    }).done(function (beanCrudResponse) {

        $('#modalCargandoEconomico').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                document.querySelector("#pageEconomico").value = 1;
                document.querySelector("#sizePageEconomico").value = 20;
                $('#ventanaModalManEconomico').modal('hide');
            } else {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationEconomico = beanCrudResponse.beanPagination;
            listaEconomico(beanPaginationEconomico);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoEconomico').modal("hide");
        showAlertErrorRequest();

    });

}

function addEconomico(economico = undefined) {
    //LIMPIAR LOS CAMPOS

    document.querySelector('#txtTituloEconomico').value = (economico == undefined) ? '' : economico.titulo;
    document.querySelector('#txtTipoArchivoEconomico').value = (economico == undefined) ? '0' : economico.tipoArchivo;
    document.querySelector('#txtResumenEconomico').value = (economico == undefined) ? '' : economico.resumen;


    $("#txtDescripcionEconomico").Editor("setText", (economico == undefined) ? '<p style="color:black"></p>' : economico.descripcion);
    $("#txtDescripcionEconomico").Editor("getText");
    if (economico != undefined) {
        tipo(document.querySelector('#txtTipoArchivoEconomico').value);
        switch (parseInt(document.querySelector('#txtTipoArchivoEconomico').value)) {
            case 1:
                document.querySelector("#imagePreview").innerHTML = `<img width='244' alt='user-picture' class='img-responsive text-center' src='${getHostFrontEnd() + 'adjuntos/economico/IMAGENES/' + economicoSelected.archivo}' />`;
                break;
            case 2:
                document.querySelector("#videoPreview").innerHTML = `<video width='244' alt='user-picture' class='img-responsive text-center' controls ><source src='${getHostFrontEnd() + 'adjuntos/economico/VIDEOS/' + economicoSelected.archivo}' type='video/mp4'></video>`;
                break;

            default:
                break;
        }
    } else {
        tipo(1);
    }

    //$("#txtResumenEconomico").Editor("setText", (economico == undefined) ? '<p /style="color:black"></p>' : economico.resumen);
    // $("#txtResumenEconomico").Editor("getText");



}

function listaEconomico(beanPagination) {
    document.querySelector('#tbodyEconomico').innerHTML = '';
    document.querySelector('#titleManagerEconomico').innerHTML =
        '[ ' + beanPagination.countFilter + ' ] HISTORIAL ECONÓMICO';
    let row = "";

    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationEconomico'));
        row += `<tr>
        <td class="text-center" colspan="12">NO HAY HISTORIAL ECONÓMICO</td>
        </tr>`;

        document.querySelector('#tbodyEconomico').innerHTML += row;
        return;
    }

    document.querySelector('#tbodyEconomico').innerHTML += row;
    let html2;
    beanPagination.list.forEach((economico) => {
        row += `<tr  ideconomico="${economico.ideconomico}">
<td class="text-center">${economico.nombre}</td>
<td class="text-center">${economico.apellido}</td>
<td class="text-center">${economico.telefono}</td>
<td class="text-center">${economico.pais}</td>
<td class="text-center">${economico.banco}</td>
<td class="text-center">${economico.moneda}</td>
<td class="text-center">${economico.comision}</td>
<td class="text-center">${economico.precio}</td>

<td class="text-center">${parseFloat(economico.precio) + parseFloat(economico.comision)}</td>
<td class="text-center ">${(economico.voucher == null || economico.voucher == "") ? "SIN VOUCHER" : ("<img src='" + getHostFrontEnd() + "adjuntos/clientes/comprobante/" + economico.voucher + "' class='img-responsive center-box' style='width:50px;height:60px;'>")}</td>
<td class="text-center">
<button class="btn btn-${economico.tipo == 2 ? "info" : "warning"}">${economico.tipo == 2 ? "EFECTIVO" : "CULQI"}</button></td>
<td class="text-center">${economico.fecha}</td>
<td class="text-center">
<button class="btn btn-danger eliminar-economico"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;

        // $('[data-toggle="tooltip"]').tooltip();
    });
    document.querySelector('#tbodyEconomico').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePageEconomico").value),
        document.querySelector("#pageEconomico"),
        $('#modalCargandoEconomico'),
        $('#paginationEconomico'));
    addEventsButtonsAdmin();


}

function addEventsButtonsAdmin() {


    document.querySelectorAll('.editar-economico').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            economicoSelected = findByEconomico(
                btn.parentElement.parentElement.getAttribute('ideconomico')
            );

            if (economicoSelected != undefined) {
                addEconomico(economicoSelected);
                $("#tituloModalManEconomico").html("EDITAR BLOG");
                $("#ventanaModalManEconomico").modal("show");
                beanRequestEconomico.type_request = 'POST';
                beanRequestEconomico.operation = 'update';
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
    document.querySelectorAll('.eliminar-economico').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            economicoSelected = findByEconomico(
                btn.parentElement.parentElement.getAttribute('ideconomico')
            );

            if (economicoSelected != undefined) {
                beanRequestEconomico.type_request = 'GET';
                beanRequestEconomico.operation = 'delete';
                $('#modalCargandoEconomico').modal('show');
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
            $("#TipoArchivoEconomico").html(`<div id="imagePreview" class="py-2 text-center"> </div>
    <input id="txtImagenEconomico" type="file"accept="image/png, image/jpeg, image/png"
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
                "#TipoArchivoEconomico"
            ).html(`<div id="videoPreview" class="py-2 text-center"></div><input id="txtVideoEconomico" type="file"
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
            $("#TipoArchivoEconomico").html(`<input id="PDF" type="file"
        class="material-control tooltips-general input-check-user"
        placeholder="Selecciona PDF" data-toggle="tooltip"
        data-placement="top" title="" 
        data-original-title="Selecciona el PDF de tu escritorio">
        <span class="highlight"></span>
        <span class="bar"></span>
        <label>Sube el Archivo</label>`);
            break;
        default:
            $("#TipoArchivoEconomico").html("");
            break;
    }
}
function addViewArchivosPrevius() {

    $("#txtImagenEconomico").change(function () {
        filePreview(this, "#imagePreview");
    });
    $("#txtVideoEconomico").change(function () {
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

function findIndexEconomico(idbusqueda) {
    return beanPaginationEconomico.list.findIndex(
        (Economico) => {
            if (Economico.ideconomico == parseInt(idbusqueda))
                return Economico;


        }
    );
}

function findByEconomico(ideconomico) {
    return beanPaginationEconomico.list.find(
        (Economico) => {
            if (parseInt(ideconomico) == Economico.ideconomico) {
                return Economico;
            }


        }
    );
}
var validateFormEconomico = () => {
    if (document.querySelector("#txtTituloEconomico").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese titulo",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtResumenEconomico").value == "") {
        swal({
            title: "Vacío",
            text: "Selecciona Resumen",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if ($("#txtDescripcionEconomico").Editor("getText") == "") {
        swal({
            title: "Vacío",
            text: "Selecciona Descripción",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtTipoArchivoEconomico").value == 0) {
        swal({
            title: "Vacío",
            text: "Selecciona Tipo",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (beanRequestEconomico.operation == 'add') {

        switch (parseInt(document.querySelector("#txtTipoArchivoEconomico").value)) {
            case 1:
                if (document.querySelector("#txtImagenEconomico").files.length == 0) {
                    swal({
                        title: "Vacío",
                        text: "Ingrese Imagen",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }
                if (!(document.querySelector("#txtImagenEconomico").files[0].type == "image/png" || document.querySelector("#txtImagenEconomico").files[0].type == "image/jpg" || document.querySelector("#txtImagenEconomico").files[0].type == "image/jpeg")) {
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
                if (document.querySelector("#txtImagenEconomico").files[0].size > (4 * 1024 * 1024)) {
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
                if (document.querySelector("#txtVideoEconomico").files.length == 0) {
                    swal({
                        title: "Vacío",
                        text: "Ingrese Video",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }
                if (document.querySelector("#txtVideoEconomico").files[0].type !== "video/mp4") {
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
                if (document.querySelector("#txtVideoEconomico").files[0].size > (17 * 1024 * 1024)) {
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

        switch (parseInt(document.querySelector("#txtTipoArchivoEconomico").value)) {
            case 1:
                if (document.querySelector("#txtImagenEconomico").files.length != 0) {
                    if (document.querySelector("#txtImagenEconomico").files.length == 0) {
                        swal({
                            title: "Vacío",
                            text: "Ingrese Imagen",
                            type: "warning",
                            timer: 1200,
                            showConfirmButton: false
                        });
                        return false;
                    }
                    if (!(document.querySelector("#txtImagenEconomico").files[0].type == "image/png" || document.querySelector("#txtImagenEconomico").files[0].type == "image/jpg" || document.querySelector("#txtImagenEconomico").files[0].type == "image/jpeg")) {
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
                    if (document.querySelector("#txtImagenEconomico").files[0].size > (1700 * 1024)) {
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
                if (document.querySelector("#txtVideoEconomico").files.length != 0) {  //video
                    if (document.querySelector("#txtVideoEconomico").files.length == 0) {
                        swal({
                            title: "Vacío",
                            text: "Ingrese Video",
                            type: "warning",
                            timer: 1200,
                            showConfirmButton: false
                        });
                        return false;
                    }
                    if (document.querySelector("#txtVideoEconomico").files[0].type !== "video/mp4") {
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
                    if (document.querySelector("#txtVideoEconomico").files[0].size > (17 * 1024 * 1024)) {
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