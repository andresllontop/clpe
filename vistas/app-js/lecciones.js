$(document).ready(function () {
    let ajax_load =
        "<div class='progress'>" +
        "<div id='progress_id' class='progress-bar progress-bar-striped active' " +
        "role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width: 45%'>" +
        "n/100</div></div>";
    $("#seccion-leccion").css("display", "none");
    $("#seccion-declaracion").css("display", "none");
    $("#seccion-cuestionario").css("display", "none");
    let pag = 1;
    let total = 5;
    var cuentaleccion = "";
    listarCliente(pag, total);
    $("#btn-regresar").css("display", "none");
    $("#btn-OpenCuestionario").css("display", "none");
    $("#btn-regresar").click(function () {
        let accionvar = $("#seccion-cuestionario").css("display");

        if (accionvar == "none") {
            $("#seccion-alumno").css("display", "initial");
            $("#seccion-leccion").css("display", "none");
            $("#seccion-declaracion").css("display", "none");
            $("#btn-regresar").css("display", "none");
            $("#btn-OpenCuestionario").css("display", "none");
        } else {
            $("#seccion-leccion").css("display", "initial");
            $("#seccion-declaracion").css("display", "initial");
            $("#seccion-cuestionario").css("display", "none");
        }
    });
    $("#btn-OpenCuestionario").click(function () {
        $("#seccion-leccion").css("display", "none");
        $("#seccion-declaracion").css("display", "none");
        $("#seccion-cuestionario").css("display", "initial");
    });
    function listarCliente(paginas, registrototal) {
        $.ajax({
            type: "GET",
            url: url + "ajax/administradorAjax.php",
            data: {
                acion: "listar",
                pagina: paginas,
                registros: registrototal,
                usuario: "alumno"
            },
            beforeSend: function () {
                $(".preloader").html(` <div class="text-center cargando">
        <h2 class="all-tittles">Cargando...</h2>
      </div>`);
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
                    $(".RespuestaListaCliente").html("<td colspan='10'>Ningun Dato</td>");
                } else {
                    let admin = JSON.parse(respuesta);
                    let html = "";
                    let contador = paginas * registrototal - registrototal;
                    for (var key in admin) {
                        contador++;
                        html += `<tr numero="${contador}" id="${admin[key].id}"cuenta="${
                            admin[key].CuentaCodigo
                            }"
             idcuenta="${admin[key].idcuenta}">
            <td class="text-center seleccionar-alumno">${contador}</td>
            <td class="text-center seleccionar-alumno">${
                            admin[key].AdminNombre
                            }</td>
            <td class="text-center seleccionar-alumno">${
                            admin[key].AdminApellido
                            }</td>
            <td class="text-center seleccionar-alumno">${
                            admin[key].AdminTelefono
                            }</td>
            <td class="text-center seleccionar-alumno">${admin[key].email}</td>
            
            <td  class="text-center seleccionar-alumno"><img 
        src="${url}adjuntos/clientes/${admin[key].foto}"
        alt="user-picture"
        class="img-responsive center-box"style="width:50px;height:60px;"
        /><div class="imag">${admin[key].foto}</div></td>
            </tr>`;
                        $(".RespuestaListaCliente").html(html);
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
        $(".seleccionar-alumno").each(function (index, value) {
            $(this).click(function () {
                let indice = $(this.parentElement).attr("cuenta");
                $("#seccion-alumno").css("display", "none");
                $("#seccion-leccion").css("display", "initial");
                $("#seccion-declaracion").css("display", "initial");

                cuentaleccion = indice;
                $("#btn-regresar").css("display", "initial");
                $("#btn-OpenCuestionario").css("display", "initial");
                // $("#btn-regresar").attr("ubicacion", "leccion");
                listarLeccion(pag, total, indice);
                listarDeclaracion(pag, total, indice);
                listarCuestionario(indice);
            });
        });
    }
    // leccion
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
            listarLeccion(parseInt(pagina) + 1, total, cuentaleccion);
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
            listarLeccion(
                parseInt(
                    $("#paginador ul > li")
                        .last()
                        .prev()
                        .find("span")
                        .text()
                ),
                total,
                cuentaleccion
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
            listarLeccion(parseInt(ultimo), total, cuentaleccion);
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
            listarLeccion(parseInt(ultimo) - 3, total, cuentaleccion);
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
            listarLeccion(parseInt(ultimo) - 2, total, cuentaleccion);
        } else {
            $("#paginador ul > li")
                .last()
                .removeClass("active");
            $(this).addClass("active");
            $("#paginador ul > li")
                .last()
                .find("span")
                .text("Siguiente");
            listarLeccion(pag, total, cuentaleccion);
        }
    });
    $(".dropdown-menu1 >button").click(function () {
        total = $(this).text();
        listarLeccion(pag, total, cuentaleccion);
    });
    function ProcesarAjaxLeccion(metodo, formdata) {
        $.ajax({
            type: metodo,
            url: url + "ajax/videousuarioAjax.php",
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
                console.log(data);
                $("#cargarpagina").html("");
                $("#ventanaModalMansubcapitulo").modal("hide");
                swal({
                    title: JSON.parse(data).Titulo,
                    text: JSON.parse(data).Texto,
                    type: JSON.parse(data).Tipo,
                    confirmButtonText: "Aceptar"
                });
                // $("#cargarpagina").html("");
                listarLeccion(pag, total, cuentaleccion);
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
    function listarLeccion(paginas, registrototal, codigocuenta) {
        // $("#cargarpaginalista").html(ajax_load);
        $.ajax({
            type: "GET",
            url: url + "ajax/videousuarioAjax.php",
            data: {
                acion: "listar",
                pagina: paginas,
                registros: registrototal,
                busca: codigocuenta
            },
            beforeSend: function () {
                $(".preloader2").html(` <div class="text-center cargando">
        <h2 class="all-tittles">Cargando...</h2>
      </div>`);
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
                respuesta;
                let html = "";
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
                    html += `<tr ><td colspan="7"class="text-center">NINGUN DATO</td></tr>`;
                    $(".RespuestaListaLeccion").html(html);
                } else {
                    let subcapitulo = JSON.parse(respuesta);

                    var contador = paginas * registrototal - registrototal;
                    for (let key in subcapitulo) {
                        let ultimo = subcapitulo[key]["video"].split(".");
                        let extension = ultimo.pop();
                        contador++;
                        html += `<tr numero="${contador}" id="${
                            subcapitulo[key].idvideoUsuario
                            }"idcuenta="${subcapitulo[key].cuenta_codigoCuenta}" subtitu="${
                            subcapitulo[key].subtitulo_codigosubtitulo
                            }">
            <td class="text-center ">${contador}</td>
            
            <td class="text-center">${
                            subcapitulo[key].subtitulo_codigosubtitulo
                            }</td>
            <td class="text-center">${subcapitulo[key].comentario}</td>
            <td  class="text-center ">
            <video alt="user-picture"class="img-responsive center-box"style="width:100%;height:60px;" controls >
            <source  src="${url}adjuntos/video-usuarios/${
                            subcapitulo[key].video
                            }" 
            type="video/${extension}"></video><div class="imag" style="display:none;">${
                            subcapitulo[key].video
                            }</div></td>
            <td class="text-center ">
                <button class="btn btn-info editar-leccion " ><i class="zmdi zmdi-eye"></i> </button>
            </td>
            <td class="text-center">
                 <button class="btn btn-danger eliminar-leccion "><i class="zmdi zmdi-delete"></i></button>
            </td>
            </tr>`;
                        $(".RespuestaListaLeccion").html(html);
                    }
                    addEventsButtonsLeccion();
                }
                $(".preloader2").html("");
            },
            error: function (e) {
                $(".preloader2").html("");
                swal(
                    "Ocurrió un error inesperado",
                    "Por favor recargue la página",
                    "error"
                );
            }
        });
        return false;
    }
    function addEventsButtonsLeccion() {
        $(".editar-leccion").each(function (index, value) {
            $(this).click(function () {
                let indice = $(this.parentElement.parentElement).attr("numero");
                let rowvalue = [];
                let rowvalue2 = [];
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
                // (rowvalue[indice - 1]);
                // $("#Codigo-reg").attr("readonly", true);

                $("#Codigo-reg").val(rowvalue[indice - 1][1]);
                $("#Comentario-reg").val(rowvalue[indice - 1][2]);
                $("#videoPreview").html(
                    "<video width='400' alt='user-picture' class='img-responsive center-box' controls ><source src='" +
                    url +
                    "adjuntos/video-usuarios/" +
                    rowvalue2[indice - 1][0] +
                    "' type='video/webm'></video>"
                );
                $("#ID-reg").val($(this.parentElement.parentElement).attr("id"));

                // $(
                //   "#Capitulo-reg option[value=" +
                //     $(this.parentElement.parentElement).attr("idcapitulo") +
                //     "]"
                // ).attr("selected", true);
                $("#formularioLibro").attr("data-form", "update");
                $("#tituloModalManlibro").html("VISUALIZAR RESULTADO");
                $("#ventanaModalManlibro").modal("show");
            });
        });
        $(".eliminar-leccion").each(function (index, value) {
            $(this).click(function () {
                let formdata = new FormData();
                formdata.append(
                    "ID-reg",
                    $(this.parentElement.parentElement).attr("id")
                );
                formdata.append(
                    "cuentaCodigo-reg",
                    $(this.parentElement.parentElement).attr("idcuenta")
                );
                formdata.append(
                    "subtitulo-reg",
                    $(this.parentElement.parentElement).attr("subtitu")
                );
                formdata.append("accion", "delete");
                ProcesarAjaxLeccion("POST", formdata);
            });
        });
    }
    //declaracion
    $(".paginador2 ul > li").click(function () {
        // $("#paginador ul > li ").attr("active", false);
        $("#paginador2 ul > li ").removeClass("active");
        pag = $(this)
            .find("span")
            .text();
        // $(this).attr("active", true);
        let pagina = $(this)
            .prev()
            .text();
        let ultimo = $("#paginador2 ul > li")
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
            listarDeclaracion(parseInt(pagina) + 1, total);
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

            $("#paginador2 ul > li")
                .last()
                .find("span")
                .text("Siguiente");
            pag = parseInt(
                $("#paginador2 ul > li")
                    .last()
                    .prev()
                    .find("span")
                    .text()
            );
            listarDeclaracion(
                parseInt(
                    $("#paginador2 ul > li")
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
            listarDeclaracion(parseInt(ultimo), total);
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
            $("#paginador2 ul > li")
                .last()
                .find("span")
                .text("Siguiente");
            pag = parseInt(ultimo) - 3;
            listarDeclaracion(parseInt(ultimo) - 3, total);
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
            $("#paginador2 ul > li")
                .last()
                .find("span")
                .text("Siguiente");

            pag = parseInt(ultimo) - 2;
            listarDeclaracion(parseInt(ultimo) - 2, total);
        } else {
            $("#paginador2 ul > li")
                .last()
                .removeClass("active");
            $(this).addClass("active");
            $("#paginador2 ul > li")
                .last()
                .find("span")
                .text("Siguiente");
            listarDeclaracion(pag, total);
        }
    });
    $(".dropdown-menu2 >button").click(function () {
        total = $(this).text();
        listarDeclaracion(pag, total);
    });

    function ProcesarAjaxDeclaracion(metodo, formdata) {
        $.ajax({
            type: metodo,
            url: url + "ajax/declaracionAjax.php",
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
                    listarDeclaracion(pag, total, cuentaleccion);
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
    function listarDeclaracion(paginas, registrototal, codigocuenta) {
        $.ajax({
            type: "GET",
            url: url + "ajax/declaracionAjax.php",
            data: {
                acion: "listar",
                pagina: paginas,
                registros: registrototal,
                busca: codigocuenta
            },
            beforeSend: function () {
                $(".preloader3").html(` <div class="text-center cargando">
        <h2 class="all-tittles">Cargando...</h2>
      </div>`);
            },
            success: function (respuesta) {
                let html = "";
                $("#cargarpaginalista").html("");
                if (respuesta == "ninguno") {
                    $("#paginador2 ul > li")
                        .last()
                        .find("span")
                        .text("Ninguno");
                    $("#paginador2 ul > li")
                        .last()
                        .prev()
                        .removeClass("active");
                    $("#paginador2 ul > li")
                        .last()
                        .addClass("active");
                    html += `<tr ><td colspan="4"class="text-center">NINGUN DATO</td></tr>`;
                    $(".RespuestaListaDeclaracion").html(html);
                } else {
                    let admin = JSON.parse(respuesta);

                    let contador = paginas * registrototal - registrototal;
                    for (var key in admin) {
                        contador++;
                        html += `<tr numero="${contador}" id="${admin[key].idaudio}">
            <td class="text-center">${contador}</td>
            <td class="text-center">${admin[key].codigo_subtitulo}</td>
            <td  class="text-center ">
              <video alt="user-picture" 
              class="img-responsive center-box"style="width:300px;height:60px;" controls >
                <source class="imag" src="${url}adjuntos/audio/${
                            admin[key].nombreAudio
                            }" 
                type="audio/webm">
              </video>
            </td>
            <td class="text-center">
                 <button class="btn btn-danger eliminar-declaracion "><i class="zmdi zmdi-delete"></i></button>
            </td>
            </tr>`;
                        $(".RespuestaListaDeclaracion").html(html);
                    }
                    addEventsButtonsDeclaracion();
                }
                $(".preloader3").html("");
            },
            error: function (e) {
                $(".preloader3").html("");
                swal(
                    "Ocurrió un error inesperado",
                    "Por favor recargue la página",
                    "error"
                );
            }
        });
        return false;
    }
    function addEventsButtonsDeclaracion() {
        $(".eliminar-declaracion").each(function (index, value) {
            $(this).click(function () {
                var formdata = new FormData();
                formdata.append(
                    "ID-reg",
                    $(this.parentElement.parentElement).attr("id")
                );
                formdata.append("accion", "delete");
                ProcesarAjaxDeclaracion("POST", formdata);
            });
        });
    }
    //cuestionario

    $(".FormularioAjaxCuestionario").submit(function (e) {
        e.preventDefault();
        let form = $(this);
        let tipo = form.attr("data-form");
        let metodo = form.attr("method");
        let formdata = new FormData(this);
        formdata.append("accion", tipo);
        // $("#cargarpagina").html(ajax_load);
        ProcesarAjaxCuestionario(metodo, formdata);
    });

    function listarCuestionario(codigocuenta) {
        // $("#cargarpagina").html(ajax_load);
        $.ajax({
            type: "GET",
            url: url + "ajax/cuestionarioclienteAjax.php",
            data: { acion: "listar", busca: codigocuenta },
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
                    $(".RespuestaListaCuestionario").html(
                        "<td colspan='15'>Ningun Dato</td>"
                    );
                } else {
                    let capitulo = JSON.parse(respuesta);
                    let html = "";
                    let contador = 0;
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
                  <button class="btn btn-warning editar-Cuestionario " ><i class="zmdi zmdi-eye"></i> </button>
              </td>
              <td class="text-center">
                   <button class="btn btn-danger eliminar-Cuestionario "><i class="zmdi zmdi-delete"></i></button>
              </td>
              </tr>`;
                        $(".RespuestaListaCuestionario").html(html);
                    }
                    addEventsButtonsCuestionario();
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
    function addEventsButtonsCuestionario() {
        $(".editar-Cuestionario").each(function (index, value) {
            $(this).click(function () {
                var indice = $(this.parentElement.parentElement).attr("numero");
                var rowvaluecues = [];
                $(".RespuestaListaCuestionario > tr").each(function (i, v) {
                    rowvaluecues[i] = $("td", this)
                        .map(function () {
                            return $(this).text();
                        })
                        .get();
                });
                $("#Nombre-reg").val(rowvaluecues[indice - 1][2]);
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
                        $("#P" + count + "-reg").val(rowvaluecues[indice - 1][index + 2]);
                    }
                }

                $("#formulariocapitulo").attr("data-form", "update");
                $("#tituloModalMancapitulo").html(
                    "VISUALIZACION DE RESPUESTAS DEL CUESTIONARIO"
                );
                $("#ventanaModalMancapitulo").modal("show");
            });
        });
        $(".eliminar-Cuestionario").each(function (index, value) {
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
