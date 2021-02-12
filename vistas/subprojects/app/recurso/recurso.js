var beanPaginationRecurso;
var recursoSelected;
var subtituloSelected;
var beanRequestRecurso = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestRecurso.entity_api = 'recursos';
    beanRequestRecurso.operation = 'paginate';
    beanRequestRecurso.type_request = 'GET';

    $('#sizePageRecurso').change(function () {
        beanRequestRecurso.type_request = 'GET';
        beanRequestRecurso.operation = 'paginate';
        $('#modalCargandoRecurso').modal('show');
    });


    $('#modalCargandoRecurso').modal('show');

    $("#modalCargandoRecurso").on('shown.bs.modal', function () {
        processAjaxRecurso();
    });
    $("#ventanaModalManRecurso").on('hide.bs.modal', function () {
        beanRequestRecurso.type_request = 'GET';
        beanRequestRecurso.operation = 'paginate';
    });

    $("#btnAbrirRecurso").click(function () {
        beanRequestRecurso.operation = 'add';
        beanRequestRecurso.type_request = 'POST';
        $("#imagePreview").html("");
        $("#tituloModalManRecurso").html("REGISTRAR RECURSO");
        if (beanPaginationSubtituloC == undefined) {
            beanRequestSubtituloC.operation = 'obtener';
            beanRequestSubtituloC.type_request = 'GET';
            $('#modalCargandoSubtituloC').modal('show');
            processAjaxSubtituloC();

        } else {
            addRecurso();
        }

        $("#ventanaModalManRecurso").modal("show");


    });
    $("#formularioRecurso").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (validarDormularioVideo()) {
            $('#modalCargandoRecurso').modal('show');
        }
    });

    $('#txtTipoDetalleArchivo').change(function (e) {
        switch (parseInt(e.target.value)) {
            case 1:
                document.querySelector('#TipoDetalleArchivo').innerHTML = `
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
                document.querySelector('#TipoDetalleArchivo').innerHTML = `
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
                document.querySelector('#TipoDetalleArchivo').innerHTML = `
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
});

function processAjaxRecurso() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestRecurso.operation == 'update' ||
        beanRequestRecurso.operation == 'add'
    ) {

        json = {
            nombre: document.querySelector("#txtNombreRecurso").value,
            disponible: parseInt(document.querySelector("#txtDisponibidadRecurso").value),
            subTitulo: document.querySelector("#txtSubtitulo").value
        };


    } else {
        form_data = null;
    }

    switch (beanRequestRecurso.operation) {
        case 'delete':
            parameters_pagination = '?id=' + recursoSelected.idrecurso;
            break;

        case 'update':
            json.idrecurso = recursoSelected.idrecurso;
            let dataImagen;
            if (document.querySelector("#txtImagenRecurso").files.length != 0) {
                dataImagen = $("#txtImagenRecurso").prop("files")[0];
                form_data.append("txtImagenRecurso", dataImagen);
            }

            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':

            let dataImagen2;

            dataImagen2 = $("#txtImagenRecurso").prop("files")[0];
            form_data.append("txtImagenRecurso", dataImagen2);

            form_data.append("class", JSON.stringify(json));
            break;

        default:

            parameters_pagination +=
                '?filtro=';
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pageRecurso").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePageRecurso").value.trim();
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestRecurso.entity_api + "/" + beanRequestRecurso.operation +
            parameters_pagination,
        type: beanRequestRecurso.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestRecurso.operation == 'update' || beanRequestRecurso.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',
        xhr: function () {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    $('.progress-bar-recurso').css({
                        width: Math.round(percentComplete * 100) + '%'
                    });
                    $(".progress-bar-recurso").text(Math.round(percentComplete * 100) + '%');
                    $(".progress-bar-recurso").attr("aria-valuenow", +Math.round(percentComplete * 100));
                    if (percentComplete === 1) {
                        // $('.progress-bar-recurso').addClass('hide');
                        $('.progress-bar-recurso').css({
                            width: + '100%'
                        });
                        $(".progress-bar-recurso").text("Cargando ... 100%");
                        $(".progress-bar-recurso").attr("aria-valuenow", "100");
                    }
                }
            }, false);
            xhr.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    $('.progress-bar-recurso').css({
                        width: Math.round(percentComplete * 100) + '%'
                    });
                    $(".progress-bar-recurso").text(Math.round(percentComplete * 100) + '%');
                    $(".progress-bar-recurso").attr("aria-valuenow", +Math.round(percentComplete * 100));
                }
            }, false);
            return xhr;
        },
    }).done(function (beanCrudResponse) {
        $('#modalCargandoRecurso').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                document.querySelector("#pageRecurso").value = 1;
                document.querySelector("#sizePageRecurso").value = 5;
                $('#ventanaModalManRecurso').modal('hide');
                addRecurso();
            } else {

                showAlertTopEnd("info", beanCrudResponse.messageServer, "");
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationRecurso = beanCrudResponse.beanPagination;
            listaRecurso(beanPaginationRecurso);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoRecurso').modal("hide");
        showAlertErrorRequest();

    });

}

function addRecurso(recurso = undefined) {
    //LIMPIAR LOS CAMPOS
    document.querySelector('#txtNombreRecurso').value = (recurso == undefined) ? '' : recurso.nombre;
    document.querySelector('#txtDisponibidadRecurso').value = (recurso == undefined) ? '0' : recurso.disponible;

    subtituloSelected = (recurso == undefined) ? undefined : recurso.subtitulo;
    document.querySelector('#txtSubtitulo').value = (recurso == undefined) ? '0' : recurso.subtitulo.codigo;

    if (recurso !== undefined) {

        $("#imagePreview").html(
            `<img width='244' alt='user-picture' class='img-responsive center-box' src='${getHostFrontEnd()}adjuntos/recurso/IMAGENES/${recurso.imagen}' />`
        );

    } else {
        $("#imagePreview").html(
            ``
        );

    }
    addViewArchivosRecursoPrevius();

}

function listaRecurso(beanPagination) {
    document.querySelector('#tbodyRecurso').innerHTML = '';
    document.querySelector('#titleManagerRecurso').innerHTML =
        '[ ' + beanPagination.countFilter + ' ] RECURSOS';
    let row = "", contador = 1;
    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationRecurso'));
        row += `<tr>
        <td class="text-center" colspan="8">NO HAY RECURSOS</td>
        </tr>`;
        document.querySelector('#tbodyRecurso').innerHTML = row;
        return;
    }
    beanPagination.list.forEach((recurso) => {
        row += `<tr idrecurso="${recurso.idrecurso}">
<td class="text-center">${contador++}</td>
<td class="text-center">${recurso.subtitulo.codigo}</td>
<td class="text-center">${recurso.subtitulo.nombre}</td>
<td class="text-center">${recurso.nombre}</td>
<td class="text-center" style="width:15%;"><img src="${getHostFrontEnd()}adjuntos/recurso/IMAGENES/${recurso.imagen}" alt="user-picture" class="img-responsive center-box w-100" ></td>
<td class="text-center">
<button class="btn btn-warning archivos-recurso" ><i class="zmdi zmdi-eye"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-info editar-recurso" ><i class="zmdi zmdi-refresh"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-recurso"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;
    });


    document.querySelector('#tbodyRecurso').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePageRecurso").value),
        document.querySelector("#pageRecurso"),
        $('#modalCargandoRecurso'),
        $('#paginationRecurso'));
    addEventsButtonsRecurso();


}

