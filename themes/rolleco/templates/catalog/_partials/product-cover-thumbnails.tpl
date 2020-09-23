{assign var=images value=Product::getAllPictures($product.id_product)}

<div class="images-container">
  {block name='product_cover'}
    <div class="product-cover">
      {if $product.cover} 
        <a data-toggle="modal" data-target="#product-modal" style="cursor:pointer">
          <img class="js-qv-product-cover col-lg-12" src="{$product.cover.bySize.large_default.url}" alt="{$product.cover.legend}" title="{$product.cover.legend}" itemprop="image">
        </a>
      {else}
        <img src="{$urls.no_picture_image.bySize.large_default.url}">
      {/if}
    </div>
  {/block}

  {block name='product_images'}
    <div class="js-qv-mask">
      <ul class="product-images js-qv-product-images margin-top-10">
        {foreach from=$images item=image}
          <li class="thumb-container">
            <img
              id="product_image_{$image->id}"
              class="thumb js-thumb {if $image->cover} selected {/if}"
              data-image-medium-src="{$image->getFileUrl('medium')|replace:'http:':'https:'}"
              data-image-large-src="{$image->getFileUrl('large')|replace:'http:':'https:'}"
              src="{$image->getFileUrl('home')|replace:'http:':'https:'}"
              alt="{$image->legend}"
              title="{$image->legend}"
              width="100"
              itemprop="image"
            >
          </li>
        {/foreach}
      </ul>
    </div>
  {/block}
</div>
{hook h='displayAfterProductThumbs'}
