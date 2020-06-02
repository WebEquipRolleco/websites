{function name="display_category" category=[]}
	<a href="{$category.link}" title="{$category.name}">
		<li class="category level-{$category.level} {if $category.current}active{/if}">
			{$category.name|truncate:$category.truncate:"...":true}
		</li>
	</a>
	{if $category.children|count > 0}
		{foreach from=$category.children item=child}
			{display_category category=$child}
		{/foreach}
	{/if}
{/function}

<ul id="nav_categories" class="hidden-sm-down">
	{foreach from=$categories item=category}
		{display_category category=$category}
	{/foreach}
</ul>

