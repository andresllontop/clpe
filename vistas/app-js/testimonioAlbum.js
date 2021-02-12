$(document).ready(function () {
    let ajax_load =
        "<div class='progress'>" +
        "<div id='progress_id' class='progress-bar progress-bar-striped active' " +
        "role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width: 45%'>" +
        "n/100</div></div>";
    let pag = 1;
    let total = 5;
    listar(pag, total);
    $("#paginador ul > li").click(function () {
        // $("#paginador ul > li ").attr("active", false);
        $("#paginador ul > li ").removeClass("active");
        pag = $(this)
            .find("span")
            .text();
        // $(this).attr("active", true);
        let pagina = $(this)
            .prev()
            .text();
        let ultimo = $("#paginador ul > li")
            .last()
            .prev()
            .find("span")
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
                .find("span")
                .text(parseInt(pagina) - 1);
            $(this)
                .prev()
                .prev()
                .find("span")
                .text(parseInt(pagina));
            $(this)
                .prev()
                .find("span")
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
                .find("span")
                .text(parseInt(pagina) - 3);
            $(this)
                .prev()
                .prev()
                .find("span")
                .text(parseInt(pagina) - 2);
            $(this)
                .prev()
                .find("span")
                .text(parseInt(pagina) - 1);
            $(this)
                .prev()
                .addClass("active");

            $("#paginador ul > li")
                .last()
                .find("span")
                .text("Siguiente");
            pag = parseInt(
                $("#paginador ul > li")
                    .last()
                    .prev()
                    .find("span")
                    .text()
            );
            listar(
                parseInt(
                    $("#paginador ul > li")
                        .last()
                        .prev()
                        .find("span")
                        .text()
                ),
                total
            );
        } else if (pag == "Ninguno" && ultimo == 3) {
            $(this)
                .prev()
                .prev()
                .prev()
                .find("span")
                .text(parseInt(ultimo) - 2);
            $(this)
                .prev()
                .prev()
                .find("span")
                .text(parseInt(ultimo) - 1);
            $(this)
                .prev()
                .find("span")
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
                .find("span")
                .text(parseInt(ultimo) - 3);
            $(this)
                .next()
                .next()
                .find("span")
                .text(parseInt(ultimo) - 2);
            $(this)
                .next()
                .next()
                .next()
                .find("span")
                .text(parseInt(ultimo) - 1);
            $(this)
                .next()
                .addClass("active");
            $("#paginador ul > li")
                .last()
                .find("span")
                .text("Siguiente");
            pag = parseInt(ultimo) - 3;
            listar(parseInt(ultimo) - 3, total);
        } else if (pag == "Anterior" && ultimo == 3) {
            $(this)
                .next()
                .addClass("active");
            $(this)
                .next()
                .find("span")
                .text(parseInt(ultimo) - 2);
            $(this)
                .next()
                .next()
                .find("span")
                .text(parseInt(ultimo) - 1);
            $(this)
                .next()
                .next()
                .next()
                .find("span")
                .text(parseInt(ultimo));
            $(this)
                .next()
                .addClass("active");
            $("#paginador ul > li")
                .last()
                .find("span")
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
                .find("span")
                .text("Siguiente");
            listar(pag, total);
        }
    });
    $(".dropdown-menu >button").click(function () {
        total = $(this).text();
        listar(pag, total);
    });
    function filePreview(input, imagen) {
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            imagen;
            reader.onload = function (e) {
                $(imagen).html(
                    "<img width='244' alt='user-picture' class='img-responsive center-box' src='" +
                    e.target.result +
                    "' />"
                );
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#Imagen-reg").change(function () {
        filePreview(this, "#imagePreview");
    });

    $("#btnAbrirbook").click(function () {
        $(".FormularioAjax")[0].reset();
        $("#imagePreview").html("");
        $("#formularioLibro").attr("data-form", "save");
        $("#tituloModalManlibro").html("REGISTRAR ALBUM");
        $("#ventanaModalManlibro").modal("show");
    });

    $(".FormularioAjax").submit(function (e) {
        $("#insertarModal").modal("hide");
        $("#modificarModal").modal("hide");
        e.preventDefault();
        let form = $(this);
        let tipo = form.attr("data-form");
        let metodo = form.attr("method");
        let formdata = new FormData(this);
        formdata.append("accion", tipo);
        var file = $("#Imagen-reg")[0].files[0];
        tipo;
        if (file) {
            $("#cargarpagina").html(ajax_load);
            ProcesarAjax(metodo, formdata);
        }
    });
    function ProcesarAjax(metodo, formdata) {
        $.ajax({
            type: metodo,
            url: url + "ajax/albumAjax.php",
            data: formdata,
            cache: false,
            contentType: false,
            processData: false,
            xhr: function () {
                let xhr = $.ajaxSettings.xhr();
                xhr.onprogress = function (evt) {
                    let porcentaje = Math.floor((evt.loaded / evt.total) * 100);
                    $("#progress_id").text(porcentaje + "/100");
                    $("#progress_id").attr("aria-valuenow", porcentaje);
                    $("#progress_id").css("width", porcentaje + "%");
                };
                return xhr;
            },
            success: function (data) {
                data;
                $("#cargarpagina").html("");
                $("#ventanaModalManlibro").modal("hide");
                swal({
                    title: JSON.parse(data).Titulo,
                    text: JSON.parse(data).Texto,
                    type: JSON.parse(data).Tipo,
                    confirmButtonText: "Aceptar"
                });
                listar(pag, total);
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
    function listar(paginas, registrototal) {
        $.ajax({
            type: "GET",
            url: url + "ajax/albumAjax.php",
            data: { acion: "listar", pagina: paginas, registros: registrototal },
            xhr: function () {
                let xhr = $.ajaxSettings.xhr();
                xhr.onprogress = function (evt) {
                    let porcentaje = Math.floor((evt.loaded / evt.total) * 100);
                    $("#progress_id").text(porcentaje + "/100");
                    $("#progress_id").attr("aria-valuenow", porcentaje);
                    $("#progress_id").css("width", porcentaje + "%");
                };
                return xhr;
            },
            success: function (respuesta) {
                respuesta;
                // $("#cargarpaginalista").html("");
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
                    let html = `<td colspan="6" class="text-center ">No hay registros</td>`;
                    $(".RespuestaLista").html(html);
                } else {
                    let videos = JSON.parse(respuesta);
                    let html = "";
                    let contador = 0;
                    for (let key in videos) {
                        contador++;
                        html += `<tr numero="${contador}" id="${videos[key].idalbum}" >
          <td class="text-center ">${contador}</td>
          <td  class="text-center "><img 
            src="${url}adjuntos/album/${videos[key].imagen}"
            alt="user-picture"
            class="img-responsive center-box" style="width:300px;height:150px;"
            /><div class="imag">${videos[key].imagen}</div></td>
          <td class="text-center ">
              <button class="btn btn-info editar-Admin" ><i class="zmdi zmdi-refresh"></i> </button>
          </td>
          <td class="text-center">
               <button class="btn btn-danger eliminar-Admin"><i class="zmdi zmdi-delete"></i></button>
          </td>
          </tr>`;
                        $(".RespuestaLista").html(html);
                    }
                    addEventsButtonsAdmin();
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
    function addEventsButtonsAdmin() {
        $(".editar-Admin").each(function (index, value) {
            $(this).click(function () {
                let indice = $(this.parentElement.parentElement).attr("numero");
                var rowvalue = [];
                var rowvalue2 = [];
                $("tbody > tr").each(function (i, v) {
                    rowvalue[i] = $("td", this)
                        .map(function () {
                            return $(this).html();
                        })
                        .get();
                    rowvalue2[i] = $("td >.imag", this)
                        .map(function () {
                            return $(this).text();
                        })
                        .get();
                });
                for (let index = 0; index < rowvalue.length; index++) {
                    if (rowvalue[index][0] == indice) {
                        indice = index + 1;
                    }
                }
                $("#ID-reg").val($(this.parentElement.parentElement).attr("id"));
                $("#Nombre-reg").val(rowvalue[indice - 1][1]);
                $("#imagePreview").html(
                    "<img  alt='user-picture' class='img-responsive  center-box'style='width:300px;height:150px;' src='" +
                    url +
                    "adjuntos/album/" +
                    rowvalue2[indice - 1][0] +
                    "' />"
                );

                $("#formularioLibro").attr("data-form", "update");
                $("#tituloModalManlibro").html("EDITAR ALBUM");
                $("#ventanaModalManlibro").modal("show");
            });
        });
        $(".eliminar-Admin").each(function (index, value) {
            $(this).click(function () {
                let formdata = new FormData();
                let va = $(this.parentElement.parentElement).attr("id");
                formdata.append("ID-reg", va);
                formdata.append("accion", "delete");
                ProcesarAjax("POST", formdata);
            });
        });
    }
});
