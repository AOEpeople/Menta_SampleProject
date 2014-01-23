<?php

/**
 * Order single item test
 *
 */
class Tests_Checkout_OrderSingleItemTest extends MagentoComponents_Tests_TestcaseAbstract
{

    /**
     * Order an item as a logged in user
     *
     * @test
     * @group adds_testdata
     */
    public function orderItem()
    {

        /* @var $customerAccount MagentoComponents_Pages_CustomerAccount */
        $customerAccount = Menta_ComponentManager::get('MagentoComponents_Pages_CustomerAccount');
        $customerAccount->login();

        /* @var $cart MagentoComponents_Pages_Cart */
        $cart = Menta_ComponentManager::get('MagentoComponents_Pages_Cart');
        $cart->clearCart();

        /* @var $productSingleView MagentoComponents_Pages_ProductSingleView */
        $productSingleView = Menta_ComponentManager::get('MagentoComponents_Pages_ProductSingleView');
        $productSingleView->putProductsIntoCart($this->getConfiguration()->getValue('testing.simple.product.id'));

        /* @var $onePageCheckout MagentoComponents_Pages_OnePageCheckout */
        $onePageCheckout = Menta_ComponentManager::get('MagentoComponents_Pages_OnePageCheckout');

        $onePageCheckout->goThrowCheckout();

        $this->assertTextNotPresent("There was an error capturing the transaction.");
        $orderNumber = $onePageCheckout->getOrderNumberFromSuccessPage();

        return $orderNumber;

    }

    /**
     * Check for order confirmation mail
     *
     * @test
     * @depends orderItem
     */
    public function checkOrderConfirmationMail($lastOrderNumber)
    {

        if (empty($lastOrderNumber)) {
            $this->markTestSkipped('No orderId from previous test found!');
        }

        // check mail
        /* @var $imapMail GeneralComponents_ImapMail */
        $imapMail = Menta_ComponentManager::get('GeneralComponents_ImapMail');
        $imapMail->checkOrderConfirmationMail($lastOrderNumber);
    }
}