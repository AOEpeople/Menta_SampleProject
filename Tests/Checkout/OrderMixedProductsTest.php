<?php

require_once dirname(__FILE__).'/../TestcaseAbstract.php';


/*
 *  * Order mixed item test - simple + configuration
 */

class Tests_Checkout_OrderMixedProductsTest extends TestcaseAbstract
{

    /**
     * @return int
     * @test
     */
    public function orderMixedProducts()
    {

        /* @var $customerAccount MagentoComponents_Pages_CustomerAccount */
        $customerAccount = Menta_ComponentManager::get('MagentoComponents_Pages_CustomerAccount');
        $customerAccount->login();

        $cart = Menta_ComponentManager::get('MagentoComponents_Pages_Cart');
        /* @var $cart MagentoComponents_Pages_Cart */
        $cart->clearCart();

        $productView = Menta_ComponentManager::get('MagentoComponents_Pages_ProductSingleView');
        /* @var $productView MagentoComponents_Pages_ProductSingleView */
        $productView->putProductsIntoCart($this->getConfiguration()->getValue('testing.simple.product.id'));

        $productView->openProduct($this->getConfiguration()->getValue('testing.configurable.product.id'));
        $productView->selectSize(100, 525); // small
        $productView->clickAddToCart();

        $onePageCheckout = Menta_ComponentManager::get('MagentoComponents_Pages_OnePageCheckout');
        /* @var $onePageCheckout MagentoComponents_Pages_OnePageCheckout */
        $onePageCheckout->goThroughCheckout();

        $this->assertTextNotPresent("There was an error capturing the transaction.");
        $orderNumber = $onePageCheckout->getOrderNumberFromSuccessPage();

        return $orderNumber;
    }

    /**
     * @test
     * @depends orderMixedProducts
     * @return void
     * @param int $lastOrderNumber
     */
    public function checkOrderInfo($lastOrderNumber)
    {

        if (empty($lastOrderNumber)) {
            $this->markTestSkipped('No order id from previous test found');
        }

        $api = Menta_ComponentManager::get('MagentoComponents_WebServiceApi');
        /* @var $api AoeComponents_Magento_WebServiceApi */
        $orderInfo = $api->getOrderInfo($lastOrderNumber);

        $this->assertEquals('simple', $orderInfo['items'][0]['product_type']);
        $this->assertEquals('1111', $orderInfo['items'][0]['sku']);
        $this->assertEquals('Ottoman', $orderInfo['items'][0]['name']);

        $this->assertEquals('configurable', $orderInfo['items'][1]['product_type']);
        $this->assertEquals('coal_sm', $orderInfo['items'][1]['sku']);
        $this->assertEquals('Coalesce: Functioning On Impatience T-Shirt', $orderInfo['items'][1]['name']);

        $this->assertEquals('simple', $orderInfo['items'][2]['product_type']);
        $this->assertEquals('coal_sm', $orderInfo['items'][2]['sku']);
        $this->assertEquals('Coalesce: Functioning On Impatience T-Shirt', $orderInfo['items'][2]['name']);
        $this->assertEquals($orderInfo['items'][1]['item_id'], $orderInfo['items'][2]['parent_item_id']);

        $this->assertEquals('new', $orderInfo['state']);
        $this->assertEquals('pending', $orderInfo['status']);
    }

}
