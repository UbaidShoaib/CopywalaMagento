<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Netbaseteam\PricingOption\Block\Product\View;

use Magento\Catalog\Model\Product;

/**
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 100.0.2
 */
class Options extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Product
     */
    protected $_product;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_registry = null;

    /**
     * Catalog product
     *
     * @var Product
     */
    protected $_catalogProduct;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogData;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Catalog\Helper\Image $helperImage,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistryInterface,
        \Netbaseteam\PricingOption\Helper\Data $helper,
        \Netbaseteam\PricingOption\Model\Option $pricingOption,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\Format $localeFormat,
        \Magento\Framework\Unserialize\Unserialize $unserialize,
        array $data = []
    ) {
        $this->pricingHelper = $pricingHelper;
        $this->_catalogData = $catalogData;
        $this->_helperImage = $helperImage;
        $this->_jsonEncoder = $jsonEncoder;
        $this->_registry = $registry;
        $this->_resourceConnection = $resourceConnection;
        $this->dateTime = $dateTime;
        $this->stockRegistryInterface = $stockRegistryInterface;
        $this->_helper = $helper;
        $this->pricingOption = $pricingOption;
        $this->_storeManager = $storeManager;
        $this->_localeFormat = $localeFormat;
        $this->unserialize = $unserialize;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve product object
     *
     * @return Product
     * @throws \LogicExceptions
     */
    public function getProduct()
    {
        if (!$this->_product) {
            if ($this->_registry->registry('current_product')) {
                $this->_product = $this->_registry->registry('current_product');
            } else {
                throw new \LogicException('Product is not defined');
            }
        }
        return $this->_product;
    }

    /**
     * Set product object
     *
     * @param Product $product
     * @return \Magento\Catalog\Block\Product\View\Options
     */
    public function setProduct(Product $product = null)
    {
        $this->_product = $product;
        return $this;
    }

    public function getOptions() {
        $product_id = $this->getProduct()->getId();
        $option_id = $this->pricingOption->getProductOption($product_id);
        $_options = array();
        $options = array();

        if($option_id){
            $_options = $this->pricingOption->getOption($option_id);
            if($_options){
                $options = $this->unserialize->unserialize($_options['fields']);
                if( !isset($options['fields']) ){
                    $options['fields'] = array();
                }
                $options['fields'] = $this->recursive_stripslashes( $options['fields'] );
                foreach ($options['fields'] as $key => $field){
                    if( !isset($field['general']['attributes']) ){
                        $field['general']['attributes'] = array();
                        $field['general']['attributes']['options'] = array();
                        $options['fields'][$key]['general']['attributes'] = array();
                        $options['fields'][$key]['general']['attributes']['options'] = array();
                    }
                    if($field['appearance']['change_image_product'] == 'y'){
                        foreach ($field['general']['attributes']['options'] as $op_index => $option ){
                            $option['product_image'] = isset($option['product_image']) ? $option['product_image'] : 0;
                            if( $option['product_image'] ){
                                $image_link = $option['product_image'];
                                $image_info = pathinfo($image_link);
                                $image_title = $image_info['filename'];
                                $image_file = $image_info['basename'];
                                $options['fields'][$key]['general']['attributes']['options'][$op_index] = array_replace_recursive($options['fields'][$key]['general']['attributes']['options'][$op_index], array(
                                    'imagep'    =>  'y',
                                    'image_link'    => $image_link,
                                    'image_title'   => $image_title,
                                    'image_file'   => $image_file,
                                    'image_alt'     => '',
                                    'image_caption' => ''
                                ));
                            }else{
                                $options['fields'][$key]['general']['attributes']['options'][$op_index]['imagep'] = 'n';
                            }
                        }
                    }
                    if( isset($field['nbpb_type']) && $field['nbpb_type'] == 'nbpb_com' ){
                        if( isset($field['general']['pb_config']) ){
                            foreach( $field['general']['pb_config'] as $a_index => $attr ){
                                foreach( $attr as $s_index => $sattr ){
                                    foreach( $sattr['views'] as $v_index => $view ){
                                        $pb_image_obj = $view['image'];
                                        $options['fields'][$key]['general']['pb_config'][$a_index][$s_index]['views'][$v_index]['image_url'] =  $pb_image_obj ? $pb_image_obj : $this->_helperImage->getDefaultPlaceholderUrl('image');
                                    }
                                }
                            }
                        }else{
                            $field['general']['pb_config'] = array();
                        }
                        foreach ($field['general']['attributes']['options'] as $op_index => $option ){
                            if( isset($option['enable_subattr']) && $option['enable_subattr'] == 'on' && isset($option['sub_attributes']) && count($option['sub_attributes']) > 0 ){
                                foreach( $option['sub_attributes'] as $sa_index => $sattr ){
                                    $options['fields'][$key]['general']['attributes']['options'][$op_index]['sub_attributes'][$sa_index]['image_url'] = $sattr['image'];
                                }
                            }else{
                                $options['fields'][$key]['general']['attributes']['options'][$op_index]['image_url'] = $option['image'];
                            }
                        };
                        $options['fields'][$key]['general']['component_icon_url'] = $field['general']['component_icon'];
                    }
                    if( isset($field['general']['attributes']['bg_type']) && $field['general']['attributes']['bg_type'] == 'i' ){
                        foreach ($field['general']['attributes']['options'] as $op_index => $option ){
                            foreach( $option['bg_image'] as $bg_index => $bg ){
                                $options['fields'][$key]['general']['attributes']['options'][$op_index]['bg_image_url'][$bg_index] = $bg;
                            }
                        };
                    }
                }
                if( isset($options['views']) ){
                    foreach ($options['views'] as $vkey => $view){
                        $options['views'][$vkey]['base_url'] = $view;
                    }
                }
            }
        }

        return $options;
    }

    protected function recursive_stripslashes( $fields ){
        $valid_fields = array();
        foreach($fields as $key => $field){
            if(is_array($field) ){
                $valid_fields[$key] = $this->recursive_stripslashes($field);
            }else if(!is_null($field)){
                $valid_fields[$key] = stripslashes($field);
            }
        }
        return $valid_fields;
    }

    public function getMaximumQtyAllowed() {
        $product = $this->getProduct();
        $productStockObj = $this->stockRegistryInterface->getStockItem($product->getId());
        return $productStockObj->getMaxSaleQty();
    }

    public function nbd_js_object(){
        $priceFormat = $this->_localeFormat->getPriceFormat();
        $args = array();
        $args['wc_currency_format_num_decimals'] = $priceFormat['precision'];
        $args['currency_format_num_decimals'] = $priceFormat['precision'];
        $args['currency_format_symbol'] = $this ->_storeManager-> getStore()->getBaseCurrency()->getCurrencySymbol();
        $args['currency_format_decimal_sep'] = $priceFormat['decimalSymbol'];
        $args['currency_format_thousand_sep'] = $priceFormat['groupSymbol'];
        $args['currency_format'] = $priceFormat['pattern'];
        $args['nbdesigner_hide_add_cart_until_form_filled'] = $this->_helper->getNbdesignerOption('pricingoption/general/nbdesigner_hide_add_cart_until_form_filled');
        $args['total'] = __('Total');
        $args['check_invalid_fields'] = __('Please check invalid fields and quantity input!');
        $args['ajax_cart'] = 'no';
        $args['nbo_qv_url'] = '';
        return $args;
    }
}
