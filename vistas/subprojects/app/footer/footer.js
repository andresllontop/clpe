
var beanPaginationFooterPublico;
var footerPublicoSelected;
var beanRequestFooterPublico = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {

    beanRequestFooterPublico.entity_api = 'empresa';
    beanRequestFooterPublico.operation = 'obtener';
    beanRequestFooterPublico.type_request = 'GET';

    fetch(getHostAPI() + "empresa/obtener" +
        "?filtro=&pagina=1&registros=1", {
        headers: {
            "Content-Type": 'application/json; charset=UTF-8',
        },
        method: "GET"
    })
        .then(response => response.json())
        .then(json => {
            document.querySelector('#visitaContador').innerHTML = json.beanPagination.countFilter;

        })
        .catch(err => {
            showAlertErrorRequest();
            console.log(err);
        });

});




