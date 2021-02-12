var beanPaginationRespuesta;
var respuestaSelected;
var beanRequestRespuesta = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestRespuesta.entity_api = 'respuestas';
    beanRequestRespuesta.operation = 'paginate';
    beanRequestRespuesta.type_request = 'GET';

    $("#modalCargandoRespuesta").on('shown.bs.modal', function () {
        processAjaxRespuesta();
    });
    $("#ventanaModalManRespuesta").on('hide.bs.modal', function () {
        beanRequestRespuesta.type_request = 'GET';
        beanRequestRespuesta.operation = 'paginate';
    });


});

function processAjaxRespuesta() {

    let parameters_pagination = '';
    switch (beanRequestRespuesta.operation) {
        default:

            parameters_pagination +=
                '?codigo=' + (tareaSelected.tipo == 1 ? tareaSelected.subTitulo.titulo.codigo : tareaSelected.subTitulo.codigo);
            parameters_pagination +=
                '&pagina=1';
            parameters_pagination +=
                '&tipo=' + tareaSelected.tipo;
            parameters_pagination +=
                '&registros=30';
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestRespuesta.entity_api + "/" + beanRequestRespuesta.operation +
            parameters_pagination,
        type: beanRequestRespuesta.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: null,
        cache: false,
        contentType: 'application/json; charset=UTF-8',
        processData: false,
        dataType: 'json'
    }).done(function (beanCrudResponse) {
        $('#modalCargandoRespuesta').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                document.querySelector("#pageRespuesta").value = 1;
                $('#ventanaModalManRespuesta').modal('hide');
            } else {
                showAlertTopEnd("info", beanCrudResponse.messageServer, "");
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationRespuesta = beanCrudResponse.beanPagination;
            listaRespuesta(beanPaginationRespuesta);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoRespuesta').modal("hide");
        showAlertErrorRequest();

    });

}

function listaRespuesta(beanPagination) {
    document.querySelector('#tbodyRespuesta').innerHTML = '';

    document.querySelector('#descripcionTest').innerHTML = '<i class="zmdi zmdi-quote"></i>' + "CAPÍTULO : " + tareaSelected.subTitulo.titulo.nombre;
    let row = "", contador = 0, dato = "", contadorRespuesta = 0;
    if (beanPagination.list.length == 0) {
        row += `  <h5 class="text-center">NO CUENTAS CON LECCIONES REALIZADAS</h5>
        <div class="text-center">  <a class="btn btn-bordered anim fadeInRight animated" role="button" href="index"
        style="visibility: visible;">INICIO</a></div>
       `;

        document.querySelector('#tbodyRespuesta').innerHTML = row;
        return;
    }
    document.querySelector('#sectionLecciones').classList.add("d-none");
    document.querySelector('#sectionRespuestas').classList.remove("d-none");
    document.querySelector('#sectionTareas').classList.add("d-none");
    beanPagination.list.forEach((detalletest) => {
        contadorRespuesta++;
        if ((contadorRespuesta + "").length == 1) {
            contadorRespuesta = "0" + contadorRespuesta;
        }
        if (detalletest.respuesta.tipo == 1) {
            document.querySelector('#titleManRespuesta').innerHTML =
                'PREGUNTAS DE CAPíTULO';
        } else {
            document.querySelector('#titleManRespuesta').innerHTML =
                ' PREGUNTAS DE REFORZAMIENTO';
        }
        contador++;
        if (dato == "") {
            dato = detalletest.subtitulo.codigo;
            row += `
            <h4 class="anim fadeIn text-primary my-1" data-wow-delay="0.24s">
            <small style="font-size: 18px;font-weight: 500;"> Subtítulo :  ${detalletest.subtitulo.nombre}</small>
         </h4>
            `;
            row += `
            <span class="input-group anim fadeIn" data-wow-delay="0.30s">
            <h5 style="font-weight: 500;">${"PREGUNTA N°" + contadorRespuesta + ": " + detalletest.test.descripcion}</h5>
                <textarea  class="lg" 
                    placeholder="Escribe ... " style="height: 100px;font-size: 16px;">${detalletest.descripcion}</textarea>
            </span>
                `;
        } else {
            if (dato == detalletest.subtitulo.codigo) {
                row += `
                <span class="input-group anim fadeIn" data-wow-delay="0.30s">
                <h5 style="font-weight: 500;">${"PREGUNTA N°" + contadorRespuesta + ": " + detalletest.test.descripcion}</h5>
                    <textarea  class="lg" 
                        placeholder="Escribe ... " style="height: 100px;font-size: 16px;">${detalletest.descripcion}</textarea>
                </span>
                    `;
            } else {
                dato = detalletest.subtitulo.codigo;

                row += `
            <h4 class="anim fadeIn text-primary my-1" data-wow-delay="0.24s"><small style="font-size: 18px;font-weight: 500;">Subtítulo : ${detalletest.subtitulo.nombre}</small>
            </h4>
                <span class="input-group anim fadeIn" data-wow-delay="0.30s">
                <h5 style="font-weight: 500;">${"PREGUNTA N°" + contadorRespuesta + ": " + detalletest.test.descripcion}</h5>
                    <textarea  class="lg" 
                        placeholder="Escribe ... " style="height: 100px;font-size: 16px;">${detalletest.descripcion}</textarea>
                </span>
                    `;
            }

        }

    });


    document.querySelector('#tbodyRespuesta').innerHTML += row;

    addEventsButtonsRespuesta();


}

function addEventsButtonsRespuesta() {
    document.querySelectorAll('.btn-regresar-subtitulo').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            document.querySelector('#sectionTareas').classList.remove("d-none");
            document.querySelector('#sectionTareasTitulo').classList.add("d-none");
            document.querySelector('#sectionLecciones').classList.add("d-none");
            document.querySelector('#sectionRespuestas').classList.add("d-none");

        };
    });

    document.querySelectorAll('.descargar-respuestas').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            $("#modalFrameLeccion").modal("show");
            downloadURL(getHostFrontEnd() + "api/alumno/reporte/respuestas?token=" + Cookies.get("clpe_token") + "&tipo=" + tareaSelected.tipo + "&codigo=" + (tareaSelected.tipo == 1 ? tareaSelected.subTitulo.titulo.codigo : tareaSelected.subTitulo.codigo));

        };
    });
}

function findIndexRespuesta(idbusqueda) {
    return beanPaginationRespuesta.list.findIndex(
        (Respuesta) => {
            if (Respuesta.idrespuesta == parseInt(idbusqueda))
                return Respuesta;


        }
    );
}

function findByRespuesta(idrespuesta) {
    return beanPaginationRespuesta.list.find(
        (Respuesta) => {
            if (parseInt(idrespuesta) == Respuesta.idrespuesta) {
                return Respuesta;
            }


        }
    );
}
function downloadURL(url) {
    var hiddenIFrameID = 'hiddenDownloader',
        iframe = document.getElementById(hiddenIFrameID);
    if (iframe === null) {
        iframe = document.createElement('iframe');
        iframe.id = hiddenIFrameID;
        iframe.style.width = '100%';
        iframe.style.height = '100%';
        document.querySelector('#modalFrameContenidoRespuesta').appendChild(iframe);
    }
    iframe.src = url;
};
