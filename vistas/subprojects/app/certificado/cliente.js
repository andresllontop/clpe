var beanPaginationCliente;
var clienteSelected;
var clienteEstado = 0;
var beanRequestCliente = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestCliente.entity_api = 'certificado';
    beanRequestCliente.operation = 'paginate';
    beanRequestCliente.type_request = 'GET';

    $('#sizePageCliente').change(function () {
        beanRequestCliente.type_request = 'GET';
        beanRequestCliente.operation = 'paginate';
        $('#modalCargandoCliente').modal('show');
    });
    document.querySelector("#tipoOpcionHeaderCurso").innerHTML = "CERTIFICADOS";
    $('#modalCargandoCurso_c').modal('show');
    // $('#modalCargandoCliente').modal('show');

    $("#modalCargandoCliente").on('shown.bs.modal', function () {
        processAjaxCliente();
    });
    $("#ventanaModalManCliente").on('hide.bs.modal', function () {
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
    document.querySelector("#btnCertificadosEntregados").onclick = () => {
        if (document.querySelector("#btnCertificadosEntregados").dataset.opcion == 0) {
            document.querySelector("#btnCertificadosEntregados").dataset.opcion = 1;
            removeClass(document.querySelector("#btnCertificadosEntregados"), "btn-primary");
            addClass(document.querySelector("#btnCertificadosEntregados"), "btn-danger");
            document.querySelector("#btnCertificadosEntregados").innerHTML = "LISTA DE CERTIFICADOS";
            clienteEstado = 1;
            $('#modalCargandoCliente').modal('show');
        } else {
            clienteEstado = 0;
            document.querySelector("#btnCertificadosEntregados").dataset.opcion = 0;
            removeClass(document.querySelector("#btnCertificadosEntregados"), "btn-danger");
            addClass(document.querySelector("#btnCertificadosEntregados"), "btn-primary");
            document.querySelector("#btnCertificadosEntregados").innerHTML = "CERTIFICADOS ENTREGADOS";
            $('#modalCargandoCliente').modal('show');
        }
    }
    document.querySelectorAll('.btn-regresar').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            document.querySelector('#cursoHTML').classList.remove("d-none");
            document.querySelector('#seccion-cliente-inactivo').classList.add("d-none");
        };
    });

});
function addEventsButtonsCurso_c() {
    document.querySelectorAll('.detalle-curso').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            curso_cSelected = findByCurso_c(
                btn.parentElement.parentElement.getAttribute('idlibro')
            );

            if (curso_cSelected != undefined) {
                addClass(
                    document.querySelector("#cursoHTML"), "d-none");
                removeClass(
                    document.querySelector("#seccion-cliente-inactivo"), "d-none");
                beanRequestCliente.type_request = 'GET';
                beanRequestCliente.operation = 'paginate';
                document.querySelector("#titleLibro").innerHTML = curso_cSelected.nombre;
                $('#modalCargandoCliente').modal('show');
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
    document.querySelectorAll('.detalle-other-curso').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            curso_cSelected = findByCurso_c(
                btn.getAttribute('idlibro')
            );

            if (curso_cSelected != undefined) {
                addClass(
                    document.querySelector("#cursoHTML"), "d-none");
                removeClass(
                    document.querySelector("#seccion-cliente-inactivo"), "d-none");
                beanRequestCliente.type_request = 'GET';
                beanRequestCliente.operation = 'paginate';
                document.querySelector("#titleLibro").innerHTML = curso_cSelected.nombre;
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
function processAjaxCliente() {


    let parameters_pagination = '';


    switch (beanRequestCliente.operation) {
        case 'delete':
            parameters_pagination = '?codigo=' + clienteSelected.cuenta.cuentaCodigo;
            break;
        default:

            parameters_pagination +=
                '?estado=' + clienteEstado + '&filtro=' + document.querySelector("#txtSearchCliente").value.trim();
            parameters_pagination +=
                '&libro=' + curso_cSelected.codigo;
            parameters_pagination +=
                '&pagina=' + parseInt(document.querySelector("#pageCliente").value.trim());
            parameters_pagination +=
                '&registros=' + parseInt(document.querySelector("#sizePageCliente").value.trim());
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestCliente.entity_api + "/" + beanRequestCliente.operation +
            parameters_pagination,
        type: beanRequestCliente.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: null,
        cache: false,
        contentType: 'application/json; charset=UTF-8',
        processData: false,
        dataType: 'json'
    }).done(function (beanCrudResponse) {

        $('#modalCargandoCliente').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                document.querySelector("#pageCliente").value = 1;
                document.querySelector("#sizePageCliente").value = 5;
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
    document.querySelector('#txtEmailUsuario').value = (cliente == undefined) ? '' : cliente.cuenta.email;




    if (cliente !== undefined) {

        $("#imagePreview").html(
            `<img  style="height:120px;width: 125px;"  alt='user-picture' class='img-responsive center-box img-circle' src='${getHostFrontEnd()}${(cliente.cuenta.foto == "" || cliente.cuenta.foto == null) ? "vistas/assets/img/userclpe.png" : "adjuntos/clientes/" + cliente.cuenta.foto}' />`
        );


    } else {
        $("#imagePreview").html(
            `<img  style="height:120px;width: 125px;" alt='user-picture' class='img-responsive center-box img-circle' src='${getHostFrontEnd()}vistas/assets/img/userclpe.png' />`
        );


    }
    addViewArchivosPrevius();

}

function listaCliente(beanPagination) {

    let row = "", contador = 1;
    document.querySelector('#tbodyCliente').innerHTML = '';
    document.querySelector('#headTablaCertificado').innerHTML = `
    <th class="text-center">#</th>
    <th class="text-center">NOMBRES</th>
    <th class="text-center">APELLIDOS</th>
    <th class="text-center">TELEFONO</th>
    <th class="text-center">EMAIL</th>
    <th class="text-center">CERTIFICADO</th>
    <th class="text-center">TERMINÓ?</th>
    <th class="text-center">VER</th>
    `;
    if (document.querySelector("#btnCertificadosEntregados").dataset.opcion == 0) {
        document.querySelector('#titleManagerCliente').innerHTML =
            'LISTA DE CERTIFICADOS';

    } else {
        document.querySelector('#titleManagerCliente').innerHTML =
            'CERTIFICADOS ENTREGADOS';
        document.querySelector('#headTablaCertificado').innerHTML += `
            <th class="text-center">ELIMINAR</th>
            `;

    }
    document.querySelector('#txtCountCliente').value =
        beanPagination.countFilter;

    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationCliente'));
        row += `<tr>
        <td class="text-center" colspan="9">NO HAY ALUMNOS</td>
        </tr>`;

        document.querySelector('#tbodyCliente').innerHTML += row;
        return;
    }
    beanPagination.list.forEach((cliente) => {

        row += `<tr  idcliente="${cliente.idcliente}" >
<td class="text-center">${contador++}</td>
<td class="text-center">${cliente.nombre}</td>
<td class="text-center">${cliente.apellido}</td>
<td class="text-center">${cliente.telefono}</td>
<td class="text-center">${cliente.cuenta.email}</td>
<td class="text-center"><button class="btn btn-warning  ${(cliente.cuenta.estado == 1) ? "descargar-certificado" : ""}" >${(cliente.cuenta.estado == 1) ? '<i class="zmdi zmdi-download"></i> DESCARGAR' : "NO DISPONIBLE"}</button></td>
<td class="text-center">
<button class="btn ${(cliente.cuenta.estado == 1) ? "btn-success" : "btn-danger"} ">${(cliente.cuenta.estado == 1) ? "SI" : "NO"} </button>
</td>
<td class="text-center">
<button class="btn btn-info ver-cliente" ><i class="zmdi zmdi-eye"></i> </button>
</td>
${document.querySelector("#btnCertificadosEntregados").dataset.opcion == 1 ? '<td class="text-center"><button class="btn btn-danger eliminar-certificado"> <i class="zmdi zmdi-delete"></i> </button></td>' : ""
            }

</tr > `;

    });


    document.querySelector('#tbodyCliente').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePageCliente").value),
        document.querySelector("#pageCliente"),
        $('#modalCargandoCliente'),
        $('#paginationCliente'));
    addEventsButtonsCliente();


}

function addEventsButtonsCliente() {
    document.querySelectorAll('.descargar-certificado').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            clienteSelected = findByCliente(
                btn.parentElement.parentElement.getAttribute('idcliente')
            );
            if (clienteSelected != undefined) {
                $("#modalFrameCertificado").modal("show");
                downloadURL(getHostFrontEnd() + "api/alumno/reporte/certificado?token=" + Cookies.get("clpe_token") + "&cuenta=" + clienteSelected.cuenta.cuentaCodigo);

            } else {
                swal(
                    "No se encontró el alumno",
                    "",
                    "info"
                );
            }

        };
    });
    document.querySelectorAll('.ver-cliente').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            clienteSelected = findByCliente(
                btn.parentElement.parentElement.getAttribute('idcliente')
            );

            if (clienteSelected != undefined) {
                $("#ventanaModalManCliente").modal("show");
                addCliente(clienteSelected);
            } else {
                swal(
                    "No se encontró el alumno",
                    "",
                    "info"
                );
            }
        };
    });
    document.querySelectorAll('.eliminar-certificado').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            clienteSelected = findByCliente(
                btn.parentElement.parentElement.getAttribute('idcliente')
            );

            if (clienteSelected != undefined) {
                beanRequestCliente.type_request = 'GET';
                beanRequestCliente.operation = 'delete';
                $('#modalCargandoCliente').modal('show');
            } else {
                swal(
                    "No se encontró el alumno",
                    "",
                    "info"
                );
            }
        };
    });
}

