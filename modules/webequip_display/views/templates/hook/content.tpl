{if $displays|count > 0}
	<div id="home_displays" class="row">
		{foreach from=$displays item=display}
			<div class="col-lg-4 margin-top-15 text-center">
				{if $display->link}<a href="{$display->link}">{/if}
					<img src="{$display->getUrl()}" title="{$display->name}">
				{if $display->link}</a>{/if}
			</div>
		{/foreach}
	</div>
{/if}