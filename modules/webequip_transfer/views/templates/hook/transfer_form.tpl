<form id="transfer_form" method="post">
	<input type="hidden" name="ajax" value="1">
	<input type="hidden" name="action" value="load_preview">
	<div class="row">
		<div class="col-lg-4">
			<div class="form-group">
				<label>{l s="Données à transférer"}</label>
				<select class="form-control" name="transfert_name" required>
					<option value=""></option>
					{foreach from=$data_list item=data key=type}
						<option value='{$type}'>{$data.name}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="col-lg-8">
			<button type="submit" class="btn btn-success" style="margin-top:22px">
				<b>{l s="Transférer" d='Shop.Theme.Actions'}</b>
			</button>
		</div>
	</div>
</form>