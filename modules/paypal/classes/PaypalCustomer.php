<?php
/**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2019 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class PaypalCustomer.
 */
class PaypalCustomer extends ObjectModel
{
    /** @var integer PrestaShop Customer ID */
    public $id_customer;

    /** @var string Paypal customer reference number */
    public $reference;

    /** @var string BT, EC, etc... */
    public $method;

    /** @var bool mode of customer (sandbox or live) */
    public $sandbox;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'paypal_customer',
        'primary' => 'id_paypal_customer',
        'multilang' => false,
        'fields' => array(
            'id_customer' => array('type' => self::TYPE_INT),
            'reference' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'method' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'sandbox' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        )
    );

    /**
     * Load customer object by ID
     * @param integer $id_customer PrestaShop Customer ID
     * @param string $method method alias
     * @param bool $sandbox mode of customer
     * @return object PaypalCustomer
     */
    public static function loadCustomerByMethod($id_customer, $method, $sandbox)
    {
        $db = Db::getInstance();
        $query = new DbQuery();
        $query->select('id_paypal_customer');
        $query->from('paypal_customer');
        $query->where('id_customer = '.(int)$id_customer);
        $query->where('method = "'.pSQL($method).'"');
        $query->where('sandbox = ' . (int)$sandbox);
        $id = $db->getValue($query);
        return new PaypalCustomer($id);
    }
}
