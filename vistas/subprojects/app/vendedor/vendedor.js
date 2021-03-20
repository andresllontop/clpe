var beanPaginationVendedor;
var vendedorSelected;
var beanRequestVendedor = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestVendedor.entity_api = 'vendedor';
    beanRequestVendedor.operation = 'paginate';
    beanRequestVendedor.type_request = 'GET';

    $('#sizePageVendedor').change(function () {
        beanRequestVendedor.type_request = 'GET';
        beanRequestVendedor.operation = 'paginate';
        $('#modalCargandoVendedor').modal('show');
    });

    $('#modalCargandoVendedor').modal('show');

    $("#modalCargandoVendedor").on('shown.bs.modal', function () {
        processAjaxVendedor();
    });
    $("#ventanaModalManVendedor").on('hide.bs.modal', function () {
        beanRequestVendedor.type_request = 'GET';
        beanRequestVendedor.operation = 'paginate';
    });



    $("#btnAbrirvendedor").click(function () {
        addClass(document.querySelector("#txtEmpresaVendedor").parentElement.parentElement, "d-none");
        beanRequestVendedor.operation = 'add';
        beanRequestVendedor.type_request = 'POST';
        $("#imagePreview").html("");
        $("#tituloModalManVendedor").html("REGISTRAR VENDEDOR");
        addVendedor();
        $("#ventanaModalManVendedor").modal("show");


    });
    $("#formularioVendedor").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (validateFormVendedor()) {
            $('#modalCargandoVendedor').modal('show');
        }
    });

});

function processAjaxVendedor() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestVendedor.operation == 'update' ||
        beanRequestVendedor.operation == 'add'
    ) {

        json = {
            nombre: document.querySelector("#txtNombreVendedor").value,
            apellido: document.querySelector("#txtApellidoVendedor").value,
            telefono: document.querySelector("#txtTelefonoVendedor").value,
            codigo: document.querySelector("#txtCodigoVendedor").value,
            empresa: document.querySelector("#txtEmpresaVendedor").value == "" ? null : document.querySelector("#txtEmpresaVendedor").value,
            tipo: document.querySelector("#txtTipoVendedor").value,
            pais: document.querySelector("#txtPaisVendedor").value

        };


    } else {
        form_data = null;
    }

    switch (beanRequestVendedor.operation) {
        case 'delete':
            parameters_pagination = '?id=' + vendedorSelected.idvendedor;
            break;

        case 'update':
            json.idvendedor = vendedorSelected.idvendedor;

            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':

            form_data.append("class", JSON.stringify(json));
            break;

        default:

            parameters_pagination +=
                '?filtro=';
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pageVendedor").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePageVendedor").value.trim();
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestVendedor.entity_api + "/" + beanRequestVendedor.operation +
            parameters_pagination,
        type: beanRequestVendedor.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestVendedor.operation == 'update' || beanRequestVendedor.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json'
    }).done(function (beanCrudResponse) {

        $('#modalCargandoVendedor').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                document.querySelector("#pageVendedor").value = 1;
                document.querySelector("#sizePageVendedor").value = 20;
                $('#ventanaModalManVendedor').modal('hide');
            } else {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationVendedor = beanCrudResponse.beanPagination;
            listaVendedor(beanPaginationVendedor);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoVendedor').modal("hide");
        showAlertErrorRequest();

    });

}

function addVendedor(vendedor = undefined) {
    //LIMPIAR LOS CAMPOS

    document.querySelector('#txtNombreVendedor').value = (vendedor == undefined) ? '' : vendedor.nombre;

    document.querySelector('#txtApellidoVendedor').value = (vendedor == undefined) ? '' : vendedor.apellido;
    document.querySelector('#txtCodigoVendedor').value = (vendedor == undefined) ? '' : vendedor.codigo;
    document.querySelector('#txtEmpresaVendedor').value = (vendedor == undefined) ? '' : vendedor.empresa;
    document.querySelector('#txtTipoVendedor').value = (vendedor == undefined) ? '1' : vendedor.tipo;
    document.querySelector('#txtPaisVendedor').value = (vendedor == undefined) ? '' : vendedor.pais;
    document.querySelector('#txtTelefonoVendedor').value = (vendedor == undefined) ? '' : vendedor.telefono;
    if (vendedor != undefined) {
        if (parseInt(vendedor.tipo) == parseInt(1)) {

            addClass(document.querySelector("#txtEmpresaVendedor").parentElement.parentElement, "d-none");
        } else {
            removeClass(document.querySelector("#txtEmpresaVendedor").parentElement.parentElement, "d-none");
        }
    }


    addViewArchivosPrevius();

}

