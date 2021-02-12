var beanPaginationAdministrador;
var administradorSelected;
var beanRequestAdministrador = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestAdministrador.entity_api = 'cliente';
    beanRequestAdministrador.operation = 'obtener';
    beanRequestAdministrador.type_request = 'GET';
    $("#modalCargandoAdministrador").on('shown.bs.modal', function () {
        processAjaxAdministrador();
    });
    $('#modalCargandoAdministrador').modal('show');
    $("#formularioPerfil").submit(function (event) {
        beanRequestAdministrador.operation = 'update';
        beanRequestAdministrador.type_request = 'POST';

        if (vaidarFormularioPerfil()) {
            $('#modalCargandoAdministrador').modal('show');
        }
        event.preventDefault();
        event.stopPropagation();
    });
    document.querySelector("#ButtonPassword1").onclick = (e) => {

        if (e.target.className.includes("zmdi-eye-off")) {
            document.querySelector("#txtPassword1").setAttribute("type", "password");
            e.target.classList.remove("zmdi-eye-off");
            e.target.classList.add("zmdi-eye");
        } else {
            document.querySelector("#txtPassword1").setAttribute("type", "text");
            e.target.classList.remove("zmdi-eye");
            e.target.classList.add("zmdi-eye-off");
        }

    }

    document.querySelector("#ButtonPassword2").onclick = (e) => {

        if (e.target.className.includes("zmdi-eye-off")) {
            document.querySelector("#txtPassword2").setAttribute("type", "password");
            e.target.classList.remove("zmdi-eye-off");
            e.target.classList.add("zmdi-eye");
        } else {
            document.querySelector("#txtPassword2").setAttribute("type", "text");
            e.target.classList.remove("zmdi-eye");
            e.target.classList.add("zmdi-eye-off");
        }

    }
});

function processAjaxAdministrador() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestAdministrador.operation == 'update' ||
        beanRequestAdministrador.operation == 'add'
    ) {

        json = {
            nombre: document.querySelector("#txtNombre").value,
            apellido: document.querySelector("#txtApellido").value,
            ocupacion: document.querySelector("#txtEspecialidad").value,
            telefono: parseInt(document.querySelector("#txtTelefono").value),
            pais: "",
            cuenta: {
                email: document.querySelector("#txtEmail").value,
                usuario: document.querySelector("#txtUsuario").value,
                clave: document.querySelector("#txtPassword1").value
            }
        };


    } else {
        form_data = null;
    }

    switch (beanRequestAdministrador.operation) {
        case 'update':
            let dataImagen;
            if (document.querySelector("#txtFoto").files.length != 0) {
                dataImagen = $("#txtFoto").prop("files")[0];
                form_data.append("txtFoto", dataImagen);
            }
            form_data.append("class", JSON.stringify(json));
            break;
        default:

            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestAdministrador.entity_api + "/" + beanRequestAdministrador.operation +
            parameters_pagination,
        type: beanRequestAdministrador.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestAdministrador.operation == 'update') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json', xhr: function () {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    $('.progress-bar-perfil-aula').css({
                        width: Math.round(percentComplete * 100) + '%'
                    });
                    $(".progress-bar-perfil-aula").text(Math.round(percentComplete * 100) + '%');
                    $(".progress-bar-perfil-aula").attr("aria-valuenow", +Math.round(percentComplete * 100));
                    if (percentComplete === 1) {
                        // $('.progress-bar-perfil-aula').addClass('hide');
                        $('.progress-bar-perfil-aula').css({
                            width: + '100%'
                        });
                        $(".progress-bar-perfil-aula").text("Cargando ... 100%");
                        $(".progress-bar-perfil-aula").attr("aria-valuenow", "100");
                    }
                }
            }, false);
            xhr.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    $('.progress-bar-perfil-aula').css({
                        width: Math.round(percentComplete * 100) + '%'
                    });
                    $(".progress-bar-perfil-aula").text(Math.round(percentComplete * 100) + '%');
                    $(".progress-bar-perfil-aula").attr("aria-valuenow", +Math.round(percentComplete * 100));
                }
            }, false);
            return xhr;
        },
    }).done(function (beanCrudResponse) {

        $('#modalCargandoAdministrador').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                $('#ventanaModalManAdministrador').modal('hide');

            } else {
                showAlertTopEnd("info", "VERIFICACIÓN!", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {
            if (beanCrudResponse.beanPagination.list !== undefined) {
                beanPaginationAdministrador = beanCrudResponse.beanPagination;
                addAdministrador(beanPaginationAdministrador.list[0]);
            } else {
                user_session.foto = beanCrudResponse.beanPagination.foto;
                user_session.email = document.querySelector('#txtEmail').value;
                user_session.usuario = document.querySelector('#txtUsuario').value;

                Cookies.set('clpe_user', user_session);
                if (beanCrudResponse.beanPagination.foto != "") {
                    setUrlFotoUserSession(getHostFrontEnd() + "adjuntos/clientes/" + user_session.foto);
                }
            }

        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoAdministrador').modal("hide");
        showAlertErrorRequest();

    });

}

function addAdministrador(administrador = undefined) {
    //LIMPIAR LOS CAMPOS

    document.querySelector('#txtNombre').value = (administrador == undefined) ? '' : administrador.nombre;
    document.querySelector('#txtApellido').value = (administrador == undefined) ? '' : administrador.apellido;

    document.querySelector('#txtTelefono').value = (administrador == undefined) ? '' : administrador.telefono;
    document.querySelector('#txtEspecialidad').value = (administrador == undefined) ? '' : administrador.ocupacion;
    document.querySelector('#txtUsuario').value = (administrador == undefined) ? '' : administrador.cuenta.usuario;
    document.querySelector('#txtEmail').value = (administrador == undefined) ? '' : administrador.cuenta.email;
    document.querySelector('#txtPassword1').value = (administrador == undefined) ? '' : administrador.cuenta.clave;
    document.querySelector('#txtPassword2').value = (administrador == undefined) ? '' : administrador.cuenta.clave;


    if (administrador !== undefined) {

        $("#imagePreview").html(
            `<img style="height:262px;width: 278px;" alt='user-picture' class='img-responsive rounded-circle' src='${getHostFrontEnd()}${(administrador.cuenta.foto == "" || administrador.cuenta.foto == null) ? "vistas/assets/img/userclpe.png" : "adjuntos/clientes/" + administrador.cuenta.foto}' />`
        );

    } else {
        $("#imagePreview").html(
            `<img style="height:262px;width: 278px;" alt='user-picture' class='img-responsive rounded-circle' src='${getHostFrontEnd()}vistas/assets/img/userclpe.png' />`
        );

    }
    addViewArchivosPrevius();

}

function addViewArchivosPrevius() {

    $("#txtFoto").change(function () {
        filePreview(this, "#imagePreview");
    });

}

function filePreview(input, imagen) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        imagen;
        reader.onload = function (e) {
            $(imagen).html(
                "<img style='height: 262px; width: 278px;' alt='user-picture' class='img-responsive center-box rounded-circle' src='" +
                e.target.result +
                "' />"
            );
        };
        reader.readAsDataURL(input.files[0]);
    }
}



