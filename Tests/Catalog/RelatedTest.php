<?php

require_once dirname(__FILE__) . '/../TestcaseAbstract.php';

/**
 * Upsell products tests
 */
class Tests_Catalog_RelatedTest extends TestcaseAbstract
{
    /**
     * Check upsell products present on product view page
     *
     * @test
     * @return void
     */
    public function checkRelatedProductsPresent()
    {
        /* @var $cart MagentoComponents_Pages_Cart */
        $cart = Menta_ComponentManager::get('MagentoComponents_Pages_Cart');
        $cart->clearCart();

        /* @var $singleView MagentoComponents_Pages_ProductSingleView */
        $singleView = Menta_ComponentManager::get('MagentoComponents_Pages_ProductSingleView');
        $singleView->openProduct(418);
        $this->getHelperWait()->waitForTextPresent('Tori Tank');

        $this->getHelperAssert()->assertElementPresent('id=related-checkbox554');
        $this->getHelperAssert()->assertElementPresent('id=related-checkbox553');

        // put one of the upsells into the cart
        $singleView->putProductsIntoCart(554);

        // after reloading the page the product should be  gone from upsells
        $singleView->openProduct(418);
        $this->getHelperAssert()->assertElementPresent('id=related-checkbox553');
        $this->getHelperAssert()->assertElementNotPresent('id=related-checkbox554');
    }
}