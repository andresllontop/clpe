$(document).ready(function () {
    let ajax_load =
        "<div class='progress'>" +
        "<div id='progress_id' class='progress-bar progress-bar-striped active' " +
        "role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width: 45%'>" +
        "n/100</div></div>";

    listar();
    function ProcesarAjax(metodo, formdata) {
        $.ajax({
            type: metodo,
            url: url + "ajax/visitaAjax.php",
            data: formdata,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                console.log(data);
                swal({
                    title: JSON.parse(data).Titulo,
                    text: JSON.parse(data).Texto,
                    type: JSON.parse(data).Tipo,
                    confirmButtonText: "Aceptar"
                });

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
        $.ajax({
            type: "GET",
            url: url + "ajax/visitaAjax.php",
            data: {
                acion: "listar"
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
            success: function (data) {
                console.log(data);
                console.log("visita");
                if (data == "ninguno") {
                    $(".RespuestaLista").html("<td colspan='6'>Ningun Dato</td>");
                } else {
                    let admin = JSON.parse(data);
                    let html = "";
                    let estado = "";
                    let estado2 = "";
                    let contador = 0;
                    for (var key in admin) {
                        contador++;
                        html += `<tr numero="${contador}" id="${admin[key].idvisita}">
            <td class="text-center">${contador}</td>
            <td class="text-center">${admin[key].ip}</td>
            <td class="text-center">${admin[key].pagina}</td>
            <td class="text-center">${admin[key].fecha}</td>
            <td class="text-center">${admin[key].horaInicio}</td>
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
                formdata.append("accion", "delete");
                ProcesarAjax("POST", formdata);
            });
        });
    }
});
