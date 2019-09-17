{foreach from=$reassurances item=reassurance}
	<a href="{$reassurance->link}" class="nav-link">
        {$reassurance->icon nofilter}
        {$reassurance->text|strip_tags:true}
    </a>
{/foreach}