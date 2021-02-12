var beanPaginationPublicidad;
var publicidadSelected;
var beanRequestPublicidad = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestPublicidad.entity_api = 'subitems';
    beanRequestPublicidad.operation = 'paginate';
    beanRequestPublicidad.type_request = 'GET';

    $('#sizePagePublicidad').change(function () {
        beanRequestPublicidad.type_request = 'GET';
        beanRequestPublicidad.operation = 'paginate';
        $('#modalCargandoPublicidad').modal('show');
    });

    $('#modalCargandoPublicidad').modal('show');

    $("#modalCargandoPublicidad").on('shown.bs.modal', function () {
        processAjaxPublicidad();
    });
    $("#ventanaModalManPublicidad").on('hide.bs.modal', function () {
        beanRequestPublicidad.type_request = 'GET';
        beanRequestPublicidad.operation = 'paginate';
    });

    $("#btnAbrirbook").click(function () {
        beanRequestPublicidad.operation = 'add';
        beanRequestPublicidad.type_request = 'POST';
        $("#imagePreview").html("");
        $("#tituloModalManPublicidad").html("REGISTRAR PUBLICIDAD");
        addPublicidad();
        $("#ventanaModalManPublicidad").modal("show");


    });

    $("#formularioPublicidad").submit(function (event) {


        if (validarDormularioVideo()) {
            $('#modalCargandoPublicidad').modal('show');
        }
        event.preventDefault();
        event.stopPropagation();
    });

});

function processAjaxPublicidad() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestPublicidad.operation == 'update' ||
        beanRequestPublicidad.operation == 'add'
    ) {

        json = {
            detalle: document.querySelector("#txtTituloPublicidad").value,
            titulo: "",
            tipo: 5
        };


    } else {
        form_data = null;
    }

    switch (beanRequestPublicidad.operation) {
        case 'delete':
            parameters_pagination = '?id=' + publicidadSelected.idsubitem;
            break;

        case 'update':
            json.idsubitem = publicidadSelected.idsubitem;
            let dataImagen = $("#txtImagenObjetivo").prop("files")[0];
            form_data.append("txtImagenObjetivo", dataImagen);
            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':
            if (document.querySelector("#txtImagenObjetivo").files.length !== 0) {
                let dataImagen2 = $("#txtImagenObjetivo").prop("files")[0];
                form_data.append("txtImagenObjetivo", dataImagen2);

            }
            form_data.append("class", JSON.stringify(json));
            break;

        default:

            parameters_pagination +=
                '?filtro=&tipo=5';
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pagePublicidad").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePagePublicidad").value.trim();
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestPublicidad.entity_api + "/" + beanRequestPublicidad.operation +
            parameters_pagination,
        type: beanRequestPublicidad.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },

        data: form_data,
        cache: false,
        contentType: ((beanRequestPublicidad.operation == 'update' || beanRequestPublicidad.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',
    }).done(function (beanCrudResponse) {

        $('#modalCargandoPublicidad').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                document.querySelector("#pagePublicidad").value = 1;
                document.querySelector("#sizePagePublicidad").value = 5;
                $('#ventanaModalManPublicidad').modal('hide');
            } else {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationPublicidad = beanCrudResponse.beanPagination;
            listaPublicidad(beanPaginationPublicidad);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoPublicidad').modal("hide");
        showAlertErrorRequest();

    });

}

function addPublicidad(publicidad = undefined) {

    document.querySelector("#txtTituloPublicidad").value = (publicidad == undefined) ? "" : publicidad.detalle;
    if (publicidad != undefined) {
        $("#imagePreview").html(
            `<img width="100%" height="100%" alt='user-picture' class='img-responsive center-box' src='${getHostFrontEnd()}adjuntos/slider/${publicidad.imagen}' />`
        );
    } else {
        $("#imagePreview").html(
            ""
        );
    }
    addViewArchivosPreviusLibro();
}

function addViewArchivosPreviusLibro() {

    $("#txtImagenObjetivo").change(function () {
        filePreview(this, "#imagePreview");
    });


}

