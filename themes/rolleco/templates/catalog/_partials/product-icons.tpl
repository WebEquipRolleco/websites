<div class="text-center">
	{foreach from=ProductIcon::getList() item=icon}
		{if $icon->display($product.id_product, Context::getContext()->shop->id)}
			<div class="margin-bottom-10">
				<a href="{$icon->url}" title="{$icon->title}">
					<img class="img-thumbnail" src="{$icon->getImgPath()}" {if $icon->height}height="{$icon->height}px"{/if} {if $icon->width}width="{$icon->width}px"{/if}>
				</a>
			</div>
		{/if}
	{/foreach}
</div>
