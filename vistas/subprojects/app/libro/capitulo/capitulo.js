var beanPaginationCapitulo;
var capituloSelected;
var beanRequestCapitulo = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestCapitulo.entity_api = 'capitulos';
    beanRequestCapitulo.operation = 'paginate';
    beanRequestCapitulo.type_request = 'GET';

    $('#sizePageCapitulo').change(function () {
        beanRequestCapitulo.type_request = 'GET';
        beanRequestCapitulo.operation = 'paginate';
        $('#modalCargandoCapitulo').modal('show');
    });


    $("#modalCargandoCapitulo").on('shown.bs.modal', function () {
        processAjaxCapitulo();
    });
    $("#modalCargandoCapitulo").on('hide.bs.modal', function () {
        $(".progress-bar-capitulo").text("Cargando ... 0%");
        $(".progress-bar-capitulo").attr("aria-valuenow", "0");
        $(".progress-bar-capitulo").css("width", "0%");
    });

    $("#ventanaModalManCapitulo").on('hide.bs.modal', function () {
        beanRequestCapitulo.type_request = 'GET';
        beanRequestCapitulo.operation = 'paginate';
    });

    $("#btnAbrirCapitulo").click(function () {
        beanRequestCapitulo.operation = 'add';
        beanRequestCapitulo.type_request = 'POST';
        $("#tituloModalManCapitulo").html("REGISTRAR CAPÍTULO");
        addCapitulo();
        $("#ventanaModalManCapitulo").modal("show");


    });

    $("#formularioCapitulo").submit(function (event) {
        if (validarDormularioCapitulo()) {
            $('#modalCargandoCapitulo').modal('show');
        }
        event.preventDefault();
        event.stopPropagation();
    });

});

function processAjaxCapitulo() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestCapitulo.operation == 'update' ||
        beanRequestCapitulo.operation == 'add'
    ) {

        json = {
            nombre: document.querySelector("#txtNombreCapitulo").value,
            codigo: libroSelected.codigo + "." + document.querySelector("#txtCodigoCapitulo").value,
            descripcion: "",
            libro: libroSelected.codigo,
            estado: 1
        };


    } else {
        form_data = null;
    }

    switch (beanRequestCapitulo.operation) {
        case 'delete':
            parameters_pagination = '?id=' + capituloSelected.idtitulo;
            break;
        case 'update':
            json.idcapitulo = capituloSelected.idtitulo;
            if (document.querySelector("#txtImagenCapitulo").files.length != 0) {
                let dataImagen2 = $("#txtImagenCapitulo").prop("files")[0];
                form_data.append("txtImagenCapitulo", dataImagen2);
            }
            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':
            let dataFot2 = $("#txtImagenCapitulo").prop("files")[0];
            form_data.append("txtImagenCapitulo", dataFot2);
            form_data.append("class", JSON.stringify(json));
            break;

        default:

            parameters_pagination +=
                '?filtro=&libro=' + libroSelected.codigo;
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pageCapitulo").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePageCapitulo").value.trim();
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestCapitulo.entity_api + "/" + beanRequestCapitulo.operation +
            parameters_pagination,
        type: beanRequestCapitulo.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestCapitulo.operation == 'update' || beanRequestCapitulo.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',
        xhr: function () {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    $('.progress-bar-capitulo').css({
                        width: Math.round(percentComplete * 100) + '%'
                    });
                    $(".progress-bar-capitulo").text(Math.round(percentComplete * 100) + '%');
                    $(".progress-bar-capitulo").attr("aria-valuenow", +Math.round(percentComplete * 100));
                    if (percentComplete === 1) {
                        // $('.progress-bar-capitulo').addClass('hide');
                        $('.progress-bar-capitulo').css({
                            width: + '100%'
                        });
                        $(".progress-bar-capitulo").text("Cargando ... 100%");
                        $(".progress-bar-capitulo").attr("aria-valuenow", "100");
                    }
                }
            }, false);
            xhr.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    $('.progress-bar-capitulo').css({
                        width: Math.round(percentComplete * 100) + '%'
                    });
                    $(".progress-bar-capitulo").text(Math.round(percentComplete * 100) + '%');
                    $(".progress-bar-capitulo").attr("aria-valuenow", +Math.round(percentComplete * 100));
                }
            }, false);
            return xhr;
        },
    }).done(function (beanCrudResponse) {

        $('#modalCargandoCapitulo').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                document.querySelector("#pageCapitulo").value = 1;
                document.querySelector("#sizePageCapitulo").value = 20;
                $('#ventanaModalManCapitulo').modal('hide');
            } else {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationCapitulo = beanCrudResponse.beanPagination;
            listaCapitulo(beanPaginationCapitulo);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoCapitulo').modal("hide");
        showAlertErrorRequest();

    });

}

