<?php

require_once dirname(__FILE__) . '/../TestcaseAbstract.php';

/**
 * Create screenshots for different views
 */

class Acceptance_Tests_Common_ScreenshotsTest extends TestcaseAbstract
{

    /**
     * Homepage screenshot
     *
     * @test
     * @return void
     */
    public function homePage()
    {
        $this->getHelperCommon()->open('/');
        $this->takeScreenshot('Home page');
    }

    /**
     * Category view screenshot
     * @test
     * @return void
     */
    public function categoryView()
    {
        /* @var $categoryView MagentoComponents_Pages_CategoryView */
        $categoryView = Menta_ComponentManager::get('MagentoComponents_Pages_CategoryView');
        $this->getHelperCommon()->open(10);
        $this->takeScreenshot('Category view', 'Category view of a sample test category');
    }

    /**
     * Simple product view screenshot
     *
     * @test
     * @return void
     */
    public function singleProductView()
    {
        // single view
        $singleView = Menta_ComponentManager::get('MagentoComponents_Pages_ProductSingleView');
        /* @var $singleView MagentoComponents_Pages_ProductSingleView */
        $singleView->openProduct(51);
        $this->takeScreenshot('Product view');
    }

    /**
     * Configurable product view screenshot
     *
     * @test
     * @return void
     */
    public function configurableProductView()
    {
        /* @var $singleView MagentoComponents_Pages_ProductSingleView */
        $singleView = Menta_ComponentManager::get('MagentoComponents_Pages_ProductSingleView');
        $singleView->openProduct(126);
        $this->takeScreenshot('Configurable view', 'Configurable product with color and size selection.');
    }


    /**
     * Bundle product view screenshot
     *
     * @test
     * @return void
     */
    public function bundleProductView()
    {
        /* @var $singleView MagentoComponents_Pages_ProductSingleView */
        $singleView = Menta_ComponentManager::get('MagentoComponents_Pages_ProductSingleView');
        $singleView->openProduct(165);
        $this->takeScreenshot('Bundle Product view', 'Bundle product with ram, cpu, selection.');
    }


    /**
     *
     * Cart, onepage checkout, dashboard, order history screenshots
     *
     * @test
     * @return void
     */
    public function checkout()
    {
        /* @var $productView MagentoComponents_Pages_ProductSingleView */
        $productView = Menta_ComponentManager::get('MagentoComponents_Pages_ProductSingleView');
        $productView->putProductsIntoCart(51);

        /* @var $cart MagentoComponents_Pages_Cart */
        $cart = Menta_ComponentManager::get('MagentoComponents_Pages_Cart');

        $cart->open();
        $this->takeScreenshot('Cart with 1 item - simple product');

        $productView->openProduct($this->getConfiguration()->getValue('testing.configurable.product.id'));
        $productView->selectDropDownOption(100, 525); // small
        $productView->clickAddToCart();


        $cart->open();
        $this->takeScreenshot('Cart with 2 item - simple, configurable product');

        // Login / Register
        /* @var $customerAccount MagentoComponents_Pages_CustomerAccount */
        $customerAccount = Menta_ComponentManager::get('MagentoComponents_Pages_CustomerAccount');

        $customerAccount->openSplitLoginOrRegister();
        $this->takeScreenshot('Login/Register', 'Split page showing the registration or login form');

        $customerAccount->login();

        // dashboard
        $customerAccount->openDashboard();
        $this->takeScreenshot('Customer dashboard', 'Dashboard of an existing user with some orders');

        // order history
        $customerAccount->openOrderHistory();
        $this->takeScreenshot('Order history');

        // checkout
        /* @var $checkout MagentoComponents_Pages_OnePageCheckout */
        $checkout = Menta_ComponentManager::get('MagentoComponents_Pages_OnePageCheckout');

        $checkout->open();
        $this->takeScreenshot('OnePage Checkout', 'Default Checkout');
    }
}