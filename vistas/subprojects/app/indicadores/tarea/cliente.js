var beanPaginationCliente, totalLecciones = 0;
var clienteSelected;
var beanRequestCliente = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestCliente.entity_api = 'tareas';
    beanRequestCliente.operation = 'alumno';
    beanRequestCliente.type_request = 'GET';

    $('#sizePageCliente').change(function () {
        $('#modalCargandoCliente').modal('show');
    });

    // $('#modalCargandoCliente').modal('show');
    PromiseInit();
    $("#modalCargandoCliente").on('shown.bs.modal', function () {
        processAjaxCliente();
    });

    $("#formularioClienteSearch").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();
        $('#modalCargandoCliente').modal('show');
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
        fetch(getHostAPI() + beanRequestCliente.entity_api + "/" + beanRequestCliente.operation + "?filter=&pagina=1&registros=20", fetOptions),

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
                '?filter=' + document.querySelector("#txtSearchCliente").value.trim();;
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
                document.querySelector("#sizePageCliente").value = 20;
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

        row += `<tr  idtarea="${cliente.idtarea}">
<td class="text-center pt-5">${contador++}</td>
<td class="text-center pt-5">${cliente.cuenta}</td>
<td class="text-center pt-5">${cliente.apellido}</td>
<td class="text-center ver-grafica">
<p style="transform: translateY(69px);margin-top:-52px; font-size: 20px;" class="f-weight-700">0%</p>
<!-- Chart -->
<canvas class="mx-auto mb-sm-0 mb-md-5 mb-xl-0" class="proposal-doughnut"
    data-fill="50" height="80" width="80"></canvas>
<!-- /chart -->
</td>
<td class="text-center pt-5  f-weight-700">${cliente.subTitulo.titulo.nombre}</td>
<td class="text-center pt-5">${cliente.subTitulo.nombre}</td>
<td class="text-center pt-5">${cliente.fecha.split(" ")[0].split("-")[2] + "-" + cliente.fecha.split(" ")[0].split("-")[1] + "-" + cliente.fecha.split(" ")[0].split("-")[0] + "<br> " + cliente.fecha.split(" ")[1]}</td>

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
            btn.parentElement.getAttribute('idtarea')
        );
        if (clienteSelected != undefined) {
            var proposal_data = {
                labels: [
                    "Realizó(%) ",
                    "Faltan(%) ",
                ],
                datasets: [
                    {
                        data: [parseInt(clienteSelected.registro.totalnoestado) + parseInt(clienteSelected.registro.totalestado), totalLecciones - (parseInt(clienteSelected.registro.totalnoestado) + parseInt(clienteSelected.registro.totalestado))],
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
            btn.firstElementChild.innerHTML = Math.round(100 * (parseInt(clienteSelected.registro.totalnoestado) + parseInt(clienteSelected.registro.totalestado)) / totalLecciones) + "%";

        } else {
            swal(
                "No se encontró el alumno",
                "",
                "info"
            );
        }

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
            if (Cliente.idtarea == parseInt(idbusqueda))
                return Cliente;


        }
    );
}

function findByCliente(idtarea) {
    return beanPaginationCliente.list.find(
        (Cliente) => {
            if (parseInt(idtarea) == Cliente.idtarea) {
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
