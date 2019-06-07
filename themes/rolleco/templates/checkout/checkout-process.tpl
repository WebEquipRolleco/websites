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

{if $list}
	<div id="checkout_step_list" class="row">
		{foreach from=$steps item="step" key="index"}

			{* HACK *}
			{assign var=old_template value=$step.ui->getTemplate()}
			{assign var=check value=$step.ui->setTemplate('checkout/_partials/steps/step.tpl')}

			{render file='checkout/_partials/steps/step.tpl' identifier=$step.identifier position=($index+1) ui=$step.ui list=$list}
			{assign var=check value=$step.ui->setTemplate($old_template)}

		{/foreach}
	</div>
{else}
	{foreach from=$steps item="step" key="index"}
	  {render identifier=$step.identifier position=($index + 1) ui=$step.ui list=$list}
	{/foreach}
{/if}