var beanPaginationSubtitulo;
var beanPaginationSubtituloTitulo;
var beanPaginationRecurso, finalizadoSelected = undefined;
var subtituloSelected, subtituloSelectedInicial, subtituloSelectedAll, player;
var videosubtituloSelected;
var beanRequestSubtitulo = new BeanRequest();
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

document.addEventListener('DOMContentLoaded', function () {
    beanRequestSubtitulo.entity_api = 'lecciones';
    beanRequestSubtitulo.operation = 'obtener';
    beanRequestSubtitulo.type_request = 'GET';

    $('#sizePageSubtitulo').change(function () {
        beanRequestSubtitulo.type_request = 'GET';
        beanRequestSubtitulo.operation = 'obtener';
        $('#modalCargandoSubtitulo').modal('show');
    });
    $("#txtVideoLeccion").on('change', function () {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    })

    $('#modalCargandoSubtitulo').modal('show');

    $("#modalCargandoSubtitulo").on('shown.bs.modal', function () {
        processAjaxSubtitulo();
    });
    $("#ventanaModalManSubtitulo").on('hide.bs.modal', function () {
        beanRequestSubtitulo.type_request = 'GET';
        beanRequestSubtitulo.operation = 'paginate';
    });
    $("#btnAbrirsubtitulo").click(function () {
        beanRequestSubtitulo.operation = 'add';
        beanRequestSubtitulo.type_request = 'POST';
        $("#imagePreview").html("");
        $("#tituloModalManSubtitulo").html("REGISTRAR LIBRO");
        addSubtitulo();
        $("#ventanaModalManSubtitulo").modal("show");
    });


    $("#formularioSubtitulo").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (validarDormularioSubtitulo()) {
            $('#modalCargandoSubtitulo').modal('show');
        }
    });


});

function loadVideo(lista) {
    window.YT.ready(function () {
        player = new window.YT.Player("ytplayer", {
            height: '360',
            width: '640',
            host: 'http://www.youtube-nocookie.com',
            playerVars: {
                rel: 0,
                modestbranding: 1,
                playlist: lista
            },
            events: {
                onReady: onPlayerReady,
                onStateChange: onPlayerStateChange
            }
        });
    });

    function onPlayerReady(event) {
        event.target.playVideo();
    }

    function onPlayerStateChange(event) {
        var videoStatuses = Object.entries(window.YT.PlayerState);
        //console.log(videoStatuses.find(status => status[1] === event.data)[0]);
    }
}

function loadVideoCuestionario(lista) {
    window.YT.ready(function () {
        player = new window.YT.Player("ytplayerCuestionario", {
            height: '360',
            width: '640',
            host: 'http://www.youtube-nocookie.com',
            playerVars: {
                rel: 0,
                modestbranding: 1,
                playlist: lista
            },
            events: {
                onReady: onPlayerReady,
                onStateChange: onPlayerStateChange
            }
        });
    });

    function onPlayerReady(event) {
        event.target.playVideo();
    }

    function onPlayerStateChange(event) {
        var videoStatuses = Object.entries(window.YT.PlayerState);
        //console.log(videoStatuses.find(status => status[1] === event.data)[0]);
    }
}

