var beanPaginationEconomico;
var economicoSelected = { fechai: "", fechaf: "", moneda: "" };
var beanRequestEconomico = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestEconomico.entity_api = 'economico';
    beanRequestEconomico.operation = 'general';
    beanRequestEconomico.type_request = 'GET';

    $('#modalCargandoEconomico').modal('show');

    $("#modalCargandoEconomico").on('shown.bs.modal', function () {
        processAjaxEconomico();
    });
    $("#ventanaModalManEconomico").on('hide.bs.modal', function () {
        beanRequestEconomico.operation = 'general';
        beanRequestEconomico.type_request = 'GET';
    });
    $("#formularioPagadoSoles").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();
        beanRequestEconomico.operation = 'generalfecha';
        beanRequestEconomico.type_request = 'GET';
        economicoSelected.fechai = document.querySelector("#txtfechaInicialPagadoSoles").value;
        economicoSelected.fechaf = document.querySelector("#txtfechaFinalPagadoSoles").value;
        economicoSelected.moneda = "PEN";
        $('#modalCargandoEconomico').modal('show');


    });
    $("#formularioPagadoDolares").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();
        beanRequestEconomico.operation = 'generalfecha';
        beanRequestEconomico.type_request = 'GET';
        economicoSelected.fechai = document.querySelector("#txtfechaInicialPagadoDolares").value;
        economicoSelected.fechaf = document.querySelector("#txtfechaFinalPagadoDolares").value;
        economicoSelected.moneda = "USD";
        $('#modalCargandoEconomico').modal('show');

    });
});

function processAjaxEconomico() {
    let parameters_pagination = '';
    switch (beanRequestEconomico.operation) {
        case 'general':
            break;
        default:
            parameters_pagination +=
                '?moneda=' + economicoSelected.moneda;
            parameters_pagination +=
                '&fechai=' + economicoSelected.fechai;
            parameters_pagination +=
                '&fechaf=' + economicoSelected.fechaf;
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestEconomico.entity_api + "/" + beanRequestEconomico.operation +
            parameters_pagination,
        type: beanRequestEconomico.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: null,
        cache: false,
        contentType: 'application/json; charset=UTF-8',
        processData: false,
        dataType: 'json'
    }).done(function (beanCrudResponse) {

        $('#modalCargandoEconomico').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationEconomico = beanCrudResponse.beanPagination;
            listaEconomico(beanPaginationEconomico);

        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoEconomico').modal("hide");
        showAlertErrorRequest();

    });

}


function listaEconomico(beanPagination) {
    beanPagination.list.forEach((economico) => {
        if (economico.moneda == "PEN") {
            if (economico.precio == null) {
                document.querySelector('#countComisionSoles').innerHTML = "S/. " + 0.00;
                document.querySelector('#countPagadoSoles').innerHTML = "S/. " + 0.00;
                document.querySelector('#countDepositadoSoles').innerHTML = "S/. " + 0.00;
            } else {
                document.querySelector('#countComisionSoles').innerHTML = "S/. " + parseFloat(economico.comision).toFixed(2);
                document.querySelector('#countPagadoSoles').innerHTML = "S/. " + parseFloat(economico.precio).toFixed(2);
                document.querySelector('#countDepositadoSoles').innerHTML = "S/. " + (parseFloat(economico.precio) - parseFloat(economico.comision)).toFixed(2);
            }

        } else {
            if (economico.precio == null) {
                document.querySelector('#countComisionDolares').innerHTML = "$ " + 0.00;
                document.querySelector('#countPagadoDolares').innerHTML = "$ " + 0.00;
                document.querySelector('#countDepositadoDolares').innerHTML = "$ " + +0.00;
            } else {
                document.querySelector('#countComisionDolares').innerHTML = "$ " + parseFloat(economico.comision).toFixed(2);
                document.querySelector('#countPagadoDolares').innerHTML = "$ " + parseFloat(economico.precio).toFixed(2);
                document.querySelector('#countDepositadoDolares').innerHTML = "$ " + (parseFloat(economico.precio) - parseFloat(economico.comision)).toFixed(2);
            }

        }



    });

}


