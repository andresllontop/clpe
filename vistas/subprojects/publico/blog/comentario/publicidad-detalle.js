var beanPaginationPublicidad;
var publicidadSelected;
var beanRequestPublicidad = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestPublicidad.entity_api = 'subitems';
    beanRequestPublicidad.operation = 'paginate';
    beanRequestPublicidad.type_request = 'GET';

});



function listaPublicidad(beanPagination) {
    if (beanPagination.countFilter == 0) {
        return;
    }
    let row = "", contador = 0, contadorInterno = 0, contadorDivi = 3, mult3 = true, mult2 = true;

    beanPagination.list.forEach((publicidad) => {
        contador++;
        contadorInterno++;
        if (contadorDivi % 3 == 0) {
            contadorDivi = 3;
            if (mult3) {
                row += ` 
                <li>
                <ul class="posts">`;
                row += ` 
                <li> 
                    <a href="${publicidad.detalle}" class="">
                    <img src="${getHostFrontEnd()}adjuntos/slider/${publicidad.imagen}" />
                    </a>
                </li>`;
                mult3 = false;
            } else {
                row += ` 
                <li>  <a href="${publicidad.detalle}" class="">
                <img src="${getHostFrontEnd()}adjuntos/slider/${publicidad.imagen}" />
                </a>
                </li>`;
            }
            if (contadorInterno % 3 == 0 || beanPaginationPublicidad.list.length == contador) {
                contadorInterno = 0;
                mult3 = true;
                contadorDivi = 1;
                row += ` 
                </ul>
        </li>`;
            }
        } else {
            if (mult2) {
                row += ` 
                <li>
                <ul class="posts">`;
                row += ` 
                <li> 
                <a href="${publicidad.detalle}" class="">
                <img src="${getHostFrontEnd()}adjuntos/slider/${publicidad.imagen}" />
                </a>
                </li>`;
                mult2 = false;
            } else {
                row += ` 
                <li> <a href="${publicidad.detalle}" class="">
                <img src="${getHostFrontEnd()}adjuntos/slider/${publicidad.imagen}" />
                </a>
                </li>`;
            }
            if (contadorInterno % 3 == 0 || beanPaginationPublicidad.list.length == contador) {
                contadorInterno = 0;
                mult2 = true;
                contadorDivi = 3;
                row += ` 
                </ul>
        </li>`;
            }
        }

    });

    document.querySelector('#carousel-5').innerHTML = row;
    //carrosel();
}

function carrosel() {
    $("#carousel-5").owlCarousel({
        autoPlay: true,
        singleItem: true,
        transitionStyle: "goDown",
        slideSpeed: 40000,
        touchDrag: false,
        mouseDrag: false
    });

}