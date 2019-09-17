$(document).ready(function() {
      
    checkTypeRequirements();
    $('#id_account_type').on('change', function() {
    	checkTypeRequirements();
    });

});

function checkTypeRequirements() {

    var element = $('#id_account_type option:selected');

    ['company', 'siret', 'tva', 'chorus'].forEach(function(name) {
        if(element.data(name)) {
        	$('#'+name+'_area').show();
            $('#'+name).prop('required', true);
        }
        else {
            $('#'+name).prop('required', false);
            $('#'+name+'_area').hide();
        }
    });
}