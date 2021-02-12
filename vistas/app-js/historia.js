$(document).ready(function() {
  listar();

  $(".FormularioAjax").submit(function(e) {
    e.preventDefault();
    var form = $(this);
    var tipo = form.attr("data-form");
    var metodo = form.attr("method");
    var formdata = new FormData(this);
    formdata.append("accion", "updateHistoria");
    var texto = $("#Historia-reg").Editor("getText");
    formdata.append("Historia-reg", texto);
    ProcesarAjax(metodo, formdata);
  });
});
function ProcesarAjax(metodo, formdata) {
  $.ajax({
    type: metodo,
    url: url + "ajax/empresaAjax.php",
    data: formdata,
    cache: false,
    contentType: false,
    processData: false,
    success: function(data) {
      (data);
      swal({
        title: JSON.parse(data).Titulo,
        text: JSON.parse(data).Texto,
        type: JSON.parse(data).Tipo,
        confirmButtonText: "Aceptar"
      });
    },
    error: function(e) {
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
  $.get(url + "ajax/empresaAjax.php", { acion: list }, function(respuesta) {
    $("#ID-reg").val(JSON.parse(respuesta)[0].idempresa);
    $("#Historia-reg").Editor();
    $("#Historia-reg").Editor("setText", [
      '<p style="color:black">' + JSON.parse(respuesta)[0].descripcion + "</p>"
    ]);
  });
}
