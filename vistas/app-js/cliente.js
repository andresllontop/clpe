$(document).ready(function () {
    let ajax_load = `<div  style="position: fixed; top:0;left:0;
    width: 100%;  z-index: 9999;  background-color:#2d394545;  height: 100%;
    padding: 0 40%;padding-top: 200px;"><div class="progress text-center " style="width:265;">
    Cargando... 
      <div id="bulk-action-progbar " class="progress-bar progress-bar-striped active" role="progressbar"
      aria-valuenow="1" aria-valuemin="0" aria-valuemax="100" style="width:1%">                 
      </div></div>
      </div>`;
    let pag = 1;
    let total = 5;
    listar(pag, total);
    $("#NameRecomendado-reg").keyup(function () {
        var ValorBusqueda = $(this).val();
        console.log(ValorBusqueda);
        var formdata = new FormData();
        formdata.append("dato-reg", ValorBusqueda);
        formdata.append("accion", "search");
        ListarBusquedaAjax("POST", formdata);
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
        console.log(tipo);
        formdata.append("accion", tipo + "Monto");
        $("#cargar").html(ajax_load);
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
        $("#cargar").html(ajax_load);
        ProcesarAjaxArbol(metodo, formdata);
    });
    function ListarBusquedaAjax(metodo, formdata) {
        $.ajax({
            type: metodo,
            url: url + "ajax/administradorAjax.php",
            data: formdata,
            cache: false,
            contentType: false,
            processData: false,
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
            success: function (data) {
                $("#cargar").html("");
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
                $("#cargar").html("");
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
            success: function (data) {
                console.log(data);
                $("#cargar").html("");
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
                $("#cargar").html("");
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
        $("#cargar").html(ajax_load);
        $.ajax({
            type: "GET",
            url: url + "ajax/administradorAjax.php",
            data: {
                acion: "listar",
                pagina: paginas,
                registros: registrototal,
                usuario: "alumno",
                aestado: "inactivo"
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
                $("#cargar").html("");
                console.log(respuesta);
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
                    let estado3 = "";
                    let estado4 = "";
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
                            }" numero="${contador}" NameRecomendado="${
                            admin[key].NombreRecomendado
                            }" idcuenta="${admin[key].idcuenta}"codigocuenta="${
                            admin[key].Cuenta_Codigo
                            }"state="${admin[key].estado}"patro="${
                            admin[key].patrocinador
                            }" idelete="${admin[key].id}">
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
                <button class="btn btn-info editar-Admin" ><i class="zmdi zmdi-refresh"></i> </button>
            </td>
            <td class="text-center">
                 <button class="btn btn-danger eliminar-Admin "><i class="zmdi zmdi-delete"></i></button>
            </td>
            </tr>`;

                        $(".RespuestaLista").html(html);
                    }
                    listarArbol();
                    addEventsButtonsAdmin();
                }
            },
            error: function (e) {
                $("#cargar").html("");
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
                $("#cargar").html("");
                // console.log(respuesta);
                let admin = JSON.parse(respuesta);
                let html = "";
                var rowvalue2 = [];
                $("tbody > tr").each(function (i, v) {
                    rowvalue2[i] = $("td", this)
                        .map(function () {
                            return $(this).html();
                        })
                        .get();
                });

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
                        }
                    }
                }
            },
            error: function (e) {
                $("#cargar").html("");
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

                $("#id-reg").val($(this.parentElement.parentElement).attr("idelete"));
                $("#nombre-reg").val(rowvalue[indice - 1][2]);
                $("#apellido-reg").val(rowvalue[indice - 1][3]);
                $("#telefono-reg").val(rowvalue[indice - 1][4]);
                $("#email-reg").val(rowvalue[indice - 1][5]);
                $("#usuario-reg").val(rowvalue[indice - 1][6]);
                $("#monto-reg").val(rowvalue[indice - 1][10]);
                $("#especialidad-reg").val(rowvalue[indice - 1][7]);
                var formdata = new FormData(this);
                formdata.append("Clave-reg", rowvalue[indice - 1][8]);
                formdata.append("accion", "desencriptar");
                $("#cargar").html(ajax_load);
                ProcesarAjax("POST", formdata);
                $("#formularioCliente").attr("data-form", "update");
                $("#tituloModalManCliente").html(
                    "ACTUALIZAR MONTO CANCELADO DEL ALUMNO"
                );
                $("#ventanaModalManCliente").modal("show");
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
                var formdata = new FormData();
                formdata.append(
                    "dato-reg",
                    $(this.parentElement.parentElement).attr("namerecomendado")
                );
                formdata.append("accion", "search");
                ListarBusquedaAjax("POST", formdata);
                $("#CodigoPadre-reg").val($(this).attr("cuentapadre"));
                $("#formularioRecomendado").attr("data-form", "save");
                $("#tituloModalManRecomendado").html("REGISTRAR RECOMENDADO");
                $("#ventanaModalManRecomendado").modal("show");
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
                $("#cargar").html(ajax_load);
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
                    formdata.append("Estado-reg", "Activo");
                    formdata.append("accion", "updateEstado");
                    $("#cargar").html(ajax_load);
                    ProcesarAjax("POST", formdata);
                    SendEmailAjax(formdata);
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
                    formdata.append("Estado-reg", "Inactivo");
                    formdata.append("accion", "updateEstado");
                    $("#cargar").html(ajax_load);
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
                    $("#cargar").html(ajax_load);
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
                    $("#cargar").html(ajax_load);
                    ProcesarAjax("POST", formdata);
                }
            });
        });
    }
    function SendEmailAjax(formdata) {
        $.ajax({
            type: "POST",
            url: url + "ajax/sendemailMatriculaAjax.php",
            data: formdata,
            cache: false,
            contentType: false,
            processData: false,
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
            success: function (data) {
                $("#cargar").html("");
                console.log("enviando al correo: ");
                console.log(data);
            },
            error: function (e) {
                $("#cargar").html("");
                swal(
                    "Ocurrió un error inesperado",
                    "Por favor recargue la página",
                    "error"
                );
            }
        });
        return false;
    }

    //   $("#Imagen-reg").change(function() {
    //     filePreview(this);
    //   });
    //   function filePreview(input) {
    //     if (input.files && input.files[0]) {
    //       var reader = new FileReader();
    //       reader.onload = function(e) {
    //         $("#imagePreview").html(
    //           "<img width='244' alt='user-picture' class='img-responsive  center-box' src='" +
    //             e.target.result +
    //             "' />"
    //         );
    //       };
    //       reader.readAsDataURL(input.files[0]);
    //     }
    //   }
});
