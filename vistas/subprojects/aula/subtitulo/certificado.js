var beanPaginationCertificado;
var certificadoSelected;
var beanRequestCertificado = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestCertificado.entity_api = 'certificado';
    beanRequestCertificado.operation = 'obtener';
    beanRequestCertificado.type_request = 'GET';

    document.querySelector("#nav-certificado").onclick = () => {

        addEventsButtonsCertificado();

    }


    $("#modalCargandoCertificado").on('shown.bs.modal', function () {
        processAjaxCertificado();
    });
    $("#modalCargandoCertificado").on('hide.bs.modal', function () {
        beanRequestCertificado.operation = 'obtener';
        beanRequestCertificado.type_request = 'GET';
    });

});
function addCertificado(certificado = undefined) {
    addClass(document.querySelector("#sectionLeccion"), "d-none");
    removeClass(document.querySelector("#htmlCertificado"), "d-none");
    if (certificado != undefined) {


        if (certificado.indicador <= 1) {
            document.querySelector("#htmlCertificado").innerHTML = `
           

        <div class="container pt-4">
        <h3 class="text-center w-100 mb-2 f-weight-700" style="font-size: 2.3em;">GRACIAS POR TERMINAR CON ÉXITO LA LECTURA Y APLICACIÓN DEL LIBRO <span>PIENSE Y HÁGASE RICO</span>
        </h3>
        <h4 class="text-center"> Solicite su Certificado.
        </h4>
      
        <p class="mb-2 f-16" style="line-height: 2.3em;">
            Ingrese nombres y apellidos para el certificado de culminación del curso.
        </p>
        <div class="form-row">
            <div class="col-sm-6 anim fadeInLeft mx-auto">
            <form id="formularioCertificado" autocomplete="off" >
                <span class="input-group">
                    <label for="contactName" class="">Sólo puede modificar su nombre 2 veces.</label>
                    <i class="zmdi  zmdi-account-o" style="line-height: 110px;"></i>
                    <input type="text" id="contactName" class="lg" placeholder="Nombre Completo"
                        style="font-size: 17px; height: 49px;color:black;" />
                </span><!-- .input-group -->
                <span class="input-group">
                    <button class="submit" type="submit"  data-loading-text="Enviando..."
                        style=" height: 49px;" type="submit">ENVIAR</button>
                </span><!-- .input-group -->
            </div><!-- .col-5 -->
            </form>
            <div class="col-sm-6 anim fadeInLeft mx-auto my-auto">
            <h4 class="text-center">Descarga tu certificado del Club de Lectura para Emprendedores.
            </h4>
            <div class="w-100 text-center">
            <button class="btn btn-primary descargar-comentario text-center f-25"><i class="fa fa-fw fa-file-pdf-o"
            style="position: relative;right: 0;top: 0;"></i> <span>Certificado
            CLPE</span> </button>
            </div>
            </div><!-- .col-5 -->
            </div>
    </div>
`;


            document.querySelector('#contactName').value = (certificado == undefined) ? "" : certificado.nombre;
            addEventsButtonsCertificado();
        } else {
            document.querySelector("#htmlCertificado").innerHTML = `
            <div class="container pt-4">
            <h3 class="text-center w-100 mb-2 f-weight-700" style="font-size: 2.3em;">GRACIAS POR TERMINAR CON ÉXITO LA LECTURA Y APLICACIÓN DEL LIBRO <span>PIENSE Y HÁGASE RICO</span>
            </h3>
            <h4 class="text-center">Descarga tu certificado del Club de Lectura para Emprendedores.
            </h4>
            <div class="w-100 text-center">
            <button class="btn btn-primary descargar-comentario text-center f-25"><i class="fa fa-fw fa-file-pdf-o"
            style="position: relative;right: 0;top: 0;"></i> <span>Certificado
            CLPE</span> </button>
            </div>
            </div>
       
`;
        }


        document.querySelectorAll('.descargar-comentario').forEach((btn) => {
            //AGREGANDO EVENTO CLICK
            btn.onclick = function () {
                $("#modalFrameCertificado").modal("show");
                downloadURLCertificado(getHostFrontEnd() + "api/alumno/reporte/certificado?token=" + Cookies.get("clpe_token"));
            };
        });
    } else {
        //document.querySelector('#contactName').value = (certificado == undefined) ? "" : certificado.nombre;
        document.querySelector("#htmlCertificado").innerHTML = `
        <div class="container pt-4">
        <h3 class="text-center w-100 mb-2 f-weight-700" style="font-size: 2.3em;">GRACIAS POR TERMINAR CON ÉXITO LA LECTURA Y APLICACIÓN DEL LIBRO <span>PIENSE Y HÁGASE RICO</span>
        </h3>
        <h4 class="text-center"> Solicite su Certificado.
        </h4>
      
        <p class="mb-2 f-16" style="line-height: 2.3em;">
            Ingrese nombres y apellidos para el certificado de culminación del curso.
        </p>
        <form id="formularioCertificado" autocomplete="off" class="form-row">
            <div class="col-sm-6 anim fadeInLeft mx-auto">
                <span class="input-group">
                    <label for="contactName" class="">Sólo puede modificar su nombre 2 veces.</label>
                    <i class="zmdi  zmdi-account-o" style="line-height: 110px;"></i>
                    <input type="text" id="contactName" class="lg" placeholder="Nombre Completo"
                        style="font-size: 17px; height: 49px;color:black;" />
                </span><!-- .input-group -->
                <span class="input-group">
                    <button class="submit"  type="submit"  data-loading-text="Enviando..."
                        style=" height: 49px;" type="submit">ENVIAR</button>
                </span><!-- .input-group -->
            </div><!-- .col-5 -->
            
        </form>
    </div>
`;
        addEventsButtonsCertificado();
    }



}

