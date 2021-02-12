var beanPaginationTarea;
var beanPaginationTareaTitulo;
var tareaSelected;
var tareaCapituloSelected;
var beanRequestTarea = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestTarea.entity_api = 'tareas';
    beanRequestTarea.operation = 'titulo';
    beanRequestTarea.type_request = 'GET';



    $("#modalCargandoTarea").on('shown.bs.modal', function () {
        beanRequestTarea.operation = 'paginate';
        beanRequestTarea.type_request = 'GET';
        processAjaxTarea();
    });

    //TITULO
    $('#modalCargandoTareaTitulo').modal('show');

    $("#modalCargandoTareaTitulo").on('shown.bs.modal', function () {
        beanRequestTarea.operation = 'titulo';
        beanRequestTarea.type_request = 'GET';
        processAjaxTarea();
    });


});

function processAjaxTarea() {

    let parameters_pagination = '';
    switch (beanRequestTarea.operation) {
        case 'titulo':
            parameters_pagination +=
                '?filtro=';
            parameters_pagination +=
                '&pagina=1';
            parameters_pagination +=
                '&registros=20';
            break;
        default:

            parameters_pagination +=
                '?filtro=' + tareaCapituloSelected.subTitulo.titulo.codigo;
            parameters_pagination +=
                '&pagina=1';
            parameters_pagination +=
                '&registros=20';
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestTarea.entity_api + "/" + beanRequestTarea.operation +
            parameters_pagination,
        type: beanRequestTarea.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: null,
        cache: false,
        contentType: 'application/json; charset=UTF-8',
        processData: false,
        dataType: 'json'
    }).done(function (beanCrudResponse) {
        $('#modalCargandoTarea').modal('hide');
        $('#modalCargandoTareaTitulo').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                $('#ventanaModalManTarea').modal('hide');
            } else {

                showAlertTopEnd("info", beanCrudResponse.messageServer, "");
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            if (beanRequestTarea.operation == "paginate") {
                beanPaginationTarea = beanCrudResponse.beanPagination;
                listaTarea(beanPaginationTarea);
            } else {
                beanPaginationTareaTitulo = beanCrudResponse.beanPagination;
                listaTareaTitulo(beanPaginationTareaTitulo);
            }

        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoTarea').modal("hide");
        showAlertErrorRequest();

    });

}

function listaTarea(beanPagination) {
    document.querySelector('#sectionTareas').classList.remove("d-none");
    document.querySelector('#sectionTareasTitulo').classList.add("d-none");
    document.querySelector('#sectionLecciones').classList.add("d-none");
    document.querySelector('#tbodyTarea').innerHTML = '';
    document.querySelector('#titleManagerTarea').innerHTML =
        'Lecciones Realizadas <p style="font-weight: bold;" class="text-purple">' + tareaCapituloSelected.subTitulo.titulo.nombre + ' </p>' + '<p style="font-weight: bold;">Subtítulos </p>';
    let row = "";
    if (beanPagination.list.length == 0) {

        row += `  <h5 class="text-center">NO CUENTAS CON LECCIONES REALIZADAS</h5>
        <div class="text-center">  <a class="btn btn-bordered anim fadeInRight animated" role="button" href="index"
        style="visibility: visible;">INICIO</a></div>
       `;

        document.querySelector('#tbodyTarea').innerHTML = row;
        return;
    }
    beanPagination.list.forEach((tarea) => {

        row += `
        <figure class="effect-oscar ${(tarea.tipo == 0) ? "detalle-tarea" : (tarea.tipo == 1) ? "examen-capitulo" : "examen-subtitulo"}" idtarea="${tarea.idtarea}">
						<img  width="100%" height="100%" src="${getHostFrontEnd() + ((tarea.subTitulo.imagen == "" || tarea.subTitulo.imagen == null) ? 'adjuntos/logoHeader.jpg' : "adjuntos/libros/subtitulos/" + tarea.subTitulo.imagen)}" alt="${tarea.subTitulo.imagen}"/>
						<figcaption>
                            <h2>${(tarea.tipo == 0) ? tarea.subTitulo.nombre : (tarea.tipo == 1) ? "Examen final del Capítulo" : "Preguntas de Reforzamiento"}</h2>
                            <p>Realizado : ${tarea.fecha}</p>
						</figcaption>			
					</figure>
`;
    });

    document.querySelector('#tbodyTarea').innerHTML += row;

    addEventsButtonsTarea();


}

