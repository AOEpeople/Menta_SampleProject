<?php

require_once dirname(__FILE__) . '/../TestcaseAbstract.php';

class Tests_Account_RegisterTest extends TestcaseAbstract
{
    /**
     * @test
     * @group adds_testdata
     */
    public function testInvalidDataRegistration()
    {
        /* @var $customerAccount MagentoComponents_Pages_CustomerAccount */
        $customerAccount = Menta_ComponentManager::get('MagentoComponents_Pages_CustomerAccount');

        $this->getHelperCommon()->open('/customer/account/create/');

        $userAccount = array(
            'username' => $customerAccount->createNewMailAddress('register'),
            'password' => 'testtest',
            'firstname' => "Test_{$this->testId}",
            'lastname' => "User_{$this->testId}"
        );

        $this->getHelperWait()->waitForTextPresent($customerAccount->__('Create an Account'));
        $this->getHelperAssert()->assertTextPresent($customerAccount->__('Confirm Password'));

        $formFields = array(
            'firstname',
            'lastname',
            'email_address',
            'password',
            'confirmation'
        );

        foreach ($formFields as $formField) {
            $this->getHelperAssert()->assertElementPresent("id=$formField");
        }

        $this->getHelperCommon()->click($customerAccount->getRegistrationSubmitButtonPath());

        // all fields should have failed
        foreach ($formFields as $formField) {
            $this->getHelperAssert()->assertElementPresent("//input[@id='$formField']/following-sibling::div[@class='validation-advice']");
        }

        $this->getHelperCommon()->type("id=firstname", $userAccount['firstname']);
        $this->getHelperCommon()->click($customerAccount->getRegistrationSubmitButtonPath());
        $this->getHelperWait()->waitForElementNotVisible("//input[@id='firstname']/following-sibling::div[@class='validation-advice']");

        $this->getHelperCommon()->type("id=lastname", $userAccount['lastname']); // triggering revalidation
        $this->getHelperCommon()->click($customerAccount->getRegistrationSubmitButtonPath());
        $this->getHelperWait()->waitForElementNotVisible("//input[@id='lastname']/following-sibling::div[@class='validation-advice']");

        // first try invalid mail address ...
        $this->getHelperCommon()->type("id=email_address", "invalid_mail_address"); // triggering revalidation
        $this->getHelperCommon()->click($customerAccount->getRegistrationSubmitButtonPath());
        $this->getHelperAssert()->assertElementPresent("//input[@id='email_address']/following-sibling::div[@id='advice-validate-email-email_address']");
        $this->assertEquals(
            'Please enter a valid email address. For example johndoe@domain.com.',
            $this->getHelperCommon()->getText("//input[@id='email_address']/following-sibling::div[@id='advice-validate-email-email_address']")
        );

        // ... then a valid mail address
        $this->getHelperCommon()->type("id=email_address", $userAccount['username']);

        $this->getHelperCommon()->click($customerAccount->getRegistrationSubmitButtonPath());
        /*how to check if element is not */
        $this->getHelperAssert()
            ->assertElementPresent("//div[@id='advice-validate-email-email_address']["
                . Menta_Util_Div::contains('display: none;', 'style') . "]");


        // First try too short passwort ...
        $this->getHelperCommon()->type("id=password", "short");
        $this->getHelperCommon()->click($customerAccount->getRegistrationSubmitButtonPath());
        $this->getHelperAssert()->assertElementPresent("//input[@id='password']/following-sibling::div[@id='advice-validate-password-password']");
        $this->assertEquals(
            'Please enter 6 or more characters. Leading or trailing spaces will be ignored.',
            $this->getHelperCommon()->getText("//input[@id='password'][" . Menta_Util_Div::contains('validation-failed') . "]/following-sibling::div[@class='validation-advice']")
        );

        // ... then a password that is long enough
        $this->getHelperCommon()->type("id=password", $userAccount['password'], true);
        $this->getHelperCommon()->click($customerAccount->getRegistrationSubmitButtonPath());
        $this->getHelperAssert()->assertElementNotPresent("//input[@id='password'][" . Menta_Util_Div::contains('validation-failed') . "]");


        // ... then a password that does not match
        $this->getHelperCommon()->type("id=confirmation", "doesntmatch");
        $this->getHelperAssert()->assertElementPresent("//input[@id='confirmation'][" . Menta_Util_Div::contains('validation-failed') . "]");
        $this->getHelperCommon()->click($customerAccount->getRegistrationSubmitButtonPath());


        $this->assertEquals(
            'Please make sure your passwords match.',
            $this->getHelperCommon()->getText("//input[@id='confirmation']/following-sibling::div[" . Menta_Util_Div::contains('validation-advice') . "]")
        );

        // ... and finally a matching password
        $this->getHelperCommon()->type("id=confirmation", $userAccount['password'], true);
        $this->getHelperAssert()->assertElementNotPresent("//div[contains(@class,'validation-error')]/input[@id='confirmation']");
        $this->getHelperCommon()->click($customerAccount->getRegistrationSubmitButtonPath());

        // Check if everything is ok and we're in the dashboard
        $this->getHelperAssert()->assertTextPresent('Thank you for registering with Main Store');
        $customerAccount->assertIsOnDashboard();
        $this->getHelperAssert()->assertTextPresent($userAccount['firstname'] . ' ' . $userAccount['lastname'], 'Firstname, lastname not present.');
        $this->getHelperAssert()->assertTextPresent($userAccount['username'], "Email/username not present.");
        $this->getHelperAssert()->assertTextPresent('You have not set a default billing address.');
        $this->getHelperAssert()->assertTextPresent('You have not set a default shipping address.');

        $customerAccount->logoutViaOpen();

        return $userAccount;
    }

