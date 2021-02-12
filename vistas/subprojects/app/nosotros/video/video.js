var beanPaginationPromotorVideo;
var promotorVideoSelected;
var beanRequestPromotorVideo = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestPromotorVideo.entity_api = 'video/promotor';
    beanRequestPromotorVideo.operation = 'paginate';
    beanRequestPromotorVideo.type_request = 'GET';

    $('#sizePagePromotorVideo').change(function () {
        beanRequestPromotorVideo.type_request = 'GET';
        beanRequestPromotorVideo.operation = 'paginate';
        $('#modalCargandoPromotorVideo').modal('show');
    });

    $('#modalCargandoPromotorVideo').modal('show');

    $("#modalCargandoPromotorVideo").on('shown.bs.modal', function () {
        processAjaxPromotorVideo();
    });
    $("#ventanaModalManPromotorVideo").on('hide.bs.modal', function () {
        beanRequestPromotorVideo.type_request = 'GET';
        beanRequestPromotorVideo.operation = 'paginate';
    });

    $("#btnAbrirbook").click(function () {
        beanRequestPromotorVideo.operation = 'add';
        beanRequestPromotorVideo.type_request = 'POST';
        $("#imagePreview").html("");
        $("#tituloModalManPromotorVideo").html("REGISTRAR PROMOTOR");
        addPromotorVideo();
        $("#ventanaModalManPromotorVideo").modal("show");


    });
    document.querySelector("#txtYoutubePromotorVideo").onkeyup = (e) => {
        if (!document.querySelector("#txtYoutubePromotorVideo").value.includes("iframe")) {
            return;
        }
        setTimeout(() => {
            document.querySelector("#youtubePreview").innerHTML = (e.target.value).replace('560', '100%');
        }, 1000);
    }
    $("#formularioPromotorVideo").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (validarDormularioVideo()) {
            $('#modalCargandoPromotorVideo').modal('show');
        }
    });

});

function processAjaxPromotorVideo() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestPromotorVideo.operation == 'update' ||
        beanRequestPromotorVideo.operation == 'add'
    ) {

        json = {
            nombre: document.querySelector("#txttituloPromotorVideo").value,
            enlace: document.querySelector("#txtYoutubePromotorVideo").value
        };


    } else {
        form_data = null;
    }

    switch (beanRequestPromotorVideo.operation) {
        case 'delete':
            parameters_pagination = '?id=' + promotorVideoSelected.idvideo;
            break;

        case 'update':
            json.idvideo = promotorVideoSelected.idvideo;
            if (document.querySelector("#txtImagenPromotorVideo").files.length !== 0) {
                let dataImagen = $("#txtImagenPromotorVideo").prop("files")[0];
                form_data.append("txtImagenPromotorVideo", dataImagen);
            }

            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':

            let dataFot = $("#txtImagenPromotorVideo").prop("files")[0];
            form_data.append("txtImagenPromotorVideo", dataFot);

            form_data.append("class", JSON.stringify(json));
            break;

        default:

            parameters_pagination +=
                '?filtro=';
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pagePromotorVideo").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePagePromotorVideo").value.trim();
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestPromotorVideo.entity_api + "/" + beanRequestPromotorVideo.operation +
            parameters_pagination,
        type: beanRequestPromotorVideo.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestPromotorVideo.operation == 'update' || beanRequestPromotorVideo.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',
    }).done(function (beanCrudResponse) {

        $('#modalCargandoPromotorVideo').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                document.querySelector("#pagePromotorVideo").value = 1;
                document.querySelector("#sizePagePromotorVideo").value = 20;
                $('#ventanaModalManPromotorVideo').modal('hide');
            } else {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationPromotorVideo = beanCrudResponse.beanPagination;
            listaPromotorVideo(beanPaginationPromotorVideo);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoPromotorVideo').modal("hide");
        showAlertErrorRequest();

    });

}

function addPromotorVideo(promotorVideo = undefined) {
    //LIMPIAR LOS CAMPOS

    document.querySelector('#txttituloPromotorVideo').value = (promotorVideo == undefined) ? '' : promotorVideo.nombre;

    document.querySelector('#txtYoutubePromotorVideo').value = (promotorVideo == undefined) ? '' : promotorVideo.enlace;

    if (promotorVideo !== undefined) {

        $("#imagePreview").html(
            `<img class="w-100" alt='user-picture' class='img-responsive center-box' src='${getHostFrontEnd()}adjuntos/video-imagenes/${promotorVideo.imagen}' />`
        );
        if (promotorVideo.enlace.includes("iframe")) {
            document.querySelector("#youtubePreview").innerHTML = promotorVideo.enlace;
        }


    } else {
        $("#imagePreview").html(
            ""
        );
        document.querySelector("#youtubePreview").innerHTML = "";

    }


    addViewArchivosPrevius();

}

