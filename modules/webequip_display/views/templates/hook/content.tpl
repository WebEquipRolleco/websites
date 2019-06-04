{if $displays|count > 0}
	<div id="home_displays" class="row">
		{foreach from=$displays item=display}
			<div class="col-lg-4 text-center">
				<a href="{$display->link}">
					<img src="{$display->getUrl()}" title="{$display->name}">
				</a>
			</div>
		{/foreach}
	</div>
{/if}