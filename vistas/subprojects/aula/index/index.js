var circleCargando = {
    container: $('.dt-module__content'),
    containerOcultar: $('.dt-module__content-inner'),
    loader: null,
    createLoader: function () {
        var svgHtml = `
        
        <div class="loader" style="width: 80px;
        height: 80px;">
          <div class="loader">
              <div class="loader">
                 <div class="loader">
    
                 </div>
              </div>
            
          </div>
        </div>
       `;
        this.loader = document.createElement('div');
        this.loader.className = 'dt-loader';
        this.loader.innerHTML = svgHtml;
        this.container.append(this.loader);
        this.toggleLoader('hide');
    },
    toggleLoader: function (display) {
        if (this.loader) {
            if (display) {
                if (display == 'show') {
                    this.containerOcultar.addClass('d-none');
                    $(this.loader).removeClass('d-none');
                } else {
                    this.containerOcultar.removeClass('d-none');
                    $(this.loader).addClass('d-none');
                }
            } else {
                this.containerOcultar.toggleClass('d-none');
                $(this.loader).toggleClass('d-none');
            }
        }
    }
};
var beanPaginationLibro;
var libroSelected;
var beanRequestLibro = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {

    beanRequestLibro.entity_api = 'libros';
    beanRequestLibro.operation = 'cuenta';
    beanRequestLibro.type_request = 'GET';

    // $('#modalCargandoLibro').modal('show');

    $("#modalCargandoLibro").on('shown.bs.modal', function () {
    });
    $("#ventanaModalManLibro").on('hide.bs.modal', function () {
        beanRequestLibro.type_request = 'GET';
        beanRequestLibro.operation = 'cuenta';
    });
    let fetOptions = {
        headers: {
            "Content-Type": 'application/json; charset=UTF-8',
            'Authorization': 'Bearer ' + Cookies.get("clpe_token") + (Cookies.get("clpe_libro") == undefined ? "" : " Clpe " + Cookies.get("clpe_libro"))
        },
        method: "GET",
    }
    circleCargando.containerOcultar = $(document.querySelector("#vistaCatalogo").firstElementChild);
    circleCargando.container = $(document.querySelector("#vistaCatalogo"));
    circleCargando.createLoader();
    circleCargando.toggleLoader("show");
    Promise.all([
        fetch(getHostAPI() + beanRequestLibro.entity_api + "/" + beanRequestLibro.operation +
            "?filtro=" + '&pagina=1&registros=1', fetOptions),
        fetch(getHostAPI() + "notificacion/obtener" +
            "?libro=" + Cookies.get("clpe_libro"), fetOptions)
    ])
        .then(responses => Promise.all(responses.map((res) => res.json())))
        .then(json => {
            circleCargando.toggleLoader("hide");
            let beanCrudResponse = json[0];

            if (beanCrudResponse.messageServer !== null) {
                if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                    showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                    document.querySelector("#pageLibro").value = 1;
                    document.querySelector("#sizePageLibro").value = 5;
                    $('#ventanaModalManLibro').modal('hide');
                } else {
                    showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
                }
            }
            if (beanCrudResponse.beanPagination !== null) {

                beanPaginationLibro = beanCrudResponse.beanPagination;
                listaLibro(beanPaginationLibro);
            }

            let beanCrudResponse2 = json[1];
            if (beanCrudResponse2.messageServer !== null) {
                if (beanCrudResponse2.messageServer.toLowerCase() == 'ok') {
                } else {
                    showAlertTopEnd("info", "VERIFICACIÓN!", beanCrudResponse2.messageServer);
                }
            }
            if (beanCrudResponse2.beanPagination !== null) {
                beanPaginationNotificacion = beanCrudResponse2.beanPagination;
                addNotificacion(beanPaginationNotificacion);
            }

        })
        .catch(err => {
            document.querySelector("#vistaCatalogo").innerHTML = "";
            circleCargando.toggleLoader("hide");
            showAlertErrorRequest();
        });
    /* */

});

function processAjaxLibro() {

    let parameters_pagination = '';
    switch (beanRequestLibro.operation) {

        default:

            parameters_pagination +=
                '?filtro=';
            parameters_pagination +=
                '&pagina=1';
            parameters_pagination +=
                '&registros=1';
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestLibro.entity_api + "/" + beanRequestLibro.operation +
            parameters_pagination,
        type: beanRequestLibro.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token") + (Cookies.get("clpe_libro")) ? "libro" + Cookies.get("clpe_libro") : ""
        },
        data: null,
        cache: false,
        contentType: ((beanRequestLibro.operation == 'update' || beanRequestLibro.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json'
    }).done(function (beanCrudResponse) {

        $('#modalCargandoLibro').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                document.querySelector("#pageLibro").value = 1;
                document.querySelector("#sizePageLibro").value = 5;
                $('#ventanaModalManLibro').modal('hide');
            } else {
                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationLibro = beanCrudResponse.beanPagination;
            listaLibro(beanPaginationLibro);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        document.querySelector("#vistaCatalogo").innerHTML = "";
        $('#modalCargandoLibro').modal("hide");
        showAlertErrorRequest();


    });

}

