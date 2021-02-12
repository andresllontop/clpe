var beanPaginationDetalle;
var detalleSelected;
var beanRequestDetalle = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestDetalle.entity_api = 'detalles/recursos';
    beanRequestDetalle.operation = 'paginate';
    beanRequestDetalle.type_request = 'GET';

    $('#sizePageDetalle').change(function () {
        beanRequestDetalle.type_request = 'GET';
        beanRequestDetalle.operation = 'paginate';
        $('#modalCargandoDetalle').modal('show');
    });

    $('#txtTipoArchivoDetalle').change(function (e) {
        switch (parseInt(e.target.value)) {
            case 1:
                document.querySelector('#Tipo-Archivo').innerHTML = `
                <div id="imagePreviewDetalle"></div>
                <input id="txtImagenDetalle" type="file" accept="image/png, image/jpeg, image/jpg" class="material-control tooltips-general input-check-user"
                  placeholder="Selecciona Imagen" data-toggle="tooltip" data-placement="top" title=""
                  data-original-title="Selecciona la Imagen">
                <span class="highlight"></span>
                <span class="bar"></span>
                <label>Selecciona Imagen</label>
                <small>Tamaño Máximo Permitido: 900 KB</small>
                <br>
                <small>Formatos Permitido:JPG, PNG, JPEG</small>
                `;
                break;
            case 2:
                document.querySelector('#Tipo-Archivo').innerHTML = `
                <div id="videoPreview"></div>
                <input id="txtVideoDetalle" type="file" accept="video/mp4"class="material-control tooltips-general input-check-user"
                  placeholder="Selecciona Video" data-toggle="tooltip" data-placement="top" title=""
                  data-original-title="Selecciona la Video">
                <span class="highlight"></span>
                <span class="bar"></span>
                <label>Selecciona Video</label>
                <small>Tamaño Máximo Permitido: 17 MB</small>
                <br>
                <small>Formatos Permitido:MP4</small>
                `;
                break;

            default:
                document.querySelector('#Tipo-Archivo').innerHTML = `
                <input id="txtPdfDetalle" type="file" accept="application/pdf" class="material-control tooltips-general input-check-user"
                  placeholder="Selecciona Archivo" data-toggle="tooltip" data-placement="top" title=""
                  data-original-title="Selecciona Archivo PDF">
                <span class="highlight"></span>
                <span class="bar"></span>
                <label>Selecciona Archivo PDF</label>
                <small>Tamaño Máximo Permitido: 5120 KB</small>
                <br>
                <small>Formatos Permitido: PDF</small>
                `;
                break;
        }
        addViewArchivosPrevius();
    });


    $("#modalCargandoDetalle").on('shown.bs.modal', function () {
        processAjaxDetalle();
    });
    $("#ventanaModalManDetalle").on('hide.bs.modal', function () {
        beanRequestDetalle.type_request = 'GET';
        beanRequestDetalle.operation = 'paginate';
    });

    $("#btnAbrirDetalle").click(function () {
        beanRequestDetalle.operation = 'add';
        beanRequestDetalle.type_request = 'POST';
        $("#imagePreviewDetalle").html("");
        $("#tituloModalManDetalle").html("REGISTRAR ARCHIVO");
        addDetalle();
        $("#ventanaModalManDetalle").modal("show");


    });
    $("#formularioDetalle").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (validarDormularioDetalle()) {
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
            nombre: document.querySelector("#txtNombreDetalle").value,
            tipo: parseInt(document.querySelector("#txtTipoArchivoDetalle").value),
            recurso: recursoSelected.idrecurso

        };


    } else {
        form_data = null;
    }

    switch (beanRequestDetalle.operation) {
        case 'delete':
            parameters_pagination = '?id=' + detalleSelected.iddetallerecurso;
            break;

        case 'update':
            json.iddetallerecurso = detalleSelected.iddetallerecurso;
            let dataImagen;
            switch (parseInt(document.querySelector("#txtTipoArchivoDetalle").value)) {
                case 1:
                    if (document.querySelector("#txtImagenDetalle").files.length != 0) {
                        dataImagen = $("#txtImagenDetalle").prop("files")[0];
                        form_data.append("txtImagenDetalle", dataImagen);
                    }

                    break;
                case 2:
                    if (document.querySelector("#txtVideoDetalle").files.length != 0) {
                        dataImagen = $("#txtVideoDetalle").prop("files")[0];
                        form_data.append("txtVideoDetalle", dataImagen);
                    }

                    break;

                default:
                    if (document.querySelector("#txtPdfDetalle").files.length != 0) {
                        dataImagen = $("#txtPdfDetalle").prop("files")[0];
                        form_data.append("txtPdfDetalle", dataImagen);
                    }

                    break;
            }

            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':

            let dataImagen2;
            switch (parseInt(document.querySelector("#txtTipoArchivoDetalle").value)) {
                case 1:

                    dataImagen2 = $("#txtImagenDetalle").prop("files")[0];
                    form_data.append("txtImagenDetalle", dataImagen2);
                    break;
                case 2:
                    dataImagen2 = $("#txtVideoDetalle").prop("files")[0];
                    form_data.append("txtVideoDetalle", dataImagen2);
                    break;

                default:
                    dataImagen2 = $("#txtPdfDetalle").prop("files")[0];
                    form_data.append("txtPdfDetalle", dataImagen2);
                    break;
            }
            form_data.append("class", JSON.stringify(json));
            break;

        default:

            parameters_pagination +=
                '?filtro=' + recursoSelected.idrecurso;
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
        dataType: 'json',
        xhr: function () {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    $('.progress-bar-detallerecurso').css({
                        width: Math.round(percentComplete * 100) + '%'
                    });
                    $(".progress-bar-detallerecurso").text(Math.round(percentComplete * 100) + '%');
                    $(".progress-bar-detallerecurso").attr("aria-valuenow", +Math.round(percentComplete * 100));
                    if (percentComplete === 1) {
                        // $('.progress-bar-detallerecurso').addClass('hide');
                        $('.progress-bar-detallerecurso').css({
                            width: + '100%'
                        });
                        $(".progress-bar-detallerecurso").text("Cargando ... 100%");
                        $(".progress-bar-detallerecurso").attr("aria-valuenow", "100");
                    }
                }
            }, false);
            xhr.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    $('.progress-bar-detallerecurso').css({
                        width: Math.round(percentComplete * 100) + '%'
                    });
                    $(".progress-bar-detallerecurso").text(Math.round(percentComplete * 100) + '%');
                    $(".progress-bar-detallerecurso").attr("aria-valuenow", +Math.round(percentComplete * 100));
                }
            }, false);
            return xhr;
        },
    }).done(function (beanCrudResponse) {

        $('#modalCargandoDetalle').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                document.querySelector("#pageDetalle").value = 1;
                document.querySelector("#sizePageDetalle").value = 5;
                $('#ventanaModalManDetalle').modal('hide');
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
    document.querySelector('#txtTipoArchivoDetalle').value = (detalle == undefined) ? '1' : detalle.tipo;
    if (detalle !== undefined) {
        switch (parseInt(detalle.tipo)) {
            case 1:
                document.querySelector('#Tipo-Archivo').innerHTML = `
                <div id="imagePreviewDetalle" class="text-center"><img width='244' alt='user-picture' class='img-responsive center-box' src='${getHostFrontEnd()}adjuntos/recurso/IMAGENES/${detalle.archivo}' /></div>
                <input id="txtImagenDetalle" type="file" accept="image/png, image/jpeg, image/jpg" class="material-control tooltips-general input-check-user"
                  placeholder="Selecciona Imagen" data-toggle="tooltip" data-placement="top" title=""
                  data-original-title="Selecciona la Imagen">
                <span class="highlight"></span>
                <span class="bar"></span>
                <label>Selecciona Imagen</label>
                <small>Tamaño Máximo Permitido: 900 KB</small>
                <br>
                <small>Formatos Permitido:JPG, PNG, JPEG</small>
                `;
                break;
            case 2:
                document.querySelector('#Tipo-Archivo').innerHTML = `
                <div id="videoPreview"><video width='100%' alt='user-picture' class='img-responsive center-box' controls ><source src='${getHostFrontEnd()}adjuntos/recurso/VIDEOS/${detalle.archivo}' type='video/mp4'></video></div>
                <input id="txtVideoDetalle" type="file" accept="video/mp4"class="material-control tooltips-general input-check-user"
                  placeholder="Selecciona Video" data-toggle="tooltip" data-placement="top" title=""
                  data-original-title="Selecciona la Video">
                <span class="highlight"></span>
                <span class="bar"></span>
                <label>Selecciona Video</label>
                <small>Tamaño Máximo Permitido: 17 MB</small>
                <br>
                <small>Formatos Permitido:MP4</small>
                `;
                break;

            default:
                document.querySelector('#Tipo-Archivo').innerHTML = `
                <input id="txtPdfDetalle" type="file" accept="application/pdf" class="material-control tooltips-general input-check-user"
                  placeholder="Selecciona Archivo" data-toggle="tooltip" data-placement="top" title=""
                  data-original-title="Selecciona Archivo PDF">
                <span class="highlight"></span>
                <span class="bar"></span>
                <label>Selecciona Archivo PDF</label>
                <small>Tamaño Máximo Permitido: 5120 KB</small>
                <br>
                <small>Formatos Permitido: PDF</small>
                `;
                break;
        }


    } else {
        document.querySelector('#Tipo-Archivo').innerHTML = `
        <div id="imagePreviewDetalle"> </div>
        <input id="txtImagenDetalle" type="file" class="material-control tooltips-general input-check-user" accept="image/png, image/jpeg, image/png"
          placeholder="Selecciona Imagen" data-toggle="tooltip" data-placement="top" title=""
          data-original-title="Selecciona la Imagen">
        <span class="highlight"></span>
        <span class="bar"></span>
        <label>Selecciona Imagen</label>
        <small>Tamaño Máximo Permitido: 900 KB</small>
                <br>
                <small>Formatos Permitido:JPG, PNG, JPEG</small>
                `;
    }

    addViewArchivosPrevius();

}

function listaDetalle(beanPagination) {
    document.querySelector('#tbodyDetalle').innerHTML = '';
    document.querySelector('#titleManagerDetalle').innerHTML =
        '[ ' + beanPagination.countFilter + ' ] "' + recursoSelected.subtitulo.nombre + '"';
    let row = "";
    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationDetalle'));
        row += `<tr>
        <td class="text-center" colspan="5">NO HAY ARCHIVOS</td>
        </tr>`;
        document.querySelector('#tbodyDetalle').innerHTML = row;
        return;
    }
    beanPagination.list.forEach((detalle) => {

        row += `<tr  iddetallerecurso="${detalle.iddetallerecurso}">
<td class="text-center">${detalle.descripcion}</td>
`;

        if (detalle.tipo == 1) {
            //imagen
            row += `
            <td class="text-center" style="width:26%;"><img src="${getHostFrontEnd()}adjuntos/recurso/IMAGENES/${detalle.archivo}" alt="${detalle.archivo}" class="img-responsive center-box w-100" ></td>
            `;
        } else if (detalle.tipo == 2) {
            //video
            row += ` <td class="text-center" style="width:26%;">
            <video width='100%' alt='${detalle.archivo}' class='img-responsive center-box' controls ><source src='${getHostFrontEnd()}adjuntos/recurso/VIDEOS/${detalle.archivo}' type='video/mp4'></video></td>
            `;
        } else {
            //archivo pdf
            row += `
            <td class="text-center" style="width:26%;">
            <button class="btn btn-warning descargar-archivo" ><i class="zmdi zmdi-download"></i></button></div>
            </td>
            `;
        }


        row += `
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
    document.querySelectorAll('.descargar-archivo').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            detalleSelected = findByDetalle(
                btn.parentElement.parentElement.getAttribute('iddetallerecurso')
            );
            if (detalleSelected != undefined) {
                $("#modalFrameDetalle").modal("show");
                downloadURL(getHostFrontEnd() + "adjuntos/recurso/PDF/" + detalleSelected.archivo);


            } else {
                console.log(
                    'warning => ',
                    'No se encontró el detallerecurso para poder ver'
                );
            }
        };
    });

    document.querySelectorAll('.editar-detalle').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            detalleSelected = findByDetalle(
                btn.parentElement.parentElement.getAttribute('iddetallerecurso')
            );

            if (detalleSelected != undefined) {
                addDetalle(detalleSelected);
                $("#tituloModalManDetalle").html("EDITAR ARCHIVO");
                $("#ventanaModalManDetalle").modal("show");
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
                btn.parentElement.parentElement.getAttribute('iddetallerecurso')
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

function addViewArchivosPrevius() {

    $("#txtImagenDetalle").change(function () {
        filePreview(this, "#imagePreviewDetalle");
    });
    $("#txtVideoDetalle").change(function () {
        videoPreview(this, "#videoPreview");
    });
}

function videoPreview(input, imagen) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        imagen;
        reader.onload = function (e) {
            $(imagen).html(
                "<video width='100%' alt='user-picture' class='img-responsive center-box' controls ><source src='" +
                e.target.result +
                "' type='video/mp4'></video>"
            );
        };
        reader.readAsDataURL(input.files[0]);
    }
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
            if (Detalle.iddetallerecurso == parseInt(idbusqueda))
                return Detalle;


        }
    );
}

function findByDetalle(iddetallerecurso) {
    return beanPaginationDetalle.list.find(
        (Detalle) => {
            if (parseInt(iddetallerecurso) == Detalle.iddetallerecurso) {
                return Detalle;
            }


        }
    );
}

var validarDormularioDetalle = () => {
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

    if (document.querySelector("#txtTipoArchivoDetalle").value == "") {
        swal({
            title: "Vacío",
            text: "Selecciona Tipo de Archivo",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (beanRequestDetalle.operation == 'add') {

        switch (parseInt(document.querySelector("#txtTipoArchivoDetalle").value)) {
            case 1:
                if (document.querySelector("#txtImagenDetalle").files.length == 0) {
                    swal({
                        title: "Vacío",
                        text: "Ingrese Imagen",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }
                if (!(document.querySelector("#txtImagenDetalle").files[0].type == "image/png" || document.querySelector("#txtImagenDetalle").files[0].type == "image/jpg" || document.querySelector("#txtImagenDetalle").files[0].type == "image/jpeg")) {
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
                if (document.querySelector("#txtImagenDetalle").files[0].size > (4 * 1024 * 1024)) {
                    swal({
                        title: "Tamaño excedido",
                        text: "el tamaño del archivo tiene que ser menor a 4 MB",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }

                break;
            case 2:
                //video
                if (document.querySelector("#txtVideoDetalle").files.length == 0) {
                    swal({
                        title: "Vacío",
                        text: "Ingrese Video",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }
                if (document.querySelector("#txtVideoDetalle").files[0].type !== "video/mp4") {
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
                if (document.querySelector("#txtVideoDetalle").files[0].size > (17 * 1024 * 1024)) {
                    swal({
                        title: "Tamaño excedido",
                        text: "el tamaño del archivo tiene que ser menor a 17 MB",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }
                break;

            default:
                //PDF
                if (document.querySelector("#txtPdfDetalle").files.length == 0) {
                    swal({
                        title: "Vacío",
                        text: "Ingrese PDF",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }
                if (document.querySelector("#txtPdfDetalle").files[0].type !== "application/pdf") {
                    swal({
                        title: "Formato Incorrecto",
                        text: "Ingrese tipo de arhivo pdf",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }
                //menor a   5 MB
                if (document.querySelector("#txtPdfDetalle").files[0].size > (5 * 1024 * 1024)) {
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

        switch (parseInt(document.querySelector("#txtTipoArchivoDetalle").value)) {
            case 1:
                if (document.querySelector("#txtImagenDetalle").files.length != 0) {
                    if (document.querySelector("#txtImagenDetalle").files.length == 0) {
                        swal({
                            title: "Vacío",
                            text: "Ingrese Imagen",
                            type: "warning",
                            timer: 1200,
                            showConfirmButton: false
                        });
                        return false;
                    }
                    if (!(document.querySelector("#txtImagenDetalle").files[0].type == "image/png" || document.querySelector("#txtImagenDetalle").files[0].type == "image/jpg" || document.querySelector("#txtImagenDetalle").files[0].type == "image/jpeg")) {
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
                    if (document.querySelector("#txtImagenDetalle").files[0].size > (4 * 1024 * 1024)) {
                        swal({
                            title: "Tamaño excedido",
                            text: "el tamaño del archivo tiene que ser menor a 900 KB",
                            type: "warning",
                            timer: 1200,
                            showConfirmButton: false
                        });
                        return false;
                    }
                }

                break;
            case 2:
                if (document.querySelector("#txtVideoDetalle").files.length != 0) {  //video
                    if (document.querySelector("#txtVideoDetalle").files.length == 0) {
                        swal({
                            title: "Vacío",
                            text: "Ingrese Video",
                            type: "warning",
                            timer: 1200,
                            showConfirmButton: false
                        });
                        return false;
                    }
                    if (document.querySelector("#txtVideoDetalle").files[0].type !== "video/mp4") {
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
                    if (document.querySelector("#txtVideoDetalle").files[0].size > (17 * 1024 * 1024)) {
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

            default:
                if (document.querySelector("#txtPdfDetalle").files.length != 0) {  //PDF
                    if (document.querySelector("#txtPdfDetalle").files.length == 0) {
                        swal({
                            title: "Vacío",
                            text: "Ingrese PDF",
                            type: "warning",
                            timer: 1200,
                            showConfirmButton: false
                        });
                        return false;
                    }
                    if (document.querySelector("#txtPdfDetalle").files[0].type !== "application/pdf") {
                        swal({
                            title: "Formato Incorrecto",
                            text: "Ingrese tipo de arhivo pdf",
                            type: "warning",
                            timer: 1200,
                            showConfirmButton: false
                        });
                        return false;
                    }
                    //menor a   5 MB
                    if (document.querySelector("#txtPdfDetalle").files[0].size > (5 * 1024 * 1024)) {
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