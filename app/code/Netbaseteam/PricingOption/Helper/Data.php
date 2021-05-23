<?php
/**
 * @copyright Copyright (c) 2019 www.cmsmart.net
 */

namespace Netbaseteam\PricingOption\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const MEDIA_PATH = 'nbdesigner';
    const MEDIA_UPLOAD_PATH = 'nbdesigner/uploads';
    
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    protected $storeManager;
    /**
     * @param \Magento\Framework\App\Helper\Context   $context
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Filesystem\DirectoryList $directory_list,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Unserialize\Unserialize $unserialize,
        \Magento\Framework\View\Asset\Repository $assetRepo
    ) {
        parent::__construct($context);
        $this->_backendUrl = $backendUrl;
        $this->storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_directory_list = $directory_list;
        $this->filesystem = $filesystem;
        $this->unserialize = $unserialize;
        $this->_assetRepo = $assetRepo;
    }
    /**
     * get products tab Url in admin
     * @return string
     */
    public function getProductsGridUrl()
    {
        return $this->_backendUrl->getUrl('pricingoption/option/products', ['_current' => true]);
    }

    public function getBaseUploadDir()
    {
        $path = $this->filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath(self::MEDIA_UPLOAD_PATH);
        return $path;
    }

    public function getBaseUrlUpload()
    {
        return $this->storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ) . '/' . self::MEDIA_UPLOAD_PATH;
    }

    public function getNbdesignerOption($key) {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        return $this->scopeConfig->getValue($key, $storeScope);
    }

    public function get_field_by_id( $option_fields, $field_id ){
        foreach($option_fields['fields'] as $key => $field){
            if( $field['id'] == $field_id ) return $field;
        }
    }

    public function recursive_stripslashes( $fields ){
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
    public function option_processing( $options, $original_price, $fields, $quantity, $cart_item_key = null ){
        $option_fields = $this->unserialize->unserialize($options['fields']);
        $option_fields = $this->recursive_stripslashes( $option_fields );
        $quantity_break = $this->get_quantity_break( $option_fields, $quantity );
        $xfactor = 1;
        $total_price = 0;
        $discount_price = 0;
        $_fields = array();
        $cart_image = '';
        $cart_item_fee = 0;
        $line_price     = array(
            'fixed'     =>   0,
            'percent'   => 0,
            'xfactor'   => 1
        );
        $fixed_amount = 0;
        foreach($fields as $key => $val){
            $origin_field = $this->get_field_by_id( $option_fields, $key );
            $published    = isset( $origin_field['general']['published'] ) ? $origin_field['general']['published'] : 'y';
            if( isset($origin_field['nbe_type']) && $origin_field['nbe_type'] == 'delivery' ){
                $turnaround_matrix = $this->build_turnaround_matrix( $option_fields, $origin_field );
                $position = $quantity_break['index'];
                if( $turnaround_matrix[ $position ][ $val ] == 0 ){
                    for ($i = 0; $i < count( $origin_field['general']['attributes']['options'] ); $i++) {
                        if( $turnaround_matrix[ $position ][ $i ] == 1 ){
                            $val = '' . $i;
                            break;
                        }
                    }
                }
            }
            $_fields[$key] = array(
                'name'  =>  $origin_field['general']['title'],
                'val'   =>  $val,
                'value_name'   =>  $val,
                'published'   =>  $published
            );
            if( $origin_field['general']['data_type'] == 'i' ){
                if($origin_field['general']['depend_quantity'] == 'n'){
                    $factor = $origin_field['general']['price'];
                }else{
                    $factor = $origin_field['general']['price_breaks'][$quantity_break['index']];
                }
                if( isset($origin_field['nbd_type']) && $origin_field['nbd_type'] == 'dimension' && $origin_field['general']['mesure'] == 'y' && isset($origin_field['general']['mesure_range']) && count($origin_field['general']['mesure_range']) > 0 ){
                    $dimension = explode("x",$val);
                    $factor = $this->calculate_price_base_measurement($origin_field, $dimension[0], $dimension[1]);
                    if( ($origin_field['general']['price_type'] == 'f' || $origin_field['general']['price_type'] == 'c') && $origin_field['general']['mesure_base_pages'] == 'y' ){
                        $no_page = 1;
                        foreach($fields as $_key => $_val){
                            $_origin_field = $this->get_field_by_id( $option_fields, $_key );
                            if( isset($_origin_field['nbd_type']) && ( $_origin_field['nbd_type'] == 'page' || $_origin_field['nbd_type'] == 'page1' ) ){
                                if( $_origin_field['general']['data_type'] == 'i' ){
                                    $no_page = $_val;
                                }else{
                                }
                            }
                        }
                        $factor *= floor( ($no_page + 1) / 2 );
                    }
                }
                if( $origin_field['general']['input_type'] == 'u' ){
                    $_fields[$key]['value_name']  = $val;
                    $_fields[$key]['is_upload']  = 1;
                }
            }else{
                $select_val = is_array($val) ? ( isset($val['value']) ? $val['value'] : $val[0] ) : $val;
                $option = $origin_field['general']['attributes']['options'][$select_val];
                $has_subattr = false;
                if( isset($option['enable_subattr']) && $option['enable_subattr'] == 'on' && isset($option['sub_attributes']) && count($option['sub_attributes']) > 0 ){
                    $has_subattr = true;
                }
                $_fields[$key]['value_name'] = $option['name']; 
                if(is_array($val)){
                    if( $has_subattr ){
                        $_fields[$key]['value_name'] .= ' - ' . $option['sub_attributes'][$val['sub_value']]['name'];
                    }else{
                        $_fields[$key]['value_name'] = '';
                        foreach($val as $k => $v){
                            $_fields[$key]['value_name'] .= ($k == 0 ? '' : ', ') . $origin_field['general']['attributes']['options'][$v]['name'];
                        }
                    }
                }
                if($origin_field['general']['depend_quantity'] == 'n'){
                    $factor = floatval( $option['price'][0] );
                }else{
                    $factor = floatval( $option['price'][$quantity_break['index']] );
                }
                if( $has_subattr ){
                    $soption = $option['sub_attributes'][$val['sub_value']];
                    if($origin_field['general']['depend_quantity'] == 'n'){
                        $factor += floatval( $soption['price'][0] );
                    }else{
                        $factor += floatval( $soption['price'][$quantity_break['index']] );
                    }
                }
                if($origin_field['appearance']['change_image_product'] == 'y' && isset($option['product_image']) && $option['product_image']){
                    $cart_image = $option['product_image'];
                }
            }
            $_fields[$key]['is_pp'] = 0;
            if( isset($origin_field['nbd_type']) && $origin_field['nbd_type'] == 'dimension' && $origin_field['general']['price_type'] == 'c' ){
                $origin_field['general']['price_type'] == 'f';
            }
            if( isset($origin_field['nbd_type']) && ( $origin_field['nbd_type'] == 'page' || $origin_field['nbd_type'] == 'page2' ) && $origin_field['general']['data_type'] == 'm' ){
                $factor = array();
                foreach($val as $k => $v){
                    $option = $origin_field['general']['attributes']['options'][$v];
                    if($origin_field['general']['depend_quantity'] == 'n'){
                        $factor[$k] = $option['price'][0];
                    }else{
                        $factor[$k] = $option['price'][$quantity_break['index']];
                    }
                }
                $_fields[$key]['price'] = 0;
                $xfac = 0; $_xfac = 0;
                foreach($factor as $fac){
                    $fac = floatval($fac);
                    $_fac = $fac;
                    if( $this->is_independent_qty( $origin_field ) ){
                        $fac =  0;
                        $_fields[$key]['ind_qty'] = 1;
                    }
                    if( $this->is_fixed_amount( $origin_field ) ){
                        $fac /= $quantity;
                        $_fields[$key]['fixed_amount'] = 1;
                    }
                    switch ($origin_field['general']['price_type']){
                        case 'f':
                            $_fields[$key]['price'] += $_fac;
                            $total_price += $fac;
                            if( $this->is_independent_qty( $origin_field ) ){
                                $line_price['fixed'] += $_fac;
                            }
                            break;
                        case 'p':
                            $_fields[$key]['price'] += $original_price * $_fac / 100;
                            $total_price += $original_price * $fac / 100;
                            if( $this->is_independent_qty( $origin_field ) ){
                                $line_price['percent'] += $_fac;
                            }
                            break;
                        case 'p+':
                            $_fields[$key]['price'] += $fac / 100;
                            $_fields[$key]['_price'] += $_fac / 100;
                            $_fields[$key]['is_pp'] = 1;
                            $xfac += $fac / 100;
                            $_xfac += $_fac / 100;
                            break;
                    }
                }
                if( $origin_field['general']['price_type'] == 'p+' ){
                    $xfactor *= (1 + $xfac / 100);
                    if( $this->is_independent_qty( $origin_field ) ){
                        $line_price['xfactor'] *= (1 + $_xfac / 100);
                    }
                }
            }else{
                $factor = floatval($factor);
                $_factor = $factor;
                if( $this->is_independent_qty( $origin_field ) ){
                    $factor = 0;
                    $_fields[$key]['ind_qty'] = 1;
                }
                if( $this->is_fixed_amount( $origin_field ) ){
                    $factor /= $quantity;
                    $_fields[$key]['fixed_amount'] = 1;
                }
                switch ($origin_field['general']['price_type']){
                    case 'f':
                        $_fields[$key]['price'] = $_factor;
                        $total_price += $factor;
                        if( $this->is_independent_qty( $origin_field ) ){
                            $line_price['fixed'] += $_factor;
                        }
                        break;
                    case 'p':
                        $_fields[$key]['price'] = $original_price * $_factor / 100;
                        $total_price += $original_price * $factor / 100;
                        if( $this->is_independent_qty( $origin_field ) ){
                            $line_price['percent'] += $_factor;
                        }
                        break;
                    case 'p+':
                        $_fields[$key]['price'] = $factor / 100;
                        $_fields[$key]['_price'] = $_factor / 100;
                        $_fields[$key]['is_pp'] = 1;
                        $xfactor *= (1 + $factor / 100);
                        if( $this->is_independent_qty( $origin_field ) ){
                            $line_price['xfactor'] *= (1 + $_factor / 100);
                        }
                        break;
                    case 'c':
                        $_fields[$key]['price'] = $_factor * abs( intval( $val ));
                        $total_price += $factor * abs( intval( $val ));
                        if( $this->is_independent_qty( $origin_field ) ){
                            $line_price['fixed'] += $_factor * abs( intval( $val ));
                        }
                        break;
                    case 'cp':
                        $_fields[$key]['price'] = $_factor * abs( intval( strlen( $val ) ));
                        $total_price += $factor * abs( intval( strlen( $val ) ));
                        if( $this->is_independent_qty( $origin_field ) ){
                            $line_price['fixed'] += $_factor * abs( intval( strlen( $val ) ));
                        }
                        break;
                }
            }
        }
        $total_price += ( ($original_price + $total_price ) * ($xfactor - 1 ) );
        foreach($_fields as $key => $val){
            if( $_fields[$key]['is_pp'] == 1 ) {
                $_fields[$key]['price'] = $_fields[$key]['price'] * ($original_price + $total_price ) / ( $_fields[$key]['price'] + 1 );
            }
        }
        if( $quantity_break['index'] == 0 && $quantity_break['oparator'] == 'lt' ){
            $qty_factor = '';
        }else{
            $qty_factor = $option_fields['quantity_breaks'][$quantity_break['index']]['dis'];
        }
        $qty_factor = floatval($qty_factor);
        $discount_price = $option_fields['quantity_discount_type'] == 'f' ? $qty_factor : ($original_price + $total_price ) * $qty_factor / 100;
        $total_cart_price = ( $original_price + $total_price - $discount_price ) * $quantity;
        $cart_item_fee = array(
            'value'   => 0,
            'name'    => '',
            'id'      => '',
            'fields'  => array()
        );
        if( $line_price['fixed'] != 0 || $line_price['xfactor'] != 1 || $line_price['percent'] != 0 ){
            $_total_cart_price = $total_cart_price;
            if( $line_price['fixed'] != 0 ){
                $total_cart_price += $line_price['fixed'];
            }
            if( $line_price['percent'] != 0 ){
                $total_cart_price += ($original_price * $line_price['percent'] / 100);
            }
            if( $line_price['xfactor'] != 1 ){
                $total_cart_price += ( $total_cart_price * ( $line_price['xfactor'] - 1 ) );
                foreach($_fields as $key => $val){
                    if( $val['is_pp'] == 1 && isset($val['ind_qty']) && $val['ind_qty'] == 1  ) {
                        $_fields[$key]['price'] = $val['_price'] * $total_cart_price / ( $val['_price'] + 1 );
                    }
                }
            }
            foreach($_fields as $key => $val){
                if( isset($val['ind_qty']) && $val['ind_qty'] == 1 ){
                    $cart_item_fee['name'] .= $val['name'] . ', ';
                    $cart_item_fee['fields'][] = array(
                        'name'  =>  $val['name'] . ': ' . $val['value_name'],
                        'price' =>  $val['price']
                    );
                }
            }
            if( $cart_item_fee['name'] != '' ){
                $cart_item_fee['name'] = substr($cart_item_fee['name'], 0, strlen($cart_item_fee['name']) - 2);
            }
            $cart_item_fee['value'] = $total_cart_price - $_total_cart_price;
        }
        return array(
            'total_price' =>  $total_price,
            'cart_item_fee' =>  $cart_item_fee,
            'discount_price' =>  $discount_price,
            'fields'    => $_fields,
            'cart_image'    =>  $cart_image
        );
    }
    public function build_turnaround_matrix( $options, $field ){
        $turnaround_matrix = array();
        $quantity_breaks = array();
        foreach( $options['quantity_breaks'] as $break ){
            $quantity_breaks[] = abs( intval($break['val']));
        }
        foreach( $quantity_breaks as $key => $break ){
            $turnaround_matrix[ $key ] = array();
            $qty = abs( intval( $break ));
            foreach( $field['general']['attributes']['options'] as $k => $option ){
                $active  = 0;
                $max_qty = abs( intval( $option['max_qty'] ));
                if( $option['max_qty'] == '' || $max_qty >= $qty ) $active = 1;
                $turnaround_matrix[ $key ][ $k ] = $active;
            }
        }
        return $turnaround_matrix;
    }
    public function is_independent_qty( $field ){
        if( isset( $field['general']['depend_qty'] ) && $field['general']['depend_qty'] == 'n' ){
            return true;
        }else{
            return false;
        }
    }
    public function is_fixed_amount( $field ){
        if( isset( $field['general']['depend_qty'] ) && $field['general']['depend_qty'] == 'n2' ){
            return true;
        }else{
            return false;
        }
    }
    public function calculate_price_base_measurement( $origin_field, $width, $height){
        $mesure_range = $origin_field['general']['mesure_range'];
        $area = floatval($width) * floatval($height);
        $price_per_unit = $start_range = $end_range = $price_range = 0;
        foreach($mesure_range as $key => $range){
            $start_range = floatval($range[0]);
            $end_range = floatval($range[1]);
            $price_range = floatval($range[2]);
            if( $start_range <= $area && ( $area <= $end_range || $end_range == 0 ) ){
                $price_per_unit = $price_range;
            }
            if( $start_range <= $area && $key == ( count($mesure_range) - 1 ) && $area > $end_range  ){
                $price_per_unit = $price_range;
            }
        }
        if( isset( $origin_field['general']['mesure_type'] ) && $origin_field['general']['mesure_type'] == 'r' ) return $price_per_unit;
        return $area * $price_per_unit;
    }
    public function get_quantity_break( $options, $quantity ){
        $quantity_break = array('index' =>  0, 'oparator' => 'gt');
        $quantity_breaks = array();
        foreach( $options['quantity_breaks'] as $break ){
            $quantity_breaks[] = abs(intval($break['val'] ? $break['val'] : 1));
        }
        foreach($quantity_breaks as $key => $break){
            if( $key == 0 && $quantity < $break){
                $quantity_break = array('index' =>  0, 'oparator' => 'lt');
            }
            if( $quantity >= $break && $key < ( count( $quantity_breaks ) - 1 ) ){
                $quantity_break = array('index' =>  $key, 'oparator' => 'bw');
            }
            if( $key == ( count( $quantity_breaks ) - 1 ) && $quantity >= $break){
                $quantity_break = array('index' =>  $key, 'oparator' => 'gt');
            }               
        }
        return $quantity_break;
    }

    public function is_mobile() {
        if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
            $is_mobile = false;
        } elseif ( strpos( $_SERVER['HTTP_USER_AGENT'], 'Mobile' ) !== false // many mobile devices (all iPhone, iPad, etc.)
            || strpos( $_SERVER['HTTP_USER_AGENT'], 'Android' ) !== false
            || strpos( $_SERVER['HTTP_USER_AGENT'], 'Silk/' ) !== false
            || strpos( $_SERVER['HTTP_USER_AGENT'], 'Kindle' ) !== false
            || strpos( $_SERVER['HTTP_USER_AGENT'], 'BlackBerry' ) !== false
            || strpos( $_SERVER['HTTP_USER_AGENT'], 'Opera Mini' ) !== false
            || strpos( $_SERVER['HTTP_USER_AGENT'], 'Opera Mobi' ) !== false ) {
                $is_mobile = true;
        } else {
            $is_mobile = false;
        }

        /**
         * Filters whether the request should be treated as coming from a mobile device or not.
         *
         * @since 4.9.0
         *
         * @param bool $is_mobile Whether the request is from a mobile device or not.
         */
        return $is_mobile;
    }

    protected function __checked_selected_helper( $helper, $current, $echo, $type ) {
        if ( (string) $helper === (string) $current )
            $result = " $type='$type'";
        else
            $result = '';

        if ( $echo )
            print_r($result);

        return $result;
    }

    public function checked( $checked, $current = true, $echo = true ) {
        return $this->__checked_selected_helper( $checked, $current, $echo, 'checked' );
    }

    public function getMainJsUrl()
    {
        $main_js_url = $this->_assetRepo->getUrl('Netbaseteam_PricingOption::js');
        return $main_js_url;
    }
}