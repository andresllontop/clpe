var beanPaginationAlbum;
var albumSelected;
var subtituloSelected;
var subtituloHastaSelected;
var tipoSelected = 0;
var beanRequestAlbum = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestAlbum.entity_api = 'albums';
    beanRequestAlbum.operation = 'paginate';
    beanRequestAlbum.type_request = 'GET';

    $('#sizePageAlbum').change(function () {
        beanRequestAlbum.type_request = 'GET';
        beanRequestAlbum.operation = 'paginate';
        $('#modalCargandoAlbum').modal('show');
    });
    document.querySelector("#tipoOpcionHeaderCurso").innerHTML = "INICIO ALUMNO";
    $('#modalCargandoCurso_c').modal('show');

    $("#modalCargandoAlbum").on('shown.bs.modal', function () {
        processAjaxAlbum();
    });
    $("#ventanaModalManAlbum").on('hide.bs.modal', function () {
        beanRequestAlbum.type_request = 'GET';
        beanRequestAlbum.operation = 'paginate';
    });

    $("#btnAbrirAlbum").click(function () {
        beanRequestAlbum.operation = 'add';
        beanRequestAlbum.type_request = 'POST';
        $("#videoPreview").html("");
        $("#tituloModalManAlbum").html("REGISTRAR INICIO ALUMNO");

        if (beanPaginationSubtituloC == undefined) {
            beanRequestSubtituloC.operation = 'obtener';
            beanRequestSubtituloC.type_request = 'GET';
            processAjaxSubtituloC(undefined);
        } else {
            addAlbum();
        }
        $("#ventanaModalManAlbum").modal("show");



    });
    $("#formularioAlbum").submit(function (event) {


        if (validarDormularioVideo()) {
            $('#modalCargandoAlbum').modal('show');
        }
        event.preventDefault();
        event.stopPropagation();
    });


    document.querySelector('#txtTipoAlbum').onchange = function (e) {
        if (e.target.value == 1) {
            document.querySelector("#tipoArchivo").innerHTML = ` <div id="imagePreview"></div>
            <input id="txtImagenAlbum" type="file" accept="image/png, image/jpeg, image/jpg"
              class="material-control tooltips-general input-check-user" placeholder="Selecciona Imagen"
              data-toggle="tooltip" data-placement="top" title="" data-original-title="Selecciona la Imagen">
            <span class="highlight"></span>
            <span class="bar"></span>
            <label>Selecciona Imagen</label>
            <small>Tamaño Máximo Permitido: 900 KB</small>
            <br>
            <small>Formatos Permitido:JPG, PNG, JPEG</small>`;
        } else if (e.target.value == 2) {
            document.querySelector("#tipoArchivo").innerHTML = ` <div id="videoPreview"></div>
            <input id="txtVideoAlbum" type="file" accept="video/mp4"
              class="material-control tooltips-general input-check-user" placeholder="Selecciona Video"
              data-toggle="tooltip" data-placement="top" title="" data-original-title="Selecciona la Video">
            <span class="highlight"></span>
            <span class="bar"></span>
            <label>Selecciona Video</label>
            <small>Tamaño Máximo Permitido: 500 MB</small>
            <br>
            <small>Formatos Permitido:MP4</small>`;
        }
        addViewArchivosAlbumPrevius();
    };

    document.querySelector("#txtSubtituloDesde").onchange = function () {
        subtituloSelected = findBySubtituloC(document.querySelector("#txtSubtituloDesde").value);

    };
    document.querySelector("#txtSubtituloHasta").onchange = function () {
        subtituloHastaSelected = findBySubtituloC(document.querySelector("#txtSubtituloHasta").value);

    };
    document.querySelectorAll('.btn-regresar').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            document.querySelector('#cursoHTML').classList.remove("d-none");
            document.querySelector('#seccion-cliente').classList.add("d-none");
        };
    });
});
function addEventsButtonsCurso_c() {
    document.querySelectorAll('.detalle-curso').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            curso_cSelected = findByCurso_c(
                btn.parentElement.parentElement.getAttribute('idlibro')
            );

            if (curso_cSelected != undefined) {
                addClass(
                    document.querySelector("#cursoHTML"), "d-none");
                removeClass(
                    document.querySelector("#seccion-cliente"), "d-none");
                beanRequestAlbum.type_request = 'GET';
                beanRequestAlbum.operation = 'paginate';
                document.querySelector("#titleLibro").innerHTML = curso_cSelected.nombre;
                $('#modalCargandoAlbum').modal('show');
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
    document.querySelectorAll('.detalle-other-curso').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            curso_cSelected = findByCurso_c(
                btn.getAttribute('idlibro')
            );

            if (curso_cSelected != undefined) {
                addClass(
                    document.querySelector("#cursoHTML"), "d-none");
                removeClass(
                    document.querySelector("#seccion-cliente"), "d-none");
                beanRequestAlbum.type_request = 'GET';
                beanRequestAlbum.operation = 'paginate';
                document.querySelector("#titleLibro").innerHTML = curso_cSelected.nombre;
                $('#modalCargandoAlbum').modal('show');
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
}
function processAjaxAlbum() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestAlbum.operation == 'update' ||
        beanRequestAlbum.operation == 'add'
    ) {

        json = {
            desde: subtituloSelected.codigo,
            hasta: subtituloHastaSelected.codigo,
            nombre: document.querySelector('#txtNombreAlbum').value,
            tipo: parseInt(document.querySelector('#txtTipoAlbum').value)
        };


    } else {
        form_data = null;
    }

    switch (beanRequestAlbum.operation) {
        case 'delete':
            parameters_pagination = '?id=' + albumSelected.idalbum;
            break;

        case 'update':
            json.idalbum = albumSelected.idalbum;
            let dataVideo;
            switch (parseInt(document.querySelector('#txtTipoAlbum').value)) {
                case 1:
                    if (document.querySelector("#txtImagenAlbum").files.length != 0) {
                        dataVideo = $("#txtImagenAlbum").prop("files")[0];
                        form_data.append("txtVideoAlbum", dataVideo);
                    }
                    break;
                case 2:
                    if (document.querySelector("#txtVideoAlbum").files.length != 0) {
                        dataVideo = $("#txtVideoAlbum").prop("files")[0];
                        form_data.append("txtVideoAlbum", dataVideo);
                    }
                    break;

                default:
                    break;
            }


            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':

            let dataVideo2;

            switch (parseInt(document.querySelector('#txtTipoAlbum').value)) {
                case 1:
                    dataVideo2 = $("#txtImagenAlbum").prop("files")[0];
                    form_data.append("txtVideoAlbum", dataVideo2);
                    break;
                case 2:
                    dataVideo2 = $("#txtVideoAlbum").prop("files")[0];
                    form_data.append("txtVideoAlbum", dataVideo2);
                    break;

                default:
                    break;
            }
            form_data.append("class", JSON.stringify(json));
            break;

        default:

            parameters_pagination +=
                '?filtro=';
            parameters_pagination +=
                '&libro=' + curso_cSelected.codigo;
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pageAlbum").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePageAlbum").value.trim();
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestAlbum.entity_api + "/" + beanRequestAlbum.operation +
            parameters_pagination,
        type: beanRequestAlbum.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestAlbum.operation == 'update' || beanRequestAlbum.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',
        xhr: function () {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    $('.progress-bar-album').css({
                        width: Math.round(percentComplete * 100) + '%'
                    });
                    $(".progress-bar-album").text(Math.round(percentComplete * 100) + '%');
                    $(".progress-bar-album").attr("aria-valuenow", +Math.round(percentComplete * 100));
                    if (percentComplete === 1) {
                        // $('.progress-bar-parrafo').addClass('hide');
                        $('.progress-bar-album').css({
                            width: + '100%'
                        });
                        $(".progress-bar-album").text("Cargando ... 100%");
                        $(".progress-bar-album").attr("aria-valuenow", "100");
                    }
                }
            }, false);
            xhr.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    $('.progress-bar-parrafo').css({
                        width: Math.round(percentComplete * 100) + '%'
                    });
                    $(".progress-bar-parrafo").text(Math.round(percentComplete * 100) + '%');
                    $(".progress-bar-parrafo").attr("aria-valuenow", +Math.round(percentComplete * 100));
                }
            }, false);
            return xhr;
        },
    }).done(function (beanCrudResponse) {
        $('#modalCargandoAlbum').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                document.querySelector("#pageAlbum").value = 1;
                document.querySelector("#sizePageAlbum").value = 20;
                $('#ventanaModalManAlbum').modal('hide');
            } else {

                showAlertTopEnd("info", beanCrudResponse.messageServer, "");
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationAlbum = beanCrudResponse.beanPagination;
            listaAlbum(beanPaginationAlbum);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoAlbum').modal("hide");
        showAlertErrorRequest();

    });

}

