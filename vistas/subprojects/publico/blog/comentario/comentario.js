
var beanPaginationBlog;
var blogSelected;
var beanRequestBlog = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    document.body.style.background = "#f7f7f7 url(" + getHostFrontEnd() + "vistas/subprojects/publico/blog/img/pattern.png) repeat top left";
    beanRequestBlog.entity_api = 'blog';
    beanRequestBlog.operation = 'get';
    beanRequestBlog.type_request = 'GET';

    let GETsearch = window.location.pathname;
    if (GETsearch.split("/").length == 5) {
        if (/^[0-9.]*$/.test(GETsearch.split("/")[4])) {
            blogSelected = { "idblog": GETsearch.split("/")[4] };

            let fetOptions = {
                headers: {
                    "Content-Type": 'application/json; charset=UTF-8',
                    //"Authorization": "Bearer " + token
                },
                method: "GET",
            }
            /* PROMESAS LLAMAR A LAS API*/
            circleCargando.containerOcultar = $(document.querySelector("#txtBlogComentario"));
            circleCargando.container = $(document.querySelector("#txtBlogComentario").parentElement);
            circleCargando.createLoader();
            circleCargando.toggleLoader("show");
            Promise.all([
                fetch(getHostAPI() + beanRequestBlog.entity_api + "/" + beanRequestBlog.operation +
                    '?id=' + parseInt(blogSelected.idblog), fetOptions),
                fetch(getHostAPI() + "subitems/paginate" +
                    '?tipo=5&pagina=1&registros=20', fetOptions),
                fetch(getHostAPI() + "empresa/obtener" +
                    "?filtro=&pagina=1&registros=1", fetOptions)
            ])
                .then(responses => Promise.all(responses.map((res) => res.json())))
                .then(json => {
                    circleCargando.toggleLoader("hide");
                    if (json[0].beanPagination !== null) {
                        beanPaginationBlog = json[0].beanPagination;
                        listaBlog(beanPaginationBlog);
                    }
                    if (json[1].beanPagination !== null) {
                        beanPaginationPublicidad = json[1].beanPagination;
                        listaPublicidad(beanPaginationPublicidad);
                    }
                    if (json[2].beanPagination !== null) {
                        beanPaginationFooterPublico = json[2].beanPagination;
                        listaFooterPublico(beanPaginationFooterPublico);
                    }

                })
                .catch(err => {
                    console.log(err);
                    showAlertErrorRequest();
                });
            /* */
        } else {
            window.location.href = getHostFrontEnd() + "blog";
        }

    } else {
        window.location.href = getHostFrontEnd() + "blog";
    }

});


function listaBlog(beanPagination) {
    document.querySelector('#txtBlogComentario').innerHTML = '';

    let row = "";

    if (beanPagination.length == 0) {
        return;
    }
    let $fragmentMeta = document.createDocumentFragment();
    let $meta = document.createElement("meta");
    $meta.setAttribute("property", "og:locale");
    $meta.setAttribute("content", "en_ES");
    $fragmentMeta.appendChild($meta);
    //
    $meta = document.createElement("meta");
    $meta.setAttribute("property", "og:type");
    $meta.setAttribute("content", "article");
    $fragmentMeta.appendChild($meta);
    //
    $meta = document.createElement("meta");
    $meta.setAttribute("property", "og:url");
    $meta.setAttribute("content", window.location.href);
    $fragmentMeta.appendChild($meta);
    //
    $meta = document.createElement("meta");
    $meta.setAttribute("property", "og:site_name");
    $meta.setAttribute("content", "CLUB DE LECTURA PARA EMPRENDEDORES");
    $fragmentMeta.appendChild($meta);
    //
    $meta = document.createElement("meta");
    $meta.setAttribute("property", "article:publisher");
    // $meta.setAttribute("content", "https://www.facebook.com/andres.llontop.1297");
    $meta.setAttribute("content", "https://www.facebook.com/zrii.c.peru");
    $fragmentMeta.appendChild($meta);
    //
    $meta = document.createElement("meta");
    $meta.setAttribute("property", "article:author");
    $meta.setAttribute("content", "https://www.facebook.com/zrii.c.peru");
    $fragmentMeta.appendChild($meta);
    document.querySelector("head").appendChild($fragmentMeta);
    //
    $("body").prepend('<script async defer crossorigin="anonymous" src="https://connect.facebook.net/es_ES/sdk.js#xfbml=1&version=v3.3"></script>');
    $("body").prepend('<div id="fb-root"></div>');

    //
    $meta = document.createElement("div");
    $meta.setAttribute("class", "fb-comments");
    $meta.setAttribute("data-href", window.location.href);
    $meta.setAttribute("data-width", "100%");
    $meta.setAttribute("data-numposts", "5");
    document.querySelector("#txtBlogComentario").parentElement.appendChild($meta);

    beanPagination.list.forEach((blog) => {
        $meta = document.createElement("meta");
        $meta.setAttribute("property", "og:title");
        $meta.setAttribute("content", blog.titulo);
        $fragmentMeta.appendChild($meta);
        //
        $meta = document.createElement("meta");
        $meta.setAttribute("property", "og:description");
        $meta.setAttribute("content", blog.descripcion);
        $fragmentMeta.appendChild($meta);


        document.querySelector("#txtTituloComentario").innerHTML = blog.titulo;
        if (parseInt(blog.tipoArchivo) === 1) {
            row += ` <span class="image anim fadeIn"><img src="${getHostFrontEnd()}adjuntos/blog/IMAGENES/${blog.archivo}"  alt="${blog.archivo}"/> </span>`;
            //
            $meta = document.createElement("meta");
            $meta.setAttribute("property", "og:image");
            $meta.setAttribute("content", getHostFrontEnd() + "adjuntos/blog/IMAGENES/" + blog.archivo);
            $fragmentMeta.appendChild($meta);
        } else {
            row += `<span class="image anim fadeIn"><video controls loop data-smart-video autoplay style="width:100%;height:100%;" src="${getHostFrontEnd()}adjuntos/blog/VIDEOS/${blog.archivo}"></video> </span>`;
        }
        row += ` <p class="anim fadeIn">${blog.descripcion}</p>
        `;



        document.querySelector("head").appendChild($fragmentMeta);
    });

    document.querySelector('#txtBlogComentario').innerHTML = row;

    smartVideo();

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

