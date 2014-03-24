<?php

require_once dirname(__FILE__) . '/../TestcaseAbstract.php';

/**
 * Forgot,reset password tests
 */
class Tests_Account_ForgotPasswordTest extends TestcaseAbstract
{

    /**
     * Get password reset link
     *
     * @test
     * @return array
     * @group adds_testdata
     */
    public function requestForgotPassword()
    {
        /* @var $mail MagentoComponents_ImapMail */
        $mail = Menta_ComponentManager::get('MagentoComponents_ImapMail');

        $mail->deleteAllMailsMatching('Password Reset Confirmation for');

        /* @var $customerAccount MagentoComponents_Pages_CustomerAccount */
        $customerAccount = Menta_ComponentManager::get('MagentoComponents_Pages_CustomerAccount');

        $customerAccount->openForgotPassword();

        $mailAddress = $this->getConfiguration()->getValue('testing.frontend.user');

        $this->getHelperCommon()->type('id=email_address', $mailAddress);
        $this->getHelperCommon()->click('//form[@id="form-validate"]//button[@type="submit"]');

        /* @var $message MagentoComponents_Pages_Message*/
        $message = Menta_ComponentManager::get('MagentoComponents_Pages_Message');
        $message->waitForSuccessMessagePresent(
            'If there is an account associated with ' . $mailAddress . ' you will receive an email with a link to reset your password.');


        $this->getHelperAssert()->assertTextPresent(
            'If there is an account associated with ' . $mailAddress . ' you will receive an email with a link to reset your password.'
        );

        return array(
            'firstname' => 'Test',
            'lastname' => 'User',
        );
    }

    /**
     * Check Reset Password Mail
     * 
     * @param array $userAccount
     * @return string
     * @test
     * @depends requestForgotPassword
     */
    public function checkMail(array $userAccount)
    {
        if (empty($userAccount)) {
            $this->markTestSkipped('No user account from previous test found!');
        }

        /* @var $mail MagentoComponents_ImapMail */
        $mail = Menta_ComponentManager::get('MagentoComponents_ImapMail');

        $mailContent = $mail->checkResetPasswordMail($userAccount);

        $resetLink = $mail->getResetPasswordLink($mailContent);

        $resetLinkWithoutProtocol = str_replace(array('http://', 'https://'), '', $resetLink);

        $mainDomain = $this->getConfiguration()->getValue('testing.maindomain');
        $mainDomain = str_replace(array('http://', 'https://'), '', $mainDomain);

        $prefix = $mainDomain . '/customer/account/resetpassword';

        $this->assertStringStartsWith($prefix, $resetLinkWithoutProtocol, 'Reset link is not correctly prefixed.');

        return $resetLink;
    }

    /**
     * Reset password
     *
     * @param string $resetLink
     * @return void
     * @test
     * @depends checkMail
     */
    public function resetPassword($resetLink)
    {

        if (empty($resetLink)) {
            $this->markTestSkipped('No reset link from previous test found');
        }

        /* @var $customerAccount MagentoComponents_Pages_CustomerAccount */
        $customerAccount = Menta_ComponentManager::get('MagentoComponents_Pages_CustomerAccount');

        $customerAccount->logoutViaOpen();

        $this->getHelperCommon()->open($resetLink);
        $this->getHelperAssert()->assertTextNotPresent('Your password reset link has expired.');

        $this->getHelperAssert()->assertTitle(
            'Reset a Password',
            'Title is not correct. Link was: ' . $resetLink
        );

        $password = $this->getConfiguration()->getValue('testing.frontend.password');
        $this->getHelperCommon()->type('id=password', $password);
        $this->getHelperCommon()->type('id=confirmation', $password);
        $this->getHelperCommon()->click('//form[@id="form-validate"]//button[@type="submit"]');

        /* @var $message MagentoComponents_Pages_Message*/
        $message = Menta_ComponentManager::get('MagentoComponents_Pages_Message');
        $message->waitForSuccessMessagePresent('Your password has been updated.');

        $this->getHelperAssert()->assertTitle('Customer Login');
        $this->getHelperAssert()->assertTextPresent('Your password has been updated.');

        // try to login
        $customerAccount->login();
    }
}