<?php
class Besi_CartProducts_Block_Products extends Mage_Catalog_Block_Product_List
{
    /**
     * @return mixed
     * get collection from helper
     */
    public function _getCollection()
    {
        return Mage::helper('cartproducts')->getCollection();
    }

    /**
     * @return mixed
     * check if should show the recommendation block in cart
     */
    public function showBlock(){
        return Mage::helper('cartproducts')->showReccomendationBlock();
    }

    /**
     * @return string
     * show difference of 30 (free shipping) and current subtotal
     */
    public function getDifferenceToFreeShipping(){
        $difference = floatval(Mage::helper('cartproducts')->getDifferenceToFreeShipping());
        $difference = number_format($difference,2,",","");
        return $difference;
    }
}