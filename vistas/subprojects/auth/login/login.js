document.addEventListener('DOMContentLoaded', function () {

    document.querySelector('#btn-logear').onclick = () => {
        $('#ventanaModalLogear').modal('show');
    };
    $('#FrmLogin').submit(function (event) {

        try {
            if (validarFormularioLogin()) {

                if (document.styleSheets[15].href.includes("login")) {
                    document.styleSheets[15].addRule('.login > .feedback:before', 'border-bottom-color:#7030a0;');
                } else if (document.styleSheets[14].href.includes("login")) {
                    document.styleSheets[14].addRule('.login > .feedback:before', 'border-bottom-color:#7030a0;');
                }
                $(".feedback").show().animate({ "opacity": "1", "bottom": "-44px" }, 400);
                $(".feedback").css({ "padding": "0", });
                document.querySelector(".feedback").innerHTML = `<div class="progress progress-striped active mb-0" style="height: 25px;border-radius: 0;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-purple" role="progressbar"
                    aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%;line-height: 26px;">
                    Verificando Credenciales...
                </div></div>`;
                processAjaxAuth();
            }
        } catch (e) {
            console.log(e);
        }
        event.preventDefault();
        event.stopPropagation();
    });
    $('#modalCargandoLogin').on('shown.bs.modal', function () {
        processAjaxAuth();
    });

    $('#modalCargandoPublicoRegistrar').on('shown.bs.modal', function () {
        processAjaxRegistrarPublico();
    });

    document.querySelector("#span-ocultar-pass").onclick = () => {

        if (document.querySelector("#span-ocultar-pass").firstElementChild.className.includes("zmdi-eye-off")) {
            document.querySelector("#txtPass").setAttribute("type", "password");
            document.querySelector("#span-ocultar-pass").firstElementChild.classList.remove("zmdi-eye-off");
            document.querySelector("#span-ocultar-pass").firstElementChild.classList.add("zmdi-eye");
        } else {
            document.querySelector("#txtPass").setAttribute("type", "text");
            document.querySelector("#span-ocultar-pass").firstElementChild.classList.remove("zmdi-eye");
            document.querySelector("#span-ocultar-pass").firstElementChild.classList.add("zmdi-eye-off");
        }

    }

    /*
        $(".input").focusin(function () {
            $(this).find("span").animate({ "opacity": "0" }, 200);
        });
    
        $(".input").focusout(function () {
            $(this).find("span").animate({ "opacity": "1" }, 300);
        });
    
    */

});
function ChatRegistrarSubmit() {

    $('#FormularioPublicoRegistrar').submit(function (event) {

        event.preventDefault();
        event.stopPropagation();
        try {

            if (validarFormularioRegistroPublico()) {
                if (emailFOOTER == undefined) {
                    $('#modalCargandoPublicoRegistrar').modal('show');
                } else {
                    swal({
                        title: "HECHO!",
                        text: 'Ya te encuentras registrado',
                        timer: 2000,
                        showConfirmButton: false,
                        confirmButtonColor: "#2ca441",
                        imageUrl: getHostFrontEnd() + 'vistas/assets/img/enviada-carta.png'
                    });
                }

            }
        } catch (e) {
            console.log(e);
        }

    });

}
function processAjaxAuth() {

    let datosSerializados = $('#FrmLogin').serialize();
    // console.log(datosSerializados);
    $.ajax({
        url: getHostAPI() + 'authentication/login',
        type: 'POST',
        data: datosSerializados,
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
        dataType: 'json'

    })
        .done(function (jsonResponse) {
            let urlresponse;
            console.log(jsonResponse.messageServer);
            if (jsonResponse.messageServer != undefined) {
                urlresponse = jsonResponse.messageServer.split("/");
                jsonResponse.messageServer = urlresponse[0];
                if (urlresponse.length == 2) {
                    if (document.styleSheets[15].href.includes("login")) {
                        document.styleSheets[15].addRule('.login > .feedback:before', 'border-bottom-color:#2ecc71;');
                    } else if (document.styleSheets[14].href.includes("login")) {
                        document.styleSheets[14].addRule('.login > .feedback:before', 'border-bottom-color:#2ecc71;');
                    }

                    $(".login").find(".submit i").removeAttr('class').addClass("fa fa-check").css({ "color": "#fff" });
                    $("input").css({ "border-color": "#2ecc71" });
                    $(".submit").css({ "background": "#2ecc71", "border-color": "#2ecc71" });
                    $("input").css({ "border-color": "#2ecc71" });
                    $(".feedback").show().animate({ "opacity": "1", "bottom": "-80px" }, 400);
                    $(".feedback").css({ "padding": "4px", });
                    document.querySelector(".feedback").innerHTML = jsonResponse.messageServer + "<br/>  revise su correo...";
                    document.querySelector('#txtUsername').value = '';
                    document.querySelector('#txtPass').value = '';
                    document.querySelector('#txtUsername').focus();
                    setTimeout(function () { $(".feedback").show().animate({ "opacity": "0", "bottom": "0px" }, 800); }, 8000);

                } else {
                    if (jsonResponse.messageServer.toLowerCase() === 'ok') {
                        if (jsonResponse.beanPagination.token !== undefined) {
                            //SET COOKIE TOKEN
                            if (document.styleSheets[15].href.includes("login")) {
                                document.styleSheets[15].addRule('.login > .feedback:before', 'border-bottom-color:#2ecc71;');
                            } else if (document.styleSheets[14].href.includes("login")) {
                                document.styleSheets[14].addRule('.login > .feedback:before', 'border-bottom-color:#2ecc71;');
                            }
                            $(".login").find(".submit i").removeAttr('class').addClass("fa fa-check").css({ "color": "#fff" });
                            $("input").css({ "border-color": "#2ecc71" });
                            $(".submit").css({ "background": "#2ecc71", "border-color": "#2ecc71" });
                            $("input").css({ "border-color": "#2ecc71" });
                            $(".feedback").show().animate({ "opacity": "1", "bottom": "-80px" }, 400);
                            $(".feedback").css({ "padding": "4px", });
                            document.querySelector(".feedback").innerHTML = `Ingresó Exitosamente! <br />
                            redireccionando...`;
                            setCookieSession(jsonResponse.beanPagination.token, jsonResponse.beanPagination.usuario);
                            sendIndex();
                        } else {
                            if (document.styleSheets[15].href.includes("login")) {
                                document.styleSheets[15].addRule('.login > .feedback:before', 'border-bottom-color:rgb(255 28 28);');
                            } else if (document.styleSheets[14].href.includes("login")) {
                                document.styleSheets[14].addRule('.login > .feedback:before', 'border-bottom-color:rgb(255 28 28);');
                            }
                            $(".login").find(".submit i").removeAttr('class').addClass("fa fa-times").css({ "color": "#fff" });
                            $(".submit").css({ "background": "rgb(255 28 28)", "border-color": "rgb(255 28 28)" });
                            $("input").css({ "border-color": "rgb(255 28 28)" });
                            $(".feedback").show().animate({ "opacity": "1", "bottom": "-80px" }, 400);
                            $(".feedback").css({ "padding": "4px", });
                            document.querySelector(".feedback").innerHTML = `No tienes Acceso!<br /> Registrate`;

                            setTimeout(function () {
                                $(".feedback").show().animate({ "opacity": "0", "bottom": "0px" }, 800); (".login").find(".submit i").removeAttr('class').addClass("fa fa-long-arrow-right").css({ "color": "#fff" });
                                $(".submit").css({ "background": "rgb(178, 26, 255)", "border-color": "rgb(178, 26, 255)" });
                            }, 6000);

                        }
                    } else {
                        if (document.styleSheets[15].href.includes("login")) {
                            document.styleSheets[15].addRule('.login > .feedback:before', 'border-bottom-color:rgb(255 28 28);');
                        } else if (document.styleSheets[14].href.includes("login")) {
                            document.styleSheets[14].addRule('.login > .feedback:before', 'border-bottom-color:rgb(255 28 28);');
                        }
                        $(".login").find(".submit i").removeAttr('class').addClass("fa fa-times").css({ "color": "#fff" });
                        $(".submit").css({ "background": "rgb(255 28 28)", "border-color": "rgb(255 28 28)" });
                        $(".feedback").css({ "background": "rgb(255 28 28)", "border-color": "rgb(255 28 28)" });
                        $("input").css({ "border-color": "rgb(255 28 28)" });
                        $(".feedback").show().animate({ "opacity": "1", "bottom": "-80px" }, 400);
                        $(".feedback").css({ "padding": "4px", });
                        document.querySelector(".feedback").innerHTML = jsonResponse.messageServer + "<br /> Registrate";
                        document.querySelector('#txtUsername').value = '';
                        document.querySelector('#txtPass').value = '';
                        document.querySelector('#txtUsername').focus();


                        setTimeout(function () {
                            $(".feedback").show().animate({ "opacity": "0", "bottom": "30px" }, 800); $(".login").find(".submit i").removeAttr('class').addClass("fa fa-long-arrow-right").css({ "color": "#fff" });
                            $(".submit").css({ "background": "rgb(178, 26, 255)", "border-color": "rgb(178, 26, 255)" });
                        }, 6000);

                    }
                }

            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            if (document.styleSheets[15].href.includes("login")) {
                document.styleSheets[15].addRule('.login > .feedback:before', 'border-bottom-color:rgb(255 28 28);');
            } else if (document.styleSheets[14].href.includes("login")) {
                document.styleSheets[14].addRule('.login > .feedback:before', 'border-bottom-color:rgb(255 28 28);');
            }
            $(".login").find(".submit i").removeAttr('class').addClass("fa fa-times").css({ "color": "#fff" });
            $(".submit").css({ "background": "rgb(255 28 28)", "border-color": "rgb(255 28 28)" });
            $(".feedback").css({ "background": "rgb(255 28 28)", "border-color": "rgb(255 28 28)" });
            $("input").css({ "border-color": "rgb(255 28 28)" });
            $(".feedback").show().animate({ "opacity": "1", "bottom": "-80px" }, 400);
            $(".feedback").css({ "padding": "4px", });
            document.querySelector(".feedback").innerHTML = "Error en el servidor";
            document.querySelector('#txtUsername').value = '';
            document.querySelector('#txtPass').value = '';
            document.querySelector('#txtUsername').focus();


            setTimeout(function () {
                $(".feedback").show().animate({ "opacity": "0", "bottom": "30px" }, 800); $(".login").find(".submit i").removeAttr('class').addClass("fa fa-long-arrow-right").css({ "color": "#fff" });
                $(".submit").css({ "background": "rgb(178, 26, 255)", "border-color": "rgb(178, 26, 255)" });
            }, 6000);
        });
}

function processAjaxRegistrarPublico() {
    let json = "";
    json = {
        nombre: document.querySelector("#txtnombrePublico").value,
        email: document.querySelector("#txtemailPublico").value,
        fecha: getFullDateJava()

    };
    console.log(json);
    $.ajax({
        url: getHostAPI() + 'publico/add',
        type: 'POST',
        data: JSON.stringify(json),
        contentType: 'application/json; charset=utf-8',
        dataType: 'json'

    })
        .done(function (jsonResponse) {
            $('#modalCargandoPublicoRegistrar').modal('hide');
            if (jsonResponse.messageServer != undefined) {
                if (jsonResponse.messageServer.toLowerCase() === 'ok') {

                    swal({
                        title: "HECHO!",
                        text: 'Se envio correctamente los datos',
                        timer: 2000,
                        showConfirmButton: false,
                        confirmButtonColor: "#2ca441",
                        imageUrl: getHostFrontEnd() + 'vistas/assets/img/enviada-carta.png'
                    });
                    emailFOOTER = document.querySelector("#txtemailPublico").value;
                } else {
                    swal({
                        title: "error",
                        text: jsonResponse.messageServer,
                        type: "error",
                        timer: 1000,
                        showConfirmButton: false
                    });
                    document.querySelector('#txtUsername').value = '';
                    document.querySelector('#txtPass').value = '';
                    document.querySelector('#txtUsername').focus();

                }
            }
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            $('#modalCargandoPublicoRegistrar').modal('hide');
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

var validarFormularioLogin = () => {

    let email = email_campo(
        document.querySelector('#txtUsername')
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

    if (limpiar_campo(document.querySelector("#txtPass").value) == "") {
        swal({
            title: "Formato Incorrecto",
            text: "Ingrese Contraseña válida",
            type: "warning",
            timer: 3000,
            showConfirmButton: false
        });
        return false;
    }

    return true;
}

var validarFormularioRegistroPublico = () => {

    let email = email_campo(
        document.querySelector('#txtemailPublico')

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

    if (limpiar_campo(document.querySelector("#txtnombrePublico").value) == "") {
        swal({
            title: "Formato Incorrecto",
            text: "Ingrese Nombre",
            type: "warning",
            timer: 3000,
            showConfirmButton: false
        });
        return false;
    }
    return true;
}

