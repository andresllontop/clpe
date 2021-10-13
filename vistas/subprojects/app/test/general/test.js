var beanPaginationTest;
var testSelected, detalleSelected;
var capituloSelected;
var contadorDetalleTest = 1;
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
    document.querySelector("#tipoOpcionHeaderCurso").innerHTML = "PREGUNTAS DE REFORSAMIENTO";
    // $('#modalCargandoTest').modal('show');
    $('#modalCargandoCurso_c').modal('show');
    $("#modalCargandoTest").on('shown.bs.modal', function () {
        processAjaxTest();
    });
    $("#ventanaModalManTest").on('hide.bs.modal', function () {
        beanRequestTest.type_request = 'GET';
        beanRequestTest.operation = 'paginate';
    });

    $("#btnAbrirTest").click(function () {
        document.querySelector('#listaDetalle').innerHTML = "";
        listDetalleTest = [];
        beanRequestTest.operation = 'add';
        beanRequestTest.type_request = 'POST';
        $("#tituloModalManTest").html("INSERTAR PREGUNTAS DE REFORSAMIENTO");
        addTest();
        $("#ventanaModalManTest").modal("show");
    });
    document.querySelector("#btnAgregarSubtitulo").onclick = function () {

        if (capituloSelected == undefined) {
            return showAlertTopEnd("info", "Seleccione el Capítulo", "");
        }
        if (beanPaginationSubtituloC == undefined) {
            $('#modalCargandoSubtituloC').modal('show');

        } else {
            addDetalle();
        }

    };
    document.querySelector("#txtCapitulo").onchange = function () {
        capituloSelected = findByCapituloC(document.querySelector("#txtCapitulo").value);
        listDetalleTest = [];
        toListTestDetalle(listDetalleTest);
        $('#modalCargandoSubtituloC').modal('show');

    };

    $("#formularioTest").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (validarDormularioTest()) {
            $('#modalCargandoTest').modal('show');
        }
    });
    document.querySelectorAll('.btn-regresar').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            document.querySelector('#cursoHTML').classList.remove("d-none");
            document.querySelector('#seccion-cliente').classList.add("d-none");
        };
    });

});

function addDetalle() {

    if (capituloSelected == undefined) {
        return showAlertTopEnd("info", "Seleccione el Capítulo", "");
    }

    listDetalleTest.unshift(new Detalle_Test(
        contadorDetalleTest++,
        "",
        beanPaginationSubtituloC.list[0].codigo,
        capituloSelected.codigo)

    );
    toListTestDetalle(listDetalleTest);
}

function processAjaxTest() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestTest.operation == 'update' ||
        beanRequestTest.operation == 'add'
    ) {

        json = {
            test: "",
            lista: listDetalleTest
        };


    } else {
        form_data = null;
    }

    switch (beanRequestTest.operation) {
        case 'delete':
            parameters_pagination = '?id=' + testSelected.idtest;
            break;

        case 'update':
            json.test = new Test(listDetalleTest.length, document.querySelector("#txtNombreTest").value, document.querySelector("#txtDescripcionTest").value, capituloSelected.codigo, testSelected.idtest),
                form_data.append("class", JSON.stringify(json));
            break;
        case 'add':
            json.test = new Test(listDetalleTest.length, document.querySelector("#txtNombreTest").value, document.querySelector("#txtDescripcionTest").value, capituloSelected.codigo),

                form_data.append("class", JSON.stringify(json));
            break;

        default:

            parameters_pagination +=
                '?filtro=1';
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pageTest").value.trim();
            parameters_pagination +=
                '&libro=' + curso_cSelected.codigo;
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
                document.querySelector("#sizePageTest").value = 20;
                $('#ventanaModalManTest').modal('hide');
            } else {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {
            beanPaginationTest = beanCrudResponse.beanPagination;
            listaTest(beanPaginationTest);

        }
        if (beanPaginationCapituloC == undefined) {
            $('#modalCargandoCapituloC').modal('show');
            addTest();
        }

    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoTest').modal("hide");
        showAlertErrorRequest();

    });

}

function addTest(test = undefined) {
    //LIMPIAR LOS CAMPOS
    document.querySelector('#txtNombreTest').value = (test == undefined) ? '' : test.nombre;
    document.querySelector('#txtDescripcionTest').value = (test == undefined) ? '' : test.descripcion;
    capituloSelected = (test == undefined) ? undefined : test.titulo;
    document.querySelector('#txtCapitulo > option[value="' + ((test == undefined) ? '0' : + test.titulo.idtitulo) + '"]').setAttribute('selected', 'selected');


}

