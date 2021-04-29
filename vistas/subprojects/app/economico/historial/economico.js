var beanPaginationEconomico;
var economicoSelected;
var beanRequestEconomico = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestEconomico.entity_api = 'economico';
    beanRequestEconomico.operation = 'paginate';
    beanRequestEconomico.type_request = 'GET';

    $('#sizePageEconomico').change(function () {
        beanRequestEconomico.type_request = 'GET';
        beanRequestEconomico.operation = 'paginate';
        $('#modalCargandoEconomico').modal('show');
    });


    $('#modalCargandoEconomico').modal('show');

    $("#modalCargandoEconomico").on('shown.bs.modal', function () {
        processAjaxEconomico();
    });
    $("#modalCargandoEconomico").on('hide.bs.modal', function () {
        beanRequestEconomico.type_request = 'GET';
        beanRequestEconomico.operation = 'paginate';
    });

    $("#ventanaModalManEconomico").on('hide.bs.modal', function () {
        beanRequestEconomico.type_request = 'GET';
        beanRequestEconomico.operation = 'paginate';
    });

    $("#formularioEconomico").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();
        beanRequestEconomico.type_request = 'POST';
        beanRequestEconomico.operation = 'update';
        if (validateFormEconomico()) {
            $('#modalCargandoEconomico').modal('show');
        }
    });


});

function processAjaxEconomico() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestEconomico.operation == 'update' ||
        beanRequestEconomico.operation == 'add'
    ) {

        json = {
            fecha: document.querySelector("#txtFechaEconomico").value,
            nombre: document.querySelector("#txtNombreEconomico").value,
            apellido: document.querySelector("#txtApellidoEconomico").value,
            telefono: document.querySelector("#txtTelefonoEconomico").value,
            pais: document.querySelector("#txtPaisEconomico").value,
            tipo: document.querySelector("#txtTipoEconomico").value,
            banco: document.querySelector("#txtNombreBancoEconomico").value,
            moneda: document.querySelector("#txtTipoMonedaEconomico").value,
            comision: document.querySelector("#txtComisionEconomico").value,
            precio: document.querySelector("#txtMontoEconomico").value


        };


    } else {
        form_data = null;
    }

    switch (beanRequestEconomico.operation) {
        case 'delete':
            parameters_pagination = '?id=' + economicoSelected.ideconomico;
            break;

        case 'update':
            json.ideconomico = economicoSelected.ideconomico;
            if (parseInt(document.querySelector("#txtTipoEconomico").value) == 2) {
                if (document.querySelector("#txtImagenVoucher").files.length !== 0) {
                    let dataFoto = $("#txtImagenVoucher").prop("files")[0];
                    form_data.append("txtImagenVoucher", dataFoto);
                }
            }

            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':
            if (parseInt(document.querySelector("#txtTipoEconomico").value) == 2) {
                let data = $("#txtImagenVoucher").prop("files")[0];
                form_data.append("txtImagenVoucher", data);
            } else {
                let data = $("#txtVideoEconomico").prop("files")[0];
                form_data.append("txtVideoEconomico", data);
            }

            form_data.append("class", JSON.stringify(json));
            break;

        default:

            parameters_pagination +=
                '?filtro=';
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pageEconomico").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePageEconomico").value.trim();
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestEconomico.entity_api + "/" + beanRequestEconomico.operation +
            parameters_pagination,
        type: beanRequestEconomico.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },

        data: form_data,
        cache: false,
        contentType: ((beanRequestEconomico.operation == 'update') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',
    }).done(function (beanCrudResponse) {

        $('#modalCargandoEconomico').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                if (beanRequestEconomico.operation == 'delete') {
                    eliminarlistEconomico(economicoSelected.ideconomico);
                    listaEconomico(beanPaginationEconomico);

                }


            } else {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationEconomico = beanCrudResponse.beanPagination;
            listaEconomico(beanPaginationEconomico);
        }
        if (beanCrudResponse.beanClass !== null) {
            if (beanRequestEconomico.operation == 'update') {
                updatelistEconomico(beanCrudResponse.beanClass);
                listaEconomico(beanPaginationEconomico);

            }
        }
        $('#ventanaModalManEconomico').modal('hide');
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoEconomico').modal("hide");
        showAlertErrorRequest();

    });

}

