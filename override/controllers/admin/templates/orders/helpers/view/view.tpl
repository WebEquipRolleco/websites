{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}
  <script type="text/javascript">
  var admin_order_tab_link = "{$link->getAdminLink('AdminOrders')|addslashes}";
  var id_order = {$order->id};
  var id_lang = {$current_id_lang};
  var id_currency = {$order->id_currency};
  var id_customer = {$order->id_customer|intval};
  {assign var=PS_TAX_ADDRESS_TYPE value=Configuration::get('PS_TAX_ADDRESS_TYPE')}
  var id_address = {$order->$PS_TAX_ADDRESS_TYPE};
  var currency_sign = "{$currency->sign}";
  var currency_format = "{$currency->format}";
  var currency_blank = "{$currency->blank}";
  var priceDisplayPrecision = {$smarty.const._PS_PRICE_DISPLAY_PRECISION_|intval};
  var use_taxes = {if $order->getTaxCalculationMethod() == $smarty.const.PS_TAX_INC}true{else}false{/if};
  var stock_management = {$stock_management|intval};
  var txt_add_product_stock_issue = "{l s='Are you sure you want to add this quantity?' d='Admin.Orderscustomers.Notification' js=1}";
  var txt_add_product_new_invoice = "{l s='Are you sure you want to create a new invoice?' d='Admin.Orderscustomers.Notification' js=1}";
  var txt_add_product_no_product = "{l s='Error: No product has been selected' d='Admin.Orderscustomers.Notification' js=1}";
  var txt_add_product_no_product_quantity = "{l s='Error: Quantity of products must be set' d='Admin.Orderscustomers.Notification' js=1}";
  var txt_add_product_no_product_price = "{l s='Error: Product price must be set' d='Admin.Orderscustomers.Notification' js=1}";
  var txt_confirm = "{l s='Are you sure?' d='Admin.Notifications.Warning' js=1}";
  var statesShipped = new Array();
  var has_voucher = {if count($discounts)}1{else}0{/if};
  {foreach from=$states item=state}
    {if (isset($currentState->shipped) && !$currentState->shipped && $state['shipped'])}
      statesShipped.push({$state['id_order_state']});
    {/if}
  {/foreach}
  var order_discount_price = {if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
                  {$order->total_discounts_tax_excl}
                {else}
                  {$order->total_discounts_tax_incl}
                {/if};

  var errorRefund = "{l s='Error. You cannot refund a negative amount.' d='Admin.Orderscustomers.Notification'}";
  </script>

  {assign var="hook_invoice" value={hook h="displayInvoice" id_order=$order->id}}
  {if ($hook_invoice)}
  <div>{$hook_invoice}</div>
  {/if}

  {assign var="order_documents" value=$order->getDocuments()}
  {assign var="order_shipping" value=$order->getShipping()}
  {assign var="order_return" value=$order->getReturn()}

  <style type="text/css">
    .panel-highlighted { background-color: lightgoldenrodyellow !important; }
  </style>

  {*<div class="panel kpi-container">
    <div class="row">
      <div class="col-xs-6 col-sm-3 box-stats color3" >
        <div class="kpi-content">
          <i class="icon-calendar-empty"></i>
          <span class="title">{l s='Date' d='Admin.Global'}</span>
          <span class="value">{dateFormat date=$order->date_add full=false}</span>
        </div>
      </div>
      <div class="col-xs-6 col-sm-3 box-stats color4" >
        <div class="kpi-content">
          <i class="icon-money"></i>
          <span class="title">{l s='Total' d='Admin.Global'}</span>
          <span class="value">{displayPrice price=$order->total_paid_tax_incl currency=$currency->id}</span>
        </div>
      </div>
      <div class="col-xs-6 col-sm-3 box-stats color2" >
        <div class="kpi-content">
          <i class="icon-comments"></i>
          <span class="title">{l s='Messages' d='Admin.Global'}</span>
          <span class="value"><a href="{$link->getAdminLink('AdminCustomerThreads')|escape:'html':'UTF-8'}&amp;id_order={$order->id|intval}">{sizeof($customer_thread_message)}</a></span>
        </div>
      </div>
      <div class="col-xs-6 col-sm-3 box-stats color1" >
        <a href="#start_products">
          <div class="kpi-content">
            <i class="icon-book"></i>
            <span class="title">{l s='Products' d='Admin.Global'}</span>
            <span class="value">{sizeof($products)}</span>
          </div>
        </a>
      </div>
    </div>
  </div>*}

  <div class="row">

    <div class="col-lg-3">
      <div class="panel" style="min-height:100px">
        <div class="panel-heading">
          <i class="icon-cogs"></i>
          {l s='Référence interne' d='Admin.Global'}
        </div>
        <form method="post" class="form-inline">
          <input type="text" class="form-control" name="new_internal_reference" value="{$order->internal_reference}" placeholder="{l s='Non renseignée' d='Admin.Global'}">
          <button type="submit" class="btn btn-info">
            <strong>{l s='Enregistrer' d='Admin.Actions'}</strong>
          </button>
        </form>
      </div>
    </div>

    <div class="col-lg-3">
      <div class="panel" style="min-height:100px">
        <div class="panel-heading">
          <i class="icon-cogs"></i>
          {l s='Etat client' d='Admin.Global'}
          <span class="panel-heading-action">
            <a href="?tab=AdminCustomers&amp;id_customer={$customer->id}&amp;updatecustomer&amp;token={getAdminToken tab='AdminCustomers'}" class="list-toolbar-btn" title="{l s='Edit' d='Admin.Actions'}" target="_blank">
                <i class="process-icon-edit"></i>
            </a>
          </span>
        </div>
        <a href="?tab=AdminCustomers&amp;id_customer={$customer->id}&amp;updatecustomer&amp;token={getAdminToken tab='AdminCustomers'}" title="{l s='Edit' d='Admin.Actions'}" target="_blank" style="text-decoration:none">
          {assign var='state' value=$order->getCustomer()->getState()}
          {if $state}
            <span class="label" style="background-color:{$state->color};{if $state->light_text}color:white;{/if}"><b>{$state->name}</b></span>
            {if $order->getCustomer()->comment}
              - {$order->getCustomer()->comment}
            {/if}
          {else}
            <span class="label label-success">
              <i class="icon-check-square"></i> {l s="OK"}
            </span>
          {/if}
        </a>
      </div>
    </div>

    <div class="col-lg-3">
      <div class="panel" style="min-height:100px">
        <div class="panel-heading">
          <i class="icon-cogs"></i>
          {l s='Solvabilité client' d='Admin.Global'}
          <span class="panel-heading-action">
            <a href="?tab=AdminCustomers&amp;id_customer={$customer->id}&amp;updatecustomer&amp;token={getAdminToken tab='AdminCustomers'}" class="list-toolbar-btn" title="{l s='Edit' d='Admin.Actions'}" target="_blank">
                <i class="process-icon-edit"></i>
            </a>
          </span>
        </div>
        <a href="?tab=AdminCustomers&amp;id_customer={$customer->id}&amp;updatecustomer&amp;token={getAdminToken tab='AdminCustomers'}" title="{l s='Edit' d='Admin.Actions'}" target="_blank" style="text-decoration:none">
          {if !$order->getCustomer()->funding}
            <span class="label label-danger text-center">
              <i class="icon-warning"></i>
            </span>
            &nbsp; <b class="text-danger">{l s="Client non solvable"}</b>
            {if $order->getCustomer()->date_funding != '0000-00-00'}
              - {$order->getCustomer()->date_funding|date_format:'d/m/Y'}
            {/if}
          {else}
            <span class="label label-success">
              <i class="icon-check-square"></i> {l s="OK"}
            </span>
          {/if}
        </a>
      </div>
    </div>

    <div class="col-lg-3">
      <div class="panel" style="min-height:100px">
        <div class="panel-heading">
          <i class="icon-cogs"></i>
          {l s='TVA client' d='Admin.Global'}
          <span class="panel-heading-action">
            <a href="?tab=AdminCustomers&amp;id_customer={$customer->id}&amp;updatecustomer&amp;token={getAdminToken tab='AdminCustomers'}" class="list-toolbar-btn" title="{l s='Edit' d='Admin.Actions'}" target="_blank">
                <i class="process-icon-edit"></i>
            </a>
          </span>
        </div>
        <a href="?tab=AdminCustomers&amp;id_customer={$customer->id}&amp;updatecustomer&amp;token={getAdminToken tab='AdminCustomers'}" title="{l s='Edit' d='Admin.Actions'}" target="_blank" style="text-decoration:none">
          {if !$order->getCustomer()->checkTVA()}
            <span class="label label-danger">
              <i class="icon-warning"></i>
              <b>&nbsp; {l s="TVA interne du client manquant."}</b>
            </span>
          {else}
            <span class="label label-success">
              <i class="icon-check-square"></i> {l s="OK"}
            </span>
          {/if}
        </a>
      </div>
    </div>

  </div>
  
  <div class="row">
    <div class="col-lg-7">
      <div class="panel">
        <div class="panel-heading">
          <i class="icon-credit-card"></i>
          {l s='Order' d='Admin.Global'}
          <span class="badge">{$order->reference}</span>
          <span class="badge">{l s="#" d='Admin.Orderscustomers.Feature'}{$order->id}</span>
          <span class="badge">{l s="Panier #%s" sprintf=[$order->getCart()->id] d='Admin.Orderscustomers.Feature'}</span>
          {assign var=export_data value=$order->getExportedData()}
          <span class="badge" title="{$export_data.info}">
            <i class="icon-circle text-{$export_data.class}"></i> 
            {l s="Statut export M3" d='Admin.Orderscustomers.Feature'}
          </span>
          <div class="panel-heading-action">
            <div class="btn-group">
              <a class="btn btn-default{if !$previousOrder} disabled{/if}" href="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$previousOrder|intval}">
                <i class="icon-backward"></i>
              </a>
              <a class="btn btn-default{if !$nextOrder} disabled{/if}" href="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$nextOrder|intval}">
                <i class="icon-forward"></i>
              </a>
            </div>
          </div>
        </div>
        <!-- Orders Actions -->
        <div class="well hidden-print">
          {*<a class="btn btn-default" href="javascript:window.print()">
            <i class="icon-print"></i>
            {l s='Print order' d='Admin.Orderscustomers.Feature'}
          </a>
          &nbsp;*}
            <a href="{$link->getAdminLink('AdminPdf')|escape:'html':'UTF-8'}&submitAction=generatePreparationSlipPDF&id_order={$order->id|intval}" class="btn btn-default _blank">
              <i class="icon-file"></i> {l s="Bon de préparation"}
            </a>
          &nbsp;
          {if Configuration::get('PS_INVOICE') && count($invoices_collection) && $order->invoice_number}
            <a data-selenium-id="view_invoice" class="btn btn-default _blank" href="{$link->getAdminLink('AdminPdf')|escape:'html':'UTF-8'}&amp;submitAction=generateInvoicePDF&amp;id_order={$order->id|intval}">
              <i class="icon-file"></i>
              {l s='View invoice' d='Admin.Orderscustomers.Feature'}
            </a>
          {else}
            <span class="span label label-inactive">
              <i class="icon-remove"></i>
              {l s='No invoice' d='Admin.Orderscustomers.Feature'}
            </span>
          {/if}
          {*&nbsp;*}
          {*if $order->delivery_number}
            <a class="btn btn-default _blank"  href="{$link->getAdminLink('AdminPdf')|escape:'html':'UTF-8'}&amp;submitAction=generateDeliverySlipPDF&amp;id_order={$order->id|intval}">
              <i class="icon-truck"></i>
              {l s='View delivery slip' d='Admin.Orderscustomers.Feature'}
            </a>
          {else}
            <span class="span label label-inactive">
              <i class="icon-remove"></i>
              {l s='No delivery slip' d='Admin.Orderscustomers.Feature'}
            </span>
          {/if*}
          &nbsp;
          {if Configuration::get('PS_ORDER_RETURN')}
            <a id="desc-order-standard_refund" class="btn btn-default" href="#refundForm">
              <i class="icon-exchange"></i>
              {if $order->hasBeenShipped()}
                {l s='Return products' d='Admin.Orderscustomers.Feature'}
              {elseif $order->hasBeenPaid()}
                {l s='Standard refund' d='Admin.Orderscustomers.Feature'}
              {else}
                {l s='Cancel products' d='Admin.Orderscustomers.Feature'}
              {/if}
            </a>
            &nbsp;
          {/if}
          {if $order->hasInvoice()}
            <a id="desc-order-partial_refund" class="btn btn-default" href="#refundForm">
              <i class="icon-exchange"></i>
              {l s='Partial refund' d='Admin.Orderscustomers.Feature'}
            </a>
          {/if}
          <form method="post" style="display:inline-block;">
            <button type="submit" id="send_invoice" class="btn btn-default" name="send_invoice">
              <i class="icon-envelope"></i> {l s="Envoyer la facture"}
            </button>
          </form>
          {hook h='displayBackOfficeOrderActions' id_order=$order->id|intval}
        </div>
        <!-- Tab nav -->
        <ul class="nav nav-tabs" id="tabOrder">
          {$HOOK_TAB_ORDER}
          <li class="active">
            <a href="#status">
              <i class="icon-time"></i>
              {l s='Status' d='Admin.Global'} <span class="badge">{$history|@count}</span>
            </a>
          </li>
          <li>
            <a href="#documents">
              <i class="icon-file-text"></i>
              {l s='Documents' d='Admin.Orderscustomers.Feature'} <span class="badge">{$order_documents|@count}</span>
            </a>
          </li>
        </ul>
        <!-- Tab content -->
        <div class="tab-content panel">
          {$HOOK_CONTENT_ORDER}
          <!-- Tab status -->
          {assign var='nb_history' value=$history|@count}
          <div class="tab-pane active" id="status">
            <h4 class="visible-print">{l s='Status' d='Admin.Global'} <span class="badge">({$nb_history})</span></h4>
            <!-- History of status -->
            <div class="table-responsive">
              <table class="table history-status row-margin-bottom">
                <tbody>
                  {foreach from=$history item=row key=key}
                    {if ($key == 0)}
                      <tr>
                        <td style="background-color:{$row['color']}"><img src="../img/os/{$row['id_order_state']|intval}.gif" width="16" height="16" alt="{$row['ostate_name']|stripslashes}" /></td>
                        <td style="background-color:{$row['color']};color:{$row['text-color']}">{$row['ostate_name']|stripslashes}</td>
                        <td style="background-color:{$row['color']};color:{$row['text-color']}">{if $row['employee_lastname']}{$row['employee_firstname']|stripslashes} {$row['employee_lastname']|stripslashes}{/if}</td>
                        <td style="background-color:{$row['color']};color:{$row['text-color']}">{dateFormat date=$row['date_add'] full=true}</td>
                        <td style="background-color:{$row['color']};color:{$row['text-color']}" class="text-right">
                          {if $row['send_email']|intval}
                            <a class="btn btn-default" href="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}&amp;sendStateEmail={$row['id_order_state']|intval}&amp;id_order_history={$row['id_order_history']|intval}" title="{l s='Resend this email to the customer' d='Admin.Orderscustomers.Help'}">
                              <i class="icon-mail-reply"></i>
                              {l s='Resend email' d='Admin.Orderscustomers.Feature'}
                            </a>
                          {/if}
                          {if $nb_history > 1}
                            <a href="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}&remove_history={$row['id_order_history']}" class="btn btn-danger remove-history" title="{l s='Supprimer'|upper d='Admin.Actions'}">
                              <i class="icon-trash"></i>
                            </a>
                          {/if}
                        </td>
                      </tr>
                    {else}
                      <tr>
                        <td><img src="../img/os/{$row['id_order_state']|intval}.gif" width="16" height="16" /></td>
                        <td>{$row['ostate_name']|stripslashes}</td>
                        <td>{if $row['employee_lastname']}{$row['employee_firstname']|stripslashes} {$row['employee_lastname']|stripslashes}{else}&nbsp;{/if}</td>
                        <td>{dateFormat date=$row['date_add'] full=true}</td>
                        <td class="text-right">
                          {if $row['send_email']|intval}
                            <a class="btn btn-default" href="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}&amp;sendStateEmail={$row['id_order_state']|intval}&amp;id_order_history={$row['id_order_history']|intval}" title="{l s='Resend this email to the customer' d='Admin.Orderscustomers.Help'}">
                              <i class="icon-mail-reply"></i>
                              {l s='Resend email' d='Admin.Orderscustomers.Feature'}
                            </a>
                          {/if}
                          {if $nb_history > 1}
                            <a href="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}&remove_history={$row['id_order_history']}" class="btn btn-danger" title="{l s='Supprimer'|upper d='Admin.Actions'}">
                              <i class="icon-trash"></i>
                            </a>
                          {/if}
                        </td>
                      </tr>
                    {/if}
                  {/foreach}
                </tbody>
              </table>
            </div>
            <!-- Change status form -->
            <form action="{$currentIndex|escape:'html':'UTF-8'}&amp;vieworder&amp;token={$smarty.get.token}" method="post" class="form-horizontal well hidden-print">
              <div class="row">
                <div class="col-lg-9">
                  <select id="id_order_state" class="chosen form-control" name="id_order_state">
                  {foreach from=$states item=state}
                    <option value="{$state['id_order_state']|intval}"{if isset($currentState) && $state['id_order_state'] == $currentState->id} selected="selected" disabled="disabled"{/if}>{$state['name']|escape}</option>
                  {/foreach}
                  </select>
                  <input type="hidden" name="id_order" value="{$order->id}" />
                </div>
                <div class="col-lg-3">
                  <button type="submit" name="submitState" id="submit_state" class="btn btn-primary">
                    {l s='Update status' d='Admin.Orderscustomers.Feature'}
                  </button>
                </div>
              </div>
            </form>
          </div>
          <!-- Tab documents -->
          <div class="tab-pane" id="documents">
            <h4 class="visible-print">{l s='Documents' d='Admin.Orderscustomers.Feature'} <span class="badge">({$order_documents|@count})</span></h4>
            {* Include document template *}
            {include file='controllers/orders/_documents.tpl'}
          </div>
        </div>
        <script>
          $('#tabOrder a').click(function (e) {
            e.preventDefault()
            $(this).tab('show')
          })
        </script>
        {*<hr />
        <!-- Tab nav -->
        <ul class="nav nav-tabs" id="myTab">
          {$HOOK_TAB_SHIP}
          <li class="active">
            <a href="#shipping">
              <i class="icon-truck "></i>
              {l s='Shipping' d='Admin.Catalog.Feature'} <span class="badge">{$order_shipping|@count}</span>
            </a>
          </li>
          <li>
            <a href="#returns">
              <i class="icon-undo"></i>
              {l s='Merchandise Returns' d='Admin.Orderscustomers.Feature'} <span class="badge">{$order_return|@count}</span>
            </a>
          </li>
        </ul>
        <!-- Tab content -->
        <div class="tab-content panel">
        {$HOOK_CONTENT_SHIP}
          <!-- Tab shipping -->
          <div class="tab-pane active" id="shipping">
            <h4 class="visible-print">{l s='Shipping' d='Admin.Catalog.Feature'} <span class="badge">({$order_shipping|@count})</span></h4>
            <!-- Shipping block -->
            {if !$order->isVirtual()}
            <div class="form-horizontal">
              {if $order->gift_message}
              <div class="form-group">
                <label class="control-label col-lg-3">{l s='Message' d='Admin.Global'}</label>
                <div class="col-lg-9">
                  <p class="form-control-static">{$order->gift_message|nl2br}</p>
                </div>
              </div>
              {/if}
              {include file='controllers/orders/_shipping.tpl'}
              {if $carrierModuleCall}
                {$carrierModuleCall}
              {/if}
              <hr />
              {if $order->recyclable}
                <span class="label label-success"><i class="icon-check"></i> {l s='Recycled packaging' d='Admin.Orderscustomers.Feature'}</span>
              {else}
                <span class="label label-inactive"><i class="icon-remove"></i> {l s='Recycled packaging' d='Admin.Orderscustomers.Feature'}</span>
              {/if}

              {if $order->gift}
                <span class="label label-success"><i class="icon-check"></i> {l s='Gift wrapping' d='Admin.Orderscustomers.Feature'}</span>
              {else}
                <span class="label label-inactive"><i class="icon-remove"></i> {l s='Gift wrapping' d='Admin.Orderscustomers.Feature'}</span>
              {/if}
            </div>
            {/if}
          </div>
          <!-- Tab returns -->
          <div class="tab-pane" id="returns">
            <h4 class="visible-print">{l s='Merchandise Returns' d='Admin.Orderscustomers.Feature'} <span class="badge">({$order_return|@count})</span></h4>
            {if !$order->isVirtual()}
            <!-- Return block -->
              {if $order_return|count > 0}
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th><span class="title_box ">{l s='Date' d='Admin.Global'}</span></th>
                      <th><span class="title_box ">{l s='Type' d='Admin.Global'}</span></th>
                      <th><span class="title_box ">{l s='Carrier' d='Admin.Shipping.Feature'}</span></th>
                      <th><span class="title_box ">{l s='Tracking number' d='Admin.Shipping.Feature'}</span></th>
                    </tr>
                  </thead>
                  <tbody>
                    {foreach from=$order_return item=line}
                    <tr>
                      <td>{$line.date_add}</td>
                      <td>{$line.type}</td>
                      <td>{$line.state_name}</td>
                      <td class="actions">
                        <span class="shipping_number_show">{if isset($line.url) && isset($line.tracking_number)}<a href="{$line.url|replace:'@':$line.tracking_number|escape:'html':'UTF-8'}">{$line.tracking_number}</a>{elseif isset($line.tracking_number)}{$line.tracking_number}{/if}</span>
                        {if $line.can_edit}
                        <form method="post" action="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}&amp;id_order_invoice={if $line.id_order_invoice}{$line.id_order_invoice|intval}{else}0{/if}&amp;id_carrier={if $line.id_carrier}{$line.id_carrier|escape:'html':'UTF-8'}{else}0{/if}">
                          <span class="shipping_number_edit" style="display:none;">
                            <button type="button" name="tracking_number">
                              {$line.tracking_number|htmlentities}
                            </button>
                            <button type="submit" class="btn btn-default" name="submitShippingNumber">
                              {l s='Update' d='Admin.Actions'}
                            </button>
                          </span>
                          <button href="#" class="edit_shipping_number_link">
                            <i class="icon-pencil"></i>
                            {l s='Edit' d='Admin.Actions'}
                          </button>
                          <button href="#" class="cancel_shipping_number_link" style="display: none;">
                            <i class="icon-remove"></i>
                            {l s='Cancel' d='Admin.Actions'}
                          </button>
                        </form>
                        {/if}
                      </td>
                    </tr>
                    {/foreach}
                  </tbody>
                </table>
              </div>
              {else}
              <div class="list-empty hidden-print">
                <div class="list-empty-msg">
                  <i class="icon-warning-sign list-empty-icon"></i>
                  {l s='No merchandise returned yet' d='Admin.Orderscustomers.Notification'}
                </div>
              </div>
              {/if}
              {if $carrierModuleCall}
                {$carrierModuleCall}
              {/if}
            {/if}
          </div>
        </div>
        <script>
          $('#myTab a').click(function (e) {
            e.preventDefault()
            $(this).tab('show')
          })
        </script>*}
      </div>
      <!-- Payments block -->
      {assign var='nb_payments' value=$order->getOrderPayments()|@count}
      <div id="formAddPaymentPanel" class="panel">
        <div class="panel-heading">
          <i class="icon-money"></i>
          {l s="Payment" d='Admin.Global'} <span class="badge">{$nb_payments}</span>
        </div>
        {if count($order->getOrderPayments()) > 0}
          <p class="alert alert-danger"{if round($orders_total_paid_tax_incl, 2) == round($total_paid, 2) || (isset($currentState) && $currentState->id == 6)} style="display: none;"{/if}>
            {l s='Warning' d='Admin.Global'}
            <strong>{displayPrice price=$total_paid currency=$currency->id}</strong>
            {l s='paid instead of' d='Admin.Orderscustomers.Notification'}
            <strong class="total_paid">{displayPrice price=$orders_total_paid_tax_incl currency=$currency->id}</strong>
            {foreach $order->getBrother() as $brother_order}
              {if $brother_order@first}
                {if count($order->getBrother()) == 1}
                  <br />{l s='This warning also concerns order ' d='Admin.Orderscustomers.Notification'}
                {else}
                  <br />{l s='This warning also concerns the next orders:' d='Admin.Orderscustomers.Notification'}
                {/if}
              {/if}
              <a href="{$current_index}&amp;vieworder&amp;id_order={$brother_order->id}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}">
                #{'%06d'|sprintf:$brother_order->id}
              </a>
            {/foreach}
          </p>
        {/if}
        <form id="formAddPayment"  method="post" action="{$current_index}&amp;vieworder&amp;id_order={$order->id}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}">
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th><span class="title_box ">{l s='Date' d='Admin.Global'}</span></th>
                  <th><span class="title_box ">{l s='Payment method' d='Admin.Orderscustomers.Feature'}</span></th>
                  <th><span class="title_box ">{l s='Transaction ID' d='Admin.Orderscustomers.Feature'}</span></th>
                  <th><span class="title_box ">{l s='Amount' d='Admin.Global'}</span></th>
                  <th><span class="title_box ">{l s='Invoice' d='Admin.Global'}</span></th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                {foreach from=$order->getOrderPaymentCollection() item=payment}
                <tr>
                  <td>{dateFormat date=$payment->date_add full=true}</td>
                  <td>{$payment->payment_method|escape:'html':'UTF-8'}</td>
                  <td>{$payment->transaction_id|escape:'html':'UTF-8'}</td>
                  <td>{displayPrice price=$payment->amount currency=$payment->id_currency}</td>
                  <td>
                  {if $invoice = $payment->getOrderInvoice($order->id)}
                    {$invoice->getInvoiceNumberFormatted($current_id_lang, $order->id_shop)}
                  {else}
                  {/if}
                  </td>
                  <td style="min-width:103px">
                    <button class="btn btn-default open_payment_information">
                      {l s='Details' d='Admin.Global'}
                    </button>
                    {if $nb_payments > 1}
                      <a href="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}&remove_payment={$payment->id}" class="btn btn-danger remove-payment" title="{l s='Supprimer'|upper d='Admin.Actions'}">
                        <i class="icon-trash"></i>
                      </a>
                    {/if}
                  </td>
                </tr>
                <tr class="payment_information" style="display: none;">
                  <td colspan="5">
                    <p>
                      <b>{l s='Card Number' d='Admin.Orderscustomers.Feature'}</b>&nbsp;
                      {if $payment->card_number}
                        {$payment->card_number}
                      {else}
                        <i>{l s='Not defined' d='Admin.Orderscustomers.Feature'}</i>
                      {/if}
                    </p>
                    <p>
                      <b>{l s='Card Brand' d='Admin.Orderscustomers.Feature'}</b>&nbsp;
                      {if $payment->card_brand}
                        {$payment->card_brand}
                      {else}
                        <i>{l s='Not defined' d='Admin.Orderscustomers.Feature'}</i>
                      {/if}
                    </p>
                    <p>
                      <b>{l s='Card Expiration' d='Admin.Orderscustomers.Feature'}</b>&nbsp;
                      {if $payment->card_expiration}
                        {$payment->card_expiration}
                      {else}
                        <i>{l s='Not defined' d='Admin.Orderscustomers.Feature'}</i>
                      {/if}
                    </p>
                    <p>
                      <b>{l s='Card Holder' d='Admin.Orderscustomers.Feature'}</b>&nbsp;
                      {if $payment->card_holder}
                        {$payment->card_holder}
                      {else}
                        <i>{l s='Not defined' d='Admin.Orderscustomers.Feature'}</i>
                      {/if}
                    </p>
                  </td>
                </tr>
                {foreachelse}
                <tr>
                  <td class="list-empty hidden-print" colspan="6">
                    <div class="list-empty-msg">
                      <i class="icon-warning-sign list-empty-icon"></i>
                      {l s='No payment methods are available' d='Admin.Orderscustomers.Notification'}
                    </div>
                  </td>
                </tr>
                {/foreach}
                <tr class="current-edit hidden-print">
                  <td>
                    <div class="input-group fixed-width-xl">
                      <input type="text" name="payment_date" class="datepicker" value="{date('Y-m-d')}" />
                      <div class="input-group-addon">
                        <i class="icon-calendar-o"></i>
                      </div>
                    </div>
                  </td>
                  <td>
                    <input name="payment_method" list="payment_method" class="payment_method form-control fixed-width-sm">
                    <datalist id="payment_method">
                    {foreach from=$payment_methods item=payment_method}
                      <option value="{$payment_method}">
                    {/foreach}
                    </datalist>
                  </td>
                  <td>
                    <input type="text" name="payment_transaction_id" value="" class="form-control fixed-width-sm"/>
                  </td>
                  <td>
                    <input type="text" name="payment_amount" value="" class="form-control fixed-width-sm pull-left" />
                    <select name="payment_currency" class="payment_currency form-control fixed-width-xs pull-left">
                      {foreach from=$currencies item=current_currency}
                        <option value="{$current_currency['id_currency']}"{if $current_currency['id_currency'] == $currency->id} selected="selected"{/if}>{$current_currency['sign']}</option>
                      {/foreach}
                    </select>
                  </td>
                  <td>
                    {if count($invoices_collection) > 0}
                      <select name="payment_invoice" id="payment_invoice">
                      {foreach from=$invoices_collection item=invoice}
                        <option value="{$invoice->id}" selected="selected">{$invoice->getInvoiceNumberFormatted($current_id_lang, $order->id_shop)}</option>
                      {/foreach}
                      </select>
                    {/if}
                  </td>
                  <td class="actions">
                    <button class="btn btn-primary" type="submit" name="submitAddPayment">
                      {l s='Add' d='Admin.Actions'}
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </form>
        {if (!$order->valid && sizeof($currencies) > 1)}
          <form class="form-horizontal well" method="post" action="{$currentIndex|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}">
            <div class="row">
              <label class="control-label col-lg-3">{l s='Change currency' d='Admin.Orderscustomers.Feature'}</label>
              <div class="col-lg-6">
                <select name="new_currency">
                {foreach from=$currencies item=currency_change}
                  {if $currency_change['id_currency'] != $order->id_currency}
                  <option value="{$currency_change['id_currency']}">{$currency_change['name']} - {$currency_change['sign']}</option>
                  {/if}
                {/foreach}
                </select>
                <p class="help-block">{l s='Do not forget to update your exchange rate before making this change.' d='Admin.Orderscustomers.Help'}</p>
              </div>
              <div class="col-lg-3">
                <button type="submit" class="btn btn-default" name="submitChangeCurrency"><i class="icon-refresh"></i> {l s='Change' d='Admin.Orderscustomers.Feature'}</button>
              </div>
            </div>
          </form>
        {/if}
      </div>

      {hook h="displayAdminOrderLeft" id_order=$order->id}
    </div>
    <div class="col-lg-5">
      <!-- Customer informations -->
      <div class="panel">
        {if $customer->id}
          <div class="panel-heading">
            <i class="icon-user"></i>
            {l s='Customer' d='Admin.Global'}
            <span class="badge">
              <a href="?tab=AdminCustomers&amp;id_customer={$customer->id}&amp;updatecustomer&amp;token={getAdminToken tab='AdminCustomers'}" target="_blank">
                <i class="icon-edit"></i>
              </a>
            </span>
            <span class="badge">
              <a href="?tab=AdminCustomers&amp;id_customer={$customer->id}&amp;viewcustomer&amp;token={getAdminToken tab='AdminCustomers'}">
                {if Configuration::get('PS_B2B_ENABLE')}{$customer->company} - {/if}
                {$gender->name|escape:'html':'UTF-8'}
                {$customer->firstname}
                {$customer->lastname}
              </a>
            </span>
            <span class="badge">
              {l s='#' d='Admin.Orderscustomers.Feature'}{$customer->id}
            </span>
          </div>
          <div class="row">
            <div class="col-xs-6">
              {if ($customer->isGuest())}
                {l s='This order has been placed by a guest.' d='Admin.Orderscustomers.Feature'}
                {if (!Customer::customerExists($customer->email))}
                  <form method="post" action="index.php?tab=AdminCustomers&amp;id_customer={$customer->id}&amp;id_order={$order->id|intval}&amp;token={getAdminToken tab='AdminCustomers'}">
                    <input type="hidden" name="id_lang" value="{$order->id_lang}" />
                    <input class="btn btn-default" type="submit" name="submitGuestToCustomer" value="{l s='Transform a guest into a customer'}" />
                    <p class="help-block">{l s='This feature will generate a random password and send an email to the customer.' d='Admin.Orderscustomers.Help'}</p>
                  </form>
                {else}
                  <div class="alert alert-warning">
                    {l s='A registered customer account has already claimed this email address' d='Admin.Orderscustomers.Notification'}
                  </div>
                {/if}
              {else}
                <dl class="well list-detail">
                  <dt>{l s='Email' d='Admin.Global'}</dt>
                    <dd><a href="mailto:{$customer->email}"><i class="icon-envelope-o"></i> {$customer->email}</a></dd>
                  <dt>{l s='Type de compte' d='Admin.Orderscustomers.Feature'}</dt>
                    <dd class="text-muted">{if $customer->getAccountType()}{$customer->getAccountType()->name|default:'-'}{else}-{/if}</dd>
                  <dt>{l s='Référence E-deal' d='Admin.Orderscustomers.Feature'}</dt>
                    <dd class="text-muted">{$customer->reference|default:'-'}</dd>
                  <dt>{l s='Account registered' d='Admin.Orderscustomers.Feature'}</dt>
                    <dd class="text-muted"><i class="icon-calendar-o"></i> {dateFormat date=$customer->date_add full=true}</dd>
                  <dt>{l s='Valid orders placed' d='Admin.Orderscustomers.Feature'}</dt>
                    <dd><span class="badge">{$customerStats['nb_orders']|intval}</span></dd>
                  <dt>{l s='Total spent since registration' d='Admin.Orderscustomers.Feature'}</dt>
                    <dd><span class="badge badge-success">{displayPrice price=Tools::ps_round(Tools::convertPrice($customerStats['total_orders'], $currency), 2) currency=$currency->id}</span></dd>
                  {if Configuration::get('PS_B2B_ENABLE')}
                    <dt>{l s='Société' d='Admin.Orderscustomers.Feature'}</dt>
                      <dd class="text-muted">{$customer->company|default:'-'}</dd>
                    <dt>{l s='SIRET' d='Admin.Orderscustomers.Feature'}</dt>
                      <dd class="text-muted">{$customer->siret|default:'-'}</dd>
                    <dt>{l s='APE' d='Admin.Orderscustomers.Feature'}</dt>
                      <dd class="text-muted">{$customer->ape|default:'-'}</dd>
                  {/if}
                </dl>
              {/if}
            </div>

            <div class="col-xs-6">
              <div class="form-group hidden-print">
                <a href="?tab=AdminCustomers&amp;id_customer={$customer->id}&amp;viewcustomer&amp;token={getAdminToken tab='AdminCustomers'}" class="btn btn-default btn-block">{l s='View full details...' d='Admin.Orderscustomers.Feature'}</a>
              </div>
              <div class="panel panel-sm">
                <div class="panel-heading">
                  <i class="icon-eye-slash"></i>
                  {l s='Private note' d='Admin.Orderscustomers.Feature'}
                </div>
                <form id="customer_note" class="form-horizontal" action="ajax.php" method="post" onsubmit="saveCustomerNote({$customer->id});return false;" >
                  <div class="form-group">
                    <div class="col-lg-12">
                      <textarea name="note" id="noteContent" class="textarea-autosize" onkeyup="$(this).val().length > 0 ? $('#submitCustomerNote').removeAttr('disabled') : $('#submitCustomerNote').attr('disabled', 'disabled')">{$customer->note}</textarea>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-12">
                      <button type="submit" id="submitCustomerNote" class="btn btn-default pull-right" disabled="disabled">
                        <i class="icon-save"></i>
                        {l s='Save' d='Admin.Actions'}
                      </button>
                    </div>
                  </div>
                  <span id="note_feedback"></span>
                </form>
              </div>
            </div>
          </div>
        {/if}
      </div>
      <div class="panel">
        <div class="panel-heading">
          <i class="icon-envelope"></i> {l s='Messages' d='Admin.Global'} <span class="badge">{sizeof($customer_thread_message)}</span>
        </div>
        {if (sizeof($messages))}
          <div class="panel panel-highlighted">
            <div class="message-item">
              {foreach from=$messages item=message}
                <div class="message-avatar">
                  <div class="avatar-md">
                    <i class="icon-user icon-2x"></i>
                  </div>
                </div>
                <div class="message-body">

                  <span class="message-date">&nbsp;<i class="icon-calendar"></i>
                    {dateFormat date=$message['date_add']} -
                  </span>
                  <h4 class="message-item-heading">
                    {if ($message['elastname']|escape:'html':'UTF-8')}{$message['efirstname']|escape:'html':'UTF-8'}
                      {$message['elastname']|escape:'html':'UTF-8'}{else}{$message['cfirstname']|escape:'html':'UTF-8'} {$message['clastname']|escape:'html':'UTF-8'}
                    {/if}
                    {if ($message['private'] == 1)}
                      <span class="badge badge-info">{l s='Private' d='Admin.Orderscustomers.Feature'}</span>
                    {/if}
                  </h4>
                  <p class="message-item-text">
                    {$message['message']|escape:'html':'UTF-8'|nl2br}
                  </p>
                </div>
                {*if ($message['is_new_for_me'])}
                  <a class="new_message" title="{l s='Mark this message as \'viewed\''}" href="{$smarty.server.REQUEST_URI}&amp;token={$smarty.get.token}&amp;messageReaded={$message['id_message']}">
                    <i class="icon-ok"></i>
                  </a>
                {/if*}
              {/foreach}
            </div>
          </div>
        {else}
          <div class="alert alert-info">
            {l s="Aucun message de la part du client"}
          </div>
        {/if}
        {*<div id="messages" class="well hidden-print">
          <form action="{$smarty.server.REQUEST_URI|escape:'html':'UTF-8'}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}" method="post" onsubmit="if (getE('visibility').checked == true) return confirm('{l s='Do you want to send this message to the customer?' d='Admin.Orderscustomers.Notification'}');">
            <div id="message" class="form-horizontal">
              <div class="form-group">
                <label class="control-label col-lg-3">{l s='Choose a standard message' d='Admin.Orderscustomers.Feature'}</label>
                <div class="col-lg-9">
                  <select class="chosen form-control" name="order_message" id="order_message" onchange="orderOverwriteMessage(this, '{l s='Do you want to overwrite your existing message?' d='Admin.Orderscustomers.Notification'}')">
                    <option value="0" selected="selected">-</option>
                    {foreach from=$orderMessages item=orderMessage}
                    <option value="{$orderMessage['message']|escape:'html':'UTF-8'}">{$orderMessage['name']}</option>
                    {/foreach}
                  </select>
                  <p class="help-block">
                    <a href="{$link->getAdminLink('AdminOrderMessage')|escape:'html':'UTF-8'}">
                      {l s='Configure predefined messages' d='Admin.Orderscustomers.Feature'}
                      <i class="icon-external-link"></i>
                    </a>
                  </p>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-lg-3">{l s='Display to customer?' d='Admin.Orderscustomers.Feature'}</label>
                <div class="col-lg-9">
                  <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" name="visibility" id="visibility_on" value="0" />
                    <label for="visibility_on">
                      {l s='Yes' d='Admin.Global'}
                    </label>
                    <input type="radio" name="visibility" id="visibility_off" value="1" checked="checked" />
                    <label for="visibility_off">
                      {l s='No' d='Admin.Global'}
                    </label>
                    <a class="slide-button btn"></a>
                  </span>
                </div>
              </div>

              <div class="form-group">
                <label class="control-label col-lg-3">{l s='Message' d='Admin.Global'}</label>
                <div class="col-lg-9">
                  <textarea id="txt_msg" class="textarea-autosize" name="message">{Tools::getValue('message')|escape:'html':'UTF-8'}</textarea>
                  <p id="nbchars"></p>
                </div>
              </div>


              <input type="hidden" name="id_order" value="{$order->id}" />
              <input type="hidden" name="id_customer" value="{$order->id_customer}" />
              <button type="submit" id="submitMessage" class="btn btn-primary pull-right" name="submitMessage">
                {l s='Send message' d='Admin.Orderscustomers.Feature'}
              </button>
              <a class="btn btn-default" href="{$link->getAdminLink('AdminCustomerThreads')|escape:'html':'UTF-8'}&amp;id_order={$order->id|intval}">
                {l s='Show all messages' d='Admin.Orderscustomers.Feature'}
                <i class="icon-external-link"></i>
              </a>
            </div>
          </form>
        </div>*}
      </div>
      
      {hook h="displayAdminOrderRight" id_order=$order->id}
    </div>
  </div>

  <div class="row">

    {* ADRESSE DE FACTURATION *}
    <div class="col-lg-6">
      <div class="panel">
        <div class="panel-heading">
          <i class="icon-file-text"></i>
          {l s='Adresse de facturation' d='Admin.Global'}
        </div>
        {if $can_edit}
          <form class="form-horizontal hidden-print" method="post" action="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}">
            <div class="form-group">
              <div class="col-lg-9">
                <select name="id_address">
                  {foreach from=$customer_addresses item=address}
                    <option value="{$address['id_address']}" {if $address['id_address'] == $order->id_address_invoice} selected="selected"{/if}>
                      {$address['alias']} - {$address['address1']} {$address['postcode']} {$address['city']} {if !empty($address['state'])}{$address['state']}{/if}, {$address['country']}
                    </option>
                  {/foreach}
                </select>
              </div>
              <div class="col-lg-3">
                <button class="btn btn-default" type="submit" name="submitAddressInvoice"><i class="icon-refresh"></i> {l s='Change' d='Admin.Orderscustomers.Feature'}</button>
              </div>
            </div>
          </form>
        {/if}
        <div class="well">
          <div class="row">
            <div class="col-sm-6">
              <a class="btn btn-default pull-right" href="?tab=AdminAddresses&amp;id_address={$addresses.invoice->id}&amp;updateaddress&amp;realedit=1&amp;id_order={$order->id}&amp;address_type=2&amp;back={$smarty.server.REQUEST_URI|urlencode}&amp;token={getAdminToken tab='AdminAddresses'}">
                <i class="icon-pencil"></i>
                {l s='Edit' d='Admin.Actions'}
              </a>
              {displayAddressDetail address=$addresses.invoice newLine='<br />'}
              {if $addresses.invoice->other}
                <hr />{$addresses.invoice->other}<br />
              {/if}
            </div>
            {*<div class="col-sm-6 hidden-print">
              <div id="map-invoice-canvas" style="height: 190px"></div>
            </div>*}
          </div>
        </div>
      </div>
    </div>

    {* ADRESSE DE LIVRAISON *}
    <div class="col-lg-6">
      <div class="panel">
        <div class="panel-heading">
          <i class="icon-truck"></i>
          {l s='Adresse de livraison' d='Admin.Global'}
        </div>
        {if !$order->isVirtual()}
          {if $can_edit}
            <form class="form-horizontal hidden-print" method="post" action="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}">
              <div class="form-group">
                <div class="col-lg-9">
                  <select name="id_address">
                    {foreach from=$customer_addresses item=address}
                      <option value="{$address['id_address']}" {if $address['id_address'] == $order->id_address_delivery}selected="selected"{/if}>
                        {$address['alias']} - {$address['address1']} {$address['postcode']} {$address['city']} {if !empty($address['state'])}{$address['state']}{/if}, {$address['country']}
                      </option>
                    {/foreach}
                  </select>
                </div>
                <div class="col-lg-3">
                  <button class="btn btn-default" type="submit" name="submitAddressShipping"><i class="icon-refresh"></i> {l s='Change' d='Admin.Orderscustomers.Feature'}</button>
                </div>
              </div>
            </form>
          {/if}
          <div class="well">
            <div class="row">
              <div class="col-sm-6">
                <a class="btn btn-default pull-right" href="?tab=AdminAddresses&amp;id_address={$addresses.delivery->id}&amp;updateaddress&amp;realedit=1&amp;id_order={$order->id}&amp;address_type=1&amp;token={getAdminToken tab='AdminAddresses'}&amp;back={$smarty.server.REQUEST_URI|urlencode}">
                  <i class="icon-pencil"></i>
                  {l s='Edit' d='Admin.Actions'}
                </a>
                {displayAddressDetail address=$addresses.delivery newLine='<br />'}
                {if $addresses.delivery->other}
                  <hr />{$addresses.delivery->other}<br />
                {/if}
              </div>
              {*<div class="col-sm-6 hidden-print">
                <div id="map-delivery-canvas" style="height: 190px"></div>
              </div>*}
            </div>
          </div>
        {/if}
      </div>
    </div>

  </div>

  <div id="informations" class="row">
    <div class="col-lg-6">
      <div class="panel">
        <div class="panel-heading">
          <i class="icon-home"></i>
          {l s='Informations fournisseur' d='Admin.Global'}
        </div>
        <form method="post" action="#informations">
          <div class="form-group">
            <textarea class="form-control" name="supplier_information">{$order->supplier_information}</textarea>
          </div>
          <div class="panel-footer text-right">
            <button type="submit" class="btn btn-success" name="save_supplier_information">
              <i class="process-icon-save"></i>
              {l s='Save' d='Admin.Actions'}
            </button>
          </div>
        </form>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="panel">
        <div class="panel-heading">
          <i class="icon-truck"></i>
          {l s='Informations livraison' d='Admin.Global'}
        </div>
        <form method="post" action="#informations">
          <div class="form-group">
            <textarea class="form-control" name="delivery_information">{$order->delivery_information}</textarea>
          </div>
          <div class="panel-footer text-right">
            <button type="submit" class="btn btn-success" name="save_delivery_information">
              <i class="process-icon-save"></i>
              {l s='Save' d='Admin.Actions'}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {hook h="displayAdminOrder" id_order=$order->id}

  <div class="row">
    <div class="col-lg-12">
      {include file="../../obligations.tpl"}
    </div>
  </div>

  {include file="../../product_modifications.tpl"}

  <div id="start_products" class="row">
    <div class="col-lg-12">
      <form class="container-command-top-spacing" action="{$current_index}&amp;vieworder&amp;token={$smarty.get.token|escape:'html':'UTF-8'}&amp;id_order={$order->id|intval}" method="post" onsubmit="return orderDeleteProduct('{l s='This product cannot be returned.' d='Admin.Orderscustomers.Notification'}', '{l s='Quantity to cancel is greater than quantity available.' d='Admin.Orderscustomers.Notification'}');">
        <input type="hidden" name="id_order" value="{$order->id}" />
        <div style="display: none">
          <input type="hidden" value="{$order->getWarehouseList()|implode}" id="warehouse_list" />
        </div>

        <div class="panel">
          <div class="panel-heading">
            <i class="icon-shopping-cart"></i>
            {l s='Products' d='Admin.Global'} <span class="badge">{$products|@count}</span>
            <span class="panel-heading-action">
              <a href="#modifications" class="list-toolbar-btn toggle-products-edit" title="{l s='Modifications'}">
                <i class="process-icon-edit"></i>
              </a>
            </span>
          </div>
          <div id="refundForm">
          <!--
            <a href="#" class="standard_refund"><img src="../img/admin/add.gif" alt="{l s='Process a standard refund'}" /> {l s='Process a standard refund'}</a>
            <a href="#" class="partial_refund"><img src="../img/admin/add.gif" alt="{l s='Process a partial refund'}" /> {l s='Process a partial refund'}</a>
          -->
          </div>

          {capture "TaxMethod"}
            {if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
              {l s='Tax excluded' d='Admin.Global'}
            {else}
              {l s='Tax included' d='Admin.Global'}
            {/if}
          {/capture}
          {if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
            <input type="hidden" name="TaxMethod" value="0">
          {else}
            <input type="hidden" name="TaxMethod" value="1">
          {/if}
          <div class="table-responsive">
            <table class="table" id="orderProducts">
              <thead>
                <tr>
                  <th></th>
                  <th>{l s='Référence' d='Admin.Orderscustomers.Feature'}</th>
                  <th><span class="title_box">{l s='Product' d='Admin.Global'}</span></th>
                  <th>
                    <span class="title_box ">{l s='Achat' d='Admin.Advparameters.Feature'}</span>
                    <small class="text-muted">{l s="HT"}</small>
                  </th>
                  <th>
                    <span class="title_box ">{l s='Price per unit' d='Admin.Advparameters.Feature'}</span>
                    <small class="text-muted">{$smarty.capture.TaxMethod}</small>
                  </th>
                  <th class="text-center"><span class="title_box ">{l s='Qty' d='Admin.Orderscustomers.Feature'}</span></th>
                  {if $display_warehouse}<th><span class="title_box ">{l s='Warehouse'}</span></th>{/if}
                  {if ($order->hasBeenPaid())}<th class="text-center"><span class="title_box ">{l s='Refunded'}</span></th>{/if}
                  {if ($order->hasBeenDelivered() || $order->hasProductReturned())}
                    <th class="text-center"><span class="title_box ">{l s='Returned' d='Admin.Orderscustomers.Feature'}</span></th>
                  {/if}
                  <th>
                    <span class="title_box ">{l s='Total' d='Admin.Global'}</span>
                    <small class="text-muted">{$smarty.capture.TaxMethod}</small>
                  </th>
                  <th style="display: none;" class="add_product_fields"></th>
                  <th style="display: none;" class="edit_product_fields"></th>
                  <th style="display: none;" class="standard_refund_fields">
                    <i class="icon-minus-sign"></i>
                    {if ($order->hasBeenDelivered() || $order->hasBeenShipped())}
                      {l s='Return' d='Admin.Orderscustomers.Feature'}
                    {elseif ($order->hasBeenPaid())}
                      {l s='Refund' d='Admin.Orderscustomers.Feature'}
                    {else}
                      {l s='Cancel' d='Admin.Actions'}
                    {/if}
                  </th>
                  <th style="display:none" class="partial_refund_fields">
                    <span class="title_box ">{l s='Partial refund' d='Admin.Orderscustomers.Feature'}</span>
                  </th>
                  <th class="text-center">{l s='Infos livraison' d='Admin.Orderscustomers.Feature'}</th>
                  <th class="text-center">{l s='Commentaire' d='Admin.Orderscustomers.Feature'}</th>
                  {if !$order->hasBeenDelivered()}
                  <th></th>
                  {/if}
                </tr>
              </thead>
              <tbody>
              {foreach from=$products item=product key=k}
                {* Include customized datas partial *}
                {include file='controllers/orders/_customized_data.tpl'}
                {* Include product line partial *}
                {include file=_PS_OVERRIDE_DIR_|cat:'controllers/admin/templates/orders/_product_line.tpl'}
              {/foreach}
              {if $can_edit}
                {include file='controllers/orders/_new_product.tpl'}
              {/if}
              </tbody>
            </table>
          </div>

          {if $can_edit}
          <div class="row-margin-bottom row-margin-top order_action">
          {if !$order->hasBeenDelivered()}
            <button type="button" id="add_product" class="btn btn-default">
              <i class="icon-plus-sign"></i>
              {l s='Add a product' d='Admin.Orderscustomers.Feature'}
            </button>
          {/if}
            <button id="add_voucher" class="btn btn-default" type="button" >
              <i class="icon-ticket"></i>
              {l s='Add a new discount' d='Admin.Orderscustomers.Feature'}
            </button>
          </div>
          {/if}
          <div class="clear">&nbsp;</div>
          <div class="row">
            <div class="col-xs-6">
              <div class="alert alert-warning">
                {* [1][/1] is for a HTML tag. *}
                {l
                  s='For this customer group, prices are displayed as: [1]%tax_method%[/1]'
                  sprintf=[
                    '%tax_method%' => $smarty.capture.TaxMethod,
                    '[1]' => '<strong>',
                    '[/1]' => '</strong>'
                  ]
                  d='Admin.Orderscustomers.Notification'
                }
                {*if !Configuration::get('PS_ORDER_RETURN')}
                  <br/><strong>{l s='Merchandise returns are disabled' d='Admin.Orderscustomers.Notification'}</strong>
                {/if*}
              </div>
            </div>
            <div class="col-xs-6">
              <div class="panel panel-vouchers" style="{if !sizeof($discounts)}display:none;{/if}">
                {if (sizeof($discounts) || $can_edit)}
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>
                          <span class="title_box ">
                            {l s='Discount name' d='Admin.Orderscustomers.Feature'}
                          </span>
                        </th>
                        <th>
                          <span class="title_box ">
                            {l s='Value' d='Admin.Orderscustomers.Feature'}
                          </span>
                        </th>
                        {if $can_edit}
                        <th></th>
                        {/if}
                      </tr>
                    </thead>
                    <tbody>
                      {foreach from=$discounts item=discount}
                      <tr>
                        <td>{$discount['name']}</td>
                        <td>
                        {if $discount['value'] != 0.00}
                          -
                        {/if}
                        {displayPrice price=$discount['value'] currency=$currency->id}
                        </td>
                        {if $can_edit}
                        <td>
                          <a href="{$current_index}&amp;submitDeleteVoucher&amp;id_order_cart_rule={$discount['id_order_cart_rule']}&amp;id_order={$order->id}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}">
                            <i class="icon-minus-sign"></i>
                            {l s='Delete voucher' d='Admin.Orderscustomers.Feature'}
                          </a>
                        </td>
                        {/if}
                      </tr>
                      {/foreach}
                    </tbody>
                  </table>
                </div>
                <div class="current-edit" id="voucher_form" style="display:none;">
                  {include file='controllers/orders/_discount_form.tpl'}
                </div>
                {/if}
              </div>
              <div class="panel panel-total">
                <div class="table-responsive">
                  <table class="table">
                    {* Assign order price *}
                    {if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
                      {assign var=order_product_price value=($order->total_products)}
                      {assign var=order_discount_price value=$order->total_discounts_tax_excl}
                      {assign var=order_wrapping_price value=$order->total_wrapping_tax_excl}
                      {assign var=order_shipping_price value=$order->total_shipping_tax_excl}
                      {assign var=shipping_refundable value=$shipping_refundable_tax_excl}
                    {else}
                      {assign var=order_product_price value=$order->total_products}
                      {assign var=order_discount_price value=$order->total_discounts_tax_incl}
                      {assign var=order_wrapping_price value=$order->total_wrapping_tax_incl}
                      {assign var=order_shipping_price value=$order->total_shipping_tax_incl}
                      {assign var=shipping_refundable value=$shipping_refundable_tax_incl}
                    {/if}
                    <tr id="total_products">
                      <td class="text-right">{l s='Products:' d='Admin.Orderscustomers.Feature'}</td>
                      <td class="amount text-right nowrap">
                        {displayPrice price=$order_product_price currency=$currency->id}
                      </td>
                      <td class="partial_refund_fields current-edit" style="display:none;"></td>
                    </tr>
                    <tr id="total_discounts" {if $order->total_discounts_tax_incl == 0}style="display: none;"{/if}>
                      <td class="text-right">{l s='Discounts' d='Admin.Orderscustomers.Feature'}</td>
                      <td class="amount text-right nowrap">
                        -{displayPrice price=$order_discount_price currency=$currency->id}
                      </td>
                      <td class="partial_refund_fields current-edit" style="display:none;"></td>
                    </tr>
                    <tr id="total_wrapping" {if $order->total_wrapping_tax_incl == 0}style="display: none;"{/if}>
                      <td class="text-right">{l s='Wrapping' d='Admin.Orderscustomers.Feature'}</td>
                      <td class="amount text-right nowrap">
                        {displayPrice price=$order_wrapping_price currency=$currency->id}
                      </td>
                      <td class="partial_refund_fields current-edit" style="display:none;"></td>
                    </tr>
                    <tr id="total_shipping">
                      <td class="text-right">{l s='Shipping' d='Admin.Catalog.Feature'}</td>
                      <td class="amount text-right nowrap" >
                        {displayPrice price=$order_shipping_price currency=$currency->id}
                      </td>
                      <td class="partial_refund_fields current-edit" style="display:none;">
                        <div class="input-group">
                          <div class="input-group-addon">
                            {$currency->sign}
                          </div>
                          <input type="text" name="partialRefundShippingCost" value="0" />
                        </div>
                        <p class="help-block"><i class="icon-warning-sign"></i> {l
                            s='(Max %s %s)'
                            sprintf=[Tools::displayPrice(Tools::ps_round($shipping_refundable, 2), $currency->id) , $smarty.capture.TaxMethod]
                            d='Admin.Orderscustomers.Feature'
                            }
                        </p>
                      </td>
                    </tr>
                    <tr id="total_taxes">
                      <td class="text-right"><b>{l s='Total HT' d='Admin.Global'}</b></td>
                      <td class="amount text-right nowrap" ><b>{displayPrice price=($order->total_paid_tax_excl) currency=$currency->id}</b></td>
                      <td class="partial_refund_fields current-edit" style="display:none;"></td>
                    </tr>
                    {*if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)*}
                    <tr id="total_taxes">
                      <td class="text-right"><b>{l s='Taxes' d='Admin.Global'}</b></td>
                      <td class="amount text-right nowrap" ><b>{displayPrice price=($order->total_paid_tax_incl-$order->total_paid_tax_excl) currency=$currency->id}</b></td>
                      <td class="partial_refund_fields current-edit" style="display:none;"></td>
                    </tr>
                    {*/if*}
                    {assign var=order_total_price value=$order->total_paid_tax_incl}
                    <tr id="total_order">
                      <td class="text-right"><strong>{l s='Total TTC' d='Admin.Global'}</strong></td>
                      <td class="amount text-right nowrap">
                        <strong>{displayPrice price=$order_total_price currency=$currency->id}</strong>
                      </td>
                      <td class="partial_refund_fields current-edit" style="display:none;"></td>
                    </tr>
                    <tr id="margin">
                      <td class="text-right" style="background-color:seashell"><strong>{l s="Marge" d="Admin.global"}</strong></td>
                      <td class="amount text-right nowrap" style="background-color:seashell">
                        {assign var=margin value=$order->total_paid_tax_incl - Order::sumBuyingPrice([$order->id], true)}
                        {displayPrice price=$margin currency=$currency->id}
                        | <strong>{Tools::getMarginRate($margin, $order->total_paid_tax_incl)} %</strong>
                      </td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div style="display: none;" class="standard_refund_fields form-horizontal panel">
            <div class="form-group">
              {if ($order->hasBeenDelivered() && Configuration::get('PS_ORDER_RETURN'))}
              <p class="checkbox">
                <label for="reinjectQuantities">
                  <input type="checkbox" id="reinjectQuantities" name="reinjectQuantities" />
                  {l s='Re-stock products' d='Admin.Orderscustomers.Feature'}
                </label>
              </p>
              {/if}
              {if ((!$order->hasBeenDelivered() && $order->hasBeenPaid()) || ($order->hasBeenDelivered() && Configuration::get('PS_ORDER_RETURN')))}
              <p class="checkbox">
                <label for="generateCreditSlip">
                  <input type="checkbox" id="generateCreditSlip" name="generateCreditSlip" onclick="toggleShippingCost()" />
                  {l s='Generate a credit slip' d='Admin.Orderscustomers.Feature'}
                </label>
              </p>
              <p class="checkbox">
                <label for="generateDiscount">
                  <input type="checkbox" id="generateDiscount" name="generateDiscount" onclick="toggleShippingCost()" />
                  {l s='Generate a voucher' d='Admin.Orderscustomers.Feature'}
                </label>
              </p>
              <p class="checkbox" id="spanShippingBack" style="display:none;">
                <label for="shippingBack">
                  <input type="checkbox" id="shippingBack" name="shippingBack" />
                  {l s='Repay shipping costs' d='Admin.Orderscustomers.Feature'}
                </label>
              </p>
              {if $order->total_discounts_tax_excl > 0 || $order->total_discounts_tax_incl > 0}
              <br/><p>{l s='This order has been partially paid by voucher. Choose the amount you want to refund:' d='Admin.Orderscustomers.Feature'}</p>
              <p class="radio">
                <label id="lab_refund_total_1" for="refund_total_1">
                  <input type="radio" value="0" name="refund_total_voucher_off" id="refund_total_1" checked="checked" />
                  {l s='Include amount of initial voucher: ' d='Admin.Orderscustomers.Feature'}
                </label>
              </p>
              <p class="radio">
                <label id="lab_refund_total_2" for="refund_total_2">
                  <input type="radio" value="1" name="refund_total_voucher_off" id="refund_total_2"/>
                  {l s='Exclude amount of initial voucher: ' d='Admin.Orderscustomers.Feature'}
                </label>
              </p>
              <div class="nowrap radio-inline">
                <label id="lab_refund_total_3" class="pull-left" for="refund_total_3">
                  {l s='Amount of your choice: ' d='Admin.Orderscustomers.Feature'}
                  <input type="radio" value="2" name="refund_total_voucher_off" id="refund_total_3"/>
                </label>
                <div class="input-group col-lg-1 pull-left">
                  <div class="input-group-addon">
                    {$currency->sign}
                  </div>
                  <input type="text" class="input fixed-width-md" name="refund_total_voucher_choose" value="0"/>
                </div>
              </div>
              {/if}
            {/if}
            </div>
            {if (!$order->hasBeenDelivered() || ($order->hasBeenDelivered() && Configuration::get('PS_ORDER_RETURN')))}
            <div class="row">
              <input type="submit" name="cancelProduct" value="{if $order->hasBeenDelivered()}{l s='Return products'}{elseif $order->hasBeenPaid()}{l s='Refund products'}{else}{l s='Cancel products'}{/if}" class="btn btn-default" />
            </div>
            {/if}
          </div>
          <div style="display:none;" class="partial_refund_fields">
            <p class="checkbox">
              <label for="reinjectQuantitiesRefund">
                <input type="checkbox" id="reinjectQuantitiesRefund" name="reinjectQuantities" />
                {l s='Re-stock products' d='Admin.Orderscustomers.Feature'}
              </label>
            </p>
            <p class="checkbox">
              <label for="generateDiscountRefund">
                <input type="checkbox" id="generateDiscountRefund" name="generateDiscountRefund" onclick="toggleShippingCost()" />
                {l s='Generate a voucher' d='Admin.Orderscustomers.Feature'}
              </label>
            </p>
            {if $order->total_discounts_tax_excl > 0 || $order->total_discounts_tax_incl > 0}
            <p>{l s='This order has been partially paid by voucher. Choose the amount you want to refund: ' d='Admin.Orderscustomers.Feature'}</p>
            <p class="radio">
              <label id="lab_refund_1" for="refund_1">
                <input type="radio" value="0" name="refund_voucher_off" id="refund_1" checked="checked" />
                {l s='Product(s) price: ' d='Admin.Orderscustomers.Feature'}
              </label>
            </p>
            <p class="radio">
              <label id="lab_refund_2" for="refund_2">
                <input type="radio" value="1" name="refund_voucher_off" id="refund_2"/>
                {l s='Product(s) price, excluding amount of initial voucher: ' d='Admin.Orderscustomers.Feature'}
              </label>
            </p>
            <div class="nowrap radio-inline">
                <label id="lab_refund_3" class="pull-left" for="refund_3">
                  {l s='Amount of your choice: ' d='Admin.Orderscustomers.Feature'}
                  <input type="radio" value="2" name="refund_voucher_off" id="refund_3"/>
                </label>
                <div class="input-group col-lg-1 pull-left">
                  <div class="input-group-addon">
                    {$currency->sign}
                  </div>
                  <input type="text" class="input fixed-width-md" name="refund_voucher_choose" value="0"/>
                </div>
              </div>
            {/if}
            <br/>
            <button type="submit" name="partialRefund" class="btn btn-default">
              <i class="icon-check"></i> {l s='Partial refund' d='Admin.Orderscustomers.Feature'}
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-12">
      <!-- Sources block -->
      {if (sizeof($sources))}
      <div class="panel">
        <div class="panel-heading">
          <i class="icon-globe"></i>
          {l s='Sources' d='Admin.Orderscustomers.Feature'} <span class="badge">{$sources|@count}</span>
        </div>
        <ul {if sizeof($sources) > 3}style="height: 200px; overflow-y: scroll;"{/if}>
        {foreach from=$sources item=source}
          <li>
            {dateFormat date=$source['date_add'] full=true}<br />
            <b>{l s='From' d='Admin.Orderscustomers.Feature'}</b>{if $source['http_referer'] != ''}<a href="{$source['http_referer']}">{parse_url($source['http_referer'], $smarty.const.PHP_URL_HOST)|regex_replace:'/^www./':''}</a>{else}-{/if}<br />
            <b>{l s='To' d='Admin.Orderscustomers.Feature'}</b> <a href="http://{$source['request_uri']}">{$source['request_uri']|truncate:100:'...'}</a><br />
            {if $source['keywords']}<b>{l s='Keywords'}</b> {$source['keywords']}<br />{/if}<br />
          </li>
        {/foreach}
        </ul>
      </div>
      {/if}

      <!-- linked orders block -->
      {if count($order->getBrother()) > 0}
      <div class="panel">
        <div class="panel-heading">
          <i class="icon-cart"></i>
          {l s='Linked orders' d='Admin.Orderscustomers.Feature'}
        </div>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>
                  {l s='Order no. ' d='Admin.Orderscustomers.Feature'}
                </th>
                <th>
                  {l s='Status' d='Admin.Global'}
                </th>
                <th>
                  {l s='Amount' d='Admin.Global'}
                </th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              {foreach $order->getBrother() as $brother_order}
              <tr>
                <td>
                  <a href="{$current_index}&amp;vieworder&amp;id_order={$brother_order->id}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}">#{$brother_order->id}</a>
                </td>
                <td>
                  {$brother_order->getCurrentOrderState()->name[$current_id_lang]}
                </td>
                <td>
                  {displayPrice price=$brother_order->total_paid_tax_incl currency=$currency->id}
                </td>
                <td>
                  <a href="{$current_index}&amp;vieworder&amp;id_order={$brother_order->id}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}">
                    <i class="icon-eye-open"></i>
                    {l s='View order' d='Admin.Orderscustomers.Feature'}
                  </a>
                </td>
              </tr>
              {/foreach}
            </tbody>
          </table>
        </div>
      </div>
      {/if}
    </div>
  </div>

  {include file="../../billing.tpl"}
  {include file="../../after_sales.tpl"}

  <script type="text/javascript">
    var geocoder = new google.maps.Geocoder();
    var delivery_map, invoice_map;

    $(document).ready(function()
    {
      $(".textarea-autosize").autosize();

      // Confirmation envoi de la facture
      $('#send_invoice').on('click', function(e) {
        if(!confirm('Etes-vous sûr de vouloir envoyer la facture au client ?'))
          e.preventDefault();
      });

      // Gestion modifications produits
      $('.toggle-products-edit').on('click', function(e) {
        e.preventDefault();

        $('#start_products').toggle();
        $('#start_modifications').toggle();
      });

      // Suppression historique
      $('.remove-history').on('click', function(e) {
        if(!confirm("Etes-vous sûr(e) de vouloir supprimer cet historique ?"))
          e.preventDefault();
      });

      // Suppression des paiements
      $('.remove_payment').on('click', function(e) {
        if(!confirm("Etes-vous sûr(e) de vouloir supprimer ce paiement ?"))
          e.preventDefault();
      });

      geocoder.geocode({
        address: '{$addresses.delivery->address1|@addcslashes:'\''},{$addresses.delivery->postcode|@addcslashes:'\''},{$addresses.delivery->city|@addcslashes:'\''}{if isset($addresses.deliveryState->name) && $addresses.delivery->id_state},{$addresses.deliveryState->name|@addcslashes:'\''}{/if},{$addresses.delivery->country|@addcslashes:'\''}'
        }, function(results, status) {
        if (status === google.maps.GeocoderStatus.OK)
        {
          delivery_map = new google.maps.Map(document.getElementById('map-delivery-canvas'), {
            zoom: 10,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            center: results[0].geometry.location
          });
          var delivery_marker = new google.maps.Marker({
            map: delivery_map,
            position: results[0].geometry.location,
            url: 'http://maps.google.com?q={$addresses.delivery->address1|urlencode},{$addresses.delivery->postcode|urlencode},{$addresses.delivery->city|urlencode}{if isset($addresses.deliveryState->name) && $addresses.delivery->id_state},{$addresses.deliveryState->name|urlencode}{/if},{$addresses.delivery->country|urlencode}'
          });
          google.maps.event.addListener(delivery_marker, 'click', function() {
            window.open(delivery_marker.url);
          });
        }
      });

      geocoder.geocode({
        address: '{$addresses.invoice->address1|@addcslashes:'\''},{$addresses.invoice->postcode|@addcslashes:'\''},{$addresses.invoice->city|@addcslashes:'\''}{if isset($addresses.deliveryState->name) && $addresses.invoice->id_state},{$addresses.deliveryState->name|@addcslashes:'\''}{/if},{$addresses.invoice->country|@addcslashes:'\''}'
        }, function(results, status) {
        if (status === google.maps.GeocoderStatus.OK)
        {
          invoice_map = new google.maps.Map(document.getElementById('map-invoice-canvas'), {
            zoom: 10,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            center: results[0].geometry.location
          });
          invoice_marker = new google.maps.Marker({
            map: invoice_map,
            position: results[0].geometry.location,
            url: 'http://maps.google.com?q={$addresses.invoice->address1|urlencode},{$addresses.invoice->postcode|urlencode},{$addresses.invoice->city|urlencode}{if isset($addresses.deliveryState->name) && $addresses.invoice->id_state},{$addresses.deliveryState->name|urlencode}{/if},{$addresses.invoice->country|urlencode}'
          });
          google.maps.event.addListener(invoice_marker, 'click', function() {
            window.open(invoice_marker.url);
          });
        }
      });

      $('.datepicker').datetimepicker({
        prevText: '',
        nextText: '',
        dateFormat: 'yy-mm-dd',
        // Define a custom regional settings in order to use PrestaShop translation tools
        currentText: '{l s='Now' js=1}',
        closeText: '{l s='Done' js=1}',
        ampm: false,
        amNames: ['AM', 'A'],
        pmNames: ['PM', 'P'],
        timeFormat: 'hh:mm:ss tt',
        timeSuffix: '',
        timeOnlyTitle: '{l s='Choose Time' js=1}',
        timeText: '{l s='Time' js=1}',
        hourText: '{l s='Hour' js=1}',
        minuteText: '{l s='Minute' js=1}'
      });
    });

    // Fix wrong maps center when map is hidden
    $('#tabAddresses').click(function(){
      if (delivery_map) {
        x = delivery_map.getZoom();
        c = delivery_map.getCenter();
        google.maps.event.trigger(delivery_map, 'resize');
        delivery_map.setZoom(x);
        delivery_map.setCenter(c);
      }

      if (invoice_map) {
        x = invoice_map.getZoom();
        c = invoice_map.getCenter();
        google.maps.event.trigger(invoice_map, 'resize');
        invoice_map.setZoom(x);
        invoice_map.setCenter(c);
      }
    });

    function toggleExtra(_this) {
      var parent = $(_this).closest('.product-line-row');
      
      var elements = ['.product-supplier-delivery', '.product-comment'];
      elements.forEach(function(e) {
        parent.find(e).toggle();
        parent.find(e+'-edit').toggle();
      });
    };
  </script>

{/block}
