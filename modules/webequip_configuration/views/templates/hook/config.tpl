<form method="post">
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i> Configuration des onglets
		</div>
		<table class="table">
			<thead>
				<tr class="bg-primary">
					<th colspan="2"><b>{l s='Menu'}</b></th>
					<th class="text-center"><b>{l s='Statut'}</b></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$tabs item=tab}
					<tr>
						<td style="background-color:lightgrey !important"><b>{$tab.name}</b></td>
						<td colspan="3" class="text-right" style="background-color:lightgrey !important">
							{if isset($tab.action) and !$tab.id}
								<button type="submit" class="btn btn-xs btn-default" {if $tab.id}disabled{else}name="action" value="{$tab.action}"{/if}>
									{l s='Installer'}
								</button>
							{/if}
						</td>
					</tr>
					{if !isset($tab.id) or $tab.id}
						{foreach from=$tab.children item=child}
							<tr>
							<td></td>
							<td>{$child.name}</td>
							<td class="text-center">
								{if $child.id}
									<i class="icon-check text-success"></i>
								{else}
									<i class="icon-times text-danger"></i>
								{/if}
							</td>
							<td class="text-right">
								<button type="submit" class="btn btn-xs btn-default" {if $child.id}disabled{else}name="action" value="{$child.action}"{/if}>
									{l s='Installer' d='Shop.Theme.Actions'}
								</button>
								{if $child.id}
									<button type="submit" class="btn btn-xs btn-danger" name="remove_tab" value="{$child.id}">
										<i class="icon-trash"></i>
									</button>
								{/if}
							</td>
						</tr>
						{/foreach}
					{/if}
				{/foreach}
			</tbody>
		</table>
	</div>
</form>

<form method="post">
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-cogs"></i> Gestion des liens du footer
		</div>
		<div class="row">
			<div class="col-lg-4">
				<div class="form-group">
					<label>{l s="Lien de la page des moyens de paiements"}</label>
					<select class="form-control" name="FOOTER_LINK_PAIEMENT">
						<option value="0">-</option>
						{foreach from=$cms item=page}
							<option value="{$page.id_cms}" {if $page.id_cms == $FOOTER_LINK_PAIEMENT}selected{/if}>
								{$page.meta_title}
							</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="form-group">
					<label>{l s="Lien de la page F.A.Q"}</label>
					<select class="form-control" name="FOOTER_LINK_FAQ">
						<option value="0">-</option>
						{foreach from=$cms item=page}
							<option value="{$page.id_cms}" {if $page.id_cms == $FOOTER_LINK_FAQ}selected{/if}>
								{$page.meta_title}
							</option>
						{/foreach}
					</select>
				</div>
			</div>
		</div>
		<div class="panel-footer text-right">
			<button type="submit" class="btn btn-success">
				<i class="process-icon-save"></i> <b>{l s="Save" d='Shop.Theme.Actions'}</b>
			</button>
		</div>
	</div>
</form>