<?php
/**
 * Copyright © 2015 Netbase . All rights reserved.
 */
namespace Netbase\Sun\Block\Product\View;
// use Magento\Framework\UrlFactory;

class Addition extends \Magento\Framework\View\Element\Template
{
	const ORDER_UPLOAD_CODE = 'Netbaseteam_Orderupload';
	const PRICEMATRIX_CODE = 'Netbaseteam_Pricematrix';
	const ONLINE_DS_CODE = 'Netbaseteam_Pricematrix';
	/**
     * @var \Magento\Framework\Module\Manager
     */
	 public $moduleManager;
	 

    /**
     * @param \Netbase\Product\Block\Context $context
	 * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
    	\Magento\Framework\View\Element\Template\Context $context,
    	\Magento\Framework\Module\Manager $moduleManager
	)
    {
    	$this->moduleManager = $moduleManager;
		parent::__construct($context);
	
    }


	public function isOutputEnabledModule($moduleCode){
		return $this->moduleManager->isOutputEnabled($moduleCode);
	}

	public function hasOrderUpload(){
		return $this->isOutputEnabledModule(self::ORDER_UPLOAD_CODE);
	}   

	public function hasPricematrix(){
		return $this->isOutputEnabledModule(self::PRICEMATRIX_CODE);
	}
	public function hasOnlineDS(){
		return $this->isOutputEnabledModule(self::ONLINE_DS_CODE);
	}      



	
	
	
}
