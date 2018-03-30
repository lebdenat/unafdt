
(function ($) {

	var resizeTimer;
	function resizeColorBox(){
		if(resizeTimer){ clearTimeout(resizeTimer); }
		resizeTimer = setTimeout(function(){
			if($('#cboxOverlay').is(':visible')){
				$.colorbox.resize({width:'90%', height:'90%'});
			}
		}, 300)
	}

	$(window).resize(resizeColorBox);
	window.addEventListener("orientationchange", resizeColorBox, false);

})(jQuery);