var beanPaginationRecurso;
var recursoSelected;
var subtituloSelected;
var beanRequestRecurso = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestRecurso.entity_api = 'recursos';
    beanRequestRecurso.operation = 'alumno';
    beanRequestRecurso.type_request = 'GET';

    $('#modalCargandoRecurso').modal('show');

    $("#modalCargandoRecurso").on('shown.bs.modal', function () {
        processAjaxRecurso();
    });
    $("#ventanaModalManRecurso").on('hide.bs.modal', function () {
        beanRequestRecurso.type_request = 'GET';
        beanRequestRecurso.operation = 'alumno';
    });


});

function processAjaxRecurso() {

    let parameters_pagination = '';
    switch (beanRequestRecurso.operation) {
        default:

            parameters_pagination +=
                '?filtro=';
            parameters_pagination +=
                '&pagina=1';
            parameters_pagination +=
                '&registros=20';
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestRecurso.entity_api + "/" + beanRequestRecurso.operation +
            parameters_pagination,
        type: beanRequestRecurso.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: null,
        cache: false,
        contentType: 'application/json; charset=UTF-8',
        processData: false,
        dataType: 'json'
    }).done(function (beanCrudResponse) {
        $('#modalCargandoRecurso').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                $('#ventanaModalManRecurso').modal('hide');
            } else {

                showAlertTopEnd("info", beanCrudResponse.messageServer, "");
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationRecurso = beanCrudResponse.beanPagination;
            listaRecurso(beanPaginationRecurso);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoRecurso').modal("hide");
        showAlertErrorRequest();

    });

}

function addRecurso(recurso = undefined) {
    //LIMPIAR LOS CAMPOS
    document.querySelector('#txtNombreRecurso').value = (recurso == undefined) ? '' : recurso.nombre;
    document.querySelector('#txtDisponibidadRecurso').value = (recurso == undefined) ? '0' : recurso.disponible;

    subtituloSelected = (recurso == undefined) ? undefined : recurso.subTitulo;
    document.querySelector('#txtSubTituloRecurso').value = (recurso == undefined) ? '' : recurso.subtitulo.codigo + " - " + recurso.subtitulo.nombre;

    if (recurso !== undefined) {

        $("#imagePreview").html(
            `<img width='244' alt='user-picture' class='img-responsive center-box' src='${getHostFrontEnd()}adjuntos/recurso/IMAGENES/${recurso.imagen}' />`
        );

    } else {
        $("#imagePreview").html(
            ``
        );

    }
    addViewArchivosRecursoPrevius();

}

function listaRecurso(beanPagination) {
    document.querySelector('#sectionDetalles').classList.add("d-none");
    document.querySelector('#sectionRecursos').classList.remove("d-none");
    document.querySelector('#tbodyRecurso').innerHTML = '';
    document.querySelector('#titleManagerRecurso').innerHTML =
        'RECURSOS DISPONIBLES ';
    let row = "";
    if (beanPagination.list.length == 0) {
        row += `  <h5 class="text-center w-100">NO CUENTAS CON RECURSOS</h5>
        <div class="text-center w-100">  <a class="btn btn-bordered anim fadeInRight animated" role="button" href="index"
        style="visibility: visible;">INICIO</a></div>
       `;

        document.querySelector('#tbodyRecurso').innerHTML = row;
        return;
    }
    beanPagination.list.forEach((recurso) => {
        row += `

<figure class="effect-oscar detalle-recurso" idrecurso="${recurso.idrecurso}">
						<img  width="100%" height="100%" src="${getHostFrontEnd()}adjuntos/recurso/IMAGENES/${recurso.imagen}" alt="recurso.imagen"/>
						<figcaption>
							<h2>${recurso.nombre}</h2>
						</figcaption>			
					</figure>
`;
    });

    document.querySelector('#tbodyRecurso').innerHTML += row;

    addEventsButtonsRecurso();


}

function addEventsButtonsRecurso() {
    document.querySelectorAll('.detalle-recurso').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            recursoSelected = findByRecurso(
                btn.getAttribute('idrecurso')
            );

            if (recursoSelected != undefined) {

                $("#titleManagerDetalle").html('"' + recursoSelected.subtitulo.nombre + '"');
                $('#modalCargandoDetalle').modal('show');
            } else {
                showAlertTopEnd("info", "Vacío!", "No se encontró el recurso");
            }
        };
    });


}

function addViewArchivosRecursoPrevius() {

    $("#txtImagenRecurso").change(function () {
        filePreview(this, "#imagePreview");
    });
}


function findIndexRecurso(idbusqueda) {
    return beanPaginationRecurso.list.findIndex(
        (Recurso) => {
            if (Recurso.idrecurso == parseInt(idbusqueda))
                return Recurso;


        }
    );
}

function findByRecurso(idrecurso) {
    return beanPaginationRecurso.list.find(
        (Recurso) => {
            if (parseInt(idrecurso) == Recurso.idrecurso) {
                return Recurso;
            }


        }
    );
}

var validarDormularioVideo = () => {
    if (document.querySelector("#txtNombreRecurso").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Nombre",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtSubTituloRecurso").value == "") {
        swal({
            title: "Vacío",
            text: "Selecciona Subtitulo",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtDisponibidadRecurso").value == "") {
        swal({
            title: "Vacío",
            text: "Selecciona Disponibilidad de Archivo",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (beanRequestRecurso.operation == 'add') {
        if (document.querySelector("#txtImagenRecurso").files.length == 0) {
            swal({
                title: "Vacío",
                text: "Ingrese Imagen",
                type: "warning",
                timer: 1200,
                showConfirmButton: false
            });
            return false;
        }
        if (!(document.querySelector("#txtImagenRecurso").files[0].type == "image/png" || document.querySelector("#txtImagenRecurso").files[0].type == "image/jpg" || document.querySelector("#txtImagenRecurso").files[0].type == "image/jpeg")) {
            swal({
                title: "Formato Incorrecto",
                text: "Ingrese formato png, jpeg y jpg",
                type: "warning",
                timer: 1200,
                showConfirmButton: false
            });
            return false;
        }
        //menor a   900 kB
        if (document.querySelector("#txtImagenRecurso").files[0].size > (900 * 1024 * 1024)) {
            swal({
                title: "Tamaño excedido",
                text: "el tamaño del archivo tiene que ser menor a 900 KB",
                type: "warning",
                timer: 1200,
                showConfirmButton: false
            });
            return false;
        }

    } else {
        if (document.querySelector("#txtImagenRecurso").files.length != 0) {
            if (document.querySelector("#txtImagenRecurso").files.length == 0) {
                swal({
                    title: "Vacío",
                    text: "Ingrese Imagen",
                    type: "warning",
                    timer: 1200,
                    showConfirmButton: false
                });
                return false;
            }
            if (!(document.querySelector("#txtImagenRecurso").files[0].type == "image/png" || document.querySelector("#txtImagenRecurso").files[0].type == "image/jpg" || document.querySelector("#txtImagenRecurso").files[0].type == "image/jpeg")) {
                swal({
                    title: "Formato Incorrecto",
                    text: "Ingrese formato png, jpeg y jpg",
                    type: "warning",
                    timer: 1200,
                    showConfirmButton: false
                });
                return false;
            }
            //menor a   900 Kb
            if (document.querySelector("#txtImagenRecurso").files[0].size > (900 * 1024 * 1024)) {
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
    }

    return true;
}