function processAjaxSubtitulo() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (beanRequestSubtitulo.operation == 'updatestado' ||
        beanRequestSubtitulo.operation == 'add'
    ) {

        json = {
            subtitulo: subtituloSelected.codigo,
            estado: 1
        };


    } else {
        form_data = null;
    }

    switch (beanRequestSubtitulo.operation) {

        case 'add':

            break;
        case 'updatestado':
            form_data.append("class", JSON.stringify(json));
            break;
        case 'anteriorleccion':
            parameters_pagination +=
                '?subtitulo=' + subtituloSelectedAll.codigo;
            break;

        default:

            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestSubtitulo.entity_api + "/" + beanRequestSubtitulo.operation +
            parameters_pagination,
        type: beanRequestSubtitulo.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestSubtitulo.operation == 'updatestado' || beanRequestSubtitulo.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json'
    }).done(function (beanCrudResponse) {

        $('#modalCargandoSubtitulo').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                $('#ventanaModalManSubtitulo').modal('hide');
                if (beanCrudResponse.beanPagination !== null) {
                    beanPaginationSubtitulo = beanCrudResponse.beanPagination;

                    listaSubtitulo(beanPaginationSubtitulo);
                }
            } else if (beanCrudResponse.messageServer.toLowerCase() == 'general') {
                if (beanRequestSubtitulo.operation == 'updatestado') {
                    window.location.reload();
                } else {
                    if (beanCrudResponse.beanPagination !== null) {
                        beanPaginationLeccion = beanCrudResponse.beanPagination;
                        subtituloSelectedInicial = beanPaginationLeccion.list[0];
                        listaPreguntasGeneral(beanPaginationLeccion);
                        processAjaxSubtituloTitulo(document.querySelector("#bodySubtitulo-titulo-cuestionario"));
                    }
                }

            } else if (beanCrudResponse.messageServer.toLowerCase() == 'interno') {
                if (beanRequestSubtitulo.operation == 'updatestado') {
                    window.location.reload();
                } else {
                    if (beanCrudResponse.beanPagination !== null) {
                        beanPaginationLeccion = beanCrudResponse.beanPagination;

                        listaPreguntasInternas(beanPaginationLeccion);
                        processAjaxSubtituloTitulo(document.querySelector("#bodySubtitulo-titulo-cuestionario"));
                    }
                }



            } else if (beanCrudResponse.messageServer.toLowerCase() == 'fin') {
                finalizadoSelected = "finalizado";

                $('#modalCargandoCertificado').modal('show');
                /*
                
                                if (beanCrudResponse.beanPagination !== null) {
                                    beanPaginationSubtitulo = beanCrudResponse.beanPagination;
                                    //   listaSubtituloFinalizado(beanPaginationSubtitulo);
                                    if (beanRequestSubtitulo.operation != "anteriorleccion") {
                                        subtituloSelectedInicial = beanPaginationSubtitulo.list[0];
                                        // processAjaxSubtituloTitulo(document.querySelector("#bodySubtitulo-titulo"));
                
                                    }
                                }
                */
            } else {
                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        } else {
            if (finalizadoSelected == undefined) {
                if (beanRequestSubtitulo.operation == "updatestado") {
                    window.location.reload();
                } else {
                    if (beanCrudResponse.beanPagination !== null) {
                        beanPaginationSubtitulo = beanCrudResponse.beanPagination;
                        if (beanRequestSubtitulo.operation != "anteriorleccion") {
                            listaSubtitulo(beanPaginationSubtitulo);
                            subtituloSelectedInicial = beanPaginationSubtitulo.list[0];
                            processAjaxSubtituloTitulo(document.querySelector("#bodySubtitulo-titulo"));
                        } else {

                            if (beanPaginationSubtitulo.countFilter > 0) {

                                if (beanPaginationLeccion != undefined) {
                                    //cuestionario view
                                    addClass(document.querySelector(".container-flat-form").parentElement, "d-none");
                                    listaSubtitulotoCuestionario(beanPaginationSubtitulo);
                                } else {
                                    //lecciones view
                                    listaSubtitulo(beanPaginationSubtitulo);
                                    if (subtituloSelectedInicial.subTitulo.codigo == subtituloSelectedAll.codigo) {
                                        removeClass(document.querySelector(".container-flat-form").parentElement, "d-none");
                                    } else {
                                        addClass(document.querySelector(".container-flat-form").parentElement, "d-none");
                                    }
                                }

                                addEventLeccion();
                            } else {
                                // addClass(document.querySelector(".container-flat-form").parentElement, "d-none");
                                showAlertTopEnd("info", "No se encuentra la lección disponible", "");
                            }
                        }
                    }
                }
            } else {

                if (beanCrudResponse.beanPagination !== null) {
                    beanPaginationSubtitulo = beanCrudResponse.beanPagination;
                    listaSubtituloFinalizado(beanPaginationSubtitulo);
                    if (beanRequestSubtitulo.operation != "anteriorleccion") {
                        subtituloSelectedInicial = beanPaginationSubtitulo.list[0];
                        processAjaxSubtituloTitulo(document.querySelector("#bodySubtitulo-titulo"));
                    }
                    addEventLeccion();


                }
            }



        }

    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoSubtitulo').modal("hide");
        showAlertErrorRequest((jqXHR.responseText).trim() + " -- " + errorThrown);


    });

}

function processAjaxSubtituloTitulo(documentId = document.querySelector("#bodySubtitulo-titulo")) {

    circleCargando.containerOcultar = $(documentId);
    circleCargando.container = $(documentId.parentElement);
    circleCargando.createLoader();
    circleCargando.toggleLoader("show");

    $.ajax({
        url: getHostAPI() + "lecciones/subtitulotitulo",
        type: "GET",
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: null,
        cache: false,
        contentType: 'application/json; charset=UTF-8',
        processData: false,
        dataType: 'json'
    }).done(function (beanCrudResponse) {
        console.log(beanCrudResponse);
        circleCargando.toggleLoader("hide");
        if (beanCrudResponse.messageServer !== null) {
            showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
        } else {
            if (beanCrudResponse.beanPagination !== null) {
                beanPaginationSubtituloTitulo = beanCrudResponse.beanPagination;
                beanPaginationRecurso = new Array();
                beanPaginationSubtituloTitulo.list.forEach(
                    (recurso) => {
                        if (recurso.disponible != undefined) {
                            beanPaginationRecurso.push(recurso);
                        }

                    }
                );
                if (documentId == document.querySelector("#bodySubtitulo-titulo")) {

                    listaSubtituloTitulo(beanPaginationSubtituloTitulo, documentId);
                } else {
                    listaSubtituloTituloCuestionario(beanPaginationSubtituloTitulo, documentId);
                }
            }
            if (finalizadoSelected != undefined) {
                processAjaxCertificado(document.querySelector("#htmlMensaje"));
            }
        }

    }).fail(function (jqXHR, textStatus, errorThrown) {
        showAlertErrorRequest((jqXHR.responseText).trim() + " -- " + errorThrown);


    });

}

function addSubtitulo(subtitulo = undefined) {
    //LIMPIAR LOS CAMPOS

    document.querySelector('#txtCodigoSubtitulo').value = (subtitulo == undefined) ? '' : subtitulo.codigo;

    document.querySelector('#txtNombreSubtitulo').value = (subtitulo == undefined) ? '' : subtitulo.nombre;

}

