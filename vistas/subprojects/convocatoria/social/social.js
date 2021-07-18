
var beanPaginationSocial, beanPaginationTestimonio;
var socialSelected, testimonioSelected;
var beanRequestSocial = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    document.body.style.background = "#f7f7f7 url(" + getHostFrontEnd() + "vistas/subprojects/publico/social/img/pattern.png) repeat top left";
    beanRequestSocial.entity_api = 'social';
    beanRequestSocial.operation = 'get';
    beanRequestSocial.type_request = 'GET';

    let GETsearch = window.location.pathname;
    if (GETsearch.split("/").length == 5) {
        if (/^[0-9.]*$/.test(GETsearch.split("/")[4])) {
            socialSelected = { "idsocial": GETsearch.split("/")[4] };

            let fetOptions = {
                headers: {
                    "Content-Type": 'application/json; charset=UTF-8',
                    //"Authorization": "Bearer " + token
                },
                method: "GET",
            }
            /* PROMESAS LLAMAR A LAS API*/
            circleCargando.containerOcultar = $(document.querySelector("#txtSocialComentario"));
            circleCargando.container = $(document.querySelector("#txtSocialComentario").parentElement);
            circleCargando.createLoader();
            circleCargando.toggleLoader("show");
            Promise.all([
                fetch(getHostAPI() + beanRequestSocial.entity_api + "/" + beanRequestSocial.operation +
                    '?id=' + parseInt(socialSelected.idsocial), fetOptions),

                fetch(getHostAPI() + "testimonios/paginate" +
                    '?filtro=&pagina=1&registros=9', fetOptions),
                fetch(getHostAPI() + "cursos/paginate" +
                    '?filtro=&pagina=1&registros=20', fetOptions)
            ])
                .then(responses => Promise.all(responses.map((res) => res.json())))
                .then(json => {
                    circleCargando.toggleLoader("hide");
                    if (json[0].beanPagination !== null) {
                        beanPaginationSocial = json[0].beanPagination;
                        listaSocial(beanPaginationSocial);
                    }

                    if (json[1].beanPagination !== null) {
                        beanPaginationTestimonio = json[1].beanPagination;
                        listaTestimonio(beanPaginationTestimonio);
                    }
                    if (json[2].beanPagination !== null) {
                        listaCurso(json[2].beanPagination);
                    }

                })
                .catch(err => {
                    console.log(err);
                });
            /* */
        } else {
            window.location.href = getHostFrontEnd();
        }

    } else {
        window.location.href = getHostFrontEnd();
    }
    // Create the homepage down pointer thing
    var chevronDown = $(".slider-wrapper i.fa#go-down");
    if (chevronDown.length) {
        function animate() {
            $(chevronDown).animate({
                bottom: '35px',
                paddingBottom: "20px",
                opacity: .1
            }, 1000, "easeOutExpo", function () {
                $(this).animate({
                    bottom: "15px",
                    paddingBottom: "0",
                    opacity: .5
                }, 1000, "easeOutBounce");
            });
        }
        setTimeout(function () {
            $(chevronDown).css({
                bottom: '35px',
                opacity: 0,
                display: 'block'
            });
            setInterval(animate, 2000);
        }, 3000);
    }

});


function listaSocial(beanPagination) {
    document.querySelector('#txtSocialComentario').innerHTML = '';

    if (beanPagination.list.length == 0) {
        window.location.href = getHostFrontEnd();
    }
    beanPagination.list.forEach((social) => {
        document.querySelector("#txtSocialTitulo").innerHTML = social.titulo;
        document.querySelector("#txtSocialComentario").innerHTML = social.descripcion;
        document.querySelector("#txtfraseTestimonio").innerHTML = social.fraseTestimonio;
        document.querySelector("#txtfraseCurso").innerHTML = social.fraseCurso;
        if (parseInt(social.tipoArchivo) === 1) {
            document.querySelector("#imgSlug").setAttribute('src', `${getHostFrontEnd()}adjuntos/social/img/${social.archivo}`);
            document.querySelector("#imgSlug").setAttribute('alt', social.archivo);

        } else {
            document.querySelector("#txtSocialVideo").innerHTML = `<video controls loop data-smart-video autoplay muted style="width:70%;" src="${getHostFrontEnd()}adjuntos/social/video/${social.archivo}"></video> `;
            smartVideo();
            document.querySelector("#imgSlug").setAttribute('src', `${getHostFrontEnd()}adjuntos/social/img/${social.imagenFondo}`);
            document.querySelector("#imgSlug").setAttribute('alt', social.imagenFondo);
        }

    });




}

