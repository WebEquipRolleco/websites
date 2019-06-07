{function name="sub_menu" nodes=[] index=0}
  <menu id="submenu_{$index}" class="megamenu_level_2 text-center" type="navigation_categories" style="display:none">
    {foreach from=$nodes key=key item=node}
      <li class="text-center">
        <a href="{$node.url}" {if $node.open_in_new_window}target="_blank"{/if}>
          <img src="{$link->getCatImageLink(null, $node.page_identifier|replace:'category-':'')}">
          <div class="title">{$node.label}</div>
        </a>
      </li>
    {/foreach}
    {*<div class="container">
      <div class="row">
        {foreach from=$nodes key=key item=node}
          <div class="col-sm-3 text-center">
            <div class="main-menu">
              <a href="{$node.url}" {if $node.open_in_new_window}target="_blank"{/if}>
                {$node.label}
              </a>
            </div>
            {if $node.children|count}
              <ul class="margin-top-15">
                {foreach from=$node.children item=child}
                  <li>
                    <a href="{$child.url}" title="{$child.label}" {if $child.open_in_new_window}target="_blank"{/if}>
                      <i class="fa fa-angle-double-right"></i> {$child.label|truncate:35:"..."}
                    </a>
                  </li>
                {/foreach}
              </ul>
            {/if}
          </div>
        {/foreach}
      </div>
    </div>*}
  </menu>
{/function}

{assign var=_counter value=0}
{function name="menu" nodes=[] depth=0 parent=null}
  {if $nodes|count}
    <menu id="megamenu" type="navigation">
      {foreach from=$nodes key=key item=node}
        <li class="main-category">
          <a href="{$node.url}" class="show-menu" data-id="{$key}"  {if $node.open_in_new_window}target="_blank"{/if}>
            {$node.label}
          </a>
        </li>
        {if $node.children|count}
            {sub_menu nodes=$node.children index=$key}
        {/if}
      {/foreach}
    </menu>
  {/if}
{/function}

<div class="col-sm-10">
  <div class="menu js-top-menu position-static hidden-lg-down" id="_desktop_top_menu">
      {menu nodes=$menu.children}
      <div class="clearfix"></div>
  </div>
</div>

<div id="iziMenu" class="iziModal">
  <table class="table" style="margin-bottom:0px">
    <tbody>
      {foreach from=$menu.children item=main}
        <tr class="bg-grey">
          <td colspan="2" class="text-center bold">
            <a href="{$main.url}" {if $main.open_in_new_window}target="_blank"{/if}>{$main.label}</a>
          </td>
        </tr>
        {if $main.children}
          {foreach from=$main.children item=child}
            <tr>
              <td style="padding:5px">
                <img src="{$link->getCatImageLink(null, $child.page_identifier|replace:'category-':'')}" style="height:50px; width:50px;">
              </td>
              <td style="vertical-align: middle;">
                <a href="{$child.url}" {if $child.open_in_new_window}target="_blank"{/if} style="color: grey">
                  {$child.label|truncate:30:"..."}
                </a>
              </td>
            </tr>
          {/foreach}
        {/if}
      {/foreach}
    </tbody>
  </table>
</div>