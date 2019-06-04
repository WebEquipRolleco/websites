<div id="reassurance_top" class="row hidden-md-down">
  {foreach from=$reassurances item=reassurance}
    <div class="col-xs-4">
     	{if $reassurance->link}<a href="{$reassurance->link}">{/if}
        {$reassurance->icon nofilter}
        {$reassurance->text nofilter}
      	{if $reassurance->link}</a>{/if}
    </div>
  {/foreach}
</div>