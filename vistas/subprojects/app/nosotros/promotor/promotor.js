var beanPaginationPromotor;
var promotorSelected;
var beanRequestPromotor = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestPromotor.entity_api = 'promotor';
    beanRequestPromotor.operation = 'paginate';
    beanRequestPromotor.type_request = 'GET';

    $('#sizePagePromotor').change(function () {
        beanRequestPromotor.type_request = 'GET';
        beanRequestPromotor.operation = 'paginate';
        $('#modalCargandoPromotor').modal('show');
    });

    $('#modalCargandoPromotor').modal('show');

    $("#modalCargandoPromotor").on('shown.bs.modal', function () {
        processAjaxPromotor();
    });
    $("#ventanaModalManPromotor").on('hide.bs.modal', function () {
        beanRequestPromotor.type_request = 'GET';
        beanRequestPromotor.operation = 'paginate';
    });

    $("#txtDescripcionPromotor").Editor();
    $("#txtHistoriaPromotor").Editor();

    $("#btnAbrirbook").click(function () {
        beanRequestPromotor.operation = 'add';
        beanRequestPromotor.type_request = 'POST';
        $("#imagePreview").html("");
        $("#tituloModalManPromotor").html("REGISTRAR PROMOTOR");
        addPromotor();
        $("#ventanaModalManPromotor").modal("show");


    });
    $("#formularioPromotor").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (validateFormPromotor()) {
            $('#modalCargandoPromotor').modal('show');
        }
    });

});

function processAjaxPromotor() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestPromotor.operation == 'update' ||
        beanRequestPromotor.operation == 'add'
    ) {

        json = {
            nombre: document.querySelector("#txtNombrePromotor").value,
            apellido: document.querySelector("#ApellidoPromotor").value,
            telefono: "",
            ocupacion: "",
            email: document.querySelector("#txtFacebookPromotor").value,
            youtube: document.querySelector("#txtYoutubePromotor").value,
            descripcion: $("#txtDescripcionPromotor").Editor("getText"),
            historia: $("#txtHistoriaPromotor").Editor("getText")

        };


    } else {
        form_data = null;
    }

    switch (beanRequestPromotor.operation) {
        case 'delete':
            parameters_pagination = '?id=' + promotorSelected.idpromotor;
            break;

        case 'update':
            json.idpromotor = promotorSelected.idpromotor;
            if (document.querySelector("#txtFotoPromotor").files.length !== 0) {
                let dataFoto = $("#txtFotoPromotor").prop("files")[0];
                form_data.append("txtFotoPromotor", dataFoto);

            }
            if (document.querySelector("#txtFotoPortadaPromotor").files.length !== 0) {
                let dataPortada = $("#txtFotoPortadaPromotor").prop("files")[0];
                form_data.append("txtFotoPortadaPromotor", dataPortada);

            }

            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':
            let dataFot = $("#txtFotoPromotor").prop("files")[0];
            form_data.append("txtFotoPromotor", dataFot);
            let dataPort = $("#txtFotoPortadaPromotor").prop("files")[0];
            form_data.append("txtFotoPortadaPromotor", dataPort);
            form_data.append("class", JSON.stringify(json));
            break;

        default:

            parameters_pagination +=
                '?filtro=';
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pagePromotor").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePagePromotor").value.trim();
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestPromotor.entity_api + "/" + beanRequestPromotor.operation +
            parameters_pagination,
        type: beanRequestPromotor.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestPromotor.operation == 'update' || beanRequestPromotor.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json'
    }).done(function (beanCrudResponse) {

        $('#modalCargandoPromotor').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                document.querySelector("#pagePromotor").value = 1;
                document.querySelector("#sizePagePromotor").value = 5;
                $('#ventanaModalManPromotor').modal('hide');
            } else {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationPromotor = beanCrudResponse.beanPagination;
            listaPromotor(beanPaginationPromotor);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoPromotor').modal("hide");
        showAlertErrorRequest();

    });

}

