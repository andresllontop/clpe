var beanPaginationRespuesta;
var respuestaSelected;
var testSelected;
var tituloSelected;
var contadordetallerespuesta = 0;
var beanRequestRespuesta = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestRespuesta.entity_api = 'respuestas';
    beanRequestRespuesta.operation = 'add';
    beanRequestRespuesta.type_request = 'POST';

    $("#modalCargandoRespuesta").on('shown.bs.modal', function () {
        processAjaxRespuesta();
    });

    $("#btnAbrirrespuesta").click(function () {
        beanRequestRespuesta.operation = 'add';
        beanRequestRespuesta.type_request = 'POST';
        if (beanPaginationLeccion != undefined) {
            listaPreguntasInternas(beanPaginationLeccion);
        }
    });


    $("#formularioRespuesta").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (validarFormularioRespuesta()) {
            $('#modalCargandoRespuesta').modal('show');
        }
    });

});

function processAjaxRespuesta() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestRespuesta.operation == 'updatestado'
    ) {


    } else if (
        beanRequestRespuesta.operation == 'add'
    ) {

        json = {
            codigo: user_session.codigo,
            test: testSelected.idtest,
            tipo: testSelected.tipo,
            titulo: (testSelected.tipo == 1 ? tituloSelected.codigo : subtituloSelected.codigo),
            list: listDetalleRespuesta
        };


    } else {
        form_data = null;
    }

    switch (beanRequestRespuesta.operation) {

        case 'add':
            form_data.append("class", JSON.stringify(json));
            break;
        case 'updatestado':
            json = {
                codigo: user_session.codigo,
                titulo: (testSelected.tipo == 1 ? tituloSelected.codigo : subtituloSelected.codigo),
                estado: 1
            };

            form_data.append("class", JSON.stringify(json));
            break;

        default:

            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestRespuesta.entity_api + "/" + beanRequestRespuesta.operation +
            parameters_pagination,
        type: beanRequestRespuesta.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },
        data: form_data,
        cache: false,
        contentType: ((beanRequestRespuesta.operation == 'add' || beanRequestRespuesta.operation == 'updatestado') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json'
    }).done(function (beanCrudResponse) {
        listDetalleRespuesta = [];
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                $('#modalCargandoRespuesta').modal('hide');

                document.querySelector("#modalSwallTitulo").innerText = "TAREA REGISTRADA!";
                document.querySelector("#modalSwallContenido").innerText = "Haga click en aceptar para pasar a la siguiente lección.";
                $('#modalSwallMensaje').modal('show');

                //AGREGANDO EVENTO CLICK
                document.querySelector('#modalSwallAceptar').onclick = function () {
                    $('#modalSwallMensaje').modal('hide');
                    beanRequestRespuesta.operation = 'updatestado';
                    beanRequestRespuesta.type_request = 'POST';
                    $('#modalCargandoRespuesta').modal('show');

                };
                document.querySelector('#modalSwallCancelar').onclick = function () {
                    $('#modalSwallMensaje').modal('hide');
                    swal("Seleccionaste Cancelar", "No avanzaste a la siguiente Lección.");
                };
            } else if (beanCrudResponse.messageServer.toLowerCase() == 'siguiente') {


                $('#modalCargandoRespuesta').modal('hide');
                window.location.reload();
                /*
                                document.querySelector('#sectionLeccion').classList.add("d-none");
                                document.querySelector('#sectionPreguntas').classList.add("d-none");
                                document.querySelector('#sectionMensaje').classList.remove("d-none");
                                document.querySelector('#titleManRespuesta').innerHTML = "";
                                document.querySelector('#listaPreguntas').innerHTML = "";
                */

            } else if (beanCrudResponse.messageServer.toLowerCase() == 'fin') {
                $('#modalCargandoRespuesta').modal('hide');
                //CURSO CULMINADO
                document.querySelector('#sectionLeccion').classList.add("d-none");
                document.querySelector('#sectionPreguntas').classList.add("d-none");
                document.querySelector('#sectionMensaje').classList.remove("d-none");
                document.querySelector('#titleManRespuesta').innerHTML = "";
                document.querySelector('#listaPreguntas').innerHTML = "";
                swal({
                    title: "TAREA REGISTRADA, FIN DEL CURSO",
                    text: "Haga click en aceptar.",
                    confirmButtonColor: "#2ca441",
                    confirmButtonText: "Aceptar",
                    imageUrl: getHostFrontEnd() + 'vistas/assets/img/enviada-carta.png',
                    closeOnConfirm: false
                },
                    function () {
                        swal.close();

                    });
            } else {
                $('#modalCargandoRespuesta').modal('hide');
                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        } else {
            $('#modalCargandoRespuesta').modal('hide');
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoRespuesta').modal("hide");
        showAlertErrorRequest();
        listDetalleRespuesta = [];

    });

}

