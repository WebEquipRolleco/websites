{extends file='layouts/layout-both-columns.tpl'}

{block name='left_column'}{/block}
{block name='right_column'}{/block}

{block name='content_wrapper'}
  <div id="content-wrapper">

    {hook h="displayContentWrapperTop"}
    {block name='content'}{/block}

  </div>
{/block}
