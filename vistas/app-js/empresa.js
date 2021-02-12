$(document).ready(function () {
    listar();

    $(".FormularioAjax").submit(function (e) {
        e.preventDefault();
        var form = $(this);
        var tipo = form.attr("data-form");
        var metodo = form.attr("method");
        var formdata = new FormData(this);
        formdata.append("accion", "update");
        ProcesarAjax(metodo, formdata);
    });
});
function filePreview(input, imagen) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();

        reader.onload = function (e) {
            $(imagen).html(
                "<img style='width:125px;height:189px;' alt='user-picture' class='img-responsive center-box' src='" +
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
function ProcesarAjax(metodo, formdata) {
    $.ajax({
        type: metodo,
        url: url + "ajax/empresaAjax.php",
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
    $.get(url + "ajax/empresaAjax.php", { acion: list }, function (respuesta) {
        $("#Precio-reg").val(JSON.parse(respuesta)[0].precio);
        $("#Nombre-reg").val(JSON.parse(respuesta)[0].EmpresaNombre);
        $("#Direccion-reg").val(JSON.parse(respuesta)[0].EmpresaDireccion);
        $("#Email-reg").val(JSON.parse(respuesta)[0].EmpresaEmail);
        $("#Telefono-reg").val(JSON.parse(respuesta)[0].EmpresaTelefono);
        $("#Telefono2-reg").val(JSON.parse(respuesta)[0].EmpresaTelefono2);
        // $("#Mision-reg").val(JSON.parse(respuesta)[0].mision);
        // $("#Vision-reg").val(JSON.parse(respuesta)[0].vision);
        $("#Url-reg").val(JSON.parse(respuesta)[0].Enlace);
        $("#Facebook-reg").val(JSON.parse(respuesta)[0].facebook);
        $("#Youtube-reg").val(JSON.parse(respuesta)[0].youtube);
        $("#ID-reg").val(JSON.parse(respuesta)[0].idempresa);
        $("#imagePreview").html(
            "<img  alt='user-picture' class='img-responsive  center-box'style='width:125px;height:189px;' src='" +
            url +
            "adjuntos/" +
            JSON.parse(respuesta)[0].EmpresaLogo +
            "' />"
        );
        $("#Historia-reg").Editor();
        $("#Historia-reg").Editor("setText", [
            '<p style="color:black">' + JSON.parse(respuesta)[0].descripcion + "</p>"
        ]);
    });
}
