var beanPaginationRespuesta;
var beanPaginationPregunta;
var respuestaSelected;
var beanRequestRespuesta = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestRespuesta.entity_api = 'respuestas';
    beanRequestRespuesta.operation = 'obtener';
    beanRequestRespuesta.type_request = 'GET';
    $('#sizePageRespuesta').change(function () {
        beanRequestRespuesta.operation = 'obtener';
        beanRequestRespuesta.type_request = 'GET';
        $('#modalCargandoRespuesta').modal('show');
    });

    $("#modalCargandoRespuesta").on('shown.bs.modal', function () {
        processAjaxRespuesta();
    });
    $("#ventanaModalManRespuesta").on('hide.bs.modal', function () {
        beanRequestRespuesta.type_request = 'GET';
        beanRequestRespuesta.operation = 'obtener';
    });


});

function processAjaxRespuesta() {

    let form_data = new FormData();

    let parameters_pagination = '';

    if (
        beanRequestRespuesta.operation == 'updateestado'
    ) {
        json = {
            estado: 1
        };
    } else {
        form_data = null;
    }

    switch (beanRequestRespuesta.operation) {
        case 'delete':
            parameters_pagination = '?id=' + respuestaSelected.idrespuesta;
            break;
        case 'updateestado':
            json.idrespuesta = respuestaSelected.idrespuesta
            json.cuenta = respuestaSelected.estado.idtarea;
            form_data.append("class", JSON.stringify(json));
            break;


        default:

            parameters_pagination +=
                '?cuenta=' + clienteSelected.cuenta.cuentaCodigo;
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pageRespuesta").value.trim();
            parameters_pagination +=
                '&tipo=' + respuestaSelected.tipo;
            parameters_pagination +=
                '&codigo=' + (respuestaSelected.titulo == undefined ? "" : respuestaSelected.titulo.codigo);
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePageRespuesta").value.trim();
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestRespuesta.entity_api + "/" + beanRequestRespuesta.operation +
            parameters_pagination,
        type: beanRequestRespuesta.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestRespuesta.operation == 'updateestado') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json'
    }).done(function (beanCrudResponse) {
        console.log("holi " + respuestaSelected.tipo);
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
            if (beanRequestRespuesta.operation == "paginate") {
                beanPaginationPregunta = beanCrudResponse.beanPagination;
                listaPreguntasYRespuesta(beanPaginationPregunta);
            } else {
                beanPaginationRespuesta = beanCrudResponse.beanPagination;
                if (respuestaSelected.tipo == 1) {
                    listaRespuesta(beanPaginationRespuesta);
                } else {
                    listaRespuestaInternos(beanPaginationRespuesta);
                }


            }

        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoRespuesta').modal("hide");
        showAlertErrorRequest();

    });

}

