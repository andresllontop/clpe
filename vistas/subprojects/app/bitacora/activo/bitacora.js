var beanPaginationBitacora;
var bitacoraSelected;
var capituloSelected;
var beanRequestBitacora = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestBitacora.entity_api = 'bitacora';
    beanRequestBitacora.operation = 'paginate';
    beanRequestBitacora.type_request = 'GET';

    $('#sizePageBitacora').change(function () {
        beanRequestBitacora.type_request = 'GET';
        beanRequestBitacora.operation = 'paginate';
        $('#modalCargandoBitacora').modal('show');
    });

    $('#modalCargandoBitacora').modal('show');

    $("#modalCargandoBitacora").on('shown.bs.modal', function () {
        processAjaxBitacora();
    });
    $("#ventanaModalManBitacora").on('hide.bs.modal', function () {
        beanRequestBitacora.type_request = 'GET';
        beanRequestBitacora.operation = 'paginate';
    });

    $("#formularioClienteSearch").submit(function (event) {
        beanRequestBitacora.type_request = 'GET';
        beanRequestBitacora.operation = 'paginate';
        $('#modalCargandoBitacora').modal('show');
        event.preventDefault();
        event.stopPropagation();
    });

});

function processAjaxBitacora() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestBitacora.operation == 'update' ||
        beanRequestBitacora.operation == 'add'
    ) {

        json = {
            estado: 1,
            codigo: bitacoraSelected.cuenta.cuenta.cuentaCodigo
        };


    } else {
        form_data = null;
    }

    switch (beanRequestBitacora.operation) {
        case 'delete':
            parameters_pagination = '?id=' + bitacoraSelected.idbitacora;
            break;
        case 'update':
            json.idbitacora = bitacoraSelected.idbitacora;
            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':

            form_data.append("class", JSON.stringify(json));
            break;

        default:

            parameters_pagination +=
                '?filter=' + document.querySelector("#txtSearchCliente").value.trim();
            parameters_pagination +=
                '&estado=';
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pageBitacora").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePageBitacora").value.trim();
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestBitacora.entity_api + "/" + beanRequestBitacora.operation +
            parameters_pagination,
        type: beanRequestBitacora.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestBitacora.operation == 'update' || beanRequestBitacora.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json'
    }).done(function (beanCrudResponse) {

        $('#modalCargandoBitacora').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                document.querySelector("#pageBitacora").value = 1;
                document.querySelector("#sizePageBitacora").value = 20;
                $('#ventanaModalManBitacora').modal('hide');
            } else {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationBitacora = beanCrudResponse.beanPagination;
            listaBitacora(beanPaginationBitacora);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoBitacora').modal("hide");
        showAlertErrorRequest();

    });

}
function addBitacora(cliente = undefined) {
    //LIMPIAR LOS CAMPOS

    document.querySelector('#txtNombreBitacora').value = (cliente == undefined) ? '' : cliente.cuenta.nombre;
    document.querySelector('#txtApellidoBitacora').value = (cliente == undefined) ? '' : cliente.cuenta.apellido;
    document.querySelector('#txtFechaBitacora').value = (cliente == undefined) ? '' : cliente.fecha_inicio.split(" ")[0] + "   Hrs: " + cliente.fecha_inicio.split(" ")[1];
    document.querySelector('#txtTelefonoBitacora').value = (cliente == undefined) ? '' : cliente.cuenta.telefono;
    document.querySelector('#txtEspecialidadBitacora').value = (cliente == undefined) ? '' : cliente.cuenta.ocupacion;
    document.querySelector('#txtEmailUsuario').value = (cliente == undefined) ? '' : cliente.cuenta.cuenta.email;


}
function listaBitacora(beanPagination) {
    document.querySelector('#tbodyBitacora').innerHTML = '';
    document.querySelector('#titleManagerBitacora').innerHTML =
        'ALUMNOS ACTIVOS';
    document.querySelector('#txtCountCliente').value =
        beanPagination.countFilter;


    let row = "", contador = 1;
    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationBitacora'));
        row += `<tr>
        <td class="text-center" colspan="9">NO HAY ALUMNOS ACTIVOS</td>
        </tr>`;

        document.querySelector('#tbodyBitacora').innerHTML += row;
        return;
    }
    beanPagination.list.forEach((bitacora) => {

        row += `<tr  idbitacora="${bitacora.idbitacora}">
<td class="text-center">${contador++} </td>
<td class="text-center">${bitacora.cuenta.nombre} </td>
<td class="text-center">${bitacora.cuenta.apellido} </td>
<td class="text-center">${bitacora.cuenta.telefono} </td>
<td class="text-center">${bitacora.cuenta.cuenta.email} </td>
<td class="text-center">${bitacora.fecha_inicio.split(" ")[0]} <p> Hrs: ${bitacora.fecha_inicio.split(" ")[1]}</p></td>
<td class="text-center d-none">${bitacora.tipo == 1 ? 'ADMINISTRADOR' : 'ALUMNO'}</td>
<td class="text-center ">
<button class="btn btn-success editar-bitacora" >ACTIVO</button>
</td>
<td class="text-center">
<button class="btn btn-info ver-bitacora" ><i class="zmdi zmdi-eye"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-bitacora"><i class="zmdi zmdi-delete"></i></button>
</td>

</tr>`;

        // $('[data-toggle="tooltip"]').tooltip();
    });


    document.querySelector('#tbodyBitacora').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePageBitacora").value),
        document.querySelector("#pageBitacora"),
        $('#modalCargandoBitacora'),
        $('#paginationBitacora'));
    addEventsButtonsAdmin();


}

function addEventsButtonsAdmin() {
    document.querySelectorAll('.ver-bitacora').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            bitacoraSelected = findByBitacora(
                btn.parentElement.parentElement.getAttribute('idbitacora')
            );

            if (bitacoraSelected != undefined) {
                addBitacora(bitacoraSelected);
                $("#tituloModalManBitacora").html("VISUALIZAR ALUMNO");
                $("#ventanaModalManBitacora").modal("show");
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
    document.querySelectorAll('.eliminar-bitacora').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            bitacoraSelected = findByBitacora(
                btn.parentElement.parentElement.getAttribute('idbitacora')
            );

            if (bitacoraSelected != undefined) {
                beanRequestBitacora.type_request = 'GET';
                beanRequestBitacora.operation = 'delete';
                $('#modalCargandoBitacora').modal('show');
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
        document.querySelector('#modalFrameContenidoBitacora').appendChild(iframe);
    }
    iframe.src = url;
};

function findIndexBitacora(idbusqueda) {
    return beanPaginationBitacora.list.findIndex(
        (Bitacora) => {
            if (Bitacora.idbitacora == parseInt(idbusqueda))
                return Bitacora;


        }
    );
}

function findByBitacora(idbitacora) {
    return beanPaginationBitacora.list.find(
        (Bitacora) => {
            if (parseInt(idbitacora) == Bitacora.idbitacora) {
                return Bitacora;
            }


        }
    );
}

