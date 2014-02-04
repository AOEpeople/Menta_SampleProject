<?php

require_once dirname(__FILE__) . '/../TestcaseAbstract.php';

class Acceptance_Tests_Catalog_ConfigurableProductSingleViewTest extends TestcaseAbstract
{
    public $singleProducts = array(
        125 => array('price' => "$130.00", 'status' => 'inStock', 'size' => 98, 'sizePrice' => 10),
        124 => array('price' => "$120.00", 'status' => 'inStock', 'size' => 99, 'sizePrice' => 20),
        37 => array('price' => "$110.00", 'status' => 'inStock', 'size' => 100, 'sizePrice' => 30),
    );
    public $sizeArray = array(
        100 => array('price' => "10", 'label' => 'Small +$10.00'),
        99 => array('price' => "20", 'label' => 'Medium +$20.00'),
        98 => array('price' => "30", 'label' => 'Large +$30.00'),
    );

    public $selectedSizeLabelArray = array(
        '100' => array(
            '100' => 'Small', '99' => 'Medium +$10.00', '98' => 'Large +$20.00'),
            '99' => array('100' => 'Small -$10.00', '99' => 'Medium', '98' => 'Large +$10.00'),
            '98' => array('100' => 'Small -$20.00', '99' => 'Medium -$10.00', '98' => 'Large'),
    );

    public function sizeOnlyDataProvider()
    {
        return array(
            array(123, 525, $this->singleProducts,$this->sizeArray,$this->selectedSizeLabelArray));
    }

    /**
     * @test
     * @group unstable
     * @dataProvider sizeOnlyDataProvider
     * @param int $productId
     * @param int $sizeAttrId
     * @param array $singleProducts
     * @param array $sizeArray
     * @param array $selectedLabelArray
     */
    public function sizeOnlyTest($productId, $sizeAttrId, $singleProducts, $sizeArray, $selectedLabelArray)
    {
        /* @var $productView MagentoComponents_Pages_ProductSingleView */
        $productView = Menta_ComponentManager::get('MagentoComponents_Pages_ProductSingleView');

        $productView->openProduct($productId);
        $this->getHelperWait()->waitForElementPresent("id=product_addtocart_form");

        // No options are selected
        $productView->assertRegularPrice('$100.00');
        $productView->assertStatus('inStock');
        $productView->assertQuantity(1);

        $productView->assertAddtoCartButton('inStock');

        // Size selector
        $productView->assertSelectedLabel('Choose an Option...', $sizeAttrId);
        foreach ($sizeArray as $id => $size) {
            $productView->assertDropdownExistOptionLabelWithPrice($id,$size['label'],$size['price'],$sizeAttrId);
        }

        foreach ($singleProducts as $key => $product) {
            // Choose size
            $productView->selectDropDownOption("", $sizeAttrId);
            $productView->selectDropDownOption($product['size'], $sizeAttrId);

            $productView->assertSelectedLabel($selectedLabelArray[$product['size']][$product['size']], $sizeAttrId);
            $productView->assertDropdownSelectedValue($product['size'], $sizeAttrId);

            foreach ($sizeArray as $id => $size) {
                $productView->assertDropdownExistOptionLabelWithPrice(
                    $id,
                    $selectedLabelArray[$product['size']][$id],
                    $size['price'],
                    $sizeAttrId
                );
            }

            // Product price and stock status after choosing size
            $productView->assertRegularPrice($product['price']);
            $productView->assertStatus($product['status']);
            $productView->assertQuantity(1);

            $productView->assertAddToCartButton($product['status']);
        }
    }




    public $nestTeeSingleProducts = array(
        38 => array('price' => "$13.50", 'status' => 'inStock',
            'size' => 100, 'sizeLabel' => 'Small', 'colorPrice' => '0', 'color' => 26), //small red

        128 => array('price' => "$13.50", 'status' => 'inStock',
            'size' => 99, 'sizeLabel' => 'Medium', 'colorPrice' => '0', 'color' => 26), //medium red

        130 => array('price' => "$13.50", 'status' => 'inStock',
            'size' => 98, 'sizeLabel' => 'Large', 'colorPrice' => '0', 'color' => 26), //large red

        127 => array('price' => "$18.75", 'status' => 'inStock',
            'size' => 100, 'sizeLabel' => 'Small', 'colorPrice' => '5.25', 'color' => 22), //small green

        129 => array('price' => "$18.75", 'status' => 'inStock',
            'size' => 99, 'sizeLabel' => 'Medium', 'colorPrice' => '5.25', 'color' => 22), //medium green

        131 => array('price' => "$18.75", 'status' => 'inStock',
            'size' => 98, 'sizeLabel' => 'Large', 'colorPrice' => '5.25', 'color' => 22), //large green
    );

    public $nestTeeColorArray = array(
        22 => array('price' => "$18.75", 'status' => 'inStock'), //green
        26 => array('price' => "$13.50", 'status' => 'inStock'), //red
    );

    public function sizeAndColorDataProvider()
    {
        return array(
            array(126, 525, 272, $this->nestTeeSingleProducts, $this->nestTeeColorArray), //nest tee
        );
    }


    /**
     * @test
     * @group unstable
     * @dataProvider sizeAndColorDataProvider
     * @param int $productId
     * @param int $sizeAttrId
     * @param int $colorAttrId
     * @param array $singleProducts
     * @param array $colorArray
     */
    public function sizeAndColorTest($productId, $sizeAttrId, $colorAttrId, $singleProducts, $colorArray)
    {
        /* @var $productView MagentoComponents_Pages_ProductSingleView */
        $productView = Menta_ComponentManager::get('MagentoComponents_Pages_ProductSingleView');

        $productView->openProduct($productId);
        $this->getHelperWait()->waitForElementPresent("id=product_addtocart_form");

        $productView->assertQuantity(1);
        $productView->assertAddtoCartButton('inStock');

        foreach ($singleProducts as $key => $product) {
            // Choose size
            $productView->selectDropDownOption($product['size'], $sizeAttrId);
            $productView->assertDropdownSelectedValue($product['size'], $sizeAttrId);
            $productView->assertDropdownExistOptionLabelWithPrice($product['size'], $product['sizeLabel'], 0, $sizeAttrId);

            $productView->assertSelectedLabel("Choose an Option...", 272);

            //Choose color
            $productView->selectDropDownOption($product['color'], $colorAttrId);
            $productView->assertDropdownSelectedValue($product['color'], $colorAttrId);
            $productView->assertRegularPrice($colorArray[$product['color']]['price']);
            $productView->assertStatus($colorArray[$product['color']]['status']);

            // Product price and stock status after choosing size
            $productView->assertRegularPrice($product['price']);
            $productView->assertStatus($product['status']);
            $productView->assertQuantity(1);

            $productView->assertAddtoCartButton($product['status']);
        }
    }
}