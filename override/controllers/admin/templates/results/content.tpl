<style>
	@media print {    
    	#header, .multishop_toolbar, #footer, .no-print {
        	display: none !important;
    	}
    	.print-new-page {
    		page-break-before: always;
    	}
	}

	.bg-dark {
		background-color: #343a40 !important;
		text-transform: uppercase;
		font-weight: bold;
		color: white;
	}
	.text-success { color: #28a745 !important; }
	.text-danger { color: #dc3545 !important; }
</style>

<div class="row">
	<div class="col-lg-4">
		<table class="table">
			<thead>
				<tr class="bg-dark">
					<td colspan="3" class="text-center">{l s="Objectifs journaliers"}</td>
				</tr>
				<tr>
					<td class="text-center"><b>{l s="Commandes"}</b></td>
					<td class="text-center"><b>{l s="CA"}</b></td>
					<td class="text-center"><b>{l s="Objectif"}</b></td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="text-center" style="padding:10px">{$today.nb_orders}</td>
					<td class="text-center" style="padding:10px">{convertPrice price=$today.turnover}</td>
					<td class="text-center" style="padding:10px">{convertPrice price=$today.objective}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-lg-2 no-print">
		<form id="period_limit" method="post">
			<table class="table">
				<thead>
					<tr class="bg-dark">
						<td class="text-center text-light">
							<b>Arrêter les dates</b>
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="bg-light">
							<select id="change_period_limit" class="form-control" name="CONFIG_RESULTS_PERIOD_LIMIT">
								<option value="1" {if $date_limit == 1}selected{/if}>Période complète</option>
								<option value="2" {if $date_limit == 2}selected{/if}>Date du jour</option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
</div>

<form method="post">
	{foreach $tables as $index => $table}
		<div class="col-lg-6 print-new-page" style="display:inline-block; float:left">
			{$table nofilter}
		</div>
	{/foreach}
</form>

<script>
	$(document).ready(function() {

		$('#change_period_limit').on('change', function() {
			$('#period_limit').submit();
		});
	});
</script>