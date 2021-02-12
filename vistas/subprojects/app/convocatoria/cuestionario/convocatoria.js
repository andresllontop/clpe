var beanPaginationConvocatoria;
var convocatoriaSelected, detalleSelected;
var contadorDetalleConvocatoria = 1;
var beanRequestConvocatoria = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestConvocatoria.entity_api = 'convocatoria';
    beanRequestConvocatoria.operation = 'paginate';
    beanRequestConvocatoria.type_request = 'GET';

    $('#sizePageConvocatoria').change(function () {
        beanRequestConvocatoria.type_request = 'GET';
        beanRequestConvocatoria.operation = 'paginate';
        $('#modalCargandoConvocatoria').modal('show');
    });
    $("#txtDescripcionConvocatoria").Editor();
    $('#modalCargandoConvocatoria').modal('show');

    $("#modalCargandoConvocatoria").on('shown.bs.modal', function () {
        processAjaxConvocatoria();
    });
    $("#ventanaModalManConvocatoria").on('hide.bs.modal', function () {
        beanRequestConvocatoria.type_request = 'GET';
        beanRequestConvocatoria.operation = 'paginate';
    });

    $("#btnAbrirConvocatoria").click(function () {
        document.querySelector('#listaDetalle').innerHTML = "";
        listDetalleConvocatoria = [];
        beanRequestConvocatoria.operation = 'add';
        beanRequestConvocatoria.type_request = 'POST';
        $("#tituloModalManConvocatoria").html("INGRESAR PREGUNTAS");
        addConvocatoria();
        $("#ventanaModalManConvocatoria").modal("show");
    });
    document.querySelector("#btnAgregarPregunta").onclick = function () {
        addDetalle();
    };

    $("#formularioConvocatoria").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (validarFormularioConvocatoria()) {
            $('#modalCargandoConvocatoria').modal('show');
        }
    });

});

function addDetalle() {

    listDetalleConvocatoria.unshift(new Detalle_Convocatoria(
        contadorDetalleConvocatoria++,
        "", 0)

    );
    toListConvocatoriaDetalle(listDetalleConvocatoria);
}

function addDetalleUpdate(bean) {

    if (bean.countFilter == 0) {
        return;
    }
    contadorDetalleConvocatoria = 0;
    bean.list.forEach(pregunta => {
        listDetalleConvocatoria.unshift(new Detalle_Convocatoria(
            contadorDetalleConvocatoria++,
            pregunta.descripcion, 0)

        );
    });
    beanRequestConvocatoria.type_request = 'POST';
    beanRequestConvocatoria.operation = 'update';
    toListConvocatoriaDetalle(listDetalleConvocatoria);
}

