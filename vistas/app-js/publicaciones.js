$(document).ready(function () {
    listar();
    let ajax_load =
        "<div class='progress'>" +
        "<div id='progress_id' class='progress-bar progress-bar-striped active' " +
        "role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width: 45%'>" +
        "n/100</div></div>";
    $("#Imagen-reg").change(function () {
        filePreview(this, "#imagePreview");
    });
    function filePreview(input, imagen) {
        if (input.files && input.files[0]) {
            let reader = new FileReader();

            reader.onload = function (e) {
                $(imagen).html(
                    "<img style='width:480px;height:300px;' alt='user-picture' class='img-responsive center-box' src='" +
                    e.target.result +
                    "' />"
                );
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#btnAbrirnoticia").click(function () {
        $(".FormularioAjax")[0].reset();
        $("#Imagen-reg").html("");
        $("#formularionoticia").attr("data-form", "save");
        $("#tituloModalMannoticia").html("REGISTRAR NOTICIA");
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
        formdata.append("Titulo-reg", "");
        formdata.append("Item-reg", 5);
        var file1 = $("#Imagen-reg")[0].files[0];

        if (file1) {
            $("#cargarpagina").html(ajax_load);
            ProcesarAjax(metodo, formdata);
        } else {
            // ("noo");
            if (tipo == "save") {
                swal(
                    "Ocurrió un error inesperado",
                    "Por favor debes ingresar las imagen",
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
            { acion: list, item: "publicidad", valor: 5 },
            function (respuesta) {
                var subitem = JSON.parse(respuesta);
                var html = "";
                var contador = 0;
                for (var key in subitem) {
                    contador++;
                    html += `<tr numero="${contador}" id="${subitem[key].idsubitem}">
            <td class="text-center ">${contador}</td>
            <td class="text-center">${subitem[key].detalle}</td>
            <td  class="text-center seleccionar"><img 
            src="${url}adjuntos/slider/${subitem[key].imagen}"
            alt="user-picture"
            class="img-responsive center-box"style="width:140px;height:130px;"
            /><div class="imag">${subitem[key].imagen}</div></td>
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
                $("#ID-reg").val($(this.parentElement.parentElement).attr("id"));
                $("#Detalle-reg").val(rowvalue[indice - 1][1]);
                $("#imagePreview").html(
                    "<img  alt='user-picture' class='img-responsive  center-box'style='width:180px;height:200px;' src='" +
                    url +
                    "adjuntos/slider/" +
                    rowvalue2[indice - 1][0] +
                    "' />"
                );
                $("#formularionoticia").attr("data-form", "update");
                $("#tituloModalMannoticia").html("EDITAR NOTICIA");
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
