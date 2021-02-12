var beanPaginationCuestionario;
var cuestionarioSelected;
var subtituloSelected;
var beanRequestCuestionario = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestCuestionario.entity_api = 'cuestionarios';
    beanRequestCuestionario.operation = 'paginate';
    beanRequestCuestionario.type_request = 'GET';

    $('#sizePageCuestionario').change(function () {
        beanRequestCuestionario.type_request = 'GET';
        beanRequestCuestionario.operation = 'paginate';
        $('#modalCargandoCuestionario').modal('show');
    });

    $('#modalCargandoCuestionario').modal('show');

    $("#modalCargandoCuestionario").on('shown.bs.modal', function () {
        processAjaxCuestionario();
    });
    $("#ventanaModalManCuestionario").on('hide.bs.modal', function () {
        beanRequestCuestionario.type_request = 'GET';
        beanRequestCuestionario.operation = 'paginate';
    });

    $("#btnAbrirCuestionario").click(function () {
        beanRequestCuestionario.operation = 'add';
        beanRequestCuestionario.type_request = 'POST';
        $("#imagePreview").html("");
        $("#tituloModalManCuestionario").html("REGISTRAR RESTRICCION");
        addCuestionario();
        $("#ventanaModalManCuestionario").modal("show");


    });
    $("#formularioCuestionario").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (validarDormularioVideo()) {
            $('#modalCargandoCuestionario').modal('show');
        }
    });

});

function processAjaxCuestionario() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestCuestionario.operation == 'update' ||
        beanRequestCuestionario.operation == 'add'
    ) {

        json = {
            nombre: document.querySelector("#txtNombreCuestionario").value,
            tipo: parseInt(document.querySelector("#txtTipoArchivoCuestionario").value),
            estado: parseInt(document.querySelector("#txtDisponibidadCuestionario").value),
            subTitulo: subtituloSelected.codigo

        };


    } else {
        form_data = null;
    }

    switch (beanRequestCuestionario.operation) {
        case 'delete':
            parameters_pagination = '?id=' + cuestionarioSelected.idcuestionario;
            break;

        case 'update':
            json.idcuestionario = cuestionarioSelected.idcuestionario;
            let dataImagen;
            switch (parseInt(document.querySelector("#txtTipoArchivoCuestionario").value)) {
                case 1:
                    if (document.querySelector("#txtImagenCuestionario").files.length != 0) {
                        dataImagen = $("#txtImagenCuestionario").prop("files")[0];
                        form_data.append("txtImagenCuestionario", dataImagen);
                    }

                    break;
                case 2:
                    if (document.querySelector("#txtVideoCuestionario").files.length != 0) {
                        dataImagen = $("#txtVideoCuestionario").prop("files")[0];
                        form_data.append("txtVideoCuestionario", dataImagen);
                    }

                    break;

                default:
                    if (document.querySelector("#txtPdfCuestionario").files.length != 0) {
                        dataImagen = $("#txtPdfCuestionario").prop("files")[0];
                        form_data.append("txtPdfCuestionario", dataImagen);
                    }

                    break;
            }

            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':

            let dataImagen2;
            switch (parseInt(document.querySelector("#txtTipoArchivoCuestionario").value)) {
                case 1:

                    dataImagen2 = $("#txtImagenCuestionario").prop("files")[0];
                    form_data.append("txtImagenCuestionario", dataImagen2);
                    break;
                case 2:
                    dataImagen2 = $("#txtVideoCuestionario").prop("files")[0];
                    form_data.append("txtVideoCuestionario", dataImagen2);
                    break;

                default:
                    dataImagen2 = $("#txtPdfCuestionario").prop("files")[0];
                    form_data.append("txtPdfCuestionario", dataImagen2);
                    break;
            }
            form_data.append("class", JSON.stringify(json));
            break;

        default:

            parameters_pagination +=
                '?filtro=';
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pageCuestionario").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePageCuestionario").value.trim();
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestCuestionario.entity_api + "/" + beanRequestCuestionario.operation +
            parameters_pagination,
        type: beanRequestCuestionario.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },

        data: form_data,
        cache: false,
        contentType: ((beanRequestCuestionario.operation == 'update' || beanRequestCuestionario.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',
    }).done(function (beanCrudResponse) {

        $('#modalCargandoCuestionario').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                document.querySelector("#pageCuestionario").value = 1;
                document.querySelector("#sizePageCuestionario").value = 5;
                $('#ventanaModalManCuestionario').modal('hide');
            } else {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationCuestionario = beanCrudResponse.beanPagination;
            listaCuestionario(beanPaginationCuestionario);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoCuestionario').modal("hide");
        showAlertErrorRequest();

    });

}

