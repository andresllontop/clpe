var beanPaginationDetalle;
var detalleSelected;
var beanRequestDetalle = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
    beanRequestDetalle.entity_api = 'detalles/recursos';
    beanRequestDetalle.operation = 'paginate';
    beanRequestDetalle.type_request = 'GET';


    $("#modalCargandoDetalle").on('shown.bs.modal', function () {
        processAjaxDetalle();
    });
    $("#ventanaModalManDetalle").on('hide.bs.modal', function () {
        beanRequestDetalle.type_request = 'GET';
        beanRequestDetalle.operation = 'paginate';
    });

});

function processAjaxDetalle() {
    let parameters_pagination = '';
    switch (beanRequestDetalle.operation) {
        default:
            parameters_pagination +=
                '?filtro=' + recursoSelected.idrecurso;
            parameters_pagination +=
                '&pagina=1';
            parameters_pagination +=
                '&registros=30';
            break;
    }
    $.ajax({
        url: getHostAPI() + beanRequestDetalle.entity_api + "/" + beanRequestDetalle.operation +
            parameters_pagination,
        type: beanRequestDetalle.type_request,
        headers: {
            'Authorization': 'Bearer ' + Cookies.get("clpe_token") + (Cookies.get("clpe_libro") == undefined ? "" : " Clpe " + Cookies.get("clpe_libro"))
        },
        data: null,
        cache: false,
        contentType: 'application/json; charset=UTF-8',
        processData: false,
        dataType: 'json'
    }).done(function (beanCrudResponse) {

        $('#modalCargandoDetalle').modal('hide');
        if (beanCrudResponse.messageServer !== null) {
            if (beanCrudResponse.messageServer.toLowerCase() != 'ok') {

                showAlertTopEnd("warning", "Error", beanCrudResponse.messageServer);
            }
        }
        if (beanCrudResponse.beanPagination !== null) {
            beanPaginationDetalle = beanCrudResponse.beanPagination;
            listaDetalle(beanPaginationDetalle);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        $('#modalCargandoDetalle').modal("hide");
        showAlertErrorRequest();

    });

}


function listaDetalle(beanPagination) {
    document.querySelector('#sectionRecursos').classList.add("d-none");
    document.querySelector('#sectionDetalles').classList.remove("d-none");
    document.querySelector('#tbodyDetalle').innerHTML = '';
    document.querySelector('#titleManagerDetalle').innerHTML =
        recursoSelected.nombre;
    let row = "", rowvideo = "", contador = 0;
    if (beanPagination.list.length == 0) {
        row += `<tr>
        <td class="text-center" colspan="5">NO HAY ARCHIVOS</td>
        </tr>`;
        document.querySelector('#tbodyDetalle').innerHTML = row;
        return;
    }

    beanPagination.list.forEach((detalle) => {
        if (detalle.tipo == 1) {
            contador++;
            //imagen
            row += `
            <div class="col-sm-4 col-12 mb-2">
        <div class="card text-center border border-purple border-2 h-100">
          <h5 class="card-header text-center  ">${detalle.descripcion}</h5>
          <img src="${getHostFrontEnd()}adjuntos/recurso/IMAGENES/${detalle.archivo}" class="card-img-top" alt="detalle.archivo">
          <div class="card-body">
            <a href="#" class="btn btn-purple border-purple ver-imagen p-1" iddetallerecurso="${detalle.iddetallerecurso}"><i class="zmdi zmdi-eye zmdi-hc mr-1"></i><span>VER</a>
            <a download href="${getHostFrontEnd()}adjuntos/recurso/IMAGENES/${detalle.archivo}" class="btn btn-purple border-purple descargar-imagen p-1" iddetallerecurso="${detalle.iddetallerecurso}"><i class="zmdi zmdi-download zmdi-hc mr-1"></i><span>DESCARGAR</span></a>
          </div>

        </div>
      </div>
            `;
        } else if (detalle.tipo == 2) {
            //video
            rowvideo += ` 
            <div class="col-sm-6 col-12 mb-2">
            <div class="card text-center border border-purple border-2 h-100">
              <h5 class="card-header">${detalle.descripcion}</h5>
              <video  width="100%" class="card-img-top my-auto"  alt='user-picture' controls ><source src='${getHostFrontEnd()}adjuntos/recurso/VIDEOS/${detalle.archivo}' type='video/mp4'></video>
            </div>
          </div>

           
            `;
        } else {
            contador++;
            //archivo pdf
            row += `
            <div class="col-sm-2 col-12 mb-2">
        <div class="card text-center  border border-purple border-2 h-100">
          <h5 class="card-header">${detalle.descripcion}</h5>
          <div class="my-auto p-2">
          <img style="width:5em;" src="${getHostFrontEnd()}vistas/assets/img/pdf.png"
          alt="user-picture" />
          <a href="#" download class="btn btn-purple border-purple descargar-archivo p-1" iddetallerecurso="${detalle.iddetallerecurso}"><i class="zmdi zmdi-download zmdi-hc mr-1"></i><span>DESCARGAR</span></a>
          
          </div>
        
        </div>
      </div>
            `;
        }



        // $('[data-toggle="tooltip"]').tooltip();
    });

    document.querySelector('#tbodyDetalle').innerHTML = row + rowvideo;

    addEventsButtonsDetalle();


}

function listaDetalle2(beanPagination) {
    document.querySelector('#sectionRecursos').classList.add("d-none");
    document.querySelector('#sectionDetalles').classList.remove("d-none");
    document.querySelector('#tbodyDetalle').innerHTML = '';
    document.querySelector('#titleManagerDetalle').innerHTML =
        recursoSelected.nombre;
    let row = "", rowvideo = "", contador = 0;
    if (beanPagination.list.length == 0) {
        row += `<tr>
        <td class="text-center" colspan="5">NO HAY ARCHIVOS</td>
        </tr>`;
        document.querySelector('#tbodyDetalle').innerHTML = row;
        return;
    }

    beanPagination.list.forEach((detalle) => {
        if (detalle.tipo == 1) {
            contador++;
            //imagen
            row += `
            <div class="col-sm-6 col-12" >
            <div class="anim fadeInLeft">
            <h5 class="text-center px-4 py-1 w-100" style="font-weight: bold;">${detalle.descripcion}</h5>
            <div class="bg-purple p-2">
            <img width="100%" height="100%"src="${getHostFrontEnd()}adjuntos/recurso/IMAGENES/${detalle.archivo}"
            alt="user-picture" />
            </div>
            <div class="text-center d-flex  py-1">
            <button class="btn btn-purple ver-imagen p-1 mr-2 w-50" iddetallerecurso="${detalle.iddetallerecurso}"><i class="zmdi zmdi-eye zmdi-hc mr-1"></i><span>VER</span>
            </button>
            <a download href="${getHostFrontEnd()}adjuntos/recurso/IMAGENES/${detalle.archivo}" class="w-50"> <button class="btn btn-purple descargar-imagen p-1 w-100" iddetallerecurso="${detalle.iddetallerecurso}"><i class="zmdi zmdi-download zmdi-hc mr-1"></i><span>DESCARGAR</span></button></a>
            </div>

            </div>
            </div>
           
            `;
        } else if (detalle.tipo == 2) {
            //video
            rowvideo += ` 
           
            <h5 class="text-center px-4 py-1 w-100" style="font-weight: bold;">${detalle.descripcion}</h5>
            <video  width="100%" height="100%" alt='user-picture' controls ><source src='${getHostFrontEnd()}adjuntos/recurso/VIDEOS/${detalle.archivo}' type='video/mp4'></video>

           
            `;
        } else {
            contador++;
            //archivo pdf
            row += `
            <div class="col-sm-6 col-12 bg-purple" >
            <div class="anim fadeInLeft">
            <h5 class="text-center px-4 py-1 w-100  text-white" style="font-weight: bold;">${detalle.descripcion}</h5>
            <div class="text-center d-flex  py-1">
            <img style="width:50px" src="${getHostFrontEnd()}vistas/assets/img/pdf.png" alt="Subtitulo">
            <button class="btn btn-light descargar-archivo m-2 w-100" iddetallerecurso="${detalle.iddetallerecurso}">
            <i class="zmdi zmdi-download mr-1"></i><span>DESCARGAR</span></button>
            </div>

            </div>
            </div>
            `;
        }



        // $('[data-toggle="tooltip"]').tooltip();
    });

    if (rowvideo == "" && contador > 0) {
        document.querySelector('#tbodyDetalle').innerHTML = `
        <div class="col-sm-8 col-12 col-sm-offset-2 col-offset-0" >
        <div class="form-row">
        ${row}
        </div>
        </div>
        </div>
        `;
    } else if (rowvideo != "" && contador > 0) {
        document.querySelector('#tbodyDetalle').innerHTML = `
        <div class="col-sm-5 col-12" >
        <div class="form-row">
        ${row}
        </div>
        </div>
        <div class="col-sm-7 col-12" >
        <div class="anim fadeInLeft">
        ${rowvideo}
        </div>
        </div>
        `;
    } else {

        document.querySelector('#tbodyDetalle').innerHTML = `
        <div class="col-sm-5 col-12 col-sm-offset-2 col-offset-0" >
        <div class="row">
        ${rowvideo}
        </div>
        </div>
        </div>
        `;
    }

    addEventsButtonsDetalle();


}



function addEventsButtonsDetalle() {
    document.querySelectorAll('.descargar-archivo').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            detalleSelected = findByDetalle(
                btn.getAttribute('iddetallerecurso')
            );
            if (detalleSelected != undefined) {
                $("#modalFrameDetalle").modal("show");
                document.querySelector("#descargarPdf").parentElement.setAttribute("href", getHostFrontEnd() + "adjuntos/recurso/PDF/" + detalleSelected.archivo);
                downloadURL(getHostFrontEnd() + "adjuntos/recurso/PDF/" + detalleSelected.archivo);
            } else {
                showAlertTopEnd("info", "Vacío!", "No se encontró el Recurso");
            }
        };
    });
    document.querySelectorAll('.ver-imagen').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            detalleSelected = findByDetalle(
                btn.getAttribute('iddetallerecurso')
            );
            if (detalleSelected != undefined) {
                $("#modalImagenDetalle").modal("show");
                document.querySelector('#modalImagenContenidoDetalle').innerHTML = ` <img width="100%" height="100%"src="${getHostFrontEnd()}adjuntos/recurso/IMAGENES/${detalleSelected.archivo}"
                alt="user-picture" />`;
            } else {
                showAlertTopEnd("info", "Vacío!", "No se encontró el Recurso");
            }
        };
    });
    document.querySelectorAll('.btn-regresar-recurso').forEach((btn) => {
        //AGREGANDO EVENTO CLICK
        btn.onclick = function () {
            document.querySelector('#sectionRecursos').classList.remove("d-none");
            document.querySelector('#sectionDetalles').classList.add("d-none");
        };
    });

}



function downloadURL(url) {
    var hiddenIFrameID = 'hiddenDownloader',
        iframe = document.getElementById(hiddenIFrameID);
    if (iframe === null) {
        iframe = document.createElement('iframe');
        iframe.id = hiddenIFrameID;
        iframe.style.width = '100%';
        iframe.style.height = '100%';
        document.querySelector('#modalFrameContenidoDetalle').appendChild(iframe);
    }
    iframe.src = url;
};

function findIndexDetalle(idbusqueda) {
    return beanPaginationDetalle.list.findIndex(
        (Detalle) => {
            if (Detalle.iddetallerecurso == parseInt(idbusqueda))
                return Detalle;


        }
    );
}

function findByDetalle(iddetallerecurso) {
    return beanPaginationDetalle.list.find(
        (Detalle) => {
            if (parseInt(iddetallerecurso) == Detalle.iddetallerecurso) {
                return Detalle;
            }


        }
    );
}

