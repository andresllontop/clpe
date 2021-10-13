var state;
var paisSelected;
document.addEventListener('DOMContentLoaded', function () {

    state = new Array({ codigo: "355", icon: "flag-icon-al", nombre: "Albania" }, { codigo: "376", icon: "flag-icon-ad", nombre: "Andorra" }, { codigo: "244", icon: "flag-icon-ao", nombre: "Angola" }, { codigo: "54", icon: "flag-icon-ar", nombre: "Argentina" }, { codigo: "374", icon: "flag-icon-am", nombre: "Armenia" }, { codigo: "61", icon: "flag-icon-au", nombre: "Australia" }, { codigo: "43", icon: "flag-icon-at", nombre: "Austria" }, { codigo: "973", icon: "flag-icon-bh", nombre: "Bahrain" }, { codigo: "880", icon: "flag-icon-bd", nombre: "Bangladesh" }, { codigo: "591", icon: "flag-icon-bo", nombre: "Bolivia" }, { codigo: "55", icon: "flag-icon-br", nombre: "Brazil" }, { codigo: "359", icon: "flag-icon-bg", nombre: "Bulgaria" }, { codigo: "1", icon: "flag-icon-ca", nombre: "Canada" }, { codigo: "236", icon: "flag-icon-cf", nombre: "Central African Republic" }, { codigo: "56", icon: "flag-icon-cl", nombre: "Chile" }, { codigo: "57", icon: "flag-icon-co", nombre: "Colombia" }, { codigo: "506", icon: "flag-icon-cr", nombre: "Costa Rica" }, { codigo: "53", icon: "flag-icon-cu", nombre: "Cuba" }, { codigo: "45", icon: "flag-icon-dk", nombre: "Dinamarca" }, { codigo: "1809", icon: "flag-icon-do", nombre: "Dominican Republic" }, { codigo: "593", icon: "flag-icon-ec", nombre: "Ecuador" }, { codigo: "503", icon: "flag-icon-sv", nombre: "El Salvador" }, { codigo: "001", icon: "flag-icon-us", nombre: "Estados Unidos" }, { codigo: "33", icon: "flag-icon-fr", nombre: "France" }, { codigo: "502", icon: "flag-icon-gt", nombre: "Guatemala" }, { codigo: "504", icon: "flag-icon-hn", nombre: "Honduras" }, { codigo: "39", icon: "flag-icon-it", nombre: "Italy" }, { codigo: "1876", icon: "flag-icon-jm", nombre: "Jamaica" }, { codigo: "81", icon: "flag-icon-jp", nombre: "Japan" }, { codigo: "52", icon: "flag-icon-mx", nombre: "México" }, { codigo: "505", icon: "flag-icon-ni", nombre: "Nicaragua" }, { codigo: "507", icon: "flag-icon-pa", nombre: "Panama" }, { codigo: "595", icon: "flag-icon-py", nombre: "Paraguay" }, { codigo: "51", icon: "flag-icon-pe", nombre: "Perú" }, { codigo: "351", icon: "flag-icon-pt", nombre: "Portugal" }, { codigo: "44", icon: "flag-icon-gb", nombre: "Reino Unido" }, { codigo: "7", icon: "flag-icon-ru", nombre: "Rusia" }, { codigo: "90", icon: "flag-icon-tr", nombre: "Turquía" }, { codigo: "598", icon: "flag-icon-uy", nombre: "Uruguay" }, { codigo: "58", icon: "flag-icon-ve", nombre: "Venezuela" }, { codigo: "260", icon: "flag-icon-zm", nombre: "Zambia" });
    document.querySelector('#btn-Register').onclick = () => {
        let paises = "<option value=''>[Pais]</option>", codigos = '';
        state.forEach(country => {
            paises += `<option value="${country.nombre}">${country.nombre}</option>`;
            codigos += `<li class="aula-cursor-mano"><i class="flag-icon ${country.icon} mr-2"></i>${country.nombre} (+${country.codigo})</li>`;
        });
        document.querySelector("#txtcountryRegister").innerHTML = paises;
        document.querySelector("#txtTelefonoCodigoRegister").innerHTML = codigos;

        $('#ventanaModalRegister').modal('show');
        $("#txtTelefonoCodigoRegister > li").click(function (btn) {
            document.querySelector(".paises").innerHTML = btn.currentTarget.innerHTML;
        });




    };
    $(".submit").css({ "background": "#b21aff", "border-color": "#b21aff", "color": "white" });

    $('#FrmRegister').submit(function (event) {

        try {
            if (validarFormularioRegister()) {

                $(".feedback-register").show().animate({ "opacity": "1", "bottom": "33px" }, 400);
                $(".feedback-register").css({ "padding": "0", });
                document.querySelector(".feedback-register").innerHTML = `<div class="progress progress-striped active mb-0" style="height: 25px;border-radius: 0;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-purple" role="progressbar"
                    aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%;line-height: 26px;">
                    Registrando...
                </div></div>`;
                processAjaxRegister();
            }
        } catch (e) {
            console.log(e);
            showAlertTopEnd(
                'error', "Error al registrar datos",
                ''
            );
        }
        event.preventDefault();
        event.stopPropagation();
    });

    $('#modalCargandoRegister').on('shown.bs.modal', function () {
        processAjaxRegister();
    });


    $(".input-register").focusin(function () {
        $(this).find("span").animate({ "opacity": "0" }, 200);
    });

    $(".input-register").focusout(function () {
        $(this).find("span").animate({ "opacity": "1" }, 300);
    });

    document.querySelector("#txtcountryRegister").onchange = function () {
        paisSelected = findByPaises(document.querySelector("#txtcountryRegister").value);
        document.querySelector(".paises").innerHTML = `<i class="flag-icon ${paisSelected.icon} mr-2"></i>${paisSelected.nombre} (+${paisSelected.codigo})`;
    };

    document.getElementsByName("radioTipoComunicacionRegister").forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onchange = function () {

            if (btn.checked == true && parseInt(btn.value) == parseInt(4)) {
                removeClass(document.querySelector("#txtCodigoVendedorCliente").parentElement, "d-none");

            } else if (btn.checked == true) {
                addClass(document.querySelector("#txtCodigoVendedorCliente").parentElement, "d-none");
            }



        }
    });

});

