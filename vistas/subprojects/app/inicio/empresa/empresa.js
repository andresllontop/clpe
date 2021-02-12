var beanPaginationEmpresa;
var empresaSelected;
var beanRequestEmpresa = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestEmpresa.entity_api = 'empresa';
    beanRequestEmpresa.operation = 'obtener';
    beanRequestEmpresa.type_request = 'GET';

    $('#modalCargandoEmpresa').modal('show');

    $("#modalCargandoEmpresa").on('shown.bs.modal', function () {
        processAjaxEmpresa();
    });

    $("#formularioEmpresa").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();
        beanRequestEmpresa.type_request = 'POST';
        beanRequestEmpresa.operation = 'update';
        if (validarDormularioVideo()) {
            $('#modalCargandoEmpresa').modal('show');
        }

    });

});

function processAjaxEmpresa() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestEmpresa.operation == 'update' ||
        beanRequestEmpresa.operation == 'add'
    ) {

        json = {
            idempresa: empresaSelected.idempresa,
            descripcion: empresaSelected.descripcion,
            direccion: document.querySelector("#txtDireccionEmpresa").value,
            email: document.querySelector("#txtEmailEmpresa").value,
            enlace: document.querySelector("#txtUrlEmpresa").value,
            facebook: document.querySelector("#txtFacebookEmpresa").value,
            instagram: document.querySelector("#txtInstagramEmpresa").value,
            logo: empresaSelected.logo,
            mision: empresaSelected.mision,
            nombre: document.querySelector("#txtNombreEmpresa").value,
            precio: document.querySelector("#txtPrecioEmpresa").value,
            telefono: document.querySelector("#txtTelefonoEmpresa").value,
            telefonoSegundo: document.querySelector("#txtTelefono2Empresa").value,
            vision: empresaSelected.vision,
            youtube: document.querySelector("#txtYoutubeEmpresa").value,
            frase: document.querySelector("#txtFraseEmpresa").value
        };
        if (document.querySelector("#txtLogoEmpresa").files.length != 0) {
            let dataImagen = $("#txtLogoEmpresa").prop("files")[0];
            form_data.append("txtLogoEmpresa", dataImagen);
        }
        form_data.append("class", JSON.stringify(json));

    } else {
        form_data = null;
    }

    switch (beanRequestEmpresa.operation) {
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
        url: getHostAPI() + beanRequestEmpresa.entity_api + "/" + beanRequestEmpresa.operation +
            parameters_pagination,
        type: beanRequestEmpresa.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestEmpresa.operation == 'update' || beanRequestEmpresa.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',
    }).done(function (beanCrudResponse) {

        $('#modalCargandoEmpresa').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");

                $('#ventanaModalManEmpresa').modal('hide');
            } else {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationEmpresa = beanCrudResponse.beanPagination;
            listaEmpresa(beanPaginationEmpresa);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoEmpresa').modal("hide");
        showAlertErrorRequest();

    });

}

function listaEmpresa(beanPagination) {
    beanPagination.list.forEach((empresa) => {
        empresaSelected = empresa;
    });
    if (empresaSelected == undefined) {
        return;
    }
    document.querySelector("#txtNombreEmpresa").value = empresaSelected.nombre;
    document.querySelector("#txtInstagramEmpresa").value = empresaSelected.instagram;
    document.querySelector("#txtFraseEmpresa").value = empresaSelected.frase;
    document.querySelector("#txtPrecioEmpresa").value = empresaSelected.precio;
    document.querySelector("#txtDireccionEmpresa").value = empresaSelected.direccion;
    document.querySelector("#txtEmailEmpresa").value = empresaSelected.email;
    document.querySelector("#txtUrlEmpresa").value = empresaSelected.enlace;
    document.querySelector("#txtTelefonoEmpresa").value = empresaSelected.telefono;
    document.querySelector("#txtTelefono2Empresa").value = empresaSelected.telefonoSegundo;
    document.querySelector("#txtFacebookEmpresa").value = empresaSelected.facebook;
    document.querySelector("#txtYoutubeEmpresa").value = empresaSelected.youtube;
    $("#imagePreview").html(
        `<img style="width:125px;height:189px;" alt='user-picture' class='img-responsive center-box' src='${getHostFrontEnd()}adjuntos/${empresaSelected.logo}' />`
    );
    addViewArchivosPreviusEmpresa();

}

function findIndexEmpresa(idbusqueda) {
    return beanPaginationEmpresa.list.findIndex(
        (Empresa) => {
            if (Empresa.idvideo == parseInt(idbusqueda))
                return Empresa;


        }
    );
}

function findByEmpresa(idvideo) {
    return beanPaginationEmpresa.list.find(
        (Empresa) => {
            if (parseInt(idvideo) == Empresa.idvideo) {
                return Empresa;
            }


        }
    );
}
function addViewArchivosPreviusEmpresa() {

    $("#txtLogoEmpresa").change(function () {
        filePreview(this, "#imagePreview");
    });

}
function filePreview(input, imagen) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        imagen;
        reader.onload = function (e) {
            $(imagen).html(
                "<img style='width: 125px; height: 189px; ' alt='user-picture' class='img-responsive center-box' src='" +
                e.target.result +
                "' />"
            );
        };
        reader.readAsDataURL(input.files[0]);
    }
}
var validarDormularioVideo = () => {
    if (document.querySelector("#txtNombreEmpresa").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Nombre",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtPrecioEmpresa").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Precio",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtDireccionEmpresa").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Dirección",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (!(/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/g.test(document.querySelector("#txtEmailEmpresa").value))) {
        swal({
            title: "Vacío",
            text: "Ingrese formato de Correo Electrónico válido",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtUrlEmpresa").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Enlace URL",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }

    if (document.querySelector("#txtTelefonoEmpresa").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese el primer Teléfono",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtTelefono2Empresa").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese el segundo Teléfono",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtFraseEmpresa").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Frase",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtLogoEmpresa").files.length !== 0) {
        if (!(document.querySelector("#txtLogoEmpresa").files[0].type == "image/png" || document.querySelector("#txtLogoEmpresa").files[0].type == "image/jpg" || document.querySelector("#txtLogoEmpresa").files[0].type == "image/jpeg")) {
            swal({
                title: "Formato Incorrecto",
                text: "Ingrese formato png, jpeg y jpg",
                type: "warning",
                timer: 1200,
                showConfirmButton: false
            });
            return false;
        }
        //menor a   5000 KB
        if (document.querySelector("#txtLogoEmpresa").files[0].size > (5000 * 1024)) {
            swal({
                title: "Tamaño excedido",
                text: "el tamaño del archivo tiene que ser menor a 5000 KB",
                type: "warning",
                timer: 1200,
                showConfirmButton: false
            });
            return false;
        }
    }


    return true;
}
