var beanPaginationVisita;
var visitaSelected;
var capituloSelected;
var beanRequestVisita = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {


    var $paises = new Array("Afganistan", "Albania", "Germany", "Andorra", "Angola", "Antigua y Barbuda", "Arabia Saudita", "Argelia", "Argentina", "Armenia", "Australia", "Austria", "Azerbaiyán", "Bahamas", "Bangladés", "Barbados", "Baréin", "Bélgica", "Belice", "Benín", "Bielorrusia", "Birmania", "Bolivia", "Bosnia y Herzegovina", "Botsuana", "Brasil", "Brunéi", "Bulgaria", "Burkina Faso", "Burundi", "Bután", "Cabo Verde", "Camboya", "Camerún", "Canadá", "Catar", "Chad", "Chile", "China", "Chipre", "Ciudad del Vaticano", "Colombia", "Comoras", "Corea del Norte", "Corea del Sur", "Costa de Marfil", "Costa Rica", "Croacia", "Cuba", "Dinamarca", "Dominica", "Ecuador", "Egipto", "El Salvador", "Emiratos Árabes Unidos", "Eritrea", "Eslovaquia", "Eslovenia", "España", "United States", "Estonia", "Etiopía", "Filipinas", "Finlandia", "Fiyi", "Francia", "Gabón", "Gambia", "Georgia", "Ghana", "Granada", "Grecia", "Guatemala", "Guyana", "Guinea", "Guinea ecuatorial", "Guinea-Bisáu", "Haití", "Honduras", "Hungría", "India", "Indonesia", "Irak", "Irán", "Irlanda", "Islandia", "Islas Marshall", "Islas Salomón", "Israel", "Italia", "Jamaica", "Japón", "Jordania", "Kazajistán", "Kenia", "Kirguistán", "Kiribati", "Kuwait", "Laos", "Lesoto", "Letonia", "Líbano", "Liberia", "Libia", "Liechtenstein", "Lituania", "Luxemburgo", "Madagascar", "Malasia", "Malaui", "Maldivas", "Malí", "Malta", "Marruecos", "Mauricio", "Mauritania", "México", "Micronesia", "Moldavia", "Mónaco", "Mongolia", "Montenegro", "Mozambique", "Namibia", "Nauru", "Nepal", "Nicaragua", "Níger", "Nigeria", "Noruega", "Nueva Zelanda", "Omán", "Países Bajos", "Pakistán", "Palaos", "Panamá", "Papúa Nueva Guinea", "Paraguay", "Peru", "Polonia", "Portugal", "Reino Unido", "República Centroafricana", "República Checa", "República de Macedonia", "República del Congo", "República Democrática del Congo", "República Dominicana", "República Sudafricana", "Ruanda", "Rumanía", "Rusia", "Samoa", "San Cristóbal y Nieves", "San Marino", "San Vicente y las Granadinas", "Santa Lucía", "Santo Tomé y Príncipe", "Senegal", "Serbia", "Seychelles", "Sierra Leona", "Singapur", "Siria", "Somalia", "Sri Lanka", "Suazilandia", "Sudán", "Sudán del Sur", "Suecia", "Suiza", "Surinam", "Tailandia", "Tanzania", "Tayikistán", "Timor Oriental", "Togo", "Tonga", "Trinidad y Tobago", "Túnez", "Turkmenistán", "Turquía", "Tuvalu", "Ucrania", "Uganda", "Uruguay", "Uzbekistán", "Vanuatu", "Venezuela", "Vietnam", "Yemen", "Yibuti", "Zambia", "Zimbabue");
    let pais = "<option value=''>TODOS</option>";
    $paises.forEach(e => {
        pais += `<option value="${e}">${e}</option>`;

    });
    document.querySelector("#filterPais").innerHTML = pais;


    beanRequestVisita.entity_api = 'visitas';
    beanRequestVisita.operation = 'paginate';
    beanRequestVisita.type_request = 'GET';

    $('#sizePageVisita').change(function () {
        beanRequestVisita.type_request = 'GET';
        beanRequestVisita.operation = 'paginate';
        $('#modalCargandoVisita').modal('show');
    });
    $('#filterPais').change(function () {
        beanRequestVisita.type_request = 'GET';
        beanRequestVisita.operation = 'paginate';
        $('#modalCargandoVisita').modal('show');
    });
    $('#filterPagina').change(function () {
        beanRequestVisita.type_request = 'GET';
        beanRequestVisita.operation = 'paginate';
        $('#modalCargandoVisita').modal('show');
    });
    $('#filterFInicial').change(function () {
        beanRequestVisita.type_request = 'GET';
        beanRequestVisita.operation = 'paginate';
        $('#modalCargandoVisita').modal('show');
    });
    let fechaHoy = getDateJava().split("/")[2] + "-" + getDateJava().split("/")[1] + "-" + getDateJava().split("/")[0];
    document.querySelector("#filterFFinal").value = fechaHoy;
    document.querySelector("#filterFFinal").setAttribute("max", fechaHoy);
    $('#filterFFinal').change(function () {
        beanRequestVisita.type_request = 'GET';
        beanRequestVisita.operation = 'paginate';
        $('#modalCargandoVisita').modal('show');
    });

    $('#modalCargandoVisita').modal('show');

    $("#modalCargandoVisita").on('shown.bs.modal', function () {
        processAjaxVisita();
    });
    $("#ventanaModalManVisita").on('hide.bs.modal', function () {
        beanRequestVisita.type_request = 'GET';
        beanRequestVisita.operation = 'paginate';
    });

    $("#formularioVisita").submit(function (event) {

        if (validarDormularioVideo()) {
            $('#modalCargandoVisita').modal('show');
        }
        event.preventDefault();
        event.stopPropagation();

    });


    $("#formularioVisitaSearch").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();
        beanRequestVisita.type_request = 'GET';
        beanRequestVisita.operation = 'paginate';
        $('#modalCargandoVisita').modal('show');
    });

});

