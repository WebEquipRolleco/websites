$(document).ready(function() {
	updateSameAddress();

	$('.change-address').on('change', function() {
		updateSameAddress();
	});

	$('#new_delivery_address').on('click', function() {
		$('input[name=use_same_address]').val(1);
	});
	
	function updateSameAddress() {

		var id_address_invoice = $('input[name=id_address_invoice]').val();
		var id_address_delivery = $('input[name=id_address_delivery]:checked').val();

		if(id_address_invoice == id_address_delivery)
			$('input[name=use_same_address]').val(1);
		else
			$('input[name=use_same_address]').val(0);

		
	}

});