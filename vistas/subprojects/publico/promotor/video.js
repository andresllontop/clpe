
var beanPaginationVideo;
var videoSelected;
var beanRequestVideo = new BeanRequest();

document.addEventListener('DOMContentLoaded', function () {
  beanRequestVideo.entity_api = 'video/promotores';
  beanRequestVideo.operation = 'ubicacion';
  beanRequestVideo.type_request = 'GET';

});

function processAjaxVideoPromotor() {
  let parameters_pagination = '';
  circleCargando.containerOcultar = $(document.querySelector("#tbodyVideoNosotros"));
  circleCargando.container = $(document.querySelector("#tbodyVideoNosotros").parentElement);
  circleCargando.createLoader();
  circleCargando.toggleLoader("show");
  switch (beanRequestVideo.operation) {
    default:
      parameters_pagination +=
        '?tipo=3';
      break;
  }
  $.ajax({
    url: getHostAPI() + beanRequestVideo.entity_api + "/" + beanRequestVideo.operation +
      parameters_pagination,
    type: beanRequestVideo.type_request,
    data: "",
    contentType: 'application/json; charset=UTF-8',
    dataType: 'json'
  }).done(function (beanCrudResponse) {
    circleCargando.toggleLoader("hide");
    // $('#modalCargandoVideo').modal('hide');
    if (beanCrudResponse.beanPagination !== null) {

      beanPaginationVideo = beanCrudResponse.beanPagination;
      listaVideoPromotor(beanPaginationVideo);
    }

  }).fail(function (jqXHR, textStatus, errorThrown) {
    $('#modalCargandoVideo').modal("hide");
    showAlertErrorRequest();

  });

}

function listaVideoPromotor(beanPagination) {
  document.querySelector('#tbodyVideoNosotros').innerHTML = '';
  let row = '';

  beanPagination.list.forEach((video) => {
    row += `
  <a href="javascript:void(0);" class="ver-video"  idvideo="${video.idvideo}"><img src="${getHostFrontEnd()}adjuntos/video-imagenes/${video.imagen}" alt="${video.nombre}">
  <span class="d-none"><i
  class="fa fa-play"></i></span>
  <div>${video.nombre}</div>
</a>
  
  `;

  });


  document.querySelector('#tbodyVideoNosotros').innerHTML = row;
  $('#dg-container').gallery({
    autoplay: true,
    interval: 4000
  });
  addEventsVideo();

}

function addEventsVideo() {
  document.querySelectorAll('.ver-video').forEach((btn) => {
    //AGREGANDO EVENTO CLICK
    btn.onclick = function () {
      videoSelected = findByVideo(
        btn.getAttribute('idvideo')
      );
      console.log(videoSelected);
      if (videoSelected != undefined) {

        document.querySelector("#txtvideoPromotor").innerHTML = videoSelected.enlace;

        $("#modalVideoPromotor").modal("show");

      } else {
        document.querySelector("#txtvideoPromotor").innerHTML = "";
        console.log(
          'warning',
          'No se encontró el video'
        );
      }
    };
  });
}


function findIndexVideo(idbusqueda) {
  return beanPaginationVideo.list.findIndex(
    (Video) => {
      if (Video.idvideo == parseInt(idbusqueda))
        return Video;


    }
  );
}
function findByVideo(idvideo) {
  return beanPaginationVideo.list.find(
    (Video) => {
      if (parseInt(idvideo) == Video.idvideo) {
        return Video;
      }


    }
  );
}