function listaTestimonio(beanPagination) {
    let row = "", contador = 0;
    if (beanPagination.list.length == 0) {
        addClass(document.querySelector("#cargarTestimonio"), "d-none");
        contadorTestimonio = 0;
        return;
    }
    beanPagination.list.forEach((testimonio) => {
        if (contador % 3 == 0) {
            row += `  <hr style="border-color: #c100c1;border-width: 3px;width: 98%;">`;
        }
        contador++;
        row += ` 
                  <div class="col-sm-4 ver-testimonio aula-cursor-mano" idtestimonio="${testimonio.idtestimonio}">
                      <div style="position:relative;">
                      <img class="w-100 col-6"
                          src="${getHostFrontEnd()}adjuntos/testimonio/${testimonio.imagen}"
                          alt="${testimonio.titulo}"><span style="position: absolute;right: 50%;top: 50%;" class="ver-testimonio" idtestimonio="${testimonio.idtestimonio}"><i class="zmdi zmdi-youtube-play text-danger zmdi-hc-2x" ></i></span>
                      </div>
                      <div class="aula-cursor-mano text-truncate">
                          <div class="content-inner">
                              <h5 class="text-purple">${testimonio.titulo}</h5>
                              <div>${testimonio.descripcion}</div>
                          </div>
                      </div>
  
                  </div>
                  
              `;

    });

    document.querySelector('#tbodyTestimonio').innerHTML += row;
    addEventsButtonsTestimonio();

}

function addEventsButtonsTestimonio() {
    document.querySelectorAll('.ver-testimonio').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            testimonioSelected = findByTestimonio(
                btn.getAttribute('idtestimonio')
            );

            if (testimonioSelected != undefined) {

                document.querySelector("#txtvideoTestimonio").innerHTML = testimonioSelected.enlaceYoutube;
                document.querySelector("#txtvideoTestimonio").firstChild.classList.add("img-respons-v");

                $("#modalVideoTestimonio").modal("show");

            } else {
                document.querySelector("#txtvideoTestimonio").innerHTML = "";
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });

}

function findByTestimonio(idtestimonio) {
    return beanPaginationTestimonio.list.find(
        (Testimonio) => {
            if (parseInt(idtestimonio) == Testimonio.idtestimonio) {
                return Testimonio;
            }


        }
    );
}

function listaCurso(beanPagination) {

    let row = "";

    beanPagination.list.forEach((curso) => {
        if ((beanPaginationSocial.list[0].parametroCurso).includes("" + curso.idcurso)) {
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
                <h4 style="color:#66398e;display: contents;">USD ${curso.precio}</h4>
                <button class="btn btn-purple-o border-radius f-weight-700 py-1 px-4" style="float: right;">${curso.tipo == 1 ? '<i class="zmdi zmdi-shopping-cart"></i>Comprar' : '<i class="zmdi zmdi-comment-video"></i> Vía Zoom'} </button>
            </div>
        </div>
        `;
        }

    });

    document.querySelector('#tbodyCurso').innerHTML += row;
    document.querySelectorAll('.ver-detalle').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {

            // Abrir nuevo tab
            var win = window.open(getHostFrontEnd() + "matricula/detalle/" + btn.getAttribute('idcurso'), '_blank');
            // Cambiar el foco al nuevo tab (punto opcional)
            win.focus();
        };
    });

}

function smartVideo() {
    let videos = document.querySelectorAll("video[data-smart-video]");
    const cb = function (entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.play();
            } else {
                entry.target.pause();
            }
            window.addEventListener("visibilitychange", (e) => {
                document.visibilityState == "visible" ? entry.target.play() : entry.target.pause();
            });
        });
    };
    let observer = new IntersectionObserver(cb, { threshold: 0.5 });
    videos.forEach(element => {
        observer.observe(element);
    });
}

