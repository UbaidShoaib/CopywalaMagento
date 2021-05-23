<?php
namespace Netbaseteam\PricingOption\Observer\Checkout;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Exception\LocalizedException;

class CheckoutCartProductAddAfterObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;
    protected $_helper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    protected $_request;
    protected $_jsonHelper;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_mediaDirectory;

    /**
     * File Uploader factory.
     *
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\LayoutInterface $layout
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\LayoutInterface $layout,
        \Netbaseteam\PricingOption\Helper\Data $helper,
        \Netbaseteam\PricingOption\Model\Option $pricingOption,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Session\SessionManager $sessionManager,
        Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    )
    {
        $this->_helper = $helper;
        $this->pricingOption = $pricingOption;
        $this->_layout = $layout;
        $this->_storeManager = $storeManager;
        $this->_request = $request;
        $this->_jsonHelper = $jsonHelper;
        $this->_session = $sessionManager;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->messageManager = $messageManager;
        $this->objectManager = $objectManager;
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
        $item = $observer->getQuoteItem();
        $additionalOptions = [];
        if ($additionalOption = $item->getOptionByCode('additional_options')) {
            $additionalOptions = $this->serializer->unserialize($additionalOption->getValue());
        }

        if (count($additionalOptions) > 0) {
            $item->addOption([
                'code' => 'additional_options',
                'value' => $this->serializer->serialize($additionalOptions)
            ]);
        }

        $post_data = $this->_request->getPostValue();
        $option_id = $this->pricingOption->getProductOption($item->getProduct()->getId());
        if( !$option_id ){
            return;
        }

        if(!isset($post_data['product'])) {
            throw new LocalizedException(__('You need to choose options for your item.'));
            $observer->getControllerAction()->getResponse()->setRedirect($item->getProduct()->getProductUrl());
        }

        if( isset($post_data['nbd-field']) || isset($post_data['nbo-add-to-cart']) ){
            $options = $this->pricingOption->getOption($option_id);
            $option_fields = unserialize($options['fields']);
            $nbd_field = isset($post_data['nbd-field']) ? $post_data['nbd-field'] : array();

            if( !empty($this->_request->getFiles()) && isset($this->_request->getFiles()["nbd-field"]) ) {
                foreach( $this->_request->getFiles()["nbd-field"] as $field_id => $file ){
                    if( !isset($nbd_field[$field_id]) ){
                        $nbd_upload_field = $this->upload_file( $this->_request->getFiles()["nbd-field"], $field_id, $post_data['nbd-field-clone']);
                        if( !empty($nbd_upload_field) ){
                            $nbd_field[$field_id] = $nbd_upload_field[$field_id];
                        }
                    }
                }
            }

            $product = $observer->getEvent()->getProduct();
            $original_price = $product->getFinalPrice();
            $quantity = $post_data['qty'];
            $option_price = $this->_helper->option_processing( $options, $original_price, $nbd_field, $quantity );
            $finalPrice = $original_price + $option_price['total_price'] - $option_price['discount_price'];
            $fields = $option_price['fields'];

            if (count($fields)) {
                foreach($fields as $value)
                {
                    $additionalOptions[] = [
                        'label' => $value['name'],
                        'value' => $value['value_name'],
                        'price' => $value['price'],
                        'is_upload' => isset($value['is_upload']) ? $value['is_upload'] : 0
                    ];
                }
            }

            if(count($additionalOptions) > 0)
            {
                if($item->getProductType() == 'configurable'){
                    $item->addOption([
                        'product_id' => $item->getProductId(),
                        'code' => 'additional_options',
                        'value' => json_encode($additionalOptions)
                    ]);

                } else {
                    $item->addOption([
                        'product_id' => $item->getProductId(),
                        'code' => 'additional_options',
                        'value' => json_encode($additionalOptions)
                    ]);

                }
            }

            $price = (float)$finalPrice;
            $item->setCustomPrice($price);
            $item->setOriginalCustomPrice($price);
        }
    }

    public function upload_file( $files, $field_id, $post_data){
        $nbd_upload_fields = array();
        if( $files[$field_id]['error'] == 0 ){
            $new_name = $post_data[$field_id];

            $target = $this->_mediaDirectory->getAbsolutePath('nbdesigner/uploads/pricing_options');
            try {
                $fileId = 'nbd-field[' .$field_id . ']';
                /** @var $uploader \Magento\MediaStorage\Model\File\Uploader */
                $uploader = $this->_fileUploaderFactory->create(
                    ['fileId' => $fileId]
                );
                $uploader->setAllowCreateFolders(true);
                $result = $uploader->save($target, $new_name);
                if ($result['file']) {
                    $nbd_upload_fields[$field_id] = $new_name;
                }
            } catch (\Exception $e) {
            }
        }
        return $nbd_upload_fields;
    }

}