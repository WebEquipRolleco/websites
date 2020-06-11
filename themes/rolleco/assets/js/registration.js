$(document).ready(function() {

    $('#email').prop('pattern', '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$');
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

            if(element.data(name) == 2) {
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