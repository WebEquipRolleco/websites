<div class="modal fade js-product-images-modal" id="product-modal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <div class="row" style="background-color:white;">
          {assign var=imagesCount value=$images|count}
          <div class="col-xs-12 col-lg-8">
            <figure>
              <img class="js-modal-product-cover product-cover-modal" width="{$product.cover.large.width}" src="{$product.cover.large.url}" alt="{$product.cover.legend}" title="{$product.cover.legend}" itemprop="image">
              <figcaption class="text-center">
              {block name='product_description_short'}
                <div id="product-description-short" itemprop="description">{$product.description_short nofilter}</div>
              {/block}
            </figcaption>
            </figure>
          </div>
          <div class="col-xs-12 col-lg-4">
            <aside id="thumbnails" class="thumbnails js-thumbnails text-sm-center">
              {block name='product_images'}
                <div class="js-modal-mask mask">
                  <ul class="product-images js-modal-product-images">
                    {foreach from=$images item=image}
                      <li class="thumb-container">
                        <img data-image-large-src="{$image->getFileUrl('large')|replace:'http:':'https:'}" class="thumb js-modal-thumb" src="{$image->getFileUrl('medium')|replace:'http:':'https:'}" alt="{$image->legend}" title="{$image-$
                      </li>
                    {/foreach}
                  </ul>
                </div>
              {/block}
              {if $imagesCount > 5}
                <div class="arrows js-modal-arrows">
                  <i class="material-icons arrow-up js-modal-arrow-up">&#xE5C7;</i>
                  <i class="material-icons arrow-down js-modal-arrow-down">&#xE5C5;</i>
                </div>
              {/if}
            </aside>
          </div>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->