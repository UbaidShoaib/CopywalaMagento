<?php

namespace Multistore\Base\Helper;


use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $_currency;

    /**
     * @var \Magento\Catalog\Block\Product\ListProduct
     */
    protected $_listProduct;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    public function __construct(
        Context $context,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Block\Product\ListProduct $listProduct,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_moduleManager = $moduleManager;
        $this->_listProduct = $listProduct;
        $this->_currency = $currency;
        $this->_storeManager = $storeManager;
    }

    /**
     * check module enable or not
     * @return bool
     */
    public function enableExtension($extensionName)
    {
        if ($this->_moduleManager->isOutputEnabled($extensionName)) {
            return true;
        }
    }

    /**
     * price html
     * @param $originalPrice
     * @param $finalPrice
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPriceListHtml($originalPrice, $finalPrice)
    {
        $currencyCode = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        $currencySymbol = $this->_currency->load($currencyCode)->getCurrencySymbol();
        $precision = 2;
        $originalPriceNew = $this->_currency->format($originalPrice, ['symbol' => $currencySymbol, 'precision'=> $precision], false, false);
        $finalPriceNew = $this->_currency->format($finalPrice, ['symbol' => $currencySymbol, 'precision'=> $precision], false, false);

        $class = $finalPrice ? 'price-sale' : '';
        $html = "<div class='price-home $class'>";
        if ($finalPrice) {
            $html .= "<span class='special'>$finalPriceNew</span>";
        };

        if ($originalPrice) {
            $html .= "<span class='normal'>$originalPriceNew</span>";
        };
        $html .= "</div>";

        echo $html;
    }

    /**
     * check price enable or not
     * @param $_product
     * @return int
     */
    public function checkIsCallForPrice($_product)
    {
        $enableCallForPrice = $this->enableExtension('Netbaseteam_Callforprice');
        $flag = false;
        if ($enableCallForPrice) {
            if ($this->_listProduct->enableCallForPrice() && ($this->_listProduct->hidePrice($_product) != 'hide_button' && $this->_listProduct->hidePrice($_product) == 'show_button')) {
                $flag = true;
            }
        }
        return $flag;
    }

    /**
     * Button CallForPrice
     * @param $_product
     * @return string
     */
    public function getButtonCallForPriceHtml($_product)
    {
        $backGroundColor = $this->_listProduct->getScopeConfig('callforprice/general/button_color');
        $borderColor = $this->_listProduct->getScopeConfig('callforprice/general/button_border_color');
        $color = $this->_listProduct->getScopeConfig('callforprice/general/button_text_color');
        $dataRev = $_product->getId();
        $callForPriceButton = $this->_listProduct->getScopeConfig('callforprice/general/callforprice_text');
        $html = "<div style='background-color: $backGroundColor;border-color: $borderColor'>";
        $html .= "<button class='action tocart primary' id='id-call' style='$color' data-rev='$dataRev' onclick='share(this)'>";
        $html .= "<span>$callForPriceButton</span></button></div>";
        return $html;
    }
}
