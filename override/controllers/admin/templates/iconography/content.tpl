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
	      		<br /><br />
	      		- Etre compréssé au format <b>ZIP</b> <br /> 
	      		- Comprendre un fichier de type <b>CSV</b> <br />
	      		- Les colones du fichier doivent être séparés par un <b>point-virgule</b> <br />
	      		- Les images doivent également être contenues dans l'archive
	      	</div>
	      	<div class="form-group">
	      		<label>{l s="Fichier ZIP"}</label>
	        	<input type="file" class="form-control" name="file">
	        </div>
	        <div class="form-group">
	        	<label>{l s="Sauter la 1ère ligne"}</label>
	        	<span class="switch prestashop-switch fixed-width-lg">
				<input type="radio" name="skip" id="skip_on" value="1" checked>
				<label for="skip_on">{l s='Yes' d='Shop.Theme.Labels'}</label>
				<input type="radio" name="skip" id="skip_off" value="0">
				<label for="skip_off">{l s='No' d='Shop.Theme.Labels'}</label>
				<a class="slide-button btn"></a>
			</span>
	        </div>
	        <div class="alert alert-info">
	        	<b>Les colonnes du fichier doivent suivre le schéma suivant :</b>
	        	<br /><br />
                - 0 = ID <small class="text-muted">Facultatif, pour la modification uniquement</small><br />
                - 1 = Nom <br />
                - 2 = Titre <br />
                - 3 = URL <br />
                - 4 = Nom de l'image <br />
                - 5 = Hauteur image <br />
                - 6 = Largeur image <br />
                - 7 = IDs liste blanche (x,y,z...) <br />
                - 8 = IDs liste noire (x,y,z...) <br />
                - 9 = Position <br />
                - 10 = statut <br />
                - 11 = Boutiques (x,y,z...)
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