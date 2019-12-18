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
		color: white !important;
	}
	.text-success { color: #28a745 !important; }
	.text-danger { color: #dc3545 !important; }
</style>

{capture name='taxes'}
	{if $use_taxes}
		<em class="label label-danger"><b>TTC</b></em>
	{else}
		<em class="label label-success"><b>HT</b></em>
	{/if}
{/capture}

<div class="panel">
	<form method="post">
		<div class="row">
			<div class="col-lg-3">
				<div class="input-group">
					<span class="bg-dark input-group-addon"><b>Semaine</b></span>
					<select class="selected_week" name="selected_week">
						{foreach from=$dates key=x item=$date}
							<option value="{$x}" {if $x == $selected_week}selected{/if}>{$x} - Du {$date.begin} au {$date.end}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="col-lg-1">
				<div class="input-group">
					<span class="bg-dark input-group-addon"><b>Prix</b></span>
					<select name="selected_taxes">
						<option value="0" {if !$use_taxes}selected{/if}>HT</option>
						<option value="1" {if $use_taxes}selected{/if}>TTC</option>
					</select>
				</div>
			</div>
			<div class="col-lg-2">
				<button type="submit" class="btn btn-success">
					<i class="icon-check-square"></i> &nbsp; <b>Valider</b>
				</button>
			</div>
		</div>
	</form>
</div>

<div class="row">

	<div class="col-lg-3">
		
		<div class="panel text-center">
			<div class="row">
				<div class="col-lg-6">
					<h2>{$nb_orders}</h2>
					<div>Commandes</div>
				</div>
				<div class="col-lg-6">
					<h2>{$nb_references}</h2>
					<div>Références</div>
				</div>
			</div>
		</div>

		<div class="panel text-center">
			<div class="row">
				<div class="col-lg-6">
					<h2>{displayPrice price=$turnover_products}</h2>
					<div>CA naturel {$smarty.capture.taxes}</div>
				</div>
				<div class="col-lg-6">
					<h2>{displayPrice price=$turnover_quotations}</h2>
					<div>CA devis {$smarty.capture.taxes}</div>
				</div>
			</div>
		</div>

		<div class="panel text-center">
			<h2>{displayPrice price=$turnover_avg}</h2>
			<div>Panier moyen {$smarty.capture.taxes}</div>
		</div>

		<div class="panel text-center">
			<div class="row">
				<div class="col-lg-6">
					<h2>{$margin_natural} %</h2>
					<div>Taux naturel</div>
				</div>
				<div class="col-lg-6">
					<h2>{$margin_quotation} %</h2>
					<div>Taux devis</div>
				</div>
			</div>
		</div>

	</div>

	<div class="col-lg-9">

		<table class="table">
			<tr class="bg-dark">
				<th colspan="{$objectives|count}">Objectif total : {displayPrice price=$total_objective}</th>
			<tr>
				{foreach from=$objectives item=objective}
					<th class="bg-primary text-center">{$objective->date|date_format:'d/m/Y'}</th>
				{/foreach}
			</tr>
			<tr>
				{foreach from=$objectives item=objective}
					<td class="text-center">{displayPrice price=$objective->value}</td>
				{/foreach}
			</tr>
		</table>

		<div class="row" style="margin-top:15px">
			
			<div class="col-lg-6">
				<table class="table">
					<tr class="bg-dark">
						<th colspan="2">
							Réductions utilisées
							<span class="pull-right">{$smarty.capture.taxes}</span>
						</th>
					</tr>
					{foreach from=$cart_rules item=rule}
						<tr>
							<th class="bg-primary">{$rule.name}</th>
							<td class="text-center">{displayPrice price=$rule.value}</td>
						</tr>
					{foreachelse}
						<tr class="text-center">
							<td colspan="2" class="text-danger">Aucune réduction n'a été utilisée cette semaine</td>
						</tr>
					{/foreach}
				</table>
			</div>

			<div class="col-lg-6">
				<table class="table">
					<tr class="bg-dark">
						<th colspan="2">
							Chiffre d'affaire des options de commande 
							<span class="pull-right">{$smarty.capture.taxes}</span>
						</th>
					</tr>
					{foreach from=$options item=row}
						<tr>
							<th class="bg-primary">{$row.option->name}</th>
							<td class="text-center">{displayPrice price=$row.turnover}</td>
						</tr>
					{foreachelse}
						<tr class="text-center">
							<td colspan="2" class="text-danger">Aucune option n'a été commandée cette semaine</td>
						</tr>
					{/foreach}
				</table>
			</div>

		</div>

	</div>

</div>