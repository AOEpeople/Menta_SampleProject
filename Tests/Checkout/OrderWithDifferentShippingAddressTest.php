<?php
require_once dirname(__FILE__).'/../TestcaseAbstract.php';

/**
 * Order downloadable product
 *
 */
class Acceptance_Tests_Checkout_OrderWithDifferentShippingAddressTest extends TestcaseAbstract
{

    protected $lastOrderId;

    /**
     * Order downloadable product
     *
     * @test
     * @group adds_testdata
     */
    public function orderWithDifferentShippingAddress()
    {
        /* @var $cart MagentoComponents_Pages_Cart */
        $cart = Menta_ComponentManager::get('MagentoComponents_Pages_Cart');
        $cart->clearCart();

        $productSingleView = Menta_ComponentManager::get('MagentoComponents_Pages_ProductSingleView');
        /* @var $productSingleView MagentoComponents_Pages_ProductSingleView */
        $productSingleView->putProductsIntoCart($this->getConfiguration()->getValue('testing.simple.product.id'));

        $onePageCheckout = Menta_ComponentManager::get('MagentoComponents_Pages_OnePageCheckout');
        /* @var $onePageCheckout MagentoComponents_Pages_OnePageCheckout */
        $onePageCheckout->open();

        $onePageCheckout->setCheckoutMethod('register');
        $onePageCheckout->finishStep('checkoutMethod');

        $onePageCheckout->addAddress('uk', 'billing');
        $this->getHelperAssert()->assertElementPresent('billing:customer_password');
        $onePageCheckout->saveAccountForLaterUse();
        $onePageCheckout->toggleShipToDifferentAddress();

        /* @var $customerAccount MagentoComponents_Pages_CustomerAccount */
        $customerAccount = Menta_ComponentManager::get('MagentoComponents_Pages_CustomerAccount');


        $onePageCheckout->finishStep('billingAddress');

        $onePageCheckout->addAddress('uk', 'shipping');
        $onePageCheckout->finishStep('shippingAddress');

        $onePageCheckout->finishStep('shippingMethod');

        $onePageCheckout->selectPaymentMethodCheckmo();
        $onePageCheckout->finishStep('paymentMethod');

        $onePageCheckout->submitForm();

        $this->lastOrderId = $onePageCheckout->getOrderIdFromSuccessPage();
        $lastOrderNumber = $onePageCheckout->getOrderNumberFromSuccessPage();

        $this->getHelperCommon()->open('/sales/order/history/?limit=50');

        $viewOrderLinkPath = '//*[@id="my-orders-table"]/tbody/tr/td/span/a[contains(@href, "view/order_id/'. $this->lastOrderId .'")]';

        $this->getHelperCommon()->click($viewOrderLinkPath);
        $this->getHelperWait()->waitForElementPresent('//h1[ ' . Menta_Util_Div::containsText("Order #$lastOrderNumber") . ']');

        $this->getHelperAssert()->assertTextPresent($this->lastOrderId);

        $addressProvider = new MagentoComponents_Provider_Address();
        $shippingAddress = $addressProvider->getAddressField('shipping', 'uk');
        $billingAddress = $addressProvider->getAddressField('billing', 'uk');

        $this->getHelperAssert()->assertTextPresent($shippingAddress['firstname']);
        $this->getHelperAssert()->assertTextPresent($shippingAddress['lastname']);
        $this->getHelperAssert()->assertTextPresent($shippingAddress['street1']);
        if (isset($shippingAddress['region']) && $shippingAddress['region']) {
            $this->getHelperAssert()->assertTextPresent($shippingAddress['region']);
        }
        $this->getHelperAssert()->assertTextPresent($shippingAddress['country']);
        $this->getHelperAssert()->assertTextPresent($shippingAddress['phone']);

        $this->getHelperAssert()->assertTextPresent($billingAddress['firstname']);
        $this->getHelperAssert()->assertTextPresent($billingAddress['lastname']);
        $this->getHelperAssert()->assertTextPresent($billingAddress['street1']);
        if (isset($billingAddress['region']) && $billingAddress['region']) {
            $this->getHelperAssert()->assertTextPresent($billingAddress['region']);
        }
        $this->getHelperAssert()->assertTextPresent($billingAddress['country']);
        $this->getHelperAssert()->assertTextPresent($billingAddress['phone']);

        return $lastOrderNumber;
    }

    /**
     * @param int $lastOrderNumber
     * @return void
     * @test
     * @group webservice_api
     * @depends orderWithDifferentShippingAddress
     */
    public function checkAddresses($lastOrderNumber)
    {
        if (empty($lastOrderNumber)) {
            $this->markTestSkipped('No orderId from previous test found!');
        }

        // check via Webservice API
        $api = Menta_ComponentManager::get('MagentoComponents_WebServiceApi');
        /* @var $api MagentoComponents_WebServiceApi */
        $order = $api->getOrderInfo($lastOrderNumber);

        $this->assertEquals('ShippingFirstname', $order['shipping_address']['firstname']);
        $this->assertEquals('ShippingLastname', $order['shipping_address']['lastname']);

        $this->assertEquals('BillingFirstname', $order['billing_address']['firstname']);
        $this->assertEquals('BillingLastname', $order['billing_address']['lastname']);

    }


    /*
     *
     * TODO test validateCheckoutTaxesWithDifferentBillingAndShipping
     */
}