function addCuestionario(cuestionario = undefined) {
    //LIMPIAR LOS CAMPOS

    document.querySelector('#txtNombreCuestionario').value = (cuestionario == undefined) ? '' : cuestionario.nombre;

    document.querySelector('#txtDisponibidadCuestionario').value = (cuestionario == undefined) ? '0' : cuestionario.estado;
    document.querySelector('#txtTipoArchivoCuestionario').value = (cuestionario == undefined) ? '1' : cuestionario.tipo;
    subtituloSelected = (cuestionario == undefined) ? undefined : cuestionario.subTitulo;
    document.querySelector('#txtSubTituloCuestionario').value = (cuestionario == undefined) ? '' : cuestionario.subTitulo.codigo + " - " + cuestionario.subTitulo.nombre;

    if (cuestionario !== undefined) {
        switch (parseInt(cuestionario.tipo)) {
            case 1:
                document.querySelector('#Tipo-Archivo').innerHTML = `
                <div id="imagePreview"><img width='244' alt='user-picture' class='img-responsive center-box' src='${getHostFrontEnd()}adjuntos/cuestionario/IMAGENES/${cuestionario.archivo}' /></div>
                <input id="txtImagenCuestionario" type="file" accept="image/png, image/jpeg, image/jpg" class="material-control tooltips-general input-check-user"
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
                <div id="videoPreview"><video width='100%' alt='user-picture' class='img-responsive center-box' controls ><source src='${getHostFrontEnd()}adjuntos/cuestionario/VIDEOS/${cuestionario.archivo}' type='video/mp4'></video></div>
                <input id="txtVideoCuestionario" type="file" accept="video/mp4"class="material-control tooltips-general input-check-user"
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
                <input id="txtPdfCuestionario" type="file" accept="application/pdf" class="material-control tooltips-general input-check-user"
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
        <div id="imagePreview"> </div>
        <input id="txtImagenCuestionario" type="file" class="material-control tooltips-general input-check-user" accept="image/png, image/jpeg, image/png"
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

function listaCuestionario(beanPagination) {
    document.querySelector('#tbodyCuestionario').innerHTML = '';
    document.querySelector('#titleManagerCuestionario').innerHTML =
        '[ ' + beanPagination.countFilter + ' ] RESTRICCIONES';
    let row = "";
    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationCuestionario'));
        row += `<tr>
        <td class="text-center" colspan="3">NO HAY CUESTIONARIOS</td>
        </tr>`;

        document.querySelector('#tbodyCuestionario').innerHTML += row;
        return;
    }
    beanPagination.list.forEach((cuestionario) => {

        row += `<tr  idcuestionario="${cuestionario.idcuestionario}">
<td class="text-center">${cuestionario.subTitulo.nombre} <p>${cuestionario.subTitulo.codigo}</p></td>
<td class="text-center">${cuestionario.nombre}</td>
`;

        if (cuestionario.tipo == 1) {
            //imagen
            row += `
            <td class="text-center" style="width:26%;"><img src="${getHostFrontEnd()}adjuntos/cuestionario/IMAGENES/${cuestionario.archivo}" alt="user-picture" class="img-responsive center-box" ><div class="imag">${cuestionario.archivo}</div></td>
            `;
        } else if (cuestionario.tipo == 2) {
            //video
            row += ` <td class="text-center" style="width:26%;">
            <video width='100%' alt='user-picture' class='img-responsive center-box' controls ><source src='${getHostFrontEnd()}adjuntos/cuestionario/VIDEOS/${cuestionario.archivo}' type='video/mp4'></video><div class="imag">${cuestionario.archivo}</div></td>
            `;
        } else {
            //archivo pdf
            row += `
            <td class="text-center" style="width:26%;">
            <button class="btn btn-warning descargar-archivo" ><i class="zmdi zmdi-download"></i></button><div class="imag">${cuestionario.archivo}</div>
            </td>
            `;
        }


        row += `
<td class="text-center">
<button class="btn btn-info editar-cuestionario" ><i class="zmdi zmdi-refresh"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-cuestionario"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;

        // $('[data-toggle="tooltip"]').tooltip();
    });


    document.querySelector('#tbodyCuestionario').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePageCuestionario").value),
        document.querySelector("#pageCuestionario"),
        $('#modalCargandoCuestionario'),
        $('#paginationCuestionario'));
    addEventsButtonsAdmin();


}

