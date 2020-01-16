{if $product.show_price}
  <div class="product-prices text-center">
    {block name='product_discount'}
      {if $product.has_discount}
        <div class="product-discount">
          {hook h='displayProductPriceBlock' product=$product type="old_price"}
          <span class="regular-price">{$product.regular_price}</span>
        </div>
      {/if}
    {/block}

    {block name='product_price'}
      <div class="product-price h5 {if $product.has_discount}has-discount{/if}" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
        <link itemprop="availability" href="{$product.seo_availability}"/>
        <meta itemprop="priceCurrency" content="{$currency.iso_code}">

        <div class="current-price">
          <span class="pre-price">{l s="A partir de"}</span>
          <span class="main-price">
            {Tools::displayPrice(Product::getPriceStatic($product.id_product, false, null, 2, null, false, true, Tools::getValue('quantity_wanted', 1)))}
            <small>HT</small>
          </span>
          <span class="full-price" itemprop="price" content="{$product.price_amount}">{Tools::displayPrice(Product::getPriceStatic($product.id_product, true, null, 2, null, false, true, Tools::getValue('quantity_wanted', 1)))} {$product.labels.tax_long}</span>

          {if $product.has_discount}
            {if $product.discount_type === 'percentage'}
              <span class="discount discount-percentage">{l s='Save %percentage%' d='Shop.Theme.Catalog' sprintf=['%percentage%' => $product.discount_percentage_absolute]}</span>
            {else}
              <span class="discount discount-amount">
                  {l s='Save %amount%' d='Shop.Theme.Catalog' sprintf=['%amount%' => $product.discount_to_display]}
              </span>
            {/if}
          {/if}
        </div>

        

        {block name='product_unit_price'}
          {if $displayUnitPrice}
            <p class="product-unit-price sub">{l s='(%unit_price%)' d='Shop.Theme.Catalog' sprintf=['%unit_price%' => $product.unit_price_full]}</p>
          {/if}
        {/block}
      </div>
    {/block}

    {foreach from=ProductIcon::getList(2) item=icon}
          {if $icon->display($product.id_product, Context::getContext()->shop->id)}
            <div style="display:inline-block; vertical-align:text-bottom; margin-left:20px;">
              <a href="{$icon->url}" title="{$icon->title}">
                <img class="img-thumbnail" src="{$icon->getImgPath()}" {if $icon->height}height="{$icon->height}px"{/if} {if $icon->width}width="{$icon->width}px"{/if}>
                {$icon->title}
              </a>
            </div>
          {/if}
        {/foreach}

    {block name='product_without_taxes'}
      {if $priceDisplay == 2}
        <p class="product-without-taxes">{l s='%price% tax excl.' d='Shop.Theme.Catalog' sprintf=['%price%' => $product.price_tax_exc]}</p>
      {/if}
    {/block}

    {block name='product_pack_price'}
      {if $displayPackPrice}
        <p class="product-pack-price"><span>{l s='Instead of %price%' d='Shop.Theme.Catalog' sprintf=['%price%' => $noPackPrice]}</span></p>
      {/if}
    {/block}

    {block name='product_ecotax'}
      {if $product.ecotax.amount > 0}
        <p class="price-ecotax">{l s='Including %amount% for ecotax' d='Shop.Theme.Catalog' sprintf=['%amount%' => $product.ecotax.value]}
          {if $product.has_discount}
            {l s='(not impacted by the discount)' d='Shop.Theme.Catalog'}
          {/if}
        </p>
      {/if}
    {/block}

    {hook h='displayProductPriceBlock' product=$product type="weight" hook_origin='product_sheet'}

    {*<div class="tax-shipping-delivery-label">
      {if $configuration.display_taxes_label}
        {$product.labels.tax_long}
      {/if}
      {hook h='displayProductPriceBlock' product=$product type="price"}
      {hook h='displayProductPriceBlock' product=$product type="after_price"}
      {if $product.additional_delivery_times == 1}
        {if $product.delivery_information}
          <span class="delivery-information">{$product.delivery_information}</span>
        {/if}
      {elseif $product.additional_delivery_times == 2}
        {if $product.quantity > 0}
          <span class="delivery-information">{$product.delivery_in_stock}</span>
        {elseif $product.quantity == 0 && $product.add_to_cart_url}
          <span class="delivery-information">{$product.delivery_out_stock}</span>
        {/if}
      {/if}
    </div>*}

  </div>
{/if}
