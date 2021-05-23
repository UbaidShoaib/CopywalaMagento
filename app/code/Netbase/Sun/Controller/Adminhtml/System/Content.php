<?php
/**
 * @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbase\Sun\Controller\Adminhtml\System;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Content extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Netbase_Sun::design_contents';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    { 
         return $this->resultPageFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}