function listaTarea33(beanPagination) {
    document.querySelector('#sectionTareas').classList.remove("d-none");
    document.querySelector('#sectionTareasTitulo').classList.add("d-none");
    document.querySelector('#sectionLecciones').classList.add("d-none");
    document.querySelector('#tbodyTarea').innerHTML = '';
    document.querySelector('#titleManagerTarea').innerHTML =
        'Lecciones Realizadas <p style="font-weight: bold;" class="text-purple">' + tareaCapituloSelected.subTitulo.titulo.nombre + ' </p>' + '<p style="font-weight: bold;">Subtítulos </p>';
    let row = "";
    if (beanPagination.list.length == 0) {

        row += `  <h5 class="text-center">NO CUENTAS CON LECCIONES REALIZADAS</h5>
        <div class="text-center">  <a class="btn btn-bordered anim fadeInRight animated" role="button" href="index"
        style="visibility: visible;">INICIO</a></div>
       `;

        document.querySelector('#tbodyTarea').innerHTML = row;
        return;
    }
    beanPagination.list.forEach((tarea) => {

        row += `
<div class="col-lg-3 col-md-3 col-sm-4 col-12" >
<div class="blog anim fadeInLeft">
    <span class="image2 ">
        <a data-icon="fa-play" class="ms-slide linked m-0" style="width:auto;">
            <img width="100%" height="100%" src="${getHostFrontEnd() + ((tarea.subTitulo.imagen == "" || tarea.subTitulo.imagen == null) ? 'adjuntos/logoHeader.jpg' : "adjuntos/libros/subtitulos/" + tarea.subTitulo.imagen)}"
                alt="user-picture" />
        </a>
        <p class="desc bg-purple" style="opacity: 0.88;"> `;
        if (tarea.tipo == 0) {
            row += `
            <i class="text-white text-center p-2 detalle-tarea" idtarea="${tarea.idtarea}" style=" font-size: 1.4rem;line-height: 21px;">${tarea.subTitulo.nombre}   </i>
            <small class="w-100 text-white px-2" style="margin-top: -30px;position: absolute;">Realizado : ${tarea.fecha}</small>
          `;
        } else if (tarea.tipo == 1) {
            row += `
            <i class="text-white text-center p-2 examen-capitulo" idtarea="${tarea.idtarea}" style=" font-size: 1.4rem;line-height: 21px;">Examen final del Capítulo</i><small class="w-100 text-white px-2" style="margin-top: -30px;position: absolute;">Realizado : ${tarea.fecha}</small>`;

        } else if (tarea.tipo == 2) {
            row += `
            <i class="text-white text-center p-2 examen-subtitulo" idtarea="${tarea.idtarea}" style=" font-size: 1.4rem;line-height: 21px;">Preguntas de Reforzamiento</i><small class="w-100 text-white px-2" style="margin-top: -30px;position: absolute;">Realizado : ${tarea.fecha}</small>`;

        }
        row += `
        </p>
    </span>
</div>
</div>

`;
    });

    document.querySelector('#tbodyTarea').innerHTML += row;

    addEventsButtonsTarea();


}

