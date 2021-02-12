"use strict";

(function ($) {
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

  $(".tooltips-general").tooltip("hide");

  $(".mobile-menu-button").on("click", function () {
    var mobileMenu = $(".navbar-lateral");
    if (mobileMenu.css("display") == "none") {
      mobileMenu.fadeIn(300);
    } else {
      mobileMenu.fadeOut(300);
    }
  });

  $(".mobile-menu-button-navbar").on("click", function () {
    var mobileMenu = $(".navbar-mobile");
    if (mobileMenu.css("display") == "none") {
      mobileMenu.fadeIn(300);
    } else {
      mobileMenu.fadeOut(300);
    }
  });

  var current_path = window.location.href;
  console.log(current_path);
  if (current_path == '') {
    current_path = 'index';
  }
  var $current_menu = $('a[href="' + current_path + '"]');
  //current_path = current_path.substring(getHostAPP().length - 1, current_path.length);

  $current_menu.parent().addClass('bg-purple');
  $current_menu.css("color", "white");
  if ($current_menu.length > 0) {
    $('.dt-side-nav__item').removeClass('open');
    if ($current_menu.parent().parent().parent().parent().parent().parents().hasClass('dt-side-nav__item')) {
      $current_menu.parent().parent().addClass('active').parents('.dt-side-nav__item').addClass('open selected');
      $current_menu.parent().parent().parent().parent().parent().parent()[0].style = "display:block";
      $current_menu.parent().parent().parent().parent()[0].style = "display:block";
      $current_menu.parent().parent()[0].style = "display:block";
    } else if ($current_menu.parent().parent().parent().parents().hasClass('dt-side-nav__item')) {
      $current_menu.parent().addClass('active').parents('.dt-side-nav__item').addClass('open selected');
      $current_menu.parent().parent().parent().parent()[0].style = "display:block";
      $current_menu.parent().parent()[0].style = "display:block";
    } else {
      // $current_menu.parents('.dt-side-nav__item').addClass('open selected');
      $current_menu.parent().parent()[0].style = "display:block";
    }
  }
  /*if ($current_menu.length > 0) {
    $('.dt-side-nav__item').removeClass('open');
    console.log($current_menu.parent().parent().parent().parents());
    if ($current_menu.parent().parent().parent().parents().hasClass('dt-side-nav__item')) {
      $current_menu.parent().addClass('active').parents('.dt-side-nav__item').addClass('open selected');
      $current_menu.parent().parent().parent().parent()[0].style = "display:block";
      $current_menu.parent().parent()[0].style = "display:block";
    } else {
      // $current_menu.parents('.dt-side-nav__item').addClass('open selected');
      $current_menu.parent().parent()[0].style = "display:block";
    }
  }
*/
  var slideDuration = 150;
  $("ul.dt-side-nav > li.dt-side-nav__item").on("click", function () {
    var menuLi = this;
    $("ul.dt-side-nav > li.dt-side-nav__item").not(menuLi).removeClass("open");
    $("ul.dt-side-nav > li.dt-side-nav__item ul").not($("ul", menuLi)).slideUp(slideDuration);

    $(" > ul", menuLi).slideToggle(slideDuration, function () {
      $(menuLi).toggleClass("open");
    });
  });

  $("ul.dt-side-nav__sub-menu li").on('click', function (e) {
    var $current_sm_li = $(this);
    var $current_sm_li_parent = $current_sm_li.parent();

    if ($current_sm_li_parent.parent().hasClass("active")) {
      $("li ul", $current_sm_li_parent).not($("ul", $current_sm_li)).slideUp(slideDuration, function () {
        $("li", $current_sm_li_parent).not($current_sm_li).removeClass("active");
      });

    } else {
      $("ul.dt-side-nav__sub-menu li ul").not($(" ul", $current_sm_li)).slideUp(slideDuration, function () {
        //$("ul.sub-menu li").not($current_sm_li).removeClass("active");console.log('has not parent');
      });
    }

    $(" > ul", $current_sm_li).slideToggle(slideDuration, function () {
      $($current_sm_li).toggleClass("active");
    });

    e.stopPropagation();
  });


  $(".dropdown-menu-button").on("click", function () {
    var dropMenu = $(this).next("ul");
    dropMenu.slideToggle("slow");
  });




})(jQuery);

