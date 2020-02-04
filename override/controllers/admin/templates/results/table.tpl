<div class="panel no-print">
<div class="row">
	<div class="col-lg-3">
		<span class="text-muted"><b>Date de début</b></span>
		<input type="date" class="form-control" name="dates[{$index}][begin]" value="{$dates.begin->format('Y-m-d')}">
	</div>
	<div class="col-lg-3">
		<span class="text-muted"><b>Date de fin</b></span>
		<input type="date" class="form-control" name="dates[{$index}][end]" value="{$dates.end->format('Y-m-d')}">
	</div>
	<div class="col-lg-3">
		<div class="text-muted">&nbsp;</div>
		<button type="submit" class="btn btn-success">
			<i class="icon-check-square"></i> &nbsp; <b>Valider</b>
		</button>
	</div>
</div>
</div>

<table class="table">
	<thead>
		<tr class="bg-dark">
			<td colspan="5" class="text-center">{$title}</td>
		</tr>
		<tr class="bg-primary">
			<td width="30%"></td>
			<td colspan="2" class="text-center">
				<b>Courante</b></td>
			<td colspan="2" class="text-center">
				<b>Année précédente</b>
			</td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><b>{l s="Commandes"}</b></td>
			<td colspan="2" class="text-center">{$current.nb_orders|default:0}</td>
			<td class="text-center">{$last.nb_orders|default:0}</td>
			{if $best.nb_orders}
				<td class="text-center text-success">+{$rate.nb_orders|round:2} %</td>
			{else}
				<td class="text-center text-danger">-{$rate.nb_orders|round:2} %</td>
			{/if}
		</tr>
		<tr>
			<td><b>{l s="CA"}</b></td>
			<td colspan="2" class="text-center">{convertPrice price=$current.turnover}</td>
			<td class="text-center">{convertPrice price=$last.turnover}</td>
			{if $best.turnover}
				<td class="text-center text-success">+{$rate.turnover|round:2} %</td>
			{else}
				<td class="text-center text-danger">-{$rate.turnover|round:2} %</td>
			{/if}
		</tr>
		<tr>
			<td><b>{l s="Panier moyen"}</b></td>
			<td colspan="2" class="text-center">{convertPrice price=$current.avg}</td>
			<td class="text-center">{convertPrice price=$last.avg}</td>
			{if $best.avg}
				<td class="text-center text-success">+{$rate.avg|round:2} %</td>
			{else}
				<td class="text-center text-danger">-{$rate.avg|round:2} %</td>
			{/if}
		</tr>
		<tr>
			<td><b>{l s="Objectif"}</b></td>
			<td colspan="2" class="text-center">{convertPrice price=$current.objective}</td>
			<td class="text-center">{convertPrice price=$last.objective}</td>
			{if $best.objective}
				<td class="text-center text-success">+{$rate.objective|round:2} %</td>
			{else}
				<td class="text-center text-danger">-{$rate.objective|round:2} %</td>
			{/if}
		</tr>
		<tr>
			<td><b>{l s="Différence objectif"}</b></td>
			<td colspan="2" class="text-center">{convertPrice price=$current.difference}</td>
			<td class="text-center">{convertPrice price=$last.difference}</td>
			{if $best.difference}
				<td class="text-center text-success">+{$rate.difference|round:2} %</td>
			{else}
				<td class="text-center text-danger">-{$rate.difference|round:2} %</td>
			{/if}
		</tr>
		<tr>
			<td><b>{l s="Marges totale"}</b></td>
			<td class="text-center">{convertPrice price=$current.margin_value.full}</td>
			<td class="text-center">{$current.margin.full|round:2} %</td>
			<td class="text-center">{$last.margin.full|round:2} %</td>
			{if $best.margin.full}
				<td class="text-center text-success">+{$rate.margin.full|round:2} %</td>
			{else}
				<td class="text-center text-danger">-{$rate.margin.full|round:2} %</td>
			{/if}
		</tr>
		<tr>
			<td><b>{l s="Marges naturelles"}</b></td>
			<td class="text-center">{convertPrice price=$current.margin_value.products}</td>
			<td class="text-center">{$current.margin.products|round:2} %</td>
			<td class="text-center">{$last.margin.products|round:2} %</td>
			{if $best.margin.products}
				<td class="text-center text-success">+{$rate.margin.products|round:2} %</td>
			{else}
				<td class="text-center text-danger">-{$rate.margin.products|round:2} %</td>
			{/if}
		</tr>
	</tbody>
</table>

