<?php

require_once dirname(__FILE__).'/../TestcaseAbstract.php';

 /*
  * Login during checkout test
  */
class Tests_Checkout_LoginDuringCheckoutTest extends TestcaseAbstract {

	/**
	 * Login during checkout
	 *
	 * @test
	 * @return void
	 */
	public function loginDuringCheckout() {

        /* @var $productSingleView MagentoComponents_Pages_ProductSingleView */
        $productSingleView = Menta_ComponentManager::get('MagentoComponents_Pages_ProductSingleView');
        $productSingleView->putProductsIntoCart(array($this->getConfiguration()->getValue('testing.simple.product.id')));

        /* @var $checkout MagentoComponents_Pages_OnePageCheckout */
		$checkout = Menta_ComponentManager::get('MagentoComponents_Pages_OnePageCheckout');
		$checkout->open();

        //insert wrong credentials
        $checkout->signInWithExistingAccount('foo@bar.com','test123');
		$this->getHelperWait()->waitForTextPresent('Invalid login or password.');

		// insert correct username
        $checkout->signInWithExistingAccount($this->getConfiguration()->getValue('testing.frontend.user'),$this->getConfiguration()->getValue('testing.frontend.password'));
		$checkout->assertUserLogged();

		// click to myaccount
        /* @var $customer MagentoComponents_Pages_CustomerAccount */
		$customer = Menta_ComponentManager::get('MagentoComponents_Pages_CustomerAccount');
		$customer->openDashboard();

		$this->getHelperWait()->waitForTextPresent($this->getConfiguration()->getValue('testing.frontend.user'));
	}

}