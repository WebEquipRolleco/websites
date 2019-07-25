<style>
	.top-5	{ margin-top: 5px; }
	.top-10 { margin-top: 10px; }
	.top-15 { margin-top: 15px; }
	.large	{ width: 300px; }
</style>

<form method="post">

	<div class="row">
		<div class="col-lg-12">
			<div class="panel text-right">
				<button type="submit" class="btn btn-success">
					<b><i class="icon-save"></i> &nbsp; {l s="Enregistrer"}</b>
				</button>
			</div>
		</div>
	</div>

	<div class="row">
		{foreach from=$cols item=$domains}
			<div class="col-lg-3">
				{foreach from=$domains key=domain item=fields}
					<div class="panel">
						<div class="panel-heading">
							<b>{$domain}</b>
						</div>
						{foreach from=$fields item=field}
							<div class="form-group top-5">
								<label for="{$field.name}">{$field.label}</label>
								<input type="text" id="{$field.name}" class="form-control large" name="{$field.name}" value="{Configuration::get($field.name)}" autocomplete="off">
								{if isset($field.help)}
									<div class="text-muted text-right">
										<em>{$field.help}</em>
									</div>
								{/if}
							</div>
						{/foreach}
					</div>
				{/foreach}
			</div>
		{/foreach}
	</div>
</form>