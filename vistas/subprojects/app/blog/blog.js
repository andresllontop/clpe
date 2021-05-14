var beanPaginationBlog;
var blogSelected;
var beanRequestBlog = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestBlog.entity_api = 'blog';
    beanRequestBlog.operation = 'paginate';
    beanRequestBlog.type_request = 'GET';

    $('#sizePageBlog').change(function () {
        beanRequestBlog.type_request = 'GET';
        beanRequestBlog.operation = 'paginate';
        $('#modalCargandoBlog').modal('show');
    });

    $('#modalCargandoBlog').modal('show');

    $("#modalCargandoBlog").on('shown.bs.modal', function () {
        processAjaxBlog();
    });

    $("#ventanaModalManBlog").on('hide.bs.modal', function () {
        beanRequestBlog.type_request = 'GET';
        beanRequestBlog.operation = 'paginate';
    });

    $("#txtDescripcionBlog").Editor();
    $("#txtDescripcionAutorBlog").Editor();

    $("#txtTipoArchivoBlog").change(function () {
        tipo($(this).val());
    });

    $("#btnAbrirbook").click(function () {
        beanRequestBlog.operation = 'add';
        beanRequestBlog.type_request = 'POST';
        $("#imagePreview").html("");
        $("#tituloModalManBlog").html("REGISTRAR BLOG");
        addBlog();
        $("#ventanaModalManBlog").modal("show");


    });
    $("#formularioBlog").submit(function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (validateFormBlog()) {
            $('#modalCargandoBlog').modal('show');
        }
    });

});

function processAjaxBlog() {
    let form_data = new FormData();

    let parameters_pagination = '';
    let json = '';
    if (
        beanRequestBlog.operation == 'update' ||
        beanRequestBlog.operation == 'add'
    ) {

        json = {
            titulo: document.querySelector("#txtTituloBlog").value,
            resumen: document.querySelector("#txtResumenBlog").value,
            autor: document.querySelector("#txtAutorBlog").value,
            descripcion: $("#txtDescripcionBlog").Editor("getText"),
            descripcionAutor: $("#txtDescripcionAutorBlog").Editor("getText"),
            archivo: 0,
            tipo_archivo: parseInt(document.querySelector("#txtTipoArchivoBlog").value),
            comentario: ""

        };


    } else {
        form_data = null;
    }

    switch (beanRequestBlog.operation) {
        case 'delete':
            parameters_pagination = '?id=' + blogSelected.idblog;
            break;

        case 'update':
            json.idblog = blogSelected.idblog;
            if (parseInt(document.querySelector("#txtTipoArchivoBlog").value) == 1) {
                if (document.querySelector("#txtImagenBlog").files.length !== 0) {
                    let dataFoto = $("#txtImagenBlog").prop("files")[0];
                    form_data.append("txtImagenBlog", dataFoto);
                }
            } else {
                if (document.querySelector("#txtVideoBlog").files.length !== 0) {
                    let dataFoto = $("#txtVideoBlog").prop("files")[0];
                    form_data.append("txtVideoBlog", dataFoto);
                }
            }
            if (document.querySelector("#txtImagenAutorBlog").files.length !== 0) {
                let dataFotoAutor = $("#txtImagenAutorBlog").prop("files")[0];
                form_data.append("txtImagenAutorBlog", dataFotoAutor);
            }
            form_data.append("class", JSON.stringify(json));
            break;
        case 'add':
            if (parseInt(document.querySelector("#txtTipoArchivoBlog").value) == 1) {
                let data = $("#txtImagenBlog").prop("files")[0];
                form_data.append("txtImagenBlog", data);
            } else {
                let data = $("#txtVideoBlog").prop("files")[0];
                form_data.append("txtVideoBlog", data);
            }
            let dataFotoAutor = $("#txtImagenAutorBlog").prop("files")[0];
            form_data.append("txtImagenAutorBlog", dataFotoAutor);
            form_data.append("class", JSON.stringify(json));
            break;

        default:

            parameters_pagination +=
                '?filtro=';
            parameters_pagination +=
                '&pagina=' + document.querySelector("#pageBlog").value.trim();
            parameters_pagination +=
                '&registros=' + document.querySelector("#sizePageBlog").value.trim();
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestBlog.entity_api + "/" + beanRequestBlog.operation +
            parameters_pagination,
        type: beanRequestBlog.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token")
        },

        data: form_data,
        cache: false,
        contentType: ((beanRequestBlog.operation == 'update' || beanRequestBlog.operation == 'add') ? false : 'application/json; charset=UTF-8'),
        processData: false,
        dataType: 'json',
    }).done(function (beanCrudResponse) {

        $('#modalCargandoBlog').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() == 'ok') {
                showAlertTopEnd("success", "Realizado", "Acción realizada existosamente!");
                document.querySelector("#pageBlog").value = 1;
                document.querySelector("#sizePageBlog").value = 20;
                $('#ventanaModalManBlog').modal('hide');
            } else {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {

            beanPaginationBlog = beanCrudResponse.beanPagination;
            listaBlog(beanPaginationBlog);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoBlog').modal("hide");
        showAlertErrorRequest();

    });

}

