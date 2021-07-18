var beanPaginationCliente;
var clienteSelected;
var beanRequestCliente = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestCliente.entity_api = 'cliente';
    beanRequestCliente.operation = 'paginate';
    beanRequestCliente.type_request = 'GET';

    $('#sizePageCliente').change(function () {
        beanRequestCliente.type_request = 'GET';
        beanRequestCliente.operation = 'paginate';
        $('#modalCargandoCliente').modal('show');
    });

    $('#modalCargandoCliente').modal('show');

    $("#modalCargandoCliente").on('shown.bs.modal', function () {
        processAjaxCliente();
    });
    $("#modalCargandoCliente").on('hide.bs.modal', function () {
        beanRequestCliente.type_request = 'GET';
        beanRequestCliente.operation = 'paginate';
    });
    $("#formularioClienteSearch").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();

        beanRequestCliente.type_request = 'GET';
        beanRequestCliente.operation = 'paginate';
        $('#modalCargandoCliente').modal('show');



    });

});

function processAjaxCliente() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestCliente.operation == 'update' ||
        beanRequestCliente.operation == 'add'
    ) {

        json = {
            titulo: document.querySelector("#txttituloCliente").value,
            enlace: document.querySelector("#txtYoutubeCliente").value,
            descripcion: $("#txtDescripcionCliente").Editor("getText"),
            estado: 1
        };


    } else {
        form_data = null;
    }

    switch (beanRequestCliente.operation) {
        case 'delete':
            parameters_pagination = '?id=' + clienteSelected.idcliente;
            break;

        case 'update':
            json.idcliente = clienteSelected.idcliente;

            let dataImagen = $("#txtImagenCliente").prop("files")[0];
            form_data.append("txtImagenCliente", dataImagen);
            form_data.append("class", JSON.stringify(json));
            break;
        case 'updateestado':
            form_data = new FormData();
            json = {
                idcuenta: clienteSelected.cuenta.idcuenta,
                codigo: clienteSelected.cuenta.cuentaCodigo,
                estado: 0
            };
            form_data.append("class", JSON.stringify(json));
            break;
        case 'updateestadocliente':
            form_data = new FormData();
            json = {
                idcliente: clienteSelected.idcliente,
                estado: clienteSelected.estado
            };
            form_data.append("class", JSON.stringify(json));
            break;

        case 'add':

            let dataFot = $("#txtImagenCliente").prop("files")[0];
            form_data.append("txtImagenCliente", dataFot);

            form_data.append("class", JSON.stringify(json));
            break;

        default:

            parameters_pagination +=
                '?filtro=' + document.querySelector("#txtSearchCliente").value.trim();
            parameters_pagination +=
                '&estado=1';
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pageCliente").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePageCliente").value.trim();
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestCliente.entity_api + "/" + beanRequestCliente.operation +
            parameters_pagination,
        type: beanRequestCliente.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },

        data: form_data,
        cache: false,
        contentType: ((beanRequestCliente.operation == 'update' || beanRequestCliente.operation == 'updateestadocliente' || beanRequestCliente.operation == 'updateestado' || beanRequestCliente.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',
    }).done(function (beanCrudResponse) {

        $('#modalCargandoCliente').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");

                $('#ventanaModalManCliente').modal('hide');
            } else {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationCliente = beanCrudResponse.beanPagination;
            listaCliente(beanPaginationCliente);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoCliente').modal("hide");
        showAlertErrorRequest();

    });

}

