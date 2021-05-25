var beanPaginationSubtitulo;
var subtituloSelected;
var beanRequestSubtitulo = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestSubtitulo.entity_api = 'subtitulos';
    beanRequestSubtitulo.operation = 'paginate';
    beanRequestSubtitulo.type_request = 'GET';

    $('#sizePageSubtitulo').change(function () {
        beanRequestSubtitulo.type_request = 'GET';
        beanRequestSubtitulo.operation = 'paginate';
        $('#modalCargandoSubtitulo').modal('show');

    });


    $("#modalCargandoSubtitulo").on('shown.bs.modal', function () {
        processAjaxSubtitulo();
    });
    $("#modalCargandoSubtitulo").on('hide.bs.modal', function () {
        $(".progress-bar-subtitulo").text("Cargando ... 0%");
        $(".progress-bar-subtitulo").attr("aria-valuenow", "0");
        $(".progress-bar-subtitulo").css("width", "0%");
    });

    $("#ventanaModalManSubtitulo").on('hide.bs.modal', function () {
        beanRequestSubtitulo.operation = 'paginate';
        beanRequestSubtitulo.type_request = 'GET';

    });
    $("#btnAbrirsubtitulo").click(function () {
        beanRequestSubtitulo.operation = 'add';
        beanRequestSubtitulo.type_request = 'POST';
        $("#imagePreview").html("");
        $("#tituloModalManSubtitulo").html("REGISTRAR SUBTÍTULO");
        addSubtitulo();
        $("#ventanaModalManSubtitulo").modal("show");
    });


    $("#formularioSubtitulo").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (validarDormularioSubtitulo()) {
            $('#modalCargandoSubtitulo').modal('show');
        }
    });

});

function processAjaxSubtitulo() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestSubtitulo.operation == 'update' ||
        beanRequestSubtitulo.operation == 'add'
    ) {

        json = {
            nombre: document.querySelector("#txtNombreSubtitulo").value,
            codigo: capituloSelected.codigo + "." + document.querySelector("#txtCodigoSubtitulo").value,
            descripcion: "",
            titulo: capituloSelected.idtitulo,
            estado: 1
        };


    } else {
        form_data = null;
    }

    switch (beanRequestSubtitulo.operation) {
        case 'delete':
            parameters_pagination = '?id=' + subtituloSelected.idsubTitulo;
            break;

        case 'update':
            json.idsubTitulo = subtituloSelected.idsubTitulo;
            if (document.querySelector("#txtPdfSubtitulo").files.length != 0) {
                let dataImagen = $("#txtPdfSubtitulo").prop("files")[0];
                form_data.append("txtPdfSubtitulo", dataImagen);
            }


            if (document.querySelector("#txtImagenSubtituloLeccion").files.length != 0) {
                let dataImagen3 = $("#txtImagenSubtituloLeccion").prop("files")[0];
                form_data.append("txtImagenSubtituloLeccion", dataImagen3);
            }

            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':

            let dataFot = $("#txtPdfSubtitulo").prop("files")[0];
            form_data.append("txtPdfSubtitulo", dataFot);
            let dataFot3 = $("#txtImagenSubtituloLeccion").prop("files")[0];
            form_data.append("txtImagenSubtituloLeccion", dataFot3);

            form_data.append("class", JSON.stringify(json));
            break;

        default:

            parameters_pagination +=
                '?filtro=&capitulo=' + capituloSelected.idtitulo;
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pageSubtitulo").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePageSubtitulo").value.trim();
            break;
    }

    $.ajax({
        url: getHostAPI() + beanRequestSubtitulo.entity_api + "/" + beanRequestSubtitulo.operation +
            parameters_pagination,
        type: beanRequestSubtitulo.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestSubtitulo.operation == 'update' || beanRequestSubtitulo.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json', xhr: function () {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    $('.progress-bar-subtitulo').css({
                        width: Math.round(percentComplete * 100) + '%'
                    });
                    $(".progress-bar-subtitulo").text(Math.round(percentComplete * 100) + '%');
                    $(".progress-bar-subtitulo").attr("aria-valuenow", +Math.round(percentComplete * 100));
                    if (percentComplete === 1) {
                        // $('.progress-bar-subtitulo').addClass('hide');
                        $('.progress-bar-subtitulo').css({
                            width: + '100%'
                        });
                        $(".progress-bar-subtitulo").text("Cargando ... 100%");
                        $(".progress-bar-subtitulo").attr("aria-valuenow", "100");
                    }
                }
            }, false);
            xhr.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    $('.progress-bar-subtitulo').css({
                        width: Math.round(percentComplete * 100) + '%'
                    });
                    $(".progress-bar-subtitulo").text(Math.round(percentComplete * 100) + '%');
                    $(".progress-bar-subtitulo").attr("aria-valuenow", +Math.round(percentComplete * 100));
                }
            }, false);
            return xhr;
        },
    }).done(function (beanCrudResponse) {

        $('#modalCargandoSubtitulo').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                swal({
                    title: "Realizado",
                    text: "Acción realizada existosamente!",
                    type: "success",
                    timer: 1200,
                    showConfirmButton: false
                });
                document.querySelector("#pageSubtitulo").value = 1;
                document.querySelector("#sizePageSubtitulo").value = 20;
                $('#ventanaModalManSubtitulo').modal('hide');
            } else {

                swal({
                    title: "Error",
                    text: beanCrudResponse.messageServer,
                    type: "error",
                    timer: 1200,
                    showConfirmButton: false
                });
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationSubtitulo = beanCrudResponse.beanPagination;
            listaSubtitulo(beanPaginationSubtitulo);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoSubtitulo').modal("hide");
        showAlertErrorRequest();
        console.log(textStatus);

    });

}

