var beanPaginationLeccion;
var leccionSelected;
var beanRequestLeccion = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestLeccion.entity_api = 'lecciones';
    beanRequestLeccion.operation = 'paginate';
    beanRequestLeccion.type_request = 'GET';

    $('#sizePageLeccion').change(function () {
        beanRequestLeccion.type_request = 'GET';
        beanRequestLeccion.operation = 'paginate';
        $('#modalCargandoLeccion').modal('show');
    });


    $("#modalCargandoLeccion").on('shown.bs.modal', function () {
        processAjaxLeccion();
    });

    $("#ventanaModalManLeccion").on('hide.bs.modal', function () {
        beanRequestLeccion.type_request = 'GET';
        beanRequestLeccion.operation = 'paginate';
    });

    $("#btnAbrirLeccion").click(function () {
        beanRequestLeccion.operation = 'add';
        beanRequestLeccion.type_request = 'POST';
        $("#tituloModalManLeccion").html("REGISTRAR CAPÍTULO");
        addLeccion();
        $("#ventanaModalManLeccion").modal("show");


    });

    $("#formularioLeccion").submit(function (event) {
        if (validarDormularioLeccion()) {
            $('#modalCargandoLeccion').modal('show');
        }
        event.preventDefault();
        event.stopPropagation();
    });

});

