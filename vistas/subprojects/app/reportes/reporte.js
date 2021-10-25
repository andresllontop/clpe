var beanPaginationReporte;
var reporteSelected;
var beanRequestReporte = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestReporte.entity_api = 'cliente';
    beanRequestReporte.operation = 'reporte';
    beanRequestReporte.type_request = 'GET';

    $("#modalCargandoReporte").on('shown.bs.modal', function () {
        processAjaxReporte();
    });
    $("#ventanaModalManReporte").on('hide.bs.modal', function () {
        beanRequestReporte.type_request = 'GET';
        beanRequestReporte.operation = 'reporte';
    });
    addEventsButtonsAdmin();
    processAjaxLibro();
});
function processAjaxLibro() {

    let parameters_pagination = '';

    parameters_pagination +=
        '?filtro=';
    parameters_pagination +=
        '&pagina=1';
    parameters_pagination +=
        '&registros=100';

    $.ajax({
        url: getHostAPI() + "libro/paginate" + parameters_pagination,
        type: "GET",
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: null,
        cache: false,
        contentType: 'application/json; charset=UTF-8',
        processData: false,
        dataType: 'json'
    }).done(function (beanCrudResponse) {
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() != 'ok') {
                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {
            listaLibro(beanCrudResponse.beanPagination);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        showAlertErrorRequest();
    });

}
function listaLibro(beanPagination) {
    let row = `
    <div class="page-header w-100">
    <h2 class="all-tittles">Reportes <small>Libros</small></h2>
</div><br>
    `;
    document.querySelector('#tbodyLibro').innerHTML = '';

    if (beanPagination.list.length == 0) {
        document.querySelector('#tbodyLibro').innerHTML += row;
        return;
    }
    beanPagination.list.forEach((libro) => {

        row += `
<div class="col-xs-12 col-sm-6 col-md-4">
                        <div class="full-reset report-content">
                            <p class="text-center">
                                <a href="javascript:void(0)"  class="code-libro" codelibro="${libro.codigo}"> <i
                                        class="zmdi zmdi-trending-down zmdi-hc-5x"></i></a>
                            </p>
                            <h3 class="text-center">Estudiantes ${libro.nombre}</h3>
                        </div>
                    </div>
`;
    });
    document.querySelector('#tbodyLibro').innerHTML += row;
    addEventsButtonsAdmin();
}

function processAjaxReporte() {

    let parameters_pagination = '';

    switch (beanRequestReporte.operation) {
        case 'reporte':
            parameters_pagination +=
                '?tipo=' + reporteSelected.tipo;
            parameters_pagination +=
                '&estado=' + reporteSelected.estado;
            break;
        case 'libroreport':
            parameters_pagination +=
                '?libro=' + reporteSelected.libro;

            break;

        default:
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestReporte.entity_api + "/" + beanRequestReporte.operation +
            parameters_pagination,
        type: beanRequestReporte.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        dataType: "html",
        contentType: 'application/json; charset=UTF-8',
    }).done(function (data, status, xhr) {
        $('#modalCargandoReporte').modal("hide");
        if (data.trim() != "") {
            downloadURL(data, xhr);
        } else {
            showAlertTopEnd("info", "Vacío!", "No cuenta con Registros.");
        }


    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoReporte').modal("hide");
        showAlertErrorRequest();
        console.log(errorThrown);

    });

}

function addEventsButtonsAdmin() {

    document.querySelector('#alumno-activo-reporte').onclick = () => {
        beanRequestReporte.entity_api = 'cliente';
        reporteSelected = { tipo: 2, estado: 1 };
        beanRequestReporte.type_request = 'GET';
        beanRequestReporte.operation = 'reporte';
        $('#modalCargandoReporte').modal('show');
    }
    document.querySelector('#alumno-inactivo-reporte').onclick = () => {
        beanRequestReporte.entity_api = 'cliente';
        reporteSelected = { tipo: 2, estado: 0 };
        beanRequestReporte.type_request = 'GET';
        beanRequestReporte.operation = 'reporte';
        $('#modalCargandoReporte').modal('show');
    }
    document.querySelector('#alumno-reporte').onclick = () => {
        beanRequestReporte.entity_api = 'cliente';
        reporteSelected = { tipo: 2, estado: -1 };
        beanRequestReporte.type_request = 'GET';
        beanRequestReporte.operation = 'reporte';
        $('#modalCargandoReporte').modal('show');
    }
    document.querySelector('#personal-reporte').onclick = () => {
        beanRequestReporte.entity_api = 'administrador';
        reporteSelected = { tipo: 1, estado: -1 };
        beanRequestReporte.type_request = 'GET';
        beanRequestReporte.operation = 'reporte';
        $('#modalCargandoReporte').modal('show');
    }
    document.querySelector('#visita-reporte').onclick = () => {
        beanRequestReporte.entity_api = 'visitas';
        reporteSelected = { tipo: 1, estado: 0 };
        beanRequestReporte.type_request = 'GET';
        beanRequestReporte.operation = 'reporte';
        $('#modalCargandoReporte').modal('show');
    }
    document.querySelector('#publico-reporte').onclick = () => {
        beanRequestReporte.entity_api = 'publicos';
        reporteSelected = { tipo: 1, estado: 0 };
        beanRequestReporte.type_request = 'GET';
        beanRequestReporte.operation = 'reporte';
        $('#modalCargandoReporte').modal('show');
    }
    document.querySelector('#tarea-reporte').onclick = () => {
        beanRequestReporte.entity_api = 'lecciones';
        beanRequestReporte.operation = 'excel';
        beanRequestReporte.type_request = 'GET';
        $('#modalCargandoReporte').modal('show');
    }
    document.querySelectorAll('.code-libro').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            reporteSelected = { libro: btn.getAttribute('codelibro') };
            beanRequestReporte.entity_api = 'cliente';
            beanRequestReporte.type_request = 'GET';
            beanRequestReporte.operation = 'libroreport';
            $('#modalCargandoReporte').modal('show');
        };
    });

}

function downloadURL(data, xhr) {
    var contentType = 'application/vnd.ms-excel';
    var filename = "";
    var disposition = xhr.getResponseHeader('Content-Disposition');
    if (disposition && disposition.indexOf('attachment') !== -1) {
        var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
        var matches = filenameRegex.exec(disposition);
        if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
    }
    console.log("FILENAME: " + filename);
    try {
        var blob = new Blob([data], { type: contentType });
        var downloadUrl = URL.createObjectURL(blob);
        var a = document.createElement("a");
        a.href = downloadUrl;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!, se descargó el archivo excel con el nombre : " + filename);
    } catch (exc) {
        showAlertTopEnd("error", "Sin Acceso", "El método Save Blob falló con la siguiente excepción.");
        //console.log(exc);
    }
};
