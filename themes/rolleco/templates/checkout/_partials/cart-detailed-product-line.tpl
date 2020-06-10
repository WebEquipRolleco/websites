<tr>

  {* IMAGE *}
  <td class="text-center">
    <img src="{$product.cover.bySize.cart_default.url}" class="cart-image">
  </td>

 {* PRODUIT *}
  <td>
    <a class="label" href="{$product.url}" data-id_customization="{$product.id_customization|intval}">
      {$product.name}
    </a>
    {if $product.id_product_attribute}
      <div class="product-line-info">
        {foreach from=Combination::loadColumn($product.id_product_attribute, 1) item=row name=column_1}
          <b>{$row.name}.</b> {$row.value} {if !$smarty.foreach.column_1.last} x {/if}
        {/foreach}
      </div>
      {assign var=data value=Combination::loadComments($product.id_product_attribute)}
      {if $data.comment_1}<div class="product-line-info">{$data.comment_1}</div>{/if}
      {if $data.comment_2}<div class="product-line-info">{$data.comment_2}</div>{/if}
    {else}
      {if $product.comment_1}<div>{$product.comment_1|replace:'|':"<br />" nofilter}{/if}
      {if $product.comment_2}<div>{$product.comment_2|replace:'|':"<br />" nofilter}{/if}
    {/if}
    {*foreach from=$product.attributes key="attribute" item="value"}
      <div class="product-line-info">
        <span class="label">{$attribute}:</span>
        <span class="value">{$value}</span>
      </div>
    {/foreach*}
    {foreach from=OrderOptionCart::findByCart() item=option}
      {if !$option->isValid($product.id_product)}
        <div class="text-danger"><i class="fa fa-exclamation-triangle"></i> {$option->warning}</div>
      {/if}
    {/foreach}
  </td>

  {* REFERENCE *}
  <td class="cart_reference text-center">
    {$product.reference}
  </td>

  {* PRIX UNITAIRE *}
  <td class="price text-center">
    <div class="product-line-info product-price h5 {if $product.has_discount}has-discount{/if}">
      {if $product.has_discount}
        <div class="product-discount">
          <span class="regular-price">{$product.regular_price}</span>
          {*if $product.discount_type === 'percentage'}
            <span class="discount discount-percentage">
                -{$product.discount_percentage_absolute}
              </span>
          {else}
            <span class="discount discount-amount">
                -{$product.discount_to_display}
              </span>
          {/if*}
        </div>
      {/if}
      <div class="current-price">
        <span class="price">
          {Tools::displayPrice($product.price_with_reduction_without_tax)}
        </span>
        {*if $product.unit_price_full}
          <div class="unit-price-cart">{$product.unit_price_full}</div>
        {/if*}
      </div>
    </div>
    {if $product.ecotax.amount > 0}
      <div class="text-muted">
        <small>{l s="Dont %s d'écotaxe" sprintf=[$product.ecotax.value] d='Shop.Theme.Checkout'}</small>
      </div>
    {/if}
  </td>

  {* QUANTITE *}
  <td class="qty text-center" style="min-width:95px">
    {if isset($product.is_gift) && $product.is_gift}
      <span class="gift-quantity">{$product.quantity}</span>
    {else}
      <input class="js-cart-line-product-quantity" data-down-url="{$product.down_quantity_url}" data-up-url="{$product.up_quantity_url}" data-update-url="{$product.update_quantity_url}" data-product-id="{$product.id_product}" type="text" value="{$product.quantity}" name="product-quantity-spin" min="{$product.minimal_quantity}" />
    {/if}
  </td>

  {* TOTAL *}
  <td class="price text-center">
    <span class="product-price">
      <strong>
        {if isset($product.is_gift) && $product.is_gift}
          <span class="gift">{l s='Gift' d='Shop.Theme.Checkout'}</span>
        {else}
          {Tools::displayPrice($product.price_with_reduction_without_tax * $product.quantity)}
        {/if}
      </strong>
    </span>
    {if $product.ecotax.amount > 0}
      <div class="text-muted">
        <small>{l s="Dont %s d'écotaxe" sprintf=[Tools::displayPrice($product.ecotax.amount * $product.quantity)] d='Shop.Theme.Checkout'}</small>
      </div>
    {/if}
  </td>

  {* ACTIONS *}
  <td class="text-center">
    <div class="cart-line-product-actions">
      <a class = "remove-from-cart hvr-icon-buzz-out" rel = "nofollow" href = "{$product.remove_from_cart_url}" data-link-action = "delete-from-cart" data-id-product = "{$product.id_product|escape:'javascript'}" data-id-product-attribute = "{$product.id_product_attribute|escape:'javascript'}" data-id-customization = "{$product.id_customization|escape:'javascript'}">
        {if !isset($product.is_gift) || !$product.is_gift}
          <i class="material-icons fa fa-trash-alt hvr-icon"></i>
        {/if}
      </a>
      {block name='hook_cart_extra_product_actions'}
        {hook h='displayCartExtraProductActions' product=$product}
      {/block}
    </div>
  </td>

</tr>

{* PERSONNALISATIONS *}
{if $product.customizations|count}
  {block name='cart_detailed_product_line_customization'}
    {foreach from=$product.customizations item="customization"}
      <tr>
        <td colspan="7">
          <a href="#" data-toggle="modal" data-target="#product-customizations-modal-{$customization.id_customization}">
            {l s='Product customization' d='Shop.Theme.Catalog'}
          </a>
          <div class="modal fade customization-modal" id="product-customizations-modal-{$customization.id_customization}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <h4 class="modal-title">{l s='Product customization' d='Shop.Theme.Catalog'}</h4>
                </div>
                <div class="modal-body">
                  {foreach from=$customization.fields item="field"}
                    <div class="product-customization-line row">
                      <div class="col-sm-3 col-xs-4 label">
                        {$field.label}
                      </div>
                      <div class="col-sm-9 col-xs-8 value">
                        {if $field.type == 'text'}
                          {if (int)$field.id_module}
                            {$field.text nofilter}
                          {else}
                            {$field.text}
                          {/if}
                        {elseif $field.type == 'image'}
                          <img src="{$field.image.small.url}">
                        {/if}
                      </div>
                    </div>
                  {/foreach}
                </div>
              </div>
            </div>
          </div>
        </td>
      </tr>
    {/foreach}
  {/block}
{/if}