var beanPaginationConferencia;
var conferenciaSelected;
var beanRequestConferencia = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestConferencia.entity_api = 'conferencias';
    beanRequestConferencia.operation = 'obtener';
    beanRequestConferencia.type_request = 'GET';

    $("#modalCargandoConferencia").on('shown.bs.modal', function () {
        processAjaxConferencia();
    });

    $('#modalCargandoConferencia').modal('show');


});

function processAjaxConferencia() {


    let parameters_pagination = '';


    $.ajax({
        url: getHostAPI() + beanRequestConferencia.entity_api + "/" + beanRequestConferencia.operation +
            parameters_pagination,
        type: beanRequestConferencia.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: null,
        cache: false,
        contentType: 'application/json; charset=UTF-8',
        processData: false,
        dataType: 'json',
    }).done(function (beanCrudResponse) {
        $('#modalCargandoConferencia').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "MENSAJE ENVIADO!", "Máximo en 24 horas se responderá su inquietud.");
            } else {
                showAlertTopEnd("info", "VERIFICACIÓN!", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {
            beanPaginationConferencia = beanCrudResponse.beanPagination;
            listaConferencia(beanPaginationConferencia);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoConferencia').modal("hide");
        showAlertErrorRequest();

    });

}

function listaConferencia(beanPagination) {
    document.querySelector('#timeline').innerHTML = '';

    let row = "";

    if (beanPagination.list.length == 0) {

        document.querySelector('#timeline').innerHTML += "NO HAY HORARIOS DE CONFERENCIAS";
        return;
    }

    let fecha1 = new Date(), contador = 0;
    let fecha2 = new Date();

    let dataCercana = findByFechaCercana().data;
    console.log(dataCercana);
    if (dataCercana == undefined) {
        dataCercana = { fecha: "x" };
    }
    beanPagination.list.forEach((conferencia) => {
        contador++;
        fecha2 = new Date(conferencia.fecha);
        console.log(dataCercana);
        row += `

<div class="card-line ${(contador % 2 == 0) ? "coffee" : "design"}">

<div class="circle ${((contador % 2 == 0) ? "circle-left circle-1 mr-10 coffee-badge" : "circle-right circle-2 ml-10 design-badge") + (((dataCercana.fecha == conferencia.fecha) && contador % 2 == 0) ? " active-left pulse-info" : ((dataCercana.fecha == conferencia.fecha) && contador % 2 != 0) ? " active-right pulse-orange" : "")}">
<img src="${getHostFrontEnd()}adjuntos/conferencia/${conferencia.imagen}"
alt="${conferencia.imagen}">
   
</div>
<div class="f-weight-700 f-20 ${((contador % 2 == 0) ? "time-fecha-left time-fecha-1" : "time-fecha-right time-fecha-2") + (dataCercana.fecha == conferencia.fecha ? " text-left-zoom" : "")}">${(conferencia.fecha).split(" ")[0].split("-")[2] + "/" + (conferencia.fecha).split(" ")[0].split("-")[1] + "/" + (conferencia.fecha).split(" ")[0].split("-")[0] + "<br>" + (conferencia.fecha).split(" ")[1]}</div>
<span class="d-none">${conferencia.fecha + ((Math.round((fecha2.getTime() - fecha1.getTime()) / (1000 * 60 * 60))) < 24 ? +" -  Faltan " + (Math.round((fecha2.getTime() - fecha1.getTime()) / (1000 * 60 * 60))) + " horas " : (Math.round((fecha2.getTime() - fecha1.getTime()) / (1000 * 60 * 60))) == 0 ? " - En estos momentos " : (Math.round((fecha2.getTime() - fecha1.getTime()) / (1000 * 60 * 60))) < 0 ? "" : " - Faltan " + Math.round((fecha2.getTime() - fecha1.getTime()) / (1000 * 60 * 60 * 24)) + " días")}</span>
<div class="cuadrado ${(contador % 2 == 0) ? " cuadrado-1" : " cuadrado-3"}"></div>

<div class="cuadrado-detalle ${((contador % 2 == 0) ? "cuadrado-detalle-left " : "cuadrado-detalle-right ") + ((dataCercana.fecha == conferencia.fecha && contador % 2 == 0) ? " cuadrado-detalle-active-left pulse-info" : (dataCercana.fecha == conferencia.fecha && contador % 2 != 0) ? " cuadrado-detalle-active-right pulse-orange" : "")}">
    <h5 class="title">CONFERENCIA</h5>
    <div class="details">${conferencia.descripcion}</div>
    <a href="${conferencia.link}" target="_blank" rel="noopener noreferrer" class="details-link"><i class="zmdi zmdi-videocam mx-1"></i> ${conferencia.link}</a>
</div>


</div>

`;

    });
    document.querySelector('#timeline').innerHTML += row;



}
function findByFechaCercana(fecha = new Date()) {
    let cantidadDias = 365, data = undefined, cantidadDiasTmp = 365;
    beanPaginationConferencia.list.find(
        (conferencia) => {
            cantidadDiasTmp = restaFechas(fecha, new Date(conferencia.fecha),);

            if ((cantidadDiasTmp < cantidadDias) && (cantidadDiasTmp >= 0)) {
                cantidadDias = cantidadDiasTmp;
                data = conferencia;
            }


        }
    );
    return { data: data, numerodias: cantidadDias };
}
restaFechas = function (fecha1 = new Date(), fecha2 = new Date()) {

    return (Math.round((fecha2.getTime() - fecha1.getTime()) / (1000 * 60 * 60 * 24)));
}