function addEventsButtonsRecurso() {
    document.querySelectorAll('.editar-recurso').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            recursoSelected = findByRecurso(
                btn.parentElement.parentElement.getAttribute('idrecurso')
            );

            if (recursoSelected != undefined) {
                if (beanPaginationSubtituloC == undefined) {
                    beanRequestSubtituloC.operation = 'obtener';
                    beanRequestSubtituloC.type_request = 'GET';
                    $('#modalCargandoSubtituloC').modal('show');
                    processAjaxSubtituloC(recursoSelected);
                } else {
                    addRecurso(recursoSelected);
                }

                $("#tituloModalManRecurso").html("EDITAR RECURSO");
                $("#ventanaModalManRecurso").modal("show");
                beanRequestRecurso.type_request = 'POST';
                beanRequestRecurso.operation = 'update';
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });

    document.querySelectorAll('.archivos-recurso').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            recursoSelected = findByRecurso(
                btn.parentElement.parentElement.getAttribute('idrecurso')
            );

            if (recursoSelected != undefined) {
                $("#titleManagerDetalle").html('"' + recursoSelected.subtitulo.nombre + '"');
                $("#ModalDetalle").modal("show");
                $('#modalCargandoDetalle').modal('show');
            } else {
                showAlertTopEnd("info", "Vacío!", "No se encontró el recurso");
            }
        };
    });

    document.querySelectorAll('.eliminar-recurso').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            recursoSelected = findByRecurso(
                btn.parentElement.parentElement.getAttribute('idrecurso')
            );

            if (recursoSelected != undefined) {
                beanRequestRecurso.type_request = 'GET';
                beanRequestRecurso.operation = 'delete';
                $('#modalCargandoRecurso').modal('show');
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
}