function listaRespuestaInternos(beanPagination) {
    document.querySelector('#tbodyRespuesta').innerHTML = '';

    document.querySelector('#titleAlumnoLeccionRespuesta').innerHTML =
        clienteSelected.nombre + " " + clienteSelected.apellido;
    let row = ``;
    document.querySelector('#btnClientePreguntas').innerHTML = `
    <button class="btn btn-danger btn-regresar-respuesta w-100">
    <i class="zmdi zmdi-long-arrow-left mr-1"></i> Regresar
  </button>`;

    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationRespuesta'));
        row += `<tr>
        <td class="text-center" colspan="7">NO HAY CUESTIONARIOS REALIZADOS</td>
        </tr>`;
        document.querySelector('#tbodyRespuesta').innerHTML = row;
        addEventsButtonsRespuesta();
        return;
    }

    beanPagination.list.forEach((respuesta) => {
        row += `<tr idrespuesta="${respuesta.idrespuesta}">
<td class="text-center">${respuesta.test.nombre}</td>
<td class="text-center">${respuesta.titulo.nombre}</td>
<td class="text-center" style="width:6em;"> <p style="font-size: 20px;
border: 2px solid #7030a0;"> ${respuesta.test.cantidadpreguntas}</p></td>
<td class="text-center">
<button class="btn btn-danger descargar-respuesta"><i class="zmdi zmdi-download"></i> </button>
</td>
<td class="text-center">
<button class="btn ${respuesta.estado.estadotarea == 1 ? "btn-success" : "btn-warning"} editar-estado-respuesta" ${respuesta.estado.estadotarea == 1 ? "disabled" : ""} ><i class="zmdi ${respuesta.estado.estadotarea == 1 ? "zmdi-check-all" : "zmdi-close"}"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-info ver-respuesta" ><i class="zmdi zmdi-eye"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-respuesta w-100"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;
    });

    document.querySelector('#tbodyRespuesta').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        6,
        document.querySelector("#pageRespuesta"),
        $('#modalCargandoRespuesta'),
        $('#paginationRespuesta'));
    addEventsButtonsRespuesta(1);


}
function listaRespuesta(beanPagination) {
    document.querySelector('#tbodyRespuesta').innerHTML = '';

    document.querySelector('#titleAlumnoLeccionRespuesta').innerHTML =
        clienteSelected.nombre + " " + clienteSelected.apellido;
    let row = ``;
    document.querySelector('#btnClientePreguntas').innerHTML = `
    <button class="btn btn-danger btn-regresar-respuesta w-100">
    <i class="zmdi zmdi-long-arrow-left mr-1"></i> Regresar
  </button>`;

    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationRespuesta'));
        row += `<tr>
        <td class="text-center" colspan="7">NO HAY CUESTIONARIOS REALIZADOS</td>
        </tr>`;
        document.querySelector('#tbodyRespuesta').innerHTML = row;
        addEventsButtonsRespuesta();
        return;
    }

    beanPagination.list.forEach((respuesta) => {
        row += `<tr idrespuesta="${respuesta.idrespuesta}">
<td class="text-center">${respuesta.titulo.codigo}</td>
<td class="text-center">${respuesta.titulo.nombre}</td>
<td class="text-center" style="width:6em;"> <p style="font-size: 20px;
border: 2px solid #7030a0;"> ${respuesta.test.cantidadpreguntas}</p></td>
<td class="text-center">
<button class="btn btn-danger descargar-respuesta"><i class="zmdi zmdi-download"></i> </button>
</td>
<td class="text-center">
<button class="btn ${respuesta.estado.estadotarea == 1 ? "btn-success" : "btn-warning"} editar-estado-respuesta" ${respuesta.estado.estadotarea == 1 ? "disabled" : ""} ><i class="zmdi ${respuesta.estado.estadotarea == 1 ? "zmdi-check-all" : "zmdi-close"}"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-info ver-respuesta" ><i class="zmdi zmdi-eye"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-respuesta w-100"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;
    });

    document.querySelector('#tbodyRespuesta').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePageRespuesta").value),
        document.querySelector("#pageRespuesta"),
        $('#modalCargandoRespuesta'),
        $('#paginationRespuesta'));
    addEventsButtonsRespuesta(2);


}

function listaPreguntasYRespuesta(beanPagination) {
    document.querySelector('#tbodyPreguntas').innerHTML = '';

    //   document.querySelector('#descripcionTest').innerHTML = '<i class="fa zmdi zmdi-quote"></i>' + "CAPÍTULO : " + tareaSelected.subTitulo.titulo.nombre;

    let row = "", contador = 0, dato = "";
    if (beanPagination.list.length == 0) {
        row += `NO HAY PREGUNTAS`;
        document.querySelector('#tbodyPreguntas').innerHTML = row;
        return;
    }
    beanPagination.list.forEach((detalletest) => {
        if (detalletest.respuesta.tipo == 1) {
            document.querySelector('#titleManPreguntas').innerHTML =
                'VISUALIZACIÓN DE RESPUESTAS DE REFORSAMIENTO <h3 class="modal-title text-center f-weight-600" > Capítulo: ' + respuestaSelected.titulo.nombre + '</h3>';
        } else {
            document.querySelector('#titleManPreguntas').innerHTML =
                'VISUALIZACIÓN DE RESPUESTAS DE PREGUNTAS INTERNAS  <h3 class="modal-title text-center f-weight-600" >' + respuestaSelected.test.nombre + '</h3>';
        }
        contador++;
        if ((contador + "").length == 1) {
            contador = "0" + contador;
        }
        if (dato == "") {
            dato = detalletest.subtitulo.codigo;

            row += `
            <div class="col-lg-12 my-2">
            <h4 style="font-weight: bold;" class="text-primary">
            Subtítulo : <small style="font-size: 19px;font-weight: bold;">${detalletest.subtitulo.nombre}</small>
            </h4>
                <label class="text-danger" style="font-weight: bold;">PREGUNTA N° ${contador + ": " + detalletest.test.descripcion}</label>
                <textarea  class="form-control" 
                    >${detalletest.descripcion}</textarea>
            </div>
                `;
        } else {
            if (dato == detalletest.subtitulo.codigo) {
                row += `
                <div class="col-lg-12 my-2">
                    <label class="text-danger" style="font-weight: bold;">PREGUNTA N° ${contador + ": " + detalletest.test.descripcion}</label>
                    <textarea  class="form-control" >${detalletest.descripcion}</textarea>
                </div>
                    `;
            } else {
                dato = detalletest.subtitulo.codigo;

                row += `
                <div class="col-lg-12 my-2" >
                    <h4 style="font-weight: bold;" class="text-primary">
                     Subtítulo : <small style="font-size: 19px;font-weight: bold;">${detalletest.subtitulo.nombre}</small>
                    </h4>
                    <label class="text-danger" style="font-weight: bold;">PREGUNTA N° ${contador + ": " + detalletest.test.descripcion}</label>
                    <textarea  class="form-control" >${detalletest.descripcion}</textarea>
                </div>
                    `;
            }
        }

    });


    document.querySelector('#tbodyPreguntas').innerHTML += row;

    document.querySelectorAll('.btn-regresar-pregunta').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            document.querySelector("#seccion-cliente").classList.add("d-none");
            document.querySelector("#seccion-leccion").classList.add("d-none");
            document.querySelector("#seccion-respuesta").classList.remove("d-none");
        };
    });


}

