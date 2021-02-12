$(document).ready(function () {
    let pag = 1;
    let total = 9;
    listar(pag, total);
    $("#paginador ul > li").click(function () {
        // $("#paginador ul > li ").attr("active", false);
        $("#paginador ul > li ").removeClass("active");
        pag = $(this)
            .find("a")
            .text();
        // $(this).attr("active", true);
        let pagina = $(this)
            .prev()
            .text();
        let ultimo = $("#paginador ul > li")
            .last()
            .prev()
            .find("a")
            .text();
        if (pag == "Siguiente") {
            $(this)
                .prev()
                .prev()
                .prev()
                .prev()
                .removeClass("disabled");
            $(this)
                .prev()
                .prev()
                .prev()
                .find("a")
                .text(parseInt(pagina) - 1);
            $(this)
                .prev()
                .prev()
                .find("a")
                .text(parseInt(pagina));
            $(this)
                .prev()
                .find("a")
                .text(parseInt(pagina) + 1);
            $(this)
                .prev()
                .addClass("active");
            pag = parseInt(pagina) + 1;
            listar(parseInt(pagina) + 1, total);
        } else if (pag == "Ninguno" && ultimo > 3) {
            // ("ultimo:" + parseInt(ultimo));
            // ("actual:" + pag);
            $(this)
                .prev()
                .prev()
                .prev()
                .find("a")
                .text(parseInt(pagina) - 3);
            $(this)
                .prev()
                .prev()
                .find("a")
                .text(parseInt(pagina) - 2);
            $(this)
                .prev()
                .find("a")
                .text(parseInt(pagina) - 1);
            $(this)
                .prev()
                .addClass("active");

            $("#paginador ul > li")
                .last()
                .find("a")
                .text("Siguiente");
            pag = parseInt(
                $("#paginador ul > li")
                    .last()
                    .prev()
                    .find("a")
                    .text()
            );
            listar(
                parseInt(
                    $("#paginador ul > li")
                        .last()
                        .prev()
                        .find("a")
                        .text()
                ),
                total
            );
        } else if (pag == "Ninguno" && ultimo == 3) {
            $(this)
                .prev()
                .prev()
                .prev()
                .find("a")
                .text(parseInt(ultimo) - 2);
            $(this)
                .prev()
                .prev()
                .find("a")
                .text(parseInt(ultimo) - 1);
            $(this)
                .prev()
                .find("a")
                .text(parseInt(ultimo));
            $(this)
                .prev()
                .addClass("active");
            pag = parseInt(ultimo);
            listar(parseInt(ultimo), total);
        } else if (pag == "Anterior" && ultimo > 3) {
            // ("ultimo:" + parseInt(ultimo));
            // ("actual:" + pag);
            $(this)
                .next()
                .find("a")
                .text(parseInt(ultimo) - 3);
            $(this)
                .next()
                .next()
                .find("a")
                .text(parseInt(ultimo) - 2);
            $(this)
                .next()
                .next()
                .next()
                .find("a")
                .text(parseInt(ultimo) - 1);
            $(this)
                .next()
                .addClass("active");
            $("#paginador ul > li")
                .last()
                .find("a")
                .text("Siguiente");
            pag = parseInt(ultimo) - 3;
            listar(parseInt(ultimo) - 3, total);
        } else if (pag == "Anterior" && ultimo == 3) {
            $(this)
                .next()
                .addClass("active");
            $(this)
                .next()
                .find("a")
                .text(parseInt(ultimo) - 2);
            $(this)
                .next()
                .next()
                .find("a")
                .text(parseInt(ultimo) - 1);
            $(this)
                .next()
                .next()
                .next()
                .find("a")
                .text(parseInt(ultimo));
            $(this)
                .next()
                .addClass("active");
            $("#paginador ul > li")
                .last()
                .find("a")
                .text("Siguiente");

            pag = parseInt(ultimo) - 2;
            listar(parseInt(ultimo) - 2, total);
        } else {
            $("#paginador ul > li")
                .last()
                .removeClass("active");
            $(this).addClass("active");
            $("#paginador ul > li")
                .last()
                .find("a")
                .text("Siguiente");
            listar(pag, total);
        }
    });
    $("#btnAbrirCliente").click(function () {
        $("#formularioCliente").attr("data-form", "save");
        $("#tituloModalManCliente").html("REGISTRAR ALUMNO");
        $("#ventanaModalManCliente").modal("show");
    });

    function listar(paginas, registrototal) {
        $.ajax({
            type: "GET",
            url: url + "ajax/blogAjax.php",
            data: {
                acion: "listar",
                pagina: paginas,
                registros: registrototal
            },
            xhr: function () {
                var xhr = new window.XMLHttpRequest();
                //Upload progress, request sending to server
                xhr.upload.addEventListener(
                    "progress",
                    function (evt) {
                        console.log("in Upload progress");
                        console.log("Upload Done");
                    },
                    false
                );
                //Download progress, waiting for response from server
                xhr.addEventListener(
                    "progress",
                    function (e) {
                        console.log("in Download progress");
                        if (e.lengthComputable) {
                            //percentComplete = (e.loaded / e.total) * 100;
                            percentComplete = parseInt((e.loaded / e.total) * 100, 10);
                            console.log(percentComplete);
                            $("#bulk-action-progbar").data("aria-valuenow", percentComplete);
                            $("#bulk-action-progbar").css("width", percentComplete + "%");
                        } else {
                            console.log("Length not computable.");
                        }
                    },
                    false
                );
                return xhr;
            },
            success: function (respuesta) {
                if (respuesta == "ninguno") {
                    $("#paginador ul > li")
                        .last()
                        .find("span")
                        .text("Ninguno");
                    $("#paginador ul > li")
                        .last()
                        .prev()
                        .removeClass("active");
                    $("#paginador ul > li")
                        .last()
                        .addClass("active");

                    listar(pag - 1, total);
                } else {
                    let admin = JSON.parse(respuesta);
                    let html = "";
                    let html2 = "";
                    let contador = 0;
                    for (var key in admin) {
                        contador++;
                        console.log(contador);

                        switch (admin[key]["tipoArchivo"]) {
                            case "1":
                                html2 = `
                    <span class="image2">
                    <a href="${url}single/${
                                    admin[key].idblog
                                    }" id="desc" data-icon="fa-link">
                        <img src="${url}/adjuntos/blog/IMAGENES/${
                                    admin[key].archivo
                                    }" alt="Blog Sample Image" />
                    </a>
                </span>`;
                                break;
                            case "2":
                                html2 = `
                  <span class="image2">
                  <video alt="user-picture"  
                  style="width:100%;height:100%;" controls ><source  
                  src="${url}adjuntos/blog/VIDEOS/${admin[key].archivo}" 
                  type="video/mp4"></video>
              </span>`;
                                break;
                        }

                        if (contador == 1 || contador == 4 || contador == 7) {
                            html += `<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">`;
                        }

                        html += ` 
            <div class="blog anim fadeInLeft" style="border-bottom: solid 0.5px rgba(74, 73, 73, 0.8);">${html2}
            <span class="title-desc">
                <h3 class="text-center">${admin[key].titulo}</h3>
            </span>
            ${admin[key].resumen}[...]      
            <span class="title-desc">
            <a class="btn btn-sm btn-primary icon" data-wow-delay=".45s" role="button" href="${url}single/${
                            admin[key].idblog
                            }"><i class="fa fa-long-arrow-right"></i>Leer más</a>
            </span>
                <div class="clearfix"></div>

            </div><!-- .blosg -->
        `;
                        if (contador == 3 || contador == 6 || contador == 9) {
                            html += `</div>`;
                        }
                        $(".RespuestaLista").html(html);
                    }
                }
            },
            error: function (e) {
                swal(
                    "Ocurrió un error inesperado",
                    "Por favor recargue la página",
                    "error"
                );
            }
        });
        return false;
    }
});
