{$list}

<form method="post" enctype="multipart/form-data">
	<div id="modal_import" class="modal">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <b style="text-transform:uppercase;">
	        	<i class="icon-upload"></i> &nbsp; {l s="Import" d='Admin.Actions'}
	        </b>
	      </div>
	      <div class="modal-body">
	      	<div class="alert alert-info">
	      		<b>Le fichier fourni doit :</b>
	      		<br />
	      		<ul>
	      		<li>Etre au format <b>CSV</b> encodé en <b>UTF-8</b></li>
	      		<li>Les colones du fichier doivent être séparées par un <b>point-virgule</b></li>
	      		<li>Les valeurs doivent être séparées par une <b>virgule</b></li>
	      	</div>
	      	<div class="form-group">
	      		<label>{l s="Fichier CSV"}</label>
	        	<input type="file" class="form-control" name="import_file" required>
	        </div>
	        <div class="form-group">
	        	<label>{l s="Sauter la 1ère ligne"}</label>
	        	<span class="switch prestashop-switch fixed-width-lg">
				<input type="radio" name="skip" id="skip_on" value="1" checked>
				<label for="skip_on">{l s='Oui' d='Shop.Theme.Labels'}</label>
				<input type="radio" name="skip" id="skip_off" value="0">
				<label for="skip_off">{l s='Non' d='Shop.Theme.Labels'}</label>
				<a class="slide-button btn"></a>
			</span>
	        </div>
	        <div class="alert alert-info">
	        	<p><b>Les colonnes du fichier doivent suivre le schéma suivant :</b></p>
	        	<ul>
	        		{foreach from=$columns item=column}
                		<li>{$column}</li>
                	{/foreach}
            	</ul>
	        </div>
	      </div>
	      <div class="modal-footer">
	        <button type="submit" class="btn btn-success" name="import">
	        	<b><i class="icon-check-square"></i> &nbsp; {l s="Import" d='Admin.Actions'}</b>
	        </button>
	        <button type="button" class="btn btn-danger" data-dismiss="modal">
	        	<b><i class="icon-times"></i> &nbsp; {l s="Close" d='Admin.Actions'}</b>
	        </button>
	      </div>
	    </div>
	  </div>
	</div>
</form>

<script>
	$(document).on('ready', function() {

		$('#desc-product_icon-import').on('click', function(e) {
			e.preventDefault();

			$('#modal_import').modal('show');
		});

	});
</script>