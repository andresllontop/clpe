var beanPaginationPromotorVideo;
var promotorVideoSelected;
var beanRequestPromotorVideo = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestPromotorVideo.entity_api = 'video/inicio';
    beanRequestPromotorVideo.operation = 'paginate';
    beanRequestPromotorVideo.type_request = 'GET';

    $('#modalCargandoPromotorVideo').modal('show');

    $("#modalCargandoPromotorVideo").on('shown.bs.modal', function () {
        processAjaxPromotorVideo();
    });

    $("#formularioPromotorVideo").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();
        beanRequestPromotorVideo.type_request = 'POST';
        beanRequestPromotorVideo.operation = 'update';
        if (validarDormularioVideo()) {
            $('#modalCargandoPromotorVideo').modal('show');
        }

    });

    $("#txtVideoInicio").change(function () {
        videoPreview(this, "#videoPreview");
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
            idvideo: (promotorVideoSelected == undefined ? 0 : promotorVideoSelected.idvideo)
        };
        let dataImagen = $("#txtVideoInicio").prop("files")[0];
        form_data.append("txtVideoInicio", dataImagen);
        form_data.append("class", JSON.stringify(json));

    } else {
        form_data = null;
    }

    switch (beanRequestPromotorVideo.operation) {
        case 'update':

            break;
        default:

            parameters_pagination +=
                '?filtro=';
            parameters_pagination +=
                '&pagina=1&registros=1';
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

function listaPromotorVideo(beanPagination) {
    document.querySelector('#videoPreview').innerHTML = '';
    let row = "";

    beanPagination.list.forEach((promotorVideo) => {
        promotorVideoSelected = promotorVideo;
        row += `<video width='100%' alt='user-picture' class='img-responsive center-box' controls ><source src='${getHostFrontEnd()}adjuntos/videos/${promotorVideo.archivo}' type='video/mp4'></video>`;
    });

    document.querySelector('#videoPreview').innerHTML += row;

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

    if (document.querySelector("#txtVideoInicio").files.length == 0) {
        swal({
            title: "Vacío",
            text: "Ingrese Nuevo Video",
            type: "warning",
            timer: 800,
            showConfirmButton: false
        });
        return false;
    }
    return true;
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