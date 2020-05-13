{block name='customer_form'}
  {block name='customer_form_errors'}
    {include file='_partials/form-errors.tpl' errors=$errors['']}
  {/block}

<form action="{block name='customer_form_actionurl'}{$action}{/block}" id="customer-form" class="js-customer-form" method="post">
  <section>
    {block "form_fields"}

      <div class="row">
        <div class="col-lg-6">
          <h3 class="section-title margin-top-sm">{l s="Mes identifiants"}</h3>
          {foreach from=$formFields item="field"}
            {if $field.name|in_array:array('email', 'password')}
              {block "form_field"}
                {form_field field=$field}
              {/block}
            {/if}
          {/foreach}
          <h3 class="section-title margin-top-sm">{l s="Mon identité"}</h3>
          {foreach from=$formFields item="field"}
            {if $field.name|in_array:array('id_gender', 'firstname', 'lastname')}
              {block "form_field"}
                {form_field field=$field}
              {/block}
            {/if}
          {/foreach}
        </div>
        <div class="col-lg-6">
          {if Configuration::get('PS_B2B_ENABLE')}
            <h3 class="section-title margin-top-sm">{l s="Mon statut"}</h3>
            {foreach from=$formFields item="field"}
              {if $field.name|in_array:array('id_account_type', 'company', 'chorus', 'siret', 'tva')}
                {block "form_field"}
                  {form_field field=$field}
                {/block}
              {/if}
            {/foreach}
          {/if}
        </div>
      </div>

      <div class="row">
        <div class="col-lg-12">
          <h3 class="section-title margin-top-sm">{l s="Nouveau mot de passe"}</h3>
          <div id="new_password_area" class="form-group row ">
            <div class="col-md-12">
              <div class="input-group js-parent-focus">
                <input class="form-control js-child-focus js-visible-password" name="new_password" type="password" value="" pattern=".{ 5, }">
                <span class="input-group-btn">
                  <button class="btn" type="button" data-action="show-password" data-text-show="{l s='Montrer'}" data-text-hide="{l s='Cacher'}">
                    {l s='Montrer'}
                  </button>
                </span>
              </div>
            </div>
            <div class="col-lg-12 text-right text-muted"></div>
          </div>
        </div>
      </div>

      {foreach from=$formFields item="field"}
        {if !$field.name|in_array:array('email', 'password', 'new_password', 'id_gender', 'firstname', 'lastname', 'id_account_type', 'company', 'chorus', 'siret', 'tva')}
          {block "form_field"}
            {form_field field=$field}
          {/block}
        {/if}
      {/foreach}
      {$hook_create_account_form nofilter}
    {/block}
  </section>

  {block name='customer_form_footer'}
    <footer class="form-footer text-center clearfix margin-bottom-15">
      <input type="hidden" name="submitCreate" value="1">
      {block "form_buttons"}
        <button class="btn btn-success bold form-control-submit mt-1" data-link-action="save-customer" type="submit">
          {l s='Save' d='Shop.Theme.Actions'}
        </button>
      {/block}
    </footer>
  {/block}

</form>
{/block}