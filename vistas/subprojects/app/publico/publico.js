var beanPaginationPublico;
var publicoSelected;
var capituloSelected;
var beanRequestPublico = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestPublico.entity_api = 'publico';
    beanRequestPublico.operation = 'paginate';
    beanRequestPublico.type_request = 'GET';

    $('#sizePagePublico').change(function () {
        beanRequestPublico.type_request = 'GET';
        beanRequestPublico.operation = 'paginate';
        $('#modalCargandoPublico').modal('show');
    });

    $('#modalCargandoPublico').modal('show');

    $("#modalCargandoPublico").on('shown.bs.modal', function () {
        processAjaxPublico();
    });
    $("#ventanaModalManPublico").on('hide.bs.modal', function () {
        beanRequestPublico.type_request = 'GET';
        beanRequestPublico.operation = 'paginate';
    });

    $("#formularioPublico").submit(function (event) {

        if (validarDormularioVideo()) {
            $('#modalCargandoPublico').modal('show');
        }
        event.preventDefault();
        event.stopPropagation();

    });

});

function processAjaxPublico() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestPublico.operation == 'update' ||
        beanRequestPublico.operation == 'add'
    ) {

        json = {
            estado: 1,
            codigo: publicoSelected.cuenta.cuenta.cuentaCodigo
        };


    } else {
        form_data = null;
    }

    switch (beanRequestPublico.operation) {
        case 'delete':
            parameters_pagination = '?id=' + publicoSelected.idpublico;
            break;
        case 'update':
            json.idpublico = publicoSelected.idpublico;
            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':

            form_data.append("class", JSON.stringify(json));
            break;

        default:

            parameters_pagination +=
                '?filtro=';
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pagePublico").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePagePublico").value.trim();
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestPublico.entity_api + "/" + beanRequestPublico.operation +
            parameters_pagination,
        type: beanRequestPublico.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestPublico.operation == 'update' || beanRequestPublico.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json'
    }).done(function (beanCrudResponse) {

        $('#modalCargandoPublico').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                document.querySelector("#pagePublico").value = 1;
                document.querySelector("#sizePagePublico").value = 5;
                $('#ventanaModalManPublico').modal('hide');
            } else {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationPublico = beanCrudResponse.beanPagination;
            listaPublico(beanPaginationPublico);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoPublico').modal("hide");
        showAlertErrorRequest();

    });

}

function addPublico(publico = undefined) {
    //LIMPIAR LOS CAMPOS

    document.querySelector('#txtNombrePublico').value = (publico == undefined) ? '' : publico.nombre;

    capituloSelected = (publico == undefined) ? undefined : publico.titulo;
    document.querySelector('#txtCapituloPublico').value = (publico == undefined) ? '' : publico.titulo.codigo + " - " + publico.titulo.nombre;
    document.querySelector('#tbodyPreguntas').innerHTML = "";
    let row = "";
    for (let index = 1; index <= 10; index++) {
        row += `<label for="txtPregunta${index}Publico">Pregunta N° ${index}</label>
         <div class="group-material">
        <textarea class="material-control w-100" data-toggle="tooltip" required="" data-placement="top" title="" data-original-title="Escribe la url de youtube" id="txtPregunta${index}Publico" rows="3"></textarea></div>`;
    }

    document.querySelector('#tbodyPreguntas').innerHTML += row;
    if (publico != undefined) {
        for (let index = 1; index <= 10; index++) {
            document.querySelector("#txtPregunta" + index + "Publico").value = publico['pregunta_P' + index];

        }
    }


}

function listaPublico(beanPagination) {
    document.querySelector('#tbodyPublico').innerHTML = '';
    document.querySelector('#titleManagerPublico').innerHTML =
        '[ ' + beanPagination.countFilter + ' ] MENSAJES DE PÚBLICO';
    let row = "";
    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationPublico'));
        row += `<tr>
        <td class="text-center" colspan="4">NO HAY MENSAJES DEL PÚBLICO</td>
        </tr>`;

        document.querySelector('#tbodyPublico').innerHTML += row;
        return;
    }
    beanPagination.list.forEach((publico) => {

        row += `<tr  idpublico="${publico.idpublico}">
<td class="text-center">${publico.nombre} </td>
<td class="text-center">${publico.email}</td>
<td class="text-center">${publico.fecha}</td>
<td class="text-center d-none">
<button class="btn ${publico.estado == 1 ? "btn-success" : "btn-default"} editar-publico" ><i class="zmdi ${publico.estado == 1 ? "zmdi-check-all" : "zmdi-check"}"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-publico"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;

        // $('[data-toggle="tooltip"]').tooltip();
    });


    document.querySelector('#tbodyPublico').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePagePublico").value),
        document.querySelector("#pagePublico"),
        $('#modalCargandoPublico'),
        $('#paginationPublico'));
    addEventsButtonsAdmin();


}

function addEventsButtonsAdmin() {

    document.querySelectorAll('.editar-publico').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            publicoSelected = findByPublico(
                btn.parentElement.parentElement.getAttribute('idpublico')
            );

            if (publicoSelected != undefined) {
                //  addPublico(publicoSelected);
                // $("#tituloModalManPublico").html("EDITAR RESTRICCIONES");
                // $("#ventanaModalManPublico").modal("show");
                beanRequestPublico.type_request = 'POST';
                beanRequestPublico.operation = 'update';
                $('#modalCargandoPublico').modal('show');
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
    document.querySelectorAll('.eliminar-publico').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            publicoSelected = findByPublico(
                btn.parentElement.parentElement.getAttribute('idpublico')
            );

            if (publicoSelected != undefined) {
                beanRequestPublico.type_request = 'GET';
                beanRequestPublico.operation = 'delete';
                $('#modalCargandoPublico').modal('show');
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
}

function downloadURL(url) {
    var hiddenIFrameID = 'hiddenDownloader',
        iframe = document.getElementById(hiddenIFrameID);
    if (iframe === null) {
        iframe = document.createElement('iframe');
        iframe.id = hiddenIFrameID;
        iframe.style.width = '100%';
        iframe.style.height = '100%';
        document.querySelector('#modalFrameContenidoPublico').appendChild(iframe);
    }
    iframe.src = url;
};

function findIndexPublico(idbusqueda) {
    return beanPaginationPublico.list.findIndex(
        (Publico) => {
            if (Publico.idpublico == parseInt(idbusqueda))
                return Publico;


        }
    );
}

function findByPublico(idpublico) {
    return beanPaginationPublico.list.find(
        (Publico) => {
            if (parseInt(idpublico) == Publico.idpublico) {
                return Publico;
            }


        }
    );
}

var validarDormularioVideo = () => {
    if (document.querySelector("#txtNombrePublico").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Nombre",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (capituloSelected == undefined) {
        swal({
            title: "Vacío",
            text: "Selecciona Capítulo",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    for (let index = 1; index <= 10; index++) {

        if (document.querySelector("#txtPregunta" + index + "Publico").value == "") {
            swal({
                title: "Vacío",
                text: "Ingrese datos a la Pregunta N° " + index,
                type: "warning",
                timer: 1200,
                showConfirmButton: false
            });
            return false;
        }
    }



    return true;
}