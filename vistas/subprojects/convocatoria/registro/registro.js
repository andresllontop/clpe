var beanPaginationConvocatoria;
var convocatoriaSelected;
var beanRequestConvocatoria = new BeanRequest();
var listDetalleRespuesta = [];
document.addEventListener('DOMContentLoaded', function () {
    beanRequestConvocatoria.entity_api = 'convocatoria';
    beanRequestConvocatoria.operation = 'obtener';
    beanRequestConvocatoria.type_request = 'GET';
    let GETsearch = window.location.pathname;
    if (GETsearch.split("/").length == 5) {
        if (/^[0-9.]*$/.test(GETsearch.split("/")[4])) {
            convocatoriaSelected = { "idconvocatoria": GETsearch.split("/")[4] };
            processAjaxConvocatoria();
        } else {
            window.location.href = getHostFrontEnd();
        }
    } else {
        window.location.href = getHostFrontEnd();
    }


    $("#formularioRegistro").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();
        beanRequestConvocatoria.type_request = 'POST';
        beanRequestConvocatoria.operation = 'add';
        if (validarFormularioRespuesta()) {
            processAjaxConvocatoria();
        }

    });

});

function processAjaxConvocatoria() {
    circleCargando.containerOcultar = $(document.querySelector("#formularioRegistro"));
    circleCargando.container = $(document.querySelector("#formularioRegistro").parentElement);
    circleCargando.createLoader();
    circleCargando.toggleLoader("show");
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestConvocatoria.operation == 'add'
    ) {
        if (document.querySelector("#preguntaImagen1")) {
            let dataImagen = $("#preguntaImagen1").prop("files")[0];
            form_data.append("txtpreguntaImagen", dataImagen);
            listDetalleRespuesta.push(
                {
                    respuesta: "",
                    pregunta: document.querySelector("#preguntaImagen1").dataset.pregunta,
                    codigo: convocatoriaSelected.codigo,
                    tipo: 2
                }
            );
        }


        json = {
            lista: listDetalleRespuesta
        };

        form_data.append("class", JSON.stringify(json));
    } else {
        form_data = null;
        beanRequestConvocatoria.operation = 'obtener';
        beanRequestConvocatoria.type_request = 'GET';
        parameters_pagination = "?id=" + convocatoriaSelected.idconvocatoria;
    }

    $.ajax({
        url: getHostAPI() + beanRequestConvocatoria.entity_api + "/" + beanRequestConvocatoria.operation +
            parameters_pagination,
        type: beanRequestConvocatoria.type_request,
        data: form_data,
        cache: false,
        contentType: ((beanRequestConvocatoria.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json'
    }).done(function (beanCrudResponse) {
        circleCargando.toggleLoader("hide");
        listDetalleRespuesta.length = 0;
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                document.querySelector("#formularioRegistro").reset();
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
            } else {
                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }

        if (beanCrudResponse.beanPagination !== null) {
            beanPaginationConvocatoria = beanCrudResponse.beanPagination;
            listaConvocatoria(beanPaginationConvocatoria);
        }

    }).fail(function (jqXHR, textStatus, errorThrown) {

        circleCargando.toggleLoader("hide");
        showAlertErrorRequest();

    });

}

