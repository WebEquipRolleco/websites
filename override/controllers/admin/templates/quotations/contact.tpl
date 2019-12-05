<form method="post">
	<input type="hidden" name="id_quotation" value="{$quotation->id}">
	<div id="modal_send" class="modal">
	  	<div class="modal-dialog" role="document">
		    <div class="modal-content">
		    	<div class="modal-header">
		        	<b style="text-transform:uppercase;">
		        		<i class="icon-envelope"></i> &nbsp; {l s="Envoi au fournisseur" d='Admin.Actions'}
		        	</b>
		      	</div>
		      	<div class="modal-body">
		      		<div class="form-group">
		      			<div class="row">
		      				<div class="col-lg-6">
		      					<label for="email">{l s="Adresse e-mail" d='Admin.Labels'}</label>
		      				</div>
		      				<div class="col-lg-6 text-right">
		      					<em class="text-muted">{l s="Emails séparés par un virgule sans espaces" d='Admin.Labels'}</em>
		      				</div>
		      			</div>
		      			<input type="email" id="email" class="form-control" name="emails" value="{$quotation->getEmails()|implode:','}">
		      			<div class="text-right"><em>{l s="E-mail également envoyé vers : %s" sprintf=[Configuration::get('PS_SHOP_EMAIL', null, $quotation->id_shop)] d='Admin.Labels'}</em></div>
		      		</div>
		      		<div class="form-group">
		      			<label for="object">{l s="Objet" d='Admin.Labels'}</label>
		      			<input type="text" id="object" class="form-control" name="object" value="{l s='Votre devis %s' sprintf=[$quotation->reference] d='Admin.Labels'}">
		      		</div>
		      		<div class="form-group">
		      			<label>{l s="Message" d='Admin.Labels'}</label>
		      			<textarea rows="10" class="tiny_mce form-control" name="content">{l s="Nous nous permettons de revenir vers vous afin de faire le point sur votre projet.\r\nN'hésitez pas à nous contacter par mail, téléphone ou fax si vous avez la moindre question au sujet de votre devis."}</textarea>
		      		</div>
		      		<div class="row">
			      		<div class="col-lg-12">
			      			<table class="table">
			      				<tr class="bg-primary">
			      					<th colspan="2" class="text-center">{l s="Documents à joindre"}</th>
			      				</tr>
			      				<tr>
			      					<td>{l s="Ajouter le PDF" d='Admin.Labels'}</td>
			      					<td class="text-right"><input type="checkbox" name="pdf" value="1" checked></td>
			      				</tr>
			      				{foreach from=$quotation->getShop()->getDocuments() item=document}
			      					{if $document.exists}
			      						<tr>
				      						<td>{$document.label}</td>
				      						<td class="text-right">
				      							{if $document.name|in_array:$quotation->getDocuments()}
				      								<i class="icon-check-square text-success"></i>
				      							{else}
				      								<i class="icon-times text-danger"></i>
				      							{/if}
				      						</td>
				      					</tr>
			      					{/if}
			      				{/foreach}
							</table>
						</div>
			      	</div>
		      	</div>

		      	<div class="modal-footer">
		        	<button type="submit" class="btn btn-success" name="send">
		        		<b><i class="icon-check-square"></i> &nbsp; {l s="Send" d='Admin.Actions'}</b>
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
	$(document).ready(function() {
		
		$('#modal_send').modal('show');
		
		$('.modal-backdrop').on('click', function() {
			$('#modal_send').modal('hide');
		});
		
	});
</script>