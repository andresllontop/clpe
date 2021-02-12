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
    $("#btnAbrirSubCapitulo").click(function () {
        $("#formulariosubcapitulo").attr("data-form", "save");
        $(".FormularioAjax")[0].reset();
        $("#Codigo-reg").attr("readonly", false);
        $("#Capitulo-reg option").attr("selected", false);
        $("#Capitulo-reg option[value=" + 0 + "]").attr("selected", true);

        $("#tituloModalMansubcapitulo").html("REGISTRAR SUBCAPITULO");
        $("#ventanaModalMansubcapitulo").modal("show");
    });

    $(".FormularioAjax").submit(function (e) {
        $("#insertarModal").modal("hide");
        $("#modificarModal").modal("hide");
        e.preventDefault();
        let form = $(this);
        let tipo = form.attr("data-form");
        let metodo = form.attr("method");
        let formdata = new FormData(this);
        var file = $("#PDF-reg")[0].files[0];
        formdata.append("accion", tipo);

        if ($("#Capitulo-reg option:selected").val() != 0 && file) {
            $("#cargarpagina").html(ajax_load);
            ProcesarAjax(metodo, formdata);
        } else {
            if (tipo == "save") {
                swal(
                    "Ocurrió un error inesperado",
                    "Por favor debes seleccionar  Capitulo y Archivo",
                    "error"
                );
            } else {
                $("#cargarpagina").html(ajax_load);
                ProcesarAjax(metodo, formdata);
            }
        }
    });
    function ProcesarAjax(metodo, formdata) {
        $.ajax({
            type: metodo,
            url: url + "ajax/contactarAjax.php",
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
            url: url + "ajax/contactarAjax.php",
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
                } else {
                    let subcapitulo = JSON.parse(respuesta);
                    let html = "";
                    let estado = "";
                    let estado2 = "";
                    let contador = paginas * registrototal - registrototal;
                    for (let key in subcapitulo) {
                        if (subcapitulo[key]["mensajeEstado"] == 0) {
                            estado = " zmdi-minus";
                            estado2 = " btn-warning";
                        } else {
                            estado = " zmdi-check-all";
                            estado2 = " btn-success";
                        }
                        contador++;
                        html += `<tr numero="${contador}" id="${
                            subcapitulo[key].idmensaje
                            }" state="${subcapitulo[key].mensajeEstado}">
            <td class="text-center ">${contador}</td>
            <td class="text-center">${subcapitulo[key].CuentaCodigo}</td>
            <td class="text-center">${subcapitulo[key].usuario}</td>
            <td class="text-center">${subcapitulo[key].email}</td>
            <td class="text-center">${subcapitulo[key].titulo}</td>
            <td class="text-center">${subcapitulo[key].descripcion}</td>
            <td class="text-center ">
                <button class="btn ${estado2} estado-Admin " ><i class="zmdi ${estado}"></i> </button>
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
                $("#Codigo-reg").val(rowvalue[indice - 1][1]);
                $("#Codigo-reg").attr("readonly", true);
                $("#Nombre-reg").val(rowvalue[indice - 1][2]);
                $("#ID-reg").val($(this.parentElement.parentElement).attr("id"));

                $(
                    "#Capitulo-reg option[value=" +
                    $(this.parentElement.parentElement).attr("idcapitulo") +
                    "]"
                ).attr("selected", true);
                $("#formulariosubcapitulo").attr("data-form", "update");
                $("#tituloModalMansubcapitulo").html("EDITAR SUBCAPITULO");
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
                formdata.append("accion", "delete");
                ProcesarAjax("POST", formdata);
            });
        });
        $(".estado-Admin").each(function (index, value) {
            $(this).click(function () {
                if ($(this.parentElement.parentElement).attr("state") == 0) {
                    $(this).removeClass("btn-warning");
                    $(this).addClass("btn-success");
                    $(this)
                        .children()
                        .removeClass("zmdi-minus");
                    $(this)
                        .children()
                        .addClass("zmdi-check-all");
                    let formdata = new FormData();
                    formdata.append(
                        "ID-reg",
                        $(this.parentElement.parentElement).attr("id")
                    );
                    formdata.append("Estado-reg", 1);
                    formdata.append("accion", "update");
                    ProcesarAjax("POST", formdata);
                }
            });
        });
    }
});
