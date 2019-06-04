$(document).ready(function() {

	$('#partners').slick({
    	slidesToShow: 5,
    	slidesToScroll: 1,
    	autoplay: true,
    	autoplaySpeed: 1000,
    	arrows: false,
    	dots: false,
    	pauseOnHover: true,
    	responsive: [{
      		breakpoint: 768,
      		settings: {
        		slidesToShow: 4
      		}
    	}, {
      		breakpoint: 520,
      		settings: {
        		slidesToShow: 3
      		}
    	}]
  	});

});