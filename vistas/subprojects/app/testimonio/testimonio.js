var beanPaginationTestimonio;
var testimonioSelected;
var beanRequestTestimonio = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestTestimonio.entity_api = 'testimonio';
    beanRequestTestimonio.operation = 'paginate';
    beanRequestTestimonio.type_request = 'GET';

    $('#sizePageTestimonio').change(function () {
        beanRequestTestimonio.type_request = 'GET';
        beanRequestTestimonio.operation = 'paginate';
        $('#modalCargandoTestimonio').modal('show');
    });

    $('#modalCargandoTestimonio').modal('show');

    $("#modalCargandoTestimonio").on('shown.bs.modal', function () {
        processAjaxTestimonio();
    });
    $("#txtDescripcionTestimonio").Editor();

    $("#btnAbrirbook").click(function () {
        beanRequestTestimonio.operation = 'add';
        beanRequestTestimonio.type_request = 'POST';
        $("#imagePreview").html("");
        $("#tituloModalManTestimonio").html("REGISTRAR TESTIMONIO");
        addTestimonio();
        $("#ventanaModalManTestimonio").modal("show");


    });
    document.querySelector("#txtYoutubeTestimonio").onkeyup = (e) => {
        if (!document.querySelector("#txtYoutubeTestimonio").value.includes("iframe")) {
            return;
        }
        setTimeout(() => {
            document.querySelector("#youtubePreview").innerHTML = e.target.value;
            document.querySelector("#youtubePreview").style.height = "320px";
        }, 1500);
    }
    $("#formularioTestimonio").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (validarDormularioVideo()) {
            $('#modalCargandoTestimonio').modal('show');
        }
    });

});

function processAjaxTestimonio() {
    let form_data = new FormData();


    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestTestimonio.operation == 'update' ||
        beanRequestTestimonio.operation == 'add'
    ) {

        json = {
            titulo: document.querySelector("#txttituloTestimonio").value,
            enlace: document.querySelector("#txtYoutubeTestimonio").value,
            descripcion: $("#txtDescripcionTestimonio").Editor("getText"),
            estado: 1
        };
    } else {
        form_data = null;
    }
    switch (beanRequestTestimonio.operation) {
        case 'delete':
            parameters_pagination = '?id=' + testimonioSelected.idtestimonio;
            break;

        case 'update':
            json.idtestimonio = testimonioSelected.idtestimonio;
            if (document.querySelector("#txtImagenTestimonio").files.length !== 0) {
                let dataFoto = $("#txtImagenTestimonio").prop("files")[0];
                form_data.append("txtImagenTestimonio", dataFoto);

            }

            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':

            let dataFot = $("#txtImagenTestimonio").prop("files")[0];
            form_data.append("txtImagenTestimonio", dataFot);

            form_data.append("class", JSON.stringify(json));
            break;

        default:

            parameters_pagination +=
                '?filtro=';
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pageTestimonio").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePageTestimonio").value.trim();

            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestTestimonio.entity_api + "/" + beanRequestTestimonio.operation +
            parameters_pagination,
        type: beanRequestTestimonio.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestTestimonio.operation == 'update' || beanRequestTestimonio.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',
    }).done(function (beanCrudResponse) {
        $('#modalCargandoTestimonio').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                document.querySelector("#pageTestimonio").value = 1;
                document.querySelector("#sizePageTestimonio").value = 20;
                $('#ventanaModalManTestimonio').modal('hide');
            } else {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationTestimonio = beanCrudResponse.beanPagination;
            listaTestimonio(beanPaginationTestimonio);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoTestimonio').modal("hide");
        showAlertErrorRequest();

    });

}

function addTestimonio(testimonio = undefined) {
    //LIMPIAR LOS CAMPOS

    document.querySelector('#txttituloTestimonio').value = (testimonio == undefined) ? '' : testimonio.titulo;

    document.querySelector('#txtYoutubeTestimonio').value = (testimonio == undefined) ? '' : testimonio.enlace;

    if (testimonio !== undefined) {

        $("#imagePreview").html(
            `<img width='244' alt='user-picture' class='img-responsive center-box' src='${getHostFrontEnd()}adjuntos/testimonio/${testimonio.imagen}' />`
        );
        if (testimonio.enlaceYoutube.includes("iframe")) {
            document.querySelector("#youtubePreview").innerHTML = testimonio.enlaceYoutube;
            document.querySelector("#txtYoutubeTestimonio").value = testimonio.enlaceYoutube;
        }


    } else {
        document.querySelector("#youtubePreview").style = "";
        $("#imagePreview").html(
            ""
        );
        document.querySelector("#youtubePreview").innerHTML = "";
        document.querySelector("#txtYoutubeTestimonio").value = "";

    }
    $("#txtDescripcionTestimonio").Editor("setText", (testimonio == undefined) ? '<p style="color:black"></p>' : testimonio.descripcion);
    $("#txtDescripcionTestimonio").Editor("getText");

    addViewArchivosPrevius();

}

