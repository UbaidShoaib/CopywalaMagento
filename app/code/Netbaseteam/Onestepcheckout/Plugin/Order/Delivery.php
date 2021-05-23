<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Plugin\Order;

use \Netbaseteam\Onestepcheckout\Helper\CheckoutData;

class Delivery
{

    /**
     * @var \Netbaseteam\Onestepcheckout\Helper\Onepage
     */
    protected $onepageHelper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Netbaseteam\Onestepcheckout\Helper\CheckoutData
     */
    protected $checkoutDataHelper;

    /**
     * Delivery constructor.
     * @param \Netbaseteam\Onestepcheckout\Helper\Onepage $onepageHelper
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Registry $registry
     * @param \Netbaseteam\Onestepcheckout\Helper\CheckoutData $registry
     * @internal param $ $
     */
    public function __construct(
        \Netbaseteam\Onestepcheckout\Helper\Onepage $onepageHelper,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Registry $registry,
        CheckoutData $checkoutDataHelper
    )
    {
        $this->onepageHelper = $onepageHelper;
        $this->request = $request;
        $this->registry = $registry;
        $this->checkoutDataHelper = $checkoutDataHelper;
    }

    /**
     * @param \Magento\Sales\Block\Items\AbstractItems $subject
     * @param $result
     * @return string
     */
    public function afterToHtml(
        \Magento\Sales\Block\Items\AbstractItems $subject, $result
    ) {
        foreach ($subject->getLayout()->getUpdate()->getHandles() as $handle) {
            if (substr($handle, 0, 12) === 'sales_email_') {
                if ($subject->getOrder() && $subject->getOrder()->getId()) {
                    $deliveryBlock = $subject->getLayout()
                        ->createBlock(
                            'Netbaseteam\Onestepcheckout\Block\Sales\Order\Email\Delivery',
                            'amcheckout.delivery',
                            [
                                'data' => [
                                    'order_id' => $subject->getOrder()->getId()
                                ]
                            ]
                        );

                    $result = $deliveryBlock->toHtml() . $result;
                }

                $additionalData = $this->onepageHelper->getAdditionalOptions();
                if (array_key_exists('comment', $additionalData)
                    && $additionalData['comment']
                    && $subject->getOrder()
                ) {
                    $comment = $this->registry
                        ->registry(CheckoutData::COMMENT_REGISTRY_KEY_NAME);
                    $this->checkoutDataHelper->addComment($comment, $subject->getOrder());
                    $commentsBlock = $subject->getLayout()
                        ->createBlock(
                            'Netbaseteam\Onestepcheckout\Block\Sales\Order\Email\Comments',
                            'amcheckout.comments',
                            [
                                'data' => [
                                    'order' => $subject->getOrder()
                                ]
                            ]
                        );

                    if (!$this->checkoutDataHelper->isCommentSet()) {
                        $this->registry->register(CheckoutData::COMMENT_REGISTRY_KEY_NAME . '_seted', true);
                    }

                    $result = $commentsBlock->toHtml() . $result;
                }
            }
        }

        return $result;
    }
}
