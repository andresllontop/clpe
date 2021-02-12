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

    $(".FormularioAjax").submit(function (e) {
        $("#insertarModal").modal("hide");
        $("#modificarModal").modal("hide");
        e.preventDefault();
        var form = $(this);
        var tipo = form.attr("data-form");
        var metodo = form.attr("method");
        var formdata = new FormData(this);

        (tipo);

        formdata.append("accion", tipo);
        formdata.append("Tipo-reg", "Administrador");
        formdata.append("Especialidad-reg", "Ninguna");
        $("#cargarpagina").html(ajax_load);
        ProcesarAjax(metodo, formdata);
    });
    function ProcesarAjax(metodo, formdata) {
        $.ajax({
            type: metodo,
            url: url + "ajax/bitacoraAjax.php",
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
                (data);
                if (JSON.parse(data).Titulo == "clave") {
                    $("#Password1-reg").val(JSON.parse(data).Clave);
                    $("#Password2-reg").val(JSON.parse(data).Clave);
                } else {
                    $("#ventanaModalManAdministrador").modal("hide");
                    swal({
                        title: JSON.parse(data).Titulo,
                        text: JSON.parse(data).Texto,
                        type: JSON.parse(data).Tipo,
                        confirmButtonText: "Aceptar"
                    });
                    listar(pag, total);
                }
                $("#cargarpagina").html("");
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
            url: url + "ajax/bitacoraAjax.php",
            data: {
                acion: "listar",
                pagina: paginas,
                registros: registrototal,
                bestado: "activo"
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
                    $(".RespuestaLista").html("<td colspan='10'>Ningun Dato</td>");
                } else {
                    let admin = JSON.parse(respuesta);
                    let html = "";
                    let estado2 = "";
                    let contador = paginas * registrototal - registrototal;
                    for (var key in admin) {
                        if (admin[key]["BitacoraEstado"] == "Inactivo") {
                            estado2 = " btn-warning";
                        } else {
                            estado2 = " btn-success";
                        }
                        contador++;
                        html += `<tr numero="${contador}" id="${admin[key].id}">
            <td class="text-center">${contador}</td>
            <td class="text-center">${admin[key].usuario}</td>
            <td class="text-center">${admin[key].email}</td>
            <td class="text-center">${admin[key].BitacoraYear}</td>
            <td class="text-center">${admin[key].BitacoraFecha}</td>
            <td class="text-center">${admin[key].BitacoraDia}</td>
            <td class="text-center">${admin[key].BitacoraHoraInicio}</td>
            <td class="text-center">${admin[key].BitacoraHoraFinal}</td>
            <td class="text-center"><button class="btn ${estado2} " >${
                            admin[key].BitacoraEstado
                            } </button></td>
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
        $(".eliminar-Admin").each(function (index, value) {
            $(this).click(function () {
                var formdata = new FormData();
                formdata.append(
                    "ID-reg",
                    $(this.parentElement.parentElement).attr("id")
                );
                formdata.append(
                    "IDcuenta-reg",
                    $(this.parentElement.parentElement).attr("idcuenta")
                );
                formdata.append("accion", "delete");
                ProcesarAjax("POST", formdata);
            });
        });
    }
});
