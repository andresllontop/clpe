$(document).ready(function () {
    $("#Descripcion-reg").Editor();
    let ajax_load =
        "<div class='progress'>" +
        "<div id='progress_id' class='progress-bar progress-bar-striped active' " +
        "role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width: 45%'>" +
        "n/100</div></div>";
    let pag = 1;
    let total = 5;
    listar(pag, total);
    $("#Tipo-reg").change(function () {
        tipo($(this).val());
    });
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
    function videoPreview(input, imagen) {
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            imagen;
            reader.onload = function (e) {
                $(imagen).html(
                    "<video width='244' alt='user-picture' class='img-responsive center-box' controls ><source src='" +
                    e.target.result +
                    "' type='video/mp4'></video>"
                );
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#Video-reg").change(function () {
        videoPreview(this, "#videoPreview");
    });
    $("#btnAbrirbook").click(function () {
        $(".FormularioAjax")[0].reset();
        $("#imagePreview").html("");
        $("#formularioLibro").attr("data-form", "save");
        $("#tituloModalManlibro").html("REGISTRAR BLOG");
        $("#ventanaModalManlibro").modal("show");
        $("#Descripcion-reg").Editor("setText", ['<p style="color:black"></p>']);
        $("#Descripcion-reg").Editor("getText");
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
        var texto = $("#Descripcion-reg").Editor("getText");
        formdata.append("Descripcion-reg", texto);
        $("#Tipo-reg option:selected").val();

        if (tipo == "save") {
            if (
                $("#Capitulo-reg option:selected").val() != 0 &&
                $("#Tipo-reg option:selected").val() != 0 &&
                $("#Disponible-reg option:selected").val() != -1
            ) {
                $("#cargarpagina").html(ajax_load);
                ProcesarAjax(metodo, formdata);
            } else {
                swal(
                    "Ocurrió un error inesperado",
                    "Por favor debes seleccionar  Tipo de Archivo",
                    "error"
                );
            }
        } else {
            $("#cargarpagina").html(ajax_load);
            ProcesarAjax(metodo, formdata);
        }
    });
    function ProcesarAjax(metodo, formdata) {
        $.ajax({
            type: metodo,
            url: url + "ajax/blogAjax.php",
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
                console.log(data);
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
            url: url + "ajax/blogAjax.php",
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
                console.log(respuesta);
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
                    let html2 = "";
                    let contador = paginas * registrototal - registrototal;
                    for (let key in videos) {
                        switch (videos[key]["tipoArchivo"]) {
                            case "1":
                                html2 = `<td  class="text-center "><img  
                  src="${url}adjuntos/blog/IMAGENES/${videos[key].archivo}"
                  alt="user-picture"
                  class="img-responsive center-box"style="width:100px;height:60px;"
                  /><div class="imag">${videos[key].archivo}</div></td>
                 `;
                                break;
                            case "2":
                                html2 = `
                  <td  class="text-center "><video alt="user-picture"  
                class="img-responsive center-box"style="width:100px;height:60px;" controls ><source class="imag" 
                src="${url}adjuntos/blog/VIDEOS/${videos[key].archivo}" 
                type="video/mp4"></video>${videos[key].archivo}</td>
                `;
                                break;
                        }
                        contador++;
                        html += `<tr numero="${contador}" id="${
                            videos[key].idblog
                            }" tipo="${videos[key]["tipoArchivo"]}">
          <td class="text-center ">${contador}</td>
          <td class="text-center">${videos[key].titulo}</td>
          <td class="text-center"style="display:none;">${videos[key].resumen}</td>
          <td class="text-center" style="display:none;">${videos[key].descripcion}</td>
          <td class="text-center">${videos[key].descripcion.substring(0, 400)}</td>
          ${html2}
          <td class="text-center">
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
                $("#Titulo-reg").val(rowvalue[indice - 1][1]);
                $("#Resumen-reg").val(rowvalue[indice - 1][2]);

                $("#Descripcion-reg").Editor("setText", [
                    '<p style="color:black">' + rowvalue[indice - 1][3] + "</p>"
                ]);
                $(
                    "#Tipo-reg option[value='" +
                    $(this.parentElement.parentElement).attr("tipo") +
                    "']"
                ).attr("selected", true);
                tipo($(this.parentElement.parentElement).attr("tipo"));

                $("#formularioLibro").attr("data-form", "update");
                $("#tituloModalManlibro").html("EDITAR BLOG");
                $("#ventanaModalManlibro").modal("show");
            });
        });
        $(".eliminar-Admin").each(function (index, value) {
            $(this).click(function () {
                let formdata = new FormData();
                let va = $(this.parentElement.parentElement).attr("id");
                formdata.append("ID-reg", va);
                formdata.append(
                    "Tipo-reg",
                    $(this.parentElement.parentElement).attr("tipo")
                );
                formdata.append("accion", "delete");
                ProcesarAjax("POST", formdata);
            });
        });
    }
    function tipo(params) {
        switch (params) {
            case "1":
                $("#Tipo-Archivo").html(`<div id="imagePreview"> </div>
        <input name="Imagen-reg" id="Imagen-reg" type="file"
        class="material-control tooltips-general input-check-user"
        placeholder="Selecciona Imagen" data-toggle="tooltip"
        data-placement="top" title="" 
        data-original-title="Selecciona la Imagen de tu escritorio">
        <span class="highlight"></span>
        <span class="bar"></span>
        <label>Selecciona Imagen</label>`);

                break;
            case "2":
                $(
                    "#Tipo-Archivo"
                ).html(`<div id="videoPreview"></div><input name="Video-reg"id="Video-reg" type="file"
        class="material-control tooltips-general input-check-user"
        placeholder="Selecciona Video" data-toggle="tooltip"
        data-placement="top" title="" 
        data-original-title="Selecciona el Video de tu escritorio">
        <span class="highlight"></span>
        <span class="bar"></span>
        <label>Selecciona el Video</label>`);
                break;
            case "3":
                $("#Tipo-Archivo").html(`<input name="PDF-reg"id="PDF-reg" type="file"
            class="material-control tooltips-general input-check-user"
            placeholder="Selecciona PDF" data-toggle="tooltip"
            data-placement="top" title="" 
            data-original-title="Selecciona el PDF de tu escritorio">
            <span class="highlight"></span>
            <span class="bar"></span>
            <label>Sube el Archivo</label>`);
                break;
            default:
                $("#Tipo-Archivo").html("");
                break;
        }
    }
});