function listaSubtitulo(beanPagination) {
    let row = "", rowother = new Array();

    if (beanPagination.list.length == 0) {
        return;
    }
    document.querySelector('#sectionLeccion').classList.remove("d-none");
    document.querySelector('#sectionPreguntas').classList.add("d-none");
    document.querySelector('#sectionMensaje').classList.add("d-none");
    beanPagination.list.forEach((videosubtitulo) => {
        subtituloSelected = videosubtitulo.subTitulo;
        row += videosubtitulo.nombre + ",";
        rowother.push(videosubtitulo.nombre);
    });
    document.querySelector('.dt-titulo').innerHTML = '"' + subtituloSelected.titulo.nombre + '"';
    document.querySelector('.dt-subtitulo').innerHTML = subtituloSelected.nombre;
    document.querySelector('.dt-subtitulo-pdf').innerHTML = `<button type="button" class="btn btn-light"><img style="width:35px; height:35px;"src="${getHostFrontEnd()}vistas/assets/img/pdf.png" alt="Subtitulo"><span  class="ml-1">Descargar Lección</span> </button>`;

    if (player == undefined) {
        $.getScript("https://www.youtube.com/iframe_api", function () {
            loadVideo(row);
        });
    } else {
        // player.cueVideoById(row);
        //  player.cuePlaylist({ list: row });
        player.loadPlaylist(
            {
                playlist: rowother
            }
        );


    }
    addEventsButtonsSubtitulo();

}

function listaSubtituloFinalizado(beanPagination) {
    let row = "", rowother = new Array();

    if (beanPagination.list.length == 0) {
        return;
    }
    document.querySelector('#sectionLeccion').classList.remove("d-none");
    document.querySelector('#sectionPreguntas').classList.add("d-none");
    document.querySelector('#sectionMensaje').classList.add("d-none");
    beanPagination.list.forEach((videosubtitulo) => {
        subtituloSelected = videosubtitulo.subTitulo;
        row += videosubtitulo.nombre + ",";
        rowother.push(videosubtitulo.nombre);
    });
    document.querySelector('.dt-titulo').innerHTML = '"' + subtituloSelected.titulo.nombre + '"';
    document.querySelector('.dt-subtitulo').innerHTML = subtituloSelected.nombre;
    document.querySelector('#htmlCuestionarioFin').innerHTML = "";
    addClass(document.querySelector('#htmlCuestionarioFin'), "d-none");
    if (player == undefined) {
        $.getScript("https://www.youtube.com/iframe_api", function () {
            loadVideo(row);
        });
    } else {
        // player.cueVideoById(row);
        //  player.cuePlaylist({ list: row });
        player.loadPlaylist(
            {
                playlist: rowother
            }
        );


    }
    addEventsButtonsSubtitulo();

}

function listaSubtitulotoCuestionario(beanPagination) {
    let row = "", rowother = new Array();

    if (beanPagination.list.length == 0) {
        return;
    }
    document.querySelector('#sectionLeccion').classList.add("d-none");
    document.querySelector('#sectionPreguntas').classList.remove("d-none");
    document.querySelector('#sectionMensaje').classList.add("d-none");
    document.querySelector('#htmlCuestionarioVideo').classList.remove("d-none");
    document.querySelector('#titleManRespuesta').parentElement.parentElement.classList.add("d-none");


    beanPagination.list.forEach((videosubtitulo) => {
        subtituloSelected = videosubtitulo.subTitulo;
        row += videosubtitulo.nombre + ",";
        rowother.push(videosubtitulo.nombre);
    });
    document.querySelector('.dt-titulo-cuest').innerHTML = '"' + subtituloSelected.titulo.nombre + '"';
    document.querySelector('.dt-subtitulo-cuest').innerHTML = subtituloSelected.nombre;

    if (player == undefined) {
        $.getScript("https://www.youtube.com/iframe_api", function () {
            loadVideoCuestionario(row);
        });
    } else {
        // player.cueVideoById(row);
        //  player.cuePlaylist({ list: row });
        player.loadPlaylist(
            {
                playlist: rowother
            }
        );


    }

}

function addEventsButtonsSubtitulo() {

    document.querySelectorAll('.descargar-archivo').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            $("#modalFrameSubtitulo").modal("show");
            downloadURL(getHostFrontEnd() + "adjuntos/archivos/" + subtituloSelected.titulo.libro.codigo + "/" + subtituloSelected.titulo.codigo + "/PDF/" + subtituloSelected.pdf);
            document.querySelector("#descargarPdf").href = getHostFrontEnd() + "adjuntos/archivos/" + subtituloSelected.titulo.libro.codigo + "/" + subtituloSelected.titulo.codigo + "/PDF/" + subtituloSelected.pdf;


        };
    });

}

function findByVideoSubtitulo(idsubtitulo) {
    return beanPaginationSubtitulo.list.find(
        (Subtitulo) => {
            if (parseInt(idsubtitulo) == Subtitulo.idsubtitulo) {
                return Subtitulo;
            }


        }
    );
}