function addAlbum(album = undefined) {
    //LIMPIAR LOS CAMPOS
    subtituloSelected = (album == undefined) ? undefined : { codigo: album.desde };
    document.querySelector('#txtSubtituloDesde').value = (album == undefined) ? '0' : subtituloSelected.codigo;
    subtituloHastaSelected = (album == undefined) ? undefined : { codigo: album.hasta };
    document.querySelector('#txtSubtituloHasta').value = (album == undefined) ? '0' : subtituloHastaSelected.codigo;
    document.querySelector('#txtTipoAlbum').value = (album == undefined) ? 1 : album.tipo;
    document.querySelector('#txtNombreAlbum').value = (album == undefined) ? "" : album.nombre;

    if (album !== undefined) {
        if (album.tipo == 1) {
            document.querySelector("#tipoArchivo").innerHTML = ` <div id="imagePreview"><img style="width:480px;height:300px;" alt="user-picture" class="img-responsive center-box" src="${getHostFrontEnd()}adjuntos/album/${album.video}"/></div>
            <input id="txtImagenAlbum" type="file" accept="image/png, image/jpeg, image/jpg"
              class="material-control tooltips-general input-check-user" placeholder="Selecciona Imagen"
              data-toggle="tooltip" data-placement="top" title="" data-original-title="Selecciona la Imagen">
            <span class="highlight"></span>
            <span class="bar"></span>
            <label>Selecciona Imagen</label>
            <small>Tamaño Máximo Permitido: 900 KB</small>
            <br>
            <small>Formatos Permitido:JPG, PNG, JPEG</small>`;
        } else if (album.tipo == 2) {
            document.querySelector("#tipoArchivo").innerHTML = `<div id="videoPreview"><video width='244' alt='user-picture' class='img-responsive center-box' controls ><source src='${getHostFrontEnd()}adjuntos/album/${album.video}' type='video/mp4'></video></div>
            <input id="txtVideoAlbum" type="file" accept="video/mp4"
              class="material-control tooltips-general input-check-user" placeholder="Selecciona Video"
              data-toggle="tooltip" data-placement="top" title="" data-original-title="Selecciona la Video">
            <span class="highlight"></span>
            <span class="bar"></span>
            <label>Selecciona Video</label>
            <small>Tamaño Máximo Permitido: 500 MB</small>
            <br>
            <small>Formatos Permitido:MP4</small>`;
        }

    } else {
        document.querySelector("#tipoArchivo").innerHTML = ` <div id="imagePreview"></div>
        <input id="txtImagenAlbum" type="file" accept="image/png, image/jpeg, image/jpg"
          class="material-control tooltips-general input-check-user" placeholder="Selecciona Imagen"
          data-toggle="tooltip" data-placement="top" title="" data-original-title="Selecciona la Imagen">
        <span class="highlight"></span>
        <span class="bar"></span>
        <label>Selecciona Imagen</label>
        <small>Tamaño Máximo Permitido: 900 KB</small>
        <br>
        <small>Formatos Permitido:JPG, PNG, JPEG</small>`;

    }
    addViewArchivosAlbumPrevius();

}

