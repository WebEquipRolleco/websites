<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use PrestaShop\PrestaShop\Core\Feature\TokenInUrls;

class Link extends LinkCore
{

    /**
     * Use controller name to create a link.
     *
     * @param string $controller
     * @param bool $withToken include or not the token in the url
     * @param array(string) $sfRouteParams Optional parameters to use into New architecture specific cases. If these specific cases should redirect to legacy URLs, then this parameter is used to complete GET query string
     * @param array $params Optional
     * @return string url
     * @throws PrestaShopException
     */
    public function getAdminLink($controller, $withToken = true, $sfRouteParams = array(), $params = array())
    {
        // Cannot generate admin link from front
        if (!defined('_PS_ADMIN_DIR_')) {
            return '';
        }

        if ($withToken && !TokenInUrls::isDisabled()) {
            $params['token'] = Tools::getAdminTokenLite($controller);
        }

        // Even if URL rewriting is not enabled, the page handled by Symfony must work !
        // For that, we add an 'index.php' in the URL before the route
        $sfContainer = SymfonyContainer::getInstance();
        if (!is_null($sfContainer)) {
            $sfRouter = $sfContainer->get('router');
        }

        $routeName = null;
        switch ($controller) {
            case 'AdminProducts':
                // New architecture modification: temporary behavior to switch between old and new controllers.
                $pagePreference = $sfContainer->get('prestashop.core.admin.page_preference_interface');
                $redirectLegacy = $pagePreference->getTemporaryShouldUseLegacyPage('product');
                if (!$redirectLegacy) {
                    if (array_key_exists('id_product', $sfRouteParams)) {
                        if (array_key_exists('deleteproduct', $sfRouteParams)) {
                            return $sfRouter->generate('admin_product_unit_action',
                                array('action' => 'delete', 'id' => $sfRouteParams['id_product'])
                            );
                        }
                        //default: if (array_key_exists('updateproduct', $sfRouteParams))
                        return $sfRouter->generate('admin_product_form',
                            array('id' => $sfRouteParams['id_product'])
                        );
                    }
                    if (array_key_exists('submitFilterproduct', $sfRouteParams)) {
                        $routeParams = array();
                        if (array_key_exists('filter_column_sav_quantity', $sfRouteParams)) {
                            $routeParams['quantity'] = $sfRouteParams['filter_column_sav_quantity'];
                        }
                        if (array_key_exists('filter_column_active', $sfRouteParams)) {
                            $routeParams['active'] = $sfRouteParams['filter_column_active'];
                        }

                        return $sfRouter->generate('admin_product_catalog_filters', $routeParams);
                    }

                    return $sfRouter->generate('admin_product_catalog', $sfRouteParams);
                } else {
                    $params = array_merge($params, $sfRouteParams);
                }
                break;

            default:
                $routes = array(
                    'AdminModulesSf' => 'admin_module_manage',
                    'AdminModulesCatalog' => 'admin_module_catalog',
                    'AdminModulesManage' => 'admin_module_manage',
                    'AdminModulesNotifications' => 'admin_module_notification',
                    'AdminStockManagement' => 'admin_stock_overview',
                    'AdminTranslationSf' => 'admin_international_translation_overview',
                    'AdminInformation' => 'admin_system_information',
                    'AdminAddonsCatalog' => 'admin_module_addons_store',
                    // 'AdminLogs' => 'admin_logs', @todo: uncomment when search feature is done.
                    'AdminPerformance' => 'admin_performance',
                    'AdminAdminPreferences' => 'admin_administration',
                    'AdminMaintenance' => 'admin_maintenance',
                    'AdminPPreferences' => 'admin_product_preferences',
                    'AdminPreferences' => 'admin_preferences',
                    'AdminCustomerPreferences' => 'admin_customer_preferences',
                    'AdminImport' => 'admin_import',
                );

                if (isset($routes[$controller])) {
                    $routeName = $routes[$controller];
                }
        }

        if (!is_null($routeName)) {
            $sfRoute = array_key_exists('route', $sfRouteParams) ? $sfRouteParams['route'] : $routeName;

            return $sfRouter->generate($sfRoute, $sfRouteParams, UrlGeneratorInterface::ABSOLUTE_URL);
        }

        $idLang = Context::getContext()->language->id;

        return $this->getAdminBaseLink().basename(_PS_ADMIN_DIR_).'/'.Dispatcher::getInstance()->createUrl($controller, $idLang, $params);
    }

    /**
     * @param int|null $idShop
     * @param bool|null $ssl
     * @param bool $relativeProtocol
     *
     * @return string
     */
    public function getAdminBaseLink($ssl = null, $relativeProtocol = false)
    {
        static $force_ssl = null;

        if ($ssl === null) {
            if ($force_ssl === null) {
                $force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
            }
            $ssl = $force_ssl;
        }

        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')) {
            $host = Tools::getHttpHost();
            $request_uri = rawurldecode($_SERVER['REQUEST_URI']);

            $sql = 'SELECT s.id_shop, CONCAT(su.physical_uri, su.virtual_uri) AS uri, su.domain, su.main
                    FROM '._DB_PREFIX_.'shop_url su
                    LEFT JOIN '._DB_PREFIX_.'shop s ON (s.id_shop = su.id_shop)
                    WHERE (su.domain = \''.pSQL($host).'\' OR su.domain_ssl = \''.pSQL($host).'\')
                        AND s.active = 1
                        AND s.deleted = 0
                    ORDER BY LENGTH(CONCAT(su.physical_uri, su.virtual_uri)) DESC';

            $result = Db::getInstance()->executeS($sql);

            $through = false;
            foreach ($result as $row) {
                // An URL matching current shop was found
                if (preg_match('#^'.preg_quote($row['uri'], '#').'#i', $request_uri)) {
                    $through = true;
                    $id_shop = $row['id_shop'];
                    break;
                }
            }

            if ($through) {
                $shop = new Shop($id_shop);
            } else {
                $shop = new Shop(Configuration::get('PS_SHOP_DEFAULT'));
            }
        } else {
            $shop = Context::getContext()->shop;
        }

        if ($relativeProtocol) {
            $base = '//'.($ssl && $this->ssl_enable ? $shop->domain_ssl : $shop->domain);
        } else {
            $base = (($ssl && $this->ssl_enable) ? 'https://'.$shop->domain_ssl : 'http://'.$shop->domain);
        }

        return $base.$shop->getBaseURI();
    }

}
