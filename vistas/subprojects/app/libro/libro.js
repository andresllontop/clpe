var beanPaginationLibro;
var libroSelected;
var beanRequestLibro = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestLibro.entity_api = 'libros';
    beanRequestLibro.operation = 'paginate';
    beanRequestLibro.type_request = 'GET';

    $('#sizePageLibro').change(function () {
        beanRequestLibro.type_request = 'GET';
        beanRequestLibro.operation = 'paginate';
        $('#modalCargandoLibro').modal('show');
    });

    $('#modalCargandoLibro').modal('show');

    $("#modalCargandoLibro").on('shown.bs.modal', function () {
        processAjaxLibro();
    });
    $("#ventanaModalManLibro").on('hide.bs.modal', function () {
        beanRequestLibro.type_request = 'GET';
        beanRequestLibro.operation = 'paginate';
    });
    $("#btnAbrirlibro").click(function () {
        beanRequestLibro.operation = 'add';
        beanRequestLibro.type_request = 'POST';
        $("#imagePreview").html("");
        $("#tituloModalManLibro").html("REGISTRAR LIBRO");
        addLibro();
        $("#ventanaModalManLibro").modal("show");
    });


    $("#formularioLibro").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (validarDormularioLibro()) {
            $('#modalCargandoLibro').modal('show');
        }
    });

});

function processAjaxLibro() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestLibro.operation == 'update' ||
        beanRequestLibro.operation == 'add'
    ) {

        json = {
            nombre: document.querySelector("#txtNombreLibro").value,
            codigo: document.querySelector("#txtCodigoLibro").value,
            descripcion: "",
            estado: 1,
            categoria: 1
        };


    } else {
        form_data = null;
    }

    switch (beanRequestLibro.operation) {
        case 'delete':
            parameters_pagination = '?id=' + libroSelected.idlibro;
            break;

        case 'update':
            json.idlibro = libroSelected.idlibro;
            if (document.querySelector("#txtImagenLibro").files.length != 0) {
                let dataImagen = $("#txtImagenLibro").prop("files")[0];
                form_data.append("txtImagenLibro", dataImagen);
            }
            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':
            let dataImagen2 = $("#txtImagenLibro").prop("files")[0];
            form_data.append("txtImagenLibro", dataImagen2);
            form_data.append("class", JSON.stringify(json));
            break;

        default:

            parameters_pagination +=
                '?filtro=';
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pageLibro").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePageLibro").value.trim();
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestLibro.entity_api + "/" + beanRequestLibro.operation +
            parameters_pagination,
        type: beanRequestLibro.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestLibro.operation == 'update' || beanRequestLibro.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json'
    }).done(function (beanCrudResponse) {

        $('#modalCargandoLibro').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                document.querySelector("#pageLibro").value = 1;
                document.querySelector("#sizePageLibro").value = 5;
                $('#ventanaModalManLibro').modal('hide');
            } else {
                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationLibro = beanCrudResponse.beanPagination;
            listaLibro(beanPaginationLibro);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoLibro').modal("hide");
        showAlertErrorRequest();


    });

}

function addLibro(libro = undefined) {
    //LIMPIAR LOS CAMPOS

    document.querySelector('#txtCodigoLibro').value = (libro == undefined) ? '' : libro.codigo;

    document.querySelector('#txtNombreLibro').value = (libro == undefined) ? '' : libro.nombre;

    if (libro !== undefined) {

        $("#imagePreview").html(
            `<img width='150' alt='user-picture' class='img-responsive center-box' src='${getHostFrontEnd()}adjuntos/libros/${libro.imagen}' />`
        );



    } else {

        $("#imagePreview").html(
            ""
        );


    }

    addViewArchivosPreviusLibro();

}

