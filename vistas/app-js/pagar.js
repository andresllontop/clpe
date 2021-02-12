$(document).ready(function () {
    let ajax_load =
        `<div class="progress">
        <div id="bulk-action-progbar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100" style="width:1%">                 
        </div>
        </div>`;
    let ajax_load2 =
        `<div class="progress">
        <div id="bulk-action-progbar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100" style="width:1%">                 
        </div>
        </div>`;
    let ajax_load3 = `<div id="circle" style="z-index:3333;">
        <div class="loader">
          <div class="loader">
              <div class="loader">
                 <div class="loader">
      
                 </div>
              </div>
          </div>
        </div>
      </div> `;

    $(".FormularioAjax").submit(function (e) {
        e.preventDefault();
        var form = $(this);
        var tipo = form.attr("data-form");
        var metodo = form.attr("method");
        var formdata = new FormData(this);
        $("#cargarpagina").html(ajax_load);
        ProcesarAjax(metodo, formdata);
    });


    function ProcesarAjax(metodo, formdata) {
        $("#paypal-button").html("");
        $.ajax({
            type: metodo,
            url: url + "ajax/pagarAjax.php",
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
                        // console.log("in Upload progress");
                        // console.log("Upload Done");
                    },
                    false
                );
                //Download progress, waiting for response from server
                xhr.addEventListener(
                    "progress",
                    function (e) {
                        // console.log("in Download progress");
                        if (e.lengthComputable) {
                            //percentComplete = (e.loaded / e.total) * 100;
                            percentComplete = parseInt((e.loaded / e.total) * 100, 10);
                            // console.log(percentComplete);
                            $("#bulk-action-progbar").data("aria-valuenow", percentComplete);
                            $("#bulk-action-progbar").css("width", percentComplete + "%");
                            $("#bulk-action-progbar").html(Math.round(percentComplete * 100) + "%");
                        } else {
                            // console.log("Length not computable.");
                        }
                    },
                    false
                );
                return xhr;


            },
            success: function (data) {
                // console.log(data);
                $("#cargarpagina").html("");
                paypalScript(data);
                $("#formularioCliente").attr("data-form", "save");
                $("#tituloModalManCliente").html("¡PASO FINAL!");
                $("#ventanaModalManCliente").modal("show");
            },
            error: function (e) {
                $("#cargarpagina").html("");
                swal(
                    "Ocurrió un error inesperado",
                    "Por favor recargue la página",
                    "error"
                );
            }
        });
        return false;
    }


    function paypalScript(json) {
        // $("#cargar").html(ajax_load3);
        let Lista = JSON.parse(json);
        console.log(Lista['precio']);
        paypal.Button.render({
            // Configure environment
            env: 'production',
            client: {
                sandbox: 'AbPngN61063LBnjzvXC9Ew9h6eNpGj9Dob7YEQBdaCycUqSrtMnI1yUeyDs1iG_b-RE-FICaZnO88LFp',
                production: 'ATaQMmL2ETrwcIjc7ShAElf0iWHtNmOLhL5Cmie85PZSQgspq0KNsJGs3LvIaS7brDkX3L-2J3dN1H0u'
            },
            // Customize button (optional)
            // locale: 'en_US',
            style: {
                size: 'large',
                color: 'gold',
                shape: 'pill',
                tagline: false,
                label: "paypal",
                fundingicons: 'true',
            },
            funding: {
                allowed: [paypal.FUNDING.CARD],
                disallowed: [paypal.FUNDING.CREDIT]
            }
            ,
            // Enable Pay Now checkout flow (optional)
            commit: true,
            // Set up a payment
            payment: function (data, actions) {
                return actions.payment.create({
                    transactions: [
                        {
                            amount: { total: Lista['precio'], currency: 'USD' },
                            description: "Compra de Curso CLPE",
                            custom: Lista['Nombre'] + "#" + Lista['Apellido'] + "#" + Lista['Email'] +
                                "#" + Lista['codigonumero'] + Lista['Telefono'] + "#" + Lista['pais']
                        }
                    ]
                });
            },
            // Execute the payment
            onAuthorize: function (data, actions) {
                return actions.payment.execute().then(function () {
                    // Show a confirmation message to the buyer
                    // var formdata = new FormData(this);
                    // formdata.append("paymentToken", data.paymentToken);
                    // formdata.append("paymentID", data.paymentID);
                    // window.location = 'Ajax/paypalAjax.php?paymentToken=' + data.paymentToken + '&paymentID=' + data.paymentID;
                    // window.alert('Thank you for your purchase!');
                    $("#cargarpagina2").html(ajax_load2);
                    $.ajax({
                        type: "GET",
                        url: url + 'ajax/paypalAjax.php',
                        data: 'paymentToken=' + data.paymentToken + '&paymentID=' + data.paymentID,
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
                                    // console.log("in Download progress");
                                    if (e.lengthComputable) {
                                        //percentComplete = (e.loaded / e.total) * 100;
                                        percentComplete = parseInt((e.loaded / e.total) * 100, 10);
                                        // console.log(percentComplete);
                                        $("#bulk-action-progbar").data("aria-valuenow", percentComplete);
                                        $("#bulk-action-progbar").css("width", percentComplete + "%");
                                        $("#bulk-action-progbar").html(Math.round(percentComplete * 100) + "%");
                                    } else {
                                        console.log("Length not computable.");
                                    }
                                },
                                false
                            );
                            return xhr;
                        },
                        success: function (data) {
                            console.log(data);
                            $(".FormularioAjax")[0].reset();
                            $("#cargarpaginas").html("");
                            $("#ventanaModalManCliente").modal("hide");
                            swal({
                                title: JSON.parse(data).Titulo,
                                text: JSON.parse(data).Texto,
                                type: JSON.parse(data).Tipo,
                                confirmButtonText: "Aceptar"
                            });
                        },
                        error: function (xhr, status, error) {
                            var errorMessage = xhr.status + ': ' + xhr.statusText
                            console.log('Error - ' + errorMessage);

                            $("#cargarpaginas").html("");
                            $("#ventanaModalManCliente").modal("hide");
                            swal(
                                "Ocurrió un error inesperado",
                                "Por favor recargue la página",
                                "error"
                            );
                        }
                    });
                });
            }
        }, '#paypal-button');

    }

});
