<div class="text-center">
	{foreach from=ProductIcon::getList() item=icon}
		{if $icon->display($product.id_product, Context::getContext()->shop->id)}
			<a href="{$icon->url}" title="{$icon->title}">
				<img src="{$icon->getImgPath()}" {if $icon->height}height="{$icon->height}px"{/if} {if $icon->width}width="{$icon->width}px"{/if}>
			</a>
		{/if}
	{/foreach}
</div>
