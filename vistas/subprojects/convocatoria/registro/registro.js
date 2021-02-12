var beanPaginationConvocatoria;
var convocatoriaSelected;
var beanRequestConvocatoria = new BeanRequest();
var listDetalleRespuesta = [];
document.addEventListener('DOMContentLoaded', function () {
    beanRequestConvocatoria.entity_api = 'convocatoria';
    beanRequestConvocatoria.operation = 'obtener';
    beanRequestConvocatoria.type_request = 'GET';
    processAjaxConvocatoria();

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

        json = {
            lista: listDetalleRespuesta
        };

        form_data.append("class", JSON.stringify(json));
    } else {
        form_data = null;
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
        listDetalleRespuesta = [];
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
    let row = "", contador = 0, idTemporal = -1;
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
        contador++;
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
            <label for="pregunta${contador}">${detalle.descripcion}</label>
            <input type="text" id="pregunta${contador}" class="lg" data-pregunta="${detalle.descripcion}" placeholder="Tu respuesta"
                style="font-size: 17px; height: 49px;" />
        </span><!-- .input-group -->
            `;
        } else {
            row += `
            <span class="input-group border-group">
            <label for="pregunta${contador}">${detalle.descripcion}</label>
            <input type="text" id="pregunta${contador}" data-pregunta="${detalle.descripcion}" class="lg" placeholder="Tu respuesta"
                style="font-size: 17px; height: 49px;" />
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

    for (let index = 1; index <= beanPaginationConvocatoria.list.length; index++) {
        if (document.querySelector("#pregunta" + index).value == "") {
            swal({
                title: "Vacío",
                text: "Ingrese Respuesta",
                type: "info",
                timer: 1400,
                showConfirmButton: false
            });
            listDetalleRespuesta = [];
            return false;
        } else {
            listDetalleRespuesta.push(
                {
                    respuesta: document.querySelector("#pregunta" + index).value,
                    pregunta: document.querySelector("#pregunta" + index).dataset.pregunta,
                    codigo: convocatoriaSelected.codigo

                }
            );
        }

    }




    return true;
}

