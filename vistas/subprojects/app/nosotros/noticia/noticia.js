var beanPaginationPromotorNoticia;
var noticiaSelected;
var beanRequestPromotorNoticia = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestPromotorNoticia.entity_api = 'subitems';
    beanRequestPromotorNoticia.operation = 'paginate';
    beanRequestPromotorNoticia.type_request = 'GET';

    $('#modalCargandoPromotorNoticia').modal('show');

    $("#modalCargandoPromotorNoticia").on('shown.bs.modal', function () {
        processAjaxPromotorNoticia();
    });

    $("#formularioPromotorNoticia").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();
        beanRequestPromotorNoticia.type_request = 'POST';
        beanRequestPromotorNoticia.operation = 'update';
        if (validarDormularioNoticia()) {
            $('#modalCargandoPromotorNoticia').modal('show');
        }

    });

    $("#txtNoticiaInicio").change(function () {
        filePreview(this, "#imagenPreview");
    });

});

function processAjaxPromotorNoticia() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestPromotorNoticia.operation == 'update'
    ) {

        json = {
            idsubitem: (noticiaSelected == undefined ? 0 : noticiaSelected.idsubitem),
            titulo: (noticiaSelected == undefined ? "" : noticiaSelected.titulo),
            detalle: (noticiaSelected == undefined ? "" : noticiaSelected.detalle),
            tipo: 6
        };
        let dataImagen = $("#txtNoticiaInicio").prop("files")[0];
        form_data.append("txtImagenObjetivo", dataImagen);
        form_data.append("class", JSON.stringify(json));

    } else {
        form_data = null;
    }

    switch (beanRequestPromotorNoticia.operation) {
        case 'update':

            break;
        default:

            parameters_pagination +=
                '?tipo=6';
            parameters_pagination +=
                '&pagina=1&registros=1';
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestPromotorNoticia.entity_api + "/" + beanRequestPromotorNoticia.operation +
            parameters_pagination,
        type: beanRequestPromotorNoticia.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },

        data: form_data,
        cache: false,
        contentType: ((beanRequestPromotorNoticia.operation == 'update' || beanRequestPromotorNoticia.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',
    }).done(function (beanCrudResponse) {

        $('#modalCargandoPromotorNoticia').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                swal({
                    title: "Realizado",
                    text: "Acción realizada existosamente!",
                    type: "success",
                    timer: 1200,
                    showConfirmButton: false
                });

                $('#ventanaModalManPromotorNoticia').modal('hide');
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

            beanPaginationPromotorNoticia = beanCrudResponse.beanPagination;
            listaPromotorNoticia(beanPaginationPromotorNoticia);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoPromotorNoticia').modal("hide");
        showAlertErrorRequest();

    });

}

function listaPromotorNoticia(beanPagination) {
    document.querySelector('#imagenPreview').innerHTML = '';
    let row = "";

    beanPagination.list.forEach((noticia) => {
        noticiaSelected = noticia;
        row += `
        <img width="100%" height="100%" alt='user-picture' class='img-responsive center-box' src='${getHostFrontEnd()}adjuntos/slider/${noticia.imagen}' />`;
    });

    document.querySelector('#imagenPreview').innerHTML += row;

}

function findIndexPromotorNoticia(idbusqueda) {
    return beanPaginationPromotorNoticia.list.findIndex(
        (PromotorNoticia) => {
            if (PromotorNoticia.idnoticia == parseInt(idbusqueda))
                return PromotorNoticia;


        }
    );
}

function findByPromotorNoticia(idnoticia) {
    return beanPaginationPromotorNoticia.list.find(
        (PromotorNoticia) => {
            if (parseInt(idnoticia) == PromotorNoticia.idnoticia) {
                return PromotorNoticia;
            }


        }
    );
}

var validarDormularioNoticia = () => {



    if (document.querySelector("#txtNoticiaInicio").files.length !== 0) {
        if (!(document.querySelector("#txtNoticiaInicio").files[0].type == "image/png" || document.querySelector("#txtNoticiaInicio").files[0].type == "image/jpg" || document.querySelector("#txtNoticiaInicio").files[0].type == "image/jpeg")) {
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
        if (document.querySelector("#txtNoticiaInicio").files[0].size > (10 * 1024 * 1024)) {
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
    return true;
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