function processAjaxConvocatoria() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestConvocatoria.operation == 'update' || beanRequestConvocatoria.operation == 'estado' ||
        beanRequestConvocatoria.operation == 'add'
    ) {

        json = {
            convocatoria: "",
            lista: listDetalleConvocatoria
        };


    } else {
        form_data = null;
    }

    switch (beanRequestConvocatoria.operation) {
        case 'delete':
            parameters_pagination = '?id=' + convocatoriaSelected.idconvocatoria;
            break;
        case 'estado':
            json = {
                idconvocatoria: convocatoriaSelected.idconvocatoria,
                estado: convocatoriaSelected.estado == 0 ? 1 : 0
            };
            form_data.append("class", JSON.stringify(json));
            break;

        case 'update':
            if (document.querySelector("#txtImagenConvocatoria").files.length != 0) {
                let dataImagen = $("#txtImagenConvocatoria").prop("files")[0];
                form_data.append("txtImagenConvocatoria", dataImagen);
            }
            json.convocatoria = new Convocatoria(listDetalleConvocatoria.length, document.querySelector("#txtFechaConvocatoria").value, $("#txtDescripcionConvocatoria").Editor("getText"), document.querySelector("#txtCodigoConvocatoria").value, 0, convocatoriaSelected.idconvocatoria);
            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':
            let dataImagen2 = $("#txtImagenConvocatoria").prop("files")[0];
            form_data.append("txtImagenConvocatoria", dataImagen2);
            json.convocatoria = new Convocatoria(listDetalleConvocatoria.length, document.querySelector("#txtFechaConvocatoria").value, $("#txtDescripcionConvocatoria").Editor("getText"), document.querySelector("#txtCodigoConvocatoria").value);

            form_data.append("class", JSON.stringify(json));
            break;
        case 'detalle':
            parameters_pagination = '?id=' + convocatoriaSelected.idconvocatoria;
            break;

        default:

            parameters_pagination +=
                '?filtro=1';
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pageConvocatoria").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePageConvocatoria").value.trim();
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestConvocatoria.entity_api + "/" + beanRequestConvocatoria.operation +
            parameters_pagination,
        type: beanRequestConvocatoria.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestConvocatoria.operation == 'update' || beanRequestConvocatoria.operation == 'estado' || beanRequestConvocatoria.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json', xhr: function () {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    $('.progress-bar-convocatoria').css({
                        width: Math.round(percentComplete * 100) + '%'
                    });
                    $(".progress-bar-convocatoria").text(Math.round(percentComplete * 100) + '%');
                    $(".progress-bar-convocatoria").attr("aria-valuenow", +Math.round(percentComplete * 100));
                    if (percentComplete === 1) {
                        // $('.progress-bar-convocatoria').addClass('hide');
                        $('.progress-bar-convocatoria').css({
                            width: + '100%'
                        });
                        $(".progress-bar-convocatoria").text("Cargando ... 100%");
                        $(".progress-bar-convocatoria").attr("aria-valuenow", "100");
                    }
                }
            }, false);
            xhr.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    $('.progress-bar-convocatoria').css({
                        width: Math.round(percentComplete * 100) + '%'
                    });
                    $(".progress-bar-convocatoria").text(Math.round(percentComplete * 100) + '%');
                    $(".progress-bar-convocatoria").attr("aria-valuenow", +Math.round(percentComplete * 100));
                }
            }, false);
            return xhr;
        },
    }).done(function (beanCrudResponse) {

        $('#modalCargandoConvocatoria').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                document.querySelector("#pageConvocatoria").value = 1;
                document.querySelector("#sizePageConvocatoria").value = 20;
                $('#ventanaModalManConvocatoria').modal('hide');
            } else {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {
            if (beanRequestConvocatoria.operation == 'detalle') {
                addDetalleUpdate(beanCrudResponse.beanPagination);
            } else {
                beanPaginationConvocatoria = beanCrudResponse.beanPagination;
                listaConvocatoria(beanPaginationConvocatoria);
            }
        }

    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoConvocatoria').modal("hide");
        showAlertErrorRequest();

    });

}


function addConvocatoria(convocatoria = undefined) {
    //LIMPIAR LOS CAMPOS
    document.querySelector('#txtFechaConvocatoria').value = (convocatoria == undefined) ? '' : (convocatoria.fecha.split(" ")[0]) + "T" + (convocatoria.fecha.split(" ")[1]);

    document.querySelector("#txtCodigoConvocatoria").value = (convocatoria == undefined) ? "" : convocatoria.codigo;


    $("#txtDescripcionConvocatoria").Editor("setText", (convocatoria == undefined) ? '<p style="color:black"></p>' : convocatoria.descripcion);
    $("#txtDescripcionConvocatoria").Editor("getText");
    if (convocatoria !== undefined) {
        $("#imagePreview").html(
            `<img width='100%' height='200' alt='user-picture' class='img-responsive center-box' src='${getHostFrontEnd()}adjuntos/convocatoria/${convocatoria.imagen}' />`
        );
    } else {
        $("#imagePreview").html(
            ""
        );
    }
    addViewArchivosPrevius();
}

