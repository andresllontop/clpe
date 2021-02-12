var beanPaginationVerificaty;
var verificatySelected;
var token_id = null;
var beanRequestVerificaty = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    let GETsearch = window.location.search;

    token_id = getParameterByName("id", GETsearch);
    if (token_id == null) {
        window.location.href = getHostFrontEnd();
    }
    beanRequestVerificaty.entity_api = 'authentication/passverificaty';
    beanRequestVerificaty.operation = 'token';
    beanRequestVerificaty.type_request = 'POST';

    $("#formularioVerificaty").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (validarFormularioVerificaty()) {
            //posicion del estilo login.css=14
            document.styleSheets[14].addRule('.login > .feedback:before', 'border-bottom-color: #7030a0;');
            $(".feedback-verificaty").show().animate({ "opacity": "1", "bottom": "-10px" }, 400);
            $(".feedback-verificaty").css({ "padding": "0", });
            document.querySelector("#submitVerificaty").disabled = true;
            document.querySelector(".feedback-verificaty").innerHTML = `<div class="progress progress-striped active mb-0" style="height: 25px;border-radius: 0;">
             <div class="progress-bar progress-bar-striped progress-bar-animated bg-purple" role="progressbar"
                 aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%;line-height: 26px;">
                 Verificando Credenciales...
             </div></div>`;
            processAjaxVerificaty();
        }
    });

});

function getParameterByName(name, url = window.location.href) {
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}
function processAjaxVerificaty() {
    let form_data = new FormData();
    let parameters_pagination = '';
    switch (beanRequestVerificaty.operation) {
        case 'token':
            let json = '';
            json = {
                pass: document.querySelector("#contactPassVerificaty").value

            };
            form_data.append("class", JSON.stringify(json));
            break;
        default:

            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestVerificaty.entity_api + "/" + beanRequestVerificaty.operation +
            parameters_pagination,
        type: beanRequestVerificaty.type_request,
        headers: {
            'Authorization': 'Bearer ' + token_id
        },
        data: form_data,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'json',
    }).done(function (jsonResponse) {
        if (jsonResponse.messageServer != undefined) {

            if (jsonResponse.messageServer.toLowerCase() === 'ok') {

                document.styleSheets[14].addRule('.login > .feedback:before', 'border-bottom-color:#2ecc71;');
                $(".feedback-verificaty").css({ "background": "#2ecc71", "border-color": "#2ecc71" });
                $("#formularioRecovery").find(".submit i").removeAttr('class').addClass("fa fa-check").css({ "color": "#fff" });
                $(".feedback-verificaty").show().animate({ "opacity": "1", "bottom": "-44px" }, 400);
                $(".feedback-verificaty").css({ "padding": "4px", });
                document.querySelector(".feedback-verificaty").innerHTML = `Registrado Exitosamente! <br>ya puedes iniciar sesión con la contraseña actualizada.<br> redireccionando...`;
                document.querySelector('#contactPassVerificaty').value = '';
                document.querySelector('#contactPassVerificaty2').value = '';
                document.querySelector('#contactPassVerificaty').focus();
                setTimeout(function () { $(".feedback-verificaty").show().animate({ "opacity": "0", "bottom": "0px" }, 800); }, 8000);
                // window.location.href = getHostFrontEnd();

            } else {
                document.styleSheets[14].addRule('.login > .feedback:before', 'border-bottom-color:rgb(255 28 28);');
                $(".feedback-verificaty").css({ "background": "rgb(255 28 28)", "border-color": "rgb(255 28 28)" });
                $(".feedback-verificaty").show().animate({ "opacity": "1", "bottom": "-15px" }, 400);
                $(".feedback-verificaty").css({ "padding": "4px", });
                document.querySelector(".feedback-verificaty").innerHTML = jsonResponse.messageServer;
                setTimeout(function () { $(".feedback-verificaty").show().animate({ "opacity": "0", "bottom": "0px" }, 800); }, 3000);
                document.querySelector("#submitVerificaty").disabled = false;
            }

        }

    }).fail(function (jqXHR, textStatus, errorThrown) {
        document.styleSheets[14].addRule('.login > .feedback:before', 'border-bottom-color:rgb(255 28 28);');
        $(".feedback-verificaty").css({ "background": "rgb(255 28 28)", "border-color": "rgb(255 28 28)" });
        $(".feedback-verificaty").show().animate({ "opacity": "1", "bottom": "-15px" }, 400);
        $(".feedback-verificaty").css({ "padding": "4px", });
        document.querySelector(".feedback-verificaty").innerHTML = 'Error en el servidor';
        setTimeout(function () { $(".feedback-verificaty").show().animate({ "opacity": "0", "bottom": "0px" }, 800); }, 3000);
        document.querySelector("#submitVerificaty").disabled = false;
        console.log(errorThrown);

    });

}

var validarFormularioVerificaty = () => {
    let numero = password_campo(
        document.querySelector('#contactPassVerificaty'),
        document.querySelector('#contactPassVerificaty2')

    );
    if (numero != undefined) {
        if (numero.value == '') {
            swal("Campo Vacío!", 'Por favor ingrese ' + numero.labels[0].innerText, 'info');
        } else {
            swal(
                "Formato Incorrecto",
                'Por favor ingrese sólo números y letras en mayúsculas o minúsculas, al campo ' + numero.labels[0].innerText, 'info'
            );
        }

        return false;
    }
    if (document.querySelector('#contactPassVerificaty').value != document.querySelector('#contactPassVerificaty2').value) {
        swal(
            "Las Contraseñas no son iguales", "", 'info'
        );
    }


    return true;
}