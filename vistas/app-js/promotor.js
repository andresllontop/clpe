$(document).ready(function () {
    let pag = 1;
    let total = 10;
    let ajax_load =
        "<div class='progress'>" +
        "<div id='progress_id' class='progress-bar progress-bar-striped active' " +
        "role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width: 45%'>" +
        "n/100</div></div>";
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
    $("#ImagenPortada-reg").change(function () {
        filePreview(this, "#imagePreview2");
    });
    $("#btnAbrirpromotor").click(function () {
        $("#formulariopromotor").attr("data-form", "save");
        $(".FormularioAjax")[0].reset();
        $("#Historia-reg").Editor();
        $("#Historia-reg").Editor("setText", "");
        $("#Descripcion-reg").Editor();
        $("#Descripcion-reg").Editor("setText", "");
        $("#imagePreview").html("");
        $("#tituloModalManpromotor").html("REGISTRAR PROMOTOR");
        $("#ventanaModalManpromotor").modal("show");
    });

    $(".FormularioAjax").submit(function (e) {
        $("#insertarModal").modal("hide");
        $("#modificarModal").modal("hide");
        e.preventDefault();
        var form = $(this);
        var tipo = form.attr("data-form");
        var metodo = form.attr("method");
        var formdata = new FormData(this);
        var file = $("#Imagen-reg")[0].files[0];
        var file2 = $("#ImagenPortada-reg")[0].files[0];
        var texto = $("#Descripcion-reg").Editor("getText");
        formdata.append("Descripcion-reg", texto);
        var texto = $("#Historia-reg").Editor("getText");
        formdata.append("Historia-reg", texto);
        formdata.append("accion", tipo);
        if (file && file2) {
            $("#cargarpagina").html(ajax_load);
            ProcesarAjax(metodo, formdata);
        } else {
            ("noo");
            if (tipo == "save") {
                swal(
                    "Ocurrió un error inesperado",
                    "Por favor debes ingresar la Foto",
                    "error"
                );
            } else {
                // ("holi");
                $("#cargarpagina").html(ajax_load);
                ProcesarAjax(metodo, formdata);
            }
        }
    });
    function ProcesarAjax(metodo, formdata) {
        $.ajax({
            type: metodo,
            url: url + "ajax/promotorAjax.php",
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
                $("#ventanaModalManpromotor").modal("hide");
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
            url: url + "ajax/promotorAjax.php",
            data: { acion: "listar", pagina: paginas, registros: registrototal },
            // modificar el valor de xhr a nuestro gusto
            success: function (respuesta) {
                // (respuesta);
                $("#cargarpagina").html("");
                if (respuesta == "ninguno") {
                    $("#paginador ul > li")
                        .last()
                        .find("span")
                        .text("Ninguno");
                } else {
                    let capitulo = JSON.parse(respuesta);
                    let html = "";
                    let contador = 0;
                    for (var key in capitulo) {
                        contador++;
                        html += `<tr numero="${contador}" id="${capitulo[key].iddocente}" >
              <td class="text-center ">${contador}</td>
              <td class="text-center">${capitulo[key].nombres}</td>
              <td class="text-center">${capitulo[key].apellidos}</td>
              <td class="text-center">${capitulo[key].email}</td>
              <td class="text-center">${capitulo[key].youtube}</td>
              <td class="text-center">${capitulo[key].descripcion.substring(0, 250)}</td>
              <td class="text-center">${capitulo[key].historia.substring(0, 250)}</td>
              <td  class="text-center "><img 
                src="${url}adjuntos/team/${capitulo[key].foto}"
                alt="user-picture"
                class="img-responsive center-box"style="width:50px;height:60px;"
                /><div class="imag">${capitulo[key].foto}</div></td>
            
              <td  class="text-center "><img 
                src="${url}adjuntos/team/${capitulo[key].fotoPortada}"
                alt="user-picture"
                class="img-responsive center-box"style="width:50px;height:60px;"
                /><div class="imag">${capitulo[key].fotoPortada}</div></td>
              <td class="text-center ">
                  <button class="btn btn-success editar-promotor " ><i class="zmdi zmdi-refresh"></i> </button>
              </td>
              <td class="text-center">
                   <button class="btn btn-danger eliminar-promotor "><i class="zmdi zmdi-delete"></i></button>
              </td>
              </tr>`;
                        $(".RespuestaLista").html(html);
                    }
                    addEventsButtonspromotor();
                }
            },
            error: function (e) {
                e;

                swal(
                    "Ocurrió un error inesperado",
                    "Por favor recargue la página",
                    "error"
                );
            }
        });
        return false;
    }
    function addEventsButtonspromotor() {
        $(".editar-promotor").each(function (index, value) {
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
                });
                $("tbody > tr").each(function (i, v) {
                    rowvalue2[i] = $("td > .imag", this)
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
                $("#Nombres-reg").val(rowvalue[indice - 1][1]);
                $("#Apellidos-reg").val(rowvalue[indice - 1][2]);
                $("#Email-reg").val(rowvalue[indice - 1][3]);
                $("#Youtube-reg").val(rowvalue[indice - 1][4]);
                $("#Historia-reg").Editor();
                $("#Historia-reg").Editor("setText", [
                    '<p style="color:black">' + rowvalue[indice - 1][6] + "</p>"
                ]);
                $("#Descripcion-reg").Editor();
                $("#Descripcion-reg").Editor("setText", [
                    '<p style="color:black">' + rowvalue[indice - 1][5] + "</p>"
                ]);
                $("#ID-reg").val($(this.parentElement.parentElement).attr("id"));
                $("#imagePreview").html(
                    "<img  alt='user-picture' class='img-responsive  center-box'style='width:120px;height:150px;' src='" +
                    url +
                    "adjuntos/team/" +
                    rowvalue2[indice - 1][0] +
                    "' />"
                );
                $("#imagePreview2").html(
                    "<img  alt='user-picture' class='img-responsive  center-box'style='width:120px;height:150px;' src='" +
                    url +
                    "adjuntos/team/" +
                    rowvalue2[indice - 1][1] +
                    "' />"
                );

                $("#formulariopromotor").attr("data-form", "update");
                $("#tituloModalManpromotor").html("EDITAR PROMOTOR");
                $("#ventanaModalManpromotor").modal("show");
            });
        });
        $(".eliminar-promotor").each(function (index, value) {
            $(this).click(function () {
                var formdata = new FormData();
                var va = $(this.parentElement.parentElement).attr("id");
                va;
                formdata.append("ID-reg", va);
                formdata.append("accion", "delete");
                ProcesarAjax("POST", formdata);
            });
        });
    }
});