function addSubtitulo(subtitulo = undefined) {
    //LIMPIAR LOS CAMPOS

    document.querySelector('#txtSubtituloCapitulo').value = capituloSelected.nombre;
    document.querySelector('#inputPrependSubtitulo').innerHTML = capituloSelected.codigo + ".";
    document.querySelector('#txtCodigoSubtitulo').value = (subtitulo == undefined) ? '' : subtitulo.codigo.substring(capituloSelected.codigo.length + 1);

    document.querySelector('#txtNombreSubtitulo').value = (subtitulo == undefined) ? '' : subtitulo.nombre;

    if (subtitulo !== undefined) {

        $("#imagePreviewSubtituloLeccion").html(
            `<img style="width: 100%;height: 133px;" alt='user-picture' class='img-responsive center-box' src='${getHostFrontEnd()}adjuntos/libros/subtitulos/${subtitulo.imagen}' />`
        );
        $("#imagePreviewSubtitulo").html(
            `<img style="width: 100%;height: 133px;" alt='user-picture' class='img-responsive center-box' src='${getHostFrontEnd()}adjuntos/libros/subtitulos/${subtitulo.descripcion}' />`
        );



    } else {

        $("#imagePreviewSubtituloLeccion").html(
            ""
        );
        $("#imagePreviewSubtitulo").html(
            ""
        );


    }

    addViewArchivosPreviusSubtitulo();

}

