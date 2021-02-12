var beanPaginationLibro;
var libroSelected;
var beanRequestLibro = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {

    beanRequestLibro.entity_api = 'libros';
    beanRequestLibro.operation = 'cuenta';
    beanRequestLibro.type_request = 'GET';

    $('#modalCargandoLibro').modal('show');

    $("#modalCargandoLibro").on('shown.bs.modal', function () {
        processAjaxLibro();
    });
    $("#ventanaModalManLibro").on('hide.bs.modal', function () {
        beanRequestLibro.type_request = 'GET';
        beanRequestLibro.operation = 'cuenta';
    });



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
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
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
        $('#modalCargandoLibro').modal("hide");
        showAlertErrorRequest();


    });

}

function listaLibro(beanPagination) {

    if (beanPagination.list.length == 0) {
        return;
    }
    let row = new Array();
    beanPagination.list.forEach((libro) => {
        libroSelected = libro
        libro.list.forEach(video => {
            row.push({
                sources: [
                    {
                        type: 'video/mp4',
                        src: getHostFrontEnd() + video
                    }
                ]

            });
        });

    });

    document.querySelectorAll('.dt-libro').forEach((img) => {
        img.setAttribute('src', getHostFrontEnd() + "adjuntos/libros/" + libroSelected.imagen);
    });
    flowplayer('.flowplayer', {
        ratio: 9 / 16,
        splash: true,
        playlist: row,
        poster: getHostFrontEnd() + "adjuntos/logoHeader.jpg"
    });
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

var validarDormularioLibro = () => {
    if (document.querySelector("#txtCodigoLibro").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Código",
            type: "warning",
            timer: 800,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtNombreLibro").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Nombre",
            type: "warning",
            timer: 800,
            showConfirmButton: false
        });
        return false;
    }

    if (beanRequestLibro.operation == 'add') {

        /*IMAGEN */
        if (document.querySelector("#txtImagenLibro").files.length == 0) {
            showAlertTopEnd("info", "Vacío", "ingrese Imagen");
            return false;
        }
        if (!(document.querySelector("#txtImagenLibro").files[0].type == "image/png" || document.querySelector("#txtImagenLibro").files[0].type == "image/jpg" || document.querySelector("#txtImagenLibro").files[0].type == "image/jpeg")) {
            showAlertTopEnd("info", "Formato Incorrecto", "Ingrese tipo de arhivo Imagen => png,jpg,jpeg");
            return false;
        }
        //menor a   1700 KB
        if (document.querySelector("#txtImagenLibro").files[0].size > (1700 * 1024)) {
            showAlertTopEnd("info", "Tamaño excedido", "el tamaño del archivo tiene que ser menor a 1700 KB");
            return false;
        }

        if (document.querySelector("#txtVideoLibro").files.length == 0) {
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
/************************
            ****** MasterSlider *****
            *************************/
// Calibrate slider's height
var sliderHeight = 390; // Smallest hieght allowed (default height)
if ($('#masterslider').data('height') == 'fullscreen') {
    var winHeight = $(window).height();
    sliderHeight = winHeight > sliderHeight ? winHeight : sliderHeight;
}

// Initialize the main slider
var slider = new MasterSlider();
slider.setup('masterslider', {
    space: 0,
    fullwidth: true,
    autoplay: true,
    overPause: false,
    width: 1024,
    height: sliderHeight
});
// adds Arrows navigation control to the slider.
slider.control('bullets', { autohide: false, dir: "h" });