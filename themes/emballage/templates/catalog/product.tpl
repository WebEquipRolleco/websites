{extends file=$layout}

{block name='head_seo' prepend}
  <link rel="canonical" href="{$product.canonical_url}">
{/block}

{block name='head' append}
  <meta property="og:type" content="product">
  <meta property="og:url" content="{$urls.current_url}">
  <meta property="og:title" content="{$page.meta.title}">
  <meta property="og:site_name" content="{$shop.name}">
  <meta property="og:description" content="{$page.meta.description}">
  <meta property="og:image" content="{$product.cover.large.url}">
  <meta property="product:pretax_price:amount" content="{$product.price_tax_exc}">
  <meta property="product:pretax_price:currency" content="{$currency.iso_code}">
  <meta property="product:price:amount" content="{$product.price_amount}">
  <meta property="product:price:currency" content="{$currency.iso_code}">
  {if isset($product.weight) && ($product.weight != 0)}
  <meta property="product:weight:value" content="{$product.weight}">
  <meta property="product:weight:units" content="{$product.weight_unit}">
  {/if}
{/block}

{block name='content'}

  <section id="main" itemscope itemtype="https://schema.org/Product">
    <meta itemprop="url" content="{$product.url}">

    {block name='page_header_container'}
      {block name='page_header'}
        <div class="row">
          <div class="col-lg-12">
            <h1 class="product-title" itemprop="name">
              {block name='page_title'}{$product.name}{/block}
              {if isset($product_manufacturer->id) and isset($manufacturer_image_url)}
                <span class="pull-right" style="margin-top:-25px; margin-bottom:10px">
                    {*<a href="{$product_brand_url}">*}
                    <img src="{$manufacturer_image_url}" class="img img-thumbnail manufacturer-logo" alt="{$product_manufacturer->name}">
                    {*</a>*}
                </span>
              {/if}
            </h1>
          </div>
        </div>
      {/block}     
    {/block}

    <div class="row">
      <div class="col-xs-12 col-md-5">
        {block name='page_content_container'}
          <section class="page-content" id="content">
            {block name='page_content'}
              {block name='product_flags'}
                <ul class="product-flags">
                  {foreach from=$product.flags item=flag}
                    <li class="product-flag {$flag.type}">{$flag.label}</li>
                  {/foreach}
                  {if $product.destocking}
                    <li class="product-flag destocking">{l s="Déstockage"}</li>
                  {/if}
                </ul>
              {/block}

              {block name='product_cover_thumbnails'}
                {include file='catalog/_partials/product-cover-thumbnails.tpl'}
              {/block}
              <div class="scroll-box-arrows">
                <i class="material-icons left">&#xE314;</i>
                <i class="material-icons right">&#xE315;</i>
              </div>

            {/block}
          </section>
        {/block}
        </div>

        <div class="col-xs-12 col-md-1">
          {block name='product_icons'}
            {include file='catalog/_partials/product-icons.tpl'}
          {/block}
        </div>

        <div class="col-xs-12 col-md-6">

          {hook h='displayProductAfterTitle' product=$product}

          {block name='product_description_short'}
            <div id="product-description-short-{$product.id}" class="margin-top-15" itemprop="description">
              {$product.description_short nofilter}
            </div>
            <a href="#full_description" class="description-link">{l s="Voir la description complète"}</a>

            {if $combinations|count > 0}
              <a href="#content-wrapper" class="btn btn-block btn-primary text-center margin-top-10">
                <b>{l s="Sélectionnez le modèle"}</b>
              </a>
            {/if}
            
          {/block}

          {block name='product_prices'}
            {include file='catalog/_partials/product-prices.tpl'}
          {/block}

          <div class="product-information">
            {if $product.is_customizable && count($product.customizations.fields)}
              {block name='product_customization'}
                {include file="catalog/_partials/product-customization.tpl" customizations=$product.customizations}
              {/block}
            {/if}

            <div class="product-actions">
              {block name='product_buy'}
                <form action="{$urls.pages.cart}" method="post" id="add-to-cart-or-refresh">
                  <input type="hidden" name="token" value="{$static_token}">
                  <input type="hidden" name="id_product" value="{$product.id}" id="product_page_product_id">
                  <input type="hidden" name="id_customization" value="{$product.id_customization}" id="product_customization_id">

                  {*block name='product_variants'}
                    {include file='catalog/_partials/product-variants.tpl'}
                  {/block*}

                  {block name='product_pack'}
                    {if $packItems}
                      <section class="product-pack">
                        <p class="h4">{l s='This pack contains' d='Shop.Theme.Catalog'}</p>
                        {foreach from=$packItems item="product_pack"}
                          {block name='product_miniature'}
                            {include file='catalog/_partials/miniatures/pack-product.tpl' product=$product_pack}
                          {/block}
                        {/foreach}
                    </section>
                    {/if}
                  {/block}

                  {*block name='product_discounts'}
                    {include file='catalog/_partials/product-discounts.tpl'}
                  {/block*}

                  {block name='product_add_to_cart'}
                    {include file='catalog/_partials/product-add-to-cart.tpl'}
                  {/block}

                  {block name='product_additional_info'}
                    {include file='catalog/_partials/product-additional-info.tpl'}
                  {/block}

                  {* Input to refresh product HTML removed, block kept for compatibility with themes *}
                  {block name='product_refresh'}{/block}
                </form>
              {/block}

              <div class="text-center">
                <a href="{$link->getProductLink($product.id_product)}?dl_pdf=1" class="btn btn-light bold hidden-lg-down" target="_blank">
                  <i class="fas fa-file-invoice-dollar"></i> &nbsp; {l s="Fiche produit"}
                </a>
                <a href="{$link->getProductLink($product.id_product)}?dl_demo=1" class="btn btn-light bold hidden-lg-down" title="{l s='PDF sans prix'}" target="_blank">
                  <i class="fa fa-file-pdf"></i> &nbsp; {l s="Fiche produit sans prix"}
                </a>
              </div>

              {*block name='product_details'}
                {include file='catalog/_partials/product-details.tpl'}
              {/block*}

            </div>

        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12">
        {block name='product_combinations_table'}
          {include file='catalog/_partials/product-combinations-table.tpl'}
        {/block}
      </div>
    </div>

    <div class="row">

      <div class="col-lg-8">

        {if $product.description}
          <div id="full_description">
            <h3 class="section-title top-space">
              {l s='Description' d='Shop.Theme.Catalog'}
            </h3>
            {$product.description nofilter}
          </div>
        {/if}

        {*block name='product_attachments'}
          {if $product.attachments}
            <div class="tab-pane fade in" id="attachments" role="tabpanel">
              <section class="product-attachments">
                <p class="h5 text-uppercase">{l s='Download' d='Shop.Theme.Actions'}</p>
                {foreach from=$product.attachments item=attachment}
                  <div class="attachment">
                    <h4><a href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}">{$attachment.name}</a></h4>
                    <p>{$attachment.description}</p>
                    <a href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}">
                      {l s='Download' d='Shop.Theme.Actions'} ({$attachment.file_size_formatted})
                    </a>
                  </div>
                {/foreach}
              </section>
            </div>
          {/if}
        {/block*}

        {*foreach from=$product.extraContent item=extra key=extraKey}
          <div class="tab-pane fade in {$extra.attr.class}" id="extra-{$extraKey}" role="tabpanel" {foreach $extra.attr as $key => $val} {$key}="{$val}"{/foreach}>
            {$extra.content nofilter}
          </div>
        {/foreach*}

      </div>

      <div class="col-lg-4">
        {block name='hook_display_reassurance'}
          {hook h='displayReassurance'}
        {/block}
      </div>

    </div>

    {block name='product_footer'}
      {if isset($category)}
        {hook h='displayFooterProduct' product=$product category=$category}
      {/if}
    {/block}

    {block name='product_images_modal'}
      {include file='catalog/_partials/product-images-modal.tpl'}
    {/block}

    {block name='page_footer_container'}
      <footer class="page-footer">
        {block name='page_footer'}
          <!-- Footer content -->
        {/block}
      </footer>
    {/block}
  </section>

{/block}
