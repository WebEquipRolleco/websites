<div id="invoice_customization" class="panel">
  <div class="panel-heading">
    <i class="icon-file-text"></i>
    {l s="Facturation" d='Admin.Global'}
  </div>
  <form method="post" action="#invoice_customization" class="form-horizontal">
    <div class="row">
      <div class="col-lg-5">
        <div class="form-group">
          <label class="control-label col-lg-3">{l s="Date de facturation"}</label>
          <div class="col-lg-9">
            <input type="date" class="form-control" name="invoice_date" value="{$order->invoice_date|date_format:'Y-m-d'}">
            {if $order->getPaymentDeadline()}
              <b>{l s='Date limite de paiment :'} <span class="text-danger">{$order->getPaymentDeadline()->format('d/m/Y')}</span></b>
            {/if}
          </div>
        </div>
        <div class="form-group">
          <label class="control-label col-lg-3">{l s="Numéro de facturation"}</label>
          <div class="col-lg-9">
            <input type="text" class="form-control" name="invoice_number" {if $order->invoice_number}value="{$order->invoice_number}"{/if}>
          </div>
        </div>
        <div class="form-group">
          <label class="control-label col-lg-3">{l s="Exclure des rappels"}</label>
          <div class="col-lg-9">
            <span class="switch prestashop-switch fixed-width-lg">
              <input type="radio" name="no_recall" id="no_recall_on" value="1" {if $order->no_recall}checked{/if}>
              <label for="no_recall_on">Oui</label>
              <input type="radio" name="no_recall" id="no_recall_off" value="0" {if !$order->no_recall}checked{/if}>
              <label for="no_recall_off">Non</label>
              <a class="slide-button btn"></a>
            </span>
          </div>
        </div>
        <style>
          #price_switch input:last-of-type:checked~a {
            border: 1px solid #279cbb !important;
            background-color: #2eacce !important;
          }
        </style>
        <div class="form-group">
          <label class="control-label col-lg-3">{l s="Afficher les prix"}</label>
          <div class="col-lg-9">
            <span id="price_switch" class="switch prestashop-switch fixed-width-lg">
              <input type="radio" name="display_with_taxes" id="display_with_taxes_on" value="1" {if $order->display_with_taxes}checked{/if}>
              <label for="display_with_taxes_on">TTC</label>
              <input type="radio" name="display_with_taxes" id="display_with_taxes_off" value="0" {if !$order->display_with_taxes}checked{/if}>
              <label for="display_with_taxes_off">HT</label>
              <a class="slide-button btn"></a>
            </span>
          </div>
        </div>
      </div>
      <div class="col-lg-7">
        <textarea rows="8" class="form-control" name="invoice_comment" style="resize:vertical" placeholder="Commentaire">{$order->invoice_comment}</textarea>
      </div>
    </div>
    <div class="panel-footer">
      <div class="form-group text-right">
        <button type="submit" class="btn btn-success" name="save_invoice">
          <i class="process-icon-save"></i>
          {l s='Save' d='Admin.Actions'}
        </button> 
      </div>
    </div>
  </form>
</div>