function processAjaxRegister() {

    let datosSerializados = $('#FrmRegister').serialize();
    $.ajax({
        url: getHostAPI() + 'authentication/register',
        type: 'POST',
        data: datosSerializados,
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
        dataType: 'json'

    })
        .done(function (jsonResponse) {

            if (jsonResponse.messageServer != undefined) {

                if (jsonResponse.messageServer.toLowerCase() === 'ok') {

                    //SET COOKIE TOKEN
                    $(".feedback-register").css({ "background": "#2ecc71", "border-color": "#2ecc71" });
                    $(".login").find(".submit i").removeAttr('class').addClass("fa fa-check").css({ "color": "#fff" });
                    $("input").css({ "border-color": "#b21aff" });
                    $(".submit").css({ "background": "#b21aff", "border-color": "#b21aff", "color": "white" });
                    $("input").css({ "border-color": "#b21aff" });
                    $(".feedback-register").show().animate({ "opacity": "1", "bottom": "31px" }, 400);
                    $(".feedback-register").css({ "padding": "4px", });
                    document.querySelector(".feedback-register").innerHTML = `Registrado Exitosamente! <br>En un máximo de 24 horas podrás acceder al curso.`;
                    document.querySelector('#txtombreRegister').value = '';
                    document.querySelector('#txtapellidoRegister').value = '';
                    document.querySelector('#txtTelefonoRegister').value = '';
                    document.querySelector('#txtpassRegister').value = '';
                    document.querySelector('#txtemailRegister').value = '';
                    document.querySelector('#txtoficioRegister').value = '';
                    document.querySelector('#txtombreRegister').focus();
                    setTimeout(function () { $(".feedback-register").show().animate({ "opacity": "0", "bottom": "-30px" }, 800); }, 8000);

                } else {

                    $(".feedback-register").css({ "background": "rgb(255 28 28)", "border-color": "rgb(255 28 28)" });
                    $("input").css({ "border-color": "rgb(255 28 28)" });
                    $(".feedback-register").show().animate({ "opacity": "1", "bottom": "31px" }, 400);
                    $(".feedback-register").css({ "padding": "4px", });
                    document.querySelector(".feedback-register").innerHTML = jsonResponse.messageServer;
                    setTimeout(function () { $(".feedback-register").show().animate({ "opacity": "0", "bottom": "-30px" }, 800); }, 3000);

                }

            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            $('#modalCargandoRegister').modal('hide');
            swal({
                title: "error",
                text: "error del servidor",
                type: "error",
                timer: 1000,
                showConfirmButton: false
            });
            console.log(errorThrown);
        });
}

var validarFormularioRegister = () => {
    if (paisSelected == undefined) {
        showAlertTopEnd("info", "Vacío", "Seleccione el País");
        return false;
    }
    let radioButTrat = document.getElementsByName("radioTipoComunicacionRegister");
    let valorRadio = 0;
    for (var i = 0; i < radioButTrat.length; i++) {

        if (radioButTrat[i].checked == true) {
            valorRadio = radioButTrat[i].value;
        }

    }
    if (valorRadio < 1 && valorRadio > 5) {
        showAlertTopEnd("info", "Vacío", "Selecciona un medio de comunicación correcto en la pregunta");
        return false;
    }
    if (valorRadio == 4 && document.querySelector("#txtCodigoVendedorCliente").value == "") {
        showAlertTopEnd("info", "Vacío", "Ingrese Código Vendedor");
        return false;
    } else {
        document.querySelector("#txtCodigoVendedorCliente").value == null;
    }


    let letra = letra_campo(
        document.querySelector('#txtombreRegister'),
        document.querySelector('#txtapellidoRegister'),
        document.querySelector('#txtoficioRegister')
    );

    if (letra != undefined) {
        if (letra.value == '') {
            showAlertTopEnd('info', "Formato Incorrecto", 'Por favor ingrese datos al campo' + letra.labels[0].innerText);
        } else {
            showAlertTopEnd(
                'info', "Formato Incorrecto",
                'Por favor ingrese sólo letras, al campo ' + letra.labels[0].innerText
            );
        }

        return false;
    }
    let numero = numero_campo(
        document.querySelector('#txtTelefonoRegister')
    );

    if (numero != undefined) {
        if (numero.value == '') {
            showAlertTopEnd('info', "Formato Incorrecto", 'Por favor ingrese datos al campo teléfono' + numero.labels[0].innerText);
        } else {
            showAlertTopEnd(
                'info', "Formato Incorrecto",
                'Por favor ingrese sólo números al campo teléfono');
        }

        return false;
    }
    document.querySelector('#txtTelefonoRegister').value = paisSelected.codigo + "" + document.querySelector('#txtTelefonoRegister').value;
    let email = email_campo(
        document.querySelector('#txtemailRegister')
    );

    if (email != undefined) {
        if (email.value == '') {
            swal({
                title: "vacío!",
                text: "Por favor ingrese Correo electrónico",
                type: "warning",
                timer: 3000,
                showConfirmButton: false
            });
        } else {
            swal({
                title: "Formato Incorrecto!",
                text: "Por favor ingrese Correo electrónico válida",
                type: "warning",
                timer: 3000,
                showConfirmButton: false
            });
        }

        return false;
    }

    if (limpiar_campo(document.querySelector("#txtpassRegister").value) == "") {
        swal({
            title: "Formato Incorrecto",
            text: "Ingrese Contraseña válida",
            type: "warning",
            timer: 3000,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtcountryRegister").value == "") {
        swal({
            title: "Formato Incorrecto",
            text: "Selecciona País",
            type: "warning",
            timer: 3000,
            showConfirmButton: false
        });
        return false;
    }

    return true;
}

var findByPaises = (nombre) => {

    return state.find(
        (detalle) => {
            if (nombre == detalle.nombre) {
                return detalle;
            }


        }
    );
};

