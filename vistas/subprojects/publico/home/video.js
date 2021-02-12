var beanPaginationVideo;
var videoSelected;
var beanRequestVideo = new BeanRequest();
document.addEventListener('DOMContentLoaded', function () {
  beanRequestVideo.entity_api = 'video/promotor';
  beanRequestVideo.operation = 'ubicacion';
  beanRequestVideo.type_request = 'GET';

});

function processAjaxVideoPromotor() {
  let parameters_pagination = '';
  circleCargando.containerOcultar = $(document.querySelector("#txtVideoHome"));
  circleCargando.container = $(document.querySelector("#txtVideoHome").parentElement);
  circleCargando.createLoader();
  circleCargando.toggleLoader("show");
  switch (beanRequestVideo.operation) {
    default:
      parameters_pagination +=
        '?tipo=1';
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
    $('#modalCargandoPromotor').modal('hide');
    if (beanCrudResponse.beanPagination !== null) {

      beanPaginationVideo = beanCrudResponse.beanPagination;
      listaVideoPromotor(beanPaginationVideo);
    }
    processAjaxFooterPublico();
  }).fail(function (jqXHR, textStatus, errorThrown) {
    $('#modalCargandoPromotor').modal("hide");
    showAlertErrorRequest();

  });

}

function listaVideoPromotor(beanPagination) {
  document.querySelector('#txtVideoHome').innerHTML = '';
  if (beanPagination.countFilter == 0) {
    document.querySelector('#txtVideoHome').parentElement.classList.remove("col-md-8");
    document.querySelector('#txtVideoHome').parentElement.classList.remove("col-lg-8");
    document.querySelector('#txtVideoHome').parentElement.classList.remove("col-sm-8");
    document.querySelector('#txtVideoHome').parentElement.classList.add("col-sm-4");
    return;
  }
  beanPagination.list.forEach((video) => {
    videoSelected = video;
  });

  document.querySelector('#txtVideoHome').innerHTML = ` <video class="fm-video video-js vjs-16-9 vjs-big-play-centered" data-setup="{}" controls id="fm-video">
  <source src="${getHostFrontEnd() + "adjuntos/videos/" + videoSelected.archivo}" type="video/mp4">
</video>
  `;
  var reproductor = videojs('fm-video', {
    fluid: true
  });
  //smartVideo();
}
function addEventsButtonsAdmin() {
  $(".editar-Admin").each(function (index, value) {
    $(this).click(function () {
      var indice = $(
        this.parentElement.parentElement.parentElement.parentElement
          .parentElement.parentElement.parentElement
      ).attr("numero");
      $("#ventanaModalvideotest").modal("show");
      $("#Modal-video").html(
        `<div
        class="flowplayer-embed-container"
        style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width:100%;">
        <iframe
          style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
          webkitAllowFullScreen mozallowfullscreen allowfullscreen
          src="${indice}"
          title="0" byline="0" portrait="0"
          width="100%" height="100%"
          frameborder="0"
          allow="autoplay">
        </iframe>
      </div>`
      );
    });
  });
}
function smartVideo() {
  let videos = document.querySelectorAll("video[data-smart-video]");
  const cb = function (entries) {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.play();
      } else {
        entry.target.pause();
      }
      window.addEventListener("visibilitychange", (e) => {
        document.visibilityState == "visible" ? entry.target.play() : entry.target.pause();
      });
    });
  };
  let observer = new IntersectionObserver(cb, { threshold: 0.5 });
  videos.forEach(element => {
    observer.observe(element);
  });
}