function addEventsButtonsTarea() {
    document.querySelectorAll('.detalle-tarea').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            tareaSelected = findByTarea(
                btn.getAttribute('idtarea')
            );

            if (tareaSelected != undefined) {

                // $("#titleManagerDetalle").html('"' + tareaSelected.subtitulo.nombre + '"');
                $('#modalCargandoLeccion').modal('show');
            } else {
                showAlertTopEnd("info", "Vacío!", "No se encontró el tarea");
            }
        };
    });

    document.querySelectorAll('.detalle-titulo').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            tareaCapituloSelected = findByTareaTitulo(
                btn.getAttribute('idtarea')
            );
            if (tareaCapituloSelected != undefined) {
                $('#modalCargandoTarea').modal('show');
            } else {
                showAlertTopEnd("info", "Vacío!", "No se encontró la tarea");
            }
        };
    });

    document.querySelectorAll('.examen-capitulo').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            tareaSelected = findByTarea(
                btn.getAttribute('idtarea')
            );
            if (tareaSelected != undefined) {
                $("#titleManRespuesta").html('"' + tareaSelected.subTitulo.nombre + '"');
                $('#modalCargandoRespuesta').modal('show');
            } else {
                showAlertTopEnd("info", "Vacío!", "No se encontró el tarea");
            }
        };
    });

    document.querySelectorAll('.examen-subtitulo').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            tareaSelected = findByTarea(
                btn.getAttribute('idtarea')
            );

            if (tareaSelected != undefined) {

                $("#titleManRespuesta").html('"' + tareaSelected.subTitulo.nombre + '"');
                $('#modalCargandoRespuesta').modal('show');
            } else {
                showAlertTopEnd("info", "Vacío!", "No se encontró el tarea");
            }
        };
    });

    document.querySelectorAll('.btn-regresar-titulo').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {

            document.querySelector('#sectionTareasTitulo').classList.remove("d-none");
            document.querySelector('#sectionTareas').classList.add("d-none");
            document.querySelector('#sectionLecciones').classList.add("d-none");

        };
    });

}

function listaTareaTitulo(beanPagination) {
    document.querySelector('#sectionTareasTitulo').classList.remove("d-none");
    document.querySelector('#sectionTareas').classList.add("d-none");
    document.querySelector('#sectionLecciones').classList.add("d-none");
    document.querySelector('#tbodyTareaTitulo').innerHTML = '';
    document.querySelector('#titleManagerTareaTitulo').innerHTML =
        'Lecciones Realizadas <p style="font-weight: bold;">Capítulos</p>';
    let row = "";
    if (beanPagination.list.length == 0) {
        row += `  <h5 class="text-center col-12">NO CUENTAS CON LECCIONES REALIZADAS</h5>
        <div class="text-center col-12"> <a class="btn btn-bordered anim fadeInRight animated" role="button" href="index"
        style="visibility: visible;">INICIO</a></div>
       `;

        document.querySelector('#tbodyTareaTitulo').innerHTML = row;
        return;
    }
    beanPagination.list.forEach((tarea) => {

        row += `
<figure class="effect-oscar detalle-titulo" idtarea="${tarea.idtarea}">
                <img width="100%" height="100%" src="${getHostFrontEnd() + ((tarea.subTitulo.titulo.imagen == "") ? 'adjuntos/logoHeader.jpg' : "adjuntos/libros/capitulos/" + tarea.subTitulo.titulo.imagen)}" alt="${tarea.subTitulo.titulo.imagen}"/>
                <figcaption>
                    <h2>${tarea.subTitulo.titulo.nombre}</h2>
                    <p>Inicio : ${tarea.fecha}</p>
                </figcaption>			
            </figure>
`;

    });

    document.querySelector('#tbodyTareaTitulo').innerHTML += row;
    addEventsButtonsTarea();


}

function findIndexTarea(idbusqueda) {
    return beanPaginationTarea.list.findIndex(
        (Tarea) => {
            if (Tarea.idtarea == parseInt(idbusqueda))
                return Tarea;


        }
    );
}

function findByTarea(idtarea) {
    return beanPaginationTarea.list.find(
        (Tarea) => {
            if (parseInt(idtarea) == Tarea.idtarea) {
                return Tarea;
            }


        }
    );
}

function findIndexTareaTitulo(idbusqueda) {
    return beanPaginationTareaTitulo.list.findIndex(
        (Tarea) => {
            if (Tarea.idtarea == parseInt(idbusqueda))
                return Tarea;


        }
    );
}

function findByTareaTitulo(idtarea) {
    return beanPaginationTareaTitulo.list.find(
        (Tarea) => {
            if (parseInt(idtarea) == Tarea.idtarea) {
                return Tarea;
            }


        }
    );
}


