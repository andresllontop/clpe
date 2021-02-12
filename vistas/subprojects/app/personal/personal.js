var beanPaginationPersonal;
var personalSelected;
var checkTexto;
var beanRequestPersonal = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestPersonal.entity_api = 'administrador';
    beanRequestPersonal.operation = 'paginate';
    beanRequestPersonal.type_request = 'GET';

    $('#sizePagePersonal').change(function () {
        beanRequestPersonal.type_request = 'GET';
        beanRequestPersonal.operation = 'paginate';
        $('#modalCargandoPersonal').modal('show');
    });

    $('#modalCargandoPersonal').modal('show');

    $("#modalCargandoPersonal").on('shown.bs.modal', function () {
        processAjaxPersonal();
    });
    $("#btnAbrirPersonal").click(function () {
        beanRequestPersonal.operation = 'add';
        beanRequestPersonal.type_request = 'POST';
        $("#tituloModalManPersonal").html("REGISTRAR ADMINISTRADOR");
        $("#ventanaModalManPersonal").modal("show");
        addPersonal();


    });
    $("#formularioPersonal").submit(function (event) {
        if (validarDormularioVideo()) {
            $('#modalCargandoPersonal').modal('show');
        }
        event.preventDefault();
        event.stopPropagation();
    });
    document.querySelector("#ButtonPassword1").onclick = () => {

        if (document.querySelector("#ButtonPassword1").firstElementChild.className.includes("zmdi-eye-off")) {
            document.querySelector("#txtPasswordUsuario").setAttribute("type", "password");
            document.querySelector("#ButtonPassword1").firstElementChild.classList.remove("zmdi-eye-off");
            document.querySelector("#ButtonPassword1").firstElementChild.classList.add("zmdi-eye");
        } else {
            document.querySelector("#txtPasswordUsuario").setAttribute("type", "text");
            document.querySelector("#ButtonPassword1").firstElementChild.classList.remove("zmdi-eye");
            document.querySelector("#ButtonPassword1").firstElementChild.classList.add("zmdi-eye-off");
        }

    }
    document.querySelector("#txtNombrePersonal").onkeyup = (e) => {
        document.querySelector("#txtUsuarioName").value = e.target.value;
    }
    document.querySelector("#txtEmailUsuario").onkeyup = (e) => {
        document.querySelector("#txtPasswordUsuario").value = "(" + e.target.value + ")";
        document.querySelector("#txtPasswordUsuario2").value = "(" + e.target.value + ")";
    }
    document.querySelector("#txtPrivilegio1").onchange = (e) => {
        if (e.target.checked) {
            document.querySelector("#txtPrivilegio2").checked = true;
            document.querySelector("#txtPrivilegio3").checked = true;
            document.querySelector("#txtPrivilegio4").checked = true;
            document.querySelector("#txtPrivilegio5").checked = true;
            document.querySelector("#txtPrivilegio6").checked = true;
        } else {
            document.querySelector("#txtPrivilegio2").checked = false;
            document.querySelector("#txtPrivilegio3").checked = false;
            document.querySelector("#txtPrivilegio4").checked = false;
            document.querySelector("#txtPrivilegio5").checked = false;
            document.querySelector("#txtPrivilegio6").checked = false;
        }
    };

});

