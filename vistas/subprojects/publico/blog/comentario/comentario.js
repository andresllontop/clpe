
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
    document.querySelector(".fb-comments").setAttribute("data-href", window.location.href);

});


function listaBlog(beanPagination) {
    document.querySelector('#txtBlogComentario').innerHTML = '';

    if (beanPagination.length == 0) {
        return;
    }
    /*
    $("body").prepend('<script async defer crossorigin="anonymous" src="https://connect.facebook.net/es_ES/sdk.js#xfbml=1&version=v3.3"></script>');
    $("body").prepend('<div id="fb-root"></div>');

    */
    let $fragmentMeta = "";
    $fragmentMeta = `
    <meta property="og:locale" content="en_ES">
    <meta property="og:type" content="article">
    <meta property="og:url" content="${window.location.href}">
    `;
    let $meta;

    beanPagination.list.forEach((blog) => {
        $fragmentMeta += `
        <meta property="og:title" content="${blog.titulo}">
        <meta property="og:description" content="${blog.resumen}">
        `;

        ARTICLE_TITLE = blog.titulo;
        ARTICLE_DESC = blog.resumen;
        ARTICLE_URL = window.location.href;
        MAIN_IMAGE_URL = getHostFrontEnd() + "adjuntos/blog/IMAGENES/" + blog.foto;
        document.querySelector("#txtTituloComentario").innerHTML = blog.titulo;
        if (parseInt(blog.tipoArchivo) === 1) {

            $fragmentMeta += `
            <meta property="og:image" content="${getHostFrontEnd() + "adjuntos/blog/IMAGENES/" + blog.archivo}">
            `;

            $meta = document.createElement("div");
            $meta.innerHTML = ` <span class="image anim fadeIn"><img src="${getHostFrontEnd()}adjuntos/blog/IMAGENES/${blog.archivo}"  alt="${blog.archivo}"/> </span>`;

            document.querySelector("#txtBlogComentario").appendChild($meta);

        } else {
            $meta = document.createElement("div");
            $meta.innerHTML = `<span class="image anim fadeIn"><video controls loop data-smart-video autoplay style="width:100%;height:100%;" src="${getHostFrontEnd()}adjuntos/blog/VIDEOS/${blog.archivo}"></video> </span>`;

            document.querySelector("#txtBlogComentario").appendChild($meta);
        }

        if (blog.foto != null) {
            $meta = document.createElement("div");
            $meta.setAttribute("class", "anim fadeIn d-inline-flex");
            $meta.innerHTML = `<span class="pr-5" style="margin-top: 25px;"><img src="${getHostFrontEnd()}adjuntos/blog/IMAGENES/${blog.foto}"style="width: 7em;"  alt="${blog.foto}"/> <figcaption class="text-center">${blog.autor}</figcaption></span>
            <section>${blog.descripcionAutor}</section>`;
            document.querySelector("#txtBlogComentario").appendChild($meta);

        }


        $meta = document.createElement("section");
        $meta.setAttribute("class", "anim fadeIn");
        $meta.innerHTML = blog.descripcion;
        document.querySelector("#txtBlogComentario").appendChild($meta);
        document.querySelectorAll("head > meta")[6].insertAdjacentHTML('afterend', $fragmentMeta);

    });


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

