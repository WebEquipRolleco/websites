<tr>
    <td class="bg-darkgrey" colspan="6">
        <b>{l s="Devis %s" sprintf=[$quotation->reference] d="Shop.Theme.Checkout"}</b>
        <em class="text-muted">- {l s="Valide jusqu'au %s" sprintf=[$quotation->date_end|date_format:'d/m/Y'] d="Shop.Theme.Checkout"}
    </td>
    <td class="bg-darkgrey text-center">
        <div class="cart-line-product-actions">
            <form method="post">
                <button type="submit" class="btn btn-danger hvr-icon-buzz-out" name="remove_quotation" value="{$quotation->id}" title="{l s='Retirer du panier' d='Shop.Theme.Checkout'}">
                    <i class="fa fa-trash hvr-icon"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
{foreach from=$quotation->getProducts() item=line}
    {include file='checkout/_partials/cart-detailed-quotation-product-line.tpl'}
{/foreach}