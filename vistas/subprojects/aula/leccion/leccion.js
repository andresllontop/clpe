var beanPaginationLeccion;
var leccionSelected, player;
var beanRequestLeccion = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestLeccion.entity_api = 'lecciones';
    beanRequestLeccion.operation = 'paginate';
    beanRequestLeccion.type_request = 'GET';

    $("#modalCargandoLeccion").on('shown.bs.modal', function () {
        processAjaxLeccion();
    });
    $("#ventanaModalManLeccion").on('hide.bs.modal', function () {
        beanRequestLeccion.type_request = 'GET';
        beanRequestLeccion.operation = 'paginate';
    });

});

function processAjaxLeccion() {

    let parameters_pagination = '';

    switch (beanRequestLeccion.operation) {
        default:

            parameters_pagination +=
                '?filtro=' + tareaSelected.subTitulo.codigo;
            parameters_pagination +=
                '&pagina=1';
            parameters_pagination +=
                '&registros=30';
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestLeccion.entity_api + "/" + beanRequestLeccion.operation +
            parameters_pagination,
        type: beanRequestLeccion.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token") + (Cookies.get("clpe_libro") == undefined ? "" : " Clpe " + Cookies.get("clpe_libro"))
        },
        data: null,
        cache: false,
        contentType: 'application/json; charset=UTF-8',
        processData: false,
        dataType: 'json'
    }).done(function (beanCrudResponse) {
        $('#modalCargandoLeccion').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                document.querySelector("#pageLeccion").value = 1;
                $('#ventanaModalManLeccion').modal('hide');
            } else {

                showAlertTopEnd("info", beanCrudResponse.messageServer, "");
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationLeccion = beanCrudResponse.beanPagination;
            listaLeccion(beanPaginationLeccion);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoLeccion').modal("hide");
        showAlertErrorRequest();

    });

}

function listaLeccion(beanPagination) {

    document.querySelector('#sectionTareas').classList.add("d-none");
    document.querySelector('#sectionLecciones').classList.remove("d-none");
    document.querySelector('#titleManagerLeccion').innerHTML =
        'Lecciones Realizadas ' + ' <p class="text-center text-purple" style="font-weight: bold; ">' + tareaSelected.subTitulo.nombre + '</p>';
    let row = "", rowvideo = "", rowother = new Array();
    if (beanPagination.list.length == 0) {
        return;
    }
    beanPagination.list.forEach((leccion) => {
        if (leccion.idleccion == undefined) {
            rowvideo += leccion.nombre + ",";
            rowother.push(leccion.nombre);
        }

    });


    $(".vjs-poster").hide();
    if (player == undefined) {
        $.getScript("https://www.youtube.com/iframe_api", function () {
            loadVideo(rowvideo);
        });
        include_script(getHostFrontEnd() + "plugins/video/js/video.js");

    } else {
        // player.cueVideoById(row);
        //  player.cuePlaylist({ list: row });
        player.loadPlaylist(
            {
                playlist: rowother
            }
        );


    }


    addEventsButtonsLeccion();


}

function addEventsButtonsLeccion() {
    document.querySelectorAll('.dt-subtitulo-pdf').forEach((btn) => {
        btn.innerHTML = `<img style="width:3em;"src="${getHostFrontEnd()}vistas/assets/img/pdf.png" alt="Subtitulo">`;
    });
    document.querySelectorAll('.detalle-leccion').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            leccionSelected = findByLeccion(
                btn.getAttribute('idleccion')
            );

            if (leccionSelected != undefined) {

                $("#titleManagerLeccion").html('"' + leccionSelected.subtitulo.nombre + '"');
                $('#modalCargandoLeccion').modal('show');
            } else {
                showAlertTopEnd("info", "Vacío!", "No se encontró el leccion");
            }
        };
    });
    document.querySelectorAll('.descargar-archivo').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            $("#modalFrameLeccion").modal("show");
            document.querySelector("#descargarPdf").parentElement.setAttribute("href", getHostFrontEnd() + "adjuntos/archivos/" + tareaSelected.subTitulo.codigo.split('.')[0] + "/" + tareaSelected.subTitulo.codigo.split('.')[0] + "." + tareaSelected.subTitulo.codigo.split('.')[1] + "." + tareaSelected.subTitulo.codigo.split('.')[2] + "/PDF/" + tareaSelected.subTitulo.pdf);
            downloadURLLeccion(getHostFrontEnd() + "adjuntos/archivos/" + tareaSelected.subTitulo.codigo.split('.')[0] + "/" + tareaSelected.subTitulo.codigo.split('.')[0] + "." + tareaSelected.subTitulo.codigo.split('.')[1] + "." + tareaSelected.subTitulo.codigo.split('.')[2] + "/PDF/" + tareaSelected.subTitulo.pdf);

        };
    });


    document.querySelectorAll('.btn-regresar-subtitulo').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            document.querySelector('#sectionTareas').classList.remove("d-none");
            document.querySelector('#sectionTareasTitulo').classList.add("d-none");
            document.querySelector('#sectionLecciones').classList.add("d-none");
            document.querySelector('#sectionRespuestas').classList.add("d-none");
        };
    });

}

function findIndexLeccion(idbusqueda) {
    return beanPaginationLeccion.list.findIndex(
        (Leccion) => {
            if (Leccion.idleccion == parseInt(idbusqueda))
                return Leccion;


        }
    );
}

function findByLeccion(idleccion) {
    return beanPaginationLeccion.list.find(
        (Leccion) => {
            if (parseInt(idleccion) == Leccion.idleccion) {
                return Leccion;
            }


        }
    );
}

function downloadURLLeccion(url) {
    var hiddenIFrameID = 'hiddenDownloader',
        iframe = document.getElementById(hiddenIFrameID);
    if (iframe === null) {
        iframe = document.createElement('iframe');
        iframe.id = hiddenIFrameID;
        iframe.style.width = '100%';
        iframe.style.height = '100%';
        document.querySelector('#modalFrameContenidoLeccion').appendChild(iframe);
    }
    iframe.src = url;
    document.querySelector("#descargarPdf").parentElement.setAttribute("href", url);
};

function loadVideo(lista) {
    window.YT.ready(function () {
        player = new window.YT.Player("ytplayer", {
            height: '360',
            width: '640',
            host: 'http://www.youtube-nocookie.com',
            playerVars: {
                rel: 0,
                color: 'red',
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
        console.log(videoStatuses.find(status => status[1] === event.data)[0]);
    }
}