function listaPromotorVideo(beanPagination) {
    document.querySelector('#tbodyPromotorVideo').innerHTML = '';
    document.querySelector('#titleManagerPromotorVideo').innerHTML =
        '[ ' + beanPagination.countFilter + ' ] Promotor Videos';
    let row = "";

    /*   if (beanPagination.length == 0) {
           destroyPagination($('#paginationPromotorVideo'));
           showAlertTopEnd('warning', 'No se encontraron resultados');
           document.querySelector('#tbodyPromotorVideo').innerHTML += row;
           return;
       }
   */
    document.querySelector('#tbodyPromotorVideo').innerHTML += row;
    let html2;
    beanPagination.list.forEach((promotorVideo) => {

        row += `<tr idvideo="${promotorVideo.idvideo}">
<td class="text-center">${promotorVideo.nombre}</td>
<td class="text-center "><img src="${getHostFrontEnd()}adjuntos/video-imagenes/${promotorVideo.imagen}" alt="${promotorVideo.imagen}" class="img-responsive center-box" style="height:60px;"></td>
<td class="text-center"  style="width:20%;">${((promotorVideo.enlace).replace('560', '100%')).replace('315', '100%')}</td>
<td class="text-center">
<button class="btn btn-info editar-promotorVideo-video" ><i class="zmdi zmdi-refresh"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-promotorVideo-video"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;

        // $('[data-toggle="tooltip"]').tooltip();
    });


    document.querySelector('#tbodyPromotorVideo').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePagePromotorVideo").value),
        document.querySelector("#pagePromotorVideo"),
        $('#modalCargandoPromotorVideo'),
        $('#paginationPromotorVideo'));
    addEventsButtonsAdmin();


}

function addEventsButtonsAdmin() {
    document.querySelectorAll('.editar-promotorVideo-video').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            promotorVideoSelected = findByPromotorVideo(
                btn.parentElement.parentElement.getAttribute('idvideo')
            );

            if (promotorVideoSelected != undefined) {
                addPromotorVideo(promotorVideoSelected);
                $("#tituloModalManPromotorVideo").html("EDITAR VIDEO DE PROMOTORES");
                $("#ventanaModalManPromotorVideo").modal("show");
                beanRequestPromotorVideo.type_request = 'POST';
                beanRequestPromotorVideo.operation = 'update';
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
    document.querySelectorAll('.eliminar-promotorVideo-video').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            promotorVideoSelected = findByPromotorVideo(
                btn.parentElement.parentElement.getAttribute('idvideo')
            );

            if (promotorVideoSelected != undefined) {
                beanRequestPromotorVideo.type_request = 'GET';
                beanRequestPromotorVideo.operation = 'delete';
                $('#modalCargandoPromotorVideo').modal('show');
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

    $("#txtImagenPromotorVideo").change(function () {
        filePreview(this, "#imagePreview");
    });
    $("#txtImagenPortadaPromotorVideo").change(function () {
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

function findIndexPromotorVideo(idbusqueda) {
    return beanPaginationPromotorVideo.list.findIndex(
        (PromotorVideo) => {
            if (PromotorVideo.idvideo == parseInt(idbusqueda))
                return PromotorVideo;


        }
    );
}

function findByPromotorVideo(idvideo) {
    return beanPaginationPromotorVideo.list.find(
        (PromotorVideo) => {
            if (parseInt(idvideo) == PromotorVideo.idvideo) {
                return PromotorVideo;
            }


        }
    );
}

var validarDormularioVideo = () => {
    if (document.querySelector("#txttituloPromotorVideo").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Título",
            type: "warning",
            timer: 800,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtYoutubePromotorVideo").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Enlace de Youtube",
            type: "warning",
            timer: 800,
            showConfirmButton: false
        });
        return false;
    }
    if (beanRequestPromotorVideo.operation == "add") {
        if (document.querySelector("#txtImagenPromotorVideo").files.length == 0) {
            swal({
                title: "Vacío",
                text: "Ingrese Foto",
                type: "warning",
                timer: 1200,
                showConfirmButton: false
            });
            return false;
        }
        if (!(document.querySelector("#txtImagenPromotorVideo").files[0].type == "image/png" || document.querySelector("#txtImagenPromotorVideo").files[0].type == "image/jpg" || document.querySelector("#txtImagenPromotorVideo").files[0].type == "image/jpeg")) {
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
        if (document.querySelector("#txtImagenPromotorVideo").files[0].size > (3700 * 1024)) {
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
        if (document.querySelector("#txtImagenPromotorVideo").files.length !== 0) {
            if (!(document.querySelector("#txtImagenPromotorVideo").files[0].type == "image/png" || document.querySelector("#txtImagenPromotorVideo").files[0].type == "image/jpg" || document.querySelector("#txtImagenPromotorVideo").files[0].type == "image/jpeg")) {
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
            if (document.querySelector("#txtImagenPromotorVideo").files[0].size > (3700 * 1024)) {
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