var validarDormularioSubtitulo = () => {

    if (document.querySelector("#txtComentarioLeccion").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Comentario",
            type: "warning",
            timer: 800,
            showConfirmButton: false
        });
        return false;
    }
    if (beanRequestSubtitulo.operation == 'add') {

        /*IMAGEN */
        if (document.querySelector("#txtImagenSubtitulo").files.length == 0) {
            showAlertTopEnd("info", "Vacío", "ingrese Imagen");
            return false;
        }
        if (!(document.querySelector("#txtImagenSubtitulo").files[0].type == "image/png" || document.querySelector("#txtImagenSubtitulo").files[0].type == "image/jpg" || document.querySelector("#txtImagenSubtitulo").files[0].type == "image/jpeg")) {
            showAlertTopEnd("info", "Formato Incorrecto", "Ingrese tipo de arhivo Imagen => png,jpg,jpeg");
            return false;
        }
        //menor a   1700 KB
        if (document.querySelector("#txtImagenSubtitulo").files[0].size > (1700 * 1024)) {
            showAlertTopEnd("info", "Tamaño excedido", "el tamaño del archivo tiene que ser menor a 1700 KB");
            return false;
        }

        if (document.querySelector("#txtVideoSubtitulo").files.length == 0) {
            swal({
                title: "Vacío",
                text: "Ingrese Video",
                type: "warning",
                timer: 800,
                showConfirmButton: false
            });
            return false;
        }
    }

    return true;
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

function eventosAcordion() {
    /************************
     ****** Accordion ******
     *************************/
    $("ul.accordion").each(function () {
        if ($(this).height() > 0) {
            $(this).css("height", "auto");
        }

        $(this)
            .children("li")
            .each(function () {
                var a = $(this)
                    .children("span")
                    .children("a");
                if ($(this).hasClass("active"))
                    $(a).append('<i class="fa fa-chevron-down" style="line-height: 25px;"></i>');
                else $(a).append('<i class="fa fa-chevron-right" style="line-height: 25px;"></i>');

                var parent = this;
                $(a).click(function (e) {
                    e.preventDefault();
                    if (!$(parent).hasClass("active")) {
                        $(parent)
                            .addClass("active")
                            .children("article")
                            .slideDown(250, "easeOutExpo");
                        $(a)
                            .children("i")
                            .removeClass("fa-chevron-right")
                            .addClass("fa-chevron-down");
                    } else {
                        $(parent)
                            .removeClass("active")
                            .children("article")
                            .slideDown(250, "easeInExpo");
                        $(a)
                            .children("i")
                            .removeClass("fa-chevron-down")
                            .addClass("fa-chevron-right");
                    }
                });
            });
    });

}