function addPromotor(promotor = undefined) {
    //LIMPIAR LOS CAMPOS

    document.querySelector('#txtNombrePromotor').value = (promotor == undefined) ? '' : promotor.nombre;

    document.querySelector('#ApellidoPromotor').value = (promotor == undefined) ? '' : promotor.apellido;
    document.querySelector('#txtFacebookPromotor').value = (promotor == undefined) ? '' : promotor.email;
    document.querySelector('#txtYoutubePromotor').value = (promotor == undefined) ? '' : promotor.youtube;
    document.querySelector('#ApellidoPromotor').value = (promotor == undefined) ? '' : promotor.nombre;

    $("#txtDescripcionPromotor").Editor("setText", (promotor == undefined) ? '<p style="color:black"></p>' : promotor.descripcion);
    $("#txtDescripcionPromotor").Editor("getText");

    $("#txtHistoriaPromotor").Editor("setText", (promotor == undefined) ? '<p /style="color:black"></p>' : promotor.historia);
    $("#txtHistoriaPromotor").Editor("getText");
    if (promotor !== undefined) {

        $("#imagePreview").html(
            `<img width='244' alt='user-picture' class='img-responsive center-box' src='${getHostFrontEnd()}adjuntos/team/${promotor.foto}' />`
        );
        $("#imagePreview2").html(
            `<img width='244' alt='user-picture' class='img-responsive center-box' src='${getHostFrontEnd()}adjuntos/team/${promotor.fotoPortada}' />`
        );
    } else {
        $("#imagePreview").html(
            ""
        );
        $("#imagePreview2").html(
            ""
        );
    }


    addViewArchivosPrevius();

}

