var beanPaginationParrafo;
var parrafoSelected;
var beanRequestParrafo = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestParrafo.entity_api = 'parrafos';
    beanRequestParrafo.operation = 'paginate';
    beanRequestParrafo.type_request = 'GET';

    $('#sizePageParrafo').change(function () {
        beanRequestParrafo.type_request = 'GET';
        beanRequestParrafo.operation = 'paginate';
        $('#modalCargandoParrafo').modal('show');
    });


    $("#modalCargandoParrafo").on('shown.bs.modal', function () {
        processAjaxParrafo();
    });

    $("#modalCargandoParrafo").on('hide.bs.modal', function () {
        $(".progress-bar-parrafo").text("Cargando ... 0%");
        $(".progress-bar-parrafo").attr("aria-valuenow", "0");
        $(".progress-bar-parrafo").css("width", "0%");
    });


    $("#btnAbrirparrafo").click(function () {
        beanRequestParrafo.operation = 'add';
        beanRequestParrafo.type_request = 'POST';
        $("#imagePreview").html("");
        $("#tituloModalManParrafo").html("REGISTRAR PARRAFO");
        addParrafo();
        $("#ventanaModalManParrafo").modal("show");


    });

    $("#formularioParrafo").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (validarDormularioParrafo()) {
            $('#modalCargandoParrafo').modal('show');
        }
    });

    document.querySelector("#txtYoutubeParrafo").onkeyup = (e) => {
        if (!document.querySelector("#txtYoutubeParrafo").value.includes("https://youtu.be/")) {
            return;
        }
        setTimeout(() => {
            document.querySelector("#youtubePreview").innerHTML = '<iframe style="width:100%;height:100%;" src="https://www.youtube-nocookie.com/embed/' + e.target.value.substring("https://youtu.be/".length) + '" frameborder="0"  allowfullscreen></iframe>';
            document.querySelector("#txtYoutubeParrafo").dataset.valor = e.target.value.substring(("https://youtu.be/").length);
            document.querySelector("#youtubePreview").style.height = "320px";
        }, 1500);
    }

});

function processAjaxParrafo() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestParrafo.operation == 'update' ||
        beanRequestParrafo.operation == 'add'
    ) {

        json = {
            codigo: subtituloSelected.codigo + "." + document.querySelector("#txtCodigoParrafo").value,
            subTitulo: subtituloSelected.codigo,
            video: document.querySelector("#txtYoutubeParrafo").dataset.valor
        };


    } else {
        form_data = null;
    }

    switch (beanRequestParrafo.operation) {
        case 'delete':
            parameters_pagination = '?id=' + parrafoSelected.idvideoSubTitulo;
            break;
        case 'update':
            json.idvideoSubTitulo = parrafoSelected.idvideoSubTitulo;
            form_data.append("bean", JSON.stringify(json));
            break;
        case 'add':
            form_data.append("bean", JSON.stringify(json));
            break;

        default:
            parameters_pagination +=
                '?filtro=&subtitulo=' + subtituloSelected.codigo;
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pageParrafo").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePageParrafo").value.trim();
            break;

    }
    $.ajax({
        url: getHostAPI() + beanRequestParrafo.entity_api + "/" + beanRequestParrafo.operation +
            parameters_pagination,
        type: beanRequestParrafo.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestParrafo.operation == 'update' || beanRequestParrafo.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',

        xhr: function () {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    $('.progress-bar-parrafo').css({
                        width: Math.round(percentComplete * 100) + '%'
                    });
                    $(".progress-bar-parrafo").text(Math.round(percentComplete * 100) + '%');
                    $(".progress-bar-parrafo").attr("aria-valuenow", +Math.round(percentComplete * 100));
                    if (percentComplete === 1) {
                        // $('.progress-bar-parrafo').addClass('hide');
                        $('.progress-bar-parrafo').css({
                            width: + '100%'
                        });
                        $(".progress-bar-parrafo").text("Cargando ... 100%");
                        $(".progress-bar-parrafo").attr("aria-valuenow", "100");
                    }
                }
            }, false);
            xhr.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    $('.progress-bar-parrafo').css({
                        width: Math.round(percentComplete * 100) + '%'
                    });
                    $(".progress-bar-parrafo").text(Math.round(percentComplete * 100) + '%');
                    $(".progress-bar-parrafo").attr("aria-valuenow", +Math.round(percentComplete * 100));
                }
            }, false);
            return xhr;
        },
    }).done(function (beanCrudResponse) {
        $('#modalCargandoParrafo').modal('hide');

        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                document.querySelector("#pageParrafo").value = 1;
                document.querySelector("#sizePageParrafo").value = 20;
                $('#ventanaModalManParrafo').modal('hide');
            } else {
                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationParrafo = beanCrudResponse.beanPagination;
            listaParrafo(beanPaginationParrafo);
        }


    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoParrafo').modal("hide");
        showAlertErrorRequest();


    });

}