function processAjaxPersonal() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestPersonal.operation == 'update' ||
        beanRequestPersonal.operation == 'add'
    ) {

        json = {
            nombre: document.querySelector("#txtNombrePersonal").value,
            apellido: document.querySelector("#txtApellidoPersonal").value,
            telefono: document.querySelector("#txtTelefonoPersonal").value,
            pais: "",
            ocupacion: "",
            cuenta: {
                email: document.querySelector("#txtEmailUsuario").value,
                usuario: document.querySelector("#txtUsuarioName").value,
                clave: document.querySelector("#txtPasswordUsuario").value,
                perfil: checkTexto
            }
        };


    } else {
        form_data = null;
    }

    switch (beanRequestPersonal.operation) {
        case 'delete':
            parameters_pagination = '?id=' + personalSelected.idadministrador;
            break;

        case 'update':
            json.idadministrador = personalSelected.idadministrador;
            form_data.append("class", JSON.stringify(json));
            break;
        case 'updateestado':
            form_data = new FormData();
            json = {
                idcuenta: personalSelected.cuenta.idcuenta,
                codigo: personalSelected.cuenta.cuentaCodigo,
                estado: personalSelected.cuenta.estado
            };
            form_data.append("class", JSON.stringify(json));
            break;

        case 'add':

            form_data.append("class", JSON.stringify(json));
            break;

        default:

            parameters_pagination +=
                '?filtro=';
            parameters_pagination +=
                '&estado=-1';
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pagePersonal").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePagePersonal").value.trim();
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestPersonal.entity_api + "/" + beanRequestPersonal.operation +
            parameters_pagination,
        type: beanRequestPersonal.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestPersonal.operation == 'updateestado' || beanRequestPersonal.operation == 'update' || beanRequestPersonal.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',
    }).done(function (beanCrudResponse) {

        $('#modalCargandoPersonal').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                swal({
                    title: "Realizado",
                    text: "Acción realizada existosamente!",
                    type: "success",
                    timer: 2000,
                    showConfirmButton: false
                });

                $('#ventanaModalManPersonal').modal('hide');
            } else {

                swal({
                    title: "VERIFICACIÓN!",
                    text: beanCrudResponse.messageServer,
                    type: "info",
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationPersonal = beanCrudResponse.beanPagination;
            listaPersonal(beanPaginationPersonal);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoPersonal').modal("hide");
        showAlertErrorRequest();

    });

}

function addPersonal(personal = undefined) {
    //LIMPIAR LOS CAMPOS

    document.querySelector('#txtNombrePersonal').value = (personal == undefined) ? '' : personal.nombre;
    document.querySelector('#txtApellidoPersonal').value = (personal == undefined) ? '' : personal.apellido;

    document.querySelector('#txtTelefonoPersonal').value = (personal == undefined) ? '' : personal.telefono;
    document.querySelector('#txtUsuarioName').value = (personal == undefined) ? '' : personal.cuenta.usuario;
    document.querySelector('#txtEmailUsuario').value = (personal == undefined) ? '' : personal.cuenta.email;
    document.querySelector('#txtPasswordUsuario').value = (personal == undefined) ? '' : personal.cuenta.clave;
    document.querySelector('#txtPasswordUsuario2').value = (personal == undefined) ? '' : personal.cuenta.clave;

    if (personal !== undefined) {
        let $array=Array.from(personal.cuenta.perfil);

            document.querySelector("#txtPrivilegio1").checked = ($array[0] == 1) ? true : false;
            document.querySelector("#txtPrivilegio2").checked =  ($array[1] == 1) ? true : false;
            document.querySelector("#txtPrivilegio3").checked =  ($array[2] == 1) ? true : false;
            document.querySelector("#txtPrivilegio4").checked =  ($array[3] == 1) ? true : false;
            document.querySelector("#txtPrivilegio5").checked =  ($array[4] == 1) ? true : false;
            document.querySelector("#txtPrivilegio6").checked =  ($array[5] == 1) ? true : false;
        $("#imagePreview").html(
            `<img style='height:220px;width: 225px;'  alt='user-picture' class='img-responsive center-box rounded-circle' src='${getHostFrontEnd()}${(personal.cuenta.foto == "" || personal.cuenta.foto == null) ? "vistas/assets/img/userclpe.png" : "adjuntos/clientes/" + personal.cuenta.foto}' />`
        );

    } else {
          document.querySelector("#txtPrivilegio1").checked = false;
          document.querySelector("#txtPrivilegio2").checked = false;
            document.querySelector("#txtPrivilegio3").checked = false;
            document.querySelector("#txtPrivilegio4").checked = false;
            document.querySelector("#txtPrivilegio5").checked = false;
            document.querySelector("#txtPrivilegio6").checked = false;
        $("#imagePreview").html(
            `<img style='height:220px;width: 225px;'  alt='user-picture' class='img-responsive center-box img-circle' src='${getHostFrontEnd()}vistas/assets/img/userclpe.png' />`
        );

    }
    addViewArchivosPrevius();

}

function listaPersonal(beanPagination) {
    document.querySelector('#tbodyPersonal').innerHTML = '';
    document.querySelector('#titlePersonal').innerHTML =
        '[ ' + beanPagination.countFilter + ' ] LISTA DE PERSONAL ADMINISTRATIVO';
    let row = "";
    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationPersonal'));
        row += `<tr>
        <td class="text-center" colspan="10">NO HAY PERSONAL</td>
        </tr>`;

        document.querySelector('#tbodyPersonal').innerHTML += row;
        return;
    }
    beanPagination.list.forEach((personal) => {

        row += `<tr  idadministrador="${personal.idadministrador}">
<td class="text-center">${personal.cuenta.cuentaCodigo}</td>
<td class="text-center" >${personal.apellido + " " + personal.nombre}</td>
<td class="text-center" >${personal.telefono}</td>
<td class="text-center" >${personal.cuenta.email}</td>
<td class="text-center" >${personal.cuenta.usuario}</td>
<td class="text-center "><img src="${getHostFrontEnd()}${(personal.cuenta.foto == "" || personal.cuenta.foto == null) ? "vistas/assets/img/userclpe.png" : "adjuntos/clientes/" + personal.cuenta.foto}" alt="user-picture" class="img-responsive center-box" style="width:50px;height:60px;"></td>
<td class="text-center">
<button class="btn ${personal.cuenta.estado == 1 ? "btn-success" : "btn-warning"} editar-estado-personal" ><i class="zmdi ${personal.cuenta.estado == 1 ? "zmdi-check-all" : "zmdi-close"}"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-info editar-personal" ><i class="zmdi zmdi-refresh"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-personal"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;
    });


    document.querySelector('#tbodyPersonal').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePagePersonal").value),
        document.querySelector("#pagePersonal"),
        $('#modalCargandoPersonal'),
        $('#paginationPersonal'));
    addEventsButtonsAdmin();


}

function addEventsButtonsAdmin() {

    document.querySelectorAll('.editar-estado-personal').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            personalSelected = findByPersonal(
                btn.parentElement.parentElement.getAttribute('idadministrador')
            );
            if (personalSelected != undefined) {
                personalSelected.cuenta.estado = (parseInt(personalSelected.cuenta.estado) == 1 ? 0 : 1);
                beanRequestPersonal.type_request = 'POST';
                beanRequestPersonal.operation = 'updateestado';
                $('#modalCargandoPersonal').modal('show');
            } else {
                showAlertTopEnd("info", "Vacío!", "No se encontró el personal para poder editar");
            }
        };
    });
    document.querySelectorAll('.editar-personal').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            personalSelected = findByPersonal(
                btn.parentElement.parentElement.getAttribute('idadministrador')
            );

            if (personalSelected != undefined) {
                addPersonal(personalSelected);
                $("#tituloModalManPersonal").html("ACTUALIZAR ADMINISTRADOR");
                $("#ventanaModalManPersonal").modal("show");
                beanRequestPersonal.type_request = 'POST';
                beanRequestPersonal.operation = 'update';
            } else {
                showAlertTopEnd("warning", "No se encuentra el personal", "");
            }
        };
    });
    document.querySelectorAll('.eliminar-personal').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            personalSelected = findByPersonal(
                btn.parentElement.parentElement.getAttribute('idadministrador')
            );

            if (personalSelected != undefined) {
                beanRequestPersonal.type_request = 'GET';
                beanRequestPersonal.operation = 'delete';
                $('#modalCargandoPersonal').modal('show');
            } else {
                showAlertTopEnd("warning", "No se encuentra el personal", "");
            }
        };
    });
}

function addViewArchivosPrevius() {

    $("#txtImagenPersonal").change(function () {
        filePreview(this, "#imagePreview");
    });

}

function filePreview(input, imagen) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        imagen;
        reader.onload = function (e) {
            $(imagen).html(
                "<img style='height:220px;width: 225px;'  alt='user-picture' class='img-responsive center-box img-circle' src='" +
                e.target.result +
                "' />"
            );
        };
        reader.readAsDataURL(input.files[0]);
    }
}


function findIndexPersonal(idbusqueda) {
    return beanPaginationPersonal.list.findIndex(
        (Personal) => {
            if (Personal.idadministrador == parseInt(idbusqueda))
                return Personal;


        }
    );
}

function findByPersonal(idadministrador) {
    return beanPaginationPersonal.list.find(
        (Personal) => {
            if (parseInt(idadministrador) == Personal.idadministrador) {
                return Personal;
            }


        }
    );
}

var validarDormularioVideo = () => {

    if (document.querySelector("#txtNombrePersonal").value == "") {
        showAlertTopEnd("info", "Vacío", "Ingrese Nombre");
        return false;
    }
    if (document.querySelector("#txtApellidoPersonal").value == "") {
        showAlertTopEnd("info", "Vacío", "Ingrese Apellido");
        return false;
    }
    let numero = numero_campo(
        document.querySelector('#txtTelefonoPersonal')

    );
    if (numero != undefined) {
        if (numero.value == '') {
            showAlertTopEnd('info', "Formato Incorrecto", 'Por favor ingrese ' + email.labels[0].innerText);
        } else {
            showAlertTopEnd(
                'info', "Formato Incorrecto",
                'Por favor ingrese sólo números, ' + numero.labels[0].innerText
            );
        }

        return false;
    }

    if (document.querySelector("#txtUsuarioName").value == "") {
        showAlertTopEnd("info", "Vacío", "Ingrese Usuario nombre");
        return false;
    }
    let email = email_campo(
        document.querySelector('#txtEmailUsuario')

    );

    if (email != undefined) {
        if (email.value == '') {
            showAlertTopEnd('info', "Formato Incorrecto", 'Por favor ingrese correo electrónico');
        } else {
            showAlertTopEnd(
                'info', "Formato Incorrecto",
                'Por favor ingrese un correo electrónico Válido'
            );
        }

        return false;
    }
    checkTexto = "";
    for (let index = 1; index < 7; index++) {

        if (document.querySelector("#txtPrivilegio" + index).checked) {
            checkTexto += "1";
        } else {
            checkTexto += "0";
        }

    }

    if (document.querySelector("#txtPasswordUsuario").value == "") {
        showAlertTopEnd("info", "Vacío", "Ingrese Contraseña");
        return false;
    }
    if (document.querySelector("#txtPasswordUsuario").value !== document.querySelector("#txtPasswordUsuario2").value) {
        showAlertTopEnd("info", "Formato Inocrrecto", "las constraseñas no son iguales");
        return false;
    }

    return true;
}