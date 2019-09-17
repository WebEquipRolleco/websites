$(document).ready(function() {

    $("form[name=register_newsletter]").on('submit', function(e) {
        e.preventDefault();

        var url = $('#newsletter_url').val();
        var email = $(this).find('input[name=email]').val();

        $('#ajax_modal_newsletter').iziModal({
            headerColor: "#00924b",
            icon: 'fa fa-envelope-open-text',
            title: "Newsletter Rolléco",
            subtitle: "Votre inscription est en cours",
            padding: "15px",
            closeButton: false,
            overlayClose: false,
            autoOpen: 1,
            timeout: 30000,
            timeoutProgressbar: true,
            onOpening: function(modal) {
            	$.post({
					url: url,
                    dataType: "json",
					data: {
						ajax:true, 
						action:'registration', 
						email: email
                    },
					success: function(response) {
							
                        modal.setHeaderColor(response.headerColor);
                        modal.setIcon(response.icon);
                        modal.setSubtitle(response.subtitle);
                        modal.setContent(response.content);
                        modal.resetProgress();

                        if(response.disable_form) {
                            $("form[name=register_newsletter]").find('button').addClass('disabled');
                            $("form[name=register_newsletter]").find('button').attr('title', "Vous êtes déjà inscrit à la newsletter !");
                        }
					}
				});
            },
        });
    });

});