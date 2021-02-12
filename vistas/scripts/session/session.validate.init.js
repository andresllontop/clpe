
class BeanURL {
    constructor() {
        this.url = "";
        //this.type_perfil = "";
    }
}

document.addEventListener("DOMContentLoaded", function () {
    let user_session = Cookies.getJSON('clpe_user');
    if (user_session != undefined) {
        var current_path = window.location.href;
        current_path = current_path.substring(getHostAPP().length - 1, current_path.length);
        switch (parseInt(user_session.tipo_usuario)) {
            case 1:
                if (current_path.includes('app')) {
                    //VALIDAMOS SI TIENE HABILITADO ESTA URL
                    let arrayTypeProfile = Array.from((user_session.perfil).toString());
                    if (arrayTypeProfile[0] == 0 && !current_path.includes('index') && !current_path.includes('lecciones') && !current_path.includes('mensajes') && !current_path.includes('personal')) {
                        if (!current_path.includes('error')) {
                            window.location.href = "error";
                        }

                    }

                    if (arrayTypeProfile[1] == 0 && current_path.includes('index')) {
                        window.location.href = "error";
                    } else
                        if (arrayTypeProfile[2] == 0 && current_path.includes('lecciones')) {
                            window.location.href = "error";
                        } else
                            if (arrayTypeProfile[3] == 0 && current_path.includes('mensajes')) {
                                window.location.href = "error";
                            } else
                                if (arrayTypeProfile[4] == 0 && current_path.includes('personal')) {
                                    window.location.href = "error";
                                }
                    console.log("Url correcta");
                } else {
                    sendIndex();
                }
                break;
            case 2:
                if (current_path.includes('aula')) {
                    //VALIDAMOS SI TIENE HABILITADO ESTA URL
                    console.log("Url correcta");
                } else {

                    sendIndex();
                }
                break;
            default:
                sendIndex();
                break;
        }
    } else {
        sendIndex();
    }

});

// NO UTILIZANDO
function loaderUrlClpe() {
    let url;

    url = new BeanURL();
    url.url = "app/index";
    list_url_ate.push(url);

    url = new BeanURL();
    url.url = "app/perfil";
    list_url_ate.push(url);

    url = new BeanURL();
    url.url = "ate/datos";
    list_url_ate.push(url);

    url = new BeanURL();
    url.url = "ate/evaluaciones";
    list_url_ate.push(url);

    url = new BeanURL();
    url.url = "ate/reservas";
    list_url_ate.push(url);

    url = new BeanURL();
    url.url = "ate/menu-semanal";
    list_url_ate.push(url);

    url = new BeanURL();
    url.url = "ate/noticias-eventos";
    list_url_ate.push(url);

    url = new BeanURL();
    url.url = "ate/constancias";
    list_url_ate.push(url);

}