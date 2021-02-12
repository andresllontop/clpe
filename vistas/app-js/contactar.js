$(document).ready(function() {
  let ajax_load =
    "<div class='progress'>" +
    "<div id='progress_id' class='progress-bar progress-bar-striped active' " +
    "role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width: 45%'>" +
    "n/100</div></div>";
  $(".FormularioAjax").submit(function(e) {
    e.preventDefault();
    var form = $(this);
    // var tipo = form.attr("data-form");
    var metodo = form.attr("method");
    var formdata = new FormData(this);
    (metodo);
    $("#cargarpagina").html(ajax_load);
    ProcesarAjax(metodo, formdata);
  });
});
function ProcesarAjax(metodo, formdata) {
  $.ajax({
    type: metodo,
    url: url + "ajax/contactarAjax.php",
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
      $("#cargarpagina").html("");
      $(".FormularioAjax")[0].reset();
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
