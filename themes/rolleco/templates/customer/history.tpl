{extends file='customer/page.tpl'}

{block name='page_title'}
  {l s='Order history' d='Shop.Theme.Customeraccount'}
{/block}

{block name='page_content'}

  {if !$orders}
    <div class="alert alert-info">
      <table>
        <tbody>
          <tr>
            <td><i class="fa fa-2x fa-exclamation-triangle" aria-hidden="true"></i></td>
            <td style="padding-left:15px; line-height:12px;">
              <strong>{l s="Here are the orders you've placed since your account was created." d='Shop.Theme.Customeraccount'}</strong>
              <br />
              <br><small>{l s="Vous n'avez pour le moment passé aucune commande sur notre site."}</small>
              <br><small>{l s="Pour toute information ou demande de devis, n'hésitez pas à [1]nous contacter[1]." tags=["<a href='/nous-contacter'>"]}</small>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  {/if}
  {if $orders}
    <table class="table combinations-table table-labeled hidden-sm-down">
      <thead class="thead-default">
        <tr>
          <th>{l s='Référence' d='Shop.Theme.Checkout'}</th>
          <th>{l s='Date' d='Shop.Theme.Checkout'}</th>
          <th>{l s='Total price' d='Shop.Theme.Checkout'}</th>
          <th class="hidden-md-down">{l s='Payment' d='Shop.Theme.Checkout'}</th>
          <th>{l s='Status' d='Shop.Theme.Checkout'}</th>
          <th class="hidden-md-down">{l s='Invoice' d='Shop.Theme.Checkout'}</th>
          <th>&nbsp;</th>
        </tr>
      </thead>
      <tbody>
        {assign var="sav_enabled" value=Configuration::get('AFTER_SALES_ENABLED')}
        {foreach from=$orders item=order}
          <tr>
            <td class="text-center bold">{$order.details.reference}</th>
            <td class="text-center">{$order.details.order_date}</td>
            <td class="text-center">{$order.totals.total.value}</td>
            <td class="text-center hidden-md-down">{$order.details.payment}</td>
            <td class="text-center">{$order.history.current.ostate_name}</td>
            <td class="text-center hidden-md-down">
              {if $order.details.invoice_url}
                <a href="{$order.details.invoice_url}" class="btn btn-primary">
                  <i class="fa fa-file-pdf"></i>
                </a>
              {else}
                <a href="#" class="btn btn-primary disabled">
                  <i class="fa fa-file"></i>
                </a>
              {/if}
            </td>
            <td class="text-center order-actions">
              <a href="{$order.details.details_url}" class="btn btn-primary" data-link-action="view-order-details" title="{l s='Details' d='Shop.Theme.Customeraccount'}">
                <i class="fa fa-edit"></i>  
              </a>
              {if $order.details.reorder_url}
                <a href="{$order.details.reorder_url}" class="btn btn-success" title="{l s='Reorder' d='Shop.Theme.Actions'}">
                  <i class="fa fa-cart-arrow-down"></i>
                </a>
              {/if}
              {if $sav_enabled}
                <a href="{$link->getPageLink('AfterSaleRequest&order='|cat:$order.details.reference)}" class="btn btn-warning" title="{l s='Ouvrir un SAV' d='Shop.Theme.Actions'}">
                  <i class="fa fa-headset"></i>
                </a>
              {/if}
            </td>
          </tr>
        {/foreach}
      </tbody>
    </table>

    <div class="orders hidden-md-up">
      {foreach from=$orders item=order}
        <table class="table combinations-table table-labeled">
          <tbody>
            <tr>
              <th class="bold">{$order.details.reference}</th>
              <td class="text-right">
                <a href="{$order.details.details_url}" class="btn btn-default" data-link-action="view-order-details" title="{l s='Details' d='Shop.Theme.Customeraccount'}">
                    <i class="fa fa-edit"></i>
                  </a>
                  {if $order.details.reorder_url}
                    <a href="{$order.details.reorder_url}" class="btn btn-success" title="{l s='Reorder' d='Shop.Theme.Actions'}">
                      <i class="fa fa-redo-alt"></i>
                    </a>
                  {/if}
              </td>
            </tr>
            <tr>
              <td>{$order.details.order_date}</td>
              <td>{$order.totals.total.value}</td>
            </tr>
            <tr>
              <td colspan="2" class="text-center">
                <span class="label label-pill {$order.history.current.contrast}" style="background-color:{$order.history.current.color}">
                  {$order.history.current.ostate_name}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      {/foreach} 
    </div>

  {/if}
{/block}
