<form method="post">
	<input type="hidden" name="modal[id]" value="{$modal->id}">
	<div id="modal_details" class="modal" tabindex="-1" role="dialog">
	  <div class="modal-dialog modal-lg" role="document">
	    <div class="modal-content">
	      <div class="modal-body">
	        <div class="alert bg-primary">
	        	<div class="row">
		        	<div class="col-lg-10">
		        		<b>{l s="Détails de la popin" mod="webequip_modal"}</b>
		        	</div>
		        	<div class="col-lg-2 text-right">
			        	<button type="button" id="switch_display" class="btn btn-xs btn-default">
			        		<i class="icon-cog"></i>
			        	</button>
			        </div>
			    </div>
	        </div>

	        <div id="modal_content">
	        	{include file="./details-content.tpl"}
	        </div>
	        <div id="modal_parameters" style="display:none">
	        	{include file="./details-parameters.tpl"}
	        </div>
	        
	      </div>
	      <div class="modal-footer" style="background-color:whitesmoke">
	        <button type="submit" class="btn btn-success">
	        	<i class="icon-check"></i>
	        </button>
	        <button type="button" class="btn btn-danger" data-dismiss="modal">
	        	<i class="icon-times"></i>
	        </button>
	      </div>
	    </div>
	  </div>
	</div>
</form>

{literal}
	<script>
		$(document).ready(function() {
			$('.select2').select2({containerCssClass:'form-control', placeholder:{id:0, text:$(this).attr('placeholder')}, allowClear:true});
		});
	</script>
{/literal}