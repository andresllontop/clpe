
var beanPaginationCurso;
var cursoSelected, contadorCurso = 2, valorHover;
var beanRequestCurso = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestCurso.entity_api = 'book/cuenta';
    beanRequestCurso.operation = 'obtener';
    beanRequestCurso.type_request = 'GET';
    /* PROMESAS LLAMAR A LAS API*/
    processAjaxCurso();


});

function processAjaxCurso() {

    $.ajax({
        url: getHostAPI() + beanRequestCurso.entity_api + "/" + beanRequestCurso.operation,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        type: beanRequestCurso.type_request,
        contentType: 'application/json; charset=UTF-8',
    }).done(function (beanCrudResponse) {
        if (beanCrudResponse.beanPagination !== null) {
            beanPaginationCursoUnico = beanCrudResponse.beanPagination;
            if (parseInt(beanPaginationCursoUnico.countFilter) == 1) {
                setCookieSessionLibro(beanPaginationCursoUnico.list[0].libro.codigo);
                window.location.href = getHostFrontEnd() + "aula/home";
            } else {
                listaCurso(beanPaginationCursoUnico);
            }


        }
    }).fail(function (err) {

        showAlertErrorRequest();
    });

}

function listaCurso(beanPagination) {

    let row = "";
    document.querySelector("#countCurso").innerHTML = "Tenemos " + beanPagination.countFilter + "cursos que te van a gustar";
    beanPagination.list.forEach((curso) => {

        row += `
    <div class="item block ver-detalle mx-auto" codigocurso="${curso.libro.codigo}">
    <div class="thumbs-wrapper">
        <div class="thumbs">
            <img style="width: 100%;height: 315px;"
                src="${getHostFrontEnd()}adjuntos/libros/${curso.libro.imagen}" />

        </div>
    </div>
    <h2 class="text-center">${curso.libro.nombre}</h2>

    <div class="intro">
       
      
        <a href="${getHostFrontEnd()}aula/home"
            class="btn btn-purple-o border-radius f-weight-700 py-1 px-4" style="float: right;">Ir al Curso </a>
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
            setCookieSessionLibro(btn.getAttribute("codigocurso"));
            window.location.href = getHostFrontEnd() + "aula/home";

        };
    });
}