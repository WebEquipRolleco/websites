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

      {foreach from=$formFields item="field"}
        {if !$field.name|in_array:array('email', 'password', 'id_gender', 'firstname', 'lastname', 'id_account_type', 'company', 'chorus', 'siret', 'tva')}
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
        <button class="btn btn-success bold form-control-submit" data-link-action="save-customer" type="submit">
          {l s='Save' d='Shop.Theme.Actions'}
        </button>
      {/block}
    </footer>
  {/block}

</form>
{/block}