<?php

require_once dirname(__FILE__) . '/../TestcaseAbstract.php';

class Tests_Admin_UsernameTest extends TestcaseAbstract
{
    /**
     * Check link in header
     * @test
     */
    public function loginWhenNotLoggedIn()
    {
        $admin = Menta_ComponentManager::get('MagentoComponents_Pages_Admin');
        /* @var $admin MagentoComponents_Pages_Admin */
        $admin->openAdmin();
        $adminUser = $admin->getAdminUser();
        $admin->logIntoAdmin($adminUser['username'], $adminUser['password']);
        $admin->loginCheck();
        $admin->logoutFromAdmin();
    }

    /**
     * Check user name when logged in
     * @test
     */
    public function userNameWhenLoggedIn()
    {
        $admin = Menta_ComponentManager::get('MagentoComponents_Pages_Admin');
        /* @var $admin MagentoComponents_Pages_Admin */
        $admin->openAdmin();
        $adminUser = $admin->getAdminUser();
        $admin->logIntoAdmin($adminUser['username'], $adminUser['password']);

        $this->getHelperAssert()->assertElementContainsText('//div[' . Menta_Util_Div::contains('header'). ']',
            $adminUser['username']);

        $admin->logoutFromAdmin();
    }
}