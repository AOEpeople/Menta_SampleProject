<?php

require_once dirname(__FILE__) . '/../TestcaseAbstract.php';

/**
 * Checkout test
 *
 * @author Fabrizio Branca
 * @since 2014-08-04
 */
class Tests_Demo_CheckoutTest extends TestcaseAbstract
{

    /**
     * In this situation the tests are build on top of each other, so we don't want new sessions to begin
     */
    protected $cleanupPreviousSession = false;
    protected $freshSessionForEachTestMethod = false;


    /**
     * Login during checkout
     *
     * @test
     * @return void
     */
    public function homePage()
    {
        Menta_SessionManager::closeSession(); // Close existing session from previous testcases to ensure we start fresh here

        $this->getHelperCommon()->open('/');
        $this->getHelperAssert()->assertTitle('Madison Island');
        $this->takeScreenshot('Home Page');

        // loop through all main menu items
        $mainMenuItems = $this->getHelperCommon()->getElements('css=.nav-primary>li');

//        $i=0;
//        foreach ($mainMenuItems as $mainMenuItem) { /* @var $mainMenuItem WebDriver\Element */
//            $this->getHelperCommon()->moveTo($mainMenuItem);
//            $this->takeScreenshot('Home Page with menu hover ' . ++$i);
//        }

        $this->getHelperCommon()->moveTo('xpath=//nav//a[text()="Accessories"]');
        $this->getHelperCommon()->getElement('xpath=//nav//li/a[text()="Shoes"]')->click();
    }

    /**
     * @test
     * @depends homePage
     */
    public function categoryView() {
        $this->getHelperAssert()->assertElementContainsText('css=.page-title h1', 'SHOES');
        $this->takeScreenshot('Category page');
        $this->getHelperCommon()->getElement('link=WINGTIP COGNAC OXFORD')->click();
    }

    /**
     * @test
     * @depends categoryView
     */
    public function productSingleView() {
        $this->getHelperAssert()->assertElementContainsText('css=.product-name span.h1', 'WINGTIP COGNAC OXFORD');
        $this->getHelperAssert()->assertElementContainsText('css=.regular-price .price ', '€375.00');
        $this->takeScreenshot('Product single view');

        $this->getHelperCommon()->select('id=attribute92', 'label=Cognac');
        $this->getHelperCommon()->select('id=attribute186', 'label=10');

        $this->takeScreenshot('Product single view with selected options');

        $this->getHelperCommon()->getElement('css=#product_addtocart_form button.btn-cart')->click();

    }

    /**
     * @test
     * @depends productSingleView
     */
    public function cart() {
        $this->getHelperAssert()->assertElementContainsText('css=.page-title h1', 'SHOPPING CART');
        $this->takeScreenshot('Shopping Cart');
        $this->getHelperCommon()->getElement('css=.top button.btn-checkout')->click();
    }

    /**
     * @test
     * @depends cart
     */
    public function checkout() {
        $this->getHelperAssert()->assertElementContainsText('css=.page-title h1', 'CHECKOUT');
        $this->takeScreenshot('Checkout - Login or Register');

        $checkout = Menta_ComponentManager::get('MagentoComponents_Pages_OnePageCheckout'); /* @var $checkout MagentoComponents_Pages_OnePageCheckout */

        $checkout->setCheckoutMethod('guest');
        $checkout->finishStep('checkoutMethod');

        $this->takeScreenshot('Checkout - Billing Information');

        $checkout->addAddress('us', 'billing');
        $checkout->finishStep('billingAddress');



    }

}