    /**
     * @test
     * @group adds_testdata
     * @return array
     */
    public function testRegistration()
    {
        /* @var $customerAccount MagentoComponents_Pages_CustomerAccount */
        $customerAccount = Menta_ComponentManager::get('MagentoComponents_Pages_CustomerAccount');

        $customerAccount->openSplitLoginOrRegister();

        $this->testId = md5(uniqid(rand(), TRUE));
        $userAccount = array(
            'username' => $customerAccount->createNewMailAddress('register'),
            'password' => 'testtest',
            'firstname' => "Test_{$this->testId}",
            'lastname' => "User_{$this->testId}"
        );

        $this->getHelperAssert()->assertElementPresent($customerAccount->getSplitPageRegistrationButtonPath(), "Can't find register button");
        $this->getHelperCommon()->open('/customer/account/create/');

        $this->getHelperWait()->waitForTextPresent($customerAccount->__('Create an Account'));
        $this->getHelperAssert()->assertTextPresent('Confirm Password');

        $formFields = array(
            'firstname',
            'lastname',
            'email_address',
            //'email_address_confirm',
            'password',
            'confirmation'
        );

        foreach ($formFields as $formField) {
            $this->getHelperAssert()->assertElementPresent("id=$formField");
        }

        $this->getHelperCommon()->type("id=firstname", $userAccount['firstname']);
        $this->getHelperCommon()->type("id=lastname", $userAccount['lastname']); // triggering revalidation
        $this->getHelperCommon()->type("id=email_address", $userAccount['username']);

        $this->getHelperCommon()->type("id=password", $userAccount['password']);
        $this->getHelperCommon()->type("id=confirmation", $userAccount['password']);

        /* sign in for newsletter */
        $this->getHelperAssert()->assertElementPresent($customerAccount->getNewsletterCheckboxIndicatorPath());
        $this->assertFalse($this->getHelperCommon()->isSelected($customerAccount->getNewsletterCheckboxIndicatorPath()));
        $this->getHelperAssert()->assertElementContainsText("//label[@for='is_subscribed']", $customerAccount->__('Sign Up for Newsletter'));

        $this->getHelperCommon()->click($customerAccount->getNewsletterCheckboxIndicatorPath());

        // submit the form
        $this->getHelperCommon()->click($customerAccount->getRegistrationSubmitButtonPath());

        // Check if everything is ok and we're in sign in page with thank you information
        $this->getHelperWait()->waitForTextPresent('Thank you for registering with Main Store');
        $customerAccount->assertIsOnDashboard();

        $this->getHelperAssert()->assertTextPresent($userAccount['firstname'] . ' ' . $userAccount['lastname'], 'Firstname, lastname not present.');
        $this->getHelperAssert()->assertTextPresent($userAccount['username'], "Email/username not present.");
        $this->getHelperAssert()->assertTextPresent('You have not set a default billing address.');
        $this->getHelperAssert()->assertTextPresent('You have not set a default shipping address.');

        $customerAccount->logoutViaOpen();

        return $userAccount;
    }