function listaTestimonio(beanPagination) {
    document.querySelector('#tbodyTestimonio').innerHTML = '';
    document.querySelector('#titleManagerTestimonio').innerHTML =
        '[ ' + beanPagination.countFilter + ' ] TESTIMONIOS';
    let row = "";
    beanPagination.list.forEach((testimonio) => {

        row += `<tr  idtestimonio="${testimonio.idtestimonio}">
<td class="text-center">${testimonio.titulo}</td>
<td class="text-center" style="width:25%;height:25%;">${testimonio.descripcion}</td>
<td class="text-center "><img src="${getHostFrontEnd()}adjuntos/testimonio/${testimonio.imagen}" alt="${testimonio.imagen}" class="img-responsive center-box" style="width:50px;height:60px;"></td>
<td class="text-center" style="width:25%;height:25%;">${testimonio.enlaceYoutube}</td>
<td class="text-center">
<button class="btn btn-info editar-testimonio-testimonio" ><i class="zmdi zmdi-refresh"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-testimonio-testimonio"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;

        // $('[data-toggle="tooltip"]').tooltip();
    });


    document.querySelector('#tbodyTestimonio').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePageTestimonio").value),
        document.querySelector("#pageTestimonio"),
        $('#modalCargandoTestimonio'),
        $('#paginationTestimonio'));
    addEventsButtonsAdmin();


}

function addEventsButtonsAdmin() {


    document.querySelectorAll('.editar-testimonio-testimonio').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            testimonioSelected = findByTestimonio(
                btn.parentElement.parentElement.getAttribute('idtestimonio')
            );

            if (testimonioSelected != undefined) {
                addTestimonio(testimonioSelected);
                $("#tituloModalManTestimonio").html("EDITAR TESTIMONIO");
                $("#ventanaModalManTestimonio").modal("show");
                beanRequestTestimonio.type_request = 'POST';
                beanRequestTestimonio.operation = 'update';
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
    document.querySelectorAll('.eliminar-testimonio-testimonio').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            testimonioSelected = findByTestimonio(
                btn.parentElement.parentElement.getAttribute('idtestimonio')
            );

            if (testimonioSelected != undefined) {
                beanRequestTestimonio.type_request = 'GET';
                beanRequestTestimonio.operation = 'delete';
                $('#modalCargandoTestimonio').modal('show');
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

    $("#txtImagenTestimonio").change(function () {
        filePreview(this, "#imagePreview");
    });
    $("#txtImagenPortadaTestimonio").change(function () {
        filePreview(this, "#imagePreview2");
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

function testimonioPreview(input, imagen) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        imagen;
        reader.onload = function (e) {
            $(imagen).html(
                "<testimonio width='244' alt='user-picture' class='img-responsive center-box' controls ><source src='" +
                e.target.result +
                "' type='testimonio/mp4'></testimonio>"
            );
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function findIndexTestimonio(idbusqueda) {
    return beanPaginationTestimonio.list.findIndex(
        (Testimonio) => {
            if (Testimonio.idtestimonio == parseInt(idbusqueda))
                return Testimonio;


        }
    );
}

function findByTestimonio(idtestimonio) {
    return beanPaginationTestimonio.list.find(
        (Testimonio) => {
            if (parseInt(idtestimonio) == Testimonio.idtestimonio) {
                return Testimonio;
            }


        }
    );
}

var validarDormularioVideo = () => {
    if (document.querySelector("#txttituloTestimonio").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Título",
            type: "warning",
            timer: 800,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtYoutubeTestimonio").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Enlace de Youtube",
            type: "warning",
            timer: 800,
            showConfirmButton: false
        });
        return false;
    }

    if (beanRequestTestimonio.operation == "add") {
        if (document.querySelector("#txtImagenTestimonio").files.length == 0) {
            swal({
                title: "Vacío",
                text: "Ingrese Foto",
                type: "warning",
                timer: 1200,
                showConfirmButton: false
            });
            return false;
        }
        if (!(document.querySelector("#txtImagenTestimonio").files[0].type == "image/png" || document.querySelector("#txtImagenTestimonio").files[0].type == "image/jpg" || document.querySelector("#txtImagenTestimonio").files[0].type == "image/jpeg")) {
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
        if (document.querySelector("#txtImagenTestimonio").files[0].size > (1700 * 1024)) {
            swal({
                title: "Tamaño excedido",
                text: "el tamaño del archivo tiene que ser menor a 1700 KB",
                type: "warning",
                timer: 1200,
                showConfirmButton: false
            });
            return false;
        }


    } else {
        if (document.querySelector("#txtImagenTestimonio").files.length !== 0) {
            if (!(document.querySelector("#txtImagenTestimonio").files[0].type == "image/png" || document.querySelector("#txtImagenTestimonio").files[0].type == "image/jpg" || document.querySelector("#txtImagenTestimonio").files[0].type == "image/jpeg")) {
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
            if (document.querySelector("#txtImagenTestimonio").files[0].size > (1700 * 1024)) {
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

    }
    return true;
}