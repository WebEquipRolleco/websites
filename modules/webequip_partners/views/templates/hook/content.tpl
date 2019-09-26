{if $slides|count > 0}
	<div class="row bg-light">
		<div class="col-sm-12 top-space">
			<h3 class="section-title">{l s='Ils nous font confiance'}</h3>
			<section id="partners">
				{foreach from=$slides item=slide}
	    			<div class="slide">
	    				<a href="{$slide->link}">
	    					<img src="{$slide->getUrl()}" title="{$slide->name}">
	    				</a>
	    			</div>
	    		{/foreach}
	    	</section>
		</div>
	</div>
{/if}