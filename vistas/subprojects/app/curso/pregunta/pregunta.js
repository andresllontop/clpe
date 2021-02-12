var beanPaginationPregunta;
var preguntaSelected;
var beanRequestPregunta = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestPregunta.entity_api = 'subitems';
    beanRequestPregunta.operation = 'paginate';
    beanRequestPregunta.type_request = 'GET';
    document.querySelector("#tipoOpcionHeaderCurso").innerHTML = "Preguntas Frecuentes";
    $('#sizePagePregunta').change(function () {
        beanRequestPregunta.type_request = 'GET';
        beanRequestPregunta.operation = 'paginate';
        $('#modalCargandoPregunta').modal('show');
    });

    //  $('#modalCargandoPregunta').modal('show');

    $("#modalCargandoPregunta").on('shown.bs.modal', function () {
        processAjaxPregunta();
    });
    $("#ventanaModalManPregunta").on('hide.bs.modal', function () {
        beanRequestPregunta.type_request = 'GET';
        beanRequestPregunta.operation = 'paginate';
    });
    $("#txtDescripcionPregunta").Editor();

    $("#btnAbrirbook").click(function () {
        beanRequestPregunta.operation = 'add';
        beanRequestPregunta.type_request = 'POST';
        $("#imagePreview").html("");
        $("#tituloModalManPregunta").html("REGISTRAR PREGUNTA FRECUENTE");
        addPregunta();
        $("#ventanaModalManPregunta").modal("show");


    });

    $("#formularioPregunta").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (validarDormularioVideo()) {
            $('#modalCargandoPregunta').modal('show');
        }
    });

});

function processAjaxPregunta() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestPregunta.operation == 'update' ||
        beanRequestPregunta.operation == 'add'
    ) {

        json = {
            titulo: document.querySelector("#txtTituloPregunta").value,
            detalle: $("#txtDescripcionPregunta").Editor("getText"),
            tipo: 3,
            curso: curso_cSelected.idcurso
        };


    } else {
        form_data = null;
    }

    switch (beanRequestPregunta.operation) {
        case 'delete':
            parameters_pagination = '?id=' + preguntaSelected.idsubitem;
            break;
        case 'curso':
            parameters_pagination = '?idcurso=' + curso_cSelected.idcurso + '&tipo=3';
            break;
        case 'update':
            json.idsubitem = preguntaSelected.idsubitem;
            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':
            form_data.append("class", JSON.stringify(json));
            break;

        default:

            parameters_pagination +=
                '?filtro=&tipo=3';
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pagePregunta").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePagePregunta").value.trim();
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestPregunta.entity_api + "/" + beanRequestPregunta.operation +
            parameters_pagination,
        type: beanRequestPregunta.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestPregunta.operation == 'update' || beanRequestPregunta.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',
    }).done(function (beanCrudResponse) {

        $('#modalCargandoPregunta').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                document.querySelector("#pagePregunta").value = 1;
                document.querySelector("#sizePagePregunta").value = 20;
                $('#ventanaModalManPregunta').modal('hide');
            } else {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationPregunta = beanCrudResponse.beanPagination;
            listaPregunta(beanPaginationPregunta);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoPregunta').modal("hide");
        showAlertErrorRequest();

    });

}

function addPregunta(pregunta = undefined) {

    document.querySelector("#txtTituloPregunta").value = (pregunta == undefined) ? "" : pregunta.titulo;
    $("#txtDescripcionPregunta").Editor("setText", (pregunta == undefined) ? '<p style="color:black"></p>' : pregunta.detalle);
    $("#txtDescripcionPregunta").Editor("getText");


}

function listaPregunta(beanPagination) {

    document.querySelector('#tbodyPregunta').innerHTML = '';
    document.querySelector('#titleManagerPregunta').innerHTML =
        '[ ' + beanPagination.countFilter + ' ]  PREGUNTAS FRECUENTE';
    let row = "";
    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationPregunta'));
        row += `<tr>
        <td class="text-center" colspan="4">NO HAY PREGUNTAS</td>
        </tr>`;

        document.querySelector('#tbodyPregunta').innerHTML += row;
        return;
    }

    beanPagination.list.forEach((pregunta) => {

        row += `<tr  idsubitem="${pregunta.idsubitem}">
<td class="text-left" >${pregunta.titulo}</td>
<td class="text-justify" >${pregunta.detalle}</td>
<td class="text-center">
<button class="btn btn-info editar-pregunta" ><i class="zmdi zmdi-refresh"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-pregunta"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;

        // $('[data-toggle="tooltip"]').tooltip();
    });


    document.querySelector('#tbodyPregunta').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePagePregunta").value),
        document.querySelector("#pagePregunta"),
        $('#modalCargandoPregunta'),
        $('#paginationPregunta'));
    addEventsButtonsAdmin();


}

function addEventsButtonsAdmin() {
    document.querySelectorAll('.btn-regresar-curso').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {

            document.querySelector('#cursoHTML').classList.remove("d-none");
            document.querySelector('#beneficioHTML').classList.add("d-none");

        };
    });

    document.querySelectorAll('.editar-pregunta').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            preguntaSelected = findByPregunta(
                btn.parentElement.parentElement.getAttribute('idsubitem')
            );

            if (preguntaSelected != undefined) {
                addPregunta(preguntaSelected);
                $("#tituloModalManPregunta").html("EDITAR PREGUNTA FRECUENTE");
                $("#ventanaModalManPregunta").modal("show");
                beanRequestPregunta.type_request = 'POST';
                beanRequestPregunta.operation = 'update';
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
    document.querySelectorAll('.eliminar-pregunta').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            preguntaSelected = findByPregunta(
                btn.parentElement.parentElement.getAttribute('idsubitem')
            );

            if (preguntaSelected != undefined) {
                beanRequestPregunta.type_request = 'GET';
                beanRequestPregunta.operation = 'delete';
                $('#modalCargandoPregunta').modal('show');
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
}

function findIndexPregunta(idbusqueda) {
    return beanPaginationPregunta.list.findIndex(
        (Pregunta) => {
            if (Pregunta.idsubitem == parseInt(idbusqueda))
                return Pregunta;


        }
    );
}

function findByPregunta(idsubitem) {
    return beanPaginationPregunta.list.find(
        (Pregunta) => {
            if (parseInt(idsubitem) == Pregunta.idsubitem) {
                return Pregunta;
            }


        }
    );
}

var validarDormularioVideo = () => {
    if (document.querySelector("#txtTituloPregunta").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Título",
            type: "warning",
            timer: 800,
            showConfirmButton: false
        });
        return false;
    }
    if ($("#txtDescripcionPregunta").Editor("getText") == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Descripción",
            type: "warning",
            timer: 800,
            showConfirmButton: false
        });
        return false;
    }



    return true;
}
function addEventsButtonsCurso_c() {
    document.querySelectorAll('.beneficio-curso').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            curso_cSelected = findByCurso_c(
                btn.parentElement.parentElement.getAttribute('idcurso')
            );

            if (curso_cSelected != undefined) {
                beanRequestPregunta.type_request = 'GET';
                beanRequestPregunta.operation = 'curso';
                addClass(document.querySelector("#cursoHTML"), "d-none");
                removeClass(document.querySelector("#beneficioHTML"), "d-none");
                $('#modalCargandoPregunta').modal('show');
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });

}