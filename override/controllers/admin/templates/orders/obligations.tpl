<div id="panel_OA" class="panel">
  <div class="panel-heading">
    <i class="icon-star"></i>
    {l s="Gestion des OA" d='Admin.Global'}
    <div class="panel-heading-action">
      <button type="button" class="btn btn-default" data-toggle="modal" data-target="#normal_send_modal">
        {l s="Envoi BC/BL"}
      </button>
      <button type="button" class="btn btn-default" data-toggle="modal" data-target="#special_send_modal">
        {l s="Envoi spécifique"}
      </button>
      &nbsp;
    </div>
  </div>
  <table class="table">
    <thead>
      <tr>
        <th>{l s="Fournisseur"}</th>
        <th class="text-center">{l s="Code"}</th>
        <th class="text-center">{l s="Configuration"}</th>
        <th class="text-center">{l s="Date BC"}</th>
        <th class="text-center">{l s="Date BL"}</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      {foreach from=OA::findByOrder($order->id) item='oa'}
        <form method="post" action="#panel_OA">
          <tr>
            <td>
              <select name="id_supplier" required>
                {foreach from=$suppliers item=supplier}
                  <option value="{$supplier.id}" {if $oa->id_supplier == $supplier.id}selected{/if}>
                    {$supplier.name}
                  </option>
                {/foreach}
              </select>
            </td>
            <td>
              <input type="text" class="text-center" name="code" value="{$oa->code}" autocomplete="off">
            </td>
            <td class="text-center">
              {if $oa->getSupplier()->emails}
                <span class="icon-check text-success" title="{l s='Mails envoyés vers :'} {$oa->getSupplier()->emails}"></span>
              {else}
                <span class="icon-times text-danger" title="{l s='E-mail(s) non configuré(s)'}"></span>
              {/if}
            </td>
            <td class="text-center">
              {$oa->date_BC|date_format:'d/m/Y H:i'|default:'-'}
            </td>
            <td class="text-center">
              {$oa->date_BL|date_format:'d/m/Y H:i'|default:'-'}
            </td>
            <td class="text-right">
              <div class="btn-group">
                <a href="{$link->getAdminLink('AdminPdf')|escape:'html':'UTF-8'}&submitAction=generatePurchaseOrderPDF&id_oa={$oa->id|intval}" class="btn btn-default _blank" title="{l s='Bon de commande'}">
                  <span class="icon-file"></span>
                </a>
                <a href="{$link->getAdminLink('AdminPdf')|escape:'html':'UTF-8'}&submitAction=generateDeliverySlipPDF&id_oa={$oa->id|intval}" class="btn btn-default _blank" title="{l s='Bon de livraison'}">
                  <span class="icon-truck"></span>
                </a>
              </div>
              &nbsp;
              <div class="btn-group">
                <button type="submit" class="btn btn-success" name="save_oa" value="{$oa->id}">
                  <span class="icon-save"></span>
                </button>
                <a href="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}&remove_oa={$oa->id}" class="btn btn-danger" title="{l s='Supprimer'}">
                  <span class="icon-trash"></span>
                </a>
              </div>
            </td>
          </tr>
        </form>
      {foreachelse}
        <tr>
          <td colspan="6">
            <b class="text-danger">{l s="Aucun OA enregistré"}</b>
          </td>
        </tr>
      {/foreach}
    </tbody>
    <tfoot>
      <form method="post" action="#panel_OA">
        <tr>
          <td>
            <select name="new_oa[id_supplier]" required>
              <option value="">{l s="Nouveau fournisseur"}</option>
              {foreach from=$suppliers item=supplier}
                <option value="{$supplier.id}">{$supplier.name}</option>
              {/foreach}
            </select>
          </td>
          <td>
            <input type="text" name="new_oa[code]" autocomplete="off" placeholder="{l s='Code du nouvel OA'}" required>
          </td>
          <td colspan="4">
            <button type="submit" class="btn btn-primary" name="save_new_oa">
              {l s="Ajouter"|upper}
            </button>
          </td>
        </tr>
      </form>
    </tfoot>
  </table>
</div>