{assign var=nb_cols value=($types|@count + 1)}
<table class="table" style="margin-top:15px">
	<thead>
		<tr class="bg-dark">
			<td colspan="{$nb_cols}" class="text-center">
				{l s="Répartition des commandes"}
			</td>
		</tr>
		<tr class="bg-primary">
			<td width="30%">&nbsp</td>
			{foreach from=$types item=type}
				<td class="text-center">
					<b>{$type.name}</b>
				</td>
			{/foreach}
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><b>{l s="commandes"}</b></td>
			{foreach from=$types item=type}
				<td class="text-center">{$type.nb_orders|default}</td>
			{/foreach}
		</tr>
		<tr>
			<td><b>{l s="CA"}</b></td>
			{foreach from=$types item=type}
				<td class="text-center">{convertPrice price=$type.turnover}</td>
			{/foreach}
		</tr>
		<tr>
			<td><b>{l s="Commande moyenne"}</b></td>
			{foreach from=$types item=type}
				<td class="text-center">{convertPrice price=$type.avg}</td>
			{/foreach}
		</tr>
	</tbody>
</table>

<table class="table" style="margin-top:15px">
	<thead>
		<tr class="bg-dark">
			<td colspan="5" class="text-center">
				{l s="Devis"}
			</td>
		</tr>
		<tr class="bg-primary">
			<td width="30%"></td>
			<td colspan="2" class="text-center">
				<b>Courante</b>
			</td>
			<td colspan="2" class="text-center">
				<b>Année précédente</b>
			</td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><b>{l s="Commandes"}</b></td>
			<td colspan="2" class="text-center">{$current.quotations.nb_orders}</td>
			<td class="text-center">{$last.quotations.nb_orders}</td>
			{if $best.quotations.nb_orders}
				<td class="text-center text-success">+{$rate.quotations.nb_orders|round:2} %</td>
			{else}
				<td class="text-center text-danger">-{$rate.quotations.nb_orders|round:2} %</td>
			{/if}
		</tr>
		<tr>
			<td><b>{l s="CA"}</b></td>
			<td colspan="2" class="text-center">{convertPrice price=$current.quotations.turnover}</td>
			<td class="text-center">{convertPrice price=$last.quotations.turnover}</td>
			{if $best.quotations.turnover}
				<td class="text-center text-success">+{$rate.quotations.turnover|round:2} %</td>
			{else}
				<td class="text-center text-danger">-{$rate.quotations.turnover|round:2} %</td>
			{/if}
		</tr>
		<tr>
			<td><b>{l s="Devis moyen"}</b></td>
			<td colspan="2" class="text-center">{convertPrice price=$current.quotations.avg}</td>
			<td class="text-center">{convertPrice price=$last.quotations.avg}</td>
			{if $best.quotations.avg}
				<td class="text-center text-success">+{$rate.quotations.avg|round:2} %</td>
			{else}
				<td class="text-center text-danger">-{$rate.quotations.avg|round:2} %</td>
			{/if}
		</tr>
		<tr>
			<td><b>{l s="Marges devis"}</b></td>
			<td class="text-center">{convertPrice price=$current.margin_value.quotations}</td>
			<td class="text-center">{$current.margin.quotations|round:2} %</td>
			<td class="text-center">{$last.margin.quotations|round:2} %</td>
			{if $best.margin.quotations}
				<td class="text-center text-success">+{$rate.margin.quotations|round:2} %</td>
			{else}
				<td class="text-center text-danger">-{$rate.margin.quotations|round:2} %</td>
			{/if}
		</tr>
	</tbody>
</table>

<table class="table" style="margin-top:15px">
	<thead>
		<tr class="bg-dark">
			<td colspan="5" class="text-center">{l s="Methodes de paiement"}</td>
		</tr>
		<tr class="bg-primary">
			<td width="30%"></td>
			<td colspan="2" class="text-center">
				<b>Courante</b>
			</td>
			<td colspan="2" class="text-center">
				<b>Année précédente</b>
			</td>
		</tr>
	</thead>
	<tbody>
		{foreach from=$methods item=method}
			<tr>
				<td><b>{$method.name}</b></td>
				<td colspan="2" class="text-center">{convertPrice price=$method.current.turnover}</td>
				<td class="text-center">{convertPrice price=$method.last.turnover}</td>
				{if $method.best.turnover}
					<td class="text-center text-success">+{$method.rate.turnover|round:2} %</td>
				{else}
					<td class="text-center text-danger">-{$method.rate.turnover|round:2} %</td>
				{/if}
			</tr>
		{/foreach}
	</tbody>
</table>