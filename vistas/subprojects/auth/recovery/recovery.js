var beanPaginationRecovery;
var recoverySelected;
document.addEventListener('DOMContentLoaded', function () {

    $("#formularioRecovery").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (validarFormularioRecovery()) {
            //posicion del estilo login.css=14
            document.styleSheets[14].addRule('.login > .feedback:before', 'border-bottom-color: #7030a0;');
            $(".feedback-recovery").show().animate({ "opacity": "1", "bottom": "-10px" }, 400);
            $(".feedback-recovery").css({ "padding": "0", });
            document.querySelector("#submitRecovery").disabled = true;
            document.querySelector(".feedback-recovery").innerHTML = `<div class="progress progress-striped active mb-0" style="height: 25px;border-radius: 0;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-purple" role="progressbar"
                aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%;line-height: 26px;">
                Verificando Credenciales...
            </div></div>`;
            processAjaxRecovery();
        }
    });

});

function processAjaxRecovery() {
    let datosSerializados = $('#formularioRecovery').serialize();
    $.ajax({
        url: getHostAPI() + 'authentication/recovery',
        type: 'POST',
        data: datosSerializados,
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
        dataType: 'json'
    }).done(function (jsonResponse) {
        if (jsonResponse.messageServer != undefined) {

            if (jsonResponse.messageServer.toLowerCase() === 'ok') {

                document.styleSheets[14].addRule('.login > .feedback:before', 'border-bottom-color:#2ecc71;');
                $(".feedback-recovery").css({ "background": "#2ecc71", "border-color": "#2ecc71" });
                $("#formularioRecovery").find(".submit i").removeAttr('class').addClass("fa fa-check").css({ "color": "#fff" });
                $(".feedback-recovery").show().animate({ "opacity": "1", "bottom": "-44px" }, 400);
                $(".feedback-recovery").css({ "padding": "4px", });
                document.querySelector(".feedback-recovery").innerHTML = `se envió un enlace a tu correo electrónico para que puedas restaurar tu contraseña.`;
                document.querySelector('#contactAddressRecovery').value = '';
                document.querySelector('#contactAddressRecovery').focus();
                setTimeout(function () { $(".feedback-recovery").show().animate({ "opacity": "0", "bottom": "0px" }, 800); }, 8000);
                document.querySelector("#submitRecovery").disabled = false;

            } else {
                document.styleSheets[14].addRule('.login > .feedback:before', 'border-bottom-color:rgb(255 28 28);');
                $(".feedback-recovery").css({ "background": "rgb(255 28 28)", "border-color": "rgb(255 28 28)" });
                $(".feedback-recovery").show().animate({ "opacity": "1", "bottom": "-15px" }, 400);
                $(".feedback-recovery").css({ "padding": "4px", });
                document.querySelector(".feedback-recovery").innerHTML = jsonResponse.messageServer;
                setTimeout(function () { $(".feedback-recovery").show().animate({ "opacity": "0", "bottom": "0px" }, 800); }, 3000);
                document.querySelector("#submitRecovery").disabled = false;
            }

        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        document.styleSheets[14].addRule('.login > .feedback:before', 'border-bottom-color:rgb(255 28 28);');
        $(".feedback-recovery").css({ "background": "rgb(255 28 28)", "border-color": "rgb(255 28 28)" });
        $(".feedback-recovery").show().animate({ "opacity": "1", "bottom": "-15px" }, 400);
        $(".feedback-recovery").css({ "padding": "4px", });
        document.querySelector(".feedback-recovery").innerHTML = 'Error en el servidor';
        setTimeout(function () { $(".feedback-recovery").show().animate({ "opacity": "0", "bottom": "0px" }, 800); }, 3000);
        document.querySelector("#submitRecovery").disabled = false;
        console.log(errorThrown);

    });

}

var validarFormularioRecovery = () => {

    let email = email_campo(
        document.querySelector('#contactAddressRecovery')

    );

    if (email != undefined) {
        if (email.value == '') {
            showAlertTopEnd('info', "Campo Vacío!", 'Por favor ingrese correo electrónico');
        } else {
            showAlertTopEnd(
                'info', "Formato Incorrecto",
                'Por favor ingrese un correo electrónico Válido'
            );
        }

        return false;
    }



    return true;
}