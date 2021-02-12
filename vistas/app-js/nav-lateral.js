$(document).ready(function() {
  listar();
  function listar() {
    $.ajax({
      type: "GET",
      url: url + "ajax/contactarAjax.php",
      data: { acion: "data" },

      success: function(respuesta) {
        // (respuesta);
        let sms = JSON.parse(respuesta);
        let contador = 0;
        for (let key in sms) {
          if (sms[key]["mensajeEstado"] == 0) {
            contador++;
          }
        }
        $(".Noti-Mensaje").html(
          '<i class="zmdi zmdi-email zmdi-hc-fw"></i>&nbsp; Mensajes <span id=""class="label label-danger pull-right label-mhover">' +
            contador +
            "</span></a>"
        );
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
});