function addEventsButtonsRespuesta(lugar = undefined) {
    document.querySelectorAll('.ver-respuesta').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {

            respuestaSelected = findByRespuesta(
                btn.parentElement.parentElement.getAttribute('idrespuesta')
            );
            if (respuestaSelected != undefined) {
                beanRequestRespuesta.operation = 'paginate';
                beanRequestRespuesta.type_request = 'GET';

                $('#modalCargandoRespuesta').modal('show');
                $('#ventanaModalManPreguntas').modal('show');
            } else {
                showAlertTopEnd("info", "Vacío!", "No se encontró las respuesta");
            }
        };
    });
    document.querySelectorAll('.eliminar-respuesta').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {

            respuestaSelected = findByRespuesta(
                btn.parentElement.parentElement.getAttribute('idrespuesta')
            );

            if (respuestaSelected != undefined) {
                beanRequestRespuesta.operation = 'delete';
                beanRequestRespuesta.type_request = 'GET';
                showAlertDelete('modalCargandoRespuesta');
            } else {
                showAlertTopEnd("info", "Vacío!", "No se encontró las respuesta");
            }

        };
    });
    document.querySelectorAll('.editar-estado-respuesta').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            respuestaSelected = findByRespuesta(
                btn.parentElement.parentElement.getAttribute('idrespuesta')
            );
            if (respuestaSelected != undefined) {
                if (lugar == 1) {
                    document.querySelector("#totalInterno").innerHTML = parseInt(document.querySelector("#totalInterno").innerText) - 1;
                } else {
                    document.querySelector("#totalReforsamiento").innerHTML = parseInt(document.querySelector("#totalReforsamiento").innerText) - 1;
                }

                beanRequestRespuesta.operation = 'updateestado';
                beanRequestRespuesta.type_request = 'POST';
                $('#modalCargandoRespuesta').modal('show');
            } else {
                showAlertTopEnd("info", "Vacío!", "No se encontró las respuesta");
            }
        };
    });
    document.querySelectorAll('.descargar-respuesta').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            respuestaSelected = findByRespuesta(
                btn.parentElement.parentElement.getAttribute('idrespuesta')
            );
            if (respuestaSelected != undefined) {
                $("#modalFrameRespuesta").modal("show");

                downloadURLRespuesta(getHostFrontEnd() + "api/alumno/reporte/respuestas?token=" + Cookies.get("clpe_token") + "&tipo=" + respuestaSelected.tipo + "&codigo=" + (respuestaSelected.titulo.codigo) + "&cuenta=" + clienteSelected.cuenta.cuentaCodigo);


            } else {
                showAlertTopEnd("info", "Vacío!", "No se encontró las respuesta");
            }

        };
    });

    addEventsButtonsRespuestaRegreso();
}

function addEventsButtonsRespuestaRegreso() {
    document.querySelectorAll('.btn-regresar-respuesta').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            document.querySelector('#btnClientePreguntas').innerHTML = "";
            document.querySelector("#seccion-cliente").classList.add("d-none");
            document.querySelector("#seccion-leccion").classList.remove("d-none");
            document.querySelector("#seccion-respuesta").classList.add("d-none");
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

function downloadURLRespuesta(url) {
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
    document.querySelector("#descargarPdfRespuesta").parentElement.setAttribute("href", url);
};

