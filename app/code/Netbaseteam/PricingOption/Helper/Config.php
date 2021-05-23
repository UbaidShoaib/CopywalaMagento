<?php
namespace Netbaseteam\PricingOption\Helper;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
	public function getUploadMaxSize(){
		return $this->scopeConfig->getValue('pricingoption/general/upload_max', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		
	}
	
	public function getUploadMinSize(){
		return $this->scopeConfig->getValue('pricingoption/general/upload_min', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		
	}

	public function getUnit(){
		return $this->scopeConfig->getValue('pricingoption/general/unit', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}
}