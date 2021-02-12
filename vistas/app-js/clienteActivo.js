$(document).ready(function () {
    let ajax_load =
        "<div class='progress'>" +
        "<div id='progress_id' class='progress-bar progress-bar-striped active' " +
        "role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width: 45%'>" +
        "n/100</div></div>";
    let pag = 1;
    let total = 5;
    listar(pag, total);
    $("#NameRecomendado-reg").keyup(function () {
        var ValorBusqueda = $(this).val();
        console.log(ValorBusqueda);
        var formdata = new FormData();
        formdata.append("dato-reg", ValorBusqueda);
        formdata.append("accion", "search");
        // ListarBusquedaAjax("POST", formdata);
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
    $("#btnAbrirCliente").click(function () {
        $("#formularioCliente").attr("data-form", "save");
        $("#tituloModalManCliente").html("REGISTRAR ALUMNO");
        $("#ventanaModalManCliente").modal("show");
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
        ProcesarAjax(metodo, formdata);
    });
    $(".FormularioAjaxRecomendado").submit(function (e) {
        $("#ventanaModalManRecomendado").modal("hide");
        e.preventDefault();
        var form = $(this);
        var tipo = form.attr("data-form");
        var metodo = form.attr("method");
        var formdata = new FormData(this);
        formdata.append("accion", tipo);
        // ProcesarAjaxArbol(metodo, formdata);
    });
    function ListarBusquedaAjax(metodo, formdata) {
        $.ajax({
            type: metodo,
            url: url + "ajax/administradorAjax.php",
            data: formdata,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                let html = "";
                let contador = 0;
                let admin = JSON.parse(data);
                for (var key in admin) {
                    contador++;
                    html += `<tr>
            <td class="text-center">${contador}</td>
            <td class="text-center">${admin[key].Cuenta_Codigo}</td>
            <td class="text-center">${admin[key].AdminNombre}</td>
            <td class="text-center">${admin[key].AdminApellido}</td>
            </tr>`;
                }
                $(".RespuestaListaBusqueda").html(html);
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
    function ProcesarAjax(metodo, formdata) {
        $.ajax({
            type: metodo,
            url: url + "ajax/administradorAjax.php",
            data: formdata,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                // console.log(data);
                if (JSON.parse(data).Titulo == "clave") {
                    $("#password1-reg").val(JSON.parse(data).Clave);
                } else {
                    $("#ventanaModalManCliente").modal("hide");
                    swal({
                        title: JSON.parse(data).Titulo,
                        text: JSON.parse(data).Texto,
                        type: JSON.parse(data).Tipo,
                        confirmButtonText: "Aceptar"
                    });
                    listar(pag, total);
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
    function ProcesarAjaxArbol(metodo, formdata) {
        $.ajax({
            type: metodo,
            url: url + "ajax/arbolAjax.php",
            data: formdata,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                console.log(data);
                $("#ventanaModalManRecomendado").modal("hide");
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
            url: url + "ajax/administradorAjax.php",
            data: {
                acion: "listar",
                pagina: paginas,
                registros: registrototal,
                usuario: "alumno",
                aestado: "activo"
            },
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
                    $(".RespuestaLista").html("<td colspan='13'>Ningun Dato</td>");
                } else {
                    let admin = JSON.parse(respuesta);
                    let html = "";
                    let estado = "";
                    let estado2 = "";
                    let contador = paginas * registrototal - registrototal;
                    for (var key in admin) {
                        if (admin[key]["estado"] == "Inactivo") {
                            estado = " zmdi-minus";
                            estado2 = " btn-warning";
                        } else {
                            estado = " zmdi-check-all";
                            estado2 = " btn-success";
                        }
                        contador++;
                        html += `<tr id="${
                            admin[key].Cuenta_Codigo
                            }" numero="${contador}"  idcuenta="${admin[key].idcuenta}"codigocuenta="${
                            admin[key].Cuenta_Codigo
                            }"state="${admin[key].estado}" idelete="${admin[key].id}">
            <td class="text-center">${contador}</td>
            <td class="text-center">${admin[key].Cuenta_Codigo}</td>
            <td class="text-center">${admin[key].AdminNombre}</td>
            <td class="text-center">${admin[key].AdminApellido}</td>
            <td class="text-center">${admin[key].AdminTelefono}</td>
            <td class="text-center">${admin[key].email}</td>
            <td class="text-center">${admin[key].usuario}</td>
            <td  class="text-center"style="display:none;">${
                            admin[key].AdminOcupacion
                            }</td>
            <td class="text-center"style="display:none;">${
                            admin[key].clave
                            }</td>
            <td  class="text-center "><img
        src="${url}adjuntos/clientes/${admin[key].foto}"
        alt="user-picture"
        class="img-responsive center-box" style="width:50px;height:60px;"
        /><div class="imag" style="display:none;">${
                            admin[key].foto
                            }</div></td>
            <td  class="text-center ">${admin[key].voucher}
       </td>
            <td class="text-center">
                <button class="btn ${estado2} estado-Admin" ><i class="zmdi ${estado}"></i> </button>
            </td>
            <td class="text-center">
                <button class="btn btn-info editar-Admin" ><i class="zmdi zmdi-eye"></i> </button>
            </td>
            <td class="text-center">
                 <button class="btn btn-danger eliminar-Admin "><i class="zmdi zmdi-delete"></i></button>
            </td>
            </tr>`;

                        $(".RespuestaLista").html(html);
                    }
                    //   listarArbol();
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
    function listarArbol() {
        $.ajax({
            type: "GET",
            url: url + "ajax/arbolAjax.php",
            data: {
                acion: "datos"
            },
            success: function (respuesta) {
                console.log(respuesta);
                let admin = JSON.parse(respuesta);
                var rowvalue2 = [];
                $("tbody > tr").each(function (i, v) {
                    rowvalue2[i] = $("td", this)
                        .map(function () {
                            return $(this).html();
                        })
                        .get();
                });
                // console.log(rowvalue2);
                for (var key in admin) {
                    for (let index = 0; index < rowvalue2.length; index++) {
                        if (rowvalue2[index][1] == admin[key]["hijo"]) {
                            $(
                                "#" + admin[key]["hijo"] + " > .recomendado > button"
                            ).removeClass("btn-warning");
                            $("#" + admin[key]["hijo"] + " > .recomendado > button").addClass(
                                "btn-success"
                            );
                            $("#" + admin[key]["hijo"] + " > .recomendado > button").attr(
                                "cuentaPadre",
                                admin[key]["padre"]
                            );
                            $(
                                "#" + admin[key]["hijo"] + " > .recomendado > button > i"
                            ).removeClass("zmdi-minus");
                            $(
                                "#" + admin[key]["hijo"] + " > .recomendado > button > i"
                            ).addClass("zmdi-check-all");
                            break;
                        } else {
                        }
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
    function addEventsButtonsAdmin() {
        $(".editar-Admin").each(function (index, value) {
            $(this).click(function () {
                var indice = $(this.parentElement.parentElement).attr("numero");
                var rowvalue = [];
                var rowvalue2 = [];
                $("tbody > tr").each(function (i, v) {
                    rowvalue[i] = $("td", this)
                        .map(function () {
                            return $(this).text();
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
                $("#imagePreview").html(
                    "<img  alt='user-picture' class='img-responsive  center-box' style='width:300px; height:244px;' src='" +
                    url +
                    "adjuntos/clientes/" +
                    rowvalue2[indice - 1][0] +
                    "' />"
                );
                $("#nombre-reg").val(rowvalue[indice - 1][2]);
                $("#apellido-reg").val(rowvalue[indice - 1][3]);
                $("#telefono-reg").val(rowvalue[indice - 1][4]);
                $("#email-reg").val(rowvalue[indice - 1][5]);
                $("#usuario-reg").val(rowvalue[indice - 1][6]);
                $("#especialidad-reg").val(rowvalue[indice - 1][7]);
                $("#monto-reg").val(rowvalue[indice - 1][10]);
                var formdata = new FormData();
                formdata.append("Clave-reg", rowvalue[indice - 1][8]);
                formdata.append("accion", "desencriptar");
                ProcesarAjax("POST", formdata);
                $("#formularioAdmin").attr("data-form", "update");
                $("#tituloModalManCliente").html("VISUALIZACION DE DATOS DEL ALUMNO");
                $("#ventanaModalManCliente").modal("show");
            });
        });
        $(".eliminar-Admin").each(function (index, value) {
            $(this).click(function () {
                var formdata = new FormData();
                formdata.append(
                    "ID-reg",
                    $(this.parentElement.parentElement).attr("idelete")
                );
                formdata.append(
                    "IDcuenta-reg",
                    $(this.parentElement.parentElement).attr("idcuenta")
                );
                formdata.append("accion", "delete");
                ProcesarAjax("POST", formdata);
            });
        });
        $(".estado-Admin").each(function (index, value) {
            $(this).click(function () {
                $(this.parentElement.parentElement).attr("state");
                if ($(this.parentElement.parentElement).attr("state") == "Inactivo") {
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
                        $(this.parentElement.parentElement).attr("idcuenta")
                    );
                    formdata.append(
                        "Codigo-reg",
                        $(this.parentElement.parentElement).attr("codigocuenta")
                    );
                    formdata.append("Estado-reg", "Activo");
                    formdata.append("accion", "updateEstado");
                    ProcesarAjax("POST", formdata);
                } else {
                    $(this).removeClass("btn-success");
                    $(this)
                        .children()
                        .removeClass("zmdi-check-all");
                    $(this).addClass("btn-warning");
                    $(this)
                        .children()
                        .addClass("zmdi-minus");
                    let formdata = new FormData(t);
                    formdata.append(
                        "ID-reg",
                        $(this.parentElement.parentElement).attr("idcuenta")
                    );
                    formdata.append(
                        "Codigo-reg",
                        $(this.parentElement.parentElement).attr("codigocuenta")
                    );
                    formdata.append("Estado-reg", "Inactivo");
                    formdata.append("accion", "updateEstado");
                    ProcesarAjax("POST", formdata);
                }
            });
        });
        $(".patrocinador-Admin").each(function (index, value) {
            $(this).click(function () {
                if ($(this.parentElement.parentElement).attr("patrocinador") == "no") {
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
                        $(this.parentElement.parentElement).attr("idcuenta")
                    );
                    formdata.append("Patrocinador-reg", "no");
                    formdata.append("accion", "updatePatrocinador");
                    ProcesarAjax("POST", formdata);
                } else {
                    $(this).removeClass("btn-success");
                    $(this)
                        .children()
                        .removeClass("zmdi-check-all");
                    $(this).addClass("btn-warning");
                    $(this)
                        .children()
                        .addClass("zmdi-minus");
                    let formdata = new FormData();
                    formdata.append(
                        "ID-reg",
                        $(this.parentElement.parentElement).attr("idcuenta")
                    );
                    formdata.append("Patrocinador-reg", "si");
                    formdata.append("accion", "updatePatrocinador");
                    ProcesarAjax("POST", formdata);
                }
            });
        });
        $(".recomendado-Admin").each(function (index, value) {
            $(this).click(function () {
                $("#CodigoHijo-reg").val(
                    $(this.parentElement.parentElement).attr("codigocuenta")
                );
                $("#NameRecomendado-reg").val(
                    $(this.parentElement.parentElement).attr("namerecomendado")
                );
                console.log($(this.parentElement.parentElement).attr("codigocuenta"));
                console.log(
                    $(this.parentElement.parentElement).attr("namerecomendado")
                );
                var formdata = new FormData();
                formdata.append(
                    "dato-reg",
                    $(this.parentElement.parentElement).attr("namerecomendado")
                );
                formdata.append("accion", "search");
                $("#CodigoPadre-reg").val($(this).attr("cuentapadre"));
                $("#formularioRecomendado").attr("data-form", "save");
                $("#tituloModalManRecomendado").html("REGISTRAR RECOMENDADO");
                $("#ventanaModalManRecomendado").modal("show");
                // ListarBusquedaAjax("POST", formdata);
            });
        });
    }
});
