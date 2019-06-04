{foreach from=$modals item=modal}
	<div id="iziModal_{$modal->id}" class="iziModal">{$modal->content nofilter}</div>
	<script>
		$(document).ready(function() {

			var options = {$modal->getOptions()|json_encode nofilter};
			{if $modal->validation}
				options.onClosed = function() {
					$.post({
						url: "{$link->getModuleLink('webequip_modal', 'ajax')}",
						data: {
							ajax:true, 
							action:'updateCookie', 
							id:{$modal->id}
						}
					});
				}
			{/if}
			window.modal = $("#iziModal_{$modal->id}").iziModal(options);
		});
	</script>
{/foreach}

