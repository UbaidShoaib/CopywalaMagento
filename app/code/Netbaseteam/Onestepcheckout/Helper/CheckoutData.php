<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;

class CheckoutData extends AbstractHelper
{

    const COMMENT_REGISTRY_KEY_NAME = 'amcheckout_comment';

    /**
     * @var \Netbaseteam\Onestepcheckout\Model\Gift\Messages
     */
    protected $giftMessages;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;
    /**
     * @var \Netbaseteam\Onestepcheckout\Model\Subscription
     */
    protected $subscription;
    /**
     * @var \Netbaseteam\Onestepcheckout\Model\FeeRepository
     */
    protected $feeRepository;
    /**
     * @var \Netbaseteam\Onestepcheckout\Model\ResourceModel\Delivery
     */
    protected $deliveryResource;
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;
    /**
     * @var \Netbaseteam\Onestepcheckout\Model\Delivery
     */
    protected $delivery;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Netbaseteam\Onestepcheckout\Model\Account
     */
    protected $account;

    public function __construct(
        Context $context,
        \Netbaseteam\Onestepcheckout\Model\Gift\Messages $giftMessages,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Netbaseteam\Onestepcheckout\Model\Subscription $subscription,
        \Netbaseteam\Onestepcheckout\Model\FeeRepository $feeRepository,
        \Netbaseteam\Onestepcheckout\Model\ResourceModel\Delivery $deliveryResource,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Netbaseteam\Onestepcheckout\Model\Delivery $delivery,
        \Magento\Framework\Registry $registry,
        \Netbaseteam\Onestepcheckout\Model\Account $account
    ) {
        parent::__construct($context);
        $this->giftMessages = $giftMessages;
        $this->checkoutSession = $checkoutSession;
        $this->subscription = $subscription;
        $this->feeRepository = $feeRepository;
        $this->deliveryResource = $deliveryResource;
        $this->orderFactory = $orderFactory;
        $this->delivery = $delivery;
        $this->registry = $registry;
        $this->account = $account;
    }

    public function beforePlaceOrder($amcheckout)
    {
        if (!$this->scopeConfig->isSetFlag('netbaseteam_onestepcheckout/general/enabled', ScopeInterface::SCOPE_STORE))
            return;

        if (isset($amcheckout['comment'])) {
            $this->registry->register(self::COMMENT_REGISTRY_KEY_NAME, $amcheckout['comment']);
        }

        if (!isset($amcheckout['gift_message']) || !$amcheckout['gift_message']) {
            $this->giftMessages->clearGiftMessages();
        }
    }

    public function afterPlaceOrder($amcheckout)
    {
        if (!$this->scopeConfig->isSetFlag('netbaseteam_onestepcheckout/general/enabled', ScopeInterface::SCOPE_STORE))
            return;
        
        $quoteId = $this->checkoutSession->getLastQuoteId();
        $orderId = $this->checkoutSession->getLastOrderId();

        if (isset($amcheckout['subscribe']) && $amcheckout['subscribe']) {
            $email = isset($amcheckout['email']) ? $amcheckout['email'] : null;
            $this->subscription->subscribe($email);
        }

        if (isset($amcheckout['comment']) && $amcheckout['comment'] && !$this->isCommentSet()) {
            $this->addComment($amcheckout['comment']);
        }
        if (isset($amcheckout['gift_wrap']) && $amcheckout['gift_wrap']) {
            $fee = $this->feeRepository->getByQuoteId($quoteId);
            if ($fee->getId()) {
                $fee->setOrderId($orderId);
                $this->feeRepository->save($fee);
            }
        }

        if (isset($amcheckout['register']) && $amcheckout['register']) {
            $this->account->create();
        }

        $delivery = $this->delivery->findByQuoteId($quoteId);

        if ($delivery->getId()) {
            $delivery->setData('order_id', $orderId);
            $this->deliveryResource->save($delivery);
        }
    }

    public function addComment($comment, $order = null)
    {
        if (!$order) {
            $lastOrderId = $this->checkoutSession->getLastOrderId();
            /** @var \Magento\Sales\Model\Order $order */
            $order = $this->orderFactory->create()->load($lastOrderId);
        }

        $history = $order->addStatusHistoryComment($comment);
        $history->setIsVisibleOnFront(true);
        $history->setIsCustomerNotified(true);
        $history->save();

        $order->save();
    }

    /**
     * @return mixed
     */
    public function isCommentSet()
    {
        return $this->registry->registry(self::COMMENT_REGISTRY_KEY_NAME . '_seted');
    }
}