function listaPublicidad(beanPagination) {
    document.querySelector('#tbodyPublicidad').innerHTML = '';
    document.querySelector('#titleManagerPublicidad').innerHTML =
        '[ ' + beanPagination.countFilter + ' ]  Lista de Publicidad';
    let row = "";
    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationPublicidad'));
        row += `<tr>
        <td class="text-center" colspan="4">NO HAY PUBLICIDAD</td>
        </tr>`;

        document.querySelector('#tbodyPublicidad').innerHTML += row;
        return;
    }
    beanPagination.list.forEach((publicidad) => {

        row += `<tr  idsubitem="${publicidad.idsubitem}">
<td class="text-center" >${publicidad.detalle}</td>
<td class="text-center ver-capitulo" style="width:25%;"><img src="${getHostFrontEnd()}adjuntos/slider/${publicidad.imagen}" alt="${publicidad.imagen}" class="img-responsive center-box" width="100%" ></td>
<td class="text-center">
<button class="btn btn-info editar-publicidad" ><i class="zmdi zmdi-refresh"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-publicidad"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;

        // $('[data-toggle="tooltip"]').tooltip();
    });


    document.querySelector('#tbodyPublicidad').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePagePublicidad").value),
        document.querySelector("#pagePublicidad"),
        $('#modalCargandoPublicidad'),
        $('#paginationPublicidad'));
    addEventsButtonsAdmin();


}

function addEventsButtonsAdmin() {


    document.querySelectorAll('.editar-publicidad').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            publicidadSelected = findByPublicidad(
                btn.parentElement.parentElement.getAttribute('idsubitem')
            );

            if (publicidadSelected != undefined) {
                addPublicidad(publicidadSelected);
                $("#tituloModalManPublicidad").html("EDITAR PUBLICIDAD");
                $("#ventanaModalManPublicidad").modal("show");
                beanRequestPublicidad.type_request = 'POST';
                beanRequestPublicidad.operation = 'update';
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
    document.querySelectorAll('.eliminar-publicidad').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            publicidadSelected = findByPublicidad(
                btn.parentElement.parentElement.getAttribute('idsubitem')
            );

            if (publicidadSelected != undefined) {
                beanRequestPublicidad.type_request = 'GET';
                beanRequestPublicidad.operation = 'delete';
                $('#modalCargandoPublicidad').modal('show');
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
}

function findIndexPublicidad(idbusqueda) {
    return beanPaginationPublicidad.list.findIndex(
        (Publicidad) => {
            if (Publicidad.idsubitem == parseInt(idbusqueda))
                return Publicidad;


        }
    );
}

function findByPublicidad(idsubitem) {
    return beanPaginationPublicidad.list.find(
        (Publicidad) => {
            if (parseInt(idsubitem) == Publicidad.idsubitem) {
                return Publicidad;
            }


        }
    );
}
function filePreview(input, imagen) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        imagen;
        reader.onload = function (e) {
            $(imagen).html(
                "<img width='100%' height='100%'  alt='user-picture' class='img-responsive center-box' src='" +
                e.target.result +
                "' />"
            );
        };
        reader.readAsDataURL(input.files[0]);
    }
}

var validarDormularioVideo = () => {
    if (document.querySelector("#txtTituloPublicidad").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Título",
            type: "warning",
            timer: 800,
            showConfirmButton: false
        });
        return false;
    }

    if (beanRequestPublicidad.operation == 'add') {
        if (!(document.querySelector("#txtImagenObjetivo").files[0].type == "image/png" || document.querySelector("#txtImagenObjetivo").files[0].type == "image/jpg" || document.querySelector("#txtImagenObjetivo").files[0].type == "image/jpeg")) {
            swal({
                title: "Formato Incorrecto",
                text: "Ingrese formato png, jpeg y jpg",
                type: "warning",
                timer: 1200,
                showConfirmButton: false
            });
            return false;
        }
        //menor a   10 MB
        if (document.querySelector("#txtImagenObjetivo").files[0].size > (10 * 1024 * 1024)) {
            swal({
                title: "Tamaño excedido",
                text: "el tamaño del archivo tiene que ser menor a 10 MB",
                type: "warning",
                timer: 1200,
                showConfirmButton: false
            });
            return false;
        }
    } else {
        if (document.querySelector("#txtImagenObjetivo").files.length !== 0) {

            if (!(document.querySelector("#txtImagenObjetivo").files[0].type == "image/png" || document.querySelector("#txtImagenObjetivo").files[0].type == "image/jpg" || document.querySelector("#txtImagenObjetivo").files[0].type == "image/jpeg")) {
                swal({
                    title: "Formato Incorrecto",
                    text: "Ingrese formato png, jpeg y jpg",
                    type: "warning",
                    timer: 1200,
                    showConfirmButton: false
                });
                return false;
            }
            //menor a   10 MB
            if (document.querySelector("#txtImagenObjetivo").files[0].size > (10 * 1024 * 1024)) {
                swal({
                    title: "Tamaño excedido",
                    text: "el tamaño del archivo tiene que ser menor a 10 MB",
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