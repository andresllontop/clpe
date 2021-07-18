var beanPaginationSocial;
var socialSelected;
var beanRequestSocial = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestSocial.entity_api = 'socials';
    beanRequestSocial.operation = 'paginate';
    beanRequestSocial.type_request = 'GET';

    $('#sizePageSocial').change(function () {
        beanRequestSocial.type_request = 'GET';
        beanRequestSocial.operation = 'paginate';
        $('#modalCargandoSocial').modal('show');
    });

    $('#modalCargandoSocial').modal('show');

    $("#modalCargandoSocial").on('shown.bs.modal', function () {
        processAjaxSocial();
    });

    $("#ventanaModalManSocial").on('hide.bs.modal', function () {
        beanRequestSocial.type_request = 'GET';
        beanRequestSocial.operation = 'paginate';
    });



    $("#btnAbrirbook").click(function () {
        beanRequestSocial.operation = 'add';
        beanRequestSocial.type_request = 'POST';
        $("#imagePreview").html("");
        $("#tituloModalManSocial").html("REGISTRAR PUBLICIDAD");
        addSocial();
        $("#ventanaModalManSocial").modal("show");


    }); $("#txtDescripcionSocial").Editor();
    $("#formularioSocial").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (validateFormSocial()) {
            $('#modalCargandoSocial').modal('show');
        }
    });
    document.querySelector('#txtTipoSocial').onchange = function (e) {
        if (e.target.value == 1) {
            addClass(document.querySelector("#div-imagen-fondo"), "d-none");
            document.querySelector("#tipoArchivo").innerHTML = ` <div id="imagePreview" class="text-center"></div>
            <input id="txtImagenSocial" type="file" accept="image/png, image/jpeg, image/jpg"
              class="material-control tooltips-general input-check-user" placeholder="Selecciona Imagen"
              data-toggle="tooltip" data-placement="top" title="" data-original-title="Selecciona la Imagen">
            <span class="highlight"></span>
            <span class="bar"></span>
            <label>Selecciona Imagen</label>
            <small>Tamaño Máximo Permitido: 4 MB</small>
            <br>
            <small>Formatos Permitido:JPG, PNG, JPEG</small>`;
        } else if (e.target.value == 2) {
            removeClass(document.querySelector("#div-imagen-fondo"), "d-none");
            document.querySelector("#tipoArchivo").innerHTML = ` <div id="videoPreview" class="text-center"></div>
            <input id="txtVideoSocial" type="file" accept="video/mp4"
              class="material-control tooltips-general input-check-user" placeholder="Selecciona Video"
              data-toggle="tooltip" data-placement="top" title="" data-original-title="Selecciona la Video">
            <span class="highlight"></span>
            <span class="bar"></span>
            <label>Selecciona Video</label>
            <small>Tamaño Máximo Permitido: 500 MB</small>
            <br>
            <small>Formatos Permitido:MP4</small>`;
        }
        addViewArchivosPrevius();
    };

});

