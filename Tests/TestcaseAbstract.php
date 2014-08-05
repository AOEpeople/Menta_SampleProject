<?php

require_once dirname(__FILE__) . '/bootstrap.php';

/**
 * Abstract base class for Magento Tests
 */
abstract class TestcaseAbstract extends Menta_PHPUnit_Testcase_Selenium2
{

    protected $cleanupPreviousSession = false; // not needed if we have a new session anyway
    protected $freshSessionForEachTestMethod = true;

    /**
     * Will send the test result to sauce labs in case we're running tests there
     *
     * @return void
     */
    protected function tearDown()
    {

        if (Menta_SessionManager::activeSessionExists()) {
            $status = $this->getStatus();
            if ($status == PHPUnit_Runner_BaseTestRunner::STATUS_ERROR
                || $status == PHPUnit_Runner_BaseTestRunner::STATUS_FAILURE
            ) {
                $passed = false;
            } else {
                $passed = true;
            }

            $sauceUserId = $this->getConfiguration()->getValue('testing.sauce.userId');
            $sauceAccessKey = $this->getConfiguration()->getValue('testing.sauce.accessKey');
            if (!empty($sauceUserId) && !empty($sauceAccessKey)) {
                $rest = new WebDriver\SauceLabs\SauceRest($sauceUserId, $sauceAccessKey);
                $rest->updateJob(Menta_SessionManager::getSessionId(), array(WebDriver\SauceLabs\Capability::PASSED => $passed));
            }
        }

        parent::tearDown();
    }


    /**
     * Convenience methods...
     */

    /**
     * Get common helper
     *
     * @return Menta_Component_Helper_Common
     */
    protected function getHelperCommon()
    {
        return Menta_ComponentManager::get('Menta_Component_Helper_Common');
    }

    /**
     * Get assert helper
     *
     * @return Menta_Component_Helper_Assert
     */
    protected function getHelperAssert()
    {
        return Menta_ComponentManager::get('Menta_Component_Helper_Assert');
    }

    /**
     * Get assert helper
     *
     * @return Menta_Component_Helper_Wait
     */
    protected function getHelperWait()
    {
        return Menta_ComponentManager::get('Menta_Component_Helper_Wait');
    }
}