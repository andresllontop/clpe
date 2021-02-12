var circleCargando = {
    container: $('.dt-module__content'),
    containerOcultar: $('.dt-module__content-inner'),
    loader: null,
    createLoader: function () {
        var svgHtml = `
        
        <div class="loader" style="width: 80px;
        height: 80px;">
          <div class="loader">
              <div class="loader">
                 <div class="loader">
    
                 </div>
              </div>
            
          </div>
        </div>
       `;
        this.loader = document.createElement('div');
        this.loader.className = 'dt-loader';
        this.loader.innerHTML = svgHtml;
        this.container.append(this.loader);
        this.toggleLoader('hide');
    },
    toggleLoader: function (display) {
        if (this.loader) {
            if (display) {
                if (display == 'show') {
                    this.containerOcultar.addClass('d-none');
                    $(this.loader).removeClass('d-none');
                } else {
                    this.containerOcultar.removeClass('d-none');
                    $(this.loader).addClass('d-none');
                }
            } else {
                this.containerOcultar.toggleClass('d-none');
                $(this.loader).toggleClass('d-none');
            }
        }
    }
};
$(document).ready(function () {

    var $body = $('body');
    var $loader = $('.dt-loader-container');
    var $root = $('.dt-root');

    if ($loader.length) {
        $loader.delay(300).fadeOut('noraml', function () {
            $body.css('overflow', 'auto');
            $root.css('opacity', '1');
            $(document).trigger('loader-hide');
        });
    } else {

        $(document).trigger('loader-hide');
    }



    $("#btn-video").click(function () {
        $("#videoHome").modal("show");
    });

    $("#btn-matricula").click(function () {
        $("#matricula").modal("show");
        $("#formularioAdmin").attr("data-form", "save");
        // $("#Codigo-reg").val(
        //   $(this.parentElement.parentElement).attr("codigo")
        // );
    });
    $("#btn-restablecer").click(function () {
        $("#logear").modal("hide");
        $("#restabler-datos").modal("show");
    });
    $(".boot-registrar").click(function () {
        if ("none" == $(".bootRegistrar").css("display")) {
            $(".bootRegistrar").css("display", "block");
        } else {
            $(".bootRegistrar").css("display", "none");
        }
    });
    $(".close").click(function () {
        $(".bootRegistrar").css("display", "none");
    });


});
