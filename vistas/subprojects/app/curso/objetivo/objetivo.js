var beanPaginationPromotorObjetivo;
var objetivoSelected;
var beanRequestPromotorObjetivo = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestPromotorObjetivo.entity_api = 'subitems';
    beanRequestPromotorObjetivo.operation = 'paginate';
    beanRequestPromotorObjetivo.type_request = 'GET';

    document.querySelector("#tipoOpcionHeaderCurso").innerHTML = "Objetivo del Curso";

    $("#modalCargandoPromotorObjetivo").on('shown.bs.modal', function () {
        processAjaxPromotorObjetivo();
    });

    $("#formularioPromotorObjetivo").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();
        if (objetivoSelected != undefined) {
            beanRequestPromotorObjetivo.type_request = 'POST';
            beanRequestPromotorObjetivo.operation = 'update';
        } else {
            beanRequestPromotorObjetivo.type_request = 'POST';
            beanRequestPromotorObjetivo.operation = 'add';
        }

        $('#modalCargandoPromotorObjetivo').modal('show');


    });
    $("#txtDescripcionObjetivo").Editor();
    $("#txtImagenObjetivo").change(function () {
        filePreview(this, "#imagenPreview");
    });

});

function processAjaxPromotorObjetivo() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestPromotorObjetivo.operation == 'update' || beanRequestPromotorObjetivo.operation == 'add'
    ) {

        json = {
            idsubitem: 0,
            titulo: "",
            detalle: $("#txtDescripcionObjetivo").Editor("getText"),
            tipo: 4,
            curso: curso_cSelected.idcurso
        };
        if (document.querySelector("#txtImagenObjetivo").files.length !== 0) {
            let dataImagen = $("#txtImagenObjetivo").prop("files")[0];
            form_data.append("txtImagenObjetivo", dataImagen);

        }


    } else {
        form_data = null;
    }

    switch (beanRequestPromotorObjetivo.operation) {
        case 'update':
            json.idsubitem = objetivoSelected.idsubitem;
            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':
            form_data.append("class", JSON.stringify(json));
            break;
        case 'curso':
            parameters_pagination = '?idcurso=' + curso_cSelected.idcurso + '&tipo=4';
            break;
        default:

            parameters_pagination +=
                '?filtro=&tipo=4';
            parameters_pagination +=
                '&pagina=1&registros=1';
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestPromotorObjetivo.entity_api + "/" + beanRequestPromotorObjetivo.operation +
            parameters_pagination,
        type: beanRequestPromotorObjetivo.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestPromotorObjetivo.operation == 'update' || beanRequestPromotorObjetivo.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',
    }).done(function (beanCrudResponse) {

        $('#modalCargandoPromotorObjetivo').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                swal({
                    title: "Realizado",
                    text: "Acción realizada existosamente!",
                    type: "success",
                    timer: 1200,
                    showConfirmButton: false
                });

                $('#ventanaModalManPromotorObjetivo').modal('hide');
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

            beanPaginationPromotorObjetivo = beanCrudResponse.beanPagination;
            listaPromotorObjetivo(beanPaginationPromotorObjetivo);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoPromotorObjetivo').modal("hide");
        showAlertErrorRequest();

    });

}

function listaPromotorObjetivo(beanPagination) {

    let row = "";
    $("#txtDescripcionObjetivo").Editor("setText", row);
    $("#txtDescripcionObjetivo").Editor("getText");
    document.querySelector('#imagenPreview').innerHTML = '';
    if (beanPagination.list.length == 0) {
        return;
    }
    beanPagination.list.forEach((objetivo) => {
        objetivoSelected = objetivo;
    });
    /*document.querySelector('#imagenPreview').innerHTML = `
    <img width="100%" height="100%" alt='user-picture' class='img-responsive center-box' src='${getHostFrontEnd()}adjuntos/slider/${objetivoSelected.imagen}' />`;*/

    $("#txtDescripcionObjetivo").Editor("setText", objetivoSelected.detalle);
    $("#txtDescripcionObjetivo").Editor("getText");
    document.querySelectorAll('.btn-regresar-curso').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {

            document.querySelector('#cursoHTML').classList.remove("d-none");
            document.querySelector('#beneficioHTML').classList.add("d-none");

        };
    });
}

function findIndexPromotorObjetivo(idbusqueda) {
    return beanPaginationPromotorObjetivo.list.findIndex(
        (PromotorObjetivo) => {
            if (PromotorObjetivo.idobjetivo == parseInt(idbusqueda))
                return PromotorObjetivo;


        }
    );
}

function findByPromotorObjetivo(idobjetivo) {
    return beanPaginationPromotorObjetivo.list.find(
        (PromotorObjetivo) => {
            if (parseInt(idobjetivo) == PromotorObjetivo.idobjetivo) {
                return PromotorObjetivo;
            }


        }
    );
}

var validarDormularioObjetivo = () => {
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

//CURSO



function addEventsButtonsCurso_c() {
    document.querySelectorAll('.beneficio-curso').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            curso_cSelected = findByCurso_c(
                btn.parentElement.parentElement.getAttribute('idcurso')
            );

            if (curso_cSelected != undefined) {
                beanRequestPromotorObjetivo.type_request = 'GET';
                beanRequestPromotorObjetivo.operation = 'curso';
                addClass(document.querySelector("#cursoHTML"), "d-none");
                removeClass(document.querySelector("#beneficioHTML"), "d-none");
                $('#modalCargandoPromotorObjetivo').modal('show');
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });

}