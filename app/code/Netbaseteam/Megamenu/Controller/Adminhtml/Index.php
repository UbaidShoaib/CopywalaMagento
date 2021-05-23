<?php
/**
 * Netbaseteam.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the cmsmart.net license that is
 * available through the world-wide-web at this URL:
 * *
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Netbaseteam
 * @package     Netbaseteam_Megamenu
 * @copyright   Copyright (c) Netbaseteam (http://www.cmsmart.net/)
 *
 */

namespace Netbaseteam\Megamenu\Controller\Adminhtml;

/**
 * Action Megamenu
 */
abstract class Index extends \Magento\Backend\App\Action
{
    /**
     * @var \Netbaseteam\Megamenu\Model\ResourceModel\Megamenu\CollectionFactory
     */
    protected $_collectionFactory;
    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $_configResoure;
    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_typeListInterface;
    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $_cacheInterface;

    /**
     * Index constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\CacheInterface $cacheInterface
     * @param \Magento\Framework\App\Cache\TypeListInterface $typeListInterface
     * @param \Magento\Config\Model\ResourceModel\Config $configResoure
     * @param \Netbaseteam\Megamenu\Model\ResourceModel\Megamenu\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\CacheInterface $cacheInterface,
        \Magento\Framework\App\Cache\TypeListInterface $typeListInterface,
        \Magento\Config\Model\ResourceModel\Config $configResoure,
        \Netbaseteam\Megamenu\Model\ResourceModel\Megamenu\CollectionFactory $collectionFactory
    )
    {
        parent::__construct($context);
        $this->_cacheInterface = $cacheInterface;
        $this->_typeListInterface = $typeListInterface;
        $this->_configResoure = $configResoure;
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Netbaseteam_Megamenu::manage');
    }
}
