{**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{block name='product_miniature_item'}
  <div class="col-lg-3 col-slim margin-top-15">

    <a href="{$product.url}">
      <article class="miniature text-center">
      
        {block name='product_thumbnail'}
          <div class="text-center margin-bottom-15">
          {if $product.cover}
            <img src="{$product.cover.bySize.home_default.url}" alt="{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}" data-full-size-image-url="{$product.cover.large.url}">
          {else}
            <img src="{$urls.no_picture_image.bySize.home_default.url}">
          {/if}
        </div>
        {/block}

        {block name='product_name'}
          <div class="description text-primary" itemprop="name">{$product.name}</div>
        {/block}

        {block name='product_price_and_shipping'}
          {if $product.show_price}
            <div>
              {if $product.has_discount}
                {hook h='displayProductPriceBlock' product=$product type="old_price"}

                <span class="sr-only">{l s='Regular price' d='Shop.Theme.Catalog'}</span>
                <span class="regular-price">{$product.regular_price}</span>
                {if $product.discount_type === 'percentage'}
                  <span class="discount-percentage discount-product">{$product.discount_percentage}</span>
                {elseif $product.discount_type === 'amount'}
                  <span class="discount-amount discount-product">{$product.discount_amount_to_display}</span>
                {/if}
              {/if}

              {hook h='displayProductPriceBlock' product=$product type="before_price"}

              <span class="sr-only">{l s='Price' d='Shop.Theme.Catalog'}</span>
              <span itemprop="price" class="price bold">{$product.price}</span>

              {hook h='displayProductPriceBlock' product=$product type='unit_price'}

              {hook h='displayProductPriceBlock' product=$product type='weight'}
            </div>
          {/if}
        {/block}

        {block name='product_reviews'}
          {hook h='displayProductListReviews' product=$product}
        {/block}

        <span class="btn btn-primary margin-top-15 margin-bottom-15 bold">
          {l s="Voir le produit"}
        </span>

      </article>
    </a>

    {block name='product_flags'}
      <ul class="product-flags">
        {foreach from=$product.flags item=flag}
          <li class="product-flag {$flag.type}">{$flag.label}</li>
        {/foreach}
        {if $product.rollcash}
          <li class="product-flag bg-red">{l s="SignalCash"} {$product.rollcash}%</li>
        {/if}
      </ul>
    {/block}

  </div>
{/block}