<?php

require_once dirname(__FILE__) . '/../TestcaseAbstract.php';

class Acceptance_Tests_Catalog_UpsellTest extends TestcaseAbstract
{
    /**
     * Check upsell products present on product view page
     *
     * @test
     * @return void
     */
    public function checkUpsellProductsPresent()
    {
        /* @var $cart MagentoComponents_Pages_Cart */
        $cart = Menta_ComponentManager::get('MagentoComponents_Pages_Cart');
        $cart->clearCart();

        /* @var $singleView MagentoComponents_Pages_ProductSingleView */
        $singleView = Menta_ComponentManager::get('MagentoComponents_Pages_ProductSingleView');
        $singleView->openProduct(126);
        $this->getHelperAssert()->assertTextPresent('The Only Children: Paisley T-Shirt');

        $this->getHelperAssert()->assertElementPresent('id=product-price-39-upsell');
        $this->getHelperAssert()->assertElementPresent('id=product-price-119-upsell');
        $this->getHelperAssert()->assertElementPresent('id=product-price-123-upsell');

        // put one of the upsells into the cart
        $singleView->putProductsIntoCart(39);

        // after reloading the page the product should be  gone from upsells
        $singleView->openProduct(126);
        $this->getHelperAssert()->assertElementPresent('id=product-price-119-upsell');
        $this->getHelperAssert()->assertElementPresent('id=product-price-123-upsell');
    }
}