function addBlog(blog = undefined) {
    //LIMPIAR LOS CAMPOS

    document.querySelector('#txtTituloBlog').value = (blog == undefined) ? '' : blog.titulo;
    document.querySelector('#txtTipoArchivoBlog').value = (blog == undefined) ? '0' : blog.tipoArchivo;
    document.querySelector('#txtResumenBlog').value = (blog == undefined) ? '' : blog.resumen;
    document.querySelector('#txtAutorBlog').value = (blog == undefined) ? '' : blog.autor == undefined || blog.autor == null ? "" : blog.autor;


    $("#txtDescripcionBlog").Editor("setText", (blog == undefined) ? '<p style="color:black"></p>' : blog.descripcion);
    $("#txtDescripcionBlog").Editor("getText");
    $("#txtDescripcionAutorBlog").Editor("setText", (blog == undefined) ? '<p style="color:black"></p>' : blog.descripcionAutor);
    $("#txtDescripcionAutorBlog").Editor("getText");
    if (blog != undefined) {
        if (blogSelected.foto == null) {
            document.querySelector("#imagenAutorPreview").innerHTML = ``;
        } else {
            document.querySelector("#imagenAutorPreview").innerHTML = `<img width='244' alt='user-picture' class='img-responsive text-center' src='${getHostFrontEnd() + 'adjuntos/blog/IMAGENES/' + blogSelected.foto}' />`;
        }


        tipo(document.querySelector('#txtTipoArchivoBlog').value);
        switch (parseInt(document.querySelector('#txtTipoArchivoBlog').value)) {
            case 1:
                document.querySelector("#imagePreview").innerHTML = `<img width='244' alt='user-picture' class='img-responsive text-center' src='${getHostFrontEnd() + 'adjuntos/blog/IMAGENES/' + blogSelected.archivo}' />`;
                break;
            case 2:
                document.querySelector("#videoPreview").innerHTML = `<video width='244' alt='user-picture' class='img-responsive text-center' controls ><source src='${getHostFrontEnd() + 'adjuntos/blog/VIDEOS/' + blogSelected.archivo}' type='video/mp4'></video>`;
                break;

            default:
                break;
        }
    } else {
        tipo(1);
        document.querySelector("#imagenAutorPreview").innerHTML = "";
    }

    //$("#txtResumenBlog").Editor("setText", (blog == undefined) ? '<p /style="color:black"></p>' : blog.resumen);
    // $("#txtResumenBlog").Editor("getText");



}

