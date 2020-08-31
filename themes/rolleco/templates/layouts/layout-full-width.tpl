{extends file='layouts/layout-both-columns.tpl'}

{block name='left_column'}{/block}
{block name='right_column'}{/block}

{block name='content_wrapper'}
  <div id="content-wrapper">

    {hook h="displayContentWrapperTop"}
    {block name='content'}{/block}

    {if $page.page_name == 'index'}
{*    	{block name='newsletter'}
    		{include file='_partials/block_newsletter.tpl'}
    	{/block}*}

    	{block name="quotation"}
    		{include file='_partials/block_quotation.tpl'}
    	{/block}
	{/if}
	
  </div>
{/block}
