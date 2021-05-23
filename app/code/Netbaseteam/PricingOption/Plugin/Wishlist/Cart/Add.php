<?php

namespace Netbaseteam\PricingOption\Plugin\Wishlist\Cart;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Add
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager = null;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $_redirect;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param ProductRepositoryInterface $productRepository
     * @param $helper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        ProductRepositoryInterface $productRepository,
        \Netbaseteam\PricingOption\Helper\Data $helper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\UrlInterface $url,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Netbaseteam\PricingOption\Model\Option $pricingOption,
        \Magento\Wishlist\Model\ItemFactory $itemFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\App\Response\Http $response
    )
    {
        $this->productRepository = $productRepository;
        $this->helper = $helper;
        $this->objectManager = $objectManager;
        $this->messageManager = $messageManager;
        $this->_redirect = $redirect;
        $this->_scopeConfig = $scopeConfig;
        $this->_url = $url;
        $this->_checkoutSession = $checkoutSession;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->_storeManager = $storeManager;
        $this->pricingOption = $pricingOption;
        $this->itemFactory = $itemFactory;
        $this->_request = $request;
        $this->resultFactory = $resultFactory;
        $this->_response = $response;
    }

    /**
     * @param \Magento\Checkout\Controller\Cart\Add $subject
     * @return mixed
     */
    public function beforeExecute(
        \Magento\Wishlist\Controller\Index\Cart $subject
    )
    {
        $itemId = (int)$this->_request->getParam('item');
        /*
         * @var $item \Magento\Wishlist\Model\Item
         */
        $item = $this->itemFactory->create()->load($itemId);

        $product = $item->getProduct();
        /**
         * Check product availability
         */
        if ($product) {
            $option_id = $this->pricingOption->getProductOption($product->getId());
            if ($option_id) {
                $this->goBack($product->getProductUrl(), $product, $subject);
            }
        }
    }

    /**
     * Resolve response
     *
     * @param string $backUrl
     * @param \Magento\Catalog\Model\Product $product
     * @return $this|\Magento\Framework\Controller\Result\Redirect
     */
    protected function goBack($backUrl = null, $product = null, $subject)
    {
        $this->_response->setRedirect($backUrl);
    }
}
