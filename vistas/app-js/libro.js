$(document).ready(function () {
    let pag = 1;
    let total = 5;
    let codigoTitulo = "";
    let idSubTitulo = "";
    let codigoSubTitulo = "";
    let codigoParrafo = "";
    $("#seccion-capitulo").css("display", "none");
    $("#seccion-subtitulo").css("display", "none");
    $("#seccion-parrafo").css("display", "none");
    $("#btn-regresar").css("display", "none");
    $("#btn-regresar").click(function () {
        switch ($(this).attr("ubicacion")) {
            case "capitulo":
                $("#seccion-libro").css("display", "initial");
                $("#seccion-capitulo").css("display", "none");
                $("#btn-regresar").css("display", "none");
                break;
            case "subtitulo":
                $("#seccion-capitulo").css("display", "initial");
                $("#seccion-subtitulo").css("display", "none");
                $("#btn-regresar").attr("ubicacion", "capitulo");
                break;
            case "parrafo":
                $("#seccion-subtitulo").css("display", "initial");
                $("#seccion-parrafo").css("display", "none");
                $("#btn-regresar").attr("ubicacion", "subtitulo");
                break;

            default:
                break;
        }
    });
    listar();
    let ajax_load =
        "<div class='progress'>" +
        "<div id='progress_id' class='progress-bar progress-bar-striped active' " +
        "role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width: 45%'>" +
        "n/100</div></div>";
    function filePreview(input, imagen) {
        if (input.files && input.files[0]) {
            let reader = new FileReader();

            reader.onload = function (e) {
                $(imagen).html(
                    "<img style='width:400px;height:300px;' alt='user-picture' class='img-responsive center-box' src='" +
                    e.target.result +
                    "' />"
                );
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#Imagen-reg").change(function () {
        filePreview(this, "#imagePreview");
    });
    $("#DesImagen-reg").change(function () {
        filePreview(this, "#imagePreview1");
    });
    $("#Video-reg").change(function () {
        videoPreview(this, "#videoPreview");
    });
    function videoPreview(input, imagen) {
        if (input.files && input.files[0]) {
            let reader = new FileReader();

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
    $("#btnAbrirbook").click(function () {
        $(".FormularioAjax")[0].reset();
        $("#Codigo-reg").attr("readonly", false);
        $("#imagePreview1").html("");
        $("#imagePreview").html("");
        $("#videoPreview").html("");
        $("#formularioLibro").attr("data-form", "save");
        $("#tituloModalManlibro").html("REGISTRAR LIBRO");
        $("#ventanaModalManlibro").modal("show");
    });
    $(".FormularioAjax").submit(function (e) {
        $("#insertarModal").modal("hide");
        $("#modificarModal").modal("hide");
        e.preventDefault();
        let form = $(this);
        let tipo = form.attr("data-form");
        let metodo = form.attr("method");
        let formdata = new FormData(this);
        formdata.append("accion", tipo);

        var file1 = $("#Imagen-reg")[0].files[0];
        var file2 = $("#Video-reg")[0].files[0];

        if (file1 && file2) {
            $("#cargarpaginalibro").html(ajax_load);
            ProcesarAjax(metodo, formdata);
        } else {
            // ("noo");
            if (tipo == "save") {
                swal(
                    "Ocurrió un error inesperado",
                    "Por favor debes ingresar las imagenes Y video",
                    "error"
                );
            } else {
                // ("holi");
                $("#cargarpaginalibro").html(ajax_load);
                ProcesarAjax(metodo, formdata);
            }
        }
    });
    function ProcesarAjax(metodo, formdata) {
        $.ajax({
            type: metodo,
            url: url + "ajax/libroAjax.php",
            data: formdata,
            cache: false,
            contentType: false,
            processData: false,
            // modificar el valor de xhr a nuestro gusto
            xhr: function () {
                // obtener el objeto XmlHttpRequest nativo
                let xhr = $.ajaxSettings.xhr();
                // añadirle un controlador para el evento onprogress
                xhr.onprogress = function (evt) {
                    // calculamos el porcentaje y nos quedamos sólo con la parte entera
                    let porcentaje = Math.floor((evt.loaded / evt.total) * 100);
                    // actualizamos el texto con el porcentaje mostrado
                    $("#progress_id").text(porcentaje + "/100");
                    // actualizamos la cantidad avanzada en la barra de progreso
                    $("#progress_id").attr("aria-valuenow", porcentaje);
                    $("#progress_id").css("width", porcentaje + "%");
                };
                // devolvemos el objeto xhr modificado
                return xhr;
            },
            success: function (data) {
                console.log(data);
                $("#cargarpaginalibro").html("");
                $("#ventanaModalManlibro").modal("hide");
                swal({
                    title: JSON.parse(data).Titulo,
                    text: JSON.parse(data).Texto,
                    type: JSON.parse(data).Tipo,
                    confirmButtonText: "Aceptar"
                });
                listar();
                $(".FormularioAjax")[0].reset();
            },
            error: function (e) {
                swal(
                    "Ocurrió un error inesperado",
                    "Por favor recargue la página",
                    "error"
                );
            }
        });
        return false;
    }
    function listar() {
        let list = "listar";
        $.get(url + "ajax/libroAjax.php", { acion: list }, function (respuesta) {
            let libro = JSON.parse(respuesta);
            let html = "";
            let contador = 0;
            for (let key in libro) {
                contador++;
                html += `<tr  numero="${contador}" id="${libro[key].idlibro}" codigo="${
                    libro[key].codigo
                    }">
          <td class="text-center seleccionar">${contador}</td>
          <td class="text-center seleccionar">${libro[key].codigo}</td>
          <td class="text-center seleccionar">${libro[key].nombre}</td>
          <td  class="text-center seleccionar"><img 
          src="${url}adjuntos/libros/${libro[key].imagen}"
          alt="user-picture"
          class="img-responsive center-box"style="width:50px;height:60px;"
          /><div class="imag">${libro[key].imagen}</div></td>
          <td  class="text-center seleccionar"><video alt="user-picture" 
          class="img-responsive center-box"style="width:100px;height:60px;" controls >
          <source class="imag" src="${url}adjuntos/libros/${
                    libro[key].libroVideo
                    }" 
          type="video/mp4"></video><div class="imag">${
                    libro[key].libroVideo
                    }</div></td>
          
          <td class="text-center ">
              <button class="btn btn-info editar-Admin " ><i class="zmdi zmdi-refresh"></i> </button>
          </td>
        
          </tr>`;
                $(".RespuestaLista").html(html);
            }
            addEventsButtonsAdmin();
        });
    }
    function addEventsButtonsAdmin() {
        $(".editar-Admin").each(function (index, value) {
            $(this).click(function () {
                let indice = $(this.parentElement.parentElement).attr("numero");
                var rowvalue = [];
                var rowvalue2 = [];
                $(".RespuestaLista > tr").each(function (i, v) {
                    rowvalue[i] = $("td", this)
                        .map(function () {
                            return $(this).text();
                        })
                        .get();
                    rowvalue2[i] = $("td >.imag", this)
                        .map(function () {
                            return $(this).text();
                        })
                        .get();
                });

                $("#ID-reg").val($(this.parentElement.parentElement).attr("id"));
                $("#Codigo-reg").val(rowvalue[indice - 1][1]);
                $("#Codigo-reg").attr("readonly", true);
                $("#Nombre-reg").val(rowvalue[indice - 1][2]);
                $("#imagePreview").html(
                    "<img  alt='user-picture' class='img-responsive  center-box'style='width:180px;height:200px;' src='" +
                    url +
                    "adjuntos/libros/" +
                    rowvalue2[indice - 1][0] +
                    "' />"
                );

                $("#videoPreview").html(
                    '<video alt="user-picture"class="img-responsive center-box"style="width:200px;height:150px;" controls ><source src="' +
                    url +
                    "adjuntos/libros/" +
                    rowvalue2[indice - 1][2] +
                    '"type="video/mp4"></video>'
                );
                $("#formularioLibro").attr("data-form", "update");
                $("#tituloModalManlibro").html("EDITAR LIBRO");
                $("#ventanaModalManlibro").modal("show");
            });
        });
        $(".seleccionar").each(function (index, value) {
            $(this).click(function () {
                let indice = $(this.parentElement).attr("numero");
                var rowvalue = [];
                var rowvalue2 = [];
                $("tbody > tr").each(function (i, v) {
                    rowvalue[i] = $("td", this)
                        .map(function () {
                            return $(this).text();
                        })
                        .get();
                });
                // $("#formularioLibro").attr("data-form", "update");
                // $("#tituloModalManlibro").html("EDITAR LIBRO");
                // $("#ventanaModalManlibro").modal("show");
                $("#btn-regresar").css("display", "initial");
                $("#btn-regresar").attr("ubicacion", "capitulo");
                $("#seccion-libro").css("display", "none");
                $("#seccion-capitulo").css("display", "initial");
                codigoTitulo = $(this.parentElement).attr("codigo");
                listarCapitulo(pag, total, $(this.parentElement).attr("codigo"));
            });
        });
        $(".eliminar-Admin").each(function (index, value) {
            $(this).click(function () {
                let formdata = new FormData();
                let va = $(this.parentElement.parentElement).attr("id");
                formdata.append("ID-reg", va);
                formdata.append("accion", "delete");
                ProcesarAjax("POST", formdata);
            });
        });
    }
    // capitulo
    $("#btnAbrircapitulo").click(function () {
        $("#formulariocapitulo").attr("data-form", "save");
        $(".FormularioAjaxCapitulo")[0].reset();

        $("#Libro-reg  option").attr("selected", false);
        $("#Libro-reg  option[value=" + 0 + "]").attr("selected", true);
        $("#tituloModalMancapitulo").html("REGISTRAR CAPITULO");
        $("#ventanaModalMancapitulo").modal("show");
    });

    $(".FormularioAjaxCapitulo").submit(function (e) {
        $("#insertarModal").modal("hide");
        $("#modificarModal").modal("hide");
        e.preventDefault();
        let form = $(this);
        let tipo = form.attr("data-form");
        let metodo = form.attr("method");
        let formdata = new FormData(this);
        formdata.append("accion", tipo);
        formdata.append("CodigoLibro-reg", $("#Libro-reg option:selected").val());
        if ($("#Libro-reg option:selected").val() != 0) {
            $("#cargarpaginacapitulo").html(ajax_load);
            ProcesarAjaxCapitulo(metodo, formdata);
        } else {
            swal(
                "Ocurrió un error inesperado",
                "Por favor debes seleccionar un libro",
                "error"
            );
        }
    });
    function ProcesarAjaxCapitulo(metodo, formdata) {
        $.ajax({
            type: metodo,
            url: url + "ajax/capituloAjax.php",
            data: formdata,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $(".preloader1").html(` <div class="text-center cargando">
        <h2 class="all-tittles">Cargando...</h2>
      </div>`);
            },
            success: function (data) {
                $("#ventanaModalMancapitulo").modal("hide");
                $("#cargarpaginacapitulo").html("");
                swal({
                    title: JSON.parse(data).Titulo,
                    text: JSON.parse(data).Texto,
                    type: JSON.parse(data).Tipo,
                    confirmButtonText: "Aceptar"
                });
                listarCapitulo(pag, total, codigoTitulo);
                $(".FormularioAjaxCapitulo")[0].reset();
            },
            error: function (e) {
                swal(
                    "Ocurrió un error inesperado",
                    "Por favor recargue la página",
                    "error"
                );
            }
        });
        return false;
    }
    function listarCapitulo(paginas, registrototal, codigo) {
        $.ajax({
            type: "GET",
            url: url + "ajax/capituloAjax.php",
            data: {
                acion: "listar",
                pagina: paginas,
                registros: registrototal,
                busca: codigo
            },
            beforeSend: function () {
                $(".preloader").html(` <div class="text-center cargando">
        <h2 class="all-tittles">Cargando...</h2>
      </div>`);
            },
            success: function (respuesta) {
                let html = "";
                $("#cargarpagina").html("");
                if (respuesta == "ninguno") {
                    $("#paginador ul > li")
                        .last()
                        .find("span")
                        .text("Ninguno");
                    $("#paginador ul > li")
                        .last()
                        .prev()
                        .removeClass("active");
                    $("#paginador ul > li")
                        .last()
                        .addClass("active");
                    html += `<tr ><td colspan="7"class="text-center">NINGUN DATO</td></tr>`;
                    $(".RespuestaListaCapitulo").html(html);
                } else {
                    let capitulo = JSON.parse(respuesta);

                    let contador = paginas * registrototal - registrototal;
                    for (var key in capitulo) {
                        contador++;
                        html += `<tr numero="${contador}" id="${
                            capitulo[key].idtitulo
                            }"codigocapitulo="${capitulo[key].codigoTitulo}" idlibro="${
                            capitulo[key].libro_codigoLibro
                            }">
           
              <td class="text-center Accion-Capitulo ">${contador}</td>
              <td class="text-center Accion-Capitulo">${
                            capitulo[key].codigoTitulo
                            }</td>
              <td class="text-center Accion-Capitulo">${
                            capitulo[key].tituloNombre
                            }</td>
              <td class="text-center Accion-Capitulo">${
                            capitulo[key].nombre
                            }</td>
              
              <td class="text-center ">
                  <button class="btn btn-success editar-Capitulo " ><i class="zmdi zmdi-refresh"></i> </button>
              </td>
              <td class="text-center">
                   <button class="btn btn-danger eliminar-Capitulo "><i class="zmdi zmdi-delete"></i></button>
              </td>
              </tr>`;
                        $(".RespuestaListaCapitulo").html(html);
                    }
                    addEventsButtonsCapitulo();
                }
                $(".preloader").html("");
            },
            error: function (e) {
                $(".preloader").html("");
                swal(
                    "Ocurrió un error inesperado",
                    "Por favor recargue la página",
                    "error"
                );
            }
        });
        return false;
    }
    function addEventsButtonsCapitulo() {
        $(".editar-Capitulo").each(function (index, value) {
            $(this).click(function () {
                var indice = $(this.parentElement.parentElement).attr("numero");
                var rowvaluec = [];
                $(".RespuestaListaCapitulo > tr").each(function (i, v) {
                    rowvaluec[i] = $("td", this)
                        .map(function () {
                            return $(this).text();
                        })
                        .get();
                });
                for (let index = 0; index < rowvaluec.length; index++) {
                    if (rowvaluec[index][0] == indice) {
                        indice = index + 1;
                    }
                }
                $("#Codigo-reg-c").val(rowvaluec[indice - 1][1]);

                $("#Nombre-reg-c").val(rowvaluec[indice - 1][2]);
                $("#ID-reg-c").val($(this.parentElement.parentElement).attr("id"));

                $(
                    "#Libro-reg option[value=" +
                    $(this.parentElement.parentElement).attr("idlibro") +
                    "]"
                ).attr("selected", true);
                $("#formulariocapitulo").attr("data-form", "update");
                $("#tituloModalMancapitulo").html("EDITAR CAPITULO");
                $("#ventanaModalMancapitulo").modal("show");
            });
        });
        $(".Accion-Capitulo").each(function (index, value) {
            $(this).click(function () {
                var indice = $(this.parentElement).attr("numero");
                var rowvalue = [];
                $("tbody > tr").each(function (i, v) {
                    rowvalue[i] = $("td", this)
                        .map(function () {
                            return $(this).text();
                        })
                        .get();
                });

                $("#btn-regresar").attr("ubicacion", "subtitulo");
                // $("#formulariocapitulo").attr("data-form", "update");
                // $("#tituloModalMancapitulo").html("EDITAR CAPITULO");
                // $("#ventanaModalMancapitulo").modal("show");
                $("#seccion-capitulo").css("display", "none");
                $("#seccion-subtitulo").css("display", "initial");
                idSubTitulo = $(this.parentElement).attr("id");
                codigoSubTitulo = $(this.parentElement).attr("codigocapitulo");
                listarSubtitulo(pag, total, $(this.parentElement).attr("id"));
            });
        });
        $(".eliminar-Capitulo").each(function (index, value) {
            $(this).click(function () {
                var formdata = new FormData();
                formdata.append(
                    "ID-reg",
                    $(this.parentElement.parentElement).attr("id")
                );
                formdata.append("accion", "delete");
                ProcesarAjaxCapitulo("POST", formdata);
            });
        });
    }
    //SUBTITULO
    // listarSubtitulo(pag, total,idSubTitulo);

    $("#btnAbrirSubCapitulo").click(function () {
        $("#formulariosubcapitulo").attr("data-form", "save");
        $(".FormularioAjaxSubtitulo")[0].reset();
        $("#Capitulo-reg option").attr("selected", false);
        $("#Capitulo-reg option[value=" + 0 + "]").attr("selected", true);
        $("#Capitulo-reg").val(idSubTitulo);
        $("#tituloModalMansubcapitulo").html("REGISTRAR SUBTITULO");
        $("#ventanaModalMansubcapitulo").modal("show");
    });

    $(".FormularioAjaxSubtitulo").submit(function (e) {
        $("#insertarModal").modal("hide");
        $("#modificarModal").modal("hide");
        e.preventDefault();
        let form = $(this);
        let tipo = form.attr("data-form");
        let metodo = form.attr("method");
        let formdata = new FormData(this);
        var file = $("#PDF-reg-s")[0].files[0];
        formdata.append("accion", tipo);

        if (file) {
            $("#cargarpaginasubtitulo").html(ajax_load);
            ProcesarAjaxSubtitulo(metodo, formdata);
        } else {
            if (tipo == "save") {
                swal(
                    "Ocurrió un error inesperado",
                    "Por favor debes seleccionar  Capitulo y Archivo",
                    "error"
                );
            } else {
                $("#cargarpaginasubtitulo").html(ajax_load);
                ProcesarAjaxSubtitulo(metodo, formdata);
            }
        }
    });
    function ProcesarAjaxSubtitulo(metodo, formdata) {
        $.ajax({
            type: metodo,
            url: url + "ajax/subcapituloAjax.php",
            data: formdata,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $(".preloader1").html(` <div class="text-center cargando">
                    <h2 class="all-tittles">Cargando...</h2>
                 </div>`);
            },
            success: function (data) {
                console.log(data);
                $("#ventanaModalMansubcapitulo").modal("hide");
                swal({
                    title: JSON.parse(data).Titulo,
                    text: JSON.parse(data).Texto,
                    type: JSON.parse(data).Tipo,
                    confirmButtonText: "Aceptar"
                });
                $("#cargarpaginasubtitulo").html("");
                listarSubtitulo(pag, total, idSubTitulo);
                $(".FormularioAjaxSubtitulo")[0].reset();
            },
            error: function (e) {
                swal(
                    "Ocurrió un error inesperado",
                    "Por favor recargue la página",
                    "error"
                );
            }
        });
        return false;
    }
    function listarSubtitulo(paginas, registrototal, codigosub) {
        // $("#cargarpaginalista").html(ajax_load);
        $.ajax({
            type: "GET",
            url: url + "ajax/subcapituloAjax.php",
            data: {
                acion: "listar",
                pagina: paginas,
                registros: registrototal,
                busca: codigosub
            },
            beforeSend: function () {
                $(".preloader1").html(` <div class="text-center cargando">
        <h2 class="all-tittles">Cargando...</h2>
      </div>`);
            },
            success: function (respuesta) {
                let html = "";
                $("#cargarpaginalista").html("");
                if (respuesta == "ninguno") {
                    $("#paginador ul > li")
                        .last()
                        .find("span")
                        .text("Ninguno");
                    $("#paginador ul > li")
                        .last()
                        .prev()
                        .removeClass("active");
                    $("#paginador ul > li")
                        .last()
                        .addClass("active");
                    html += `<tr ><td colspan="7"class="text-center">NINGUN DATO</td></tr>`;
                    $(".RespuestaListaSubtitulo").html(html);
                } else {
                    let subcapitulo = JSON.parse(respuesta);

                    let extension = ".pdf";
                    var contador = paginas * registrototal - registrototal;
                    for (let key in subcapitulo) {
                        contador++;
                        html += `<tr numero="${contador}" codigosubtitulo="${
                            subcapitulo[key].codigo_subtitulo
                            }" id="${subcapitulo[key].idsubtitulo}"idcapitulo="${
                            subcapitulo[key].titulo_idtitulo
                            }">
            <td class="text-center Accion-Parrafo ">${contador}</td>
            <td class="text-center Accion-Parrafo">${
                            subcapitulo[key].codigo_subtitulo
                            }</td>
            <td class="text-center Accion-Parrafo">${
                            subcapitulo[key].nombre
                            }</td>
            <td class="text-center Accion-Parrafo">${
                            subcapitulo[key].tituloNombre
                            }</td>
            <td class="text-center Accion-Parrafo"><a href="${url}adjuntos/archivos/${
                            subcapitulo[key].libro_codigoLibro
                            }/${subcapitulo[key].codigoTitulo}/PDF/${
                            subcapitulo[key].subtituloPDF
                            }"
            download='${
                            subcapitulo[key].nombre
                            }${extension}' class="btn btn-warning" style="text-decoration:none;">
            <i class="zmdi zmdi-download"></i> 
            
          </a></td>
            <td class="text-center ">
                <button class="btn btn-success editar-Subtitulo " ><i class="zmdi zmdi-refresh"></i> </button>
            </td>
            <td class="text-center">
                 <button class="btn btn-danger eliminar-Subtitulo "><i class="zmdi zmdi-delete"></i></button>
            </td>
            </tr>`;
                        $(".RespuestaListaSubtitulo").html(html);
                    }
                    addEventsButtonsSubtitulo();
                }
                $(".preloader1").html("");
            },
            error: function (e) {
                $(".preloader1").html("");
                swal(
                    "Ocurrió un error inesperado",
                    "Por favor recargue la página",
                    "error"
                );
            }
        });
        return false;
    }
    function addEventsButtonsSubtitulo() {
        $(".editar-Subtitulo").each(function (index, value) {
            $(this).click(function () {
                let indice = $(this.parentElement.parentElement).attr("numero");
                let rowvalue = [];
                $(".RespuestaListaSubtitulo > tr").each(function (i, v) {
                    rowvalue[i] = $("td", this)
                        .map(function () {
                            return $(this).text();
                        })
                        .get();
                });
                for (let index = 0; index < rowvalue.length; index++) {
                    if (rowvalue[index][0] == indice) {
                        indice = index + 1;
                    }
                }
                $("#Codigo-reg-s").val(rowvalue[indice - 1][1]);
                $("#Nombre-reg-s").val(rowvalue[indice - 1][2]);
                $("#ID-reg-s").val($(this.parentElement.parentElement).attr("id"));

                $("#Capitulo-reg").val(
                    $(this.parentElement.parentElement).attr("idcapitulo")
                );
                $("#formulariosubcapitulo").attr("data-form", "update");
                $("#tituloModalMansubcapitulo").html("EDITAR SUBTITULO");
                $("#ventanaModalMansubcapitulo").modal("show");
            });
        });
        $(".Accion-Parrafo").each(function (index, value) {
            $(this).click(function () {
                var indice = $(this.parentElement).attr("numero");
                var rowvalue = [];
                $("tbody > tr").each(function (i, v) {
                    rowvalue[i] = $("td", this)
                        .map(function () {
                            return $(this).text();
                        })
                        .get();
                });

                $("#btn-regresar").attr("ubicacion", "parrafo");
                // $("#formulariocapitulo").attr("data-form", "update");
                // $("#tituloModalMancapitulo").html("EDITAR CAPITULO");
                // $("#ventanaModalMancapitulo").modal("show");
                $("#seccion-subtitulo").css("display", "none");
                $("#seccion-parrafo").css("display", "initial");
                codigoParrafo = $(this.parentElement).attr("codigosubtitulo");
                console.log("codigo : " + codigoParrafo);
                //

                // idSubTitulo = $(this.parentElement).attr("id");
                // codigoSubTitulo = $(this.parentElement).attr("codigocapitulo");
                listarParrafo(pag, total, codigoParrafo);
            });
        });
        $(".eliminar-Subtitulo").each(function (index, value) {
            $(this).click(function () {
                let formdata = new FormData();
                let va = $(this.parentElement.parentElement).attr("id");
                formdata.append("ID-reg", va);
                formdata.append("accion", "delete");
                ProcesarAjaxSubtitulo("POST", formdata);
            });
        });
    }
    // parrafo

    $("#Video-reg-p").change(function () {
        videoPreview(this, "#videoPreview-p");
    });
    function videoPreview(input, imagen) {
        if (input.files && input.files[0]) {
            let reader = new FileReader();
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
    $("#btnAbrirvideos").click(function () {
        $("#formulariovideos").attr("data-form", "save");
        $(".FormularioAjaxvideos")[0].reset();
        $("#videoPreview-p").html("");
        $("#CodigoParrafo-reg").val("");
        $("#Subcapitulo-reg").val(codigoParrafo);
        $("#tituloModalManvideos").html("REGISTRAR PARRAFO");
        $("#ventanaModalManvideos").modal("show");
    });

    $(".FormularioAjaxvideos").submit(function (e) {
        $("#insertarModal").modal("hide");
        $("#modificarModal").modal("hide");
        e.preventDefault();
        var form = $(this);
        var tipo = form.attr("data-form");
        var metodo = form.attr("method");
        var formdata = new FormData(this);
        formdata.append("accion", tipo);
        var file2 = $("#Video-reg-p")[0].files[0];
        // if (file2) {
        $("#cargarpaginaparrafo").html(ajax_load);
        ProcesarAjaxParrafo(metodo, formdata);
        // } else {
        //   swal(
        //     "Ocurrió un error inesperado",
        //     "Por favor debes ingresar Video",
        //     "error"
        //   );
        // }
    });
    function ProcesarAjaxParrafo(metodo, formdata) {
        $.ajax({
            type: metodo,
            url: url + "ajax/videosubcapituloAjax.php",
            data: formdata,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $(".preloader1").html(` <div class="text-center cargando">
                    <h2 class="all-tittles">Cargando...</h2>
                 </div>`);
            },
            success: function (data) {
                console.log(data);
                $("#ventanaModalManvideos").modal("hide");
                swal({
                    title: JSON.parse(data).Titulo,
                    text: JSON.parse(data).Texto,
                    type: JSON.parse(data).Tipo,
                    confirmButtonText: "Aceptar"
                });
                listarParrafo(pag, total, codigoParrafo);
                $(".FormularioAjaxvideos")[0].reset();
                $("#cargarpaginaparrafo").html("");
            },

            error: function (e) {
                swal(
                    "Ocurrió un error inesperado",
                    "Por favor recargue la página",
                    "error"
                );
            }
        });
        return false;
    }
    function listarParrafo(paginas, registrototal, codigosub) {
        $.ajax({
            type: "GET",
            url: url + "ajax/videosubcapituloAjax.php",
            data: {
                acion: "listar",
                pagina: paginas,
                registros: registrototal,
                busca: codigosub
            },
            beforeSend: function () {
                $(".preloader2").html(` <div class="text-center cargando">
        <h2 class="all-tittles">Cargando...</h2>
      </div>`);
            },
            success: function (respuesta) {
                $("#cargarpaginalista").html("");
                var html = "";
                if (respuesta == "ninguno") {
                    $("#paginador ul > li")
                        .last()
                        .find("span")
                        .text("Ninguno");
                    $("#paginador ul > li")
                        .last()
                        .prev()
                        .removeClass("active");
                    $("#paginador ul > li")
                        .last()
                        .addClass("active");
                    html += `<tr ><td colspan="7"class="text-center">NINGUN DATO</td></tr>`;
                    $(".RespuestaListaParrafo").html(html);
                } else {
                    var videos = JSON.parse(respuesta);

                    var contador = paginas * registrototal - registrototal;
                    for (var key in videos) {
                        let array = videos[key]["subtitulo_codigosubtitulo"].split(".");
                        contador++;
                        html += `<tr numero="${contador}" id="${
                            videos[key].idvideo_subtitulo
                            }" Codsubtitulo="${videos[key].subtitulo_codigosubtitulo}" >
          <td class="text-center ">${contador}</td>
          <td class="text-center">${videos[key].codigovideo_subtitulo}</td>
          <td class="text-center">${videos[key].nombre}</td>
          <td  class="text-center "><video alt="user-picture"
            class="img-responsive center-box"style="width:100px;height:60px;" controls >
            <source  src="${url}adjuntos/archivos/${array[0]}/${array[0]}.${
                            array[1]
                            }.${array[2]}/VIDEOS/${videos[key].subtitulo_codigosubtitulo}/${
                            videos[key].nombreVideo
                            }"
            type="video/mp4"></video><div class="imag">${
                            videos[key].nombreVideo
                            }</div></td>
          <td class="text-center ">
              <button class="btn btn-info editar-videos " ><i class="zmdi zmdi-refresh"></i> </button>
          </td>
          <td class="text-center">
               <button class="btn btn-danger eliminar-videos "><i class="zmdi zmdi-delete"></i></button>
          </td>
          </tr>`;
                    }
                    $(".RespuestaListaParrafo").html(html);
                    addEventsButtonsvideos();
                }
                $(".preloader2").html("");
            },
            error: function (e) {
                $(".preloader2").html("");
                swal(
                    "Ocurrió un error inesperado",
                    "Por favor recargue la página",
                    "error"
                );
            }
        });
        return false;
    }
    function addEventsButtonsvideos() {
        $(".editar-videos").each(function (index, value) {
            $(this).click(function () {
                var indice = $(this.parentElement.parentElement).attr("numero");
                var rowvalue = [];
                var rowvalue2 = [];
                $(".RespuestaListaParrafo > tr").each(function (i, v) {
                    rowvalue[i] = $("td", this)
                        .map(function () {
                            return $(this).text();
                        })
                        .get();
                    rowvalue2[i] = $("td >.imag", this)
                        .map(function () {
                            return $(this).text();
                        })
                        .get();
                });
                for (let index = 0; index < rowvalue.length; index++) {
                    if (rowvalue[index][0] == indice) {
                        indice = index + 1;
                    }
                }

                $("#ID-reg-p").val($(this.parentElement.parentElement).attr("id"));
                let array2 = rowvalue[indice - 1][1].split(".");
                $("#videoPreview-p").html(
                    '<video alt="user-picture"class="img-responsive center-box"style="width:200px;height:120px;" controls ><source src="' +
                    url +
                    "adjuntos/archivos/" +
                    array2[0] +
                    "/" +
                    array2[0] +
                    "." +
                    array2[1] +
                    "." +
                    array2[2] +
                    "/VIDEOS/" +
                    $(this.parentElement.parentElement).attr("Codsubtitulo") +
                    "/" +
                    rowvalue2[indice - 1][0] +
                    '"type="video/mp4"></video>'
                );
                $("#CodigoParrafo-reg").val(rowvalue[indice - 1][1]);
                $("#Subcapitulo-reg").val(
                    $(this.parentElement.parentElement).attr("Codsubtitulo")
                );
                // $("#Subcapitulo-reg").attr("readonly", true);
                $("#formulariovideos").attr("data-form", "update");
                $("#tituloModalManvideos").html("EDITAR VIDEO PARA EL SUBTITULO");
                $("#ventanaModalManvideos").modal("show");
            });
        });
        $(".eliminar-videos").each(function (index, value) {
            $(this).click(function () {
                var formdata = new FormData();
                formdata.append(
                    "ID-reg-p",
                    $(this.parentElement.parentElement).attr("id")
                );
                formdata.append("accion", "delete");
                ProcesarAjaxParrafo("POST", formdata);
            });
        });
    }
});
