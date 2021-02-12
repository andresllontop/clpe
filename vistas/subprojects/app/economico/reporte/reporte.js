var beanPaginationReporte;
var reporteSelected;
var beanRequestReporte = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestReporte.entity_api = 'economico';
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
});

function processAjaxReporte() {

    let parameters_pagination = '';

    switch (beanRequestReporte.operation) {
        case 'reporte':
            parameters_pagination +=
                '?moneda=' + reporteSelected.moneda;
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


    document.querySelector('#economico-dolares-reporte').onclick = () => {
        reporteSelected = { moneda: 'USD' };
        beanRequestReporte.type_request = 'GET';
        beanRequestReporte.operation = 'reporte';
        $('#modalCargandoReporte').modal('show');
    }
    document.querySelector('#economico-soles-reporte').onclick = () => {
        reporteSelected = { moneda: 'PEN' };
        beanRequestReporte.type_request = 'GET';
        beanRequestReporte.operation = 'reporte';
        $('#modalCargandoReporte').modal('show');
    }



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
