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
                  <option value="{$supplier.id_supplier}" {if $oa->id_supplier == $supplier.id_supplier}selected{/if}>
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
                <option value="{$supplier.id_supplier}">{$supplier.name}</option>
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

{* Modal envoi mails classiques *}
<form method="post">
  <div id="normal_send_modal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <b class="modal-title">{l s="Envoi des bons de préparation / livraison"}</b>
        </div>
        <div class="modal-body">
          <table class="table">
            <thead>
              <tr class="bg-primary">
                <th><b>{l s="Fournisseur"}</b></th>
                <th class="text-center"><b>{l s="BC"}</b></th>
                <th class="text-center"><b>{l s="BL"}</b></th>
                <th class="text-center"><b>{l s="E-mails"}</b></th>
              </tr>
            </thead>
            <tbody>
              {foreach from=OA::findByOrder($order->id) item='oa'}
                {if $oa->getSupplier()->emails}
                  <tr>
                    <td><b>{$oa->getSupplier()->name}</b></td>
                    <td class="text-center">
                      {if $oa->getSupplier()->BC}
                        <span class="icon-check text-success"></span>
                      {else}
                        <span class="icon-times text-danger"></span>
                      {/if}
                    </td>
                    <td class="text-center">
                      {if $oa->getSupplier()->BL}
                        <span class="icon-check text-success"></span>
                      {else}
                        <span class="icon-times text-danger"></span>
                      {/if}
                    </td>
                    <td class="text-center">
                      {$oa->getSupplier()->emails}
                    </td>
                  </tr>
                {/if}
              {/foreach}
            </tbody>
          </table>
          <table class="table" style="margin-top:20px">
            <thead>
              <tr>
                <th class="bg-primary"><b>{l s="Message envoyé"}</b></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  <input type="text" class="form-control" name="object" value="{l s='Admin.Object.Fournisseur.envoi-normal' d='Admin.Orderscustomers.Object'}" placeholder="{l s='Objet du mail'}">
                </td>
              </tr>
              <tr>
                <td>
                  <textarea class="form-control" rows="5" name="message" placeholder="{l s='Message personnalisé'}">{l s='Message.Fournisseur.envoi-normal' d='Admin.Orderscustomers.Message'}</textarea>
                </td>
              </tr>
            </tbody>
          </table>
          <table class="table" style="margin-top:20px">
            <thead>
              <tr>
                <th class="bg-primary"><b>{l s="Changement de statut"}</b></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  {if $BLBC_state->id}
                    <span class="label" style="background-color:{$BLBC_state->color}">{$BLBC_state->name}</span>
                  {else}
                    <span class="label label-danger"><i class="icon-exclamation-triangle"></i> <b>{l s="Le changement d'état n'est pas configuré"}</b></span>
                  {/if}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success" name="send_documents">
            <b>{l s="Envoyer"|upper}</b>
          </button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">
            <b>{l s="Annuler"|upper}</b>
          </button>
        </div>
      </div>
    </div>
  </div>
</form>

{* Modal envoi mails spécifiques *}
<form method="post">
  <div id="special_send_modal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <b class="modal-title">{l s="Envoi des bons de préparation / livraison"}</b>
          </div>
        <div class="modal-body">
          <table class="table">
            <thead>
              <tr class="bg-primary">
                <th colspan="2" class="text-center">
                  <b>{l s="Documents à envoyer"}</b>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="text-center" style="padding:5px">
                  <div class="label label-default">
                    <input type="checkbox" id="doc_BC" class="specific_document" name="documents[]" value="BC" style="vertical-align:middle"> 
                    <b>{l s="Bon de commande"}</b>
                  </div>
                </td>
                <td class="text-center" style="padding:5px">
                  <div class="label label-default">
                    <input type="checkbox" id="doc_BL" class="specific_document" name="documents[]" value="BL" style="vertical-align:middle"> 
                    <b>{l s="Bon de livraison"}</b>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
          <table class="table" style="margin-top:20px">
            <thead>
              <tr class="bg-primary">
                <th></th>
                <th><b>{l s="Fournisseur"}</b></th>
                <th class="text-center"><b>{l s="E-mails"}</b></th>
              </tr>
            </thead>
            <tbody>
              {foreach from=OA::findByOrder($order->id) item='oa'}
                {if $oa->getSupplier()->emails}
                  <tr>
                    <td>
                      <input type="checkbox" name="ids_supplier[]" value="{$oa->getSupplier()->id}">
                    </td>
                    <td>
                      <b>{$oa->getSupplier()->name}</b>
                    </td>
                    <td class="text-center">
                      {$oa->getSupplier()->emails}
                    </td>
                  </tr>
                {/if}
              {/foreach}
            </tbody>
          </table>
          <input type="hidden" id="object_BL" value="{l s='Object.Fournisseur.envoi-BL'}" d='Admin.Orderscustomers.Object'>
          <input type="hidden" id="object_BC" value="{l s='Object.Fournisseur.envoi-BC'}" d='Admin.Orderscustomers.Object'>
          <input type="hidden" id="object_BLBC" value="{l s='Object.Fournisseur.envoi-BLBC'}" d='Admin.Orderscustomers.Object'>
          <input type="hidden" id="message_BL" value="{l s='Message.Fournisseur.envoi-BL'}" d='Admin.Orderscustomers.Message'>
          <input type="hidden" id="message_BC" value="{l s='Message.Fournisseur.envoi-BC'}" d='Admin.Orderscustomers.Message'>
          <input type="hidden" id="message_BLBC" value="{l s='Message.Fournisseur.envoi-BLBC'}" d='Admin.Orderscustomers.Message'>
          <table class="table" style="margin-top:20px">
            <thead>
              <tr>
                <th class="bg-primary"><b>{l s="Message envoyé"}</b></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  <input type="text" id="specific_object" class="form-control" name="object" placeholder="{l s='Objet du mail'}">
                </td>
              </tr>
              <tr>
                <td><textarea id="specific_message" class="form-control" rows="5" name="message" placeholder="{l s='Message personnalisé'}"></textarea></td>
              </tr>
            </tbody>
          </table>
          <table class="table" style="margin-top:20px">
            <thead>
              <tr>
                <th class="bg-primary"><b>{l s="Changement de statut"}</b></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  {if $BLBC_state->id}
                    <span class="label" style="background-color:{$BLBC_state->color}">{$BLBC_state->name}</span>
                  {else}
                    <span class="label label-danger"><i class="icon-exclamation-triangle"></i> <b>{l s="Le changement d'état n'est pas configuré"}</b></span>
                  {/if}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success" name="send_documents">
            <b>{l s="Envoyer"|upper}</b>
          </button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">
            <b>{l s="Annuler"|upper}</b>
          </button>
        </div>
      </div>
    </div>
  </div>
</form>

<script>
  $(document).ready(function() {

    $('.specific_document').on('change', function() {

        var doc = "";
        if($('#doc_BL').is(':checked')) doc = doc + "BL";
        if($('#doc_BC').is(':checked')) doc = doc + "BC";

        if(doc) {
          $('#specific_object').val($('#object_'+doc).val());
          $('#specific_message').html($('#message_'+doc).val());
        }
    });

  });
</script>