function listaSubtituloTitulo(beanPagination, documentId = document.querySelector("#bodySubtitulo-titulo")) {
    let row = "", contador = 1, contadorsubtitulo = 1, tituloTemporal = "", recurso;

    if (beanPagination.list.length == 0) {
        return;
    }
    beanPagination.list.sort(compare);
    beanPagination.list.forEach((subtitulo) => {
        if (subtitulo.disponible == undefined) {
            if (subtitulo.titulo == "") {
                if (subtitulo.estado == 1) {
                    row += `
                <article>
                    <div class="btn-light form-row pl-4">
                        <p class="col-1" style="max-width: 1.7em;">
                            <input type="checkbox" ${(subtitulo.codigo < (beanPaginationSubtitulo == undefined ? beanPaginationLeccion.list[0].subtitulo.codigo : beanPaginationSubtitulo.list[0].subTitulo.codigo)) ? "checked" : ""} class="aula-cursor-mano" style="
                                    -ms-transform: scale(1.5); 
                                    -moz-transform: scale(1.5); 
                                    -webkit-transform: scale(1.5); 
                                    -o-transform: scale(1.5);  transform: scale(1.5);
                                    padding: 10px;">
                        </p>
                        <p class="col-sm-6 col-11 py-1">
                            <span class="text-udemy f-14"> Cuestinario Final del Capítulo</span>
                            <br>
                            <i class="zmdi zmdi-file-text text-primary"></i>
                        </p>
                    </div>
                </article>
                `;
                } else if (subtitulo.estado == 2) {
                    row += `
                <article idsubtitulo="${subtitulo.idsubTitulo}">
                    <div class="btn-light form-row pl-4 ">
                        <p class="col-1" style="max-width: 1.7em;">
                            <input type="checkbox" ${(subtitulo.codigo < (beanPaginationSubtitulo == undefined ? beanPaginationLeccion.list[0].subtitulo.codigo : beanPaginationSubtitulo.list[0].subTitulo.codigo)) ? "checked" : ""} class="aula-cursor-mano" style="
                                    -ms-transform: scale(1.5); 
                                    -moz-transform: scale(1.5); 
                                    -webkit-transform: scale(1.5); 
                                    -o-transform: scale(1.5);  transform: scale(1.5);
                                    padding: 10px;">
                        </p>
                        <p class="col-sm-6 col-11 py-1">
                            <span class="text-udemy f-14">${subtitulo.nombre}</span>
                            <br>
                            <i class="zmdi zmdi-file-text text-primary"></i>
                        </p>
                    </div>
                </article>
                `;
                }


            } else {
                recurso = findByRecurso(subtitulo.codigo);
                if (tituloTemporal == "") {
                    tituloTemporal = subtitulo.titulo.codigo;

                    row += `
            <li class="${(beanPaginationSubtitulo == undefined ? beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[0] + "." + beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[1] + "." + beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[2] : beanPaginationSubtitulo.list[0].subTitulo.titulo.codigo) == subtitulo.titulo.codigo ? "active" : ""}" style="border-bottom: 1px solid #dedfe0;">
            <span><a href="#" class="text-udemy f-weight-700 py-2" style="line-height: 24px;">Capítulo ${(contador++) + " : " + subtitulo.titulo.nombre}</a></span>
            <article ${(subtitulo.codigo > (beanPaginationSubtitulo == undefined ? beanPaginationLeccion.list[0].subtitulo.codigo : beanPaginationSubtitulo.list[0].subTitulo.codigo)) ? "" : " idsubtitulo='" + subtitulo.idsubTitulo + "' class='seleccionar-subtitulo aula-cursor-mano'"}>
                <div class="btn-light form-row pl-4 ${beanPaginationSubtitulo == undefined ? "" : beanPaginationSubtitulo.list[0].subTitulo.codigo == subtitulo.codigo ? "btn-light-hover" : ""}">
                    <p class="col-1" style="max-width: 1.7em;">
                        <input type="checkbox" ${(subtitulo.codigo < (beanPaginationSubtitulo == undefined ? beanPaginationLeccion.list[0].subtitulo.codigo : beanPaginationSubtitulo.list[0].subTitulo.codigo)) ? "checked" : ""} class="aula-cursor-mano" style="
                                -ms-transform: scale(1.5); 
                                -moz-transform: scale(1.5); 
                                -webkit-transform: scale(1.5); 
                                -o-transform: scale(1.5);  transform: scale(1.5);
                                padding: 10px;">
                    </p>
                    <p class="col-sm-6 col-11 py-1">
                        <span class="text-udemy f-14">Subtítulo ${(contadorsubtitulo++) + " : " + subtitulo.nombre}</span>
                        <br>
                        <i class="zmdi zmdi-youtube-play text-danger"></i>
                        ${recurso == undefined ? '' : '<a href="recursos" class="p-1"><button type="button" class="btn btn-outline-info py-0 mx-2"><i class="zmdi zmdi-folder-outline mr-1"></i> Recursos</button></a>'}
                        
                    </p>
                </div>
            </article>
            `;

                } else {
                    //si el titulo es igual al anterior entonces agrega subtitulo
                    if (subtitulo.titulo.codigo == tituloTemporal) {
                        row += `
                    <article  ${(subtitulo.codigo > (beanPaginationSubtitulo == undefined ? beanPaginationLeccion.list[0].subtitulo.codigo : beanPaginationSubtitulo.list[0].subTitulo.codigo)) ? "" : " idsubtitulo='" + subtitulo.idsubTitulo + "' class='seleccionar-subtitulo aula-cursor-mano'"}>
                        <div class="btn-light form-row pl-4 ${beanPaginationSubtitulo == undefined ? "" : beanPaginationSubtitulo.list[0].subTitulo.codigo == subtitulo.codigo ? "btn-light-hover" : ""}">
                            <p class="col-1" style="max-width: 1.7em;">
                                <input type="checkbox" ${(subtitulo.codigo < (beanPaginationSubtitulo == undefined ? beanPaginationLeccion.list[0].subtitulo.codigo : beanPaginationSubtitulo.list[0].subTitulo.codigo)) ? "checked" : ""} class="aula-cursor-mano" style="
                                        -ms-transform: scale(1.5); 
                                        -moz-transform: scale(1.5); 
                                        -webkit-transform: scale(1.5); 
                                        -o-transform: scale(1.5);  transform: scale(1.5);
                                        padding: 10px;">
                            </p>
                            <p class="col-sm-6 col-11 py-1">
                                <span class="text-udemy f-14">Subtítulo ${(contadorsubtitulo++) + " : " + subtitulo.nombre}</span>
                                <br>
                                <i class="zmdi zmdi-youtube-play text-danger"></i>
                                ${recurso == undefined ? '' : '<a href="recursos" class="p-1"><button type="button" class="btn btn-outline-info py-0 mx-2"><i class="zmdi zmdi-folder-outline mr-1"></i> Recursos</button></a>'}
                            </p>
                        </div>
                    </article>
                    `;
                    } else {
                        row += `
                </li>
                    `;
                        contadorsubtitulo = 1;
                        //camibar el temporal por el nuevo titulo
                        tituloTemporal = subtitulo.titulo.codigo;
                        row += `
                    <li class="${(beanPaginationSubtitulo == undefined ? beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[0] + "." + beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[1] + "." + beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[2] : beanPaginationSubtitulo.list[0].subTitulo.titulo.codigo) == subtitulo.titulo.codigo ? "active" : ""}" style="border-bottom: 1px solid #dedfe0;">
                    <span><a href="#" class="text-udemy f-weight-700 py-2" style="line-height: 24px;">Capítulo ${contador++ + " : " + subtitulo.titulo.nombre}</a></span>
                    <article  ${(subtitulo.codigo > (beanPaginationSubtitulo == undefined ? beanPaginationLeccion.list[0].subtitulo.codigo : beanPaginationSubtitulo.list[0].subTitulo.codigo)) ? "" : " idsubtitulo='" + subtitulo.idsubTitulo + "' class='seleccionar-subtitulo aula-cursor-mano'"}>
                        <div class="btn-light form-row pl-4 ${beanPaginationSubtitulo == undefined ? "" : beanPaginationSubtitulo.list[0].subTitulo.codigo == subtitulo.codigo ? "btn-light-hover" : ""}">
                            <p class="col-1" style="max-width: 1.7em;">
                                <input type="checkbox" ${(subtitulo.codigo < (beanPaginationSubtitulo == undefined ? beanPaginationLeccion.list[0].subtitulo.codigo : beanPaginationSubtitulo.list[0].subTitulo.codigo)) ? "checked" : ""} class="aula-cursor-mano" style="
                                        -ms-transform: scale(1.5); 
                                        -moz-transform: scale(1.5); 
                                        -webkit-transform: scale(1.5); 
                                        -o-transform: scale(1.5);  transform: scale(1.5);
                                        padding: 10px;">
                            </p>
                            <p class="col-sm-6 col-11 py-1">
                                <span class="text-udemy f-14">Subtítulo ${(contadorsubtitulo++) + " : " + subtitulo.nombre}</span>
                                <br>
                                <i class="zmdi zmdi-youtube-play text-danger"></i>
                                ${recurso == undefined ? '' : '<a href="recursos" class="p-1"><button type="button" class="btn btn-outline-info py-0 mx-2"><i class="zmdi zmdi-folder-outline mr-1"></i> Recursos</button></a>'}
                            </p>
                        </div>
                    </article>
                    `;
                    }
                }
            }

            if (beanPaginationSubtituloTitulo.list[beanPaginationSubtituloTitulo.list.length - 1].codigo == subtitulo.codigo) {
                row += `
    </li>
        `;
            }
        }

    });
    documentId.innerHTML = row;
    eventosAcordion();
    addEventLeccion();
    if (finalizadoSelected != undefined) {
        document.querySelectorAll("input[type='checkbox']").forEach((btn) => {
            btn.checked = true;
        });
    }

}

