<?php

require_once dirname(__FILE__) . '/../TestcaseAbstract.php';

/**
 * Tax calculation test
 */
class Tests_Checkout_TaxCalculationTest extends TestcaseAbstract
{
    public function productsProvider()
    {
        return array(
            array('id' => 44, 'price' => '$550.00'),
            array('id' => 132, 'price' => '$99.00'),
            array('id' => 133, 'price' => '$34.00'),
            array('id' => 134, 'price' => '$19.00'),
        );
    }

    public function checkTaxCalculationProvider()
    {
        return array(
            //country,	gross subtotal, gross shipping,	grand total
            'US' => array('us', "$702.00", "$20.00", "$722.00", null),
            'Finland' => array('fi', "$702.00", "$20.00", "$722.00", null),
            'Germany' => array('de', "$702.00", "$20.00", "$722.00", null),
            'Estonia' => array('es', "$702.00", "$20.00", "$722.00", null),
            'Egypt' => array('eg', "$702.00", "$20.00", "$722.00", null),
            'Italy' => array('it', "$702.00", "$20.00", "$722.00", null),
            'US California' => array('us_california', "$702.00", "$20.00", "$779.92", "$57.92")
        );
    }

    /**
     * Check tax calculation in checkout
     *
     * @test
     * @param $country
     * @param $grossSubtotal
     * @param $grossShipping
     * @param $grandTotal
     * @param $tax
     * @dataProvider checkTaxCalculationProvider
     * country, gross subtotal, gross shipping, grand total, tax
     */
    public function checkTaxCalculation($country, $grossSubtotal, $grossShipping, $grandTotal, $tax)
    {
        $onePageCheckout = Menta_ComponentManager::get('MagentoComponents_Pages_OnePageCheckout');
        /* @var $onePageCheckout MagentoComponents_Pages_OnePageCheckout */
        $categoryView = Menta_ComponentManager::get('MagentoComponents_Pages_CategoryView');
        /* @var $categoryView MagentoComponents_Pages_CategoryView */
        $productSingleView = Menta_ComponentManager::get('MagentoComponents_Pages_ProductSingleView');
        /* @var $productSingleView MagentoComponents_Pages_ProductSingleView */
        $cart = Menta_ComponentManager::get('MagentoComponents_Pages_Cart');
        /* @var $cart MagentoComponents_Pages_Cart */

        $cart->clearCart();

        $products = $this->productsProvider();

        $this->checkSingleViewPrices($products);

        $categoryView->open(12); //test product category
        $this->checkCategoryViewPrices($categoryView, $products);
        $productSingleView->putProductsIntoCart(array(44, 132, 133, 134));


        $onePageCheckout->open();
        $onePageCheckout->setCheckoutMethod('register');
        $onePageCheckout->finishStep('checkoutMethod');

        $onePageCheckout->addAddress($country, 'billing');
        $onePageCheckout->assertBillingCountry(strtoupper(substr($country, 0, 2)));

        $this->getHelperAssert()->assertElementPresent('billing:customer_password');
        $onePageCheckout->saveAccountForLaterUse();

        $onePageCheckout->finishStep('billingAddress');
        $onePageCheckout->waitForShippingMethod();

        $onePageCheckout->finishStep('shippingMethod');
        $onePageCheckout->selectPaymentMethodCheckmo();

        $onePageCheckout->finishStep('paymentMethod');
        $onePageCheckout->waitForReview();

        $this->checkCheckoutPrices($onePageCheckout, $products, $grossSubtotal, $grossShipping, $grandTotal, $tax);
    }

    /**
     * Check products price in category page
     *
     * @param $categoryView MagentoComponents_Pages_CategoryView
     * @param $products
     */
    public function checkCategoryViewPrices($categoryView, $products)
    {
        foreach ($products as $product) {
            $categoryView->assertRegularPrice($product['id'], $product['price']);
        }
    }

    /**
     * Check products price in product page
     *
     * @param $products
     */
    public function checkSingleViewPrices($products)
    {
        foreach ($products as $product) {
            $this->checkSingleViewPrice($product['id'], $product['price']);
        }
    }

    /**
     * Check product price in product page
     *
     * @param $productId
     * @param $price
     */
    public function checkSingleViewPrice($productId, $price)
    {
        $singleView = Menta_ComponentManager::get('MagentoComponents_Pages_ProductSingleView');
        /* @var $singleView MagentoComponents_Pages_ProductSingleView */
        $singleView->openProduct($productId);
        $singleView->assertRegularPrice($price);
    }

    /**
     * Check checkout prices in summary
     *
     * @param $checkout MagentoComponents_Pages_OnePageCheckout
     * @param $products
     * @param $subtotal
     * @param $shippingSubtotal
     * @param $tax
     * @param $grandTotal
     */
    public function checkCheckoutPrices($checkout, $products, $subtotal, $shippingSubtotal, $grandTotal, $tax)
    {
        $i = 1;
        foreach ($products as $product) {
            $checkout->assertPriceInSummary($i, $product['price']);
            $i++;
        }

        if (!is_null($shippingSubtotal)) {
            $checkout->assertShippingPrice($shippingSubtotal);
        }

        if (!is_null($subtotal)) {
            $checkout->assertSubtotal($subtotal);
        }

        if (!is_null($tax)) {
            $checkout->assertTax($tax);
        }

        if (!is_null($grandTotal)) {
            $checkout->assertGrandTotal($grandTotal);
        }
    }
}
