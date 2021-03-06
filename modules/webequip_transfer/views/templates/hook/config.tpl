<form id="form" method="post">
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i> {l s="Configuration" mod="webequip_transfer"}
			<div class="panel-heading-action">
				<a href="" id="save_configuration" class="list-toolbar-btn" title="{l s="Save" d='Shop.Theme.Actions'}">
					<i class="process-icon-save"></i>
				</a>
			</div>
		</div>
		<div class="row">
			{foreach from=$configs item=config}
				<div class="col-lg-{math equation="12 / x" x=$configs|count}">
					<div class="form-group">
						<label for="{$config.name}">{$config.label}</label>
						<input type="{$config.type}" class="form-control" name="{$config.name}" value="{$config.value}">
					</div>
				</div>
			{/foreach}
		</div>
	</div>
</form>

{if $is_configured}
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i> {l s="Gestion des transfert" mod="webequip_transfer"}
			<div class="panel-heading-action">
				<a href="" id="refresh_actions" class="list-toolbar-btn" title="{l s='Réessayer' mod="webequip_transfer"}">
					<i class="process-icon-refresh"></i>
				</a>
			</div>
		</div>
		<div id="loading">
			<div class="alert alert-info">
				<i class="icon-spinner icon-spin"></i> &nbsp; Chargement en cours...
			</div>
		</div>
		<div id="ajax_result">
			
		</div>
	</div>
{else}
	<div class="alert alert-info">
		{l s="La BDD n'est pas configurée" mod="webequip_transfer"}
	</div>
{/if}

<div class="row">
	<div class="col-lg-3" id="check_customers">

	</div>
	<div class="col-lg-3" id="check_orders">

	</div>
	<div class="col-lg-3" id="check_carts">

	</div>
</div>

<script>
	var is_configured = {$is_configured};
	$(document).ready(function() {

		$('#save_configuration').on('click', function(e) {
			
			e.preventDefault();
			$(this).closest('form').submit();
		});

		$('#refresh_actions').on('click', function(e) {
			
			e.preventDefault();
			loadActions();
		});

		loadData('customers', false);
		loadData('orders', false);
		loadData('carts', false);

		$(document).on('click', '.load-data', function(e) {
			e.preventDefault();
			loadData($(this).data('type'), $(this).data('update'));
		});

		if(is_configured) {
			loadActions();
		}

		$(document).on('submit', '#transfer_form', function(e) {
			e.preventDefault();

			$('#loading').show();
			$('#ajax_result').hide();

			$.post(
				"{$link->getAdminLink('AdminModules')}&configure=webequip_transfer",
				$(this).serialize(), 
				function(response) {
					$('#ajax_result').html(response);
					$('#loading').hide();
					$('#ajax_result').show();
				}
			);
		});

	});

	function loadActions() {

		$('#loading').show();
		$('#ajax_result').hide();

		$.post(
			"{$link->getAdminLink('AdminModules')}&configure=webequip_transfer", {
				ajax:true, 
				action: 'load_transfer'
			}, 
			function(response) {
				$('#ajax_result').html(response);
				$('#loading').hide();
				$('#ajax_result').show();
			}
		);
	}

	function loadData(type, update) {
		$('#refresh_'+type).addClass('icon-spin');
		$.post(
			"{$link->getAdminLink('AdminModules')}&configure=webequip_transfer", {
				ajax:true, 
				action: 'load_'+type,
				update: update,
				skip_test: true
			},
			function(response) {
				$("#check_"+type).html(response);
			}
		);
	}

</script>