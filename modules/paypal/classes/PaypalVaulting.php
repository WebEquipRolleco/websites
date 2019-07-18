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
 * Class PaypalVaulting.
 */
class PaypalVaulting extends ObjectModel
{
    /** @var string Token received from BT */
    public $token;

    /** @var int BT Customer ID */
    public $id_paypal_customer;

    /** @var string client can set card name in prestashop account */
    public $name;

    /** @var string Card or paypal account short info like last 4 numbers/exp. date */
    public $info;

    /** @var string card ou paypal, etc... */
    public $payment_tool;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'paypal_vaulting',
        'primary' => 'id_paypal_vaulting',
        'multilang' => false,
        'fields' => array(
            'token' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'id_paypal_customer' => array('type' => self::TYPE_INT),
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'info' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'payment_tool' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        )
    );

    /**
     * Checking if vault was created already for this card/pp account
     * @return boolean
     */
    public static function vaultingExist($token, $customer)
    {
        $db = Db::getInstance();
        $query = new DbQuery();
        $query->select('id_paypal_vaulting');
        $query->from('paypal_vaulting');
        $query->where('token = "'.pSQL($token).'" AND id_paypal_customer = '.(int)$customer);
        $result = $db->getValue($query);
        return $result ? true : false;
    }

    /**
     * Get all vaulted methods (cards, accounts) for this customer
     * @param integer $customer PrestaShop Customer ID
     * @param string $method payment tool (card or paypal account)
     * @return array PaypalVaulting
     */
    public static function getCustomerMethods($customer, $method)
    {
        $db = Db::getInstance();
        $query = new DbQuery();
        $query->select('*');
        $query->from('paypal_vaulting', 'pv');
        $query->leftJoin('paypal_customer', 'pc', 'pv.id_paypal_customer = pc.id_paypal_customer');
        $query->where('pc.id_customer = '.(int)$customer);
        $query->where('pv.payment_tool = "'.pSQL($method).'"');
        $query->where('pc.sandbox = ' . (int)Configuration::get('PAYPAL_SANDBOX'));
        $result = $db->executeS($query);
        return $result;
    }

    /**
     * Get vaulted methods grouped by tools (card or paypal account)
     * @param integer $customer PrestaShop Customer ID
     * @return array PaypalVaulting
     */
    public static function getCustomerGroupedMethods($customer)
    {
        $db = Db::getInstance();
        $methods = array();
        $query = new DbQuery();
        $query->select('*');
        $query->from('paypal_vaulting', 'pv');
        $query->leftJoin('paypal_customer', 'pc', 'pv.id_paypal_customer = pc.id_paypal_customer');
        $query->where('pc.id_customer = '.(int)$customer);
        $query->where('pc.sandbox = ' . (int)Configuration::get('PAYPAL_SANDBOX'));
        $results = $db->query($query);
        while ($result = $db->nextRow($results)) {
            $methods[$result['payment_tool']][] = $result;
        }
        return $methods;
    }
}
