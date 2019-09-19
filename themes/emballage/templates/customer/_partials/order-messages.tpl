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
{block name='order_messages_table'}
  {if $order.messages}
    <div class="messages">
      <table class="table combinations-table">
        <thead>
          <tr>
            <th colspan="2" class="bg-darkgrey">{l s='Messages' d='Shop.Theme.Customeraccount'}</th>
          </tr>
        </thead>
        <tbody>
          {foreach from=$order.messages item=message}
            <tr>
              <td>
                <strong>
                  {if $message.id_employee}
                    <i class="fa fa-comment"></i>
                  {else}
                    <i class="fa fa-user"></i>
                  {/if}
                  {$message.name}
                </strong>
                <br />
                {$message.message_date}
              </td>
              <td>{$message.message nofilter}</td>
            </tr>
          {/foreach} 
        </tbody>
      </table>
    </div>
  {/if}
{/block}

{block name='order_message_form'}
  <section class="order-message-form">
    <form action="{$urls.pages.order_detail}" method="post">
      <table class="table combinations-table">
        <thead>
          <tr>
            <th class="bg-darkgrey">{l s='Ajouter un message' d='Shop.Theme.Customeraccount'}</th>
          </tr>
          {*<tr>
            <th class="bg-grey">
              {l s='If you would like to add a comment about your order, please write it in the field below.' d='Shop.Theme.Customeraccount'}
            </th>
          </tr>*}
        </thead>
        <tbody>
          <tr>
            <td>
              <select name="id_product" class="bg-light form-control form-control-select">
                <option value="0">{l s='-- please choose --' d='Shop.Forms.Labels'}</option>
                {foreach from=$order.products item=product}
                  <option value="{$product.id_product}">{$product.name}</option>
                {/foreach}
              </select>
            </td>
          </tr>
          <tr>
            <td>
              <textarea rows="3" name="msgText" class="bg-light form-control" placeholder="{l s='Mon message à envoyer' d='Shop.Forms.Labels'}"></textarea>
            </td>
          </tr>
          <tr>
            <td class="text-center">
              <input type="hidden" name="id_order" value="{$order.details.id}">
              <button type="submit" name="submitMessage" class="btn btn-success bold form-control-submit">
                {l s='Send' d='Shop.Theme.Actions'}
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </form>
  </section>
{/block}
