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
    $("#btnAbrirAdmin").click(function () {
        $("#formularioAdmin").attr("data-form", "save");
        $(".FormularioAjax")[0].reset();
        $("#tituloModalManAdministrador").html("REGISTRAR ADMINISTRADOR");
        $("#ventanaModalManAdministrador").modal("show");
    });

    $(".FormularioAjax").submit(function (e) {
        $("#insertarModal").modal("hide");
        $("#modificarModal").modal("hide");
        e.preventDefault();
        var form = $(this);
        var tipo = form.attr("data-form");
        var metodo = form.attr("method");
        var formdata = new FormData(this);

        formdata.append("accion", tipo);
        formdata.append("Tipo-reg", "Administrador");

        formdata.append("Especialidad-reg", "Ninguna");
        $("#cargarpagina").html(ajax_load);
        ProcesarAjax(metodo, formdata);
    });
    function ProcesarAjax(metodo, formdata) {
        $.ajax({
            type: metodo,
            url: url + "ajax/administradorAjax.php",
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
            url: url + "ajax/administradorAjax.php",
            data: {
                acion: "listar",
                pagina: paginas,
                registros: registrototal,
                usuario: "admin"
            },
            beforeSend: function () {
                $(".preloader").html(` <div class="text-center cargando">
        <h2 class="all-tittles">Cargando...</h2>
      </div>`);
            },
            success: function (respuesta) {
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
                    let contador = paginas * registrototal - registrototal;
                    for (var key in admin) {
                        contador++;
                        html += `<tr numero="${contador}" id="${admin[key].id}"clave="${
                            admin[key].clave
                            }"
             idcuenta="${admin[key].idcuenta}">
            <td class="text-center">${contador}</td>
            <td class="text-center">${admin[key].AdminNombre}</td>
            <td class="text-center">${admin[key].AdminApellido}</td>
            <td class="text-center">${admin[key].AdminTelefono}</td>
            <td class="text-center">${admin[key].email}</td>
            <td class="text-center">${admin[key].CuentaCodigo}</td>
            <td class="text-center">${admin[key].usuario}</td>
            <td  class="text-center "><img 
        src="${url}adjuntos/clientes/${admin[key].foto}"
        alt="user-picture"
        class="img-responsive center-box"style="width:50px;height:60px;"
        /><div class="imag">${admin[key].foto}</div></td>
            <td class="text-center">
                <button class="btn btn-info editar-Admin" ><i class="zmdi zmdi-refresh"></i> </button>
            </td>
            <td class="text-center">
                 <button class="btn btn-danger eliminar-Admin "><i class="zmdi zmdi-delete"></i></button>
            </td>
            </tr>`;
                        $(".RespuestaLista").html(html);
                    }
                    addEventsButtonsAdmin();
                }
                $(".preloader").html("");
            },
            error: function (e) {
                $(".preloader").html("");
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
        $(".FormularioAjax")[0].reset();
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
                // (rowvalue[indice - 1]);
                $("#Nombre-reg").val(rowvalue[indice - 1][1]);
                $("#Apellido-reg").val(rowvalue[indice - 1][2]);
                $("#Telefono-reg").val(rowvalue[indice - 1][3]);
                $("#Email-reg").val(rowvalue[indice - 1][4]);
                $("#Usuario-reg").val(rowvalue[indice - 1][6]);
                $("#ID-reg").val($(this.parentElement.parentElement).attr("id"));
                $("#IDCuenta-reg").val(
                    $(this.parentElement.parentElement).attr("idcuenta")
                );
                var formdata = new FormData();
                formdata.append(
                    "Clave-reg",
                    $(this.parentElement.parentElement).attr("clave")
                );
                formdata.append("accion", "desencriptar");
                ProcesarAjax("POST", formdata);
                $("#formularioAdmin").attr("data-form", "update");
                $("#tituloModalManAdministrador").html("EDITAR ADMINISTRADOR");
                $("#ventanaModalManAdministrador").modal("show");
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
                    "IDcuenta-reg",
                    $(this.parentElement.parentElement).attr("idcuenta")
                );
                formdata.append("accion", "delete");
                ProcesarAjax("POST", formdata);
            });
        });
    }
    $(".visible").click(function (index, value) {
        if ($("#Password1-reg").attr("type") == "password") {
            $(this).removeClass("zmdi-eye-off");
            $(this).removeClass("btn-info");
            $(this).addClass("btn-success");
            $(this).addClass("zmdi-eye");
            $("#Password1-reg").attr("type", "text");
        } else {
            $(this).removeClass("zmdi-eye");
            $(this).removeClass("btn-success");
            $(this).addClass("btn-info");
            $(this).addClass("zmdi-eye-off");
            $("#Password1-reg").attr("type", "password");
        }
    });
});