function processAjaxSocial() {
    let form_data = new FormData(), parametroCurso = "";

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestSocial.operation == 'update' ||
        beanRequestSocial.operation == 'add'
    ) {

        let arrayValue = $('#txtCursos').val();
        for (let index = 0; index < arrayValue.length; index++) {
            parametroCurso += arrayValue[index] + "-";

        }
        if (parametroCurso.substring(parametroCurso.length - 1) == '-') {
            parametroCurso = parametroCurso.substring(0, parametroCurso.length - 1);
        }
        json = {
            titulo: document.querySelector("#txtTituloSocial").value,
            frase_curso: document.querySelector("#txtFraseCursoSocial").value,
            prase_testimonio: document.querySelector("#txtFraseTestimonioSocial").value,
            descripcion: $("#txtDescripcionSocial").Editor("getText"),
            tipo_archivo: document.querySelector("#txtTipoSocial").value,
            parametro_curso: parametroCurso,

        };


    } else {
        form_data = null;
    }

    switch (beanRequestSocial.operation) {
        case 'delete':
            parameters_pagination = '?id=' + socialSelected.idsocial;
            break;

        case 'update':
            json.idsocial = socialSelected.idsocial;
            if (parseInt(document.querySelector("#txtTipoSocial").value) == 1) {
                if (document.querySelector("#txtImagenSocial").files.length !== 0) {
                    let dataFoto = $("#txtImagenSocial").prop("files")[0];
                    form_data.append("txtImagenSocial", dataFoto);
                }
            } else {
                if (document.querySelector("#txtVideoSocial").files.length !== 0) {
                    let dataPortada = $("#txtVideoSocial").prop("files")[0];
                    form_data.append("txtVideoSocial", dataPortada);
                }
                if (document.querySelector("#txtImagenFondoSocial").files.length !== 0) {
                    let dataPresentacion = $("#txtImagenFondoSocial").prop("files")[0];
                    form_data.append("txtImagenFondoSocial", dataPresentacion);
                }
            }





            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':
            let dataFoto, dataPortada, dataPresentacion;
            if (parseInt(document.querySelector("#txtTipoSocial").value) == 1) {
                dataFoto = $("#txtImagenSocial").prop("files")[0];
                form_data.append("txtImagenSocial", dataFoto);

            } else {
                dataPortada = $("#txtVideoSocial").prop("files")[0];
                form_data.append("txtVideoSocial", dataPortada);

                dataPresentacion = $("#txtImagenFondoSocial").prop("files")[0];
                form_data.append("txtImagenFondoSocial", dataPresentacion);
            }

            form_data.append("class", JSON.stringify(json));
            break;

        default:

            parameters_pagination +=
                '?filtro=';
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pageSocial").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePageSocial").value.trim();
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestSocial.entity_api + "/" + beanRequestSocial.operation +
            parameters_pagination,
        type: beanRequestSocial.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },

        data: form_data,
        cache: false,
        contentType: ((beanRequestSocial.operation == 'update' || beanRequestSocial.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',
    }).done(function (beanCrudResponse) {

        $('#modalCargandoSocial').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                document.querySelector("#pageSocial").value = 1;
                document.querySelector("#sizePageSocial").value = 20;
                $('#ventanaModalManSocial').modal('hide');
            } else {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationSocial = beanCrudResponse.beanPagination;
            listaSocial(beanPaginationSocial);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoSocial').modal("hide");
        showAlertErrorRequest();

    });

}