function findIndexAdministrador(idbusqueda) {
    return beanPaginationAdministrador.list.findIndex(
        (Administrador) => {
            if (Administrador.idadministrador == parseInt(idbusqueda))
                return Administrador;


        }
    );
}

function findByAdministrador(idadministrador) {
    return beanPaginationAdministrador.list.find(
        (Administrador) => {
            if (parseInt(idadministrador) == Administrador.idadministrador) {
                return Administrador;
            }


        }
    );
}

var vaidarFormularioPerfil = () => {

    let letra = letra_campo(
        document.querySelector('#txtNombre'),
        document.querySelector("#txtApellido"),
        document.querySelector("#txtEspecialidad"),
        document.querySelector("#txtUsuario")

    );
    if (letra != undefined) {
        if (letra.value == '') {
            showAlertTopEnd('info', "Formato Incorrecto", 'Por favor ingrese ' + letra.labels[0].innerText);
        } else {
            showAlertTopEnd(
                'info', "Formato Incorrecto",
                'Por favor ingrese sólo letras, ' + letra.labels[0].innerText
            );
        }

        return false;
    }
    let letra_numero = letra_numero_campo(
        document.querySelector("#txtUsuario")

    );
    if (letra_numero != undefined) {
        if (letra_numero.value == '') {
            showAlertTopEnd('info', "Formato Incorrecto", 'Por favor ingrese ' + letra_numero.labels[0].innerText);
        } else {
            showAlertTopEnd(
                'info', "Formato Incorrecto",
                'Por favor ingrese sólo letras y números, ' + letra_numero.labels[0].innerText
            );
        }

        return false;
    }
    let numero = numero_campo(
        document.querySelector('#txtTelefono')

    );
    if (numero != undefined) {
        if (numero.value == '') {
            showAlertTopEnd('info', "Formato Incorrecto", 'Por favor ingrese ' + numero.labels[0].innerText);
        } else {
            showAlertTopEnd(
                'info', "Formato Incorrecto",
                'Por favor ingrese sólo números, ' + numero.labels[0].innerText
            );
        }

        return false;
    }

    let email = email_campo(
        document.querySelector('#txtEmail')

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
    let pass = password_campo(
        document.querySelector('#txtPassword1'),
        document.querySelector('#txtPassword2')

    );
    if (pass != undefined) {
        if (pass.value == '') {
            showAlertTopEnd('info', "Formato Incorrecto", 'Por favor ingrese ' + pass.labels[0].innerText);
        } else {
            showAlertTopEnd(
                'info', "Formato Incorrecto",
                'Por favor ingrese formato válido, números, letras y los carácteres "! ?"; tamaño mínimo:8, tamaño máximo:12, ' + pass.labels[0].innerText
            );
        }

        return false;
    }

    if (document.querySelector("#txtPassword1").value !== document.querySelector("#txtPassword2").value) {
        showAlertTopEnd("info", "Formato Incorrecto", "las constraseñas no coinciden");
        return false;
    }
    if (document.querySelector("#txtFoto").files.length != 0) {
        if (document.querySelector("#txtFoto").files.length == 0) {
            swal({
                title: "Vacío",
                text: "Ingrese Imagen",
                type: "warning",
                timer: 1200,
                showConfirmButton: false
            });
            return false;
        }
        if (!(document.querySelector("#txtFoto").files[0].type == "image/png" || document.querySelector("#txtFoto").files[0].type == "image/jpg" || document.querySelector("#txtFoto").files[0].type == "image/jpeg")) {
            swal({
                title: "Formato Incorrecto",
                text: "Ingrese formato png, jpeg y jpg",
                type: "warning",
                timer: 1200,
                showConfirmButton: false
            });
            return false;
        }
        //menor a   5 MB
        if (document.querySelector("#txtFoto").files[0].size > (5 * 1024 * 1024)) {
            swal({
                title: "Tamaño excedido",
                text: "el tamaño del archivo tiene que ser menor a 5 MB",
                type: "warning",
                timer: 1200,
                showConfirmButton: false
            });
            return false;
        }
    }
    return true;
}