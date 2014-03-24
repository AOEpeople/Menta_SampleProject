<?php
require_once dirname(__FILE__).'/../TestcaseAbstract.php';

/**
 * Order single item test
 */
class Tests_Checkout_OrderSingleItemTest extends TestcaseAbstract
{

    /**
     * Order an item as a logged in user
     *
     * @test
     * @group adds_testdata
     * @return int
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

        $onePageCheckout->goThroughCheckout();

        $this->getHelperAssert()->assertTextNotPresent("There was an error capturing the transaction.");
        $orderNumber = $onePageCheckout->getOrderNumberFromSuccessPage();

        return $orderNumber;
    }

    /**
     * Check order confirmation mail
     *
     * @test
     * @depends orderItem
     * @return void
     * @param int $lastOrderNumber
     */
    public function checkOrderConfirmationMail($lastOrderNumber)
    {

        if (empty($lastOrderNumber)) {
            $this->markTestSkipped('No orderId from previous test found!');
        }

        /* @var $imapMail GeneralComponents_ImapMail */
        $imapMail = Menta_ComponentManager::get('GeneralComponents_ImapMail');

        $this->assertNotEmpty($lastOrderNumber);

        $imapMail->getMailContent('Main Store: New Order # '. $lastOrderNumber);
    }
}