function listaLibro(beanPagination) {
    let row = "";
    document.querySelector('#tbodyLibro').innerHTML = '';
    document.querySelector('#titleManagerLibro').innerHTML =
        ' LIBRO';

    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationLibro'));
        row += `<tr>
        <td class="text-center" colspan="4">NO HAY LIBROS</td>
        </tr>`;

        document.querySelector('#tbodyLibro').innerHTML += row;
        return;
    }
    beanPagination.list.forEach((libro) => {

        row += `<tr  idlibro="${libro.idlibro}">
<td class="text-center ver-capitulo aula-cursor-mano">${libro.codigo}</td>
<td class="text-center ver-capitulo aula-cursor-mano">${libro.nombre}</td>
<td class="text-center ver-capitulo aula-cursor-mano" style="width:10%;"><img src="${getHostFrontEnd()}adjuntos/libros/${libro.imagen}" alt="user-picture" class="img-responsive center-box" width="100%"></td>
<td class="text-center">
<button class="btn btn-info editar-libro" ><i class="zmdi zmdi-refresh"></i> </button>
</td>
<td class="text-center d-none">
<button class="btn btn-danger eliminar-libro "><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;

        // $('[data-toggle="tooltip"]').tooltip();
    });


    document.querySelector('#tbodyLibro').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePageLibro").value),
        document.querySelector("#pageLibro"),
        $('#modalCargandoLibro'),
        $('#paginationLibro'));
    addEventsButtonsLibro();


}

function addEventsButtonsLibro() {

    document.querySelectorAll('.ver-capitulo').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            libroSelected = findByLibro(
                btn.parentElement.getAttribute('idlibro')
            );

            if (libroSelected != undefined) {

                document.querySelector("#seccion-libro").classList.add("d-none");
                document.querySelector("#seccion-capitulo").classList.remove("d-none");
                beanRequestCapitulo.type_request = 'GET';
                beanRequestCapitulo.operation = 'paginate';
                $('#modalCargandoCapitulo').modal('show');
            } else {
                console.log(
                    'warning => ',
                    'No se encontró el libro para poder ver'
                );
            }
        };
    });
    document.querySelectorAll('.editar-libro').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            libroSelected = findByLibro(
                btn.parentElement.parentElement.getAttribute('idlibro')
            );

            if (libroSelected != undefined) {
                addLibro(libroSelected);
                $("#tituloModalManLibro").html("EDITAR LIBRO");
                $("#ventanaModalManLibro").modal("show");
                beanRequestLibro.type_request = 'POST';
                beanRequestLibro.operation = 'update';
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
    document.querySelectorAll('.eliminar-libro').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            libroSelected = findByLibro(
                btn.parentElement.parentElement.getAttribute('idlibro')
            );

            if (libroSelected != undefined) {
                beanRequestLibro.type_request = 'GET';
                beanRequestLibro.operation = 'delete';
                $('#modalCargandoLibro').modal('show');
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
}

function addViewArchivosPreviusLibro() {

    $("#txtImagenLibro").change(function () {
        filePreview(this, "#imagePreview");
    });


}

function findIndexLibro(idbusqueda) {
    return beanPaginationLibro.list.findIndex(
        (Libro) => {
            if (Libro.idlibro == parseInt(idbusqueda))
                return Libro;


        }
    );
}

function findByLibro(idlibro) {
    return beanPaginationLibro.list.find(
        (Libro) => {
            if (parseInt(idlibro) == Libro.idlibro) {
                return Libro;
            }


        }
    );
}

var validarDormularioLibro = () => {
    if (document.querySelector("#txtCodigoLibro").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Código",
            type: "warning",
            timer: 800,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtNombreLibro").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Nombre",
            type: "warning",
            timer: 800,
            showConfirmButton: false
        });
        return false;
    }

    if (beanRequestLibro.operation == 'add') {

        /*IMAGEN */
        if (document.querySelector("#txtImagenLibro").files.length == 0) {
            showAlertTopEnd("info", "Vacío", "ingrese Imagen");
            return false;
        }
        if (!(document.querySelector("#txtImagenLibro").files[0].type == "image/png" || document.querySelector("#txtImagenLibro").files[0].type == "image/jpg" || document.querySelector("#txtImagenLibro").files[0].type == "image/jpeg")) {
            showAlertTopEnd("info", "Formato Incorrecto", "Ingrese tipo de arhivo Imagen => png,jpg,jpeg");
            return false;
        }
        //menor a   1700 KB
        if (document.querySelector("#txtImagenLibro").files[0].size > (1700 * 1024)) {
            showAlertTopEnd("info", "Tamaño excedido", "el tamaño del archivo tiene que ser menor a 1700 KB");
            return false;
        }


    } else {
        if (document.querySelector("#txtImagenLibro").files.length != 0) {
            if (!(document.querySelector("#txtImagenLibro").files[0].type == "image/png" || document.querySelector("#txtImagenLibro").files[0].type == "image/jpg" || document.querySelector("#txtImagenLibro").files[0].type == "image/jpeg")) {
                showAlertTopEnd("info", "Formato Incorrecto", "Ingrese tipo de arhivo Imagen => png,jpg,jpeg");
                return false;
            }
            //menor a   1700 KB
            if (document.querySelector("#txtImagenLibro").files[0].size > (1700 * 1024)) {
                showAlertTopEnd("info", "Tamaño excedido", "el tamaño del archivo tiene que ser menor a 1700 KB");
                return false;
            }

        }
    }

    return true;
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

function downloadURL(url) {
    var hiddenIFrameID = 'hiddenDownloader',
        iframe = document.getElementById(hiddenIFrameID);
    if (iframe === null) {
        iframe = document.createElement('iframe');
        iframe.id = hiddenIFrameID;
        iframe.style.width = '100%';
        iframe.style.height = '100%';
        document.querySelector('#modalFrameContenidoSubtitulo').appendChild(iframe);
    }
    iframe.src = url;
};