function listaConvocatoria(beanPagination) {
    document.querySelector('#tbodyConvocatoria').innerHTML = '';
    document.querySelector('#titleManagerConvocatoria').innerHTML =
        'CUESTIONARIO PÚBLICO GENERAL';
    let row = "", contador = 1;
    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationConvocatoria'));
        row += `<tr>
        <td class="text-center" colspan="8">NO HAY CUESTIONARIOS</td>
        </tr>`;
        document.querySelector('#tbodyConvocatoria').innerHTML = row;
        return;
    }
    beanPagination.list.forEach((convocatoria) => {

        row += `<tr  idconvocatoria="${convocatoria.idconvocatoria}">
<td class="text-center">${contador++} </td>
<td class="text-center">${convocatoria.codigo} </td>
<td class="text-center">${(convocatoria.fecha).split(" ")[0].split("-")[2] + "/" + (convocatoria.fecha).split(" ")[0].split("-")[1] + "/" + (convocatoria.fecha).split(" ")[0].split("-")[0] + "<br>" + (convocatoria.fecha).split(" ")[1]
            } </td>
<td class="text-center">${convocatoria.descripcion} </td>
<td class="text-center f-weight-600 " style="
font-size: 22px;"> <p style="border: 3px solid #7030a0;">${convocatoria.cantidad}</p></td>
<td class="text-center"><img width='70px' alt='user-picture' class='img-responsive center-box' src='${getHostFrontEnd()}adjuntos/convocatoria/${convocatoria.imagen}'/></td>

<td class="text-center">
<button class="btn ${convocatoria.estado == 1 ? 'btn-success' : 'btn-warning'} estado-Convocatoria" >${convocatoria.estado == 1 ? 'SI' : 'NO'}</button>
</td>
<td class="text-center">
<button class="btn btn-info editar-Convocatoria" ><i class="zmdi zmdi-refresh"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-Convocatoria"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;

        // $('[data-toggle="tooltip"]').tooltip();
    });


    document.querySelector('#tbodyConvocatoria').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePageConvocatoria").value),
        document.querySelector("#pageConvocatoria"),
        $('#modalCargandoConvocatoria'),
        $('#paginationConvocatoria'));
    addEventsButtonsConvocatoria();


}

function addEventsButtonsConvocatoria() {
    document.querySelectorAll('.ver-preguntas').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            convocatoriaSelected = findByConvocatoria(
                btn.parentElement.parentElement.getAttribute('idconvocatoria')
            );
            if (convocatoriaSelected != undefined) {
                $("#ModalDetalle").modal("show");
                document.querySelector('#titleManagerDetalle').innerHTML = convocatoriaSelected.titulo.nombre;
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

    document.querySelectorAll('.editar-Convocatoria').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            document.querySelector('#listaDetalle').innerHTML = "";
            listDetalleConvocatoria = [];
            convocatoriaSelected = findByConvocatoria(
                btn.parentElement.parentElement.getAttribute('idconvocatoria')
            );

            if (convocatoriaSelected != undefined) {
                beanRequestConvocatoria.operation = 'detalle';
                beanRequestConvocatoria.type_request = 'GET';
                addConvocatoria(convocatoriaSelected);
                $('#modalCargandoConvocatoria').modal('show');
                $("#tituloModalManConvocatoria").html("EDITAR PREGUNTAS");
                $("#ventanaModalManConvocatoria").modal("show");

            } else {
                swal({
                    title: "",
                    text: "no se encuentra el cuestionario",
                    type: "warning",
                    timer: 1200,
                    showConfirmButton: false
                });
            }
        };
    });
    document.querySelectorAll('.estado-Convocatoria').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            document.querySelector('#listaDetalle').innerHTML = "";
            convocatoriaSelected = findByConvocatoria(
                btn.parentElement.parentElement.getAttribute('idconvocatoria')
            );

            if (convocatoriaSelected != undefined) {
                beanRequestConvocatoria.operation = 'estado';
                beanRequestConvocatoria.type_request = 'POST';
                $('#modalCargandoConvocatoria').modal('show');

            } else {
                swal({
                    title: "",
                    text: "no se encuentra el cuestionario",
                    type: "warning",
                    timer: 1200,
                    showConfirmButton: false
                });
            }
        };
    });
    document.querySelectorAll('.eliminar-Convocatoria').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            convocatoriaSelected = findByConvocatoria(
                btn.parentElement.parentElement.getAttribute('idconvocatoria')
            );

            if (convocatoriaSelected != undefined) {
                beanRequestConvocatoria.type_request = 'GET';
                beanRequestConvocatoria.operation = 'delete';
                $('#modalCargandoConvocatoria').modal('show');
            } else {
                swal({
                    title: "",
                    text: "no se encuentra el cuestionario",
                    type: "warning",
                    timer: 1200,
                    showConfirmButton: false
                });
            }
        };
    });
}

function findIndexConvocatoria(idbusqueda) {
    return beanPaginationConvocatoria.list.findIndex(
        (Convocatoria) => {
            if (Convocatoria.idconvocatoria == parseInt(idbusqueda))
                return Convocatoria;


        }
    );
}

function findByConvocatoria(idconvocatoria) {
    return beanPaginationConvocatoria.list.find(
        (Convocatoria) => {
            if (parseInt(idconvocatoria) == Convocatoria.idconvocatoria) {
                return Convocatoria;
            }


        }
    );
}

var validarFormularioConvocatoria = () => {
    if (document.querySelector("#txtCodigoConvocatoria").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Código o Nombre",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtFechaConvocatoria").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Nombre",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if ($("#txtDescripcionConvocatoria").Editor("getText") == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Instrucciones",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (listDetalleConvocatoria.length == 0) {
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

function toListConvocatoriaDetalle(beanPagination) {

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
            <label >Pregunta N${contador}</label>
            <div class="group-material">
              <textarea class="material-control w-100 descripcion-detalle-Convocatoria"  required="" iddetalle="${detalle.iddetalleConvocatoria}"rows="2">${detalle.descripcion}</textarea>
            </div>
          </div>
        </div>
      </div>
      <div class="col-3 col-sm-2" style="line-height: 8;">
        <button type="button" class="btn btn-danger eliminar-detalle" iddetalle="${detalle.iddetalleConvocatoria}">
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
    document.querySelectorAll('.descripcion-detalle-Convocatoria').forEach((btn) => {
        btn.onkeyup = () => {
            detalleSelected = findByDetalle(
                btn.getAttribute(
                    'iddetalle'
                )
            );
            if (detalleSelected == undefined) return false;
            eliminarDetalle(detalleSelected.iddetalleConvocatoria);
            listDetalleConvocatoria.push(
                new Detalle_Convocatoria(
                    detalleSelected.iddetalleConvocatoria,
                    btn.value,
                    0
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
            eliminarDetalle(detalleSelected.iddetalleConvocatoria);
            toListConvocatoriaDetalle(listDetalleConvocatoria);
        };
    });

};

var findByDetalle = (iddetalleConvocatoria) => {

    return listDetalleConvocatoria.find(
        (detalle) => {
            if (parseInt(iddetalleConvocatoria) == parseInt(detalle.iddetalleConvocatoria)) {
                return detalle;
            }


        }
    );
};

var eliminarDetalle = (idbusqueda) => {
    listDetalleConvocatoria.splice(findIndexDetalle(parseInt(idbusqueda)), 1);
};

function findIndexDetalle(idbusqueda) {
    return listDetalleConvocatoria.findIndex(
        (cargo) => {
            if (cargo.iddetalleConvocatoria == parseInt(idbusqueda))
                return cargo;


        }
    );
}

function addViewArchivosPrevius() {
    $("#txtImagenConvocatoria").change(function () {
        filePreview(this, "#imagePreview");
    });
}
function filePreview(input, imagen) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        imagen;
        reader.onload = function (e) {
            $(imagen).html(
                "<img width='100%' height='200' alt='user-picture' class='img-responsive center-box' src='" +
                e.target.result +
                "' />"
            );
        };
        reader.readAsDataURL(input.files[0]);
    }
}