function addSocial(social = undefined) {
    //LIMPIAR LOS CAMPOS

    document.querySelector('#txtTituloSocial').value = (social == undefined) ? '' : social.titulo;
    document.querySelector('#txtTipoSocial').value = (social == undefined) ? '1' : social.tipoArchivo;
    $("#txtDescripcionSocial").Editor("setText", (social == undefined) ? '' : social.descripcion);
    $("#txtDescripcionSocial").Editor("getText");
    document.querySelector('#txtFraseTestimonioSocial').value = (social == undefined) ? '' : social.fraseTestimonio;
    document.querySelector('#txtFraseCursoSocial').value = (social == undefined) ? '' : social.fraseCurso;
    document.querySelector('#txtCursos').value = (social == undefined) ? '' : social.parametroCurso;

    addClass(document.querySelector("#div-imagen-fondo"), "d-none");
    if (social != undefined) {
        if (parseInt(social.tipoArchivo) == 1) {
            addClass(document.querySelector("#div-imagen-fondo"), "d-none");
            document.querySelector("#tipoArchivo").innerHTML = ` <div id="imagePreview" class="text-center"><img style="width:50%" alt="user-picture" class="img-responsive center-box" src="${getHostFrontEnd()}adjuntos/social/img/${social.archivo}"/></div>
            <input id="txtImagenSocial" type="file" accept="image/png, image/jpeg, image/jpg"
              class="material-control tooltips-general input-check-user" placeholder="Selecciona Imagen"
              data-toggle="tooltip" data-placement="top" title="" data-original-title="Selecciona la Imagen">
            <span class="highlight"></span>
            <span class="bar"></span>
            <label>Selecciona Imagen</label>
            <small>Tamaño Máximo Permitido: 4 MB</small>
            <br>
            <small>Formatos Permitido:JPG, PNG, JPEG</small>`;
        } else if (parseInt(social.tipoArchivo) == 2) {
            removeClass(document.querySelector("#div-imagen-fondo"), "d-none");
            document.querySelector("#tipoArchivo").innerHTML = `<div id="videoPreview" class="text-center"><video width='50%' alt='user-picture' class='img-responsive center-box' controls ><source src='${getHostFrontEnd()}adjuntos/social/video/${social.archivo}' type='video/mp4'></video></div>
            <input id="txtVideoSocial" type="file" accept="video/mp4"
              class="material-control tooltips-general input-check-user" placeholder="Selecciona Video"
              data-toggle="tooltip" data-placement="top" title="" data-original-title="Selecciona la Video">
            <span class="highlight"></span>
            <span class="bar"></span>
            <label>Selecciona Video</label>
            <small>Tamaño Máximo Permitido: 17 MB</small>
            <br>
            <small>Formatos Permitido:MP4</small>`;
            document.querySelector("#imagenFondoPreview").innerHTML = "<img width='50%' alt='user-picture' class='img-responsive center-box' src='" +
                getHostFrontEnd() + 'adjuntos/social/img/' + social.imagenFondo +
                "' />";
        }
    } else {
        document.querySelector("#tipoArchivo").innerHTML = ` <div id="imagePreview" class="text-center"></div>
        <input id="txtImagenSocial" type="file" accept="image/png, image/jpeg, image/jpg"
          class="material-control tooltips-general input-check-user" placeholder="Selecciona Imagen"
          data-toggle="tooltip" data-placement="top" title="" data-original-title="Selecciona la Imagen">
        <span class="highlight"></span>
        <span class="bar"></span>
        <label>Selecciona Imagen</label>
        <small>Tamaño Máximo Permitido: 4 MB</small>
        <br>
        <small>Formatos Permitido:JPG, PNG, JPEG</small>`;
    }
    addViewArchivosPrevius();
}

