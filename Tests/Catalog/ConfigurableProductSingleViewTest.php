<?php

require_once dirname(__FILE__) . '/../TestcaseAbstract.php';

/**
 * Configurable product tests
 */
class Acceptance_Tests_Catalog_ConfigurableProductSingleViewTest extends TestcaseAbstract
{

    public $nestTeeSingleProducts = array(
        0 => array('price' => "$140.00", 'status' => 'inStock',
            'size' => 65, 'sizeLabel' => '30', 'colorPrice' => '0', 'color' => 25), //30 khaki

        1 => array('price' => "$140.00", 'status' => 'inStock',
            'size' => 63, 'sizeLabel' => '32', 'colorPrice' => '0', 'color' => 25), //32 khaki

        2 => array('price' => "$140.00", 'status' => 'inStock',
            'size' => 61, 'sizeLabel' => '34', 'colorPrice' => '0', 'color' => 25), //34 khaki

        3 => array('price' => "$140.00", 'status' => 'inStock',
            'size' => 65, 'sizeLabel' => '30', 'colorPrice' => '5.25', 'color' => 17), //30 charcoal

        4 => array('price' => "$140.00", 'status' => 'inStock',
            'size' => 63, 'sizeLabel' => '32', 'colorPrice' => '5.25', 'color' => 17), //32 charcoal

        5 => array('price' => "$140.00", 'status' => 'inStock',
            'size' => 61, 'sizeLabel' => '34', 'colorPrice' => '5.25', 'color' => 17), //34 charcoal
    );

    public $nestTeeSizeArray = array(
        61 => array('price' => "$140.00", 'status' => 'inStock'), //34
        65 => array('price' => "$140.00", 'status' => 'inStock'), //32
        63 => array('price' => "$140.00", 'status' => 'inStock'), //30
    );

    /**
     * Data provider for test sizeAndcolorTest
     *
     * @return array
     */
    public function sizeAndColorDataProvider()
    {
        return array(
            array(456, 180, 92, $this->nestTeeSingleProducts, $this->nestTeeSizeArray), //nest tee
        );
    }


    /**
     * Select size and color products, check status and price
     *
     * @test
     *
     * @group unstable
     * @dataProvider sizeAndColorDataProvider
     * @param int $productId configurable product id
     * @param int $sizeAttrId size attribute id
     * @param int $colorAttrId color attribute id
     * @param array $singleProducts
     * @param array $sizeArray
     */
    public function sizeAndColorTest($productId, $sizeAttrId, $colorAttrId, $singleProducts,
        $sizeArray)
    {
        /* @var $productView MagentoComponents_Pages_ProductSingleView */
        $productView = Menta_ComponentManager::get('MagentoComponents_Pages_ProductSingleView');

        $productView->openProduct($productId);
        $this->getHelperWait()->waitForElementPresent("id=product_addtocart_form");
        $this->getHelperWait()->waitForTextPresent('Khaki Bowery Chino Pants');

        $productView->assertAddtoCartButton('inStock');

        foreach ($singleProducts as $key => $product) {

            //Choose color
            $productView->selectDropDownOption($product['color'], $colorAttrId);
            $productView->assertDropdownSelectedValue($product['color'], $colorAttrId);

            // Choose size
            $productView->selectDropDownOption($product['size'], $sizeAttrId);
            $productView->assertDropdownSelectedValue($product['size'], $sizeAttrId);

            $productView->assertRegularPrice($sizeArray[$product['size']]['price']);
            $productView->assertStatus($sizeArray[$product['size']]['status']);


            // Product price and stock status after choosing size
            $productView->assertRegularPrice($product['price']);
            $productView->assertStatus($product['status']);

            $productView->assertAddtoCartButton($product['status']);
        }
    }
}