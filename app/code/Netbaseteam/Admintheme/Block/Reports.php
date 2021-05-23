<?php
namespace Netbaseteam\Admintheme\Block;

class Reports extends \Magento\Framework\View\Element\Template
{
	/**
     * @var \Magento\Reports\Model\ResourceModel\Quote\CollectionFactory
     */
    protected $quoteItemCollectionFactory;

    /**
    * @var \Magento\Quote\Model\QueryResolver
    */
    protected $queryResolver;

    /**
     * @var \Magento\Reports\Model\ResourceModel\Product\Lowstock\CollectionFactory
     */
    protected $_lowstocksFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $_customerCollectionFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Report\Refunded\Collection\Refunded
     */
    protected $_refundCollection;


    protected $_currentCurrencyCode;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Visitor
     */

    protected $_vistorCollectionFactory;

	public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Quote\Model\QueryResolver $queryResolver,
        \Magento\Reports\Model\ResourceModel\Quote\Item\CollectionFactory $quoteItemCollectionFactory,
        \Magento\Reports\Model\ResourceModel\Product\Lowstock\CollectionFactory $lowstocksFactory,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Report\Refunded\Collection\Refunded $refundCollection,
        \Magento\Customer\Model\ResourceModel\Visitor\CollectionFactory $visitorCollectionFactory,

        array $data = []
    ){
        $this->quoteItemCollectionFactory = $quoteItemCollectionFactory;
        $this->queryResolver = $queryResolver;
        $this->_lowstocksFactory = $lowstocksFactory;
        $this->_customerCollectionFactory = $customerCollectionFactory;
        $this->_refundCollection = $refundCollection;
        $this->_vistorCollectionFactory = $visitorCollectionFactory;
        parent::__construct($context, $data);
    }

    public function getCurrency()
    {
        if ($this->_currentCurrencyCode === null) {
            if ($this->getRequest()->getParam('store')) {
                $this->_currentCurrencyCode = $this->_storeManager->getStore(
                    $this->getRequest()->getParam('store')
                )->getCurrencySymbol();
            } elseif ($this->getRequest()->getParam('website')) {
                $this->_currentCurrencyCode = $this->_storeManager->getWebsite(
                    $this->getRequest()->getParam('website')
                )->getCurrencySymbol();
            } elseif ($this->getRequest()->getParam('group')) {
                $this->_currentCurrencyCode = $this->_storeManager->getGroup(
                    $this->getRequest()->getParam('group')
                )->getWebsite()->getCurrencySymbol();
            } else {
                $this->_currentCurrencyCode = $this->_storeManager->getStore()->getCurrencySymbol();
            }
        }

        return $this->_currentCurrencyCode;
    }
    
    public function getCountProductInCart(){
    	$collection = $this->quoteItemCollectionFactory->create();
        $collection->getSelect()->group('sku');
        $_count = 0;
        foreach ($collection as $value) {
            $_count += $value['qty'];
        }
        return $_count;
    }

    public function getCountProductLowStock(){
        $website = $this->getRequest()->getParam('website');
        $group = $this->getRequest()->getParam('group');
        $store = $this->getRequest()->getParam('store');

        if ($website) {
            $storeIds = $this->_storeManager->getWebsite($website)->getStoreIds();
            $storeId = array_pop($storeIds);
        } elseif ($group) {
            $storeIds = $this->_storeManager->getGroup($group)->getStoreIds();
            $storeId = array_pop($storeIds);
        } elseif ($store) {
            $storeId = (int)$store;
        } else {
            $storeId = '';
        }

        /** @var $collection \Magento\Reports\Model\ResourceModel\Product\Lowstock\Collection  */
        $collection = $this->_lowstocksFactory->create()->addAttributeToSelect(
            '*'
        )->setStoreId(
            $storeId
        )->filterByIsQtyProductTypes()->joinInventoryItem(
            'qty'
        )->useManageStockFilter(
            $storeId
        )->useNotifyStockQtyFilter(
            $storeId
        )->setOrder(
            'qty',
            \Magento\Framework\Data\Collection::SORT_ORDER_ASC
        );

        if ($storeId) {
            $collection->addStoreFilter($storeId);
        }

        return count($collection);
    }
    public function getAllCustomer(){
        $collection = $this->_customerCollectionFactory->create();
        
        return count($collection);
    }

    public function getRefundTotal(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
        $collection = $objectManager->create('\Magento\Sales\Model\ResourceModel\Report\Refunded\Collection\Refunded');
        $collection->load();
        
        $_total = 0;
        foreach ($collection as $value) {
            $_total += $value['refunded'];
        }
        return $_total;        
    }

    public function getVisitor(){
        $collection = $this->_vistorCollectionFactory->create();
        
        for ($i = 1; $i <= 12; $i++) {
            $j = $i-1;
            $months[] = date("Y-m", strtotime( date( 'Y-m-01' )." -$j months"));
        }
        $months = array_reverse($months);
        $datas = array();

        foreach($months as $month){
            $i = 0;
            $datas[$month] = $i;
            foreach($collection as $value){
                $last_visit_at = date("Y-m", strtotime($value->getLastVisitAt()));
                if($last_visit_at == $month){
                    $i++;
                    $datas[$month] = $i;
                }
            }
        }
        return $datas;
    }
}