    /**
     * @test
     * @depends testRegistration
     * @param array $userAccount
     */
    public function testRegistrationMail($userAccount)
    {
        $this->markTestIncomplete();
        if (empty($userAccount)) {
            $this->markTestSkipped('No useraccout found from previous test');
        }

        /* @var $mail MagentoComponents_ImapMail */
        $mail = Menta_ComponentManager::get('MagentoComponents_ImapMail');


        $mail->checkRegistrationMail($userAccount);
    }

    /**
     * @test
     * @depends testRegistration
     * @param array $userAccount
     */
    public function testNewsletterSignUpMail($userAccount)
    {
        $this->markTestIncomplete();
        if (empty($userAccount)) {
            $this->markTestSkipped('No useraccout found from previous test');
        }

        /* @var $mail MagentoComponents_ImapMail */
        $mail = Menta_ComponentManager::get('MagentoComponents_ImapMail');


        $mail->checkNewsletterSignUpMail($userAccount);
    }

    /**
     * @group adds_testdata
     * @return array
     */
    public function testRegistrationWithoutNewsletterSignUp()
    {
        /* @var $customerAccount MagentoComponents_Pages_CustomerAccount */
        $customerAccount = Menta_ComponentManager::get('MagentoComponents_Pages_CustomerAccount');
        $customerAccount->openSplitLoginOrRegister();

        $this->testId = md5(uniqid(rand(), TRUE));
        $userAccount = array(
            'username' => $customerAccount->createNewMailAddress('register'),
            'password' => 'testtest',
            'firstname' => "Test_{$this->testId}",
            'lastname' => "User_{$this->testId}"
        );
        $this->getHelperAssert()->assertElementPresent($customerAccount->getSplitPageRegistrationButtonPath(), "Can't find register button");
        $this->getHelperCommon()->open('/customer/account/create/');

        $this->getHelperWait()->waitForTextPresent($customerAccount->__('Create an Account'));
        $this->getHelperAssert()->assertTextPresent($customerAccount->__('Confirm Password'));

        $formFields = array(
            'firstname',
            'lastname',
            'email_address',
            //'email_address_confirm',
            'password',
            'confirmation'
        );

        foreach ($formFields as $formField) {
            $this->getHelperAssert()->assertElementPresent("id=$formField");
        }

        $this->getHelperCommon()->type("id=firstname", $userAccount['firstname']);
        $this->getHelperCommon()->type("id=lastname", $userAccount['lastname']); // triggering revalidation
        $this->getHelperCommon()->type("id=email_address", $userAccount['username']);
        //$this->getHelperCommon()->type("id=email_address_confirm", $userAccount['username']);
        $this->getHelperCommon()->type("id=password", $userAccount['password']);
        $this->getHelperCommon()->type("id=confirmation", $userAccount['password']);

        // submit the form
        $this->getHelperCommon()->click($customerAccount->getRegistrationSubmitButtonPath());

        // Check if everything is ok and we're in sign in page with thank you information
        $this->getHelperWait()->waitForTextPresent('Thank you for registering with Main Store');
        $customerAccount->assertIsOnDashboard();
        //$this->getHelperAssert()->assertTextPresent('My Dashboard', "Couldn't register user: ".$useraccount['username']);
        $this->getHelperAssert()->assertTextPresent($userAccount['firstname'] . ' ' . $userAccount['lastname'], 'Firstname, lastname not present.');
        $this->getHelperAssert()->assertTextPresent($userAccount['username'], "Email/username not present.");
        $this->getHelperAssert()->assertTextPresent('You have not set a default billing address.');
        $this->getHelperAssert()->assertTextPresent('You have not set a default shipping address.');

        return $userAccount;
    }
}