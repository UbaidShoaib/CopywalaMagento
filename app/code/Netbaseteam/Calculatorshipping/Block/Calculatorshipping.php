<?php
namespace Netbaseteam\Calculatorshipping\Block;
use Magento\Shipping\Model\Shipping;

/**
 * Calculatorshipping content block
 */
class Calculatorshipping extends \Magento\Framework\View\Element\Template
{
    /**
     * Calculatorshipping factory
     *
     * @var \Netbaseteam\Calculatorshipping\Model\CalculatorshippingFactory
     */
    protected $_calculatorshippingCollectionFactory;
    
    /** @var \Netbaseteam\Calculatorshipping\Helper\Data */
    protected $_dataHelper;
    
	protected $_countryCollectionFactory;
	protected $_scopeConfig;
    protected $_shipModel;
    protected $_checkoutSession;
    protected $_pricecurrency;
	protected $_regionColFactory;
	
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Netbaseteam\Calculatorshipping\Model\ResourceModel\Calculatorshipping\CollectionFactory $calculatorshippingCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
		\Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Netbaseteam\Calculatorshipping\Helper\Data $dataHelper,
		\Magento\Checkout\Model\Session $checkoutSession,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Shipping\Model\Shipping $shippingModel,
		\Magento\Framework\Pricing\PriceCurrencyInterface $pricecurrency,
		\Magento\Directory\Model\RegionFactory $regionColFactory,
        array $data = []
    ) {
        $this->_dataHelper = $dataHelper;
		$this->_countryCollectionFactory = $countryCollectionFactory;
		$this->_scopeConfig = $scopeConfig;
		$this->_checkoutSession = $checkoutSession;
        $this->_shipModel = $shippingModel;
        $this->_pricecurrency = $pricecurrency;
		$this->_regionColFactory     = $regionColFactory;
        parent::__construct(
            $context,
            $data
        );
//        $this->setTemplate('Netbaseteam_Calculatorshipping::opencalculatorshipping.phtml');
    }
	
    public function getAvailableCountries()
    {
        $collection = $this->_countryCollectionFactory->create();
        $collection->addFieldToSelect('*');

        return $collection;
    }
	
	public function getCheckOutSession()
	{
		return $this->_checkoutSession;
	}
	
	public function _EnableModule(){
        return $this->_dataHelper->EnableModule();
    }
	
	public function _getPopupTitle(){
        return $this->_dataHelper->getPopupTitle();
    }
	
	public function _getButtonTitle(){
        return $this->_dataHelper->getButtonTitle();
    }
	
	public function _getShowOnCategory(){
        return $this->_dataHelper->getShowOnCategory();
    }
	
	public function _getCurrencySymbol(){
		return $this->_pricecurrency->getCurrency()->getCurrencySymbol();
	}
	
	public function _getLocation(){
		return $this->_dataHelper->getLocation();
	}
	
	public function _getAutoIp(){
		return $this->_dataHelper->getAutoIp();
	}
	
	public function _getIncludeCart(){
		return $this->_dataHelper->getIncludeCart();
	}
	
	public function _getRegionId($region_code = null) {
		$regions = $this->_regionColFactory
						->create()
						->getCollection()
						->addFieldToFilter('code', $region_code);
		
		if($region_code == "") return "";
		foreach($regions as $r){
			return $r->getId();
		}
	}

	public function shippingPrice() { 
		$session = $this->getCheckOutSession();
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$address = $request = $objectManager->create('Magento\Quote\Model\Quote\Address\RateRequest'); 
		//$request = new \Magento\Quote\Model\Quote\Address\RateCollectorInterface();
		$item = array();
		$item["country_id"] = "AU";
		$item["region_id"] =  "526";
		$item["region"] = "Victoria";
		$item["postcode"] = "3016";
		/* $address->setData($item); */
		
		
		$request = $objectManager->create('Magento\Quote\Model\Quote\Address\RateCollectorInterface');
		
		$address->setCountryId("US") 
				->setCity("New York") 
				->setPostcode("11209") 
				->setRegionId("") 
				->setRegion("") 
				->collectShippingRates(); 

		$shippingCarrier = 'flatrate';
		$carriers[$shippingCarrier] = '';
		//$result = $request->collectRates($address, array_keys($carriers))->getResult();
		$result = $this->_shipModel->collectRatesByAddress($address, array_keys($carriers))->getResult();
		foreach ($result->getAllRates() as $rate) {
			\zend_debug::dump($rate);
		}
	} 
}
