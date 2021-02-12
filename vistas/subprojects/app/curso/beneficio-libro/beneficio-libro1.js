var beanPaginationPromotorBeneficioLibro;
var beneficioLibroSelected;
var beanRequestPromotorBeneficioLibro = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestPromotorBeneficioLibro.entity_api = 'subitems';
    beanRequestPromotorBeneficioLibro.operation = 'paginate';
    beanRequestPromotorBeneficioLibro.type_request = 'GET';

    $('#modalCargandoPromotorBeneficioLibro').modal('show');

    $("#modalCargandoPromotorBeneficioLibro").on('shown.bs.modal', function () {
        processAjaxPromotorBeneficioLibro();
    });

    $("#formularioPromotorBeneficioLibro").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();
        beanRequestPromotorBeneficioLibro.type_request = 'POST';
        beanRequestPromotorBeneficioLibro.operation = 'update';
        if (validarDormularioBeneficioLibro()) {
            $('#modalCargandoPromotorBeneficioLibro').modal('show');
        }

    });

    $("#txtImagenObjetivo").change(function () {
        filePreview(this, "#imagenPreview");
    });

});

function processAjaxPromotorBeneficioLibro() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestPromotorBeneficioLibro.operation == 'update'
    ) {

        json = {
            idsubitem: (beneficioLibroSelected == undefined ? 0 : beneficioLibroSelected.idsubitem),
            titulo: "",
            detalle: (beneficioLibroSelected == undefined ? 0 : beneficioLibroSelected.detalle),
            tipo: 2
        };
        if (document.querySelector("#txtImagenObjetivo").files.length !== 0) {
            let dataImagen = $("#txtImagenObjetivo").prop("files")[0];
            form_data.append("txtImagenObjetivo", dataImagen);

        }
        form_data.append("class", JSON.stringify(json));

    } else {
        form_data = null;
    }

    switch (beanRequestPromotorBeneficioLibro.operation) {
        case 'update':

            break;
        default:

            parameters_pagination +=
                '?filtro=&tipo=2';
            parameters_pagination +=
                '&pagina=1&registros=1';
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestPromotorBeneficioLibro.entity_api + "/" + beanRequestPromotorBeneficioLibro.operation +
            parameters_pagination,
        type: beanRequestPromotorBeneficioLibro.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestPromotorBeneficioLibro.operation == 'update' || beanRequestPromotorBeneficioLibro.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',
    }).done(function (beanCrudResponse) {

        $('#modalCargandoPromotorBeneficioLibro').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                swal({
                    title: "Realizado",
                    text: "Acción realizada existosamente!",
                    type: "success",
                    timer: 1200,
                    showConfirmButton: false
                });

                $('#ventanaModalManPromotorBeneficioLibro').modal('hide');
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

            beanPaginationPromotorBeneficioLibro = beanCrudResponse.beanPagination;
            listaPromotorBeneficioLibro(beanPaginationPromotorBeneficioLibro);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoPromotorBeneficioLibro').modal("hide");
        showAlertErrorRequest();

    });

}

function listaPromotorBeneficioLibro(beanPagination) {
    document.querySelector('#imagenPreview').innerHTML = '';
    let row = "";

    beanPagination.list.forEach((beneficioLibro) => {
        beneficioLibroSelected = beneficioLibro;
        row += `
        <img width="100%" height="100%" alt='user-picture' class='img-responsive center-box' src='${getHostFrontEnd()}adjuntos/slider/${beneficioLibro.imagen}' />`;
    });

    document.querySelector('#imagenPreview').innerHTML += row;

}

function findIndexPromotorBeneficioLibro(idbusqueda) {
    return beanPaginationPromotorBeneficioLibro.list.findIndex(
        (PromotorBeneficioLibro) => {
            if (PromotorBeneficioLibro.idbeneficioLibro == parseInt(idbusqueda))
                return PromotorBeneficioLibro;


        }
    );
}

function findByPromotorBeneficioLibro(idbeneficioLibro) {
    return beanPaginationPromotorBeneficioLibro.list.find(
        (PromotorBeneficioLibro) => {
            if (parseInt(idbeneficioLibro) == PromotorBeneficioLibro.idbeneficioLibro) {
                return PromotorBeneficioLibro;
            }


        }
    );
}

var validarDormularioBeneficioLibro = () => {
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