function listaBlog(beanPagination) {
    document.querySelector('#tbodyBlog').innerHTML = '';
    document.querySelector('#titleManagerBlog').innerHTML =
        '[ ' + beanPagination.countFilter + ' ] BLOGS';
    let row = "";

    if (beanPagination.list.length == 0) {
        destroyPagination($('#paginationBlog'));
        row += `<tr>
        <td class="text-center" colspan="6">NO HAY BLOG</td>
        </tr>`;

        document.querySelector('#tbodyBlog').innerHTML += row;
        return;
    }

    document.querySelector('#tbodyBlog').innerHTML += row;
    let html2;
    beanPagination.list.forEach((blog) => {
        switch (blog.tipoArchivo) {
            case "1":
                html2 = `<td  class="text-center"><img  
  src="${getHostFrontEnd()}adjuntos/blog/IMAGENES/${blog.archivo}"
  alt="${blog.archivo}"
  class="img-responsive center-box"style="width:100px;height:60px;"
  /></td>
 `;
                break;
            case "2":
                html2 = `
  <td  class="text-center "><video alt="user-picture"  
class="img-responsive center-box"style="width:100px;height:60px;" controls ><source class="imag" 
src="${getHostFrontEnd()}adjuntos/blog/VIDEOS/${blog.archivo}" 
type="video/mp4"></video></td>
`;
                break;
        }
        row += `<tr  idblog="${blog.idblog}">
<td class="text-center">${blog.titulo}</td>
<td class="text-center"style="display:none;">${blog.resumen}</td>
<td class="text-center" style="display:none;">${blog.descripcion}</td>
<td class="text-center">${blog.resumen.substring(0, 400)}</td>
${html2}
<td class="text-center">
<button class="btn btn-info editar-blog" ><i class="zmdi zmdi-refresh"></i> </button>
</td>
<td class="text-center">
<button class="btn btn-danger eliminar-blog"><i class="zmdi zmdi-delete"></i></button>
</td>
</tr>`;

        // $('[data-toggle="tooltip"]').tooltip();
    });
    document.querySelector('#tbodyBlog').innerHTML += row;
    buildPagination(
        beanPagination.countFilter,
        parseInt(document.querySelector("#sizePageBlog").value),
        document.querySelector("#pageBlog"),
        $('#modalCargandoBlog'),
        $('#paginationBlog'));
    addEventsButtonsBlog();


}

function addEventsButtonsBlog() {


    document.querySelectorAll('.editar-blog').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            blogSelected = findByBlog(
                btn.parentElement.parentElement.getAttribute('idblog')
            );

            if (blogSelected != undefined) {
                addBlog(blogSelected);
                $("#tituloModalManBlog").html("EDITAR BLOG");
                $("#ventanaModalManBlog").modal("show");
                beanRequestBlog.type_request = 'POST';
                beanRequestBlog.operation = 'update';
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
    document.querySelectorAll('.eliminar-blog').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            //   $('[data-toggle="tooltip"]').tooltip("hide");
            blogSelected = findByBlog(
                btn.parentElement.parentElement.getAttribute('idblog')
            );

            if (blogSelected != undefined) {
                beanRequestBlog.type_request = 'GET';
                beanRequestBlog.operation = 'delete';
                $('#modalCargandoBlog').modal('show');
            } else {
                console.log(
                    'warning',
                    'No se encontró el Almacen para poder editar'
                );
            }
        };
    });
}

