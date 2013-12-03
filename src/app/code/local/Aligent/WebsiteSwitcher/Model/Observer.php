<?php
/**
 * @category    Aligent
 * @package     Aligent_WebsiteSwitcher
 * @copyright   Copyright (c) 2013 Aligent Consulting. (http://www.aligent.com.au)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author 		Luke Mills <luke@aligent.com.au>
 * @author      Swapna Palaniswamy <swapna@aligent.com.au>
 * @author      Jim O'Halloran <jim@aligent.com.au>
 */
class Aligent_WebsiteSwitcher_Model_Observer
{

    const HANDLE_PREFIX = 'aligent_websiteswitcher_';

    /**
     * Observe the controller_front_init_before event and set the store cookie
     * to the current store.
     */
    public function setStoreCookie() {
        $iCurrentStoreId = Mage::app()->getCookie()->get(Mage_Core_Model_Store::COOKIE_NAME);
        if ($iCurrentStoreId === false) {
            if (Mage::helper('aligent_websiteswitcher')->canUseGeoIP()) {
                $iGeoStore = Mage::helper('aligent_websiteswitcher')->geoLocateToStoreId();

                if ($iGeoStore !== Mage::app()->getStore()->getId()) {
                    $oStore = Mage::getModel('core/store')->load($iGeoStore);

                    Mage::app()->getCookie()->set(Mage_Core_Model_Store::COOKIE_NAME, $oStore->getCode(), true);

                    Mage::app()->init($oStore->getCode(), 'store');

                }
            }
        } else {
            Mage::log("Store Id: ".Mage::app()->getStore()->getId()." Cookie Store: ".$iCurrentStoreId);
            Mage::app()->getCookie()->set(Mage_Core_Model_Store::COOKIE_NAME, Mage::app()->getStore()->getCode(), true);
        }
    }


    /**
     * Observes the controller_action_layout_load_before event amd sets
     * appropriate layout handles for the layout system to work with.
     */
    public function setLayoutHandles() {

        /** @var $oHelper Aligent_WebsiteSwitcher_Helper_Data */
        $oHelper = Mage::helper('aligent_websiteswitcher');

        /** @var $oUpdate Mage_Core_Model_Layout_Update */
        $oUpdate = Mage::app()->getLayout()->getUpdate();

        if (count(Mage::app()->getStores()) > 1) {

            if ($oHelper->canDisplayInMenu()) {
                $oUpdate->addHandle(self::HANDLE_PREFIX . 'display_in_menu');
            }

            if ($oHelper->canDisplayModal()) {
                $oUpdate->addHandle(self::HANDLE_PREFIX . 'display_modal');
            }
        }

    }

}