function listaLibro(beanPagination) {

    if (beanPagination.list.length == 0) {
        return;
    }
    beanPagination.list.forEach((libro) => {
        libroSelected = libro
        if (libro.list.length == 0) {
            document.querySelector("#videoInicio").innerHTML = `<img class="img-responsive center-box img-index-aula" src="${getHostFrontEnd() + 'adjuntos/logoHeader.jpg'}"/>`;
        } else {
            libro.list.forEach(detalle => {
                if (detalle.videonombre == "") {
                    removeClass(document.querySelector(".dt-libro").parentElement.parentElement.parentElement.parentElement, "mt-10");
                    addClass(document.querySelector(".dt-libro").parentElement.parentElement.parentElement.parentElement, "mt-2");
                }
                if (detalle.video.toLocaleLowerCase().includes("mp4")) {
                    document.querySelector("#tituloVideo").innerHTML = detalle.videonombre;
                    document.querySelector("#videoInicio").innerHTML = `<video class="fm-video video-js vjs-16-9 vjs-big-play-centered" data-setup="{}" controls id="fm-video" poster="${getHostFrontEnd()}adjuntos/logoHeader.jpg">
                    <source  src="${getHostFrontEnd() + detalle.video}" type="video/mp4" style="height:auto;">
                </video>`;

                } else {
                    document.querySelector("#tituloVideo").innerHTML = detalle.videonombre;
                    document.querySelector("#videoInicio").innerHTML = `<img class="img-responsive center-box img-index-aula" style="height:auto;" src="${getHostFrontEnd() + detalle.video}"/>`;
                }

            });

        }


    });

    document.querySelectorAll('.dt-libro').forEach((img) => {
        img.setAttribute('src', getHostFrontEnd() + "adjuntos/libros/" + libroSelected.imagen);
    });

    include_script(getHostFrontEnd() + "plugins/video/js/video.js");
    addEventsButtonsLibro();
    /*
     flowplayer('.flowplayer', {
        src: getHostFrontEnd() + "adjuntos/libros/" + libroSelected.video,
        logo: getHostFrontEnd() + "adjuntos/logoHeader.jpg",
        title: libroSelected.nombre,
        preload: 'auto'
    })
    
*/

}

function addEventsButtonsLibro() {
    document.querySelectorAll('.ver-capitulo').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            libroSelected = findByLibro(
                btn.parentElement.getAttribute('idlibro')
            );
            if (libroSelected != undefined) {
                document.querySelector("#seccion-libro").classList.add("d-none");
                document.querySelector("#seccion-capitulo").classList.remove("d-none");
                beanRequestCapitulo.type_request = 'GET';
                beanRequestCapitulo.operation = 'paginate';
                $('#modalCargandoCapitulo').modal('show');
            } else {
                console.log(
                    'warning => ',
                    'No se encontró el libro para poder ver'
                );
            }
        };
    });
    document.querySelectorAll('.editar-libro').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            libroSelected = findByLibro(
                btn.parentElement.parentElement.getAttribute('idlibro')
            );

            if (libroSelected != undefined) {
                addLibro(libroSelected);
                $("#tituloModalManLibro").html("EDITAR VIDEO DE LIBROES");
                $("#ventanaModalManLibro").modal("show");
                beanRequestLibro.type_request = 'POST';
                beanRequestLibro.operation = 'update';
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });

}

function addViewArchivosPreviusLibro() {

    $("#txtImagenLibro").change(function () {
        filePreview(this, "#imagePreview");
    });

    $("#txtVideoLibro").change(function () {
        videoPreview(this, "#videoPreview");
    });
}

function findIndexLibro(idbusqueda) {
    return beanPaginationLibro.list.findIndex(
        (Libro) => {
            if (Libro.idlibro == parseInt(idbusqueda))
                return Libro;


        }
    );
}

function findByLibro(idlibro) {
    return beanPaginationLibro.list.find(
        (Libro) => {
            if (parseInt(idlibro) == Libro.idlibro) {
                return Libro;
            }


        }
    );
}


function filePreview(input, imagen) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        imagen;
        reader.onload = function (e) {
            $(imagen).html(
                "<img width='100%' alt='user-picture' class='img-responsive center-box' src='" +
                e.target.result +
                "' />"
            );
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function videoPreview(input, imagen) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        imagen;
        reader.onload = function (e) {
            $(imagen).html(
                "<video width='100%' alt='user-picture' class='img-responsive center-box' controls ><source src='" +
                e.target.result +
                "' type='video/mp4'></video>"
            );
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function downloadURL(url) {
    var hiddenIFrameID = 'hiddenDownloader',
        iframe = document.getElementById(hiddenIFrameID);
    if (iframe === null) {
        iframe = document.createElement('iframe');
        iframe.id = hiddenIFrameID;
        iframe.style.width = '100%';
        iframe.style.height = '100%';
        document.querySelector('#modalFrameContenidoSubtitulo').appendChild(iframe);
    }
    iframe.src = url;
};
