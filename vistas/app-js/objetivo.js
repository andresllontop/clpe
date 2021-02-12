$(document).ready(function () {
    listar();
    let ajax_load =
        "<div class='progress'>" +
        "<div id='progress_id' class='progress-bar progress-bar-striped active' " +
        "role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width: 45%'>" +
        "n/100</div></div>";
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
    $("#Imagen-reg").change(function () {
        filePreview(this, "#imagePreview");
    });
    $("#btnAbrirnoticia").click(function () {
        $(".FormularioAjax")[0].reset();
        $("#imagePreview").html("");
        $("#formularionoticia").attr("data-form", "save");
        $("#tituloModalMannoticia").html("REGISTRAR NOTICIA");
        $("#ventanaModalMannoticia").modal("show");
    });

    $(".FormularioAjax").submit(function (e) {
        $("#formularionoticia").attr("data-form", "update");
        e.preventDefault();
        var form = $(this);
        var tipo = form.attr("data-form");
        var metodo = form.attr("method");
        var formdata = new FormData(this);
        formdata.append("accion", tipo);
        formdata.append("Titulo-reg", "");
        var texto = $("#Detalle-reg").Editor("getText");
        formdata.append("Detalle-reg", texto);
        formdata.append("Item-reg", 4);

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
                swal({
                    title: JSON.parse(data).Titulo,
                    text: JSON.parse(data).Texto,
                    type: JSON.parse(data).Tipo,
                    confirmButtonText: "Aceptar"
                });
                $(".FormularioAjax")[0].reset();
                $(".Editor-container").html("");
                listar();
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
            { acion: list, item: "pregunta", valor: 4 },
            function (respuesta) {
                var noticia = JSON.parse(respuesta);
                for (var key in noticia) {
                    $("#ID-reg").val(`${noticia[key].idsubitem}`);
                    $("#imagePreview").html(
                        "<img  alt='user-picture' class='img-responsive  center-box'style='width:480px;height:300px;' src='" +
                        url +
                        "adjuntos/slider/" +
                        noticia[0]["imagen"] +
                        "' />"
                    );
                    $("#Detalle-reg").Editor();
                    $("#Detalle-reg").Editor("setText", [
                        `<p style="color:black"> ${noticia[key].detalle}</p>`
                    ]);
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
                //   $("#Titulo-reg").val(rowvalue[indice - 1][1]);
                //   $("#Descripcion-reg").val(rowvalue[indice - 1][2]);
                $("#imagePreview").html(
                    "<img width='244' alt='user-picture' class='img-responsive  center-box' src='" +
                    url +
                    "adjuntos/slider/" +
                    rowvalue2[indice - 1] +
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