function addParrafo(parrafo = undefined) {
    //LIMPIAR LOS CAMPOS
    document.querySelector('#inputPrependParrafo').innerHTML = subtituloSelected.codigo + ".";
    document.querySelector('#txtCodigoParrafo').value = (parrafo == undefined) ? '' : parrafo.codigo.substring(subtituloSelected.codigo.length + 1);

    if (parrafo !== undefined) {
        document.querySelector("#youtubePreview").style.height = "320px";
        document.querySelector("#txtYoutubeParrafo").dataset.valor = parrafo.nombre;
        document.querySelector("#txtYoutubeParrafo").value = "https://youtu.be/" + parrafo.nombre;
        document.querySelector("#youtubePreview").innerHTML = '<iframe style="width:100%;height:100%;" src="https://www.youtube-nocookie.com/embed/' + parrafo.nombre + '" frameborder="0"  allowfullscreen></iframe>';

    } else {
        document.querySelector("#youtubePreview").style.height = "0px";
        $("#youtubePreview").html(
            ""
        );
        document.querySelector("#txtYoutubeParrafo").dataset.valor = "";
    }



}

function listaParrafo(beanPagination) {
    document.querySelector('#tbodyParrafo').innerHTML = '';
    document.querySelector('#titleManagerParrafo').innerHTML =
        ' LISTA DE VIDEOS';
    let row = "", header, contador = 1;
    header = `
    <button class="btn btn-danger btnregresarParrafo">
    <i class="zmdi zmdi-long-arrow-left mr-1"></i> Regresar
  </button>`;
    document.querySelector('#listaLibroCapitulos').innerHTML = header;
    if (beanPagination.list.length == 0) {
        addEventsButtonsParrafoRegreso();
        destroyPagination($('#paginationParrafo'));
        row += `<tr>
        <td class="text-center" colspan="6">NO HAY PARRAFOS</td>
        </tr>`;

        document.querySelector('#tbodyParrafo').innerHTML += row;
        return;
    }
    beanPagination.list.forEach((parrafo) => {

        row += `<tr  idvideoSubTitulo="${parrafo.idvideoSubTitulo}">
        <td class="text-center ver-subtitulo">${contador++}</td>
<td class="text-center">${parrafo.codigo}</td>
<td class="text-center ver-subtitulo">${subtituloSelected.nombre}</td>
<td class="text-center ">
<iframe style="width:320px;" src="https://www.youtube-nocookie.com/embed/${parrafo.nombre}" frameborder="0"  allowfullscreen></iframe>
</td>
<td class="text-center">
<button class="btn btn-info editar-parrafo" ><i class="zmdi zmdi-refresh"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-parrafo"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;

        // $('[data-toggle="tooltip"]').tooltip();
    });


    document.querySelector('#tbodyParrafo').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePageParrafo").value),
        document.querySelector("#pageParrafo"),
        $('#modalCargandoParrafo'),
        $('#paginationParrafo'));
    addEventsButtonsParrafo();


}

function addEventsButtonsParrafo() {


    document.querySelectorAll('.editar-parrafo').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            parrafoSelected = findByParrafo(
                btn.parentElement.parentElement.getAttribute('idvideoSubTitulo')
            );

            if (parrafoSelected != undefined) {
                addParrafo(parrafoSelected);
                $("#tituloModalManParrafo").html("EDITAR VIDEO DE PARRAFOS");
                $("#ventanaModalManParrafo").modal("show");
                beanRequestParrafo.type_request = 'POST';
                beanRequestParrafo.operation = 'update';
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
    document.querySelectorAll('.eliminar-parrafo').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            parrafoSelected = findByParrafo(
                btn.parentElement.parentElement.getAttribute('idvideoSubTitulo')
            );

            if (parrafoSelected != undefined) {
                beanRequestParrafo.type_request = 'GET';
                beanRequestParrafo.operation = 'delete';
                $('#modalCargandoParrafo').modal('show');
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
    addEventsButtonsParrafoRegreso();
}

function addEventsButtonsParrafoRegreso() {


    document.querySelectorAll('.btnregresarParrafo').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            document.querySelector('#listaLibroCapitulos').innerHTML = `
            <button class="btn btn-danger btnregresarSubtitulo">
            <i class="zmdi zmdi-long-arrow-left mr-1"></i> Regresar
          </button>`;;
            document.querySelector("#seccion-subtitulo").classList.remove("d-none");
            document.querySelector("#seccion-parrafo").classList.add("d-none");
            addEventsButtonsSubtituloRegreso();


        };
    });

}


function findIndexParrafo(idbusqueda) {
    return beanPaginationParrafo.list.findIndex(
        (Parrafo) => {
            if (Parrafo.idvideoSubTitulo == parseInt(idbusqueda))
                return Parrafo;


        }
    );
}

function findByParrafo(idvideoSubTitulo) {
    return beanPaginationParrafo.list.find(
        (Parrafo) => {
            if (parseInt(idvideoSubTitulo) == Parrafo.idvideoSubTitulo) {
                return Parrafo;
            }


        }
    );
}

var validarDormularioParrafo = () => {

    if (document.querySelector("#txtCodigoParrafo").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Codigo",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtYoutubeParrafo").dataset.valor == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Enlace de Youtube",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }

    return true;
}