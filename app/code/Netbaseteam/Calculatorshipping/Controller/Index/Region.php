<?php

namespace Netbaseteam\Calculatorshipping\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class Region extends \Magento\Framework\App\Action\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;
    protected $_regionColFactory;
    protected $_resultJsonFactory;
	
	public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory    $resultJsonFactory,
        \Magento\Directory\Model\RegionFactory $regionColFactory,
        PageFactory $resultPageFactory
    ) {        
        $this->_regionColFactory     = $regionColFactory;
        $this->_resultJsonFactory    = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $result  = $this->_resultJsonFactory->create();
        $regions = $this->_regionColFactory->create()->getCollection()
				->addFieldToFilter('country_id', $this->getRequest()->getParam('country')
				);
        return $result->setData(['success' => true,'value'=>$regions->getData()]);

    }
}