function processAjaxLeccion() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestLeccion.operation == 'update' ||
        beanRequestLeccion.operation == 'add'
    ) {

        json = {
            estado: 1
        };


    } else {
        form_data = null;
    }

    switch (beanRequestLeccion.operation) {
        case 'delete':
            parameters_pagination = '?id=' + leccionSelected.idleccion;
            break;
        case 'deletevideo':
            parameters_pagination = '?id=' + leccionSelected.idleccion;
            break;
        case 'update':
            json.idleccion = leccionSelected.idleccion;
            json.cuenta = leccionSelected.estado.idtarea;
            form_data.append("class", JSON.stringify(json));
            break;
        default:

            parameters_pagination +=
                '?filtro=&cuenta=' + clienteSelected.cuenta.cuentaCodigo;
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pageLeccion").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePageLeccion").value.trim();
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestLeccion.entity_api + "/" + beanRequestLeccion.operation +
            parameters_pagination,
        type: beanRequestLeccion.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },

        data: form_data,
        cache: false,
        contentType: ((beanRequestLeccion.operation == 'update' || beanRequestLeccion.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json'
    }).done(function (beanCrudResponse) {
        $('#modalCargandoLeccion').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                if (beanRequestLeccion.operation == 'update') {
                    leccionSelected.estado.estadotarea = json.estado;
                    updatelistLeccion(leccionSelected);
                    listaLeccion(beanPaginationLeccion);
                } else if (beanRequestLeccion.operation == 'delete') {

                    eliminarlistLeccion(leccionSelected.idleccion);
                    listaLeccion(beanPaginationLeccion);
                } else if (beanRequestLeccion.operation = 'deletevideo') {
                    leccionSelected.video = null;
                    updatelistLeccion(leccionSelected);
                    listaLeccion(beanPaginationLeccion);
                } else {
                    document.querySelector("#pageLeccion").value = 1;
                    document.querySelector("#sizePageLeccion").value = 20;
                }
                beanRequestLeccion.operation = 'paginate';
                beanRequestLeccion.type_request = 'GET';
                // document.querySelector('#sizePageLeccion').dispatchEvent(new Event('change'));
            } else {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
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

function addLeccion(leccion = undefined) {
    //LIMPIAR LOS CAMPOS

    document.querySelector('#txtNombreLeccion').value = (leccion == undefined) ? '' : leccion.comentario;
    document.querySelector('#txtFechaLeccion').value = (leccion == undefined) ? '' : leccion.fecha.split(" ")[0] + "   /   " + leccion.fecha.split(" ")[1];
    document.querySelector('#txtCodigoLeccion').value = (leccion == undefined) ? '' : leccion.subTitulo.codigo;

    if (leccion !== undefined) {
        $("#videoPreview").html(
            `<video width='100%' alt='user-picture' class='img-responsive center-box' controls ><source src='${getHostFrontEnd()}adjuntos/video-usuarios/${leccion.video}' type='video/mp4'></video>`
        );

    } else {
        $("#videoPreview").html(
            ""
        );


    }


}

function listaLeccion(beanPagination) {
    document.querySelector('#tbodyLeccion').innerHTML = '';
    document.querySelector('#titleManagerLeccion').innerHTML =
        ' TAREA ALUMNO';
    document.querySelector('#titleAlumnoLeccion').innerHTML =
        clienteSelected.nombre + " " + clienteSelected.apellido;

    let row = "", contador = 1;
    document.querySelector('#listaClienteComentarios').innerHTML = ` 
    <button class="btn btn-danger btn-regresar-leccion w-100">
    <i class="zmdi zmdi-long-arrow-left mr-1"></i> Regresar
  </button>`;

    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationLeccion'));
        row += `<tr>
        <td class="text-center" colspan="11">NO HAY LECCIONES</td>
        </tr>`;

        document.querySelector('#tbodyLeccion').innerHTML += row;
        addEventsButtonsLeccion();
        return;
    }
    beanPagination.list.forEach((leccion) => {
        if (leccion.reforsamiento != undefined) {
            document.querySelector("#totalReforsamiento").innerHTML = leccion.reforsamiento;
        } else if (leccion.interno != undefined) {
            document.querySelector("#totalInterno").innerHTML = leccion.interno;
        } else {

            row += `<tr idleccion="${leccion.idleccion}">
<td class="text-left">${contador++}</td>
<td class="text-left">${leccion.subTitulo.codigo}</td>
<td class="text-left">${leccion.subTitulo.nombre}</td>
<td class="text-center">
<p class="pr-1" style="max-height: 6.3em;overflow: auto;">${(leccion.comentario).substring(0, 10)}(...)</p>
</td>
<td class="text-center">
<button class="btn btn-secondary descargar-comentario" type="button"><i class="zmdi zmdi-download"></i></button>
</td>
`;
            if (leccion.video == null) {
                row += `
    <td class="text-center" colspan="2">
    <button class="btn btn-primary" type="button" >Video Descargado</button>
    </td>
   
    `;
            } else {
                row += `
                <td class="text-center">
    <video width="150px" alt="${leccion.video}" class="center-box" controls="" data-video="0"><source class="imag" src="${getHostFrontEnd()}adjuntos/video-usuarios/${leccion.video}" type="video/mp4"></video></td>
    <td class="text-center">
    <p class="my-0"><a href="${getHostFrontEnd()}adjuntos/video-usuarios/${leccion.video}" class="btn btn-secondary descargar-video aula-cursor-mano" download><i class="zmdi zmdi-download text-white"></i></a></p>
    <p class="my-1"><button class="btn btn-danger eliminar-video aula-cursor-mano" type="button" ><i class="zmdi zmdi-delete text-white"></i></button></p>
    </td>`;
            }

            row += `

<td class="text-center">${(leccion.fecha.split(" ")[0]).split("-")[2] + "-" + (leccion.fecha.split(" ")[0]).split("-")[1] + "-" + (leccion.fecha.split(" ")[0]).split("-")[0]} <p>${leccion.fecha.split(" ")[1]} </p></td>
<td class="text-center">
<button class="btn ${leccion.estado.estadotarea == 1 ? "btn-success" : "btn-warning"} editar-estado-leccion" ${leccion.estado.estadotarea == 1 ? "disabled" : ""} ><i class="zmdi ${leccion.estado.estadotarea == 1 ? "zmdi-check-all" : "zmdi-close"}"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-info editar-leccion" type="button" ><i class="zmdi zmdi-eye"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-leccion" type="button" ><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;
        }
        // $('[data-toggle="tooltip"]').tooltip();
    });


    document.querySelector('#tbodyLeccion').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePageLeccion").value),
        document.querySelector("#pageLeccion"),
        $('#modalCargandoLeccion'),
        $('#paginationLeccion'));
    addEventsButtonsLeccion();


}

function addEventsButtonsLeccion() {
    document.querySelectorAll('.editar-leccion').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            leccionSelected = findByLeccion(
                btn.parentElement.parentElement.getAttribute('idleccion')
            );

            if (leccionSelected != undefined) {


                addLeccion(leccionSelected);
                $("#tituloModalManLeccion").html("VISUALIZAR TAREA");
                $("#ventanaModalManLeccion").modal("show");

            } else {
                swal(
                    "No se encontró la lección",
                    "",
                    "info"
                );
            }
        };
    });
    document.querySelectorAll('.editar-estado-leccion').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            leccionSelected = findByLeccion(
                btn.parentElement.parentElement.getAttribute('idleccion')
            );

            if (leccionSelected != undefined) {
                document.querySelectorAll("#tbodyCliente > tr").forEach((btn) => {
                    if (parseInt(clienteSelected.idcliente) == parseInt(btn.getAttribute("idcliente"))) {
                        btn.children[6].firstElementChild.innerText = parseInt(btn.children[6].firstElementChild.innerText) - 1;
                    }

                });
                beanRequestLeccion.type_request = 'POST';
                beanRequestLeccion.operation = 'update';
                $('#modalCargandoLeccion').modal('show');
            } else {
                swal(
                    "No se encontró la lección",
                    "",
                    "info"
                );
            }
        };
    });
    document.querySelectorAll('.eliminar-leccion').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            leccionSelected = findByLeccion(
                btn.parentElement.parentElement.getAttribute('idleccion')
            );

            if (leccionSelected != undefined) {
                beanRequestLeccion.type_request = 'GET';
                beanRequestLeccion.operation = 'delete';
                showAlertDelete('modalCargandoLeccion');
                // $('#modalCargandoLeccion').modal('show');
            } else {
                swal(
                    "No se encontró la lección",
                    "",
                    "info"
                );
            }
        };
    });
    document.querySelectorAll('.eliminar-video').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            leccionSelected = findByLeccion(
                btn.parentElement.parentElement.parentElement.getAttribute('idleccion')
            );

            if (leccionSelected != undefined) {
                beanRequestLeccion.type_request = 'GET';
                beanRequestLeccion.operation = 'deletevideo';
                showAlertDelete('modalCargandoLeccion');
            } else {
                swal(
                    "No se encontró la lección",
                    "",
                    "info"
                );
            }
        };
    });
    document.querySelectorAll('.btnregresarLeccion').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            document.querySelector('#listaLibroLeccions').innerHTML = "";
            document.querySelector("#seccion-leccion").classList.add("d-none");
            document.querySelector("#seccion-cliente").classList.remove("d-none");
        };
    });
    document.querySelector('#btnAbrirPreguntaInterno').onclick = () => {

        if (clienteSelected != undefined) {
            document.querySelector('#titleManagerRespuesta').innerHTML =
                'RESPUESTAS DE CUESTIONARIOS INTERNOS';
            document.querySelector("#seccion-cliente").classList.add("d-none");
            document.querySelector("#seccion-leccion").classList.add("d-none");
            document.querySelector("#seccion-respuesta").classList.remove("d-none");
            beanRequestRespuesta.operation = 'obtener';
            beanRequestRespuesta.type_request = 'GET';
            respuestaSelected = { tipo: 2 };
            document.querySelector('#tablaNombreRespuesta').innerHTML = ` <tr>
            <th class="text-center">NOMBRES CUESTIONARIO</th>
            <th class="text-center">NOMBRE SUBTITULO</th>
            <th class="text-center">NÚMERO DE PREGUNTAS</th>
            <th class="text-center">DESCARGAR PDF</th>
            <th class="text-center">VER</th>
            <th class="text-center">VISUALIZAR</th>
            <th class="text-center">ELIMINAR</th>
        </tr>`;
            $('#modalCargandoRespuesta').modal('show');
        } else {
            swal(
                "No se encontró el alumno",
                "",
                "info"
            );
        }
    };
    document.querySelector('#btnAbrirPreguntaReforzamiento').onclick = () => {

        if (clienteSelected != undefined) {
            document.querySelector('#titleManagerRespuesta').innerHTML =
                'RESPUESTAS DE PREGUNTAS DE REFORSAMIENTO';
            document.querySelector("#seccion-cliente").classList.add("d-none");
            document.querySelector("#seccion-leccion").classList.add("d-none");
            document.querySelector("#seccion-respuesta").classList.remove("d-none");
            beanRequestRespuesta.operation = 'obtener';
            beanRequestRespuesta.type_request = 'GET';
            respuestaSelected = { tipo: 1 };
            document.querySelector('#tablaNombreRespuesta').innerHTML = ` <tr>
            <th class="text-center">CODIGO CAPITULO</th>
            <th class="text-center">NOMBRE CAPITULO</th>
            <th class="text-center">NÚMERO DE PREGUNTAS</th>
            <th class="text-center">DESCARGAR PDF</th>
            <th class="text-center">VER</th>
            <th class="text-center">VISUALIZAR</th>
            <th class="text-center">ELIMINAR</th>
        </tr>`;
            $('#modalCargandoRespuesta').modal('show');
        } else {
            swal(
                "No se encontró el alumno",
                "",
                "info"
            );
        }
    };
    document.querySelectorAll('.descargar-comentario').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            leccionSelected = findByLeccion(
                btn.parentElement.parentElement.getAttribute('idleccion')
            );
            if (leccionSelected != undefined) {
                $("#modalFrameComentario").modal("show");
                downloadURL(getHostFrontEnd() + "api/alumno/reporte/leccion?token=" + Cookies.get("clpe_token") + "&subtitulo=" + leccionSelected.subTitulo.codigo + "&cuenta=" + clienteSelected.cuenta.cuentaCodigo);

            } else {
                swal(
                    "No se encontró la lección",
                    "",
                    "info"
                );
            }

        };
    });
    addEventsButtonsComentarioRegreso();
}

function addEventsButtonsComentarioRegreso() {
    document.querySelectorAll('.btn-regresar-leccion').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            document.querySelector('#listaClienteComentarios').innerHTML = "";
            document.querySelector("#seccion-cliente").classList.remove("d-none");
            document.querySelector("#seccion-leccion").classList.add("d-none");
            document.querySelector("#seccion-respuesta").classList.add("d-none");

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
function downloadURL(url) {
    var hiddenIFrameID = 'hiddenDownloader',
        iframe = document.getElementById(hiddenIFrameID);
    if (iframe === null) {
        iframe = document.createElement('iframe');
        iframe.id = hiddenIFrameID;
        iframe.style.width = '100%';
        iframe.style.height = '100%';
        document.querySelector('#modalFrameContenidoComentario').appendChild(iframe);
    }

    iframe.src = url;
    document.querySelector("#descargarPdfComentario").parentElement.setAttribute("href", url);
};

var validarDormularioLeccion = () => {
    if (document.querySelector("#txtNombreLeccion").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Nombre",
            type: "warning",
            timer: 800,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtCodigoLeccion").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese codigo",
            type: "warning",
            timer: 800,
            showConfirmButton: false
        });
        return false;
    }

    if (clienteSelected == undefined) {
        swal({
            title: "Vacío",
            text: "Ingrese cliente",
            type: "warning",
            timer: 800,
            showConfirmButton: false
        });
        return false;
    }


    return true;
}
function eliminarlistLeccion(idbusqueda) {
    beanPaginationLeccion.count_filter--;
    beanPaginationLeccion.list.splice(findIndexLeccion(parseInt(idbusqueda)), 1);
}
function updatelistLeccion(classBean) {
    beanPaginationLeccion.list.splice(findIndexLeccion(classBean.idleccion), 1, classBean);
}
