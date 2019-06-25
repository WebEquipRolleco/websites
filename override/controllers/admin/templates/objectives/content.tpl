<script type="text/javascript" src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>

<div class="panel">
	<div class="row">
		<div class="col-lg-6">

		</div>
		<div class="col-lg-6 text-right">
			<button type="button" id="switch_display" class="btn btn-default">
				<i class="icon-refresh"></i> &nbsp; {l s='Graphiques / Données'}
			</button>
		</div>
	</div>
</div>

<div id="graphics" {if $display_tab == 2}style="display:none"{/if}>

	<div class="panel">
		<form method="post" class="form-inline">
			<input type="date" class="form-control" name="date_current" value="{$date_current}">
			<button type="submit" class="btn btn-primary">
				<b><i class="icon-refresh"></i> &nbsp; Filtrer</b>
			</button>
		</form>
	</div>

	{if !$objective->id}
		<div class="alert alert-danger">
			{l s="Aucun objectif n'est enregistré pour cette journée."}
		</div>
	{else}

		<div class="row">
			<div class="col-lg-3">
				<div class="panel text-center" style="min-height:103px; background-color:#ffc107; color:white;">
					<div class="panel-heading">
						<b style="color:white">{l s="Objectif du jour"}</b>
					</div>
					<p>{displayPrice price=$objective->value}</p>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="panel">
					<div class="panel-heading">
						<div class="row">
							<div class="col-lg-4 text-center text-muted">{l s="CA Web Equip"}</div>
							<div class="col-lg-4 text-center text-muted">{l s="Nombre de commandes"}</div>
							<div class="col-lg-4 text-center text-muted">{l s="Panier moyen HT"}</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-4 text-center">
							<div><b>{displayPrice price=$turnover}</b></div>
							<div class="text-{if $rate_turnover}success{else}danger{/if}">
								{if $rate_turnover}+{/if}{$rate_turnover} %
							</div>
						</div>
						<div class="col-lg-4 text-center">
							<div><b>{$nb_orders}</b></div>
							<div class="text-{if $rate_nb_orders}success{else}danger{/if}">
								{if $rate_nb_orders}+{/if}{$rate_nb_orders} %
							</div>
						</div>
						<div class="col-lg-4 text-center">
							<div><b>{displayPrice price=$avg}</b></div>
							<div class="text-{if $rate_avg}success{else}danger{/if}">
								{if $rate_avg}+{/if}{$rate_avg} %
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-3">
				<div class="panel text-center" style="min-height:103px; border-color:#28a745;">
					<div class="panel-heading text-muted">
						{l s="Différence objectif jour"}
					</div>
					<b>{displayPrice price=$balance}</b>
				</div>
			</div>
		</div>

		<div class="row">
			{foreach from=$shops item=shop}
				<div class="col-lg-{(12 / $shops|@count)}">
					{include file="./helpers/view/panel_shop.tpl"}
				</div>
			{/foreach}
		</div>

		<div class="row">
			<div class="col-lg-6">
				<div class="panel" style="min-height:475px">
					<div class="panel-heading text-center">
						{l s="Répartition du chiffre d'affaire"}
					</div>
					<div id="pie_chart"></div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="panel" style="min-height:475px">
					<div class="panel-heading text-center">
						{l s="Progression de la journée"}
					</div>
					<div id="line_chart"></div>
				</div>
			</div>
		</div>

	{/if}

</div>

