var beanPaginationPromotorBeneficio;
var beneficioSelected;
var beanRequestPromotorBeneficio = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestPromotorBeneficio.entity_api = 'subitems';
    beanRequestPromotorBeneficio.operation = 'paginate';
    beanRequestPromotorBeneficio.type_request = 'GET';

    document.querySelector("#tipoOpcionHeaderCurso").innerHTML = "Cuadro de Compra";

    $("#modalCargandoPromotorBeneficio").on('shown.bs.modal', function () {
        processAjaxPromotorBeneficio();
    });

    $("#formularioPromotorBeneficio").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();
        if (beneficioSelected != undefined) {
            beanRequestPromotorBeneficio.type_request = 'POST';
            beanRequestPromotorBeneficio.operation = 'update';
        } else {
            beanRequestPromotorBeneficio.type_request = 'POST';
            beanRequestPromotorBeneficio.operation = 'add';
        }


        $('#modalCargandoPromotorBeneficio').modal('show');


    });
    $("#txtDescripcionBeneficio").Editor();
    $("#txtImagenBeneficio").change(function () {
        filePreview(this, "#imagenPreview");
    });

});

function processAjaxPromotorBeneficio() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestPromotorBeneficio.operation == 'update' || beanRequestPromotorBeneficio.operation == 'add'
    ) {

        json = {
            idsubitem: 0,
            titulo: "",
            detalle: $("#txtDescripcionBeneficio").Editor("getText"),
            tipo: 1,
            curso: curso_cSelected.idcurso
        };
        if (document.querySelector("#txtImagenBeneficio").files.length !== 0) {
            let dataImagen = $("#txtImagenBeneficio").prop("files")[0];
            form_data.append("txtImagenBeneficio", dataImagen);

        }


    } else {
        form_data = null;
    }

    switch (beanRequestPromotorBeneficio.operation) {
        case 'update':
            json.idsubitem = beneficioSelected.idsubitem;
            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':
            form_data.append("class", JSON.stringify(json));
            break;
        case 'curso':
            parameters_pagination = '?idcurso=' + curso_cSelected.idcurso + '&tipo=1';
            break;
        default:

            parameters_pagination +=
                '?filtro=&tipo=4';
            parameters_pagination +=
                '&pagina=1&registros=1';
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestPromotorBeneficio.entity_api + "/" + beanRequestPromotorBeneficio.operation +
            parameters_pagination,
        type: beanRequestPromotorBeneficio.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestPromotorBeneficio.operation == 'update' || beanRequestPromotorBeneficio.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',
    }).done(function (beanCrudResponse) {

        $('#modalCargandoPromotorBeneficio').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                swal({
                    title: "Realizado",
                    text: "Acción realizada existosamente!",
                    type: "success",
                    timer: 1200,
                    showConfirmButton: false
                });

                $('#ventanaModalManPromotorBeneficio').modal('hide');
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

            beanPaginationPromotorBeneficio = beanCrudResponse.beanPagination;
            listaPromotorBeneficio(beanPaginationPromotorBeneficio);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoPromotorBeneficio').modal("hide");
        showAlertErrorRequest();

    });

}

function listaPromotorBeneficio(beanPagination) {

    let row = "";
    $("#txtDescripcionBeneficio").Editor("setText", row);
    $("#txtDescripcionBeneficio").Editor("getText");
    document.querySelector('#imagenPreview').innerHTML = '';
    if (beanPagination.list.length == 0) {
        return;
    }
    beanPagination.list.forEach((beneficio) => {
        beneficioSelected = beneficio;
    });
    /*document.querySelector('#imagenPreview').innerHTML = `
    <img width="100%" height="100%" alt='user-picture' class='img-responsive center-box' src='${getHostFrontEnd()}adjuntos/slider/${beneficioSelected.imagen}' />`;*/

    $("#txtDescripcionBeneficio").Editor("setText", beneficioSelected.detalle);
    $("#txtDescripcionBeneficio").Editor("getText");
    document.querySelectorAll('.btn-regresar-curso').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {

            document.querySelector('#cursoHTML').classList.remove("d-none");
            document.querySelector('#beneficioHTML').classList.add("d-none");

        };
    });
}

function findIndexPromotorBeneficio(idbusqueda) {
    return beanPaginationPromotorBeneficio.list.findIndex(
        (PromotorBeneficio) => {
            if (PromotorBeneficio.idbeneficio == parseInt(idbusqueda))
                return PromotorBeneficio;


        }
    );
}

function findByPromotorBeneficio(idbeneficio) {
    return beanPaginationPromotorBeneficio.list.find(
        (PromotorBeneficio) => {
            if (parseInt(idbeneficio) == PromotorBeneficio.idbeneficio) {
                return PromotorBeneficio;
            }


        }
    );
}

var validarDormularioBeneficio = () => {
    if (document.querySelector("#txtImagenBeneficio").files.length !== 0) {

        if (!(document.querySelector("#txtImagenBeneficio").files[0].type == "image/png" || document.querySelector("#txtImagenBeneficio").files[0].type == "image/jpg" || document.querySelector("#txtImagenBeneficio").files[0].type == "image/jpeg")) {
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
        if (document.querySelector("#txtImagenBeneficio").files[0].size > (10 * 1024 * 1024)) {
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
                beanRequestPromotorBeneficio.type_request = 'GET';
                beanRequestPromotorBeneficio.operation = 'curso';
                addClass(document.querySelector("#cursoHTML"), "d-none");
                removeClass(document.querySelector("#beneficioHTML"), "d-none");
                $('#modalCargandoPromotorBeneficio').modal('show');
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });

}