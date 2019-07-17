<form method="post">
	<div class="panel">
		<div class="panel-heading">Factures à exporter</div>
		<div class="form-group">
			<textarea rows="7" class="form-control" name="numbers" required></textarea>
			<div class="text-right text-muted">
				<em>Numéros séparés par une virgule.</em>
			</div>
		</div>
		<div class="panel-footer text-right">
			<button type="submit" class="btn btn-success">
				<i class="process-icon-save"></i> <b>{l s="Exporter" d='Shop.Theme.Actions'}</b>
			</button>
		</div>
	</fieldset>
</form>