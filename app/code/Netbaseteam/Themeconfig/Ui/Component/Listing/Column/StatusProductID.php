<?php

namespace Netbaseteam\Themeconfig\Ui\Component\Listing\Column;

use \Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Ui\Component\Listing\Columns\Column;
use \Magento\Framework\Api\SearchCriteriaBuilder;

class StatusProductID extends Column
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $_orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $_searchCriteria;

    protected $_helper;

    /**
     * StatusID constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param SearchCriteriaBuilder $criteria
     * @param array $components
     * @param array $data
     */
    public function __construct(ContextInterface $context, UiComponentFactory $uiComponentFactory, SearchCriteriaBuilder $criteria, \Netbaseteam\Themeconfig\Helper\Data $helper, array $components = [], array $data = [])
    {
        $this->_searchCriteria  = $criteria;
        $this->_helper = $helper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare the status color data.
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/huan.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $result = $this->_helper->getStatusProduct();
                if (count($result)>0) {
                    if(isset($result[0]['value'])) {
                        $arr_status = json_decode($result[0]['value'], true);
                        if (count($arr_status)>0) {
                            $status = ($item['status']==1?'enabled':'disabled');
                            $item[$this->getData('name')] = ($arr_status[$status]) ? $arr_status[$status] : '';
                        }
                    }
                }
            }
        }
        return $dataSource;
    }

}