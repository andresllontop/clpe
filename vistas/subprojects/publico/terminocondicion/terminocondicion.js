
var beanPaginationTerminoCondicion;
var terminoCondicionSelected;
var beanRequestTerminoCondicion = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {

    beanRequestTerminoCondicion.entity_api = 'empresa';
    beanRequestTerminoCondicion.operation = 'obtener';
    beanRequestTerminoCondicion.type_request = 'GET';

    fetch(getHostAPI() + "empresa/obtener" +
        "?filtro=&pagina=1&registros=1", {
        headers: {
            "Content-Type": 'application/json; charset=UTF-8',
        },
        method: "GET"
    })
        .then(response => response.json())
        .then(json => {
            document.querySelector('#TerminoCondicion').innerHTML = json.beanPagination.list[0].terminoCondicion;

        })
        .catch(err => {
            showAlertErrorRequest();
            console.log(err);
        });

});


