<div class="alert alert-info">
	<b>Fonctionnement de la réduction accordée :</b>
	<ul>
		<li>Un groupe de client "Newsletter" a été crée</li>
		<li>Une réduction spéciale avec le code "NEWSLETTER" a été crée</li>
		<li>Cette réduction n'est accessible qu'aux client faisant partie du groupe cité précédemment</li>
		<li>Les clients sont ajoutés à ce groupe lors de l'inscription à la newsletter</li>
	</ul>
	<br />
	<ul>
		<li>La réduction peut être modifiés (ne pas modifier le code : NEWSLETTER)</li>
		<li><b>ATTENTION :</b> Le groupe de doit pas être modifié</li>
		<li><b>ATTENTION :</b> Ne pas supprimer le groupe ou la réduction</li>
	</ul>
</div>

<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<div class="panel-heading">
				{l s="Groupe paramétré"}
			</div>
			{if isset($group) && $group}
				<table class="table">
					<tbody>
						<tr>
							<td style="background-color:lightgrey;"><b>{l s="ID" d='Shop.Theme.Labels'}</b></td>
							<td class="text-right">{$group->id}</td>
						</tr>
						<tr>
							<td style="background-color:lightgrey;"><b>{l s="Nom" d='Shop.Theme.Labels'}</b></td>
							<td class="text-right">{$group->name}</td>
						</tr>
						<tr>
							<td style="background-color:lightgrey;"><b>{l s="Date de création" d='Shop.Theme.Labels'}</b></td>
							<td class="text-right">{$group->date_add|date_format:'d/m/Y'}</td>
						</tr>
						<tr>
							<td style="background-color:lightgrey;"><b>{l s="Membres" d='Shop.Theme.Labels'}</b></td>
							<td class="text-right">{$group->getCustomers(true)}</td>
						</tr>
					</tbody>
				</table>
			{else}
				<div class="alert alert-danger">
					{l s="Le groupe requis n'existe pas"}
				</div>
			{/if}
		</div>
	</div>
	<div class="col-lg-6">
		<div class="panel">
			<div class="panel-heading">
				{l s="Réduction paramétrée"}
			</div>
			{if isset($reduction) && $reduction}
				<table class="table">
					<tbody>
						<tr>
							<td style="background-color:lightgrey;"><b>{l s="ID" d='Shop.Theme.Labels'}</b></td>
							<td class="text-right">{$reduction->id}</td>
						</tr>
						<tr>
							<td style="background-color:lightgrey;"><b>{l s="Nom" d='Shop.Theme.Labels'}</b></td>
							<td class="text-right">{$reduction->name}</td>
						</tr>
						<tr>
							<td style="background-color:lightgrey;"><b>{l s="Code" d='Shop.Theme.Labels'}</b></td>
							<td class="text-right">{$reduction->code}</td>
						</tr>
						<tr>
							<td style="background-color:lightgrey;"><b>{l s="Date de début" d='Shop.Theme.Labels'}</b></td>
							<td class="text-right">{$reduction->date_from|date_format:'d/m/Y'}</td>
						</tr>
						<tr>
							<td style="background-color:lightgrey;"><b>{l s="Date de fin" d='Shop.Theme.Labels'}</b></td>
							<td class="text-right">{$reduction->date_to|date_format:'d/m/Y'}</td>
						</tr>
					</tbody>
				</table>
			{else}
				<div class="alert alert-danger">
					{l s="La réduction requise n'existe pas"}
				</div>
			{/if}
		</div>
	</div>
</div>