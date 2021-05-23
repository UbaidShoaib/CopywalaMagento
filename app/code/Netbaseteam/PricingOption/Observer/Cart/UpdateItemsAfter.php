<?php

namespace Netbaseteam\PricingOption\Observer\Cart;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Cart;


class UpdateItemsAfter implements ObserverInterface
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    protected $_request;
    protected $_jsonHelper;
    protected $_matrixtableFactory;


    protected $cart;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\LayoutInterface $layout
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\App\RequestInterface $request,
        Cart $cart,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Netbaseteam\PricingOption\Helper\Data $helper,
        \Netbaseteam\PricingOption\Model\Option $pricingOption,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    )
    {
        $this->_layout = $layout;
        $this->_storeManager = $storeManager;
        $this->_request = $request;
        $this->_jsonHelper = $jsonHelper;
        $this->cart = $cart;
        $this->_helper = $helper;
        $this->pricingOption = $pricingOption;
        $this->serializer = $serializer;
    }

    /**
     * Add order information into GA block to render on checkout success pages
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {

        $infos = $observer->getEvent()->getData('info');
        $infoItems = $infos->getData();
        if (!empty($infoItems)) {
            foreach ($infoItems as $itemId => $info) {
                $this->isValidQtyPricingOption($itemId, $info['qty']);
            }
        }
    }


    protected function isValidQtyPricingOption($itemId, $itemQty)
    {
        $item = $this->cart->getQuote()->getItemById($itemId);
        $product = $item->getProduct();
        $option_id = $this->pricingOption->getProductOption($product->getId());
        if( !$option_id ){
            return true;
        }

        $post_data = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());

        $options = $this->pricingOption->getOption($option_id);
        $option_fields = unserialize($options['fields']);
        $nbd_field_original = isset($post_data['info_buyRequest']['nbd-field']) ? $post_data['info_buyRequest']['nbd-field'] : array();
        $nbd_field_clone = isset($post_data['info_buyRequest']['nbd-field-clone']) ? $post_data['info_buyRequest']['nbd-field-clone'] : array();
        $nbd_field = array_merge($nbd_field_original,$nbd_field_clone);

        $original_price = $product->getFinalPrice();
        $quantity = $itemQty;
        $option_price = $this->_helper->option_processing( $options, $original_price, $nbd_field, $quantity );
        $finalPrice = $original_price + $option_price['total_price'] - $option_price['discount_price'];

        if ($finalPrice) {
            $price = (float)$finalPrice;
            $item->setCustomPrice($finalPrice);
            $item->setOriginalCustomPrice($finalPrice);
        }

        return true;
    }
}