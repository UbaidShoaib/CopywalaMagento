<?php
/**
 * @category   Netbaseteam
 * @package    Netbaseteam_Ajaxcart
 * @copyright  Copyright (c) 2018 Netbaseteam
 */

namespace Netbaseteam\Ajaxcart\Plugin\Checkout\Cart;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\Exception\NoSuchEntityException;
use Netbaseteam\Ajaxcart\Helper\Data as AjaxcartData;

class Add
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var \Netbaseteam\Ajaxcart\Helper\Data
     */
    protected $_ajaxcartData;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Wishlist\Model\ItemFactory
     */
    protected $_itemFactory;

    /**
     * @var \Magento\Wishlist\Controller\WishlistProviderInterface
     */
    protected $_wishlistProvider;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

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
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param CustomerCart $cart
     * @param AjaxcartData $ajaxcartData
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Wishlist\Model\ItemFactory $itemFactory
     * @param \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        CustomerCart $cart,
        AjaxcartData $ajaxcartData,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Wishlist\Model\ItemFactory $itemFactory,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\UrlInterface $url,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->productRepository = $productRepository;
        $this->_objectManager = $objectManager;
        $this->messageManager = $messageManager;
        $this->cart = $cart;
        $this->_ajaxcartData = $ajaxcartData;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_coreRegistry = $registry;
        $this->_itemFactory = $itemFactory;
        $this->_wishlistProvider = $wishlistProvider;
        $this->_checkoutSession = $checkoutSession;
        $this->_formKeyValidator = $formKeyValidator;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->_eventManager = $eventManager;
        $this->_redirect = $redirect;
        $this->_scopeConfig = $scopeConfig;
        $this->_url = $url;
        $this->_storeManager = $storeManager;
    }

    /**
     * Initialize product instance from request data
     *
     * @return \Magento\Catalog\Model\Product|false
     */
    protected function _initProduct($subject)
    {
        $productId = (int)$subject->getRequest()->getParam('product');
        if ($productId) {
            $storeId = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();
            try {
                return $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * Add product to shopping cart action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function aroundExecute(
        \Magento\Checkout\Controller\Cart\Add $subject
    )
    {
        $params = $subject->getRequest()->getParams();
        $product = $this->_initProduct($subject);

        if ($this->_ajaxcartData->getConfigEnabled() && isset($params['ajax'])) {
            $json_encode = array();
            if (isset($params['ajax']) && $params['ajax']) {
                try {
                    if (isset($params['qty'])) {
                        $filter = new \Zend_Filter_LocalizedToNormalized(
                            ['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
                        );
                        $params['qty'] = $filter->filter($params['qty']);
                    }
                    $related = $subject->getRequest()->getParam('related_product');
                    /**
                     * Check product availability
                     */
                    if (!$product) {
                        $json_encode["error"] = 1;
                        $json_encode["error_msg"] = __("The product is not available");
                        $result = $this->_resultJsonFactory->create();
                        return $result->setData($json_encode);
                    }

                    /* return options popup content when product type is grouped */

                    $showOptionsResponse = false;
                    switch ($product->getTypeId()) {
                        case 'configurable':
                            $showOptionsResponse = true;
                            break;
                        case 'grouped':
                            if (!array_key_exists('super_group', $params)) {
                                $showOptionsResponse = true;
                            }
                            break;
                        case 'bundle':
                            if (!array_key_exists('bundle_option', $params)) {
                                $showOptionsResponse = true;
                            }
                            break;
                        case 'downloadable':
                            if (!array_key_exists('links', $params)) {
                                $showOptionsResponse = true;
                            }
                            break;
                    }

                    if ($params['utype'] != "detail-add") {
                        if ($showOptionsResponse || ($product->isSaleable() && $product->getTypeInstance()->hasOptions($product))) {
                            $this->_coreRegistry->register('product', $product);
                            $this->_coreRegistry->register('current_product', $product);

                            $json_encode["popup_option"] = 1;

                            $htmlPopup = $this->_ajaxcartData->getPopupOptionHtml($product);
                            $json_encode['html_popup_option'] = $htmlPopup;


                            if (isset($params['item'])) {
                                $json_encode['item'] = $params['item'];
                            }

                            $result = $this->_resultJsonFactory->create();
                            return $result->setData($json_encode);
                        }
                    }

                    $this->cart->addProduct($product, $params);
                    if (!empty($related)) {

                        $this->cart->addProductsByIds(explode(',', $related));
                    }

                    $this->cart->save();


                    if (isset($params['item'])) {
                        $item = $this->_itemFactory->create()->load($params['item']);
                        $wishlist = $this->_wishlistProvider->getWishlist($item->getWishlistId());

                        $item->delete();
                        $wishlist->save();
                        $json_encode["item"] = $params['item'];

                    }

                    /**
                     * @todo remove wishlist observer \Magento\Wishlist\Observer\AddToCart
                     */

                    if (!$this->_checkoutSession->getNoCartRedirect(true)) {
                        if (!$this->cart->getQuote()->getHasError()) {
                            $message = __(
                                'You added %1 to your shopping cart.',
                                $product->getName()
                            );
                            $this->_coreRegistry->register('product', $product);
                            $this->_coreRegistry->register('current_product', $product);
                            $json_encode["success_msg"] = $message;
                            $json_encode["error"] = 0;
                            $htmlPopup = $this->_ajaxcartData->getSuccessHtml($product);
                            $json_encode['html_popup'] = $htmlPopup;
                        }
                    }
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    if ($this->_checkoutSession->getUseNotice(true)) {
                        $this->messageManager->addNotice(
                            $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($e->getMessage())
                        );
                        $json_encode["error_msg"] = $e->getMessage();
                    } else {
                        $messages = array_unique(explode("\n", $e->getMessage()));
                        foreach ($messages as $message) {
                            $json_encode["error_msg"] = $message;
                        }
                    }
                    $json_encode["error"] = 1;

                } catch (\Exception $e) {
                    $json_encode["error_msg"] = $e->getMessage();
                    $this->messageManager->addError(__($json_encode["error_msg"]));
                }
                $result = $this->_resultJsonFactory->create();
                return $result->setData($json_encode);
            }
        } else {
            if (!$this->_formKeyValidator->validate($subject->getRequest())) {
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
            try {
                if (isset($params['qty'])) {
                    $filter = new \Zend_Filter_LocalizedToNormalized(
                        ['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
                    );
                    $params['qty'] = $filter->filter($params['qty']);
                }
                $related = $subject->getRequest()->getParam('related_product');

                /**
                 * Check product availability
                 */
                if (!$product) {
                    return $this->goBack($subject);
                }
                $this->cart->addProduct($product, $params);
                if (!empty($related)) {
                    $this->cart->addProductsByIds(explode(',', $related));
                }
                $this->cart->save();

                /**
                 * @todo remove wishlist observer \Magento\Wishlist\Observer\AddToCart
                 */
                $this->_eventManager->dispatch(
                    'checkout_cart_add_product_complete',
                    ['product' => $product, 'request' => $subject->getRequest(), 'response' => $subject->getResponse()]
                );
                if (!$this->_checkoutSession->getNoCartRedirect(true)) {
                    if (!$this->cart->getQuote()->getHasError()) {
                        $message = __(
                            'You added %1 to your shopping cart.',
                            $product->getName()
                        );
                        $this->messageManager->addSuccessMessage($message);
                    }
                    return $this->goBack(null, $subject);
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                if ($this->_checkoutSession->getUseNotice(true)) {
                    $this->messageManager->addNotice(
                        $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($e->getMessage())
                    );
                } else {
                    $messages = array_unique(explode("\n", $e->getMessage()));
                    foreach ($messages as $message) {
                        $this->messageManager->addError(
                            $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($message)
                        );
                    }
                }

                $url = $this->_checkoutSession->getRedirectUrl(true);

                if (!$url) {
                    $cartUrl = $this->_objectManager->get('Magento\Checkout\Helper\Cart')->getCartUrl();
                    $url = $this->_redirect->getRedirectUrl($cartUrl);
                }

                return $this->goBack($url, $subject);

            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('We can\'t add this item to your shopping cart right now.'));
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                return $this->goBack($subject);
            }
        }
    }

    /**
     * @param null $backUrl
     * @param $subject
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function goBack($backUrl = null, $subject)
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($backUrl || $backUrl = $this->getBackUrl($this->_redirect->getRefererUrl(), $subject)) {
            $resultRedirect->setUrl($backUrl);
        }

        return $resultRedirect;
    }

    /**
     * Get resolved back url
     * @param null $defaultUrl
     * @return mixed|null|string
     */
    protected function getBackUrl($defaultUrl = null, $subject)
    {
        $returnUrl = $subject->getRequest()->getParam('return_url');
        if ($returnUrl && $this->_isInternalUrl($returnUrl)) {
            $this->messageManager->getMessages()->clear();
            return $returnUrl;
        }
        $shouldRedirectToCart = $this->_scopeConfig->getValue(
            'checkout/cart/redirect_to_cart',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($shouldRedirectToCart || $subject->getRequest()->getParam('in_cart')) {
            if ($subject->getRequest()->getActionName() == 'add' && !$subject->getRequest()->getParam('in_cart')) {
                $this->_checkoutSession->setContinueShoppingUrl($subject->_redirect->getRefererUrl());
            }
            return $this->_url->getUrl('checkout/cart');
        }
        return $defaultUrl;
    }

    /**
     * Check if URL corresponds store
     *
     * @param string $url
     * @return bool
     */
    protected function _isInternalUrl($url)
    {
        if (strpos($url, 'http') === false) {
            return false;
        }

        /**
         * Url must start from base secure or base unsecure url
         * @var $store \Magento\Store\Model\Store
         */
        $store = $this->_storeManager->getStore();
        $unsecure = strpos($url, $store->getBaseUrl()) === 0;
        $secure = strpos($url, $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK, true)) === 0;
        return $unsecure || $secure;
    }
}
