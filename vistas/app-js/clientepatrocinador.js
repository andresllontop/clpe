$(document).ready(function () {
    let ajax_load =
        "<div class='progress'>" +
        "<div id='progress_id' class='progress-bar progress-bar-striped active' " +
        "role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width: 45%'>" +
        "n/100</div></div>";

    listar();
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
            success: function (data) {
                console.log(data);
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

                    listar();
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
    function listar() {
        $.ajax({
            type: "GET",
            url: url + "ajax/administradorAjax.php",
            data: {
                acion: "listar",
                pagina: 0,
                registros: 0,
                usuario: "alumno",
                aestado: "patrocinador"
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
                console.log(respuesta);
                console.log("3");
                if (respuesta == "ninguno") {
                    $(".RespuestaLista").html("<td colspan='13'>Ningun Dato</td>");
                } else {
                    let admin = JSON.parse(respuesta);
                    let html = "";
                    let estado = "";
                    let estado2 = "";
                    let contador = 0;
                    for (var key in admin) {
                        if (admin[key]["estado"] == "Inactivo") {
                            estado = " zmdi-minus";
                            estado2 = " btn-warning";
                        } else {
                            estado = " zmdi-check-all";
                            estado2 = " btn-success";
                        }
                        if (admin[key]["patrocinador"] == "no") {
                            estado4 = " zmdi-minus";
                            estado3 = " btn-warning";
                        } else {
                            estado4 = " zmdi-check-all";
                            estado3 = " btn-success";
                        }
                        contador++;
                        html += `<tr numero="${contador}" idcuenta="${
                            admin[key].idcuenta
                            }"codigocuenta="${admin[key].Cuenta_Codigo}"state="${
                            admin[key].estado
                            }"patro="${admin[key].patrocinador}" id="${admin[key].id}">
            <td class="text-center">${contador}</td>
            <td class="text-center">${admin[key].Cuenta_Codigo}</td>
            <td class="text-center">${admin[key].AdminNombre}</td>
            <td class="text-center">${admin[key].AdminApellido}</td>
            <td class="text-center">${admin[key].AdminTelefono}</td>
            <td class="text-center">${admin[key].email}</td>
            <td class="text-center">${admin[key].usuario}</td>
            <td class="text-center">${admin[key].AdminMonto}</td>
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
        /></td>
            <td  class="text-center "><img
        src="${url}adjuntos/deposito/${admin[key].voucher}"
        alt="user-picture"
        class="img-responsive center-box" style="width:50px;height:60px;"
        /><div class="imag" style="display:none;">${
                            admin[key].voucher
                            }</div></td>
            <td class="text-center">
                <button class="btn ${estado2} estado-Admin" ><i class="zmdi ${estado}"></i> </button>
            </td>
            <td class="text-center">
                <button class="btn ${estado3} patrocinador-Admin" ><i class="zmdi ${estado4}"></i> </button>
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
                    "<img  alt='user-picture' class='img-responsive  center-box' style='width:360px; height:244px;' src='" +
                    url +
                    "adjuntos/deposito/" +
                    rowvalue2[indice - 1][0] +
                    "' />"
                );
                $("#id-reg").val($(this.parentElement.parentElement).attr("id"));
                $("#nombre-reg").val(rowvalue[indice - 1][2]);
                $("#apellido-reg").val(rowvalue[indice - 1][3]);
                $("#telefono-reg").val(rowvalue[indice - 1][4]);
                $("#email-reg").val(rowvalue[indice - 1][5]);
                $("#usuario-reg").val(rowvalue[indice - 1][6]);
                $("#monto-reg").val(rowvalue[indice - 1][7]);
                $("#especialidad-reg").val(rowvalue[indice - 1][8]);
                var formdata = new FormData();
                formdata.append("Clave-reg", rowvalue[indice - 1][9]);
                formdata.append("accion", "desencriptar");
                ProcesarAjax("POST", formdata);
                $("#formularioCliente").attr("data-form", "update");
                $("#tituloModalManCliente").html(
                    "ACTUALIZAR MONTO CANCELADO DEL ALUMNO"
                );
                $("#ventanaModalManCliente").modal("show");
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
                    formdata.append("Patrocinador-reg", "si");
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
                    formdata.append("Patrocinador-reg", "no");
                    formdata.append("accion", "updatePatrocinador");
                    ProcesarAjax("POST", formdata);
                }
            });
        });
    }
});