function listaSubtituloTituloCuestionario(beanPagination, documentId = document.querySelector("#bodySubtitulo-titulo")) {
    let row = "", contador = 1, contadorsubtitulo = 1, tituloTemporal = "", recurso;

    if (beanPagination.list.length == 0) {
        return;
    }
    beanPagination.list.sort(compare);

    beanPagination.list.forEach((subtitulo) => {
        if (subtitulo.disponible == undefined) {
            if (subtitulo.titulo == "") {
                if (subtitulo.estado == 1) {
                    row += `
                <article ${beanPaginationLeccion.list[0].test.tipo == 1 ? (beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[0] + "." + beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[1] + "." + beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[2] == subtitulo.codigo.split(".")[0] + "." + subtitulo.codigo.split(".")[1] + "." + subtitulo.codigo.split(".")[2] ? "class='ver-cuestionario'" : "") : ""} >
                    <div class="btn-light form-row pl-4 ${beanPaginationLeccion.list[0].test.tipo == 1 ? ((beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[0] + "." + beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[1] + "." + beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[2] == subtitulo.codigo.split(".")[0] + "." + subtitulo.codigo.split(".")[1] + "." + subtitulo.codigo.split(".")[2] ? "btn-light-hover" : "")) : ""}">
                        <p class="col-1" style="max-width: 1.7em;">
                            <input type="checkbox" ${(subtitulo.codigo < (beanPaginationSubtitulo == undefined ? beanPaginationLeccion.list[0].subtitulo.codigo : beanPaginationSubtitulo.list[0].subTitulo.codigo)) ? "checked" : ""} class="aula-cursor-mano" style="
                                    -ms-transform: scale(1.5); 
                                    -moz-transform: scale(1.5); 
                                    -webkit-transform: scale(1.5); 
                                    -o-transform: scale(1.5);  transform: scale(1.5);
                                    padding: 10px;">
                        </p>
                        <p class="col-11 py-1">
                            <span class="text-udemy f-14"> Cuestinario Final del Capítulo</span>
                            <br>
                            <i class="zmdi zmdi-file-text text-primary"></i>
                        </p>
                    </div>
                </article>
                `;
                } else if (subtitulo.estado == 2) {

                    row += `
                <article ${beanPaginationLeccion.list[0].test.tipo == 2 ? (subtitulo.codigo.split(".")[0] + "." + subtitulo.codigo.split(".")[1] + "." + subtitulo.codigo.split(".")[2] == beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[0] + "." + beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[1] + "." + beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[2] ? "class='ver-cuestionario'" : "") : ""} >
                    <div class="btn-light form-row pl-4 ${beanPaginationLeccion.list[0].subtitulo.titulo == undefined ? "" : beanPaginationLeccion.list[0].subtitulo.codigo == subtitulo.codigo ? "btn-light-hover" : ""}">
                        <p class="col-1" style="max-width: 1.7em;">
                            <input type="checkbox" ${beanPaginationLeccion.list[0].test.tipo == 1 ? ((subtitulo.codigo.split(".")[0] + "." + subtitulo.codigo.split(".")[1] + "." + subtitulo.codigo.split(".")[2] <= beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[0] + "." + beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[1] + "." + beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[2]) ? "checked" : "") : ((subtitulo.codigo < beanPaginationLeccion.list[0].subtitulo.codigo) ? "checked" : "")} class="aula-cursor-mano" style="
                                    -ms-transform: scale(1.5); 
                                    -moz-transform: scale(1.5); 
                                    -webkit-transform: scale(1.5); 
                                    -o-transform: scale(1.5);  transform: scale(1.5);
                                    padding: 10px;">
                        </p>
                        <p class="col-11 py-1">
                            <span class="text-udemy f-14">${subtitulo.nombre}</span>
                            <br>
                            <i class="zmdi zmdi-file-text text-primary"></i>
                        </p>
                    </div>
                </article>
                `;
                }

            } else {
                recurso = findByRecurso(subtitulo.codigo);
                if (tituloTemporal == "") {
                    tituloTemporal = subtitulo.titulo.codigo;

                    row += `
            <li class="${(beanPaginationSubtitulo == undefined ? beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[0] + "." + beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[1] + "." + beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[2] : beanPaginationSubtitulo.list[0].subTitulo.titulo.codigo) == subtitulo.titulo.codigo ? "active" : ""}" style="border-bottom: 1px solid #dedfe0;">
            <span><a href="#" class="text-udemy f-weight-700 py-2" style="line-height: 24px;">Capítulo ${(contador++) + " : " + subtitulo.titulo.nombre}</a></span>
            <article  ${beanPaginationLeccion.list[0].test.tipo == 1 ? ((subtitulo.titulo.codigo <= beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[0] + "." + beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[1] + "." + beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[2]) ? " idsubtitulo='" + subtitulo.idsubTitulo + "' class='seleccionar-subtitulo' " : "") : ((subtitulo.codigo > beanPaginationLeccion.list[0].subtitulo.codigo) ? "" : " idsubtitulo='" + subtitulo.idsubTitulo + "' class='seleccionar-subtitulo' ")}>
                <div class="btn-light form-row pl-4 ">
                    <p class="col-1" style="max-width: 1.7em;">
                        <input type="checkbox" ${beanPaginationLeccion.list[0].test.tipo == 1 ? ((subtitulo.titulo.codigo <= beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[0] + "." + beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[1] + "." + beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[2]) ? "checked" : "") : ((subtitulo.codigo <= beanPaginationLeccion.list[0].subtitulo.codigo) ? "checked" : "")} class="aula-cursor-mano" style="
                                -ms-transform: scale(1.5); 
                                -moz-transform: scale(1.5); 
                                -webkit-transform: scale(1.5); 
                                -o-transform: scale(1.5);  transform: scale(1.5);
                                padding: 10px;">
                    </p>
                    <p class="col-11 py-1">
                        <span class="text-udemy f-14">Subtítulo ${(contadorsubtitulo++) + " : " + subtitulo.nombre}</span>
                        <br>
                        <i class="zmdi zmdi-youtube-play text-danger"></i>
                        ${recurso == undefined ? '' : '<a href="recursos" class="p-1"><button type="button" class="btn btn-outline-info py-0 mx-2"><i class="zmdi zmdi-folder-outline mr-1"></i> Recursos</button></a>'}
                    </p>
                </div>
            </article>
            `;

                } else {
                    //si el titulo es igual al anterior entonces agrega subtitulo
                    if (subtitulo.titulo.codigo == tituloTemporal) {
                        row += `
                    <article ${beanPaginationLeccion.list[0].test.tipo == 1 ? ((subtitulo.titulo.codigo <= beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[0] + "." + beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[1] + "." + beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[2]) ? " idsubtitulo='" + subtitulo.idsubTitulo + "' class='seleccionar-subtitulo' " : "") : ((subtitulo.codigo > beanPaginationLeccion.list[0].subtitulo.codigo) ? "" : " idsubtitulo='" + subtitulo.idsubTitulo + "' class='seleccionar-subtitulo' ")}>
                        <div class="btn-light form-row pl-4 ">
                            <p class="col-1" style="max-width: 1.7em;">
                                <input type="checkbox" ${beanPaginationLeccion.list[0].test.tipo == 1 ? ((subtitulo.titulo.codigo <= beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[0] + "." + beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[1] + "." + beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[2]) ? "checked" : "") : ((subtitulo.codigo <= beanPaginationLeccion.list[0].subtitulo.codigo) ? "checked" : "")} class="aula-cursor-mano" style="
                                        -ms-transform: scale(1.5); 
                                        -moz-transform: scale(1.5); 
                                        -webkit-transform: scale(1.5); 
                                        -o-transform: scale(1.5);  transform: scale(1.5);
                                        padding: 10px;">
                            </p>
                            <p class="col-11 py-1">
                                <span class="text-udemy f-14">Subtítulo ${(contadorsubtitulo++) + " : " + subtitulo.nombre}</span>
                                <br>
                                <i class="zmdi zmdi-youtube-play text-danger"></i>
                                ${recurso == undefined ? '' : '<a href="recursos" class="p-1"><button type="button" class="btn btn-outline-info py-0 mx-2"><i class="zmdi zmdi-folder-outline mr-1"></i> Recursos</button></a>'}
                            </p>
                        </div>
                    </article>
                    `;
                    } else {
                        row += `
                </li>
                    `;
                        contadorsubtitulo = 1;
                        //camibar el temporal por el nuevo titulo
                        tituloTemporal = subtitulo.titulo.codigo;
                        row += `
                    <li class="${(beanPaginationSubtitulo == undefined ? beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[0] + "." + beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[1] + "." + beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[2] : beanPaginationSubtitulo.list[0].subTitulo.titulo.codigo) == subtitulo.titulo.codigo ? "active" : ""}" style="border-bottom: 1px solid #dedfe0;">
                    <span><a href="#" class="text-udemy f-weight-700 py-2" style="line-height: 24px;">Capítulo ${contador++ + " : " + subtitulo.titulo.nombre}</a></span>
                    <article ${beanPaginationLeccion.list[0].test.tipo == 1 ? ((subtitulo.titulo.codigo <= beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[0] + "." + beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[1] + "." + beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[2]) ? " idsubtitulo='" + subtitulo.idsubTitulo + "' class='seleccionar-subtitulo' " : "") : ((subtitulo.codigo > beanPaginationLeccion.list[0].subtitulo.codigo) ? "" : " idsubtitulo='" + subtitulo.idsubTitulo + "' class='seleccionar-subtitulo' ")}>
                        <div class="btn-light form-row pl-4 ">
                            <p class="col-1" style="max-width: 1.7em;">
                                <input type="checkbox" ${beanPaginationLeccion.list[0].test.tipo == 1 ? ((subtitulo.titulo.codigo <= beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[0] + "." + beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[1] + "." + beanPaginationLeccion.list[0].subtitulo.codigo.split(".")[2]) ? "checked" : "") : ((subtitulo.codigo <= beanPaginationLeccion.list[0].subtitulo.codigo) ? "checked" : "")} class="aula-cursor-mano" style="
                                        -ms-transform: scale(1.5); 
                                        -moz-transform: scale(1.5); 
                                        -webkit-transform: scale(1.5); 
                                        -o-transform: scale(1.5);  transform: scale(1.5);
                                        padding: 10px;">
                            </p>
                            <p class="col-11 py-1">
                                <span class="text-udemy f-14">Subtítulo ${(contadorsubtitulo++) + " : " + subtitulo.nombre}</span>
                                <br>
                                <i class="zmdi zmdi-youtube-play text-danger"></i>
                                ${recurso == undefined ? '' : '<a href="recursos" class="p-1"><button type="button" class="btn btn-outline-info py-0 mx-2"><i class="zmdi zmdi-folder-outline mr-1"></i> Recursos</button></a>'}
                            </p>
                        </div>
                    </article>
                    `;
                    }
                }
            }

            if (beanPaginationSubtituloTitulo.list[beanPaginationSubtituloTitulo.list.length - 1].codigo == subtitulo.codigo) {
                row += `
    </li>
        `;
            }
        }

    });
    documentId.innerHTML = row;
    eventosAcordion();
    addEventLeccion();
}

