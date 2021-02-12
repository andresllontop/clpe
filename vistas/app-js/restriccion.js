$(document).ready(function () {
    let ajax_load =
        "<div class='progress'>" +
        "<div id='progress_id' class='progress-bar progress-bar-striped active' " +
        "role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width: 45%'>" +
        "n/100</div></div>";
    let pag = 1;
    let total = 5;
    listar(pag, total);

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
    $("#btnAbrirSubCapitulo").click(function () {
        $("#formulariosubcapitulo").attr("data-form", "save");
        $("#Tipo-Archivo").html("");
        $(".FormularioAjax")[0].reset();
        $("#Codigo-reg").attr("readonly", false);
        $("#Capitulo-reg option").attr("selected", false);
        $("#Capitulo-reg option[value='" + 0 + "']").attr("selected", true);
        $("#Tipo-reg option").attr("selected", false);
        $("#Tipo-reg option[value='" + 0 + "']").attr("selected", true);
        $("#Disponible-reg option").attr("selected", false);
        $("#Disponible-reg option[value='" + -1 + "']").attr("selected", true);
        tipo("1");
        $("#Tipo-reg option[value='1']").attr("selected", true);
        $("#tituloModalMansubcapitulo").html("REGISTRAR SUBCAPITULO");
        $("#ventanaModalMansubcapitulo").modal("show");
    });
    $("#Tipo-reg").change(function () {
        tipo($(this).val());
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
        $("#Tipo-reg option:selected").val();
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
                "Por favor debes seleccionar  SubTitulo",
                "error"
            );
        }
    });
    function ProcesarAjax(metodo, formdata) {
        $.ajax({
            type: metodo,
            url: url + "ajax/restriccionAjax.php",
            data: formdata,
            cache: false,
            contentType: false,
            processData: false,
            // modificar el valor de xhr a nuestro gusto
            xhr: function () {
                // obtener el objeto XmlHttpRequest nativo
                let xhr = $.ajaxSettings.xhr();
                // añadirle un controlador para el evento onprogress
                xhr.onprogress = function (evt) {
                    // calculamos el porcentaje y nos quedamos sólo con la parte entera
                    let porcentaje = Math.floor((evt.loaded / evt.total) * 100);
                    // actualizamos el texto con el porcentaje mostrado
                    $("#progress_id").text(porcentaje + "/100");
                    // actualizamos la cantidad avanzada en la barra de progreso
                    $("#progress_id").attr("aria-valuenow", porcentaje);
                    $("#progress_id").css("width", porcentaje + "%");
                };
                // devolvemos el objeto xhr modificado
                return xhr;
            },
            success: function (data) {
                data;
                $("#ventanaModalMansubcapitulo").modal("hide");
                swal({
                    title: JSON.parse(data).Titulo,
                    text: JSON.parse(data).Texto,
                    type: JSON.parse(data).Tipo,
                    confirmButtonText: "Aceptar"
                });
                $("#cargarpagina").html("");
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
        // $("#cargarpaginalista").html(ajax_load);
        $.ajax({
            type: "GET",
            url: url + "ajax/restriccionAjax.php",
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
                // (respuesta);
                $("#cargarpaginalista").html("");
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
                    $(".RespuestaLista").html("<td colspan='7'>Ningún Dato</td>");
                } else {
                    let subcapitulo = JSON.parse(respuesta);
                    let html = "";
                    var contador = paginas * registrototal - registrototal;
                    for (let key in subcapitulo) {
                        switch (subcapitulo[key]["tipoArchivo"]) {
                            case "1":
                                var html2 = `<td  class="text-center "><img  
                src="${url}adjuntos/restriccion/IMAGENES/${
                                    subcapitulo[key].imagen
                                    }"
                alt="user-picture"
                class="img-responsive center-box"style="width:50px;height:60px;"
                /><div class="imag">${subcapitulo[key].imagen}</div></td>
               `;
                                break;
                            case "2":
                                var html2 = `
                <td  class="text-center "><video alt="user-picture"  
              class="img-responsive center-box"style="width:100px;height:60px;" controls ><source class="imag" 
              src="${url}adjuntos/restriccion/VIDEOS/${subcapitulo[key].video}" 
              type="video/mp4"></video>${subcapitulo[key].video}</td>
              `;
                                break;
                            case "3":
                                var html2 = `
                <td class="text-center"><a href="${url}adjuntos/restriccion/PDF/${
                                    subcapitulo[key].archivo
                                    }"
            download='${
                                    subcapitulo[key].archivo
                                    }' class="btn btn-warning" style="text-decoration:none;">
            <i class="zmdi zmdi-download"></i> </a></td>`;
                                break;
                        }
                        contador++;
                        html += `<tr numero="${contador}" id="${
                            subcapitulo[key].idrestriccion
                            }" disponible="${subcapitulo[key].disponible}" tipo="${
                            subcapitulo[key]["tipoArchivo"]
                            }">
            <td class="text-center ">${contador}</td>
            <td class="text-center">${subcapitulo[key].codigo_subtitulo}</td>
            <td class="text-center">${subcapitulo[key].subtituloName}</td>
            <td class="text-center">${subcapitulo[key].Nombre}</td> ${html2}
            <td class="text-center ">
                <button class="btn btn-success editar-Admin " ><i class="zmdi zmdi-refresh"></i> </button>
            </td>
            <td class="text-center">
                 <button class="btn btn-danger eliminar-Admin "><i class="zmdi zmdi-delete"></i></button>
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
                let rowvalue = [];
                $("tbody > tr").each(function (i, v) {
                    rowvalue[i] = $("td", this)
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
                $("#Nombre-reg").val(rowvalue[indice - 1][3]);
                $("#ID-reg").val($(this.parentElement.parentElement).attr("id"));

                $("#Capitulo-reg option[value='" + rowvalue[indice - 1][1] + "']").attr(
                    "selected",
                    true
                );
                $(
                    "#Tipo-reg option[value='" +
                    $(this.parentElement.parentElement).attr("tipo") +
                    "']"
                ).attr("selected", true);
                tipo($(this.parentElement.parentElement).attr("tipo"));
                $(
                    "#Disponible-reg option[value='" +
                    $(this.parentElement.parentElement).attr("disponible") +
                    "']"
                ).attr("selected", true);
                $("#formulariosubcapitulo").attr("data-form", "update");
                $("#tituloModalMansubcapitulo").html("EDITAR RECURSO");
                $("#ventanaModalMansubcapitulo").modal("show");
            });
        });
        $(".eliminar-Admin").each(function (index, value) {
            $(this).click(function () {
                let formdata = new FormData();
                formdata.append(
                    "ID-reg",
                    $(this.parentElement.parentElement).attr("id")
                );
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
        console.log(params);
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