function listaAlbum(beanPagination) {
    document.querySelector('#tbodyAlbum').innerHTML = '';
    document.querySelector('#titleManagerAlbum').innerHTML =
        'INICIO ALUMNO';
    let row = "";
    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationAlbum'));
        row += `<tr>
        <td class="text-center" colspan="6">NO HAY ARCHIVOS</td>
        </tr>`;
        document.querySelector('#tbodyAlbum').innerHTML = row;
        return;
    }
    beanPagination.list.forEach((album) => {
        row += `<tr  idalbum="${album.idalbum}">
<td class="text-center">${album.nombre} </td>
<td class="text-center">${album.desde} </td>
<td class="text-center">${album.hasta}</td>
<td class="text-center" >`;
        if (album.tipo == 1) {
            row += `<img  style="width:10em" alt="user-picture" class="img-responsive center-box " src="${getHostFrontEnd()}adjuntos/album/${album.video}"/>`;
        } else {

            row += `<video style="width:10em" class='img-responsive center-box' controls ><source src='${getHostFrontEnd()}adjuntos/album/${album.video}' type='video/mp4'></video>
            `;
        }

        row += `
<td class="text-center">
<button class="btn btn-info editar-album" ><i class="zmdi zmdi-refresh"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-album"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;
    });


    document.querySelector('#tbodyAlbum').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePageAlbum").value),
        document.querySelector("#pageAlbum"),
        $('#modalCargandoAlbum'),
        $('#paginationAlbum'));
    addEventsButtonsAlbum();


}

function addEventsButtonsAlbum() {
    document.querySelectorAll('.editar-album').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            albumSelected = findByAlbum(
                btn.parentElement.parentElement.getAttribute('idalbum')
            );

            if (albumSelected != undefined) {
                if (beanPaginationSubtituloC == undefined) {
                    beanRequestSubtituloC.operation = 'obtener';
                    beanRequestSubtituloC.type_request = 'GET';

                    processAjaxSubtituloC(albumSelected);
                } else {
                    addAlbum(albumSelected);
                }

                $("#tituloModalManAlbum").html("EDITAR INICIO ALUMNO");
                $("#ventanaModalManAlbum").modal("show");
                beanRequestAlbum.type_request = 'POST';
                beanRequestAlbum.operation = 'update';
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });

    document.querySelectorAll('.archivos-album').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            albumSelected = findByAlbum(
                btn.parentElement.parentElement.getAttribute('idalbum')
            );

            if (albumSelected != undefined) {
                $("#titleManagerDetalle").html('"' + albumSelected.subtitulo.nombre + '"');
                $("#ModalDetalle").modal("show");
                $('#modalCargandoDetalle').modal('show');
            } else {
                showAlertTopEnd("info", "Vacío!", "No se encontró el album");
            }
        };
    });

    document.querySelectorAll('.eliminar-album').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            albumSelected = findByAlbum(
                btn.parentElement.parentElement.getAttribute('idalbum')
            );

            if (albumSelected != undefined) {
                beanRequestAlbum.type_request = 'GET';
                beanRequestAlbum.operation = 'delete';
                $('#modalCargandoAlbum').modal('show');
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
}

function addViewArchivosAlbumPrevius() {
    $("#txtImagenAlbum").change(function () {
        filePreview(this, "#imagePreview");
    });
    $("#txtVideoAlbum").change(function () {
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
function filePreview(input, imagen) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();

        reader.onload = function (e) {
            $(imagen).html(
                "<img style='width:480px;height:300px;' alt='user-picture' class='img-responsive center-box' src='" +
                e.target.result +
                "' />"
            );
        };
        reader.readAsDataURL(input.files[0]);
    }
}
function findIndexAlbum(idbusqueda) {
    return beanPaginationAlbum.list.findIndex(
        (Album) => {
            if (Album.idalbum == parseInt(idbusqueda))
                return Album;


        }
    );
}

function findByAlbum(idalbum) {
    return beanPaginationAlbum.list.find(
        (Album) => {
            if (parseInt(idalbum) == Album.idalbum) {
                return Album;
            }


        }
    );
}

var validarDormularioVideo = () => {
    if (subtituloSelected == undefined) {
        swal({
            title: "Vacío",
            text: "Ingrese Subtitulo INICIAL",
            type: "warning",
            timer: 4000,
            showConfirmButton: false
        });
        return false;
    }
    if (subtituloHastaSelected == undefined) {
        swal({
            title: "Vacío",
            text: "Ingrese Subtitulo FINAL",
            type: "warning",
            timer: 4000,
            showConfirmButton: false
        });
        return false;
    }

    if (subtituloSelected.codigo > subtituloHastaSelected.codigo) {
        swal({
            title: "INCORRECTO",
            text: "el subtitulo inicial tiene que ser menor al subtitulo final",
            type: "warning",
            timer: 4000,
            showConfirmButton: false
        });
        return false;
    }
    if (beanRequestAlbum.operation == 'add') {
        switch (parseInt(document.querySelector('#txtTipoAlbum').value)) {
            case 1:
                /*IMAGEN */
                if (document.querySelector("#txtImagenAlbum").files.length == 0) {
                    showAlertTopEnd("info", "Vacío", "ingrese Imagen");
                    return false;
                }
                if (!(document.querySelector("#txtImagenAlbum").files[0].type == "image/png" || document.querySelector("#txtImagenAlbum").files[0].type == "image/jpg" || document.querySelector("#txtImagenAlbum").files[0].type == "image/jpeg")) {
                    showAlertTopEnd("info", "Formato Incorrecto", "Ingrese tipo de arhivo Imagen => png,jpg,jpeg");
                    return false;
                }
                //menor a   1700 KB
                if (document.querySelector("#txtImagenAlbum").files[0].size > (1700 * 1024)) {
                    showAlertTopEnd("info", "Tamaño excedido", "el tamaño del archivo tiene que ser menor a 1700 KB");
                    return false;
                }
                break;
            case 2:
                if (document.querySelector("#txtVideoAlbum").files.length == 0) {
                    swal({
                        title: "Vacío",
                        text: "Ingrese Video",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }
                if (!(document.querySelector("#txtVideoAlbum").files[0].type == "video/mp4")) {
                    swal({
                        title: "Formato Incorrecto",
                        text: "Ingrese formatoMP4",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }
                //menor a   500 MB
                if (document.querySelector("#txtVideoAlbum").files[0].size > (500 * 1024 * 1024)) {
                    swal({
                        title: "Tamaño excedido",
                        text: "el tamaño del archivo tiene que ser menor a 500 MB",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }

                break;

            default:
                break;
        }

    } else {
        switch (parseInt(document.querySelector('#txtTipoAlbum').value)) {
            case 1:
                if (document.querySelector("#txtImagenAlbum").files.length != 0) {
                    /*IMAGEN */
                    if (document.querySelector("#txtImagenAlbum").files.length == 0) {
                        showAlertTopEnd("info", "Vacío", "ingrese Imagen");
                        return false;
                    }
                    if (!(document.querySelector("#txtImagenAlbum").files[0].type == "image/png" || document.querySelector("#txtImagenAlbum").files[0].type == "image/jpg" || document.querySelector("#txtImagenAlbum").files[0].type == "image/jpeg")) {
                        showAlertTopEnd("info", "Formato Incorrecto", "Ingrese tipo de arhivo Imagen => png,jpg,jpeg");
                        return false;
                    }
                    //menor a   1700 KB
                    if (document.querySelector("#txtImagenAlbum").files[0].size > (1700 * 1024)) {
                        showAlertTopEnd("info", "Tamaño excedido", "el tamaño del archivo tiene que ser menor a 1700 KB");
                        return false;
                    }
                }
                break;
            case 2:
                if (document.querySelector("#txtVideoAlbum").files.length != 0) {
                    if (document.querySelector("#txtVideoAlbum").files.length == 0) {
                        swal({
                            title: "Vacío",
                            text: "Ingrese Video",
                            type: "warning",
                            timer: 1200,
                            showConfirmButton: false
                        });
                        return false;
                    }
                    if (!(document.querySelector("#txtVideoAlbum").files[0].type == "video/mp4")) {
                        swal({
                            title: "Formato Incorrecto",
                            text: "Ingrese formato MP4",
                            type: "warning",
                            timer: 1200,
                            showConfirmButton: false
                        });
                        return false;
                    }
                    //menor a   500 Mb
                    if (document.querySelector("#txtVideoAlbum").files[0].size > (500 * 1024 * 1024)) {
                        swal({
                            title: "Tamaño excedido",
                            text: "el tamaño del archivo tiene que ser menor a 500 mB",
                            type: "warning",
                            timer: 1200,
                            showConfirmButton: false
                        });
                        return false;
                    }
                }
                break;

            default:
                break;
        }

    }

    return true;
}