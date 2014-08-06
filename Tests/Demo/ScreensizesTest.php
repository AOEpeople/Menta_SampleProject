<?php

require_once dirname(__FILE__) . '/../TestcaseAbstract.php';

/**
 * Class Tests_Demo_ScreensizesTest
 *
 * @author Fabrizio Branca
 * @since 2014-08-05
 */
class Tests_Demo_ScreensizesTest extends TestcaseAbstract
{

    /**
     * @test
     * @dataProvider screenSizes
     */
    public function homePage($width, $height) {
        $this->getSession()->window('main')->postSize(array('width' => $width, 'height' => $height));

        $this->getHelperCommon()->open('/');
        $this->takeScreenshot("Home Page")->setVariant("{$width}x{$height}");
    }

    /**
     * Used as dataprovider
     *
     * @return array
     */
    public function screenSizes()
    {
        return array(
            array(1920, 900),
            array(1280, 600),
            array(980, 1280),
            array(768, 1024),
            array(360, 640),
            array(320, 480),
        );
    }

} 