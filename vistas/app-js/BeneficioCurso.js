$(document).ready(function () {
    listar();
    let ajax_load =
        "<div class='progress'>" +
        "<div id='progress_id' class='progress-bar progress-bar-striped active' " +
        "role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width: 45%'>" +
        "n/100</div></div>";

    $("#btnAbrirnoticia").click(function () {
        $(".FormularioAjax")[0].reset();
        $("#formularionoticia").attr("data-form", "save");
        $("#tituloModalMannoticia").html("REGISTRAR BENEFICIO");
        $("#ventanaModalMannoticia").modal("show");
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
        formdata.append("Item-reg", 1);
        formdata.append("Titulo-reg", "");
        $("#cargarpagina").html(ajax_load);
        ProcesarAjax(metodo, formdata);
    });
    function ProcesarAjax(metodo, formdata) {
        $.ajax({
            type: metodo,
            url: url + "ajax/subitemAjax.php",
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
                $("#ventanaModalMannoticia").modal("hide");
                swal({
                    title: JSON.parse(data).Titulo,
                    text: JSON.parse(data).Texto,
                    type: JSON.parse(data).Tipo,
                    confirmButtonText: "Aceptar"
                });
                listar();
                $(".FormularioAjax")[0].reset();
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
        var list = "listar";
        $.get(
            url + "ajax/subitemAjax.php",
            { acion: list, item: "pregunta", valor: 1 },
            function (respuesta) {
                var subitem = JSON.parse(respuesta);
                var html = "";
                var contador = 0;
                for (var key in subitem) {
                    contador++;
                    html += `<tr numero="${contador}" id="${subitem[key].idsubitem}">
              <td class="text-center ">${contador}</td>
              <td class="text-center">${subitem[key].detalle}</td>
              <td class="text-center ">
                  <button class="btn btn-info editar-Noticia " ><i class="zmdi zmdi-refresh"></i> </button>
              </td>
              <td class="text-center">
                   <button class="btn btn-danger eliminar-Noticia "><i class="zmdi zmdi-delete"></i></button>
              </td>
              </tr>`;
                    $(".RespuestaLista").html(html);
                }
                addEventsButtonsNoticia();
            }
        );
    }
    function addEventsButtonsNoticia() {
        $(".editar-Noticia").each(function (index, value) {
            $(this).click(function () {
                var indice = $(this.parentElement.parentElement).attr("numero");
                var rowvalue = [];
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
                $("#ID-reg").val($(this.parentElement.parentElement).attr("id"));
                $("#Detalle-reg").val(rowvalue[indice - 1][1]);

                $("#formularionoticia").attr("data-form", "update");
                $("#tituloModalMannoticia").html("EDITAR BENEFICIO");
                $("#ventanaModalMannoticia").modal("show");
            });
        });
        $(".eliminar-Noticia").each(function (index, value) {
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