function tipo(params) {
    switch (parseInt(params)) {
        case 1:
            $("#TipoArchivoBlog").html(`<div id="imagePreview" class="py-2 text-center"> </div>
    <input id="txtImagenBlog" type="file"accept="image/png, image/jpeg, image/png"
    class="material-control tooltips-general input-check-user"
    placeholder="Selecciona Imagen" data-toggle="tooltip"
    data-placement="top" title="" 
    data-original-title="Selecciona la Imagen de tu escritorio">
    <span class="highlight"></span>
    <span class="bar"></span>
    <label>Selecciona Imagen</label>
    <small>Tamaño Máximo Permitido: 4 MB</small>
    <br>
    <small>Formatos Permitido:JPG, PNG, JPEG</small>`);
            addViewArchivosPrevius();
            break;
        case 2:
            $(
                "#TipoArchivoBlog"
            ).html(`<div id="videoPreview" class="py-2 text-center"></div><input id="txtVideoBlog" type="file"
    class="material-control tooltips-general input-check-user"
    placeholder="Selecciona Video" data-toggle="tooltip"
    data-placement="top" title="" accept="video/mp4"
    data-original-title="Selecciona el Video de tu escritorio">
    <span class="highlight"></span>
    <span class="bar"></span>
    <label>Selecciona el Video</label>
    <small>Tamaño Máximo Permitido: 17 MB</small>
    <br>
    <small>Formatos Permitido:MP4</small>`);
            addViewArchivosPrevius();
            break;
        case 3:
            $("#TipoArchivoBlog").html(`<input id="PDF" type="file"
        class="material-control tooltips-general input-check-user"
        placeholder="Selecciona PDF" data-toggle="tooltip"
        data-placement="top" title="" 
        data-original-title="Selecciona el PDF de tu escritorio">
        <span class="highlight"></span>
        <span class="bar"></span>
        <label>Sube el Archivo</label>`);
            break;
        default:
            $("#TipoArchivoBlog").html("");
            break;
    }
}
function addViewArchivosPrevius() {

    $("#txtImagenBlog").change(function () {
        filePreview(this, "#imagePreview");
    });
    $("#txtImagenAutorBlog").change(function () {
        filePreview(this, "#imagenAutorPreview");
    });
    $("#txtVideoBlog").change(function () {
        videoPreview(this, "#videoPreview");
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

function videoPreview(input, imagen) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        imagen;
        reader.onload = function (e) {
            $(imagen).html(
                "<video width='244' alt='user-picture' class='img-responsive center-box' controls ><source src='" +
                e.target.result +
                "' type='video/mp4'></video>"
            );
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function findIndexBlog(idbusqueda) {
    return beanPaginationBlog.list.findIndex(
        (Blog) => {
            if (Blog.idblog == parseInt(idbusqueda))
                return Blog;


        }
    );
}

function findByBlog(idblog) {
    return beanPaginationBlog.list.find(
        (Blog) => {
            if (parseInt(idblog) == Blog.idblog) {
                return Blog;
            }


        }
    );
}
var validateFormBlog = () => {
    if (document.querySelector("#txtTituloBlog").value == "") {
        swal({
            title: "Vacío",
            text: "Ingrese titulo",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtResumenBlog").value == "") {
        swal({
            title: "Vacío",
            text: "Escribe Resumen",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtAutorBlog").value == "") {
        swal({
            title: "Vacío",
            text: "Escribe el nombre del Autor",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if ($("#txtDescripcionBlog").Editor("getText") == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Descripción",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if ($("#txtDescripcionAutorBlog").Editor("getText") == "") {
        swal({
            title: "Vacío",
            text: "Ingrese Descripción del Autor",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (document.querySelector("#txtTipoArchivoBlog").value == 0) {
        swal({
            title: "Vacío",
            text: "Selecciona Tipo",
            type: "warning",
            timer: 1200,
            showConfirmButton: false
        });
        return false;
    }
    if (beanRequestBlog.operation == 'add') {

        switch (parseInt(document.querySelector("#txtTipoArchivoBlog").value)) {
            case 1:
                if (document.querySelector("#txtImagenBlog").files.length == 0) {
                    swal({
                        title: "Vacío",
                        text: "Ingrese Imagen",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }
                if (!(document.querySelector("#txtImagenBlog").files[0].type == "image/png" || document.querySelector("#txtImagenBlog").files[0].type == "image/jpg" || document.querySelector("#txtImagenBlog").files[0].type == "image/jpeg")) {
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
                if (document.querySelector("#txtImagenBlog").files[0].size > (4 * 1024 * 1024)) {
                    swal({
                        title: "Tamaño excedido",
                        text: "el tamaño del archivo tiene que ser menor a 4 MB",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }

                break;
            default:
                //video
                if (document.querySelector("#txtVideoBlog").files.length == 0) {
                    swal({
                        title: "Vacío",
                        text: "Ingrese Video",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }
                if (document.querySelector("#txtVideoBlog").files[0].type !== "video/mp4") {
                    swal({
                        title: "Formato Incorrecto",
                        text: "Ingrese tipo de arhivo MP4 ",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }
                //menor a   17 MB
                if (document.querySelector("#txtVideoBlog").files[0].size > (17 * 1024 * 1024)) {
                    swal({
                        title: "Tamaño excedido",
                        text: "el tamaño del archivo tiene que ser menor a 5120 KB",
                        type: "warning",
                        timer: 1200,
                        showConfirmButton: false
                    });
                    return false;
                }
                break;

        }
        if (document.querySelector("#txtImagenAutorBlog").files.length == 0) {
            swal({
                title: "Vacío",
                text: "Ingrese Imagen",
                type: "warning",
                timer: 1200,
                showConfirmButton: false
            });
            return false;
        }
        if (!(document.querySelector("#txtImagenAutorBlog").files[0].type == "image/png" || document.querySelector("#txtImagenAutorBlog").files[0].type == "image/jpg" || document.querySelector("#txtImagenAutorBlog").files[0].type == "image/jpeg")) {
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
        if (document.querySelector("#txtImagenAutorBlog").files[0].size > (4 * 1024 * 1024)) {
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

        switch (parseInt(document.querySelector("#txtTipoArchivoBlog").value)) {
            case 1:
                if (document.querySelector("#txtImagenBlog").files.length != 0) {
                    if (document.querySelector("#txtImagenBlog").files.length == 0) {
                        swal({
                            title: "Vacío",
                            text: "Ingrese Imagen",
                            type: "warning",
                            timer: 1200,
                            showConfirmButton: false
                        });
                        return false;
                    }
                    if (!(document.querySelector("#txtImagenBlog").files[0].type == "image/png" || document.querySelector("#txtImagenBlog").files[0].type == "image/jpg" || document.querySelector("#txtImagenBlog").files[0].type == "image/jpeg")) {
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
                    if (document.querySelector("#txtImagenBlog").files[0].size > (4 * 1024 * 1024)) {
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

                break;

            default:
                if (document.querySelector("#txtVideoBlog").files.length != 0) {  //video
                    if (document.querySelector("#txtVideoBlog").files.length == 0) {
                        swal({
                            title: "Vacío",
                            text: "Ingrese Video",
                            type: "warning",
                            timer: 1200,
                            showConfirmButton: false
                        });
                        return false;
                    }
                    if (document.querySelector("#txtVideoBlog").files[0].type !== "video/mp4") {
                        swal({
                            title: "Formato Incorrecto",
                            text: "Ingrese tipo de arhivo MP4 ",
                            type: "warning",
                            timer: 1200,
                            showConfirmButton: false
                        });
                        return false;
                    }
                    //menor a   17 MB
                    if (document.querySelector("#txtVideoBlog").files[0].size > (17 * 1024 * 1024)) {
                        swal({
                            title: "Tamaño excedido",
                            text: "el tamaño del archivo tiene que ser menor a 17 MB",
                            type: "warning",
                            timer: 1200,
                            showConfirmButton: false
                        });
                        return false;
                    }
                }

                break;

        }
        if (document.querySelector("#txtImagenAutorBlog").files.length != 0) {
            if (document.querySelector("#txtImagenAutorBlog").files.length == 0) {
                swal({
                    title: "Vacío",
                    text: "Ingrese Imagen",
                    type: "warning",
                    timer: 1200,
                    showConfirmButton: false
                });
                return false;
            }
            if (!(document.querySelector("#txtImagenAutorBlog").files[0].type == "image/png" || document.querySelector("#txtImagenAutorBlog").files[0].type == "image/jpg" || document.querySelector("#txtImagenAutorBlog").files[0].type == "image/jpeg")) {
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
            if (document.querySelector("#txtImagenAutorBlog").files[0].size > (4 * 1024 * 1024)) {
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


    }

    return true;
}