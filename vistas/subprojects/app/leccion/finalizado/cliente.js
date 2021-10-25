var beanPaginationCliente;
var clienteSelected;
var beanRequestCliente = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestCliente.entity_api = 'cliente';
    beanRequestCliente.operation = 'terminado';
    beanRequestCliente.type_request = 'GET';

    $('#sizePageCliente').change(function () {
        beanRequestCliente.type_request = 'GET';
        beanRequestCliente.operation = 'terminado';
        $('#modalCargandoCliente').modal('show');
    });
    document.querySelector("#tipoOpcionHeaderCurso").innerHTML = "FINALIZADOS";
    $('#modalCargandoCurso_c').modal('show');
    $("#modalCargandoCliente").on('shown.bs.modal', function () {
        processAjaxCliente();
    });
    $("#ventanaModalManCliente").on('hide.bs.modal', function () {
        beanRequestCliente.type_request = 'GET';
        beanRequestCliente.operation = 'terminado';
    });
    $("#formularioClienteSearch").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();

        beanRequestCliente.type_request = 'GET';
        beanRequestCliente.operation = 'terminado';
        $('#modalCargandoCliente').modal('show');



    });

    document.querySelectorAll('.btn-regresar').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            document.querySelector('#cursoHTML').classList.remove("d-none");
            document.querySelector('#seccion-cliente').classList.add("d-none");
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
                    document.querySelector("#seccion-cliente"), "d-none");
                beanRequestCliente.type_request = 'GET';
                beanRequestCliente.operation = 'terminado';
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
                    document.querySelector("#seccion-cliente"), "d-none");
                beanRequestCliente.type_request = 'GET';
                beanRequestCliente.operation = 'terminado';
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
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';

    switch (beanRequestCliente.operation) {

        default:

            parameters_pagination +=
                '?filtro=' + document.querySelector("#txtSearchCliente").value.trim();
            parameters_pagination +=
                '&libro=' + curso_cSelected.codigo;
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
        contentType: ((beanRequestCliente.operation == 'update' || beanRequestCliente.operation == 'add') ? false : 'application/json; charset=UTF-8'),
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

function listaCliente(beanPagination) {

    let row = "", contador = 1;
    document.querySelector('#tbodyCliente').innerHTML = '';
    document.querySelector('#titleManagerCliente').innerHTML =
        'LISTA DE ALUMNOS (TAREAS FINALIZADAS)';
    document.querySelector('#txtCountCliente').value =
        beanPagination.countFilter;

    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationCliente'));
        row += `<tr>
        <td class="text-center" colspan="9">NO HAY TAREAS</td>
        </tr>`;

        document.querySelector('#tbodyCliente').innerHTML += row;
        return;
    }
    beanPagination.list.forEach((cliente) => {

        row += `<tr  idcliente="${cliente.idcliente}" class="aula-cursor-mano">
<td class="text-center ver-lecciones">${contador++}</td>
<td class="text-center ver-lecciones">${cliente.nombre}</td>
<td class="text-center ver-lecciones">${cliente.apellido}</td>
<td class="text-center ver-lecciones">${cliente.telefono}</td>
<td class="text-center ver-lecciones">${cliente.cuenta.email}</td>
<td class="text-center ver-lecciones">${cliente.pais}</td>
<td class="text-center ver-lecciones" style="width:6em;"> <p style="font-size: 20px;
border: 2px solid #7030a0;"> ${cliente.tarea.totalestado}</p></td>
<td class="text-center ver-lecciones" style="width:6em;"> <p style="font-size: 20px;
border: 2px solid #28a745;"> ${parseInt(cliente.tarea.totalnoestado) + parseInt(cliente.tarea.totalestado)}</p></td>
<td class="text-center ver-lecciones" style="width:8%;"><img src="${getHostFrontEnd() + ((cliente.cuenta.foto == '' || cliente.cuenta.foto == null) ? 'vistas/assets/img/userclpe.png' : 'adjuntos/clientes/' + cliente.cuenta.foto)}" alt="user-picture" class="img-responsive center-box" width="100%" ></td>

</tr>`;

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

    document.querySelectorAll('.ver-lecciones').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            clienteSelected = findByCliente(
                btn.parentElement.getAttribute('idcliente')
            );

            if (clienteSelected != undefined) {

                document.querySelector("#seccion-cliente").classList.add("d-none");
                document.querySelector("#seccion-leccion").classList.remove("d-none");
                document.querySelector("#seccion-cuestionario").classList.add("d-none");
                beanRequestLeccion.type_request = 'GET';
                beanRequestLeccion.operation = 'paginate';
                $('#modalCargandoLeccion').modal('show');
            } else {
                swal(
                    "No se encontró el alumno",
                    "",
                    "info"
                );
            }
        };
    });
    document.querySelectorAll('.ver-cuestionarios-sub').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            clienteSelected = findByCliente(
                btn.parentElement.parentElement.getAttribute('idcliente')
            );

            if (clienteSelected != undefined) {
                document.querySelector("#seccion-cliente").classList.add("d-none");
                document.querySelector("#seccion-leccion").classList.add("d-none");
                document.querySelector("#seccion-respuesta").classList.remove("d-none");
                beanRequestRespuesta.operation = 'obtener';
                beanRequestRespuesta.type_request = 'GET';
                respuestaSelected = { tipo: 2 };
                document.querySelector('#tablaNombreRespuesta').innerHTML = 'SUBTITULO';
                $('#modalCargandoRespuesta').modal('show');
            } else {
                swal(
                    "No se encontró el alumno",
                    "",
                    "info"
                );
            }
        };
    });
    document.querySelectorAll('.ver-cuestionarios-cap').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            clienteSelected = findByCliente(
                btn.parentElement.parentElement.getAttribute('idcliente')
            );

            if (clienteSelected != undefined) {
                document.querySelector("#seccion-cliente").classList.add("d-none");
                document.querySelector("#seccion-leccion").classList.add("d-none");
                document.querySelector("#seccion-respuesta").classList.remove("d-none");
                beanRequestRespuesta.operation = 'obtener';
                beanRequestRespuesta.type_request = 'GET';
                respuestaSelected = { tipo: 1 };
                document.querySelector('#tablaNombreRespuesta').innerHTML = 'CAPÍTULO';
                $('#modalCargandoRespuesta').modal('show');
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

    $("#txtVideoCliente").change(function () {
        videoPreview(this, "#videoPreview");
    });
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
function videoPreview(input, imagen) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        imagen;
        reader.onload = function (e) {
            $(imagen).html(
                "<video width='100%' alt='user-picture' class='img-responsive center-box' controls ><source src='" +
                e.target.result +
                "' type='video/mp4'></video>"
            );
        };
        reader.readAsDataURL(input.files[0]);
    }
}
