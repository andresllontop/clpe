

  <!-- /WhatsHelp.io widget -->
<script >
const url="<?php echo SERVERURL; ?>";
</script>

<!--script language="Javascript">
document.oncontextmenu = function(){return false}
</script>
<!-- <SCRIPT>
    function Cerrar() {
       console.log("Se va a cerrar la ventana");
      //  event.returnValue = "Te estás saliendo del sitio…";
    }
</SCRIPT> -->
  
<script language="javascript" src="<?php echo SERVERURL; ?>vistas/publico/Dale/js/jquery-1.11.0.min.js"></script>
  <!-- Bootstrap core JavaScript -->
    <script language="javascript" src="<?php echo SERVERURL; ?>vistas/publico/js/bootstrap.min.js"></script>
    <!-- Easing core JavaScript -->
	<script language="javascript" src="<?php echo SERVERURL; ?>vistas/publico/Dale/js/jquery.easing.1.3.js"></script>
	
    <!-- Master slider core JavaScript -->
	<script language="javascript" src="<?php echo SERVERURL; ?>vistas/publico/Dale/js/masterslider.min.js"></script>
    <!-- Master slider staff core JavaScript -->
    <script language="javascript" src="<?php echo SERVERURL; ?>vistas/publico/Dale/js/masterslider.staff.carousel.dev.js"></script>
    <!-- WOW core JavaScript -->
    <script language="javascript" src="<?php echo SERVERURL; ?>vistas/publico/Dale/js/wow.min.js"></script>
    <!-- Waypoints core JavaScript -->
	<script language="javascript" src="<?php echo SERVERURL; ?>vistas/publico/Dale/js/waypoints.min.js"></script>
    <!-- Underscore core JavaScript -->
    <script language="javascript" src="<?php echo SERVERURL; ?>vistas/publico/Dale/js/underscore-min.js"></script>
    <!-- jQuery Backstretch core -->
    <script language="javascript" src="<?php echo SERVERURL; ?>vistas/publico/Dale/js/jquery.backstretch.min.js"></script>
    <!-- jQuery color core JavaScript -->
    <script language="javascript" src="<?php echo SERVERURL; ?>vistas/publico/Dale/js/jquery.animation.js"></script>
    <!-- Isotope core JavaScript -->
    <script language="javascript" src="<?php echo SERVERURL; ?>vistas/publico/Dale/js/jquery.isotope.min.js"></script>
	<!-- Stellar core JavaScript -->
    <script language="javascript" src="<?php echo SERVERURL; ?>vistas/publico/Dale/js/jquery.stellar.min.js"></script>
    <!-- Contact core JavaScript -->
    <script language="javascript" src="<?php echo SERVERURL; ?>vistas/publico/Dale/js/jquery.contact.min.js"></script>
<script language="javascript" src="<?php echo SERVERURL; ?>vistas/publico/Dale/js/main.js"></script>
    <!-- NiceScroll core Javascript -->
    <script language="javascript" src="<?php echo SERVERURL; ?>vistas/publico/Dale/js/jquery.nicescroll.min.js"></script>
    <!-- Retina core JavaScript -->
    <script language="javascript" src="<?php echo SERVERURL; ?>vistas/publico/Dale/js/retina-1.1.0.min.js"></script>
    <!-- Nivo Slider JavaScript -->
    <script language="javascript" src="<?php echo SERVERURL; ?>vistas/publico/Dale/js/jquery.nivo.slider.pack.js"></script>
    <!-- Video core JavaScript -->
    <script language="javascript" src="<?php echo SERVERURL; ?>vistas/publico/Dale/js/video.js"></script>
    <!-- OWL Carousel core JavaScript -->
    <script language="javascript" src="<?php echo SERVERURL; ?>vistas/publico/Dale/js/owl.carousel.min.js"></script>
    <!-- twitterfeed core JavaScript -->
    <!-- <script language="javascript" src="<?php echo SERVERURL; ?>vistas/publico/Dale/js/jquery.twitterfeed.js"></script> -->
    <!-- Lightbox core JavaScript -->
    <script language="javascript" src="<?php echo SERVERURL; ?>vistas/publico/Dale/js/lightbox.min.js"></script>
    <script language="javascript" src="<?php echo SERVERURL; ?>vistas/publico/Dale/js/functions.js"></script>
  	<!-- Everything else -->
  
    <script language="javascript" src="<?php echo SERVERURL; ?>vistas/publico/Dale/js/custom.js"></script>
    <script language="javascript" src="<?php echo SERVERURL; ?>vistas/publico/Dale/js/sweet-alert.min.js"></script>
    <script language="javascript" src="<?php echo SERVERURL; ?>vistas/publico/Dale/js/main.js"></script>
    <script language="javascript" src="<?php echo SERVERURL; ?>vistas/app-js/login.js"></script>


    <script type="text/javascript" language="javascript">
	<!--
	jQuery(document).ready(function($) {

		/************************
		****** MasterSlider *****
		*************************/
		// Calibrate slider's height
		var sliderHeight = 790; // Smallest hieght allowed (default height)
		if ( $('#masterslider').data('height') == 'fullscreen' ) {
			var winHeight = $(window).height();
			sliderHeight = winHeight > sliderHeight ? winHeight : sliderHeight;
		}

		// Initialize the main slider
		var slider = new MasterSlider();
		slider.setup('masterslider', {
			space:0,
			fullwidth:true,
			autoplay:true,
			overPause:false,
			width:1024,
			height:sliderHeight
		});
		// adds Arrows navigation control to the slider.
		slider.control('bullets',{autohide:false  , dir:"h"});

		var teamslider = new MasterSlider();
		teamslider.setup('teamslider' , {
			loop:true,
			width:300,
			height:290,
			speed:20,
			view:'stffade',
			grabCursor:false,
			preload:0,
			space:29
		});
		teamslider.control('slideinfo',{insertTo:'#staff-info'});

		$(".team .ms-nav-next").click(function() {
			teamslider.api.next();
		});

		$(".team .ms-nav-prev").click(function() {
			teamslider.api.previous();
		});
	});


	// -->
	</script>