function listaTest(beanPagination) {
    document.querySelector('#tbodyTest').innerHTML = '';
    document.querySelector('#titleManagerTest').innerHTML =
        ' LISTA DE PREGUNTAS DE REFORZAMIENTO N01';
    let row = "", contador = 1;
    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationTest'));
        row += `<tr>
        <td class="text-center" colspan="7">NO HAY CUESTIONARIOS</td>
        </tr>`;
        document.querySelector('#tbodyTest').innerHTML = row;
        return;
    }
    beanPagination.list.forEach((test) => {

        row += `<tr  idtest="${test.idtest}">
<td class="text-center">${contador++} </td>
<td class="text-center">${test.titulo.codigo} </td>
<td class="text-center">${test.titulo.nombre} </td>
<td class="text-center">${test.nombre}</td>
<td class="text-center f-weight-600 " style="
font-size: 22px;"> <p style="border: 3px solid #7030a0;">${test.cantidad}</p></td>

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
                beanRequestTest.operation = 'paginate';
                beanRequestTest.type_request = 'GET';
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
                processAjaxDetalle();
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
            document.querySelector('#listaDetalle').innerHTML = "";
            listDetalleTest = [];
            testSelected = findByTest(
                btn.parentElement.parentElement.getAttribute('idtest')
            );

            if (testSelected != undefined) {
                beanRequestDetalle.type_request = 'GET';
                beanRequestDetalle.operation = 'paginate';
                addTest(testSelected);
                $('#modalCargandoDetalle').modal('show');
                $("#tituloModalManTest").html("EDITAR PREGUNTAS DE REFORSAMIENTO");
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
    if (document.querySelector("#txtDescripcionTest").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Instrucciones",
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

    if (listDetalleTest.length == 0) {
        swal({
            title: "Vacío",
            text: "Agrega Preguntas",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }


    return true;
}

function toListTestDetalle(beanPagination) {

    document.querySelector('#listaDetalle').innerHTML = "";
    let row = "", contador = 0;
    if (beanPagination.length == 0) {
        document.querySelector('#listaDetalle').innerHTML += row;
        return;
    }
    beanPagination.forEach((detalle) => {
        contador++;
        if ((contador + "").length == 1) {
            contador = "0" + contador;
        }
        row += `<div class="col-9 col-sm-9">
        <div class="row">
          <div class="col-12">
            <!-- SUBTITULO-->
            <label>Nombre del Subtitulo</label>
            <div class="group-material mb-2">
              <select class="material-control seleccionar-subtitulo" iddetalle="${detalle.iddetalletest}">`;
        if (beanPaginationSubtituloC != undefined) {
            beanPaginationSubtituloC.list.forEach((subtitulo) => {
                if (subtitulo.codigo == detalle.subtitulo) {
                    row += `<option value="${subtitulo.codigo}" selected="selected">${subtitulo.codigo} - ${subtitulo.nombre}</option>
                        `;
                } else {
                    row += `<option value="${subtitulo.codigo}">${subtitulo.codigo} - ${subtitulo.nombre}</option>
                        `;
                }

            });
        }

        row += `
              </select>
            </div>
          </div>
          <div class="col-12">
            <label >Pregunta N${contador}</label>
            <div class="group-material">
              <textarea class="material-control w-100 descripcion-detalle-test" data-toggle="tooltip" required="" data-placement="top" iddetalle="${detalle.iddetalletest}"
                title="" data-original-title="Escribe la url de youtube" 
                rows="3">${detalle.descripcion}</textarea>
            </div>
          </div>

        </div>
      </div>
      <div class="col-3 col-sm-2" style="padding-top: 8em;">
        <button type="button" class="btn btn-danger eliminar-detalle" iddetalle="${detalle.iddetalletest}">
          ELIMINAR
        </button>
      </div>
  `;
        // $('[data-toggle="tooltip"]').tooltip();
    });

    document.querySelector('#listaDetalle').innerHTML += row;

    addEventsDetalleCompra();

}

var addEventsDetalleCompra = () => {

    document.querySelectorAll('.seleccionar-subtitulo').forEach((btn) => {
        btn.onchange = () => {
            detalleSelected = findByDetalle(
                btn.getAttribute(
                    'iddetalle'
                )
            );

            if (detalleSelected == undefined) return false;
            eliminarDetalle(detalleSelected.iddetalletest);
            listDetalleTest.push(
                new Detalle_Test(
                    detalleSelected.iddetalletest,
                    detalleSelected.descripcion,
                    btn.value,
                    capituloSelected.codigo
                )
            );
        };
    });
    /* inputs teclado*/
    document.querySelectorAll('.descripcion-detalle-test').forEach((btn) => {
        btn.onkeyup = () => {
            detalleSelected = findByDetalle(
                btn.getAttribute(
                    'iddetalle'
                )
            );
            if (detalleSelected == undefined) return false;
            eliminarDetalle(detalleSelected.iddetalletest);
            listDetalleTest.push(
                new Detalle_Test(
                    detalleSelected.iddetalletest,
                    btn.value,
                    detalleSelected.subtitulo,
                    capituloSelected.codigo
                )
            );
        };
    });
    /* eliminar compra*/
    document.querySelectorAll('.eliminar-detalle').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {

            detalleSelected = findByDetalle(
                btn.getAttribute(
                    'iddetalle'
                )
            );
            eliminarDetalle(detalleSelected.iddetalletest);
            toListTestDetalle(listDetalleTest);
        };
    });

};

var findByDetalle = (iddetalletest) => {

    return listDetalleTest.find(
        (detalle) => {
            if (parseInt(iddetalletest) == parseInt(detalle.iddetalletest)) {
                return detalle;
            }


        }
    );
};

var eliminarDetalle = (idbusqueda) => {
    listDetalleTest.splice(findIndexDetalle(parseInt(idbusqueda)), 1);
};

function findIndexDetalle(idbusqueda) {
    return listDetalleTest.findIndex(
        (cargo) => {
            if (cargo.iddetalletest == parseInt(idbusqueda))
                return cargo;


        }
    );
}
