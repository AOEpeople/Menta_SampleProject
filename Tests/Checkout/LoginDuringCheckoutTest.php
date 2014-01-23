<?php

class Acceptance_Tests_Common_LoginDuringCheckoutTest extends MagentoComponents_Tests_TestcaseAbstract {

	/**
	 * Login during checkout
	 *
	 * @test
	 * @return void
	 * @author Thomas Layh <thomas.layh@aoemedia.de>
	 * @since 04.11.2011
	 */
	public function loginDuringCheckout() {

        /* @var $productSingleView MagentoComponents_Pages_ProductSingleView */
        $productSingleView = Menta_ComponentManager::get('MagentoComponents_Pages_ProductSingleView');
        $productSingleView->putProductsIntoCart(array($this->getConfiguration()->getValue('testing.product.id')));

        /* @var $checkout MagentoComponents_Pages_OnePageCheckout */
		$checkout = Menta_ComponentManager::get('MagentoComponents_Pages_OnePageCheckout'); /* @var $checkout MagentoComponents_Pages_OnePageCheckout */
		$checkout->open();

        //insert wrong credentials
        $checkout->signInWithExistingAccount('foo@bar.com','test123');
		$this->waitForTextPresent('Invalid login or password.');

		// insert correct username
        $checkout->signInWithExistingAccount($this->getConfiguration()->getValue('testing.frontend.user'),$this->getConfiguration()->getValue('testing.frontend.password'));
		$checkout->assertUserLogged();

		// click to myaccount
		$customer = Menta_ComponentManager::get('MagentoComponents_Pages_CustomerAccount'); /* @var $customer RovioComponents_Pages_CustomerAccount */
		$customer->openDashboard();

		$this->waitForTextPresent($this->getConfiguration()->getValue('testing.frontend.user'));

	}

}