function addEventsButtonsAdmin() {
    document.querySelectorAll('.descargar-archivo').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            cuestionarioSelected = findByCuestionario(
                btn.parentElement.parentElement.getAttribute('idcuestionario')
            );
            if (cuestionarioSelected != undefined) {
                $("#modalFrameCuestionario").modal("show");
                downloadURL(getHostFrontEnd() + "adjuntos/cuestionario/PDF/" + cuestionarioSelected.archivo);


            } else {
                console.log(
                    'warning => ',
                    'No se encontró el capitulo para poder ver'
                );
            }
        };
    });

    document.querySelectorAll('.editar-cuestionario').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            cuestionarioSelected = findByCuestionario(
                btn.parentElement.parentElement.getAttribute('idcuestionario')
            );

            if (cuestionarioSelected != undefined) {
                addCuestionario(cuestionarioSelected);
                $("#tituloModalManCuestionario").html("EDITAR RESTRICCIONES");
                $("#ventanaModalManCuestionario").modal("show");
                beanRequestCuestionario.type_request = 'POST';
                beanRequestCuestionario.operation = 'update';
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
    document.querySelectorAll('.eliminar-cuestionario').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            cuestionarioSelected = findByCuestionario(
                btn.parentElement.parentElement.getAttribute('idcuestionario')
            );

            if (cuestionarioSelected != undefined) {
                beanRequestCuestionario.type_request = 'GET';
                beanRequestCuestionario.operation = 'delete';
                $('#modalCargandoCuestionario').modal('show');
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

    $("#txtImagenCuestionario").change(function () {
        filePreview(this, "#imagePreview");
    });
    $("#txtVideoCuestionario").change(function () {
        videoPreview(this, "#videoPreview");
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
        document.querySelector('#modalFrameContenidoCuestionario').appendChild(iframe);
    }
    iframe.src = url;
};

function findIndexCuestionario(idbusqueda) {
    return beanPaginationCuestionario.list.findIndex(
        (Cuestionario) => {
            if (Cuestionario.idcuestionario == parseInt(idbusqueda))
                return Cuestionario;


        }
    );
}

function findByCuestionario(idcuestionario) {
    return beanPaginationCuestionario.list.find(
        (Cuestionario) => {
            if (parseInt(idcuestionario) == Cuestionario.idcuestionario) {
                return Cuestionario;
            }


        }
    );
}

var validarDormularioVideo = () => {
    if (document.querySelector("#txtNombreCuestionario").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Nombre",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtSubTituloCuestionario").value == "") {
        swal({
            title: "Vacío",
            text: "Selecciona Subtitulo",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtDisponibidadCuestionario").value == "") {
        swal({
            title: "Vacío",
            text: "Selecciona Disponibilidad de Archivo",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtTipoArchivoCuestionario").value == "") {
        swal({
            title: "Vacío",
            text: "Selecciona Tipo de Archivo",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (beanRequestCuestionario.operation == 'add') {

        switch (parseInt(document.querySelector("#txtTipoArchivoCuestionario").value)) {
            case 1:
                if (document.querySelector("#txtImagenCuestionario").files.length == 0) {
                    swal({
                        title: "Vacío",
                        text: "Ingrese Imagen",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }
                if (!(document.querySelector("#txtImagenCuestionario").files[0].type == "image/png" || document.querySelector("#txtImagenCuestionario").files[0].type == "image/jpg" || document.querySelector("#txtImagenCuestionario").files[0].type == "image/jpeg")) {
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
                if (document.querySelector("#txtImagenCuestionario").files[0].size > (4 * 1024 * 1024)) {
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
            case 2:
                //video
                if (document.querySelector("#txtVideoCuestionario").files.length == 0) {
                    swal({
                        title: "Vacío",
                        text: "Ingrese Video",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }
                if (document.querySelector("#txtVideoCuestionario").files[0].type !== "video/mp4") {
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
                if (document.querySelector("#txtVideoCuestionario").files[0].size > (17 * 1024 * 1024)) {
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

            default:
                //PDF
                if (document.querySelector("#txtPdfCuestionario").files.length == 0) {
                    swal({
                        title: "Vacío",
                        text: "Ingrese PDF",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }
                if (document.querySelector("#txtPdfCuestionario").files[0].type !== "application/pdf") {
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
                if (document.querySelector("#txtPdfCuestionario").files[0].size > (5 * 1024 * 1024)) {
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

        switch (parseInt(document.querySelector("#txtTipoArchivoCuestionario").value)) {
            case 1:
                if (document.querySelector("#txtImagenCuestionario").files.length != 0) {
                    if (document.querySelector("#txtImagenCuestionario").files.length == 0) {
                        swal({
                            title: "Vacío",
                            text: "Ingrese Imagen",
                            type: "warning",
                            timer: 1200,
                            showConfirmButton: false
                        });
                        return false;
                    }
                    if (!(document.querySelector("#txtImagenCuestionario").files[0].type == "image/png" || document.querySelector("#txtImagenCuestionario").files[0].type == "image/jpg" || document.querySelector("#txtImagenCuestionario").files[0].type == "image/jpeg")) {
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
                    if (document.querySelector("#txtImagenCuestionario").files[0].size > (4 * 1024 * 1024)) {
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
                if (document.querySelector("#txtVideoCuestionario").files.length != 0) {  //video
                    if (document.querySelector("#txtVideoCuestionario").files.length == 0) {
                        swal({
                            title: "Vacío",
                            text: "Ingrese Video",
                            type: "warning",
                            timer: 1200,
                            showConfirmButton: false
                        });
                        return false;
                    }
                    if (document.querySelector("#txtVideoCuestionario").files[0].type !== "video/mp4") {
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
                    if (document.querySelector("#txtVideoCuestionario").files[0].size > (17 * 1024 * 1024)) {
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
                if (document.querySelector("#txtPdfCuestionario").files.length != 0) {  //PDF
                    if (document.querySelector("#txtPdfCuestionario").files.length == 0) {
                        swal({
                            title: "Vacío",
                            text: "Ingrese PDF",
                            type: "warning",
                            timer: 1200,
                            showConfirmButton: false
                        });
                        return false;
                    }
                    if (document.querySelector("#txtPdfCuestionario").files[0].type !== "application/pdf") {
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
                    if (document.querySelector("#txtPdfCuestionario").files[0].size > (5 * 1024 * 1024)) {
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