function addViewArchivosPreviusCliente() {

    $("#txtImagenCliente").change(function () {
        filePreview(this, "#imagePreview");
    });


}

function findIndexCliente(idbusqueda) {
    return beanPaginationCliente.list.findIndex(
        (cliente) => {
            if (cliente.idcliente == parseInt(idbusqueda))
                return cliente;


        }
    );
}

function findByCliente(idcliente) {
    return beanPaginationCliente.list.find(
        (cliente) => {
            if (parseInt(idcliente) == cliente.idcliente) {
                return cliente;
            }


        }
    );
}

var validarDormularioCliente = () => {
    if (document.querySelector("#txtCodigoCliente").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Código",
            type: "warning",
            timer: 800,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtNombreCliente").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Nombre",
            type: "warning",
            timer: 800,
            showConfirmButton: false
        });
        return false;
    }

    if (beanRequestCliente.operation == 'add') {
        if (document.querySelector("#txtImagenCliente").files.length == 0) {
            swal({
                title: "Vacío",
                text: "Ingrese Imagen",
                type: "warning",
                timer: 800,
                showConfirmButton: false
            });
            return false;
        }
        if (document.querySelector("#txtVideoCliente").files.length == 0) {
            swal({
                title: "Vacío",
                text: "Ingrese Video",
                type: "warning",
                timer: 800,
                showConfirmButton: false
            });
            return false;
        }
    }

    return true;
}

function filePreview(input, imagen) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        imagen;
        reader.onload = function (e) {
            $(imagen).html(
                "<img width='100%' alt='user-picture' class='img-responsive center-box' src='" +
                e.target.result +
                "' />"
            );
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function downloadURL(url) {
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
