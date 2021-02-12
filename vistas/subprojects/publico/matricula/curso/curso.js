
var beanPaginationCurso;
var cursoSelected, contadorCurso = 2, valorHover;
var beanRequestCurso = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestCurso.entity_api = 'cursos';
    beanRequestCurso.operation = 'paginate';
    beanRequestCurso.type_request = 'GET';


    let fetOptions = {
        headers: {
            "Content-Type": 'application/json; charset=UTF-8',
            //"Authorization": "Bearer " + token
        },
        method: "GET",
    }
    /* PROMESAS LLAMAR A LAS API*/
    circleCargando.containerOcultar = $(document.querySelector("#tbodyCurso"));
    circleCargando.container = $(document.querySelector("#tbodyCurso").parentElement);
    circleCargando.createLoader();
    circleCargando.toggleLoader("show");
    Promise.all([
        fetch(getHostAPI() + beanRequestCurso.entity_api + "/" + beanRequestCurso.operation +
            "?filtro=" + '&pagina=1' + '&registros=8', fetOptions),
        fetch(getHostAPI() + "empresa/obtener" +
            "?filtro=&pagina=1&registros=1", fetOptions)
    ])
        .then(responses => Promise.all(responses.map((res) => res.json())))
        .then(json => {
            circleCargando.toggleLoader("hide");
            if (json[0].beanPagination !== null) {
                beanPaginationCurso = json[0].beanPagination;
                beanPaginationCursoUnico = json[0].beanPagination;
                listaCurso(beanPaginationCursoUnico);
            }
            if (json[1].beanPagination !== null) {
                beanPaginationFooterPublico = json[1].beanPagination;
                listaFooterPublico(beanPaginationFooterPublico);
            }

        })
        .catch(err => {
            console.log(err);
            showAlertErrorRequest();
        });
    /* */


});

function processAjaxCurso() {

    let parameters_pagination = '';
    let json = '';
    circleCargando.containerOcultar = $(document.querySelector("#tbodyCurso"));
    circleCargando.container = $(document.querySelector("#tbodyCurso").parentElement);
    circleCargando.createLoader();
    circleCargando.toggleLoader("show");
    switch (beanRequestCurso.operation) {
        default:
            parameters_pagination +=
                '?filtro=';
            parameters_pagination +=
                '&pagina=1';
            parameters_pagination +=
                '&registros=8';
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestCurso.entity_api + "/" + beanRequestCurso.operation +
            parameters_pagination,
        type: beanRequestCurso.type_request,
        json: json,
        contentType: 'application/json; charset=UTF-8',
        dataType: 'json'
    }).done(function (beanCrudResponse) {
        circleCargando.toggleLoader("hide");
        if (beanCrudResponse.beanPagination !== null) {
            beanPaginationCursoUnico = beanCrudResponse.beanPagination;
            if (beanCrudResponse.beanPagination.list.length > 0) {
                beanPaginationCurso.list = (beanPaginationCurso.list).concat(beanCrudResponse.beanPagination.list);
            }
            listaCurso(beanPaginationCursoUnico);

        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        circleCargando.toggleLoader("hide");

        showAlertErrorRequest();
    });

}

function listaCurso(beanPagination) {
    //https://dde9c0myqwtst.cloudfront.net/imagenes/publications/1/real/leadgods_20766e45c7a11416639044aa437e25ef_27182.jpg
    let row = "";
    document.querySelector("#countCurso").innerHTML = "Tenemos " + beanPagination.countFilter + "cursos que te van a gustar";
    beanPagination.list.forEach((curso) => {

        row += `
    <div class="item block ver-detalle mx-auto" idcurso="${curso.idcurso}">
    <div class="thumbs-wrapper">
        <div class="thumbs">
            <img style="width: 100%;"
                src="${getHostFrontEnd()}adjuntos/libros/${curso.portada}" />

        </div>
    </div>
    <h2 class="text-center">${curso.titulo}</h2>

    <div class="intro">
        <p class="text-justify">${curso.descripcion}</p>
        <h3 style="color:#66398e;display: contents;">USD ${curso.precio}</h3>
        <a href="${getHostFrontEnd()}matricula/detalle/${curso.idcurso}"
            class="btn btn-purple-o border-radius f-weight-700 py-1 px-4" style="float: right;">${curso.tipo == 1 ? '<i class="zmdi zmdi-shopping-cart"></i>Comprar' : '<i class="zmdi zmdi-comment-video"></i> Vía Zoom'} </a>
    </div>

</div>

`;



    });


    document.querySelector('#tbodyCurso').innerHTML += row;
    addEventsButtonsAdmin();

}

function addEventsButtonsAdmin() {


    document.querySelectorAll('.ver-detalle').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {

            window.location.href = getHostFrontEnd() + "matricula/detalle/" + btn.getAttribute('idcurso');

        };
    });
}