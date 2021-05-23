<?php
namespace Netbase\Product\Plugin\Catalog\Block;
class View 
{
	public $productOption;

	public $productview;

    public function __construct(
      \Magento\Catalog\Model\Product\Option $productOption,
      \Magento\Catalog\Block\Product\AbstractProduct $productview
    )
    {
        $this->productOption = $productOption;
        $this->productview = $productview;
    }

    public function aroundHasOptions($hasOption,$excute)
    {

        $customOptions = $this->productOption->getProductOptionCollection($this->productview->getProduct());
        if (!empty($customOptions->getData())) {
        	return true;
        }

        return $excute();
    }


   

}
