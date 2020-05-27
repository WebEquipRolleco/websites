<form method="post">
	<div class="panel">
		<div class="panel-heading">
			<b>{l s="Configuration"}</b>
		</div>
		<div class="row">
			<div class="col-lg-4">
				<div class="form-group">
					<label>Dossier d'export</label>
					<input type="text" class="form-control" name="BEEZUP_DIRECTORY" value="{$BEEZUP_DIRECTORY}">
				</div>
				<div class="form-group">
					<label>Format des images</label>

					<select class="form-control" name="BEEZUP_IMG_FORMAT">
						<option value=""></option>
						{foreach from=$formats item=format}
							<option value="{$format}" {if $format == $BEEZUP_IMG_FORMAT}selected{/if}>{$format}</option>
						{/foreach}
					</select>
				</div>
			</div>
		</div>
		<div class="panel-footer text-right">
			<button type="submit" class="btn btn-success">
				<i class="process-icon-save"></i> <b>{l s="Save"}</b>
			</button>
		</div>
	</div>
</form>

{if $BEEZUP_DIRECTORY}
	
	<form method="post" id="manual_export">
		<div class="panel">
			<button type="submit" class="btn btn-warning" name="manual_export">
				<i class="icon-play"></i> &nbsp; <b>Effectuer un export manuel</b>
			</button>
		</div>
	</form>

	{if isset($confirmation)}
		<div class="alert alert-success">
			<b>{$confirmation}</b>
		</div>
	{/if}

	{if !empty($files)}
		<div class="panel">
			<div class="panel-heading">
				<b>{l s="Fichiers actuels"}</b>
			</div>
			<div class="panel-content">
				<table class="table">
					<thead>
						<tr class="bg-primary">
							<th>
								<b>{l s="Fichier"}</b>
							</th>
							<th class="text-center">
								<b>{l s="Création"}</b>
							</th>
						</tr>
					</thead>
					<tbody>
						{foreach from=$files item=file}
							<tr>
								<td>
									<a href="{$file.path}">{$file.name}</a>
								</td>
								<td class="text-center">
									{$file.time}
								</td>
							</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>
	{/if}

{/if}

<script>
	$(document).ready(function() {

		$('#manual_export').on('submit', function(e) {
			if(!confirm("Etes-vous sûr(e) ? Cette action entrainera l'écrasement des précédents fichiers de sauvegarde."))
				e.preventDefault();
		});
	});
</script>