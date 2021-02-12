var beanPaginationCuestionario;
var cuestionarioSelected;
var beanRequestCuestionario = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestCuestionario.entity_api = 'respuestas';
    beanRequestCuestionario.operation = 'paginate';
    beanRequestCuestionario.type_request = 'GET';

    $('#sizePageCuestionario').change(function () {
        beanRequestCuestionario.type_request = 'GET';
        beanRequestCuestionario.operation = 'paginate';
        $('#modalCargandoCuestionario').modal('show');
    });


    $("#modalCargandoCuestionario").on('shown.bs.modal', function () {
        processAjaxCuestionario();
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
            codigo: subtituloSelected.codigo + "." + document.querySelector("#txtCodigoCuestionario").value,
            subTitulo: subtituloSelected.codigo
        };


    } else {
        form_data = null;
    }

    switch (beanRequestCuestionario.operation) {
        case 'delete':
            parameters_pagination = '?id=' + cuestionarioSelected.idvideoSubTitulo;
            break;

        case 'update':
            json.idvideoSubTitulo = cuestionarioSelected.idvideoSubTitulo;

            if (document.querySelector("#txtVideoCuestionario").files.length !== 0) {
                let dataImagen = $("#txtVideoCuestionario").prop("files")[0];
                form_data.append("txtVideoCuestionario", dataImagen);
            }
            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':

            let dataFot = $("#txtVideoCuestionario").prop("files")[0];
            form_data.append("txtVideoCuestionario", dataFot);

            form_data.append("class", JSON.stringify(json));
            break;

        default:

            parameters_pagination +=
                '?filtro=&subtitulo=' + subtituloSelected.codigo;
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
        dataType: 'json'
    }).done(function (beanCrudResponse) {

        $('#modalCargandoCuestionario').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                swal({
                    title: "Realizado",
                    text: "Acción realizada existosamente!",
                    type: "success",
                    timer: 1200,
                    showConfirmButton: false
                });
                document.querySelector("#pageCuestionario").value = 1;
                document.querySelector("#sizePageCuestionario").value = 5;
                $('#ventanaModalManCuestionario').modal('hide');
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
    document.querySelector('#inputPrependCuestionario').innerHTML = subtituloSelected.codigo + ".";
    document.querySelector('#txtCodigoCuestionario').value = (cuestionario == undefined) ? '' : cuestionario.codigo.substring(subtituloSelected.codigo.length + 1);

    if (cuestionario !== undefined) {

        $("#videoPreviewCuestionario").html(
            `<video width='100%' alt='user-picture' class='img-responsive center-box' controls ><source src='${getHostFrontEnd()}adjuntos/archivos/${libroSelected.codigo}/${capituloSelected.codigo}/VIDEOS/${cuestionario.subTitulo.codigo}/${cuestionario.nombre}' type='video/mp4'></video>`
        );

    } else {
        $("#videoPreviewCuestionario").html(
            ""
        );

    }

    addViewArchivosPreviusCuestionario();

}

function listaCuestionario(beanPagination) {
    document.querySelector('#tbodyCuestionario').innerHTML = '';
    document.querySelector('#titleManagerCuestionario').innerHTML =
        '[ ' + beanPagination.countFilter + ' ] PARRAFOS';
    let row = "", header;
    header = `<li class="breadcrumb-item btnregresarCapitulo aula-cursor-mano">${libroSelected.nombre}</li><li class="breadcrumb-item btnregresarSubtitulo aula-cursor-mano">${capituloSelected.nombre}</li><li class="breadcrumb-item btnregresarCuestionario aula-cursor-mano">${subtituloSelected.nombre}</li>`;
    document.querySelector('#listaLibroCapitulos').innerHTML = header;
    if (beanPagination.list.length == 0) {
        destroyPagination($('#tbodyCuestionario'));
        row += `<tr>
        <td class="text-center" colspan="5">NO HAY DATOS</td>
        </tr>`;

        document.querySelector('#tbodyCuestionario').innerHTML += row;
        addEventsButtonsCuestionario();
        return;
    }
    beanPagination.list.forEach((cuestionario) => {

        row += `<tr  idvideoSubTitulo="${cuestionario.idvideoSubTitulo}">
<td class="text-center">${cuestionario.codigo}</td>
<td class="text-center ">
<video alt="imagen de usuario" class="img-responsive center-box" style="width:100px;height:60px;" controls=""><source src="${getHostFrontEnd()}adjuntos/archivos/${capituloSelected.libro.codigo}/${capituloSelected.codigo}/VIDEOS/${subtituloSelected.codigo}/${cuestionario.nombre}" type="video/mp4"></video>
<div class="imag">${cuestionario.nombre}</div>
</td>
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
    addEventsButtonsCuestionario();


}

function addEventsButtonsCuestionario() {


    document.querySelectorAll('.editar-cuestionario').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            cuestionarioSelected = findByCuestionario(
                btn.parentElement.parentElement.getAttribute('idvideoSubTitulo')
            );

            if (cuestionarioSelected != undefined) {
                addCuestionario(cuestionarioSelected);
                $("#tituloModalManCuestionario").html("EDITAR VIDEO DE PARRAFOS");
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
                btn.parentElement.parentElement.getAttribute('idvideoSubTitulo')
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
    document.querySelectorAll('.btnregresarCuestionario').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //document.querySelector('#listaLibroCapitulos').innerHTML = "";
            var list = document.querySelector('#listaLibroCapitulos');
            list.removeChild(list.childNodes[list.childNodes.length - 1]);
            document.querySelector("#seccion-subtitulo").classList.remove("d-none");
            document.querySelector("#seccion-cuestionario").classList.add("d-none");
        };
    });
    document.querySelectorAll('.btnregresarSubtitulo').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //document.querySelector('#listaLibroCapitulos').innerHTML = "";
            var list = document.querySelector('#listaLibroCapitulos');
            list.removeChild(list.childNodes[list.childNodes.length - 1]);
            document.querySelector("#seccion-subtitulo").classList.remove("d-none");
            document.querySelector("#seccion-cuestionario").classList.add("d-none");
            document.querySelector("#seccion-capitulo").classList.add("d-none");
        };
    });
    document.querySelectorAll('.btnregresarCapitulo').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            document.querySelector('#listaLibroCapitulos').innerHTML = "";
            document.querySelector("#seccion-libro").classList.remove("d-none");
            document.querySelector("#seccion-capitulo").classList.add("d-none");
            document.querySelector("#seccion-cuestionario").classList.add("d-none");
        };
    });
}

function addViewArchivosPreviusCuestionario() {

    $("#txtVideoCuestionario").change(function () {
        videoPreview(this, "#videoPreviewCuestionario");
    });
}

function findIndexCuestionario(idbusqueda) {
    return beanPaginationCuestionario.list.findIndex(
        (Cuestionario) => {
            if (Cuestionario.idvideoSubTitulo == parseInt(idbusqueda))
                return Cuestionario;


        }
    );
}

function findByCuestionario(idvideoSubTitulo) {
    return beanPaginationCuestionario.list.find(
        (Cuestionario) => {
            if (parseInt(idvideoSubTitulo) == Cuestionario.idvideoSubTitulo) {
                return Cuestionario;
            }


        }
    );
}

var validarDormularioCuestionario = () => {

    if (document.querySelector("#txtCodigoCuestionario").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Codigo",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (beanRequestSubtitulo.operation == 'add') {
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
                text: "Ingrese tipo de arhivo pdf",
                type: "warning",
                timer: 1200,
                showConfirmButton: false
            });
            return false;
        }
        //menor a   30 MB
        if (document.querySelector("#txtVideoCuestionario").files[0].size > (30 * 1024 * 1024)) {
            swal({
                title: "Tamaño excedido",
                text: "el tamaño del archivo tiene que ser menor a 17 MB",
                type: "warning",
                timer: 1200,
                showConfirmButton: false
            });
            return false;
        }
    }
    if (document.querySelector("#txtVideoCuestionario").files.length !== 0) {
        if (document.querySelector("#txtVideoCuestionario").files[0].type !== "video/mp4") {
            swal({
                title: "Formato Incorrecto",
                text: "Ingrese tipo de arhivo pdf",
                type: "warning",
                timer: 1200,
                showConfirmButton: false
            });
            return false;
        }
        //menor a   30 MB
        if (document.querySelector("#txtVideoCuestionario").files[0].size > (30 * 1024 * 1024)) {
            swal({
                title: "Tamaño excedido",
                text: "el tamaño del archivo tiene que ser menor a 17 MB",
                type: "warning",
                timer: 1200,
                showConfirmButton: false
            });
            return false;
        }
    }

    return true;
}