<?php

/**
 * Calculatorshipping data helper
 */
namespace Netbaseteam\Calculatorshipping\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_layoutFactory;
    protected $_objectManagerr;
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        $this->_layoutFactory = $layoutFactory;
        $this->_objectManagerr = \Magento\Framework\App\ObjectManager::getInstance();
        parent::__construct($context);
    }

    public function showShippingEstimate(){
        return $this->_layoutFactory->create()->createBlock('Netbaseteam\Calculatorshipping\Block\Calculatorshipping')
            ->setTemplate('Netbaseteam_Calculatorshipping::view.phtml')->toHtml();
    }

    public function checkVersion(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $versonCur = $objectManager->get('Magento\Framework\App\ProductMetadataInterface')->getVersion();
        $versonRequire = ['2.0.0','2.0.1','2.0.2','2.0.3','2.0.4','2.0.5','2.0.6','2.0.7','2.0.8','2.0.9'];
        return in_array($versonCur, $versonRequire)?1:0;

    }

    public function showPrice($price){
        return $this->_objectManagerr->get('\Magento\Framework\Pricing\Helper\Data')->currency($price, true, false);
    }

    /*get getSuccessHtml method*/
    public function getSuccessHtml($product)
    {
        $layout = $this->_layoutFactory->create();
        $layout->getUpdate()->load('calculatorshipping_success_message');
        $layout->generateXml();
        $layout->generateElements();

        return $layout->getOutput();
    }

    public function getPopupOptionHtml($product)
    {
        $layout = $this->_layoutFactory->create();

        $update = $layout->getUpdate();
        $update->load('calculatorshipping_popup_option');
        $layout->generateXml();
        $layout->generateElements();

        return $layout->getOutput();
    }

    public function EnableModule(){
        return $this->scopeConfig->getValue('calculatorshipping/view/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getPopupTitle(){
        return $this->scopeConfig->getValue('calculatorshipping/view/popup_title', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getButtonTitle(){
        return $this->scopeConfig->getValue('calculatorshipping/view/est_btn_title', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getShowOnCategory(){
        return $this->scopeConfig->getValue('calculatorshipping/view/show_est_cateogry', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getAutoIp(){
        return $this->scopeConfig->getValue('calculatorshipping/view/auto_ip', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getIncludeCart(){
        //return $this->scopeConfig->getValue('calculatorshipping/view/show_include_cart', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Return Ip Address of customer
     */

    public function getIpAddr(){
        if (!empty($_SERVER['HTTP_CLIENT_IP'])){
            // check ip from share internet
            $ipAddr = $_SERVER['HTTP_CLIENT_IP'];
        }elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            // to check ip is pass from proxy
            $ipAddr = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $ipAddr = $_SERVER['REMOTE_ADDR'];
        }
        /* return "174.125.192.125";
        return "117.4.252.165"; */
        return $ipAddr;
    }

    /**
     *  Get information about Location of customer by ip
     */
    public function getLocation(){
        // verify the IP.
        ip2long($this->getIpAddr())== -1 || ip2long($this->getIpAddr()) === false ? trigger_error("Invalid IP", E_USER_ERROR) : "";
        //get the JSON result from hostip.info
        $result = file_get_contents("http://api.ipstack.com/".$this->getIpAddr() . "?access_key=316e30d6ed0ce348a99d0d0bb49a64fe");
        /**
         * Ip Address
         * Country: United State, Region: Alabama, City: Hartselle, Zipcode: 35640
         */
        // $result = file_get_contents("http://freegeoip.net/json/174.125.192.125");

        /**
         * Ip Address
         * Country: United Kingdom , Region: London, City: London, Zipcode: N6
         */

        // $result = file_get_contents("http://freegeoip.net/json/77.99.179.98");

        /**
         * Ip Address
         * Country: Viet Nam , Region: Ha Noi, City: Ha Noi, Zipcode: ''
         */
        // $result = file_get_contents("http://freegeoip.net/json/117.0.35.249");

        $result = json_decode($result, 1);

        return $result;
    }
}