function listaVendedor(beanPagination) {
    document.querySelector('#tbodyVendedor').innerHTML = '';
    document.querySelector('#titleManagerVendedor').innerHTML =
        '[ ' + beanPagination.countFilter + ' ] Vendedores';
    let row = "";

    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationVendedor'));
        return;
    }
    document.querySelector('#tbodyVendedor').innerHTML += row;
    let html2;
    beanPagination.list.forEach((vendedor) => {

        row += `<tr  idvendedor="${vendedor.idvendedor}">
<td class="text-center">${vendedor.codigo}</td>
<td class="text-center">${vendedor.apellido + " " + vendedor.nombre}</td>
<td class="text-center">${vendedor.telefono}</td>
<td class="text-center">${vendedor.pais}</td>
<td class="text-center">${vendedor.empresa}</td>
<td class="text-center">
<button class="btn btn-info editar-vendedor" ><i class="zmdi zmdi-refresh"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-vendedor"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;

        // $('[data-toggle="tooltip"]').tooltip();
    });


    document.querySelector('#tbodyVendedor').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePageVendedor").value),
        document.querySelector("#pageVendedor"),
        $('#modalCargandoVendedor'),
        $('#paginationVendedor'));
    addEventsButtonsAdmin();


}

function addEventsButtonsAdmin() {
    document.querySelectorAll('.editar-vendedor').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            vendedorSelected = findByVendedor(
                btn.parentElement.parentElement.getAttribute('idvendedor')
            );

            if (vendedorSelected != undefined) {
                addVendedor(vendedorSelected);
                $("#tituloModalManVendedor").html("EDITAR VENDEDOR");
                $("#ventanaModalManVendedor").modal("show");
                beanRequestVendedor.type_request = 'POST';
                beanRequestVendedor.operation = 'update';
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
    document.querySelectorAll('.eliminar-vendedor').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            vendedorSelected = findByVendedor(
                btn.parentElement.parentElement.getAttribute('idvendedor')
            );

            if (vendedorSelected != undefined) {
                beanRequestVendedor.type_request = 'GET';
                beanRequestVendedor.operation = 'delete';
                $('#modalCargandoVendedor').modal('show');
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
}

var validateFormVendedor = () => {
    if (document.querySelector("#txtNombreVendedor").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Nombres",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtApellidoVendedor").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Apellidos",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtCodigoVendedor").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese codigo de vendedor",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (parseInt(document.querySelector("#txtCodigoVendedor").value) == 2 && document.querySelector("#txtEmpresaVendedor").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese empresa",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }

    if (document.querySelector("#txtTipoVendedor").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese tipo de Vendedor",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtPaisVendedor").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese País",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtTelefonoVendedor").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Teléfono",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }

    return true;
}

function addViewArchivosPrevius() {
    $('#txtTipoVendedor').change(function () {
        if (parseInt(document.querySelector("#txtTipoVendedor").value) == parseInt(1)) {

            addClass(document.querySelector("#txtEmpresaVendedor").parentElement.parentElement, "d-none");
        } else {
            removeClass(document.querySelector("#txtEmpresaVendedor").parentElement.parentElement, "d-none");
        }
    });


}

function findIndexVendedor(idbusqueda) {
    return beanPaginationVendedor.list.findIndex(
        (Vendedor) => {
            if (Vendedor.idvendedor == parseInt(idbusqueda))
                return Vendedor;


        }
    );
}

function findByVendedor(idvendedor) {
    return beanPaginationVendedor.list.find(
        (Vendedor) => {
            if (parseInt(idvendedor) == Vendedor.idvendedor) {
                return Vendedor;
            }


        }
    );
}