function listaSocial(beanPagination) {
    document.querySelector('#tbodySocial').innerHTML = '';
    document.querySelector('#titleManagerSocial').innerHTML =
        '[ ' + beanPagination.countFilter + ' ] PUBLICIDAD';
    let row = "";

    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationSocial'));
        row += `<tr>
        <td class="text-center" colspan="6">NO HAY PUBLICIDAD</td>
        </tr>`;

        document.querySelector('#tbodySocial').innerHTML += row;
        return;
    }

    document.querySelector('#tbodySocial').innerHTML += row;
    beanPagination.list.forEach((social) => {

        row += `<tr  idsocial="${social.idsocial}">
<td class="text-center">${social.titulo}</td>
<td class="text-center">${social.descripcion}</td>
<td class="text-center">${social.fraseCurso}</td>
<td class="text-center">${social.fraseTestimonio}</td>
<td  class="text-center">`;
        if (parseInt(social.tipoArchivo) == 1) {
            row += `
    <img  
      src="${getHostFrontEnd()}adjuntos/social/img/${social.archivo}"
      alt="${social.imagen}"
      class="img-responsive center-box"style="width:200px;"
      /></td><td  class="text-center">`;

        } else if (parseInt(social.tipoArchivo) == 2) {
            row += `<video controls style="width:200px;" src="${getHostFrontEnd()}adjuntos/social/video/${social.archivo}"></video></td> <td  class="text-center">`;

        }
        if (social.imagenFondo != null) {
            row += ` <img  
            src="${getHostFrontEnd()}adjuntos/social/img/${social.imagenFondo}"
            alt="${social.imagenFondo}"
            class="img-responsive center-box"style="width:200px;"
            />`;
        }
        row += `
  </td>
<td class="text-center">
<button class="btn btn-info editar-social" ><i class="zmdi zmdi-refresh"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-social"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;

        // $('[data-toggle="tooltip"]').tooltip();
    });
    document.querySelector('#tbodySocial').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePageSocial").value),
        document.querySelector("#pageSocial"),
        $('#modalCargandoSocial'),
        $('#paginationSocial'));
    addEventsButtonsSocial();


}
function addEventsButtonsSocial() {


    document.querySelectorAll('.editar-social').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            socialSelected = findBySocial(
                btn.parentElement.parentElement.getAttribute('idsocial')
            );

            if (socialSelected != undefined) {
                addSocial(socialSelected);
                $("#tituloModalManSocial").html("EDITAR PUBLICIDAD");
                $("#ventanaModalManSocial").modal("show");
                beanRequestSocial.type_request = 'POST';
                beanRequestSocial.operation = 'update';
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
    document.querySelectorAll('.eliminar-social').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            socialSelected = findBySocial(
                btn.parentElement.parentElement.getAttribute('idsocial')
            );

            if (socialSelected != undefined) {
                beanRequestSocial.type_request = 'GET';
                beanRequestSocial.operation = 'delete';
                $('#modalCargandoSocial').modal('show');
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

    $("#txtImagenSocial").change(function () {
        filePreview(this, "#imagePreview");
    });
    $("#txtImagenFondoSocial").change(function () {
        filePreview(this, "#imagenFondoPreview");
    });
    $("#txtVideoSocial").change(function () {
        videoPreview(this, "#videoPreview");
    });
}
function filePreview(input, imagen) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        imagen;
        reader.onload = function (e) {
            $(imagen).html(
                "<img width='50%' alt='user-picture' class='img-responsive center-box' src='" +
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
                "<video width='50%' alt='user-picture' class='img-responsive center-box' controls ><source src='" +
                e.target.result +
                "' type='video/mp4'></video>"
            );
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function findIndexSocial(idbusqueda) {
    return beanPaginationSocial.list.findIndex(
        (Social) => {
            if (Social.idsocial == parseInt(idbusqueda))
                return Social;


        }
    );
}

function findBySocial(idsocial) {
    return beanPaginationSocial.list.find(
        (Social) => {
            if (parseInt(idsocial) == Social.idsocial) {
                return Social;
            }


        }
    );
}
var validateFormSocial = () => {
    if (document.querySelector("#txtTituloSocial").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese titulo",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }

    if (document.querySelector("#txtFraseCursoSocial").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Frase Curso",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtFraseTestimonioSocial").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Frase Testimonio",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }

    if (document.querySelector("#txtTipoSocial").value == 0) {
        swal({
            title: "Vacío",
            text: "Selecciona Tipo",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (beanRequestSocial.operation == 'add') {

        if (document.querySelector("#txtTipoSocial").value == 1) {
            if (document.querySelector("#txtImagenSocial").files.length == 0) {
                swal({
                    title: "Vacío",
                    text: "Ingrese Imagen",
                    type: "warning",
                    timer: 1200,
                    showConfirmButton: false
                });
                return false;
            }
            if (!(document.querySelector("#txtImagenSocial").files[0].type == "image/png" || document.querySelector("#txtImagenSocial").files[0].type == "image/jpg" || document.querySelector("#txtImagenSocial").files[0].type == "image/jpeg")) {
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
            if (document.querySelector("#txtImagenSocial").files[0].size > (4 * 1024 * 1024)) {
                swal({
                    title: "Tamaño excedido",
                    text: "el tamaño del archivo tiene que ser menor a 4 MB",
                    type: "warning",
                    timer: 1200,
                    showConfirmButton: false
                });
                return false;
            }
        } else {
            //FONDO
            if (document.querySelector("#txtImagenFondoSocial").files.length == 0) {
                swal({
                    title: "Vacío",
                    text: "Ingrese portada",
                    type: "warning",
                    timer: 1200,
                    showConfirmButton: false
                });
                return false;
            }
            if (!(document.querySelector("#txtImagenFondoSocial").files[0].type == "image/png" || document.querySelector("#txtImagenFondoSocial").files[0].type == "image/jpg" || document.querySelector("#txtImagenFondoSocial").files[0].type == "image/jpeg")) {
                swal({
                    title: "Formato Incorrecto",
                    text: "Ingrese formato png, jpeg y jpg en la portada",
                    type: "warning",
                    timer: 1200,
                    showConfirmButton: false
                });
                return false;
            }
            //menor a   4 MB
            if (document.querySelector("#txtImagenFondoSocial").files[0].size > (4 * 1024 * 1024)) {
                swal({
                    title: "Tamaño excedido",
                    text: "el tamaño del archivo tiene que ser menor a 4 MB en la portada",
                    type: "warning",
                    timer: 1200,
                    showConfirmButton: false
                });
                return false;
            }
            //VIDEO
            if (document.querySelector("#txtVideoSocial").files.length == 0) {
                swal({
                    title: "Vacío",
                    text: "Ingrese Video",
                    type: "warning",
                    timer: 1200,
                    showConfirmButton: false
                });
                return false;
            }
            if (!(document.querySelector("#txtVideoSocial").files[0].type == "video/mp4")) {
                swal({
                    title: "Formato Incorrecto",
                    text: "Ingrese formato mp4 en el video",
                    type: "warning",
                    timer: 1200,
                    showConfirmButton: false
                });
                return false;
            }
            //menor a   17 MB
            if (document.querySelector("#txtVideoSocial").files[0].size > (17 * 1024 * 1024)) {
                swal({
                    title: "Tamaño excedido",
                    text: "el tamaño del archivo tiene que ser menor a 17 MB en la presentación",
                    type: "warning",
                    timer: 1200,
                    showConfirmButton: false
                });
                return false;
            }
        }








    } else {
        if (document.querySelector("#txtTipoSocial").value == 1) {
            if (document.querySelector("#txtImagenSocial").files.length != 0) {
                if (document.querySelector("#txtImagenSocial").files.length == 0) {
                    swal({
                        title: "Vacío",
                        text: "Ingrese Imagen",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }
                if (!(document.querySelector("#txtImagenSocial").files[0].type == "image/png" || document.querySelector("#txtImagenSocial").files[0].type == "image/jpg" || document.querySelector("#txtImagenSocial").files[0].type == "image/jpeg")) {
                    swal({
                        title: "Formato Incorrecto",
                        text: "Ingrese formato png, jpeg y jpg",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }
                //menor a   4 mb
                if (document.querySelector("#txtImagenSocial").files[0].size > (4 * 1024 * 1024)) {
                    swal({
                        title: "Tamaño excedido",
                        text: "el tamaño del archivo tiene que ser menor a 4 MB",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }
            }
        } else {
            if (document.querySelector("#txtImagenFondoSocial").files.length != 0) {
                if (document.querySelector("#txtImagenFondoSocial").files.length == 0) {
                    swal({
                        title: "Vacío",
                        text: "Ingrese portada",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }
                if (!(document.querySelector("#txtImagenFondoSocial").files[0].type == "image/png" || document.querySelector("#txtImagenFondoSocial").files[0].type == "image/jpg" || document.querySelector("#txtImagenFondoSocial").files[0].type == "image/jpeg")) {
                    swal({
                        title: "Formato Incorrecto",
                        text: "Ingrese formato png, jpeg y jpg en la portada",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }
                //menor a   4 mb
                if (document.querySelector("#txtImagenFondoSocial").files[0].size > (4 * 1024 * 1024)) {
                    swal({
                        title: "Tamaño excedido",
                        text: "el tamaño de la portada tiene que ser menor a 4 MB",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }
            }
            if (document.querySelector("#txtVideoSocial").files.length != 0) {
                if (document.querySelector("#txtVideoSocial").files.length == 0) {
                    swal({
                        title: "Vacío",
                        text: "Ingrese VIDEO",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }
                if (!(document.querySelector("#txtVideoSocial").files[0].type == "video/mp4")) {
                    swal({
                        title: "Formato Incorrecto",
                        text: "Ingrese formato png, jpeg y jpg en el VIDEO",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }
                //menor a   17 mb
                if (document.querySelector("#txtVideoSocial").files[0].size > (17 * 1024 * 1024)) {
                    swal({
                        title: "Tamaño excedido",
                        text: "el tamaño deL VIDEO tiene que ser menor a 17 MB",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }
            }
        }
    }

    return true;
}