function addCapitulo(capitulo = undefined) {
    //LIMPIAR LOS CAMPOS
    document.querySelector('#inputPrependCapitulo').innerHTML = libroSelected.codigo + ".";
    document.querySelector('#txtCodigoCapitulo').value = (capitulo == undefined) ? "" : capitulo.codigo.substring(libroSelected.codigo.length + 1);

    document.querySelector('#txtNombreCapitulo').value = (capitulo == undefined) ? '' : capitulo.nombre;
    document.querySelector('#txtLibroCapitulo').value = libroSelected.nombre;


    addViewArchivosPreviusCapitulo();

}

function listaCapitulo(beanPagination) {
    document.querySelector('#tbodyCapitulo').innerHTML = '';
    document.querySelector('#titleManagerCapitulo').innerHTML =
        ' LISTA DE CAPITULOS ';
    let row = "", header, contador = 1;
    header = `
    <button class="btn btn-danger btnregresarCapitulo">
    <i class="zmdi zmdi-long-arrow-left mr-1"></i> Regresar
  </button>`;
    document.querySelector('#listaLibroCapitulos').innerHTML = header;
    if (beanPagination.list.length == 0) {
        addEventsButtonsCapituloRegreso();
        destroyPagination($('#paginationCapitulo'));
        row += `<tr>
        <td class="text-center" colspan="5">NO HAY CAPITULOS</td>
        </tr>`;

        document.querySelector('#tbodyCapitulo').innerHTML += row;
        return;
    }

    beanPagination.list.forEach((capitulo) => {

        row += `<tr  idtitulo="${capitulo.idtitulo}">
<td class="text-center ver-subtitulo aula-cursor-mano">${contador++}</td>
<td class="text-center ver-subtitulo aula-cursor-mano">${capitulo.codigo}</td>
<td class="text-center ver-subtitulo aula-cursor-mano">${capitulo.nombre}</td>
<td class="text-center ver-subtitulo aula-cursor-mano">${libroSelected.nombre}</td>
<td class="text-center " style="width:10%;"><img src="${getHostFrontEnd()}adjuntos/libros/capitulos/${capitulo.imagen}" alt="user-picture" class="img-responsive center-box" width="100%"></td>
<td class="text-center">
<button class="btn btn-info editar-capitulo" ><i class="zmdi zmdi-refresh"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-capitulo"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;

        // $('[data-toggle="tooltip"]').tooltip();
    });


    document.querySelector('#tbodyCapitulo').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePageCapitulo").value),
        document.querySelector("#pageCapitulo"),
        $('#modalCargandoCapitulo'),
        $('#paginationCapitulo'));
    addEventsButtonsCapitulo();


}

function addEventsButtonsCapitulo() {

    document.querySelectorAll('.ver-subtitulo').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            capituloSelected = findByCapitulo(
                btn.parentElement.getAttribute('idtitulo')
            );

            if (capituloSelected != undefined) {

                document.querySelector("#seccion-subtitulo").classList.remove("d-none");
                document.querySelector("#seccion-capitulo").classList.add("d-none");
                beanRequestSubtitulo.type_request = 'GET';
                beanRequestSubtitulo.operation = 'paginate';
                $('#modalCargandoSubtitulo').modal('show');
            } else {
                console.log(
                    'warning => ',
                    'No se encontró el capitulo para poder ver'
                );
            }
        };
    });
    document.querySelectorAll('.editar-capitulo').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            capituloSelected = findByCapitulo(
                btn.parentElement.parentElement.getAttribute('idtitulo')
            );

            if (capituloSelected != undefined) {
                addCapitulo(capituloSelected);
                $("#tituloModalManCapitulo").html("EDITAR CAPÍTULO");
                $("#ventanaModalManCapitulo").modal("show");
                beanRequestCapitulo.type_request = 'POST';
                beanRequestCapitulo.operation = 'update';
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
    document.querySelectorAll('.eliminar-capitulo').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            capituloSelected = findByCapitulo(
                btn.parentElement.parentElement.getAttribute('idtitulo')
            );

            if (capituloSelected != undefined) {
                beanRequestCapitulo.type_request = 'GET';
                beanRequestCapitulo.operation = 'delete';
                showAlertDelete('modalCargandoCapitulo');
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });

    addEventsButtonsCapituloRegreso();
}
function addEventsButtonsCapituloRegreso() {


    document.querySelectorAll('.btnregresarCapitulo').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            document.querySelector('#listaLibroCapitulos').innerHTML = "";
            document.querySelector("#seccion-capitulo").classList.add("d-none");
            document.querySelector("#seccion-libro").classList.remove("d-none");
        };
    });

}

function addViewArchivosPreviusCapitulo() {

    $("#txtImagenCapitulo").change(function () {
        filePreview(this, "#imagePreviewCapitulo");
    });
}

function findIndexCapitulo(idbusqueda) {
    return beanPaginationCapitulo.list.findIndex(
        (Capitulo) => {
            if (Capitulo.idtitulo == parseInt(idbusqueda))
                return Capitulo;


        }
    );
}

function findByCapitulo(idtitulo) {
    return beanPaginationCapitulo.list.find(
        (Capitulo) => {
            if (parseInt(idtitulo) == Capitulo.idtitulo) {
                return Capitulo;
            }


        }
    );
}

var validarDormularioCapitulo = () => {
    if (document.querySelector("#txtNombreCapitulo").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Nombre",
            type: "warning",
            timer: 800,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtCodigoCapitulo").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese codigo",
            type: "warning",
            timer: 800,
            showConfirmButton: false
        });
        return false;
    }

    if (libroSelected == undefined) {
        swal({
            title: "Vacío",
            text: "Ingrese libro",
            type: "warning",
            timer: 800,
            showConfirmButton: false
        });
        return false;
    }

    if (beanRequestCapitulo.operation == 'add') {

        /*IMAGEN */
        if (document.querySelector("#txtImagenCapitulo").files.length == 0) {
            showAlertTopEnd("info", "Vacío", "ingrese Imagen");
            return false;
        }
        if (!(document.querySelector("#txtImagenCapitulo").files[0].type == "image/png" || document.querySelector("#txtImagenCapitulo").files[0].type == "image/jpg" || document.querySelector("#txtImagenCapitulo").files[0].type == "image/jpeg")) {
            showAlertTopEnd("info", "Formato Incorrecto", "Ingrese tipo de arhivo Imagen => png,jpg,jpeg");
            return false;
        }
        //menor a   7700 KB
        if (document.querySelector("#txtImagenCapitulo").files[0].size > (7700 * 1024)) {
            showAlertTopEnd("info", "Tamaño excedido", "el tamaño del archivo tiene que ser menor a 7700 KB");
            return false;
        }


    } else {

        if (document.querySelector("#txtImagenCapitulo").files.length != 0) {
            if (!(document.querySelector("#txtImagenCapitulo").files[0].type == "image/png" || document.querySelector("#txtImagenCapitulo").files[0].type == "image/jpg" || document.querySelector("#txtImagenCapitulo").files[0].type == "image/jpeg")) {
                showAlertTopEnd("info", "Formato Incorrecto", "Ingrese tipo de arhivo Imagen => png,jpg,jpeg");
                return false;
            }
            //menor a   7700 KB
            if (document.querySelector("#txtImagenCapitulo").files[0].size > (7700 * 1024)) {
                showAlertTopEnd("info", "Tamaño excedido", "el tamaño del archivo tiene que ser menor a 7700 KB");
                return false;
            }
        }
    }
    return true;
}