
var tiempoAdmin = Math.round(new Date().getTime() / 1000), tiempoUser, valorFOOTER, emailFOOTER;
document.addEventListener('DOMContentLoaded', function () {
    hideChat(0);

    $('#prime').click(function () {
        toggleFab();
    });
    /*
        setTimeout(function () {
            document.querySelector("#chat_form").innerHTML += `
        <span class="chat_msg_item chat_msg_item_admin">
        <div class="chat_avatar">
            <img src="${getHostFrontEnd()}adjuntos/logoHeader.jpg" />
        </div>Envíe un mensaje al agente.
        <div>
            <form class="message_form">
                <input placeholder="Tu email" />
                <input style="display:none;" value="MENSAJE DEL CHAT DE CLPE" />
                <textarea rows="4" placeholder="Tu mensaje"></textarea>
                <button>Enviar</button>
            </form>
    
        </div>
    </span>
    `;
        }, 50000);
    */
    document.querySelector(".header_img > img").src = getHostFrontEnd() + "adjuntos/logoHeader.jpg";

    document.querySelector("#fab_send").onclick = () => {
        tiempoUser = (Math.round(new Date().getTime() / 1000)) - tiempoAdmin;
        document.querySelector("#chat_form").innerHTML += `<span class="chat_msg_item chat_msg_item_user">${document.querySelector("#chatSend").value}</span><span class="status">Hace ${tiempoUser > 60 ? Math.round(tiempoUser / 60) + 'min' : tiempoUser + 'seg'}</span >
    `;
        document.querySelector("#chatSend").value = "";
        if (!document.querySelector("#FormularioPublicoRegistrar")) {
            setTimeout(function () {
                document.querySelector("#chat_form").innerHTML += `<span class="chat_msg_item chat_msg_item_admin">
                <div class="chat_avatar">
                    <img src="${getHostFrontEnd()}adjuntos/logoHeader.jpg" />
                </div>El agente suele responder en unas pocas horas. No se pierda su respuesta.
                <div>
                    <br>
                    <form id="FormularioPublicoRegistrar" class="get-notified">
                        <label for="txtemailPublico">Regístrate para Recibir notificación por correo
                            electrónico</label>
                        <input id="txtnombrePublico" placeholder="Ingresa tu Nombre" />
                        <input id="txtemailPublico" placeholder="Ingresa tu Email" />
                        <input class="btn btn-success p-0 py-1" type="submit" value="Registrar">
                        
                    </form>
                </div>
            </span>
`;

            }, 1500);


        } else {
            setTimeout(function () {
                document.querySelector("#chat_form").innerHTML += `
            <span class="chat_msg_item chat_msg_item_admin">
            <div class="chat_avatar">
                <img src="${getHostFrontEnd()}adjuntos/logoHeader.jpg" />
            </div>Registrate para recibir notificaciones
        </span>
        `;
            }, 1500);
        }
        setTimeout(function () {
            ChatRegistrarSubmit();

        }, 1500);

    };

    $('#chat_fullscreen_loader').click(function (e) {
        $('.fullscreen').toggleClass('zmdi-window-maximize');
        $('.fullscreen').toggleClass('zmdi-window-restore');
        $('.chat').toggleClass('chat_fullscreen');
        $('.fab').toggleClass('is-hide');
        $('.header_img').toggleClass('change_img');
        $('.img_container').toggleClass('change_img');
        $('.chat_header').toggleClass('chat_header2');
        $('.fab_field').toggleClass('fab_field2');
        $('.chat_converse').toggleClass('chat_converse2');

    });

});
//Toggle chat and links
function toggleFab() {

    $('.prime').toggleClass('zmdi-comment-outline');
    $('.prime').toggleClass('zmdi-close');
    $('.prime').toggleClass('is-active');
    $('.prime').toggleClass('is-visible');
    $('#prime').toggleClass('is-float');
    $('.chat').toggleClass('is-visible');
    $('.fab').toggleClass('is-visible');
    if (document.querySelector("#prime").className.includes("is-visible")) {
        hideChat(3);
    } else {
        hideChat(0);
    }

    if (valorFOOTER == undefined) {
        setTimeout(function () {
            document.querySelector("#chat_form").innerHTML += `
            <span class="chat_msg_item chat_msg_item_admin">
            <div class="chat_avatar">
                <img src="${getHostFrontEnd()}adjuntos/logoHeader.jpg" />
            </div>Hola! Soy el Administrador del Club de Lectura para Emprendedores
        </span>
        `;
        }, 1000);
        valorFOOTER = 1;
    }


}

function hideChat(hide) {
    switch (hide) {
        case 0:
            $('.chat').addClass('d-none');
            $('#chat_converse').css('display', 'none');
            $('#chat_body').css('display', 'none');
            $('#chat_form').css('display', 'none');
            $('.chat_login').css('display', 'block');
            $('.chat_fullscreen_loader').css('display', 'none');
            $('#chat_fullscreen').css('display', 'none');
            break;
        default:
            $('.chat').removeClass('d-none');
            $('#chat_converse').css('display', 'none');
            $('#chat_body').css('display', 'none');
            $('#chat_form').css('display', 'block');
            $('.chat_login').css('display', 'none');
            $('.chat_fullscreen_loader').css('display', 'block');
            break;

    }
}