function processAjaxCertificado() {

    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (

        beanRequestCertificado.operation == 'add' || beanRequestCertificado.operation == 'update'
    ) {

        json = {
            nombre: (document.querySelector("#contactName").value).toUpperCase(),
            indicador: 1
        };


    } else {
        form_data = null;
    }
    switch (beanRequestCertificado.operation) {

        case 'add':
            form_data.append("class", JSON.stringify(json));
            break;
        case 'update':
            json.idcertificado = certificadoSelected.idcertificado;
            json.indicador = certificadoSelected.indicador;
            form_data.append("class", JSON.stringify(json));
            break;

        default:

            break;
    }

    $.ajax({
        url: getHostAPI() + beanRequestCertificado.entity_api + "/" + beanRequestCertificado.operation +
            parameters_pagination,
        type: beanRequestCertificado.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token") + (Cookies.get("clpe_libro") == undefined ? "" : " Clpe " + Cookies.get("clpe_libro"))
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestCertificado.operation == 'add' || beanRequestCertificado.operation == 'update') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',
    }).done(function (beanCrudResponse) {
        $('#modalCargandoCertificado').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "NOMBRE DEL CERTIFICADO REGISTRADO!", "");

            } else {
                showAlertTopEnd("info", "VERIFICACIÓN!", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {
            beanPaginationCertificado = beanCrudResponse.beanPagination;

            certificadoSelected = beanPaginationCertificado.list[0];
            addCertificado(certificadoSelected);
        }

    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoCertificado').modal("hide");
        showAlertErrorRequest();

    });

}

var validarFormularioCertificado = () => {
    let letra = letra_campo(
        document.querySelector('#contactName')

    );
    if (letra != undefined) {
        if (numero.value == '') {
            showAlertTopEnd('info', "Campo Vacío!", 'Por favor ingrese ' + letra.labels[0].innerText);
        } else {
            showAlertTopEnd(
                'info', "Formato Incorrecto",
                'Por favor ingrese sólo números, ' + letra.labels[0].innerText
            );
        }

        return false;
    }

    return true;
}

function addEventsButtonsCertificado() {
    $("#formularioCertificado").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();
        if (certificadoSelected == undefined) {
            beanRequestCertificado.operation = 'add';
            beanRequestCertificado.type_request = 'POST';
        } else {
            certificadoSelected.indicador++;
            beanRequestCertificado.operation = 'update';
            beanRequestCertificado.type_request = 'POST';
        }

        if (validarFormularioCertificado()) {
            processAjaxCertificado();
        }

    });
}

function downloadURLCertificado(url) {
    var hiddenIFrameID = 'hiddenDownloader',
        iframe = document.getElementById(hiddenIFrameID);
    if (iframe === null) {
        iframe = document.createElement('iframe');
        iframe.id = hiddenIFrameID;
        iframe.style.width = '100%';
        iframe.style.height = '100%';
        document.querySelector('#modalFrameContenidoCertificado').appendChild(iframe);
    }

    iframe.src = url;
    document.querySelector("#descargarPdfCertificado").parentElement.setAttribute("href", url);
};