function addEventLeccion() {

    document.querySelectorAll('.seleccionar-subtitulo').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            subtituloSelectedAll = findBySubtitulo(
                btn.getAttribute('idsubtitulo')
            );

            if (subtituloSelectedAll != undefined) {
                beanRequestSubtitulo.type_request = 'GET';
                beanRequestSubtitulo.operation = 'anteriorleccion';
                $('#modalCargandoSubtitulo').modal('show');
            } else {
                showAlertTopEnd("warning", "vacio", "No se encuentra la lección!");
            }


        };
    });

    document.querySelectorAll('.ver-cuestionario').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            addClass(document.querySelector("#htmlCuestionarioVideo"), "d-none");
            removeClass(document.querySelector("#titleManRespuesta").parentElement.parentElement, "d-none");

            //    beanRequestSubtitulo.operation = 'obtener';
            //  beanRequestSubtitulo.type_request = 'GET';
            //  $('#modalCargandoSubtitulo').modal('show');
        };
    });

    document.querySelectorAll('article').forEach((btn) => {
        if (subtituloSelectedAll != undefined) {
            if (subtituloSelectedAll.idsubTitulo == btn.getAttribute('idsubtitulo')) {
                addClass(btn.firstElementChild, "btn-light-hover");
            } else {
                removeClass(btn.firstElementChild, "btn-light-hover");

            }
        } else {
            console.log("no");
        }



    });

}

function findBySubtitulo(idblog) {
    return beanPaginationSubtituloTitulo.list.find(
        (Blog) => {
            if (parseInt(idblog) == Blog.idsubTitulo) {
                return Blog;
            }


        }
    );
}

function compare(a, b) {
    // Use toUpperCase() to ignore character casing
    const bandA = a.codigo.toUpperCase();
    const bandB = b.codigo.toUpperCase();

    let comparison = 0;
    if (bandA > bandB) {
        comparison = 1;
    } else if (bandA < bandB) {
        comparison = -1;
    }
    return comparison;
}

function findByRecurso(codigo) {
    return beanPaginationRecurso.find(
        (recurso) => {
            if (codigo == recurso.codigo) {
                return recurso;
            }


        }
    );
}

