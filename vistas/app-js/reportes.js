$(document).ready(function() {
  let ajax_load =
    "<div class='progress'>" +
    "<div id='progress_id' class='progress-bar progress-bar-striped active' " +
    "role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width: 45%'>" +
    "n/100</div></div>";
  $("#btn-Estudiante").click(function(e) {
    ("metodo");
    var formdata = new FormData();

    formdata.append("accion", "alumnos");
    // $("#cargarpagina").html(ajax_load);
    ProcesarAjax("POST", formdata);
  });
});
function ProcesarAjax(metodo, formdata) {
  $.ajax({
    type: metodo,
    url: url + "ajax/reporteAjax.php",
    data: formdata,
    cache: false,
    contentType: false,
    processData: false,
    success: function(data) {
      (data);
      $("#cargarpagina").html("");
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
