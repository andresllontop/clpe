var beanPaginationTest;
var testSelected;
var capituloSelected;
var beanRequestTest = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestTest.entity_api = 'test';
    beanRequestTest.operation = 'paginate';
    beanRequestTest.type_request = 'GET';

    $('#sizePageTest').change(function () {
        beanRequestTest.type_request = 'GET';
        beanRequestTest.operation = 'paginate';
        $('#modalCargandoTest').modal('show');
    });

    $('#modalCargandoTest').modal('show');

    $("#modalCargandoTest").on('shown.bs.modal', function () {
        processAjaxTest();
    });
    $("#ventanaModalManTest").on('hide.bs.modal', function () {
        beanRequestTest.type_request = 'GET';
        beanRequestTest.operation = 'paginate';
    });

    $("#btnAbrirTest").click(function () {
        beanRequestTest.operation = 'add';
        beanRequestTest.type_request = 'POST';
        $("#tituloModalManTest").html("REGISTRAR CUESTIONARIO INTERNO");
        addTest();
        $("#ventanaModalManTest").modal("show");


    });
    $("#formularioTest").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (validarDormularioTest()) {
            $('#modalCargandoTest').modal('show');
        }
    });

});

function processAjaxTest() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestTest.operation == 'update' ||
        beanRequestTest.operation == 'add'
    ) {

        json = {
            descripcion: document.querySelector("#txtNombreTest").value,
            tipo: 2,
            titulo: capituloSelected.codigo,
        };


    } else {
        form_data = null;
    }

    switch (beanRequestTest.operation) {
        case 'delete':
            parameters_pagination = '?id=' + testSelected.idtest;
            break;

        case 'update':
            json.idtest = testSelected.idtest;
            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':

            form_data.append("class", JSON.stringify(json));
            break;

        default:

            parameters_pagination +=
                '?filtro=2';
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pageTest").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePageTest").value.trim();
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestTest.entity_api + "/" + beanRequestTest.operation +
            parameters_pagination,
        type: beanRequestTest.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestTest.operation == 'update' || beanRequestTest.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json'
    }).done(function (beanCrudResponse) {

        $('#modalCargandoTest').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                document.querySelector("#pageTest").value = 1;
                document.querySelector("#sizePageTest").value = 5;
                $('#ventanaModalManTest').modal('hide');
            } else {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationTest = beanCrudResponse.beanPagination;
            listaTest(beanPaginationTest);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoTest').modal("hide");
        showAlertErrorRequest();

    });

}

function addTest(test = undefined) {
    //LIMPIAR LOS CAMPOS

    document.querySelector('#txtNombreTest').value = (test == undefined) ? '' : test.descripcion;

    capituloSelected = (test == undefined) ? undefined : test.titulo;
    document.querySelector('#txtCapituloTest').value = (test == undefined) ? '' : test.titulo.codigo + " - " + test.titulo.nombre;



}

function listaTest(beanPagination) {
    document.querySelector('#tbodyTest').innerHTML = '';
    document.querySelector('#titleManagerTest').innerHTML =
        '[ ' + beanPagination.countFilter + ' ] CUESTIONARIOS INTERNOS';
    let row = "";
    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationTest'));
        row += `<tr>
        <td class="text-center" colspan="5">NO HAY CUESTIONARIOS INTERNOS</td>
        </tr>`;
        document.querySelector('#tbodyTest').innerHTML = row;
        return;
    }
    beanPagination.list.forEach((test) => {

        row += `<tr  idtest="${test.idtest}">
<td class="text-center">${test.titulo.nombre} <p>${test.titulo.codigo}</p></td>
<td class="text-center">${test.descripcion}</td>
<td class="text-center">
<button class="btn btn-warning ver-preguntas" ><i class="zmdi zmdi-collection-text"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-info editar-test" ><i class="zmdi zmdi-refresh"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-test"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;

        // $('[data-toggle="tooltip"]').tooltip();
    });


    document.querySelector('#tbodyTest').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePageTest").value),
        document.querySelector("#pageTest"),
        $('#modalCargandoTest'),
        $('#paginationTest'));
    addEventsButtonsTest();


}

function addEventsButtonsTest() {
    document.querySelectorAll('.ver-preguntas').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            testSelected = findByTest(
                btn.parentElement.parentElement.getAttribute('idtest')
            );
            if (testSelected != undefined) {
                $("#ModalDetalle").modal("show");
                document.querySelector('#titleManagerDetalle').innerHTML = testSelected.titulo.nombre;
                $('#modalCargandoDetalle').modal('show');
            } else {
                console.log(
                    'warning => ',
                    'No se encontró el capitulo para poder ver'
                );
            }
        };
    });

    document.querySelectorAll('.editar-test').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            testSelected = findByTest(
                btn.parentElement.parentElement.getAttribute('idtest')
            );

            if (testSelected != undefined) {
                addTest(testSelected);
                $("#tituloModalManTest").html("EDITAR CUESTIONARIO INTERNO");
                $("#ventanaModalManTest").modal("show");
                beanRequestTest.type_request = 'POST';
                beanRequestTest.operation = 'update';
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
    document.querySelectorAll('.eliminar-test').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            testSelected = findByTest(
                btn.parentElement.parentElement.getAttribute('idtest')
            );

            if (testSelected != undefined) {
                beanRequestTest.type_request = 'GET';
                beanRequestTest.operation = 'delete';
                $('#modalCargandoTest').modal('show');
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
}

function findIndexTest(idbusqueda) {
    return beanPaginationTest.list.findIndex(
        (Test) => {
            if (Test.idtest == parseInt(idbusqueda))
                return Test;


        }
    );
}

function findByTest(idtest) {
    return beanPaginationTest.list.find(
        (Test) => {
            if (parseInt(idtest) == Test.idtest) {
                return Test;
            }


        }
    );
}

var validarDormularioTest = () => {
    if (document.querySelector("#txtNombreTest").value == "") {
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




    return true;
}