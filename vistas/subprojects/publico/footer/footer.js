
var beanPaginationFooterPublico;
var footerPublicoSelected;
var beanRequestFooterPublico = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {

    beanRequestFooterPublico.entity_api = 'empresa';
    beanRequestFooterPublico.operation = 'obtener';
    beanRequestFooterPublico.type_request = 'GET';


});



function listaFooterPublico(beanPagination) {

    if (beanPagination.length == 0) {
        return;
    }
    document.querySelector("#footerPublico").classList.remove("d-none");

    document.querySelector('#visitaContador').innerHTML = beanPagination.countFilter;
    beanPagination.list.forEach((empresa) => {
        footerPublicoSelected = empresa;
    });
    if (document.querySelector('#empresa-historia')) {
        document.querySelector('#empresa-historia').innerHTML = footerPublicoSelected.descripcion;
    }
    /*
    if (document.querySelector('.precio-curso')) {
        document.querySelector('.precio-curso').innerHTML = `<span style="font-weight: 100;color: #8c8b8b;">$</span>${footerPublicoSelected.precio}<span style="font-size: 25px;
    font-weight: 700;">USD</span>`;
    }
*/
    document.querySelector('#footerCelular').setAttribute("href", "https://web.whatsapp.com/send?phone=" + footerPublicoSelected.telefono);
    document.querySelector('.empresa-logo').innerHTML = `<img src="${getHostFrontEnd() + "adjuntos/" + footerPublicoSelected.logo}" alt="Light logo" style="height: 110px;" />`;
    document.querySelector('.empresa-instagram').innerHTML = `<a href="${footerPublicoSelected.instagram}"  target="_blank" style="border: none;"><img src="${getHostFrontEnd() + "vistas/assets/img/instagram.jpg"}" alt="Light logo" style="width: 2.3em;border-radius: 8px;margin-top: -6px;" /></a>`;

    document.querySelector('.empresa-email').innerHTML = footerPublicoSelected.email;
    document.querySelector('.empresa-email').setAttribute("href", "mailto:" + footerPublicoSelected.email);
    document.querySelector('.empresa-frase').innerHTML = footerPublicoSelected.frase;
    document.querySelector('.empresa-facebook').innerHTML = `<a href="${footerPublicoSelected.facebook}" class="facebook" target="_blank"><i class="fa fa-facebook"></i></a>`;
    document.querySelector('.empresa-youtube').innerHTML = `<a href="${footerPublicoSelected.youtube}" class="youtube" target="_blank"><i class="fa fa-youtube"></i></a>`;

}
