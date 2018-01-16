<?php

class Besi_CartProducts_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * @return object
     * get previuos ordered collection
     */
    public function getCollection()
    {

        $previousOrdered = $this->getPreviousOrderedItems();
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection->getSelect()->group('e.entity_id');

        /**
         * filter by categories
         */
        $collection->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id=entity_id', null, 'left')->addAttributeToFilter('category_id', array('in' => $previousOrdered));


        $collection->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents();


        /**
         * add price filter to make total > 30 EUR
         */

        $minPrice = 30 - $this->getCartSubtotal();
        $collection->addFieldToFilter('price', array('gteq' => $minPrice));
        $collection->setOrder('price', 'ASC');
        /**
         * add availability filters
         */
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        /**
         * return only 3 products from collection
         */
        $collection->getSelect()->limit(3);

        return $collection;
    }

    /**
     * @return array|bool
     * get previous ordered items category ids
     */
    public function getPreviousOrderedItems()
    {
        $orderedCat = array();
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {

            $orders = Mage::getModel('sales/order')->getCollection()->addAttributeToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId());
            foreach ($orders as $eachOrder) {
                $order = Mage::getModel("sales/order")->load($eachOrder->getId());

                $items = $order->getAllVisibleItems();
                foreach ($items as $item) {
                    $product = Mage::getModel('catalog/product')->load($item->getProductId());
                    $cats = $product->getCategoryIds();
                    foreach ($cats as $cat) {
                        $orderedCat[] = $cat;
                    }
                }
            }

            return array_unique($orderedCat);
        }
        return false;
    }

    /**
     * @return bool
     * check if should show recommendation block or not
     */
    public function showReccomendationBlock()
    {
        $customerSession = Mage::getSingleton('customer/session');
        if (!$customerSession->isLoggedIn()) {
            return false;
        } elseif ($this->hasPreviousOrders($customerSession) && $this->getCartSubtotal() < 30) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $session
     * @return bool
     * check if customer has previous orders
     */
    public function hasPreviousOrders($session)
    {
        $customerEmail = $session->getCustomer()->getEmail();
        $collection = Mage::getModel('sales/order')->getCollection()
            ->addFieldToFilter('customer_email', $customerEmail);

        if ($collection->count() >= 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return mixed
     * get quote subtotal total
     */
    public function getCartSubtotal()
    {
        $quote = Mage::getModel('checkout/session')->getQuote();
        $shipping = $quote->getShippingAddress()->getShippingAmount();
        return ($quote->getGrandTotal() - $shipping);
    }

    public function getDifferenceToFreeShipping()
    {
        return (30 - $this->getCartSubtotal());
    }
}