function addEconomico(economico = undefined) {
    //LIMPIAR LOS CAMPOS
    document.querySelector('#txtFechaEconomico').value = (economico == undefined) ? '' : (economico.fecha.split(" ")[0]) + "T" + (economico.fecha.split(" ")[1].split(":")[0] + ":" + economico.fecha.split(" ")[1].split(":")[1]);

    console.log((economico.fecha.split(" ")[0]) + "T" + (economico.fecha.split(" ")[1]));


    document.querySelector('#txtNombreEconomico').value = (economico == undefined) ? '' : economico.nombre;
    document.querySelector('#txtApellidoEconomico').value = (economico == undefined) ? '' : economico.apellido;

    document.querySelector('#txtTelefonoEconomico').value = (economico == undefined) ? '' : economico.telefono;
    document.querySelector('#txtPaisEconomico').value = (economico == undefined) ? '' : economico.pais;
    document.querySelector('#txtMontoEconomico').value = (economico == undefined) ? '' : economico.precio;
    document.querySelector('#txtComisionEconomico').value = (economico == undefined) ? '0' : economico.comision;
    document.querySelector('#txtNombreBancoEconomico').value = (economico == undefined) ? '0' : economico.banco;
    document.querySelector('#txtTipoMonedaEconomico').value = (economico == undefined) ? 'PEN' : economico.moneda;
    document.querySelector('#txtTipoEconomico').value = (economico == undefined) ? '1' : economico.tipo;

    if (economico !== undefined) {
        if (economico.tipo == 1) {
            $("#imagenVaucherPreview").html(
                ``
            );
            addClass(document.querySelector('#txtImagenVoucher').parentElement.parentElement, "d-none");
        } else {
            removeClass(document.querySelector('#txtImagenVoucher').parentElement.parentElement, "d-none");
            if (economico.voucher == "" || economico.voucher == null) {
                $("#imagenVaucherPreview").html(
                    ``
                );

            } else {

                $("#imagenVaucherPreview").html(
                    `<img  style="height:180px;width: 100%;"  alt='user-picture' class='img-responsive center-box ' src='${getHostFrontEnd() + "adjuntos/clientes/comprobante/" + economico.voucher}' />`
                );
            }
        }



    } else {

        $("#imagenVaucherPreview").html(
            `<img  style="height:180px;width: 100%;" alt='user-picture' class='img-responsive center-box' src='${getHostFrontEnd()}vistas/assets/img/framed.png' />`
        );
    }
    addViewArchivosPrevius();

}

function listaEconomico(beanPagination) {
    document.querySelector('#tbodyEconomico').innerHTML = '';
    document.querySelector('#titleManagerEconomico').innerHTML =
        '[ ' + beanPagination.countFilter + ' ] HISTORIAL ECONÓMICO';
    let row = "";

    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationEconomico'));
        row += `<tr>
        <td class="text-center" colspan="12">NO HAY HISTORIAL ECONÓMICO</td>
        </tr>`;

        document.querySelector('#tbodyEconomico').innerHTML += row;
        return;
    }

    document.querySelector('#tbodyEconomico').innerHTML += row;
    beanPagination.list.forEach((economico) => {
        row += `<tr  ideconomico="${economico.ideconomico}">
<td class="text-center">${economico.nombre + " " + economico.apellido}</td>
<td class="text-center">${economico.telefono}</td>
<td class="text-center">${economico.pais}</td>
<td class="text-center">${economico.banco}</td>
<td class="text-center">${economico.moneda}</td>
<td class="text-center">${economico.comision}</td>
<td class="text-center">${economico.precio}</td>

<td class="text-center">${parseFloat(economico.precio) + parseFloat(economico.comision)}</td>
<td class="text-center ">${(economico.voucher == null || economico.voucher == "") ? "SIN VOUCHER" : ("<img src='" + getHostFrontEnd() + "adjuntos/clientes/comprobante/" + economico.voucher + "' class='img-responsive center-box' style='width:50px;height:60px;'>")
            }</td >
    <td class="text-center">
        <button class="btn btn-${economico.tipo == 2 ? "info" : "warning"}">${economico.tipo == 2 ? "EFECTIVO" : "NIUBIZ"}</button></td >
<td class="text-center">${economico.fecha.split(" ")[0].split("-")[2] + "/" + economico.fecha.split(" ")[0].split("-")[1] + "/" + economico.fecha.split(" ")[0].split("-")[0] + " " + economico.fecha.split(" ")[1]}</td>
<td class="text-center">
<button class="btn btn-success editar-economico"><i class="zmdi zmdi-edit"></i></button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-economico"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr > `;

        // $('[data-toggle="tooltip"]').tooltip();
    });
    document.querySelector('#tbodyEconomico').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePageEconomico").value),
        document.querySelector("#pageEconomico"),
        $('#modalCargandoEconomico'),
        $('#paginationEconomico'));
    addEventsButtonsAdmin();


}

