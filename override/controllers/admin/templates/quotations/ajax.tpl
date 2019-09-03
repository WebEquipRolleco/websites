<div id="ajax_result"></div>

<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
	$(document).on('ready', function() {

		$('.send-mail').on('click', function(e) {
			e.preventDefault();

			$('#modal_send').modal('show');
			//$('#id_quotation').val($(this).val());
		});

		$('.send-mail').on('click', function(e) {
			
			e.preventDefault();
			var id_quotation = $(this).val();

			$.ajax({
				url: "{$link->getAdminLink('AdminQuotations')}",
				data: { ajax:true, action:"contact_modal", id_quotation:id_quotation },
				success : function(response) {
					$('#ajax_result').html(response);
				}
			});
		});

	});
</script>