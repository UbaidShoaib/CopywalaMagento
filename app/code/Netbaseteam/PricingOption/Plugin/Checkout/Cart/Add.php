<?php

namespace Netbaseteam\PricingOption\Plugin\Checkout\Cart;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;

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
        \Netbaseteam\PricingOption\Model\Option $pricingOption,
        \Magento\Framework\App\Response\Http $response,
        \Magento\Framework\App\RequestInterface $request
    )
    {
        $this->productRepository = $productRepository;
        $this->helper = $helper;
        $this->objectManager = $objectManager;
        $this->pricingOption = $pricingOption;
        $this->_response = $response;
        $this->request = $request;
    }

    /**
     * Initialize product instance from request data
     *
     * @return \Magento\Catalog\Model\Product|false
     */
    protected function _initProduct()
    {
        $productId = (int)$this->request->getParam('product');
        if ($productId) {
            $storeId = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();
            try {
                return $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * @param \Magento\Checkout\Controller\Cart\Add $subject
     * @return mixed
     */
    public function beforeAddProduct(
        \Magento\Checkout\Model\Cart $subject, $productInfo, $requestInfo = null
    )
    {
        if (!$this->request->getParam('nbo-add-to-cart')) {
            $product = $this->_initProduct();
            /**
             * Check product availability
             */
            if ($product) {
                $option_id = $this->pricingOption->getProductOption($product->getId());
                if ($option_id) {
                    throw new LocalizedException(__('You need to choose options for your item.'));
                    return [$productInfo, $requestInfo];
                }
            }
        }
    }
}
