var beanPaginationTest;
var testSelected, detalleSelected;
var SubtituloSelected, capituloSelected;
var contadorDetalleTest = 0;
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
    document.querySelector("#tipoOpcionHeaderCurso").innerHTML = "PREGUNTAS INTERNAS";
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
        $("#tituloModalManTest").html("INSERTAR CUESTIONARIO INTERNO");

        addTest();
        $("#ventanaModalManTest").modal("show");
    });
    document.querySelector("#btnAgregarSubtitulo").onclick = function () {
        if (beanPaginationSubtituloC == undefined) {
            $('#modalCargandoSubtituloC').modal('show');

        } else {
            addDetalle();
        }

    };
    document.querySelector("#txtSubtitulo").onchange = function () {
        if (beanPaginationSubtituloC == undefined) {
            $('#modalCargandoSubtituloC').modal('show');

        }
        SubtituloSelected = findBySubtituloC(document.querySelector("#txtSubtitulo").value);
        let arraySubtitulo = SubtituloSelected.codigo.split(".");
        capituloSelected = { codigo: arraySubtitulo[0] + "." + arraySubtitulo[1] + "." + arraySubtitulo[2] };
        listDetalleTest = [];
        toListTestDetalle(listDetalleTest);

    };
    document.querySelector("#txtSubtitulo").onclick = function () {
        if (beanPaginationSubtituloC == undefined) {
            $('#modalCargandoSubtituloC').modal('show');

        }

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


function addDetalle() {

    if (SubtituloSelected == undefined) {
        return showAlertTopEnd("info", "Seleccione el Subtítulo", "");;
    }
    let arraySubtitulo = SubtituloSelected.codigo.split(".");
    if (capituloSelected == undefined) {
        capituloSelected = { codigo: arraySubtitulo[0] + "." + arraySubtitulo[1] + "." + arraySubtitulo[2] };
    }

    listDetalleTest.unshift(new Detalle_Test(
        contadorDetalleTest++,
        "",
        SubtituloSelected.codigo,
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
            json.test = new Test(listDetalleTest.length, document.querySelector("#txtNombreTest").value, document.querySelector("#txtDescripcionTest").value, SubtituloSelected.nombre, SubtituloSelected.codigo, capituloSelected.codigo, testSelected.idtest);
            if (document.querySelector("#txtImagenTest").files.length != 0) {
                let dataImagen = $("#txtImagenTest").prop("files")[0];
                form_data.append("txtImagenTest", dataImagen);
            }
            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':
            json.test = new Test(listDetalleTest.length, document.querySelector("#txtNombreTest").value, document.querySelector("#txtDescripcionTest").value, SubtituloSelected.nombre, SubtituloSelected.codigo, capituloSelected.codigo);
            let dataImagen2 = $("#txtImagenTest").prop("files")[0];
            form_data.append("txtImagenTest", dataImagen2);
            form_data.append("class", JSON.stringify(json));
            break;

        default:

            parameters_pagination +=
                '?filtro=2';
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
                document.querySelector("#txtImagenTest").value = "";
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
    document.querySelector('#txtNombreTest').value = (test == undefined) ? '' : test.nombre;
    document.querySelector('#txtDescripcionTest').value = (test == undefined) ? '' : test.descripcion;
    SubtituloSelected = (test == undefined) ? undefined : { codigo: test.subcodigo, nombre: test.sub };
    capituloSelected = (test == undefined) ? undefined : test.titulo;
    if (beanPaginationSubtituloC == undefined
    ) {
        document.querySelector('#txtSubtitulo').innerHTML = `<option value="0" selected="selected">SIN DEFINIR</option>`;
    } else {
        document.querySelector('#txtSubtitulo > option[value="0"]').setAttribute('selected', 'selected');
    }
    if (test !== undefined) {
        $("#imagePreviewTest").html(
            `<img width='100%' alt='user-picture' class='img-responsive center-box' src='${getHostFrontEnd()}adjuntos/libros/subtitulos/${test.imagen}' />`
        );
    } else {
        $("#imagePreviewTest").html(
            ""
        );

    }
    $("#txtImagenTest").change(function () {
        filePreview(this, "#imagePreviewTest");
    });

}

function listaTest(beanPagination) {
    document.querySelector('#tbodyTest').innerHTML = '';
    document.querySelector('#titleManagerTest').innerHTML =
        ' CUESTIONARIOS INTERNOS';
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
<td class="text-center d-none">${test.titulo.codigo}</td>
<td class="text-center">${test.nombre} </td>
<td class="text-center"><textarea class="w-100 text-justify" rows="4" style="border: none;background: transparent;">${test.descripcion}</textarea></td>
<td class="text-center">${test.sub}</td>
<td class="text-center f-weight-600 " style="
font-size: 22px;"> <p style="border: 3px solid #7030a0;">${test.cantidad}</p></td>
<td class="text-center ver-capitulo" style="width:10%;"><img src="${getHostFrontEnd()}adjuntos/libros/subtitulos/${test.imagen}" alt="user-picture" class="img-responsive center-box" width="100%" ></td>
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
                $("#tituloModalManTest").html("EDITAR CUESTIONARIO INTERNO");

                $("#ventanaModalManTest").modal("show");
                beanRequestTest.type_request = 'POST';
                beanRequestTest.operation = 'update';
                document.querySelector('#txtSubtitulo').innerHTML = `<option value="0" selected="selected">SIN DEFINIR</option><option value="${testSelected.subcodigo}">${testSelected.subcodigo} - ${testSelected.sub}</option> `;
                document.querySelector('#txtSubtitulo > option[value="' + testSelected.subcodigo + '"]').setAttribute('selected', 'selected');

            } else {
                console.log(
                    'warning',
                    'No se encontró el cuestionario para poder editar'
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

    if (beanRequestTest.operation == 'add') {

        /*IMAGEN */
        if (document.querySelector("#txtImagenTest").files.length == 0) {
            showAlertTopEnd("info", "Vacío", "ingrese Imagen");
            return false;
        }
        if (!(document.querySelector("#txtImagenTest").files[0].type == "image/png" || document.querySelector("#txtImagenTest").files[0].type == "image/jpg" || document.querySelector("#txtImagenTest").files[0].type == "image/jpeg")) {
            showAlertTopEnd("info", "Formato Incorrecto", "Ingrese tipo de arhivo Imagen => png,jpg,jpeg");
            return false;
        }
        //menor a   5700 KB
        if (document.querySelector("#txtImagenTest").files[0].size > (5700 * 1024)) {
            showAlertTopEnd("info", "Tamaño excedido", "el tamaño del archivo tiene que ser menor a 5700 KB");
            return false;
        }


    } else {
        if (document.querySelector("#txtImagenTest").files.length != 0) {
            if (!(document.querySelector("#txtImagenTest").files[0].type == "image/png" || document.querySelector("#txtImagenTest").files[0].type == "image/jpg" || document.querySelector("#txtImagenTest").files[0].type == "image/jpeg")) {
                showAlertTopEnd("info", "Formato Incorrecto", "Ingrese tipo de arhivo Imagen => png,jpg,jpeg");
                return false;
            }
            //menor a   5700 KB
            if (document.querySelector("#txtImagenTest").files[0].size > (5700 * 1024)) {
                showAlertTopEnd("info", "Tamaño excedido", "el tamaño del archivo tiene que ser menor a 5700 KB");
                return false;
            }

        }
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
    contador = listDetalleTest.length + 1;
    beanPagination.forEach((detalle) => {
        contador--;
        if ((contador + "").length == 1) {
            contador = "0" + contador;
        }
        row += `<div class="col-9 col-sm-9">
        <div class="row">
          <div class="col-12">
            <label >Pregunta N° ${contador}</label>
            <div class="group-material">
              <textarea class="material-control w-100 descripcion-detalle-test" data-toggle="tooltip" required="" data-placement="top" iddetalle="${detalle.iddetalletest}"
                title="" data-original-title="Escribe la url de youtube" 
                rows="3">${detalle.descripcion}</textarea>
            </div>
          </div>

        </div>
      </div>
      <div class="col-3 col-sm-2" style="padding-top: 3em;">
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
            if (parseInt(iddetalletest) == detalle.iddetalletest) {
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
