<?php

require_once dirname(__FILE__) . '/../TestcaseAbstract.php';

class Tests_Account_UsernameTest extends TestcaseAbstract
{

    /**
     * Check link in header
     *
     * @test
     */
    public function loginWhenNotLoggedIn()
    {
        /* @var $helper MagentoComponents_Helper*/
        $helper = Menta_ComponentManager::get('MagentoComponents_Helper');
        // open homepage
        $this->getSession()->open($this->getConfiguration()->getValue('testing.maindomain'));
        $this->assertTrue($this->getHelperCommon()->isVisible($helper->getLoginLinkPath()));
        $this->assertTrue($this->getHelperCommon()->isVisible($helper->getAccountLinkPath()));
    }

    /**
     * Check user name when logged in
     * 
     * @test
     */
    public function userNameWhenLoggedIn()
    {
        /* @var $customer MagentoComponents_Pages_CustomerAccount */
        $customer = Menta_ComponentManager::get('MagentoComponents_Pages_CustomerAccount');

        $customer->login();

        $customer->openDashboard();
        $this->getHelperAssert()->assertElementContainsText('//div[' . Menta_Util_Div::contains('welcome-msg'). ']',
            'Test User');

        $customer->logout();
    }
}