<div id="panel_data" class="panel" {if $display_tab == 1}style="display:none"{/if}>
	<div class="panel-heading">
		<b>{l s="Objectifs journaliers"}</b>
	</div>
	<div class="row">
		<div class="col-lg-6">
			<div class="well">
				<form method="post" class="form-inline" enctype="multipart/form-data">
					<input type="hidden" name="display_tab" value="2">
					<input type="file" class="form-control" name="objective_file" required>
					<button type="submit" class="btn btn-success">
						<b><i class="icon-upload"></i> &nbsp; Uploader</b>
					</button>
					<span class="badge badge-info" title="Fichier CSV de 2 colonnes : date au format d/m/Y et objectif numérique séparés par un point-virgule">
						<i class="icon-info"></i>
					</span>
				</form>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="well">
				<form method="post">
					<input type="hidden" name="display_tab" value="2">
					<div class="row">
						<div class="col-lg-5">
							<input type="date" class="form-control" name="date_begin" value="{$date_begin}" required>
						</div>
						<div class="col-lg-5">
							<input type="date" class="form-control" name="date_end" value="{$date_end}" required>
						</div>
						<div class="col-lg-2">
							<button type="submit" class="btn btn-block btn-primary">
								<b><i class="icon-refresh"></i> &nbsp; Filtrer</b>
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	
	<table class="table">
		<thead>
			<tr class="bg-primary">
				<th><b>Jour</b></th>
				<th class="text-center"><b>Objectif</b></th>
				<th></th>
			</tr>
		</thead>
		<form method="post">
			<input type="hidden" name="display_tab" value="2">
			<tbody>
				{foreach from=$objectives item=daily}
					<tr>
						<td>{$daily->date|date_format:'d/m/Y'}</td>
						<td class="text-center">{displayPrice price=$daily->value}</td>
						<td class="text-right">
							<button type="submit" class="btn btn-xs btn-danger delete" name="remove_objective" value="{$daily->id}">
								<i class="icon-trash"></i>
							</button>
						</td>
					</tr>
				{/foreach}
			</tbody>
		</form>
		<form method="post">
			<input type="hidden" name="display_tab" value="2">
			<tfoot>
				<tr style="background-color:aliceblue">
					<td>
						<input type="date" class="form-control" name="new[date]">
					</td>
					<td>
						<input type="number" min="0" step="any" class="form-control" name="new[objective]" required>
					</td>
					<td class="text-right">
						<button type="submit" class="btn btn-success" name="save_new_objective" required>
							<b><i class="icon-save"></i> &nbsp; Enregistrer</b>
						</button>
					</td>
				</tr>
			</tfoot>
		</form>
	</table>
</div>

<script>
	$(document).ready(function() {

		$('#switch_display').on('click', function() {
			$('#graphics').toggle();
			$('#panel_data').toggle();
		});

		$('.delete').on('click', function(e) {
			if(!confirm("Confirmer la suppression ?"))
				e.preventDefault();
		});

		var options = {
			data: [{
				type: "pie",
				startAngle: 45,
				{literal}
				showInLegend: "true",
				legendText: "{label}",
				indexLabel: "{label} ({y}%)",
				color: "{color}",
				{/literal}
				dataPoints: [
					{foreach from=$shops item=shop}
						{
							label: "{$shop.name}",
							y: {$shop.total_rate},
							color: "{$shop.color}"
						},
					{/foreach}
				]
			}]
		};
		$("#pie_chart").CanvasJSChart(options);

		var options = {
			animationEnabled: true,
			theme: "light2",
			axisY: {
				suffix: "€",
			},
			axisX:{
				suffix: "h",
				valueFormatString: "H"
			},
			toolTip:{
				shared:true
			},  
			legend:{
				cursor:"pointer",
				verticalAlign: "bottom",
				horizontalAlign: "left",
				dockInsidePlotArea: true,
				
			},
			data: [{
				type: "line",
				showInLegend: true,
				name: "Objectif",
				markerType: false,
				color: "#F08080",
				yValueFormatString: "# €",
				xValueFormatString: "H",
				dataPoints: [
					{foreach from=$evolution item=row}
						{ 
							x: new Date("{$row.date}"), 
							y: {$objective->value}
						},
					{/foreach}
				]
			},
			{
				type: "line",
				showInLegend: true,
				name: "Chiffre d'affaire",
				color: '#1e4688',
				yValueFormatString: "# €",
				dataPoints: [
					{foreach from=$evolution item=row}
						{ 
							x: new Date("{$row.date}"), 
							y: {$row.turnover}
						},
					{/foreach}
				]
			}]
		};
		$("#line_chart").CanvasJSChart(options);

	});
</script>