function addViewArchivosRecursoPrevius() {

    $("#txtImagenRecurso").change(function () {
        filePreview(this, "#imagePreview");
    });
}

function filePreview(input, imagen) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        imagen;
        reader.onload = function (e) {
            $(imagen).html(
                "<img width='100%' alt='user-picture' class='img-responsive center-box' src='" +
                e.target.result +
                "' />"
            );
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function findIndexRecurso(idbusqueda) {
    return beanPaginationRecurso.list.findIndex(
        (Recurso) => {
            if (Recurso.idrecurso == parseInt(idbusqueda))
                return Recurso;


        }
    );
}

function findByRecurso(idrecurso) {
    return beanPaginationRecurso.list.find(
        (Recurso) => {
            if (parseInt(idrecurso) == Recurso.idrecurso) {
                return Recurso;
            }


        }
    );
}

var validarDormularioVideo = () => {
    if (document.querySelector("#txtNombreRecurso").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Nombre",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtSubtitulo").value == "") {
        swal({
            title: "Vacío",
            text: "Selecciona Subtitulo",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtDisponibidadRecurso").value == "") {
        swal({
            title: "Vacío",
            text: "Selecciona Disponibilidad de Archivo",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (beanRequestRecurso.operation == 'add') {
        if (document.querySelector("#txtImagenRecurso").files.length == 0) {
            swal({
                title: "Vacío",
                text: "Ingrese Imagen",
                type: "warning",
                timer: 1200,
                showConfirmButton: false
            });
            return false;
        }
        if (!(document.querySelector("#txtImagenRecurso").files[0].type == "image/png" || document.querySelector("#txtImagenRecurso").files[0].type == "image/jpg" || document.querySelector("#txtImagenRecurso").files[0].type == "image/jpeg")) {
            swal({
                title: "Formato Incorrecto",
                text: "Ingrese formato png, jpeg y jpg",
                type: "warning",
                timer: 1200,
                showConfirmButton: false
            });
            return false;
        }
        //menor a   900 kB
        if (document.querySelector("#txtImagenRecurso").files[0].size > (900 * 1024 * 1024)) {
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
        if (document.querySelector("#txtImagenRecurso").files.length != 0) {
            if (document.querySelector("#txtImagenRecurso").files.length == 0) {
                swal({
                    title: "Vacío",
                    text: "Ingrese Imagen",
                    type: "warning",
                    timer: 1200,
                    showConfirmButton: false
                });
                return false;
            }
            if (!(document.querySelector("#txtImagenRecurso").files[0].type == "image/png" || document.querySelector("#txtImagenRecurso").files[0].type == "image/jpg" || document.querySelector("#txtImagenRecurso").files[0].type == "image/jpeg")) {
                swal({
                    title: "Formato Incorrecto",
                    text: "Ingrese formato png, jpeg y jpg",
                    type: "warning",
                    timer: 1200,
                    showConfirmButton: false
                });
                return false;
            }
            //menor a   900 Kb
            if (document.querySelector("#txtImagenRecurso").files[0].size > (900 * 1024 * 1024)) {
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
    }

    return true;
}