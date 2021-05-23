<?php

namespace Netbaseteam\PricingOption\Observer\Checkout;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class SalesModelServiceQuoteSubmitBeforeObserver implements ObserverInterface
{
    private $quoteItems = [];
    private $quote = null;
    private $order = null;
    protected $_jsonHelper;
    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\LayoutInterface $layout
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Netbaseteam\PricingOption\Helper\Data $helper,
        \Netbaseteam\PricingOption\Model\Option $pricingOption
    ){
        $this->_jsonHelper = $jsonHelper;
        $this->_helper = $helper;
        $this->pricingOption = $pricingOption;
    }


    public function execute(EventObserver $observer)
    {
        $this->quote = $observer->getQuote();
        $this->order = $observer->getOrder();
        /* @var  \Magento\Sales\Model\Order\Item $orderItem */
        foreach($this->order->getItems() as $orderItem)
        {
            if ($orderItem->getProductOptions()) {
                $additionalOptions = $orderItem->getProductOptions();
                if (!empty($additionalOptions['info_buyRequest'])) {
                    $buyInfo = $additionalOptions['info_buyRequest'];
                    if (!empty($buyInfo['nbd-field'])) {
                        $option_id = $this->pricingOption->getProductOption($orderItem->getProductId());
                        if( !$option_id ){
                            return;
                        }

                        $options = $this->pricingOption->getOption($option_id);

                        $nbd_field_original = isset($buyInfo['nbd-field']) ? $buyInfo['nbd-field'] : array();
                        $nbd_field_clone = isset($buyInfo['nbd-field-clone']) ? $buyInfo['nbd-field-clone'] : array();
                        $nbd_field = array_merge($nbd_field_original,$nbd_field_clone);

                        $original_price = $orderItem->getOriginalPrice();
                        $quantity = $orderItem->getQtyOrdered();
                        $option_price = $this->_helper->option_processing( $options, $original_price, $nbd_field, $quantity );
                        $fields = $option_price['fields'];

                        foreach($fields as $key => $val){
                            $additionalOptions['additional_options'][] = array(
                                'label'  =>  $val['name'],
                                'value'   =>  $val['value_name'],
                                'price' => $val['price'],
                                'is_upload' => isset($val['is_upload']) ? $val['is_upload'] : 0
                            );
                        }

                        $orderItem->setProductOptions($additionalOptions);
                    }
                }
                   
            }
        }
    }
}