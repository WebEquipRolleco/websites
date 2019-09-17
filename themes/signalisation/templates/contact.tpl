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
{extends file='layouts/layout-full-width.tpl'}

{block name='content'}
  
  	{if isset($alert)}
  		<div class="alert alert-{$alert.type}">
  			<b>{$alert.message}</b>
  		</div>
 	{/if}

	<h1 class="page-title top-space">
  		{l s="Contactez-nous"}
  	</h1>

  	<table>
		<tbody>
			<tr>
				<td class="icon-cell"><i class="far fa-4x fa-comments"></i></td>
				<td class="description-cell">
					{l s='Pour toute demande ou information, vous pouvez nous contacter en remplissant le formulaire ci-dessous, nous vous répondrons dans les plus brefs délais.'}
					<p class="bold">{l s="Vous pouvez également nous joindre par téléphone au %s" sprintf=[Configuration::get('PS_SHOP_PHONE')]}</p>
				</td>
			</tr>
		</tbody>
	</table>

	<form method="post">
		<div class="row">

		  	<div class="col-lg-6">
		  		<h3 class="section-title margin-top-sm">{l s="Votre message"}</h3>
		  		<div class="form-group">
					<label for="contact_firstname">
						{l s="Prénom" d='Shop.Forms.Labels'}
					</label>
					<input type="text" id="contact_firstname" class="form-control" name="contact[firstname]">
				</div>
				<div class="form-group">
					<label for="contact_lastname">
						{l s="Nom" d='Shop.Forms.Labels'} <em class="text-danger">*</em>
					</label>
					<input type="text" id="contact_lastname" class="form-control" name="contact[lastname]" required>
				</div>
				<div class="form-group">
					<label for="contact_company">
						{l s="Société" d='Shop.Forms.Labels'} <em class="text-danger">*</em>
					</label>
					<input type="text" id="contact_company" class="form-control" name="contact[company]" required>
				</div>
				<div class="form-group">
					<label for="contact_number">
						{l s="Numéro de client" d='Shop.Forms.Labels'}
					</label>
					<input type="text" id="contact_number" class="form-control" name="contact[number]">
				</div>
				<div class="form-group">
					<label for="contact_phone">
						{l s="Téléphone" d='Shop.Forms.Labels'}
					</label>
					<input type="text" id="contact_phone" class="form-control" name="contact[phone]">
				</div>
				<div class="form-group">
					<label for="contact_email">
						{l s="E-mail" d='Shop.Forms.Labels'} <em class="text-danger">*</em>
					</label>
					<input type="email" id="contact_email" class="form-control" name="contact[email]" required>
				</div>
				<div class="form-group">
					<label for="contact_city">
						{l s="CP / Ville" d='Shop.Forms.Labels'}
					</label>
					<input type="text" id="contact_city" class="form-control" name="contact[city]">
				</div>
		  	</div>

		  	<div class="col-xs-12 col-lg-6">
		  		<h3 class="section-title margin-top-sm">{l s="Votre message"}</h3>
		  		<div class="form-group">
					<label for="contact_message">
						{l s="Demande détaillée" d='Shop.Forms.Labels'}
						<em class="text-danger bold">*</em>
					</label>
					<textarea rows="26" id="contact_message" class="form-control" name="contact[message]" required></textarea>
				</div>
		  	</div>

		  	<div class="col-lg-12 margin-bottom-15 text-right">
		  		<div class="well">
			  		<button type="submit" class="btn btn-info bold">
			  			{l s="Envoyer ma demande" d='Shop.Forms.Labels'}
			  		</button>
			  	</div>
		  	</div>

		</div>
	</form>

{/block}