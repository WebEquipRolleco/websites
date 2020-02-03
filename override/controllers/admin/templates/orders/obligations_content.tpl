{if isset($confirmation)}
  <div class="alert alert-success">
    {$confirmation}
  </div>
{/if}

<div class="well text-right" style="padding:5px">
  <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#send_modal">
    <b><i class="icon-envelope"></i> &nbsp; Envoi des bons de préparation / livraison</b>
  </button>
</div>

<table class="table">
  <thead>
    <tr class="bg-primary">
      <th><b>Fournisseur</b></th>
      <th class="text-center"><b>Code</b></th>
      <th class="text-center"><b>Configuration</b></th>
      <th class="text-center"><b>Date BC</b></th>
      <th class="text-center"><b>Date BL</b></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    {foreach from=OA::findByOrder($order->id) item='oa'}
      <form method="post" action="#panel_OA">
        <tr>
          <td>
            <select id="id_supplier_{$oa->id}" required>
              {foreach from=$suppliers item=supplier}
                <option value="{$supplier.id_supplier}" {if $oa->id_supplier == $supplier.id_supplier}selected{/if}>
                  {$supplier.name}
                </option>
              {/foreach}
            </select>
          </td>
          <td>
            <input type="text" id="code_{$oa->id}" class="text-center" value="{$oa->code}" autocomplete="off">
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
              <button type="submit" id="save_oa" class="btn btn-success" value="{$oa->id}">
                <span class="icon-save"></span>
              </button>
              <button id="delete_oa" class="btn btn-danger" value="{$oa->id}" title="Supprimer">
                <span class="icon-trash"></span>
              </a>
            </div>
          </td>
        </tr>
      </form>
    {foreachelse}
      <tr><td colspan="6"><b class="text-danger">Aucun OA enregistré</b></td></tr>
    {/foreach}
  </tbody>
<tfoot>
  <tr>
    <td>
      <select id="new_oa_id_supplier" required>
        <option value="">{l s="Nouveau fournisseur"}</option>
        {foreach from=$suppliers item=supplier}
          <option value="{$supplier.id_supplier}">{$supplier.name}</option>
        {/foreach}
      </select>
    </td>
    <td>
      <input type="text" id="new_oa_code" autocomplete="off" placeholder="{l s='Code du nouvel OA'}" required>
    </td>
      <td colspan="4">
        <button type="button" id="save_new_oa" class="btn btn-success" >
          <b>{l s="Ajouter"|upper}</b>
        </button>
      </td>
    </tr>
  </tfoot>
</table>

{* Modal envoi mails spécifiques *}
<form method="post">
  <div id="send_modal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <b class="modal-title">{l s="Envoi des bons de préparation / livraison"}</b>
          </div>
        <div class="modal-body">
          <table class="table">
            <thead>
              <tr class="bg-primary">
                <th colspan="2"><b>{l s="Fournisseur"}</b></th>
                <th colspan="2" class="text-center"><b>{l s="Documents à envoyer"}</b></th>
                <th class="text-center"><b>{l s="E-mails"}</b></th>
              </tr>
            </thead>
            <tbody>
              {foreach from=OA::findByOrder($order->id) item='oa'}
                {if $oa->getSupplier()->emails}
                  <tr>
                    <td>
                      <input type="checkbox" class="send_supplier" name="ids_supplier[]" value="{$oa->getSupplier()->id}" checked>
                    </td>
                    <td>
                      <b>{$oa->getSupplier()->name}</b>
                    </td>
                    <td class="text-center" style="padding:5px">
                      <div class="label label-default">
                        <input type="checkbox" id="doc_BC_{$oa->getSupplier()->id}" class="specific_document" name="documents[{$oa->getSupplier()->id}][BC]" value="1" style="vertical-align:middle" {if $oa->getSupplier()->BC}checked{/if}> 
                        <b>{l s="Bon de commande"}</b>
                      </div>
                    </td>
                    <td class="text-center" style="padding:5px">
                      <div class="label label-default">
                        <input type="checkbox" id="doc_BL_{$oa->getSupplier()->id}" class="specific_document" name="documents[{$oa->getSupplier()->id}][BL]" value="1" style="vertical-align:middle" {if $oa->getSupplier()->BL}checked{/if}> 
                        <b>{l s="Bon de livraison"}</b>
                      </div>
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
                <th class="bg-primary">
                  <input type="checkbox" id="custom_send" name="custom_send" value="1" style="vertical-align:middle;"> 
                  <b>{l s="Personnaliser le message envoyé"}</b></th>
              </tr>
            </thead>
            <tbody id="normal_content">
              <tr>
                <td>{l s="Les e-mails classiques seront envoyés en fonction des documents sélectionnés"}</td>
              </tr>
            </tbody>
            <tbody id="custom_centent" style="display:none;">
              <tr>
                <td>
                  <input type="text" id="specific_object" class="form-control" name="object" value="{l s='Nouvelle commande {order_reference} de Web Equip'}" placeholder="{l s='Objet du mail'}">
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
                  <select class="form-control" name="id_change_state">
                    <option value="">{l s="Pas de changement de statut"}</option>
                    {foreach from=OrderState::getOrderStates(1) item=state}
                      <option value="{$state.id_order_state}" {if $state.id_order_state == $BLBC_state_id}selected{/if}>{$state.name}</option>
                    {/foreach}
                  </select>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="submit" id="send_documents" class="btn btn-success" name="send_documents" disabled>
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
    window.checkForDocuments();
  });
</script>