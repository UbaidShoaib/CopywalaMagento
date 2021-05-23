<?php

namespace Netbaseteam\Themeconfig\Block;

/**
 * Themeconfig content block
 */
class Themeconfig extends \Magento\Framework\View\Element\Template
{
    /**
     * Themeconfig collection
     *
     * @var Netbaseteam\Themeconfig\Model\ResourceModel\Themeconfig\Collection
     */
    protected $_themeconfigCollection = null;
    
    /**
     * Themeconfig factory
     *
     * @var \Netbaseteam\Themeconfig\Model\ThemeconfigFactory
     */
    protected $_themeconfigCollectionFactory;
    
    /** @var \Netbaseteam\Themeconfig\Helper\Data */
    protected $_dataHelper;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Netbaseteam\Themeconfig\Model\ResourceModel\Themeconfig\CollectionFactory $themeconfigCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Netbaseteam\Themeconfig\Model\ResourceModel\Themeconfig\CollectionFactory $themeconfigCollectionFactory,
        \Netbaseteam\Themeconfig\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_themeconfigCollectionFactory = $themeconfigCollectionFactory;
        $this->_dataHelper = $dataHelper;
        parent::__construct(
            $context,
            $data
        );
    }
    
    /**
     * Retrieve themeconfig collection
     *
     * @return Netbaseteam\Themeconfig\Model\ResourceModel\Themeconfig\Collection
     */
    protected function _getCollection()
    {
        $collection = $this->_themeconfigCollectionFactory->create();
        return $collection;
    }
    
    /**
     * Retrieve prepared themeconfig collection
     *
     * @return Netbaseteam\Themeconfig\Model\ResourceModel\Themeconfig\Collection
     */
    public function getCollection()
    {
        if (is_null($this->_themeconfigCollection)) {
            $this->_themeconfigCollection = $this->_getCollection();
            $this->_themeconfigCollection->setCurPage($this->getCurrentPage());
            $this->_themeconfigCollection->setPageSize($this->_dataHelper->getThemeconfigPerPage());
            $this->_themeconfigCollection->setOrder('published_at','asc');
        }

        return $this->_themeconfigCollection;
    }
    
    /**
     * Fetch the current page for the themeconfig list
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->getData('current_page') ? $this->getData('current_page') : 1;
    }
    
    /**
     * Return URL to item's view page
     *
     * @param Netbaseteam\Themeconfig\Model\Themeconfig $themeconfigItem
     * @return string
     */
    public function getItemUrl($themeconfigItem)
    {
        return $this->getUrl('*/*/view', array('id' => $themeconfigItem->getId()));
    }
    
    /**
     * Return URL for resized Themeconfig Item image
     *
     * @param Netbaseteam\Themeconfig\Model\Themeconfig $item
     * @param integer $width
     * @return string|false
     */
    public function getImageUrl($item, $width)
    {
        return $this->_dataHelper->resize($item, $width);
    }
    
    /**
     * Get a pager
     *
     * @return string|null
     */
    public function getPager()
    {
        $pager = $this->getChildBlock('themeconfig_list_pager');
        if ($pager instanceof \Magento\Framework\Object) {
            $themeconfigPerPage = $this->_dataHelper->getThemeconfigPerPage();

            $pager->setAvailableLimit([$themeconfigPerPage => $themeconfigPerPage]);
            $pager->setTotalNum($this->getCollection()->getSize());
            $pager->setCollection($this->getCollection());
            $pager->setShowPerPage(TRUE);
            $pager->setFrameLength(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setJump(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame_skip',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            );

            return $pager->toHtml();
        }

        return NULL;
    }
}