function listaConvocatoria(beanPagination) {
    document.querySelector('#formularioRegistro').innerHTML = '';
    let row = "", contador = 0, idTemporal = -1, contadorImagen = 0
    if (beanPagination.list.length == 0) {
        row += `
        <span class="input-group border-group text-center">
            NO HAY CUESTIONARIOS
        </span><!-- .input-group -->`;
        document.querySelector('#formularioRegistro').innerHTML = row;
        return;
    }
    convocatoriaSelected = beanPaginationConvocatoria.list[0].convocatoria;
    beanPagination.list.forEach((detalle) => {

        if (detalle.convocatoria.idconvocatoria != idTemporal) {
            idTemporal = detalle.convocatoria.idconvocatoria;
            row += `
            <div class="border-group mb-3 p-0">
                <img style="border-radius: 0.7em;" class="w-100" src="${getHostFrontEnd() + "adjuntos/convocatoria/" + detalle.convocatoria.imagen}"
                    alt="${detalle.convocatoria.imagen}">
            </div>
            <div class="border-group mb-3">${detalle.convocatoria.descripcion}
            </div>
            <span class="input-group border-group">
            <label for="pregunta${contador}">${detalle.descripcion}</label>`;
            if (parseInt(detalle.tipo) == 1) {
                contador++;
                row += `
                <input type="text" id="pregunta${contador}" class="lg" data-pregunta="${detalle.descripcion}" placeholder="Tu respuesta"
                    style="font-size: 17px; height: 49px;" />`;
            } else if (parseInt(detalle.tipo) == 2) {
                contadorImagen++;
                row += `
                <input id="preguntaImagen${contadorImagen}" type="file" data-pregunta="${detalle.descripcion}"
                    class="material-control tooltips-general input-check-user"
                    placeholder="Nombre de usuario" data-toggle="tooltip" data-placement="top" title=""
                    data-original-title="Selecciona una Imagen"
                    accept="image/png, image/jpeg, image/png">
                <small>Tamaño Máximo Permitido:1700 KB</small>
                <br>
                <small>Formatos Permitido:JPG, PNG, JPEG</small>
            `;
            }


            row += `
        </span><!-- .input-group -->
            `;
        } else {
            row += `
            <span class="input-group border-group">
            <label for="pregunta${contador}">${detalle.descripcion}</label>`;
            if (parseInt(detalle.tipo) == 1) {
                contador++;
                row += `
                <input type="text" id="pregunta${contador}" class="lg" data-pregunta="${detalle.descripcion}" placeholder="Tu respuesta"
                    style="font-size: 17px; height: 49px;" />`;
            } else if (parseInt(detalle.tipo) == 2) {
                contadorImagen++;
                row += `
                <input id="preguntaImagen${contadorImagen}" type="file"
                    class="material-control tooltips-general input-check-user" data-pregunta="${detalle.descripcion}"
                    placeholder="Nombre de usuario" data-toggle="tooltip" data-placement="top" title=""
                    data-original-title="Selecciona una Imagen"
                    accept="image/png, image/jpeg, image/png">
                <small>Tamaño Máximo Permitido:1700 KB</small>
                <br>
                <small>Formatos Permitido:JPG, PNG, JPEG</small>
            `;
            }


            row += `
        </span><!-- .input-group -->
            `;
        }


    });
    row += `
    <span class="input-group ">
                <button class="submit" data-loading-text="Enviando..." style=" height: 49px;">ENVIAR</button>
            </span><!-- .input-group -->
    `;

    document.querySelector('#formularioRegistro').innerHTML += row;


}

var validarFormularioRespuesta = () => {
    listDetalleRespuesta.length = 0;
    let total = beanPaginationConvocatoria.list.length;
    if (document.querySelector("#preguntaImagen1")) {
        total--;
        /*IMAGEN */
        if (document.querySelector("#preguntaImagen1").files.length == 0) {
            showAlertTopEnd("info", "Vacío", "ingrese Imagen");
            return false;
        }
        if (!(document.querySelector("#preguntaImagen1").files[0].type == "image/png" || document.querySelector("#preguntaImagen1").files[0].type == "image/jpg" || document.querySelector("#preguntaImagen1").files[0].type == "image/jpeg")) {
            showAlertTopEnd("info", "Formato Incorrecto", "Ingrese tipo de arhivo Imagen => png,jpg,jpeg");
            return false;
        }
        //menor a   1700 KB
        if (document.querySelector("#preguntaImagen1").files[0].size > (1700 * 1024)) {
            showAlertTopEnd("info", "Tamaño excedido", "el tamaño del archivo tiene que ser menor a 1700 KB");
            return false;
        }
    }

    for (let index = 1; index <= total; index++) {

        if (document.querySelector("#pregunta" + index).value == "") {
            swal({
                title: "Vacío",
                text: "Ingrese Respuesta",
                type: "info",
                timer: 1400,
                showConfirmButton: false
            });
            listDetalleRespuesta.length = 0;
            document.querySelector("#pregunta" + index).focus();
            return false;
        } else {
            listDetalleRespuesta.push(
                {
                    respuesta: document.querySelector("#pregunta" + index).value,
                    pregunta: document.querySelector("#pregunta" + index).dataset.pregunta,
                    codigo: convocatoriaSelected.codigo,
                    tipo: 1

                }
            );
        }

    }
    return true;
}

