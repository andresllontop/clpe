var beanPaginationHome;
var homeSelected;
var beanRequestHome = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    document.body.style.background = "#f7f7f7 url(" + getHostFrontEnd() + "vistas/subprojects/publico/blog/img/pattern.png) repeat top left";
    //INICIALIZANDO VARIABLES DE SOLICITUD
    beanRequestHome.entity_api = 'noticias';
    beanRequestHome.operation = 'obtener';
    beanRequestHome.type_request = 'GET';
    let fetOptions = {
        headers: {
            "Content-Type": 'application/json; charset=UTF-8',
            "Access-Control-Allow-Origin": "*",
            //"Authorization": "Bearer " + token
        },
        method: "GET",
    }
    /* PROMESAS LLAMAR A LAS API*/
    circleCargando.containerOcultar = $(document.querySelector("#firstSection"));
    circleCargando.container = $(document.querySelector("#firstSection").parentElement);
    circleCargando.createLoader();
    circleCargando.toggleLoader("show");
    Promise.all([
        fetch(getHostAPI() + beanRequestHome.entity_api + "/" + beanRequestHome.operation +
            "?nombre=&pagina=1&registros=1", fetOptions),
        fetch(getHostAPI() + "video/promotor/ubicacion" +
            '?tipo=1', fetOptions),
        fetch(getHostAPI() + "empresa/obtener" +
            "?filtro=&pagina=1&registros=1", fetOptions)
    ])
        .then(responses => Promise.all(responses.map((res) => res.json())))
        .then(json => {
            circleCargando.toggleLoader("hide");
            if (json[0].beanPagination !== null) {
                beanPaginationHome = json[0].beanPagination;
                toListHome(beanPaginationHome);
            }
            if (json[1].beanPagination !== null) {
                beanPaginationVideo = json[1].beanPagination;
                listaVideoPromotor(beanPaginationVideo);
            }
            if (json[2].beanPagination !== null) {
                beanPaginationFooterPublico = json[2].beanPagination;
                document.querySelector('#txtDescripcionHome').innerHTML = beanPaginationFooterPublico.list[0].mision;
                listaFooterPublico(beanPaginationFooterPublico);
            }

        })
        .catch(err => {
            showAlertErrorRequest();
        });
    /* */

});

function processAjaxHome() {
    let parameters_pagination = "";
    let json = "";
    if (beanRequestHome.operation == "obtener") {
        parameters_pagination += "?nombre=&pagina=1&registros=1";

    } else {
        return;
    }
    circleCargando.containerOcultar = $(document.querySelector("#firstSection"));
    circleCargando.container = $(document.querySelector("#firstSection").parentElement);
    circleCargando.createLoader();
    circleCargando.toggleLoader("show");
    $.ajax({
        url: getHostAPI() + beanRequestHome.entity_api + "/" + beanRequestHome.operation +
            parameters_pagination,
        type: beanRequestHome.type_request,
        contentType: 'application/json; charset=UTF-8',
        data: "",
        dataType: 'json'
    }).done(function (beanCrudResponse) {
        circleCargando.toggleLoader("hide");
        $('#modalCargandoPromotor').modal('hide');
        if (beanCrudResponse.beanPagination !== null) {
            beanPaginationHome = beanCrudResponse.beanPagination;
            toListHome(beanPaginationHome);
        }
        processAjaxVideoPromotor();
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(errorThrown)

    });
}

function toListHome(beanPagination) {
    if (beanPagination.count_filter == 0) {
        return;
    }
    beanPagination.list.forEach((Home) => {
        homeSelected = Home;
    });
    document.querySelector('#txtImagenHome').setAttribute("src", getHostFrontEnd() + "adjuntos/slider/" + homeSelected.imagen);


}
