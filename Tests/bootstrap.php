<?php

define('TEST_ROOTDIR', dirname(__FILE__) . '/../');

require_once TEST_ROOTDIR . 'vendor/autoload.php';

Menta_Bootstrap::init();

Menta_ConfigurationPhpUnitVars::addConfigurationFile(TEST_ROOTDIR . 'conf/defaults.xml');


// Initialize session manager and provider selenium server url
$configuration = Menta_ConfigurationPhpUnitVars::getInstance();
Menta_SessionManager::init(
	$configuration->getValue('testing.selenium.seleniumServerUrl'),
	$configuration->getValue('testing.selenium.browser'),
	$configuration->getValue('testing.selenium.capabilities')
);

// Do some stuff based on configuration values after the session is initialized
Menta_Events::addObserver('after_session_create', function(\WebDriver\Session $session, $forceNew) {

	$configuration = Menta_ConfigurationPhpUnitVars::getInstance();
		
	// window focus
	try {
		if ($configuration->issetKey('testing.selenium.windowFocus') && $configuration->getValue('testing.selenium.windowFocus')) {
			$session->window('main'); // focus
		}
	} catch (Exception $e) {
		// nevermind
	}
		
	// window position
	try {
		if ($configuration->issetKey('testing.selenium.windowPosition')) {
			list($x, $y) = explode(',', $configuration->getValue('testing.selenium.windowPosition'));
			$x = intval(trim($x)); $y = intval(trim($y));
			$session->window('main')->postPosition(array('x' => $x, 'y' => $y));
		}
	} catch (Exception $e) {
		// nevermind
	}

	// window size
	try {
		if ($configuration->issetKey('testing.selenium.windowSize')) {
			list($width, $height) = explode('x', $configuration->getValue('testing.selenium.windowSize'));
			$width = intval(trim($width)); $height = intval(trim($height));
			if (empty($height) || empty($width)) {
				throw new Exception('Invalid window size');
			}
			$session->window('main')->postSize(array('width' => $width, 'height' => $height));
		}
	} catch (Exception $e) {
		// nevermind
	}

	// implicit wait
	try {
		if ($configuration->issetKey('testing.selenium.timeoutImplicitWait')) {
			$time = $configuration->getValue('testing.selenium.timeoutImplicitWait');
			$time = intval($time);
			$session->timeouts()->implicit_wait(array('ms' => $time));
		}
	} catch (Exception $e) {
		// nevermind
	}
	
});


