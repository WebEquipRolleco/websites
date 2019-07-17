<form method="post" action="{$action}">
	<input type="hidden" name="id_review" value="{$review->id}">
	<div class="panel">
		<div class="panel-heading">
			{l s="Avis client" mod="webequip_reviews"}
		</div>
		<div class="row">
			<div class="col-lg-4">
				<div class="form-group">
					<label for="name">{l s="Produit" mod="webequip_reviews"}</label>
					<input type="text" id="name" class="form-control" name="review[name]" value="{$review->name}">
				</div>
				<div class="form-group">
					<label for="rating">{l s="Note cient" mod="webequip_reviews"}</label>
					<select id="rating" class="form-control" name="review[rating]">
						{for $x=0 to 5}
							<option value="{$x}" {if $review->rating == $x}selected{/if}>{$x} / 5</option>
						{/for}
					</select>
				</div>
				<div class="form-group">
					<label for="comment">{l s="Commentaire client" mod="webequip_reviews"}</label>
					<textarea rows="7" id="comment" class="form-control" name="review[comment]">{$review->comment}</textarea>
				</div>
				<div class="form-group">
					<span class="switch prestashop-switch fixed-width-lg" style="margin-left:auto; margin-right:auto;">
						<input type="radio" name="review[active]" id="active_on" value="1" {if $review->active}checked{/if}>
						<label for="active_on">{l s='Active' d='Shop.Theme.Labels'}</label>
						<input type="radio" name="review[active]" id="active_off" value="0" {if !$review->active}checked{/if}>
						<label for="active_off">{l s='Inactive' d='Shop.Theme.Labels'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
			</div>
		</div>
		<div class="panel-footer text-right">
			<a href="{$action}" class="btn btn-default">
				<i class="process-icon-cancel"></i> <b>{l s="Cancel" d='Shop.Theme.Actions'}</b>
			</a>
			<button type="submit" class="btn btn-success">
				<i class="process-icon-save"></i> <b>{l s="Save" d='Shop.Theme.Actions'}</b>
			</button>
		</div>
	</div>
</form>