<div id="start_modifications" class="row" style="display:none">
  <div class="col-lg-12">
    <form method="post" action="#start_products">
      <div class="panel">
        <div class="panel-heading">
          <i class="icon-shopping-cart"></i>
          {l s='Products' d='Admin.Global'} <span class="badge">{$products|@count}</span>
          <span class="panel-heading-action">
            <a href="#modifications" class="list-toolbar-btn toggle-products-edit" title="{l s='Produits'}">
              <i class="process-icon-edit"></i>
            </a>
          </span>
        </div>
        <table class="table">
          <thead>
            <tr>
              <th><b>{l s="Produit"}</b></th>
              <th class="text-center"><b>{l s="Fournisseur"}</b></th>
              <th class="text-center"><b>{l s="Références"}</b></th>
              <th class="text-center"><b>{l s="Quantités"}</b></th>
              <th class="text-center"><b>{l s="Prix"}</b></th>
            </tr>
          </thead>
          <tbody>
            {foreach from=$order->getDetails() item=product}
              <tr>
                <td>
                  {$product->product_name}
                </td>
                <td>
                  <select class="form-control" name="update[{$product->id}][id_supplier]">
                    <option {if !$product->id_product_supplier}selected{/if}></option>
                    {foreach from=$suppliers item=supplier}
                      <option value="{$supplier.id_supplier}" {if $product->id_product_supplier == $supplier.id_supplier}selected{/if}>{$supplier.name}</option>
                    {/foreach}
                  </select>
                </td>
                <td>
                  <em class="text-muted">{l s="Produit"}</em>
                  <input type="text" class="form-control" name="update[{$product->id}][product_reference]" value="{$product->product_reference}">
                  <em class="text-muted">{l s="Fournisseur"}</em>
                  <input type="text" class="form-control" name="update[{$product->id}][product_supplier_reference]" value="{$product->product_supplier_reference}">
                </td>
                <td>
                  <input type="number" min="1" step="1" id="quantity_{$product->id}" class="form-control update-shipping-cost" name="update[{$product->id}][product_quantity]" value="{$product->product_quantity}" data-target="{$product->id}">
                </td>
                <td>
                  <em class="text-muted">{l s='PA Unitaire'}</em>
                  <input type="number" min="0" step="0.01" class="form-control" name="update[{$product->id}][purchase_supplier_price]" value="{$product->purchase_supplier_price}">
                  <em class="text-muted">{l s='Ports Unitaire'}</em>
                  <input type="number" min="0" step="0.01" class="form-control" name="update[{$product->id}][delivery_fees]" value="{$product->delivery_fees|string_format:"%.2f"}">
                </td>
              </tr>
            {/foreach}
          </tbody>
        </table>
        <div class="panel-footer text-right">
          <button type="button" class="btn btn-danger toggle-products-edit" style="width:80px">
              <i class="process-icon-cancel"></i>
              {l s='Cancel' d='Admin.Actions'}
          </button>
          <button type="submit" class="btn btn-success">
            <i class="process-icon-save"></i>
            {l s='Save' d='Admin.Actions'}
          </button>
        </div>
      </div>
    </form>
  </div>
</div>