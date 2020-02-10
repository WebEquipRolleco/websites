$(document).ready(function() {

    $("#siret").prop('pattern', '.{14}');
    $("#siret").prop('title', "14 caractères");

    checkTypeRequirements();
    $('#id_account_type').on('change', function() {
    	checkTypeRequirements();
    });
    
});

function checkTypeRequirements() {

    var element = $('#id_account_type option:selected');
    $('.cw').remove();

    ['company', 'siret', 'tva', 'chorus'].forEach(function(name) {
        if(element.data(name)) {
        	$('#'+name+'_area').show();

            if(element.val() == 2 && jQuery.inArray(name, ['company', 'siret', 'tva']) >= 0) {
                $('#'+name+'_area').find('label').append("<em class='cw text-danger'>*</em>");
                $('#'+name).prop('required', true);
            }

            if(element.val() == 3 && jQuery.inArray(name, ['company', 'siret']) >= 0) {
                $('#'+name+'_area').find('label').append("<em class='cw text-danger'>*</em>");
                $('#'+name).prop('required', true);
            }
        }
        else {
            $('#'+name).prop('required', false);
            $('#'+name+'_area').hide();
        }
    });
}