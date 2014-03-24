<?php

require_once dirname(__FILE__) . '/../TestcaseAbstract.php';

/**
 * Product view tests
 * Check product price in product view, cart view, category view
 */
class Tests_Catalog_SingleViewTest extends TestcaseAbstract
{
    /**
     * Put simple products into cart
     *
     * @test
     */
    public function putItemsIntoCartFromProductSingleView()
    {
        $cart = Menta_ComponentManager::get('MagentoComponents_Pages_Cart');
        /* @var $cart MagentoComponents_Pages_Cart */
        $cart->clearCart();

        /* @var $singleView MagentoComponents_Pages_ProductSingleView */
        $singleView = Menta_ComponentManager::get('MagentoComponents_Pages_ProductSingleView');

        $this->assertEquals(0, $cart->getCartItemsFromHeader(), 'Cart items from eggs is not 0');

        $singleView->putProductsIntoCart($this->getConfiguration()->getValue('testing.simple.product.id'));
        $this->assertEquals(1, $cart->getCartItemsFromHeader(), 'Cart items from eggs is not 1');

        $singleView->putProductsIntoCart(52);
        $this->assertEquals(2, $cart->getCartItemsFromHeader(), 'Cart items from eggs is not 2');

        // add same product a second
        $singleView->putProductsIntoCart(52);
        /* @var $categoryView MagentoComponents_Pages_CategoryView */
        $categoryView = Menta_ComponentManager::get('MagentoComponents_Pages_CategoryView');
        $categoryView->open(10);

        $this->assertEquals(3, $cart->getCartItemsFromHeader(), 'Cart items from eggs is not 3 (from category view)');

        $cart->open();
        $this->assertEquals(3, $cart->getCartItemsFromHeader(), 'Cart items from eggs is not 3 (from cart view)');
        $this->assertEquals(2, $this->getHelperCommon()->getElementCount($cart->getCartTablePath() . "/tbody/tr"), 'Expecting 2 rows in cart');

        // remove second row
        $this->getHelperCommon()->click($cart->getCartTablePath() . '/tbody/tr[2]/td/a[' .
        Menta_Util_Div::contains($cart->__('Remove item'), 'title') . ']');
        $this->assertEquals(1, $cart->getCartItemsFromHeader(), 'Cart items from eggs is not 1 (from cart view)');
    }
}