function addCliente(cliente = undefined) {
    //LIMPIAR LOS CAMPOS

    document.querySelector('#txtNombreCliente').value = (cliente == undefined) ? '' : cliente.nombre;
    document.querySelector('#txtApellidoCliente').value = (cliente == undefined) ? '' : cliente.apellido;

    document.querySelector('#txtTelefonoCliente').value = (cliente == undefined) ? '' : cliente.telefono;
    document.querySelector('#txtEspecialidadCliente').value = (cliente == undefined) ? '' : cliente.ocupacion;
    document.querySelector('#txtMontoCliente').value = (cliente == undefined) ? '' : cliente.cuenta.precio;
    document.querySelector('#txtUsuarioName').value = (cliente == undefined) ? '' : cliente.cuenta.usuario;
    document.querySelector('#txtEmailUsuario').value = (cliente == undefined) ? '' : cliente.cuenta.email;
    document.querySelector('#txtPasswordUsuario').value = (cliente == undefined) ? '' : cliente.cuenta.clave;



    if (cliente !== undefined) {

        $("#imagePreview").html(
            `<img  style="height:120px;width: 125px;"  alt='user-picture' class='img-responsive center-box img-circle' src='${getHostFrontEnd()}${(cliente.cuenta.foto == "" || cliente.cuenta.foto == null) ? "vistas/assets/img/userclpe.png" : "adjuntos/clientes/" + cliente.cuenta.foto}' />`
        );
        if (cliente.cuenta.voucher == "CULQI") {
            document.querySelector('#txtMontoCliente').parentElement.parentElement.style = "";
            $("#imagenVaucherPreview").html(
                `METODO DE PAGO CULQI`
            );
        } else {
            $("#imagenVaucherPreview").html(
                `<img  style="height:180px;width: 100%;"  alt='user-picture' class='img-responsive center-box ' src='${getHostFrontEnd() + "adjuntos/clientes/comprobante/" + cliente.cuenta.voucher}' />`
            );
        }


    } else {
        $("#imagePreview").html(
            `<img  style="height:120px;width: 125px;" alt='user-picture' class='img-responsive center-box img-circle' src='${getHostFrontEnd()}vistas/assets/img/userclpe.png' />`
        );
        $("#imagenVaucherPreview").html(
            ``
        );

    }
    addViewArchivosPrevius();

}

