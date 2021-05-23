<?php

namespace Netbaseteam\Ajaxcart\Block\Magento\Catalog\Product;


class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{
    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($product, $additional = [])
    {
        return $this->_cartHelper->getAddUrl($product, $additional);
    }
}
