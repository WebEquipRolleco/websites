<form method="post" enctype="multipart/form-data">
	<input type="hidden" name="id" value="{$icon->id}">

	<div class="panel">
		<div class="row">
			<div class="col-lg-6">
				<span class="switch prestashop-switch fixed-width-lg">
					<input type="radio" name="form[active]" id="active_on" value="1" {if $icon->active}checked{/if}>
					<label for="active_on">{l s='Active' d='Shop.Theme.Labels'}</label>
					<input type="radio" name="form[active]" id="active_off" value="0" {if !$icon->active}checked{/if}>
					<label for="active_off">{l s='Inactive' d='Shop.Theme.Labels'}</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
			<div class="col-lg-6 text-right">
				<a href="{$link->getAdminLink('AdminIconography')}" class="btn btn-default">
					<b>{l s='Retour' d='Shop.Theme.Labels'}</b>
				</a>
				<button type="submit" class="btn btn-success" name="save">
					<b>{l s='Enregistrer' d='Shop.Theme.Labels'}</b>
				</button>
			</div>
		</div>
	</div>

	<div class="row">

		<div class="col-lg-4">
			<div class="panel">
				<div class="panel-heading">
					Informations
				</div>
				<div class="form-group">
					<label for="name">{l s="Nom"} <em class="text-danger">*</em></label>
					<input type="text" id="name" class="form-control" name="form[name]" value="{$icon->name}" autocomplete="off" required>
				</div>
				<div class="form-group">
					<label for="id_group">{l s="Groupe"}</label>
					<select id="id_group" class="form-control" name="form[id_group]">
						<option value=""></option>
						{foreach from=$groups item=group}
							<option value="{$group.id_product_icon_group}" {if $icon->id_group == $group.id_product_icon_group}selected{/if}>{$group.name}</option>
						{/foreach}
					</select>
				</div>
				<div class="form-group">
					<label for="location">{l s="Emplacement"} <em class="text-danger">*</em></label>
					<select id="location" class="form-control" name="form[location]" required>
						{foreach from=ProductIcon::getLocations() key=value item=name}
							<option value="{$value}" {if $value == $icon->location}selected{/if}>{$name}</option>
						{/foreach}
					</select>
				</div>
				<div class="form-group">
					<label for="position">{l s="Position"} <em class="text-danger">*</em></label>
					<input type="number" step="1" min="1" id="position" class="form-control" name="form[position]" value="{$icon->position}" required>
				</div>
			</div>
		</div>

		<div class="col-lg-4">
			<div class="panel">
				<div class="panel-heading">
					{l s='Lien'}
				</div>
				<div class="form-group">
					<label for="title">{l s="Titre"} <em class="text-danger">*</em></label>
					<input type="text" id="title" class="form-control" name="form[title]" value="{$icon->title}" autocomplete="off" required>
				</div>
				<div class="form-group">
					<label for="url">{l s="Url"}</label>
					<input type="text" id="url" class="form-control" name="form[url]" value="{$icon->url}" autocomplete="off">
				</div>
			</div>
		</div>

		<div class="col-lg-4">
			<div class="panel">
				<div class="panel-heading">
					{l s='Image'}
				</div>
				<div class="form-group">
					<label for="picture">{l s="Nouvelle image"}</label>
					<input type="file" id="url" class="form-control" name="picture">
				</div>
				{if $icon->hasFile()}
					<hr />
					<div class="row">
						<div class="col-lg-3">
							<div class="form-group">
								<label for="height">{l s="Forcer la hauteur"}</label>
								<input type="number" step="1" min="0" id="height" class="form-control" name="form[height]" {if $icon->height}value="{$icon->height}"{/if} placeholder="Taille originale">
							</div>
							<div class="form-group">
								<label for="width">{l s="Forcer la largeur"}</label>
								<input type="number" step="1" min="0" id="width" class="form-control" name="form[width]" {if $icon->width}value="{$icon->width}"{/if} placeholder="Taille originale">
							</div>
						</div>
						<div class="col-lg-9 text-center">
							<img src="{$icon->getImgPath()}" {if $icon->height}height="{$icon->height}px"{/if} {if $icon->width}width="{$icon->width}px"{/if}>
						</div>
					</div>
				{/if}
			</div>
		</div>

	</div>

</form>

<script>
	$(document).ready(function() {
		$('#filter_product').select2({ width:'auto' });
	});
</script>