function addEventsButtonsAdmin() {


    document.querySelectorAll('.editar-economico').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            economicoSelected = findByEconomico(
                btn.parentElement.parentElement.getAttribute('ideconomico')
            );

            if (economicoSelected != undefined) {
                addEconomico(economicoSelected);
                $("#tituloModalManEconomico").html("EDITAR HISTORIAL ECONÓMICO");
                $("#ventanaModalManEconomico").modal("show");
                beanRequestEconomico.type_request = 'POST';
                beanRequestEconomico.operation = 'update';
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
    document.querySelectorAll('.eliminar-economico').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            economicoSelected = findByEconomico(
                btn.parentElement.parentElement.getAttribute('ideconomico')
            );

            if (economicoSelected != undefined) {
                beanRequestEconomico.type_request = 'GET';
                beanRequestEconomico.operation = 'delete';
                $('#modalCargandoEconomico').modal('show');
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
}


function addViewArchivosPrevius() {

    $("#txtImagenVoucher").change(function () {
        filePreview(this, "#imagenVaucherPreview");
    });
    $('#txtTipoEconomico').change(function () {
        if (document.querySelector('#txtTipoEconomico').value == 1) {
            addClass(document.querySelector('#txtImagenVoucher').parentElement.parentElement, "d-none");
        } else {
            removeClass(document.querySelector('#txtImagenVoucher').parentElement.parentElement, "d-none");
        }
    });
}
function filePreview(input, imagen) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        imagen;
        reader.onload = function (e) {
            $(imagen).html(
                "<img width='244' alt='user-picture' class='img-responsive center-box' src='" +
                e.target.result +
                "' />"
            );
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function findIndexEconomico(idbusqueda) {
    return beanPaginationEconomico.list.findIndex(
        (Economico) => {
            if (Economico.ideconomico == parseInt(idbusqueda))
                return Economico;


        }
    );
}

function findByEconomico(ideconomico) {
    return beanPaginationEconomico.list.find(
        (Economico) => {
            if (parseInt(ideconomico) == Economico.ideconomico) {
                return Economico;
            }


        }
    );
}
var validateFormEconomico = () => {
    if (document.querySelector("#txtNombreEconomico").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Nombres",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtApellidoEconomico").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Apellidos",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    let numero = numero_campo(
        document.querySelector('#txtTelefonoEconomico')

    );
    if (numero != undefined) {
        if (numero.value == '') {
            showAlertTopEnd('info', "Formato Incorrecto", 'Por favor ingrese ' + numero.labels[0].innerText);
        } else {
            showAlertTopEnd(
                'info', "Formato Incorrecto",
                'Por favor ingrese sólo números, ' + numero.labels[0].innerText
            );
        }

        return false;
    }
    if (document.querySelector("#txtPaisEconomico").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese País",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtPaisEconomico").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese País",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtFechaEconomico").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Fecha",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtTipoEconomico").value > 2) {
        swal({
            title: "Vacío",
            text: "Ingrese Medio de Pago",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtNombreBancoEconomico").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Nombre del Banco",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtTipoMonedaEconomico").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Moneda",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtComisionEconomico").value < 0) {
        swal({
            title: "Vacío",
            text: "Ingrese Comisión del Banco",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtMontoEconomico").value < 0) {
        swal({
            title: "Vacío",
            text: "Ingrese Monto",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (beanRequestEconomico.operation == 'update') {

        switch (parseInt(document.querySelector("#txtTipoEconomico").value)) {
            case 2:
                if (document.querySelector("#txtImagenVoucher").files.length > 0) {
                    if (!(document.querySelector("#txtImagenVoucher").files[0].type == "image/png" || document.querySelector("#txtImagenVoucher").files[0].type == "image/jpg" || document.querySelector("#txtImagenVoucher").files[0].type == "image/jpeg")) {
                        swal({
                            title: "Formato Incorrecto",
                            text: "Ingrese formato png, jpeg y jpg",
                            type: "warning",
                            timer: 1200,
                            showConfirmButton: false
                        });
                        return false;
                    }
                    //menor a   4 MB
                    if (document.querySelector("#txtImagenVoucher").files[0].size > (4 * 1024 * 1024)) {
                        swal({
                            title: "Tamaño excedido",
                            text: "el tamaño del archivo tiene que ser menor a 900 KB",
                            type: "warning",
                            timer: 1200,
                            showConfirmButton: false
                        });
                        return false;
                    }
                }


                break;
            default:

                break;

        }

    }

    return true;
}

function eliminarlistEconomico(idbusqueda) {
    beanPaginationEconomico.count_filter--;
    beanPaginationEconomico.list.splice(findIndexEconomico(parseInt(idbusqueda)), 1);
}
function updatelistEconomico(classBean) {
    beanPaginationEconomico.list.splice(findIndexEconomico(classBean.ideconomico), 1, classBean);
}