function listaPreguntasInternas(beanPagination) {
    document.querySelector('#listaPreguntas').innerHTML = "";
    let row = "", dato = "", contador = 0, contadorRespuesta = 0;
    if (beanPagination.list.length == 0) {
        return;
    }
    console.log(beanPagination.list[0].test);
    document.querySelector('#sectionLeccion').classList.add("d-none");
    document.querySelector('#sectionPreguntas').classList.remove("d-none");
    document.querySelector('#sectionMensaje').classList.add("d-none");
    document.querySelector('#titleManRespuesta').innerHTML = `
    <h3 class="text-center">`+ beanPagination.list[0].test.nombre + `</h3>
    <h4 class="text-center" style="font-size: 24px;">Capítulo :
        <small style="font-size: 22px;font-weight: 500;" id="titleCapitulo"></small>
    </h4>
    `;
    beanPagination.list.forEach((detalletest) => {
        testSelected = detalletest.test;
        subtituloSelected = detalletest.subtitulo;
        contador++;
        contadorRespuesta++;
        if ((contadorRespuesta + "").length == 1) {
            contadorRespuesta = "0" + contadorRespuesta;
        }
        if (dato == "") {
            dato = detalletest.subtitulo.codigo;
            row += `
            <h4 class="anim fadeIn text-primary my-1" data-wow-delay="0.24s">
           <small style="font-size: 18px;font-weight: 500;"> Subtítulo :  ${detalletest.subtitulo.descripcion}</small>
        </h4>
            `;
            row += `
            <span class="input-group anim fadeIn" data-wow-delay="0.30s">
                <h5 style="font-weight: 500;">${"PREGUNTA N°" + contadorRespuesta + ": " + detalletest.descripcion}</h5>
                <textarea  data-pregunta="${detalletest.descripcion}" data-subtitulo="${detalletest.subtitulo.codigo}"  data-test="${detalletest.iddetalletest}" id="respuesta${contador}" class="lg" 
                    placeholder="Escribe ... " style="height: 100px;font-size: 16px;"></textarea>
            </span>
                `;
        } else {
            if (dato == detalletest.subtitulo.codigo) {
                row += `
                <span class="input-group anim fadeIn" data-wow-delay="0.30s">
                    <h5 style="font-weight: 500;">${"PREGUNTA N°" + contadorRespuesta + ": " + detalletest.descripcion}</h5>
                    <textarea  data-pregunta="${detalletest.descripcion}" data-subtitulo="${detalletest.subtitulo.codigo}" data-test="${detalletest.iddetalletest}"  id="respuesta${contador}" class="lg" 
                        placeholder="Escribe ... " style="height: 100px;font-size: 16px;"></textarea>
                </span>
                    `;
            } else {
                dato = detalletest.subtitulo.codigo;

                row += `
                <h4 class="anim fadeIn text-primary my-1" data-wow-delay="0.24s"><small style="font-size: 18px;font-weight: 500;">Subtítulo : ${detalletest.subtitulo.descripcion}</small>
            </h4>
                <span class="input-group anim fadeIn" data-wow-delay="0.30s">
                    <h5 style="font-weight: 500;">${"PREGUNTA N°" + contadorRespuesta + ": " + detalletest.descripcion}</h5>
                    <textarea  data-pregunta="${detalletest.descripcion}" data-subtitulo="${detalletest.subtitulo.codigo}" data-test="${detalletest.iddetalletest}"  id="respuesta${contador}" class="lg" 
                        placeholder="Escribe ... " style="height: 100px;font-size: 16px;"></textarea>
                </span>
                    `;
            }

        }
        document.querySelector('#descripcionTest').innerHTML = '<i class="fa fa-quote-left"></i>' + detalletest.test.descripcion;
        document.querySelector('#titleCapitulo').innerHTML = detalletest.subtitulo.titulo.nombre;
    });


    document.querySelector('#listaPreguntas').innerHTML = row;

}

