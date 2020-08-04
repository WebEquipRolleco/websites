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
{block name="address_form"}
  <div class="js-address-form">
    {include file='_partials/form-errors.tpl' errors=$errors['']}

    {block name="address_form_url"}
    <form method="POST" action="{url entity='address' params=['id_address' => $id_address]}" data-id-address="{$id_address}" data-refresh-url="{url entity='address' params=['ajax' => 1, 'action' => 'addressForm']}">
    {/block}

      {block name="address_form_fields"}
        {block name='form_fields'}
          <section class="form-fields">
            <div class="row">
              <div class="col-xs-12 col-lg-6">
                <h3 class="section-title margin-top-sm">{l s="mes informations"}</h3>
                {foreach from=$formFields item="field"}
                  {if $field.name|in_array:array('id_address', 'id_customer', 'back', 'token', 'alias', 'firstname', 'lastname', 'company', 'vat_number')}
                    {block name='form_field'}
                      {form_field field=$field}
                    {/block}
                  {/if}
                {/foreach}
                <h3 class="section-title margin-top-sm">{l s="Contact"}</h3>
                {foreach from=$formFields item="field"}
                  {if $field.name|in_array:array('phone', 'phone_mobile')}
                    {block name='form_field'}
                      {form_field field=$field}
                    {/block}
                  {/if}
                {/foreach}
              </div>
              <div class="col-xs-12 col-lg-6">
                <h3 class="section-title margin-top-sm">{l s="Coordonnées"}</h3>
                {foreach from=$formFields item="field"}
                  {if $field.name|in_array:array('address1', 'address2', 'postcode', 'city', 'id_country')}
                    {block name='form_field'}
                      {form_field field=$field}
                    {/block}
                  {/if}
                {/foreach}
              </div>
            </div>
          </section>
        {/block}
      {/block}

      {block name="address_form_footer"}
      <footer class="form-footer clearfix">
        <input type="hidden" name="submitAddress" value="1">
        {block name='form_buttons'}
          <div class="bg-light text-center margin-bottom-15">
            <button class="btn btn-success margin-top-10 margin-bottom-10" type="submit" class="form-control-submit">
              <b>{l s='Save' d='Shop.Theme.Actions'}</b>
            </button>
          </div>
        {/block}
      </footer>
      {/block}

    </form>
  </div>
{/block}
