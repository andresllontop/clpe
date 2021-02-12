$(document).ready(function () {
    let pag = 1;
    let total = 5;
    let ajax_load =
        "<div class='progress'>" +
        "<div id='progress_id' class='progress-bar progress-bar-striped active' " +
        "role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width: 45%'>" +
        "n/100</div></div>";
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

    $("#btnAbrircapitulo").click(function () {
        $("#formulariocapitulo").attr("data-form", "save");
        $(".FormularioAjax")[0].reset();
        $("#Codigo-reg").attr("readonly", false);
        $("#Libro-reg  option").attr("selected", false);
        $("#Libro-reg  option[value=" + 0 + "]").attr("selected", true);
        $("#tituloModalMancapitulo").html("REGISTRAR CUESTIONARIO");
        $("#ventanaModalMancapitulo").modal("show");
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

        if ($("#Libro-reg option:selected").val() != 0) {
            $("#cargarpagina").html(ajax_load);
            ProcesarAjax(metodo, formdata);
        } else {
            swal(
                "Ocurrió un error inesperado",
                "Por favor debes seleccionar un capitulo",
                "error"
            );
        }
    });
    $(".FormularioAjaxCuestionario").submit(function (e) {
        e.preventDefault();
        let form = $(this);
        let tipo = form.attr("data-form");
        let metodo = form.attr("method");
        let formdata = new FormData(this);
        formdata.append("accion", tipo);
        $("#cargarpagina").html(ajax_load);
        ProcesarAjaxCuestionario(metodo, formdata);
    });
    function ProcesarAjax(metodo, formdata) {
        $.ajax({
            type: metodo,
            url: url + "ajax/cuestionarioclienteAjax.php",
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
                ProcesarAjax("");
                $("#ventanaModalMancapitulo").modal("hide");
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
    function ProcesarAjaxCuestionario(metodo, formdata) {
        $.ajax({
            type: metodo,
            url: url + "ajax/cuestionarioclienteAjax.php",
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
                // (data);
                $("#cargarpagina").html("");
                swal(
                    {
                        title: JSON.parse(data).Titulo,
                        text: JSON.parse(data).Texto,
                        type: JSON.parse(data).Tipo,
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        closeOnConfirm: false,
                        confirmButtonText: "Aceptar"
                    },
                    function () {
                        location.reload();
                    }
                );
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
        // $("#cargarpagina").html(ajax_load);
        $.ajax({
            type: "GET",
            url: url + "ajax/cuestionarioclienteAjax.php",
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
                $("#cargarpagina").html("");
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
                    let capitulo = JSON.parse(respuesta);
                    let html = "";
                    let contador = paginas * registrototal - registrototal;
                    for (var key in capitulo) {
                        contador++;
                        html += `<tr numero="${contador}" idtitulo="${
                            capitulo[key].idtitulo
                            }" id="${capitulo[key].idtest}">
              <td class="text-center ">${contador}</td>
              <td class="text-center">${capitulo[key].usuario}</td>
              <td class="text-center">${capitulo[key].nombre}</td>`;
                        for (let index = 1; index <= 10; index++) {
                            html +=
                                '<td class="text-center">' +
                                JSON.stringify(capitulo[key]["respuesta_p" + index]).substr(
                                    0,
                                    10
                                ) +
                                '...</td> <td class="text-center" style="display:none;">' +
                                capitulo[key]["respuesta_p" + index] +
                                "</td>";
                        }
                        html += `<td class="text-center ">
                  <button class="btn btn-warning editar-Admin " ><i class="zmdi zmdi-eye"></i> </button>
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
                var indice = $(this.parentElement.parentElement).attr("numero");
                var rowvalue = [];
                $("tbody > tr").each(function (i, v) {
                    indice;
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
                $("#Nombre-reg").val(rowvalue[indice - 1][1]);
                $("#ID-reg").val($(this.parentElement.parentElement).attr("id"));

                $(
                    "#Libro-reg option[value=" +
                    $(this.parentElement.parentElement).attr("idtitulo") +
                    "]"
                ).attr("selected", true);
                let count = 0;
                for (let index = 1; index <= 20; index++) {
                    if (index % 2 == 0) {
                        count++;
                        $("#P" + count + "-reg").val(rowvalue[indice - 1][index + 2]);
                    }
                }

                $("#formulariocapitulo").attr("data-form", "update");
                $("#tituloModalMancapitulo").html(
                    "VISUALIZACION DE RESPUESTAS DEL CUESTIONARIO"
                );
                $("#ventanaModalMancapitulo").modal("show");
            });
        });
        $(".eliminar-Admin").each(function (index, value) {
            $(this).click(function () {
                var formdata = new FormData();
                formdata.append(
                    "ID-reg",
                    $(this.parentElement.parentElement).attr("id")
                );
                formdata.append(
                    "IDtitulo-reg",
                    $(this.parentElement.parentElement).attr("idtitulo")
                );
                formdata.append("accion", "delete");
                ProcesarAjax("POST", formdata);
            });
        });
    }
});