function listaPreguntasGeneral(beanPagination) {
    document.querySelector('#listaPreguntas').innerHTML = "";
    let row = "", dato = "", contador = 0, contadorRespuesta = 0;
    if (beanPagination.list.length == 0) {
        return;
    }
    document.querySelector('#titleManRespuesta').innerHTML = `
    <h4 class="text-center" style="font-size: 24px;">Capítulo :
        <small style="font-size: 22px;font-weight: 500;" id="titleCapitulo"></small>
    </h4>
    `;
    document.querySelector('#sectionLeccion').classList.add("d-none");
    document.querySelector('#sectionPreguntas').classList.remove("d-none");
    document.querySelector('#sectionMensaje').classList.add("d-none");

    beanPagination.list.forEach((detalletest) => {
        contadorRespuesta++;
        if ((contadorRespuesta + "").length == 1) {
            contadorRespuesta = "0" + contadorRespuesta;
        }
        testSelected = detalletest.test;
        contador++;
        if (dato == "") {
            dato = detalletest.subtitulo.codigo;
            row += `
            <h4 class="anim fadeIn text-primary my-1" style="font-size: 18px;font-weight: 500;" data-wow-delay="0.24s">
            Subtítulo : <small style="font-size: 18px;font-weight: 500;">${detalletest.subtitulo.descripcion}</small>
        </h4>
            `;
            row += `
            <span class="input-group anim fadeIn" data-wow-delay="0.30s">
                <h5 style="font-weight: 500;">${"PREGUNTA N°" + contadorRespuesta + ": " + detalletest.descripcion}</h5>
                <textarea  data-subtitulo="${detalletest.subtitulo.codigo}" data-pregunta="${detalletest.descripcion}" data-test="${detalletest.iddetalletest}" id="respuesta${contador}" class="lg" 
                    placeholder="Escribe ... " style="height: 100px;font-size: 16px;"></textarea>
            </span>
                `;
        } else {
            if (dato == detalletest.subtitulo.codigo) {
                row += `
                <span class="input-group anim fadeIn" data-wow-delay="0.30s">
                    <h5 style="font-weight: 500;">${"PREGUNTA N°" + contadorRespuesta + ": " + detalletest.descripcion}</h5>
                    <textarea  data-subtitulo="${detalletest.subtitulo.codigo}" data-pregunta="${detalletest.descripcion}" data-test="${detalletest.iddetalletest}"  id="respuesta${contador}" class="lg" 
                        placeholder="Escribe ... " style="height: 100px;font-size: 16px;"></textarea>
                </span>
                    `;
            } else {
                dato = detalletest.subtitulo.codigo;

                row += `
                <h4 class="anim fadeIn text-primary"  style="font-size: 18px;font-weight: 500;"  data-wow-delay="0.24s">
                Subtítulo :<small style="font-size: 18px;font-weight: 500;">${detalletest.subtitulo.descripcion}</small>
            </h4>
                <span class="input-group anim fadeIn" data-wow-delay="0.30s">
                    <h5 style="font-weight: 500;">${"PREGUNTA N°" + contadorRespuesta + ": " + detalletest.descripcion}</h5>
                    <textarea  data-subtitulo="${detalletest.subtitulo.codigo}" data-pregunta="${detalletest.descripcion}" data-test="${detalletest.iddetalletest}"  id="respuesta${contador}" class="lg" 
                        placeholder="Escribe ... " style="height: 100px;font-size: 16px;"></textarea>
                </span>
                    `;
            }

        }
        document.querySelector('#descripcionTest').innerHTML = '<i class="fa fa-quote-left"></i>' + detalletest.test.descripcion;
        document.querySelector('#titleCapitulo').innerHTML = detalletest.subtitulo.titulo.nombre;
    });


    document.querySelector('#listaPreguntas').innerHTML = row;

}

function findByRespuesta(idrespuesta) {
    return beanPaginationRespuesta.list.find(
        (Respuesta) => {
            if (parseInt(idrespuesta) == Respuesta.idrespuesta) {
                return Respuesta;
            }


        }
    );
}

var validarFormularioRespuesta = () => {

    for (let index = 1; index <= beanPaginationLeccion.list.length; index++) {
        if (document.querySelector("#respuesta" + index).value == "") {
            swal({
                title: "Vacío",
                text: "Ingrese Respuesta",
                type: "info",
                timer: 1400,
                showConfirmButton: false
            });
            listDetalleRespuesta = [];
            return false;
        } else {
            tituloSelected = { codigo: document.querySelector("#respuesta" + index).dataset.subtitulo.split(".")[0] + "." + document.querySelector("#respuesta" + index).dataset.subtitulo.split(".")[1] + "." + document.querySelector("#respuesta" + index).dataset.subtitulo.split(".")[2] };
            listDetalleRespuesta.push(
                new Detalle_Respuesta(
                    contadordetallerespuesta++,
                    document.querySelector("#respuesta" + index).value,
                    document.querySelector("#respuesta" + index).dataset.pregunta,
                    document.querySelector("#respuesta" + index).dataset.subtitulo,
                    document.querySelector("#respuesta" + index).dataset.test,
                    testSelected.tipo

                )
            );
        }

    }




    return true;
}