function listaSubtitulo(beanPagination) {
    document.querySelector('#tbodySubtitulo').innerHTML = '';
    document.querySelector('#titleManagerSubtitulo').innerHTML =
        'LISTA DE SUBTÍTULOS';
    let row = "", header, contador = 1;
    header = `
    <button class="btn btn-danger btnregresarSubtitulo">
    <i class="zmdi zmdi-long-arrow-left mr-1"></i> Regresar
  </button>`;
    document.querySelector('#listaLibroCapitulos').innerHTML = header;
    if (beanPagination.list.length == 0) {
        addEventsButtonsSubtituloRegreso();
        destroyPagination($('#paginationSubtitulo'));
        row += `<tr>
        <td class="text-center" colspan="8">NO HAY SUBTITULOS</td>
        </tr>`;

        document.querySelector('#tbodySubtitulo').innerHTML += row;
        return;
    }
    beanPagination.list.forEach((subtitulo) => {

        row += `<tr  idsubTitulo="${subtitulo.idsubTitulo}">
<td class="text-center ver-parrafo aula-cursor-mano">${contador++}</td>
<td class="text-center ver-parrafo aula-cursor-mano">${subtitulo.codigo}</td>
<td class="text-center ver-parrafo aula-cursor-mano">${subtitulo.nombre}</td>
<td class="text-center ver-parrafo aula-cursor-mano">${capituloSelected.nombre}</td>
<td class="text-center ver-parrafo" style="width:10%;"><img src="${getHostFrontEnd()}adjuntos/libros/subtitulos/${subtitulo.imagen}" alt="user-picture" class="img-responsive center-box" width="100%"></td>
<td class="text-center">
<button class="btn btn-warning descargar-archivo" ><i class="zmdi zmdi-download"></i></button>
</td>
<td class="text-center">
<button class="btn btn-info editar-subtitulo" ><i class="zmdi zmdi-refresh"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-subtitulo"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;

        // $('[data-toggle="tooltip"]').tooltip();
    });


    document.querySelector('#tbodySubtitulo').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePageSubtitulo").value),
        document.querySelector("#pageSubtitulo"),
        $('#modalCargandoSubtitulo'),
        $('#paginationSubtitulo'));
    addEventsButtonsSubtitulo();


}

function addEventsButtonsSubtitulo() {

    document.querySelectorAll('.ver-parrafo').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            subtituloSelected = findBySubtitulo(
                btn.parentElement.getAttribute('idsubTitulo')
            );

            if (subtituloSelected != undefined) {

                document.querySelector("#seccion-subtitulo").classList.add("d-none");
                document.querySelector("#seccion-parrafo").classList.remove("d-none");
                beanRequestParrafo.type_request = 'GET';
                beanRequestParrafo.operation = 'paginate';
                $('#modalCargandoParrafo').modal('show');
            } else {
                console.log(
                    'warning => ',
                    'No se encontró el capitulo para poder ver'
                );
            }
        };
    });
    document.querySelectorAll('.descargar-archivo').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            subtituloSelected = findBySubtitulo(
                btn.parentElement.parentElement.getAttribute('idsubTitulo')
            );

            if (subtituloSelected != undefined) {
                $("#modalFrameSubtitulo").modal("show");
                downloadURL(getHostFrontEnd() + "adjuntos/archivos/" + capituloSelected.libro.codigo + "/" + capituloSelected.codigo + "/PDF/" + subtituloSelected.pdf);


            } else {
                console.log(
                    'warning => ',
                    'No se encontró el capitulo para poder ver'
                );
            }
        };
    });

    document.querySelectorAll('.editar-subtitulo').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            subtituloSelected = findBySubtitulo(
                btn.parentElement.parentElement.getAttribute('idsubTitulo')
            );

            if (subtituloSelected != undefined) {
                addSubtitulo(subtituloSelected);
                $("#tituloModalManSubtitulo").html("EDITAR SUBTÍTULO");
                $("#ventanaModalManSubtitulo").modal("show");
                beanRequestSubtitulo.type_request = 'POST';
                beanRequestSubtitulo.operation = 'update';
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
    document.querySelectorAll('.eliminar-subtitulo').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            subtituloSelected = findBySubtitulo(
                btn.parentElement.parentElement.getAttribute('idsubTitulo')
            );

            if (subtituloSelected != undefined) {
                beanRequestSubtitulo.type_request = 'GET';
                beanRequestSubtitulo.operation = 'delete';
                $('#modalCargandoSubtitulo').modal('show');
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
    addEventsButtonsSubtituloRegreso();

}

function addEventsButtonsSubtituloRegreso() {

    document.querySelectorAll('.btnregresarSubtitulo').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            document.querySelector('#listaLibroCapitulos').innerHTML = `
            <button class="btn btn-danger btnregresarCapitulo">
            <i class="zmdi zmdi-long-arrow-left mr-1"></i> Regresar
          </button>`;
            document.querySelector("#seccion-subtitulo").classList.add("d-none");
            document.querySelector("#seccion-capitulo").classList.remove("d-none");
            addEventsButtonsCapituloRegreso();
        };
    });

}

function addViewArchivosPreviusSubtitulo() {



    $("#txtImagenSubtituloLeccion").change(function () {
        filePreview(this, "#imagePreviewSubtituloLeccion");
    });
}

function findIndexSubtitulo(idbusqueda) {
    return beanPaginationSubtitulo.list.findIndex(
        (Subtitulo) => {
            if (Subtitulo.idsubTitulo == parseInt(idbusqueda))
                return Subtitulo;


        }
    );
}

function findBySubtitulo(idsubTitulo) {
    return beanPaginationSubtitulo.list.find(
        (Subtitulo) => {
            if (parseInt(idsubTitulo) == Subtitulo.idsubTitulo) {
                return Subtitulo;
            }


        }
    );
}

var validarDormularioSubtitulo = () => {
    if (document.querySelector("#txtNombreSubtitulo").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Nombre del SubTitulo",
            type: "warning",
            timer: 800,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtCodigoSubtitulo").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Código",
            type: "warning",
            timer: 800,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtCodigoSubtitulo").value.length !== 2) {
        swal({
            title: "Formato Incorrecto",
            text: "Por favor ingrese 2 números al código",
            type: "warning",
            timer: 800,
            showConfirmButton: false
        });
        return false;
    }
    if (beanRequestSubtitulo.operation == 'add') {
        if (document.querySelector("#txtPdfSubtitulo").files.length == 0) {
            swal({
                title: "Vacío",
                text: "Ingrese PDF",
                type: "warning",
                timer: 1000,
                showConfirmButton: false
            });
            return false;
        }
        if (document.querySelector("#txtPdfSubtitulo").files[0].type !== "application/pdf") {
            swal({
                title: "Formato Incorrecto",
                text: "Ingrese tipo de arhivo pdf",
                type: "warning",
                timer: 1000,
                showConfirmButton: false
            });
            return false;
        }
        //menor a   5120 KB
        if (document.querySelector("#txtPdfSubtitulo").files[0].size > (5 * 1024 * 1024)) {
            swal({
                title: "Tamaño excedido",
                text: "el tamaño del archivo tiene que ser menor a 5120 KB",
                type: "warning",
                timer: 1000,
                showConfirmButton: false
            });
            return false;
        }


        /*IMAGEN */
        if (document.querySelector("#txtImagenSubtituloLeccion").files.length == 0) {
            showAlertTopEnd("info", "Vacío", "ingrese Imagen");
            return false;
        }
        if (!(document.querySelector("#txtImagenSubtituloLeccion").files[0].type == "image/png" || document.querySelector("#txtImagenSubtituloLeccion").files[0].type == "image/jpg" || document.querySelector("#txtImagenSubtituloLeccion").files[0].type == "image/jpeg")) {
            showAlertTopEnd("info", "Formato Incorrecto", "Ingrese tipo de arhivo Imagen => png,jpg,jpeg");
            return false;
        }
        //menor a   1700 KB
        if (document.querySelector("#txtImagenSubtituloLeccion").files[0].size > (1700 * 1024)) {
            showAlertTopEnd("info", "Tamaño excedido", "el tamaño del archivo tiene que ser menor a 1700 KB");
            return false;
        }


    } else {
        if (document.querySelector("#txtPdfSubtitulo").files.length != 0) {
            if (document.querySelector("#txtPdfSubtitulo").files[0].type !== "application/pdf") {
                swal({
                    title: "Formato Incorrecto",
                    text: "Ingrese tipo de arhivo pdf",
                    type: "warning",
                    timer: 800,
                    showConfirmButton: false
                });
                return false;
            }
            //menor a   5 MB
            if (document.querySelector("#txtPdfSubtitulo").files[0].size > (5 * 1024 * 1024)) {
                swal({
                    title: "Tamaño excedido",
                    text: "el tamaño del archivo tiene que ser menor a 5120 KB",
                    type: "warning",
                    timer: 800,
                    showConfirmButton: false
                });
                return false;
            }

        }

        if (document.querySelector("#txtImagenSubtituloLeccion").files.length != 0) {
            if (!(document.querySelector("#txtImagenSubtituloLeccion").files[0].type == "image/png" || document.querySelector("#txtImagenSubtituloLeccion").files[0].type == "image/jpg" || document.querySelector("#txtImagenSubtituloLeccion").files[0].type == "image/jpeg")) {
                showAlertTopEnd("info", "Formato Incorrecto", "Ingrese tipo de arhivo Imagen => png,jpg,jpeg");
                return false;
            }
            //menor a   1700 KB
            if (document.querySelector("#txtImagenSubtituloLeccion").files[0].size > (1700 * 1024)) {
                showAlertTopEnd("info", "Tamaño excedido", "el tamaño del archivo tiene que ser menor a 1700 KB");
                return false;
            }
        }
    }

    return true;
}