var beanPaginationBeneficio;
var beneficioSelected;
var beanRequestBeneficio = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestBeneficio.entity_api = 'subitems';
    beanRequestBeneficio.operation = 'paginate';
    beanRequestBeneficio.type_request = 'GET';

    $('#sizePageBeneficio').change(function () {
        beanRequestBeneficio.type_request = 'GET';
        beanRequestBeneficio.operation = 'paginate';
        $('#modalCargandoBeneficio').modal('show');
    });

    // $('#modalCargandoBeneficio').modal('show');

    $("#modalCargandoBeneficio").on('shown.bs.modal', function () {
        processAjaxBeneficio();
    });
    $("#ventanaModalManBeneficio").on('hide.bs.modal', function () {
        beanRequestBeneficio.type_request = 'GET';
        beanRequestBeneficio.operation = 'paginate';
    });
    $("#txtDescripcionBeneficio").Editor();

    $("#btnAbrirbook").click(function () {
        beanRequestBeneficio.operation = 'add';
        beanRequestBeneficio.type_request = 'POST';
        $("#imagePreview").html("");
        $("#tituloModalManBeneficio").html("REGISTRAR BENEFICIO DEL CURSO");
        addBeneficio();
        $("#ventanaModalManBeneficio").modal("show");


    });

    $("#formularioBeneficio").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (validarDormularioVideo()) {
            $('#modalCargandoBeneficio').modal('show');
        }
    });

});

function processAjaxBeneficio() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestBeneficio.operation == 'update' ||
        beanRequestBeneficio.operation == 'add'
    ) {

        json = {
            titulo: "",
            detalle: $("#txtDescripcionBeneficio").Editor("getText"),
            tipo: 1,
            curso: curso_cSelected.idcurso
        };
        form_data.append("class", JSON.stringify(json));

    } else {
        form_data = null;
    }

    switch (beanRequestBeneficio.operation) {
        case 'delete':
            parameters_pagination = '?id=' + beneficioSelected.idsubitem;
            break;
        case 'curso':
            parameters_pagination = '?idcurso=' + curso_cSelected.idcurso + '&tipo=1';
            break;
        case 'update':
            json.idsubitem = beneficioSelected.idsubitem;
            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':
            form_data.append("class", JSON.stringify(json));
            break;

        default:

            parameters_pagination +=
                '?filtro=&tipo=1';
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pageBeneficio").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePageBeneficio").value.trim();
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestBeneficio.entity_api + "/" + beanRequestBeneficio.operation +
            parameters_pagination,
        type: beanRequestBeneficio.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestBeneficio.operation == 'update' || beanRequestBeneficio.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',
    }).done(function (beanCrudResponse) {

        $('#modalCargandoBeneficio').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                document.querySelector("#pageBeneficio").value = 1;
                document.querySelector("#sizePageBeneficio").value = 20;
                $('#ventanaModalManBeneficio').modal('hide');
            } else {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationBeneficio = beanCrudResponse.beanPagination;
            listaBeneficio(beanPaginationBeneficio);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoBeneficio').modal("hide");
        showAlertErrorRequest();

    });

}

function addBeneficio(beneficio = undefined) {

    $("#txtDescripcionBeneficio").Editor("setText", (beneficio == undefined) ? '<p style="color:black"></p>' : beneficio.detalle);
    $("#txtDescripcionBeneficio").Editor("getText");


}

function listaBeneficio(beanPagination) {
    document.querySelector('#tbodyBeneficio').innerHTML = '';
    document.querySelector('#titleManagerBeneficio').innerHTML =
        '[ ' + beanPagination.countFilter + ' ]  BENEFICIOS DEL CURSO';
    let row = "";
    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationBeneficio'));
        row += `<tr>
        <td class="text-center" colspan="3">NO HAY BENEFICIOS</td>
        </tr>`;

        document.querySelector('#tbodyBeneficio').innerHTML += row;
        return;
    }
    beanPagination.list.forEach((beneficio) => {

        row += `<tr  idsubitem="${beneficio.idsubitem}">
<td class="text-center" >${beneficio.detalle}</td>
<td class="text-center">
<button class="btn btn-info editar-beneficio" ><i class="zmdi zmdi-refresh"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-beneficio"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;

        // $('[data-toggle="tooltip"]').tooltip();
    });


    document.querySelector('#tbodyBeneficio').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePageBeneficio").value),
        document.querySelector("#pageBeneficio"),
        $('#modalCargandoBeneficio'),
        $('#paginationBeneficio'));
    addEventsButtonsBeneficio();


}

function addEventsButtonsBeneficio() {


    document.querySelectorAll('.editar-beneficio').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            beneficioSelected = findByBeneficio(
                btn.parentElement.parentElement.getAttribute('idsubitem')
            );

            if (beneficioSelected != undefined) {
                addBeneficio(beneficioSelected);
                $("#tituloModalManBeneficio").html("EDITAR BENEFICIO DEL CURSO");
                $("#ventanaModalManBeneficio").modal("show");
                beanRequestBeneficio.type_request = 'POST';
                beanRequestBeneficio.operation = 'update';
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
    document.querySelectorAll('.eliminar-beneficio').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            beneficioSelected = findByBeneficio(
                btn.parentElement.parentElement.getAttribute('idsubitem')
            );

            if (beneficioSelected != undefined) {
                beanRequestBeneficio.type_request = 'GET';
                beanRequestBeneficio.operation = 'delete';
                $('#modalCargandoBeneficio').modal('show');
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
    document.querySelectorAll('.btn-regresar-curso').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {

            document.querySelector('#cursoHTML').classList.remove("d-none");
            document.querySelector('#beneficioHTML').classList.add("d-none");

        };
    });
}

function findIndexBeneficio(idbusqueda) {
    return beanPaginationBeneficio.list.findIndex(
        (Beneficio) => {
            if (Beneficio.idsubitem == parseInt(idbusqueda))
                return Beneficio;


        }
    );
}

function findByBeneficio(idsubitem) {
    return beanPaginationBeneficio.list.find(
        (Beneficio) => {
            if (parseInt(idsubitem) == Beneficio.idsubitem) {
                return Beneficio;
            }


        }
    );
}

var validarDormularioVideo = () => {
    if ($("#txtDescripcionBeneficio").Editor("getText") == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Descripción",
            type: "warning",
            timer: 800,
            showConfirmButton: false
        });
        return false;
    }

    return true;
}

//CURSO



function addEventsButtonsCurso_c() {
    document.querySelectorAll('.beneficio-curso').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            curso_cSelected = findByCurso_c(
                btn.parentElement.parentElement.getAttribute('idcurso')
            );

            if (curso_cSelected != undefined) {
                beanRequestBeneficio.type_request = 'GET';
                beanRequestBeneficio.operation = 'curso';
                addClass(document.querySelector("#cursoHTML"), "d-none");
                removeClass(document.querySelector("#beneficioHTML"), "d-none");
                $('#modalCargandoBeneficio').modal('show');
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });

}