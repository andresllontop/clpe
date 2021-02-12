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

});
function addCertificado(certificado = undefined) {
    if (certificado != undefined) {
        if (certificado.indicador == 1) {
            document.querySelector("#htmlMensaje").innerHTML += `
            <p class="mb-2 f-16" style="line-height: 16px;">Curso Finalizado, descarga tu certificado del Club de Lectura para Emprendedores.</p>
        <button class="btn btn-primary descargar-comentario"><i class="fa fa-fw fa-file-pdf-o"
                style="position: relative;right: 0;top: 0;"></i> <span>Certificado
                CLPE</span> </button>
`;
            document.querySelector('#contactName').value = (certificado == undefined) ? "" : certificado.nombre;
            addEventsButtonsCertificado();
        } else {
            document.querySelector("#htmlMensaje").innerHTML = `
            <p class="mb-2 f-16" style="line-height: 16px;">Curso Finalizado, descarga tu certificado del Club de Lectura para Emprendedores.</p>
        <button class="btn btn-primary descargar-comentario"><i class="fa fa-fw fa-file-pdf-o"
                style="position: relative;right: 0;top: 0;"></i> <span>Certificado
                CLPE</span> </button>
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
        document.querySelector('#contactName').value = (certificado == undefined) ? "" : certificado.nombre;
    }



}

function processAjaxCertificado(documentId = document.querySelector("#htmlMensaje")) {
    circleCargando.containerOcultar = $(documentId);
    circleCargando.container = $(documentId.parentElement);
    circleCargando.createLoader();
    circleCargando.toggleLoader("show");
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (

        beanRequestCertificado.operation == 'add' || beanRequestCertificado.operation == 'update'
    ) {

        json = {
            nombre: document.querySelector("#contactName").value,
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
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestCertificado.operation == 'add' || beanRequestCertificado.operation == 'update') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',
    }).done(function (beanCrudResponse) {
        circleCargando.toggleLoader("hide");
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
            processAjaxCertificado(document.querySelector("#htmlMensaje"));
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