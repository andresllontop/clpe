$(document).ready(function() {
  $("#vistaLibro").css("display", "none");

  $(".btnAbrirLibro").click(function() {
    let nombrebook = $(this).attr("nombre");
    let codigobook = $(this).attr("codigoLibro");
    let imagenbook = $(this).attr("imagenLibro");
    let descripcionbook = $(this).attr("descripcionLibro");
    let html = `<img style="width: 300px; height: 400px;" src="${url +
      "adjuntos/imagen/" +
      imagenbook}"> `;
    $("#vistaCatalogo").css("display", "none");
    $("#vistaLibro").css("display", "block");
    $(".details p").html(descripcionbook);
    $(".all-titulo").html(nombrebook);
    $(".imgBox").html(html);

    let formdata = new FormData(this);
    formdata.append("codigo-libro", codigobook);
    // ProcesarAjax(
    //   "POST",
    //   formdata
    // );
  });
});
function ProcesarAjax(metodo, formdata) {
  $.ajax({
    type: metodo,
    url: url + "ajax/libroAjax.php",
    data: formdata,
    cache: false,
    contentType: false,
    processData: false,
    success: function(data) {
      (data);
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
