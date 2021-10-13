var beanPaginationCliente, totalLecciones = 0;
var clienteSelected;

var beanRequestCliente = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    parametro = 'tarea';
    beanRequestCliente.entity_api = 'cliente';
    beanRequestCliente.operation = 'tarea';
    beanRequestCliente.type_request = 'GET';

    $('#sizePageCliente').change(function () {
        beanRequestCliente.type_request = 'GET';
        beanRequestCliente.operation = 'tarea';
        $('#modalCargandoCliente').modal('show');
    });
    if (Cookies.get("clpe_libro") != undefined) {
        curso_cSelected = JSON.parse(Cookies.get("clpe_libro"));
        document.querySelector("#titleLibro").innerHTML = curso_cSelected.nombre;
        addClass(
            document.querySelector("#cursoHTML"), "d-none");
        removeClass(
            document.querySelector("#seccion-cliente"), "d-none");

        PromiseInit();
    } else {
        addClass(
            document.querySelector("#seccion-cliente"), "d-none");
        removeClass(
            document.querySelector("#cursoHTML"), "d-none");
        processAjaxTarea();
    }

    $("#modalCargandoCliente").on('shown.bs.modal', function () {
        processAjaxCliente();
    });
    $("#ventanaModalManCliente").on('hide.bs.modal', function () {
        beanRequestCliente.type_request = 'GET';
        beanRequestCliente.operation = 'tarea';
    });
    $("#formularioClienteSearch").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();
        beanRequestCliente.type_request = 'GET';
        beanRequestCliente.operation = 'tarea';
        $('#modalCargandoCliente').modal('show');
    });
    document.querySelectorAll('.btn-regresar').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            document.querySelector('#cursoHTML').classList.remove("d-none");
            document.querySelector('#seccion-cliente').classList.add("d-none");
            Cookies.remove('clpe_libro');
            if (beanPaginationCurso_c == undefined) {
                processAjaxTarea();
            }

        };
    });


});
function PromiseInit() {

    document.querySelector('#tbodyCliente').innerHTML += `<tr>
    <td class="text-center" colspan="10">Espere cargando ...</td>
    </tr>`;
    let fetOptions = {
        headers: {
            "Content-Type": 'application/json; charset=UTF-8',
            // "Access-Control-Allow-Origin": "*",
            "Authorization": 'Bearer ' + Cookies.get("clpe_token")
        },
        method: "GET",
    }
    Promise.all([
        fetch(getHostAPI() + "cliente/tarea?filtro=&pagina=1&registros=20&libro=" + curso_cSelected.codigo, fetOptions),

        fetch(getHostAPI() + "subtitulos/total", fetOptions)
    ])
        .then(responses => Promise.all(responses.map((res) => res.json())))
        .then(json => {
            if (json[1].beanPagination !== null) {
                totalLecciones = json[1].beanPagination.countFilter;
            }
            if (json[0].beanPagination !== null) {
                beanPaginationCliente = json[0].beanPagination;
                listaCliente(beanPaginationCliente);
            }


        })
        .catch(err => {
            console.log(err);
            showAlertErrorRequest();
        });
}
function processAjaxCliente() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';

    switch (beanRequestCliente.operation) {

        default:

            parameters_pagination +=
                '?filtro=' + document.querySelector("#txtSearchCliente").value.trim();;
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pageCliente").value.trim();
            parameters_pagination +=
                '&libro=' + curso_cSelected.codigo;
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
function processAjaxTarea() {
    $.ajax({
        url: getHostAPI() + "tareas/libros",
        type: "GET",
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

        if (beanCrudResponse.beanPagination !== null) {
            beanPaginationCurso_c = beanCrudResponse.beanPagination;
            listaCurso_c(beanPaginationCurso_c);
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
        'LISTA DE ALUMNOS (TAREAS)';
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
<td class="text-center ver-lecciones pt-5">${contador++}</td>
<td class="text-center ver-lecciones pt-5">${cliente.nombre}</td>
<td class="text-center ver-lecciones pt-5">${cliente.apellido}</td>
<td class="text-center ver-lecciones pt-5">${cliente.telefono}</td>
<td class="text-center ver-lecciones pt-5 d-none">${cliente.cuenta.email}</td>
<td class="text-center ver-lecciones pt-5">${cliente.pais}</td>
<td class="text-center ver-lecciones ver-grafica">
<p style="transform: translateY(69px);margin-top:-52px; font-size: 20px;" class="f-weight-700">0%</p>
<!-- Chart -->
<canvas class="mx-auto mb-sm-0 mb-md-5 mb-xl-0" class="proposal-doughnut"
    data-fill="50" height="80" width="80"></canvas>
<!-- /chart -->
</td>
<td class="text-center ver-lecciones pt-5" style="width:6em;"> <p style="font-size: 20px;
border: 2px solid #7030a0;"> ${cliente.tarea.totalestado}</p></td>
<td class="text-center ver-lecciones pt-5" style="width:6em;"> <p style="font-size: 20px;
border: 2px solid #28a745;"> ${parseInt(cliente.tarea.totalnoestado) + parseInt(cliente.tarea.totalestado)}</p></td>

<td class="text-center ver-lecciones pt-5" style="width:8%;"><img src="${getHostFrontEnd() + ((cliente.cuenta.foto == '' || cliente.cuenta.foto == null) ? 'vistas/assets/img/userclpe.png' : 'adjuntos/clientes/' + cliente.cuenta.foto)}" alt="user-picture" class="img-responsive center-box" width="100%" ></td>

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
function addEventsButtonsCurso_c() {
    document.querySelectorAll('.detalle-curso').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            curso_cSelected = findByCurso_c(
                btn.parentElement.parentElement.getAttribute('idlibro')
            );

            if (curso_cSelected != undefined) {
                setCookieSessionLibro({ codigo: curso_cSelected.codigo, nombre: curso_cSelected.nombre });
                addClass(
                    document.querySelector("#cursoHTML"), "d-none");
                removeClass(
                    document.querySelector("#seccion-cliente"), "d-none");
                beanRequestCliente.operation = 'tarea';
                beanRequestCliente.type_request = 'GET';
                PromiseInit();
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });

}
function addEventsButtonsCliente() {
    let color = Chart.helpers.color;
    let chartColors = {
        red: '#f37070',
        pink: '#ff445d',
        orange: '#ff8f3a',
        yellow: '#ffde16',
        lightGreen: '#24cf91',
        green: '#4ecc48',
        blue: '#5797fc',
        skyBlue: '#33d4ff',
        gray: '#cfcfcf'
    };

    document.querySelectorAll('.ver-grafica').forEach((btn) => {
        clienteSelected = findByCliente(
            btn.parentElement.getAttribute('idcliente')
        );
        if (clienteSelected != undefined) {
            var proposal_data = {
                labels: [
                    "Realizó(%) ",
                    "Faltan(%) ",
                ],
                datasets: [
                    {
                        data: [parseInt(clienteSelected.tarea.totalnoestado) + parseInt(clienteSelected.tarea.totalestado), totalLecciones - (parseInt(clienteSelected.tarea.totalnoestado) + parseInt(clienteSelected.tarea.totalestado))],
                        backgroundColor: [
                            color(chartColors.green).alpha(0.8).rgbString(),
                            color(chartColors.red).alpha(0.8).rgbString(),
                        ],
                        hoverBackgroundColor: [
                            color(chartColors.green).alpha(0.8).rgbString(),
                            color(chartColors.red).alpha(0.8).rgbString(),
                        ]
                    }
                ]
            };

            new Chart(btn.lastElementChild, {
                type: 'doughnut',
                data: proposal_data,
                options: {
                    cutoutPercentage: 80,
                    responsive: false,
                    legend: {
                        display: false
                    }, tooltips: {
                        callbacks: {
                            label: function (tooltipItem) {
                                return tooltipItem.yLabel;
                            }
                        }
                    }
                }
            });
            btn.firstElementChild.innerHTML = Math.round(100 * (parseInt(clienteSelected.tarea.totalnoestado) + parseInt(clienteSelected.tarea.totalestado)) / totalLecciones) + "%";

        } else {
            swal(
                "No se encontró el alumno",
                "",
                "info"
            );
        }

    });
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