function processAjaxVisita() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestVisita.operation == 'update' ||
        beanRequestVisita.operation == 'add'
    ) {

        json = {
            estado: 1,
            codigo: visitaSelected.cuenta.cuenta.cuentaCodigo
        };


    } else {
        form_data = null;
    }

    switch (beanRequestVisita.operation) {
        case 'delete':
            parameters_pagination = '?id=' + visitaSelected.idvisita;
            break;
        case 'update':
            json.idvisita = visitaSelected.idvisita;
            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':

            form_data.append("class", JSON.stringify(json));
            break;

        default:


            parameters_pagination +=
                '?filter=' + (document.querySelector("#filterPais").value).toLocaleUpperCase();
            parameters_pagination +=
                '&f_pagina=' + (document.querySelector("#filterPagina").value.trim()).toLocaleLowerCase();
            parameters_pagination +=
                '&f_inicial=' + document.querySelector("#filterFInicial").value.trim();
            parameters_pagination +=
                '&f_final=' + document.querySelector("#filterFFinal").value.trim();
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pageVisita").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePageVisita").value.trim();
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestVisita.entity_api + "/" + beanRequestVisita.operation +
            parameters_pagination,
        type: beanRequestVisita.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestVisita.operation == 'update' || beanRequestVisita.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json'
    }).done(function (beanCrudResponse) {

        $('#modalCargandoVisita').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                document.querySelector("#pageVisita").value = 1;
                document.querySelector("#sizePageVisita").value = 5;
                $('#ventanaModalManVisita').modal('hide');
            } else {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationVisita = beanCrudResponse.beanPagination;
            listaVisita(beanPaginationVisita);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoVisita').modal("hide");
        showAlertErrorRequest();

    });

}

function listaVisita(beanPagination) {
    document.querySelector('#tbodyVisita').innerHTML = '';
    document.querySelector('#titleManagerVisita').innerHTML =
        '[ ' + beanPagination.countFilter + ' ] VISITAS';
    let row = "";
    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationVisita'));
        row += `<tr>
        <td class="text-center" colspan="7">NO HAY VISITAS</td>
        </tr>`;

        document.querySelector('#tbodyVisita').innerHTML += row;
        return;
    }
    beanPagination.list.forEach((visita) => {

        row += `<tr  idvisita="${visita.idvisita}">
<td class="text-center">${visita.ip} </td>
<td class="text-center">${visita.info == null ? "" : visita.info} </td>
<td class="text-center">${visita.pagina} </td>
<td class="text-center">${(visita.fecha).split(" ")[0].split("-")[2] + "/" + (visita.fecha).split(" ")[0].split("-")[1] + "/" + (visita.fecha).split(" ")[0].split("-")[0] + "<br>" + (visita.fecha).split(" ")[1]
            }</td >
<td class="text-center">${visita.contador + " Veces"}</td>
<td class="text-center">${visita.fecha_fin == null ? (visita.fecha).split(" ")[0].split("-")[2] + "/" + (visita.fecha).split(" ")[0].split("-")[1] + "/" + (visita.fecha).split(" ")[0].split("-")[0] + "<br>" + (visita.fecha).split(" ")[1] : (visita.fecha_fin).split(" ")[0].split("-")[2] + "/" + (visita.fecha_fin).split(" ")[0].split("-")[1] + "/" + (visita.fecha_fin).split(" ")[0].split("-")[0] + "<br>" + (visita.fecha_fin).split(" ")[1]}</td>
<td class="text-center">
<button class="btn btn-danger eliminar-visita"><i class="zmdi zmdi-delete"></i></button>
</td>

</tr > `;

        // $('[data-toggle="tooltip"]').tooltip();
    });


    document.querySelector('#tbodyVisita').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePageVisita").value),
        document.querySelector("#pageVisita"),
        $('#modalCargandoVisita'),
        $('#paginationVisita'));
    addEventsButtonsAdmin();


}

function addEventsButtonsAdmin() {

    document.querySelectorAll('.eliminar-visita').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            visitaSelected = findByVisita(
                btn.parentElement.parentElement.getAttribute('idvisita')
            );

            if (visitaSelected != undefined) {
                beanRequestVisita.type_request = 'GET';
                beanRequestVisita.operation = 'delete';
                $('#modalCargandoVisita').modal('show');
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
        document.querySelector('#modalFrameContenidoVisita').appendChild(iframe);
    }
    iframe.src = url;
};

function findIndexVisita(idbusqueda) {
    return beanPaginationVisita.list.findIndex(
        (Visita) => {
            if (Visita.idvisita == parseInt(idbusqueda))
                return Visita;


        }
    );
}

function findByVisita(idvisita) {
    return beanPaginationVisita.list.find(
        (Visita) => {
            if (parseInt(idvisita) == Visita.idvisita) {
                return Visita;
            }


        }
    );
}

var validarDormularioVideo = () => {
    if (document.querySelector("#txtNombreVisita").value == "") {
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

        if (document.querySelector("#txtPregunta" + index + "Visita").value == "") {
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