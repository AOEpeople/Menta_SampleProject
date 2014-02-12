<?php

require_once dirname(__FILE__) . '/../TestcaseAbstract.php';

class Tests_Account_DashboardTest extends TestcaseAbstract
{

    /**
     * Check newsletter box and subscribe to newsletter
     *
     * @test
     */
    public function testCheckNewsletterboxAndSave()
    {
        /* @var $mail MagentoComponents_ImapMail */
        $mail = Menta_ComponentManager::get('MagentoComponents_ImapMail');

        $mail->deleteAllMailsMatching('Newsletter subscription success');

        /* @var $customerAccount MagentoComponents_Pages_CustomerAccount */
        $customerAccount = Menta_ComponentManager::get('MagentoComponents_Pages_CustomerAccount');

        $customerAccount->login();

        $customerAccount->openDashboard();


        $this->getHelperAssert()->assertElementPresent($customerAccount->getNewsletterEditPathInDashoboard());
        $this->getHelperCommon()->click($customerAccount->getNewsletterEditPathInDashoboard());
        $this->getHelperWait()->waitForElementPresent('//h1[' . Menta_Util_Div::containsText('Newsletter Subscription') . ']');

        if ($this->getHelperCommon()->isSelected("//input[@id='subscription']")) {
            $this->markTestSkipped('Subscribe to newsletter already selected!');
        }

        $this->getHelperAssert()->assertElementPresent("//input[@id='subscription']");
        $this->getHelperCommon()->click("//input[@id='subscription']");
        $this->getHelperCommon()->isSelected("//input[@id='subscription']");
        $this->getHelperCommon()->click('//form[@id="form-validate"]//button[@type="submit"]');

        /* @var $message MagentoComponents_Pages_Message */
        $message = Menta_ComponentManager::get('MagentoComponents_Pages_Message');
        $message->waitForSuccessMessagePresent('The subscription has been saved');

        $this->getHelperWait()->waitForTextPresent('The subscription has been saved.');

        $mail->checkNewsletterSignUpMail(array(
            'firstname' => 'Test',
            'lastname' => 'User',
        ));
    }

    /**
     * Check newsletter box and unsubscribe from newsletter
     *
     * @test
     */
    public function testUnCheckNewsletterboxAndSave()
    {
        /* @var $mail MagentoComponents_ImapMail */
        $mail = Menta_ComponentManager::get('MagentoComponents_ImapMail');

        $mail->deleteAllMailsMatching('Newsletter unsubscription success');

        /* @var $customerAccount MagentoComponents_Pages_CustomerAccount */
        $customerAccount = Menta_ComponentManager::get('MagentoComponents_Pages_CustomerAccount');

        $customerAccount->login();

        $customerAccount->openDashboard();

        $this->getHelperAssert()->assertElementPresent($customerAccount->getNewsletterEditPathInDashoboard());
        $this->getHelperCommon()->click($customerAccount->getNewsletterEditPathInDashoboard());

        if (!$this->getHelperCommon()->isSelected("//input[@id='subscription']")) {
            $this->markTestSkipped('Subscribe to newsletter already unchecked!');
        }

        $this->getHelperAssert()->assertElementPresent("//input[@id='subscription']");
        $this->getHelperCommon()->click("//input[@id='subscription']");
        $this->getHelperCommon()->isSelected("//input[@id='subscription']");
        $this->getHelperCommon()->click('//form[@id="form-validate"]//button[@type="submit"]');

        /* @var $message MagentoComponents_Pages_Message */
        $message = Menta_ComponentManager::get('MagentoComponents_Pages_Message');
        $message->waitForSuccessMessagePresent('The subscription has been removed.');

        $mail->checkNewsletterSignOutMail(array(
            'firstname' => 'Test',
            'lastname' => 'User',
        ));
    }
}