$(document).ready(function () {
    let ajax_load = `<div  style="position: absolute;
  width: 100%;  z-index: 9999;  background-color: #c5b2b25c;  height: 100%;
  padding: 0 40%;padding-top: 200px;"><div class="progress text-center" style="width:265;">
  Ingresando... 
    <div id="bulk-action-progbar " class="progress-bar progress-bar-striped active" role="progressbar"
    aria-valuenow="1" aria-valuemin="0" aria-valuemax="100" style="width:1%">                 
    </div></div>
    </div>`;
    $(".FormularioAjaxLogear").submit(function (e) {
        e.preventDefault();
        var form = $(this);
        // var tipo = form.attr("data-form");
        var metodo = form.attr("method");
        var formdata = new FormData(this);
        $("#cargar").html(ajax_load);
        $("body").css("overflow-y", "hidden");
        ProcesarAjax(metodo, formdata, "ajax/loginAjax.php");
    });
    $(".FormularioAjaxRegistrar").submit(function (e) {
        e.preventDefault();
        var form = $(this);
        // var tipo = form.attr("data-form");
        var metodo = form.attr("method");
        var formdata = new FormData(this);
        formdata.append("accion", "save");
        $("#cargarpagina").html(ajax_load);
        ProcesarAjaxRegis(metodo, formdata, "ajax/publicoAjax.php");
    });
    function ProcesarAjax(metodo, formdata, urldestino) {
        $.ajax({
            type: metodo,
            url: url + urldestino,
            data: formdata,
            cache: false,
            contentType: false,
            processData: false,
            xhr: function () {
                var xhr = new window.XMLHttpRequest();
                //Upload progress, request sending to server
                xhr.upload.addEventListener(
                    "progress",
                    function (evt) {
                        console.log("in Upload progress");
                        console.log("Upload Done");
                    },
                    false
                );
                //Download progress, waiting for response from server
                xhr.addEventListener(
                    "progress",
                    function (e) {
                        console.log("in Download progress");
                        if (e.lengthComputable) {
                            //percentComplete = (e.loaded / e.total) * 100;
                            percentComplete = parseInt((e.loaded / e.total) * 100, 10);
                            console.log(percentComplete);
                            $("#bulk-action-progbar").data("aria-valuenow", percentComplete);
                            $("#bulk-action-progbar").css("width", percentComplete + "%");
                        } else {
                            console.log("Length not computable.");
                        }
                    },
                    false
                );
                return xhr;
            },
            success: function (data) {
                data;
                $(".FormularioAjaxLogear")[0].reset();
                $(".RespuestaAjax").html(data);
                $("#cargar").html("");
            },
            error: function (e) {
                $("#cargar").html("");
                swal(
                    "Ocurrió un error inesperado",
                    "Por favor recargue la página",
                    "error"
                );
            }
        });
        return false;
    }
    $(".FormularioAjaxRestablecer").submit(function (e) {
        e.preventDefault();
        var form = $(this);
        var metodo = form.attr("method");
        var formdata = new FormData(this);
        ProcesarAjaxRestablecer(metodo, formdata, "ajax/sendemail.php");
    });
    function ProcesarAjaxRestablecer(metodo, formdata, urldestino) {
        $.ajax({
            type: metodo,
            url: url + urldestino,
            data: formdata,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                data;
                $(".FormularioAjaxRestablecer")[0].reset();
                // $("#restabler-datos").modal("hide");
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
    $(".FormularioAjaxRegistrar").submit(function (e) {
        e.preventDefault();
        var form = $(this);
        // var tipo = form.attr("data-form");
        var metodo = form.attr("method");
        var formdata = new FormData(this);
        formdata.append("accion", "save");
        formdata;
        ProcesarAjaxRegis(metodo, formdata, "ajax/publicoAjax.php");
    });

    function ProcesarAjaxRegis(metodo, formdata, urldestino) {
        $.ajax({
            type: metodo,
            url: url + urldestino,
            data: formdata,
            cache: false,
            contentType: false,
            processData: false,
            success: function (data) {
                // $(".FormularioAjaxRegistrar")[0].reset();
                // $("#registrar").modal("hide");
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
});