function listaCliente(beanPagination) {
    document.querySelector('#tbodyCliente').innerHTML = '';
    document.querySelector('#titleCliente').innerHTML =
        'ALUMNOS INSCRITOS N01';
    document.querySelector('#txtCountCliente').value =
        beanPagination.countFilter

    let row = "";
    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationCliente'));
        row += `<tr>
        <td class="text-center" colspan="11">NO HAY ALUMNOS INSCRITOS</td>
        </tr>`;

        document.querySelector('#tbodyCliente').innerHTML += row;
        return;
    }
    beanPagination.list.forEach((cliente) => {

        row += `<tr  idcliente="${cliente.idcliente}">
        
<td class="text-center" >${cliente.fecha == null ? '' : cliente.fecha.split(" ")[0].split("-")[2] + '-' + cliente.fecha.split(" ")[0].split("-")[1] + '-' + cliente.fecha.split(" ")[0].split("-")[0]}</br>${cliente.fecha == null ? '' : cliente.fecha.split(" ")[1]}</td>
<td class="text-center px-1" >${cliente.nombre}</td>
<td class="text-center px-1" >${cliente.apellido}</td>
<td class="text-center px-1" >${cliente.telefono}</td>
<td class="text-center px-1" >${cliente.cuenta.email}</td>
<td class="text-center px-1">
<button class="btn ${cliente.estado == 1 ? "btn-warning" : "btn-danger"} editar-estado-visto-cliente" ><i class="zmdi ${cliente.estado == 1 ? "zmdi-check-all" : "zmdi-minus"}"></i> </button>
</td>
<td class="text-center d-none" >${cliente.cuenta.usuario}</td>
<td class="text-center px-1"><img src="${getHostFrontEnd()}${(cliente.cuenta.foto == "" || cliente.cuenta.foto == null) ? "vistas/assets/img/userclpe.png" : "adjuntos/clientes/" + cliente.cuenta.foto}" alt="user-picture" class="img-responsive" style="width:50px;height:50px;border-radius:50%;"></td>
<td class="text-center d-none" >${cliente.cuenta.precio}</td>
<td class="text-center px-1 d-none">${cliente.cuenta.voucher == "CULQI" ? "METODO DE PAGO CULQI" : "<img src='" + getHostFrontEnd() + "adjuntos/clientes/comprobante/" + cliente.cuenta.voucher + "' alt='user-picture' class='img-responsive center-box' style='width:50px;height:60px;'"}</td>
<td class="text-center px-1">
<button class="btn ${cliente.cuenta.estado == 1 ? "btn-success" : "btn-warning"} editar-estado-cliente" ><i class="zmdi ${cliente.cuenta.estado == 1 ? "zmdi-check-all" : "zmdi-minus"}"></i> </button>
</td>
<td class="text-center px-1">
<button class="btn btn-info editar-cliente" ><i class="zmdi zmdi-eye"></i> </button>
</td>
<td class="text-center px-1">
<button class="btn btn-danger eliminar-cliente"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;

        // $('[data-toggle="tooltip"]').tooltip();
    });


    document.querySelector('#tbodyCliente').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePageCliente").value),
        document.querySelector("#pageCliente"),
        $('#modalCargandoCliente'),
        $('#paginationCliente'));
    addEventsButtonsAdmin();


}

function addEventsButtonsAdmin() {

    document.querySelectorAll('.editar-estado-cliente').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            clienteSelected = findByCliente(
                btn.parentElement.parentElement.getAttribute('idcliente')
            );

            if (clienteSelected != undefined && clienteSelected.cuenta.estado == 1) {
                clienteSelected.cuenta.estado = 1;
                beanRequestCliente.type_request = 'POST';
                beanRequestCliente.operation = 'updateestado';
                $('#modalCargandoCliente').modal('show');
            } else {
                showAlertTopEnd("info", "Vacío!", "No se encontró el alumno para poder editar");
            }
        };
    });
    document.querySelectorAll('.editar-estado-visto-cliente').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            clienteSelected = findByCliente(
                btn.parentElement.parentElement.getAttribute('idcliente')
            );

            if (clienteSelected != undefined) {
                if ((btn.firstElementChild.classList.value).includes("minus")) {
                    clienteSelected.estado = 1;
                } else {
                    clienteSelected.estado = 0;
                }

                beanRequestCliente.type_request = 'POST';
                beanRequestCliente.operation = 'updateestadocliente';
                $('#modalCargandoCliente').modal('show');
            } else {
                showAlertTopEnd("info", "Vacío!", "No se encontró el alumno para poder editar");
            }
        };
    });
    document.querySelectorAll('.editar-cliente').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            clienteSelected = findByCliente(
                btn.parentElement.parentElement.getAttribute('idcliente')
            );

            if (clienteSelected != undefined) {
                addCliente(clienteSelected);
                $("#tituloModalManCliente").html("DATOS DEL ALUMNO");
                $("#ventanaModalManCliente").modal("show");
                // beanRequestCliente.type_request = 'POST';
                // beanRequestCliente.operation = 'update';
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
    document.querySelectorAll('.eliminar-cliente').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            clienteSelected = findByCliente(
                btn.parentElement.parentElement.getAttribute('idcliente')
            );

            if (clienteSelected != undefined) {
                beanRequestCliente.type_request = 'GET';
                beanRequestCliente.operation = 'delete';
                $('#modalCargandoCliente').modal('show');
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
}

function addViewArchivosPrevius() {

    $("#txtImagenCliente").change(function () {
        filePreview(this, "#imagePreview");
    });
    $("#txtImagenPortadaCliente").change(function () {
        filePreview(this, "#imagePreview2");
    });
}

function filePreview(input, imagen) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        imagen;
        reader.onload = function (e) {
            $(imagen).html(
                "<img width='244' alt='user-picture' class='img-responsive center-box' src='" +
                e.target.result +
                "' />"
            );
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function clientePreview(input, imagen) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        imagen;
        reader.onload = function (e) {
            $(imagen).html(
                "<cliente width='244' alt='user-picture' class='img-responsive center-box' controls ><source src='" +
                e.target.result +
                "' type='cliente/mp4'></cliente>"
            );
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function findIndexCliente(idbusqueda) {
    return beanPaginationCliente.list.findIndex(
        (Cliente) => {
            if (Cliente.idcliente == parseInt(idbusqueda))
                return Cliente;


        }
    );
}

function findByCliente(idcliente) {
    return beanPaginationCliente.list.find(
        (Cliente) => {
            if (parseInt(idcliente) == Cliente.idcliente) {
                return Cliente;
            }


        }
    );
}