function listaPromotor(beanPagination) {
    document.querySelector('#tbodyPromotor').innerHTML = '';
    document.querySelector('#titleManagerPromotor').innerHTML =
        '[ ' + beanPagination.countFilter + ' ] Promotores';
    let row = "";

    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationPromotor'));
        return;
    }
    document.querySelector('#tbodyPromotor').innerHTML += row;
    let html2;
    beanPagination.list.forEach((promotor) => {

        row += `<tr  idpromotor="${promotor.idpromotor}">
<td class="text-center">${promotor.apellido + " " + promotor.nombre}</td>
<td class="text-center">${promotor.email}</td>
<td class="text-center">${promotor.youtube}</td>
<td class="text-justify"><p class="pr-1" style="max-height: 6.3em;overflow: auto;">${promotor.descripcion}</p></td>
<td class="text-center "><img src="${getHostFrontEnd()}adjuntos/team/${promotor.foto}" alt="${promotor.foto}" class="img-responsive center-box" style="width:100%;height:60px;"></td>
<td class="text-center "><img src="${getHostFrontEnd()}adjuntos/team/${promotor.fotoPortada}" alt="${promotor.fotoPortada}" class="img-responsive center-box" style="width:50px;height:60px;"></td>
<td class="text-center">
<button class="btn btn-info editar-promotor" ><i class="zmdi zmdi-refresh"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-promotor"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;

        // $('[data-toggle="tooltip"]').tooltip();
    });


    document.querySelector('#tbodyPromotor').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePagePromotor").value),
        document.querySelector("#pagePromotor"),
        $('#modalCargandoPromotor'),
        $('#paginationPromotor'));
    addEventsButtonsAdmin();


}

function addEventsButtonsAdmin() {
    document.querySelectorAll('.editar-promotor').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            promotorSelected = findByPromotor(
                btn.parentElement.parentElement.getAttribute('idpromotor')
            );

            if (promotorSelected != undefined) {
                addPromotor(promotorSelected);
                $("#tituloModalManPromotor").html("EDITAR PROMOTOR");
                $("#ventanaModalManPromotor").modal("show");
                beanRequestPromotor.type_request = 'POST';
                beanRequestPromotor.operation = 'update';
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
    document.querySelectorAll('.eliminar-promotor').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            promotorSelected = findByPromotor(
                btn.parentElement.parentElement.getAttribute('idpromotor')
            );

            if (promotorSelected != undefined) {
                beanRequestPromotor.type_request = 'GET';
                beanRequestPromotor.operation = 'delete';
                $('#modalCargandoPromotor').modal('show');
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
}

var validateFormPromotor = () => {
    if (document.querySelector("#txtNombrePromotor").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Nombres",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#ApellidoPromotor").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Apellidos",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtFacebookPromotor").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Facebook",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtYoutubePromotor").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Youtube",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if ($("#txtDescripcionPromotor").Editor("getText") == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Descripción",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if ($("#txtHistoriaPromotor").Editor("getText") == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Historia",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (beanRequestPromotor.operation == "add") {
        if (document.querySelector("#txtFotoPromotor").files.length == 0) {
            swal({
                title: "Vacío",
                text: "Ingrese Foto",
                type: "warning",
                timer: 1200,
                showConfirmButton: false
            });
            return false;
        }
        if (!(document.querySelector("#txtFotoPromotor").files[0].type == "image/png" || document.querySelector("#txtFotoPromotor").files[0].type == "image/jpg" || document.querySelector("#txtFotoPromotor").files[0].type == "image/jpeg")) {
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
        if (document.querySelector("#txtFotoPromotor").files[0].size > (3700 * 1024)) {
            swal({
                title: "Tamaño excedido",
                text: "el tamaño del archivo tiene que ser menor a 3700 KB",
                type: "warning",
                timer: 1200,
                showConfirmButton: false
            });
            return false;
        }

        if (document.querySelector("#txtFotoPortadaPromotor").files.length == 0) {
            swal({
                title: "Vacío",
                text: "Ingrese Foto Portada",
                type: "warning",
                timer: 1200,
                showConfirmButton: false
            });
            return false;
        }
        if (!(document.querySelector("#txtFotoPortadaPromotor").files[0].type == "image/png" || document.querySelector("#txtFotoPortadaPromotor").files[0].type == "image/jpg" || document.querySelector("#txtFotoPortadaPromotor").files[0].type == "image/jpeg")) {
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
        if (document.querySelector("#txtFotoPortadaPromotor").files[0].size > (3700 * 1024)) {
            swal({
                title: "Tamaño excedido",
                text: "el tamaño del archivo tiene que ser menor a 3700 KB",
                type: "warning",
                timer: 1200,
                showConfirmButton: false
            });
            return false;
        }
    } else {
        if (document.querySelector("#txtFotoPromotor").files.length !== 0) {
            if (!(document.querySelector("#txtFotoPromotor").files[0].type == "image/png" || document.querySelector("#txtFotoPromotor").files[0].type == "image/jpg" || document.querySelector("#txtFotoPromotor").files[0].type == "image/jpeg")) {
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
            if (document.querySelector("#txtFotoPromotor").files[0].size > (3700 * 1024)) {
                swal({
                    title: "Tamaño excedido",
                    text: "el tamaño del archivo tiene que ser menor a 3700 KB",
                    type: "warning",
                    timer: 1200,
                    showConfirmButton: false
                });
                return false;
            }


        }
        if (document.querySelector("#txtFotoPortadaPromotor").files.length !== 0) {
            if (!(document.querySelector("#txtFotoPortadaPromotor").files[0].type == "image/png" || document.querySelector("#txtFotoPortadaPromotor").files[0].type == "image/jpg" || document.querySelector("#txtFotoPortadaPromotor").files[0].type == "image/jpeg")) {
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
            if (document.querySelector("#txtFotoPortadaPromotor").files[0].size > (3700 * 1024)) {
                swal({
                    title: "Tamaño excedido",
                    text: "el tamaño del archivo tiene que ser menor a 3700 KB",
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

function addViewArchivosPrevius() {

    $("#txtFotoPromotor").change(function () {
        filePreview(this, "#imagePreview");
    });
    $("#txtFotoPortadaPromotor").change(function () {
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

function videoPreview(input, imagen) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        imagen;
        reader.onload = function (e) {
            $(imagen).html(
                "<video width='244' alt='user-picture' class='img-responsive center-box' controls ><source src='" +
                e.target.result +
                "' type='video/mp4'></video>"
            );
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function findIndexPromotor(idbusqueda) {
    return beanPaginationPromotor.list.findIndex(
        (Promotor) => {
            if (Promotor.idpromotor == parseInt(idbusqueda))
                return Promotor;


        }
    );
}

function findByPromotor(idpromotor) {
    return beanPaginationPromotor.list.find(
        (Promotor) => {
            if (parseInt(idpromotor) == Promotor.idpromotor) {
                return Promotor;
            }


        }
    );
}

/************************
****** NiceScroll *****
*************************/
// Remove NiceScroll dependacny
if ($.isFunction($.fn.niceScroll)) {
    $("html").niceScroll({ // The document page (body)
        cursorcolor: "#6ebff3",
        cursorborder: "0",
        zindex: 999999999
    });
    $(".navbar ul.mini").niceScroll({
        cursoropacitymax: 0,
        cursoropacitymin: 0,
        cursorborder: 0
    });
}
