<?php

namespace Netbaseteam\PricingOption\Block\Adminhtml\Option\Edit;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Fieldset element renderer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Renderer extends \Magento\Backend\Block\Template implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Framework\Unserialize\Unserialize $unserialize,
        array $data = []
    )
    {
        $this->_storeManager = $context->getStoreManager();
        $this->_objectManager = $objectmanager;
        $this->unserialize = $unserialize;
        parent::__construct($context, $data);
    }
    
    /**
     * @var AbstractElement
     */
    protected $_element;

    /**
     * @return AbstractElement
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $this->_element = $element;
        return $this->toHtml();
    }

    public function getRootCategory(){
        $store = $this->_storeManager->getStore();
        $categoryId = $store->getRootCategoryId();
        $category = $this->_objectManager->create('Magento\Catalog\Model\Category')->load($categoryId);
        return $category;
    }

    public function getTreeCategory($category, $parent, $ids = array(), $checkedCat){
        $rootCategoryId = $this->getRootCategory()->getId();
        $children = $category->getChildrenCategories();
        $childrenCount = count($children);
        $htmlLi = '<li lang="'.$category->getId().'">';
        $html[] = $htmlLi;
        $ids[] = $category->getId();
        $html[] = '<a id="node'.$category->getId().'">';

        $html[] = '<input lang="'.$category->getId().'" type="checkbox" id="radio'.$category->getId().'" name="product[category_ids][]" value="'.$category->getId().'" class="checkbox'.$parent.'"';
        if(in_array($category->getId(), $checkedCat)){
            $html[] = 'checked';
        }
        $html[] = '/>';


        $html[] = '<label for="radio'.$category->getId().'">' . $category->getName() . '</label>';

        $html[] = '</a>';

        $htmlChildren = '';
        if($childrenCount>0){
            foreach ($children as $child) {
                $_child = $this->_objectManager->create('Magento\Catalog\Model\Category')->load($child->getId());
                $htmlChildren .= $this->getTreeCategory($_child, $category->getId(), $ids, $checkedCat);
            }
        }
        if (!empty($htmlChildren)) {
            $html[] = '<ul id="container'.$category->getId().'" style="display:none">';
            $html[] = $htmlChildren;
            $html[] = '</ul>';
        }

        $html[] = '</li>';
        $html = implode("\n", $html);
        return $html;
    }

    public function getPricingOptions() {
        $id = $this->getRequest()->getParam('id');
        $_options = ($id > 0) ? $this->get_option() : false;
        if($_options){
            $raw_options = $this->unserialize->unserialize($_options['fields']);
            if( !isset($raw_options["fields"]) ){
                $raw_options["fields"] = array();
            }
            $options = $this->build_options( $raw_options );
            $options['id'] = $_options['id'];
            $options['title'] = $_options['title'];
            $options['priority'] = $_options['priority'];
            $options['published'] = $_options['published'];
            $options['date_from'] = isset($_options['date_from']) ? $_options['date_from'] : '';
            $options['date_to'] = isset($_options['date_to']) ? $_options['date_to'] : '';
            $options['apply_for'] = isset($_options['apply_for']) ? $_options['apply_for'] : 'p';
            $options['product_cats'] = isset($_options['product_cats']) ? ( !is_null($this->unserialize->unserialize($_options['product_cats'])) ? $this->unserialize->unserialize($_options['product_cats']) : array()) : array();
            $options['product_ids'] = isset($_options['product_ids']) ? ( !is_null($this->unserialize->unserialize($_options['product_ids'])) ? $this->unserialize->unserialize($_options['product_ids']) : array()) : array();
        }else{
            $options = $this->build_options();
            $options['id'] = 0;
            $options['title'] = '';
            $options['date_from'] = '';
            $options['date_to'] = '';
            $options['priority'] = 1;                     
            $options['published'] = 1; 
            $options['apply_for'] = 'p';                    
            $options['product_cats'] = array();               
            $options['product_ids'] = array();               
        }
        foreach ( $options["fields"] as $f_index => $field ){
            if($field["conditional"]['enable'] == 'n'){
                $options["fields"][$f_index]["conditional"]['depend'] = $this->build_config_conditional_depend();
                $options["fields"][$f_index]["conditional"]['logic'] = $this->build_config_conditional_logic();
                $options["fields"][$f_index]["conditional"]['show'] = $this->build_config_conditional_show();
            }
        }    

        $product_id = $this->getRequest()->getParam('product_id');
        if( $product_id ){
            if( !$_options ){
                $options['product_ids'] = array( 0 => $product_id);
            }
        }
        return $options;
    }

    private function validate_option( $options ){
        if( $options['display_type'] == 2 ){
            if( !isset($options['pm_hoz']) ){
                $options['pm_hoz'] = array();
            }
            if( !isset($options['pm_ver']) ){
                $options['pm_ver'] = array();
            }                
        }else if( $options['display_type'] == 3 ){
            if( !isset($options['bulk_fields']) ){
                $options['bulk_fields'] = array();
            }
        }else if( $options['display_type'] == 4 ) {
            if( !isset($options['groups']) ){
                $options['groups'] = array();
            }
        } else if( !isset( $options['display_type'] ) ) {
            $options['display_type'] = 1;
        }
        if( isset($options["fields"]) ){
            foreach ( $options["fields"] as $f_index => $field ){
                $array_price_type = array( 'f', 'p', 'p+', 'c', 'cp', 'cf' );
                if( !in_array( $field["general"]['price_type'], $array_price_type ) ){
                    $options["fields"][$f_index]["general"]['price_type'] = 'f';
                }
                if( isset( $field["conditional"]['depend'] ) ){
                    foreach( $field["conditional"]['depend'] as $d_index => $depend ){
                        if( ( $depend['id'] == '? string: ?' ) || ( ($depend['operator'] == 'i' || $depend['operator'] == 'n') && ( !isset( $depend['val'] ) || $depend['val'] == '? string: ?' || $depend['val'] == '? string:? object:null ? ?' ) ) ){
                            unset($options["fields"][$f_index]["conditional"]['depend'][$d_index]);
                        }
                    }
                }
                if( $field["general"]['data_type'] == 'm' ){
                    foreach( $field["general"]['attributes']['options'] as $oIndex => $option ){
                        if( isset( $option['enable_con'] ) && $option['enable_con'] == 'y' ){
                            foreach( $option['depend'] as $d_index => $depend ){
                                if( ( $depend['id'] == '? string: ?' ) || ( ($depend['operator'] == 'i' || $depend['operator'] == 'n') && $depend['val'] == '? string: ?' ) ){
                                    unset($options["fields"][$f_index]["general"]['attributes']['options'][$oIndex]['depend'][$d_index]);
                                }
                            }
                        }
                    }
                }
                if( isset( $field["general"]['published'] ) && $field["general"]['published'] == 'n' ){
                    $options["fields"][$f_index]["general"]['required'] = 'n';
                }
                if( isset( $field["general"]['depend_qty'] ) && $field["general"]['depend_qty'] == 'n2' ){
                    $options["fields"][$f_index]["general"]['price_type'] = 'f';
                }
            }
        }
        return $options;
    }
    public function get_option(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $registry = $objectManager->get('\Magento\Framework\Registry');
        return $registry->registry('current_option')->getData();
    }
    public function build_options( $options = null ){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('\Magento\Framework\Module\ResourceInterface');
        $version = $resource->getDbVersion('Netbaseteam_PricingOption');

        if( is_null($options) ){
            $options = array(
                'quantity_type' => 'r',
                'quantity_discount_type' => 'p',
                'quantity_min' => 1,
                'quantity_max' => 100,
                'quantity_step' => 1,
                'quantity_enable' => 'n',
                'quantity_breaks' => array(
                    array( 'val' =>  1, 'dis'    =>  '', 'default' => 0 )
                ),
                'display_type'  =>  1,
                'version'  =>  $version,
                'pm_hoz'  =>  array(),
                'pm_ver'  =>  array(),
                'bulk_fields'  =>  array(),
                'groups'  =>  array(),
                'fields'    => array(
                    0   =>  $this->default_field()                          
                )
            );
        }
        if( !isset($options['version']) ){
            $options['quantity_enable'] = 'n';
        }
        if( !isset($options['groups']) ){
            $options['groups'] = array();
        }
        foreach ( $options['groups'] as $gkey => $group ){
            $options['groups'][$gkey]['image_url'] = $this->nbd_get_image_thumbnail( $group['image'] );
        }
        $options['fields'] = $this->recursive_stripslashes($options['fields']);
        foreach( $options['fields'] as $f_key => $field ){
            $field = array_replace_recursive($this->default_field(), $field);
            foreach ($field as $tab =>  $data){
                if( $tab != 'id' && $tab != 'nbd_type' && $tab != 'nbpb_type' && $tab != 'nbe_type' ){
                    foreach ($data as $key => $f){
                        if( !in_array($key, array('component_icon', 'page_display', 'exclude_page', 'auto_select_page', 'mesure', 'mesure_type', 'mesure_range', 'mesure_base_pages', 'min_width', 'max_width', 'step_width', 'default_width', 'min_height', 'max_height', 'step_height', 'default_height')) ){
                            $funcname = "build_config_".$tab.'_'.$key;
                            if( is_callable( array( $this, $funcname ) ) ){
                                $options['fields'][$f_key][$tab][$key] = $this->$funcname($f);
                            }
                        }
                        if( $key == 'component_icon' ){
                            $options['fields'][$f_key][$tab]['component_icon_url'] = $this->nbd_get_image_thumbnail( $f );
                        }
                    }
                }
                if( $tab == 'nbd_type' ){
                    $options['fields'][$f_key]['nbd_template'] = 'nbd.'.$data;
                    if( isset($options['fields'][$f_key]['general']['mesure'])){
                        if( !isset($options['fields'][$f_key]['general']['mesure_range']) ) $options['fields'][$f_key]['general']['mesure_range'] = array();
                        if( !isset($options['fields'][$f_key]['general']['mesure_base_pages']) ) $options['fields'][$f_key]['general']['mesure_base_pages'] = 'y';
                        if( !isset($options['fields'][$f_key]['general']['mesure_type']) ) $options['fields'][$f_key]['general']['mesure_type'] = 'u';
                    }
                }
                if( $tab == 'nbpb_type' || $tab == 'nbe_type' ){
                    $options['fields'][$f_key]['nbd_template'] = 'nbd.'.$data;
                }
            }
        }
        if( isset($options['views']) ){
            foreach ($options['views'] as $vkey => $view){
                $view['base'] = isset($view['base']) ? $view['base'] : 0;
                $options['views'][$vkey]['base'] = $view['base'];
                $options['views'][$vkey]['base_url'] = $this->nbd_get_image_thumbnail( $view['base'] );
            }
        }
        return $options;
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
    public function default_field(){
        return array(
            'id'    =>  'f' . round(microtime(true) * 1000),
            'general' => array(
                'title' =>  null,
                'description'       =>  null,
                'data_type'         =>  null,
                'input_type'        =>  null,
                'input_option'      =>  null,
                'text_option'       =>  null,
                'upload_option'     =>  null,
                'enabled'           =>  null,
                'required'          =>  null,
                'published'         =>  null,
                'price_type'        =>  null,
                'depend_quantity'   =>  null,
                'depend_qty'        =>  null,
                'price'             =>  null,
                'price_breaks'      =>  null,
                'attributes'        =>  null
            ),
            'conditional' => array(
                'enable'    =>  'n',
                'show'      =>  'y',
                'logic'     =>  'a',
                'depend'    =>  null
            ),
            'appearance' => array(
                'display_type'          =>  null,
                'change_image_product'  =>  null,
                'show_in_archives'      =>  null
            )                            
        ); 
    }
    public function getDefaultConfigField(){
        $field = $this->default_field();
        foreach ($field as $tab =>  $data){
            if( $tab != 'id' ){
                foreach ($data as $key => $f){
                    $funcname          = "build_config_".$tab.'_'.$key;
                    if( is_callable( array( $this, $funcname ) ) ){
                        $field[$tab][$key] = $this->$funcname($f);
                    }
                }
            }
        }
        return $field;
    }
    public function build_config_general_title( $value = null ){
        if (is_null($value)) $value = __( 'Option name');
        return array(
            'title' => __( 'Option name'),
            'description'   =>  '',
            'value' => $value,
            'type'  => 'text'              
        );
    }
    public function build_config_general_description( $value = null ){
        if (is_null($value)) $value = __( 'Option description');
        return array(
            'title' => __( 'Description'),
            'description'   =>  '',
            'value' => $value,
            'type'  => 'textarea'              
        );            
    }
    public function build_config_general_data_type( $value = null ){
        if (is_null($value)) $value = 'm';
        return array(
            'title' => __( 'Data type'),
            'description'   =>  '',
            'value' => $value,
            'type'      => 'dropdown',
            'options' =>    array(
                array(
                    'key'   =>  'i',
                    'text'   =>  __( 'Custom input')
                ),
                array(
                    'key'   =>  'm',
                    'text'   =>  __( 'Multiple options')
                )
            )               
        );            
    } 
    public function build_config_general_input_type( $value = null ){
        if (is_null($value)) $value = 't';
        return array(
            'title' => __( 'Input type'),
            'description'   =>  '',
            'value' => $value,
            'type'      => 'dropdown',
            'depend'    =>  array(
                array(
                    'field' =>  'data_type',
                    'operator' =>  '=',
                    'value' =>  'i'
                )                    
            ),
            'options' =>    array(
                array(
                    'key'   =>  't',
                    'text'   =>  __( 'Text')
                ),
                array(
                    'key'   =>  'n',
                    'text'   =>  __( 'Number')
                ),
                array(
                    'key'   =>  'r',
                    'text'   =>  __( 'Number range')
                ),
                array(
                    'key'   =>  'u',
                    'text'   =>  __( 'Upload')
                ),
                array(
                    'key'   =>  'a',
                    'text'   =>  __( 'Textarea')
                )
            )               
        );            
    }
    public function build_config_general_input_option( $value = null ){
        if (is_null($value)){$value = array(
            'min'   =>  1,
            'max'   =>  100,
            'step'   =>  1
        );}
        return array(
            'title' => __( 'Input option'),
            'description'   =>  '',
            'value' => $value,
            'type'      => 'table',
            'depend'    =>  array(
                array(
                    'field' =>  'data_type',
                    'operator' =>  '=',
                    'value' =>  'i'
                ), 
                array(
                    'field' =>  'input_type',
                    'operator' =>  '#',
                    'value' =>  't'
                ),
                array(
                    'field' =>  'input_type',
                    'operator' =>  '#',
                    'value' =>  'u'
                ),
                array(
                    'field' =>  'input_type',
                    'operator' =>  '#',
                    'value' =>  'a'
                )
            )              
        );            
    }
    public function build_config_general_text_option( $value = null ){
        if (is_null($value)){$value = array(
            'min'   =>  0,
            'max'   =>  999
        );}
        return array(
            'title' => __( 'Text input option'),
            'description'   =>  '',
            'value' => $value,
            'type'      => 'table',
            'depend'    =>  array(
                array(
                    'field' =>  'data_type',
                    'operator' =>  '=',
                    'value' =>  'i'
                ), 
                array(
                    'field' =>  'input_type',
                    'operator' =>  '=',
                    'value' =>  't,a'
                )                    
            )              
        );            
    }
    public function build_config_general_upload_option( $value = null ){
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $config = $_objectManager->get('Netbaseteam\PricingOption\Helper\Config');

        if (is_null($value)){$value = array(
            'min_size'   =>  $config->getUploadMinSize(),
            'max_size'   =>  $config->getUploadMaxSize(),
            'allow_type'   =>  'png,jpg,jpeg'
        );}
        return array(
            'title' => __( 'Upload file option'),
            'description'   =>  '',
            'value' => $value,
            'type'      => 'table',
            'depend'    =>  array(
                array(
                    'field' =>  'data_type',
                    'operator' =>  '=',
                    'value' =>  'i'
                ), 
                array(
                    'field' =>  'input_type',
                    'operator' =>  '=',
                    'value' =>  'u'
                )                    
            )              
        );            
    }
    public function build_config_general_enabled( $value = null ){
        if (is_null($value)) $value = 'y';
        return array(
            'title' => __( 'Enabled'),
            'description'   =>  'Choose whether the option is enabled or not.',
            'value' => $value,
            'type'      => 'dropdown',
            'options' =>    array(
                array(
                    'key'   =>  'y',
                    'text'   =>  __( 'Yes')
                ),
                array(
                    'key'   =>  'n',
                    'text'   =>  __( 'No')
                )
            )               
        );            
    }
    public function build_config_general_published( $value = null ){
        if (is_null($value)) $value = 'y';
        return array(
            'title' => __( 'Published'),
            'description'   =>  'Show in summary options or not.',
            'value' => $value,
            'type'      => 'dropdown',
            'options' =>    array(
                array(
                    'key'   =>  'y',
                    'text'   =>  __( 'Yes')
                ),
                array(
                    'key'   =>  'n',
                    'text'   =>  __( 'No')
                )
            )               
        );            
    }
    public function build_config_general_required( $value = null ){
        if (is_null($value)) $value = 'n';
        return array(
            'title' => __( 'Required'),
            'description'   =>  __( 'Choose whether the option is required or not.' ),
            'value' => $value,
            'type'      => 'dropdown',
            'options' =>    array(
                array(
                    'key'   =>  'y',
                    'text'   =>  __( 'Yes')
                ),
                array(
                    'key'   =>  'n',
                    'text'   =>  __( 'No')
                )
            ),
            'depend'    => array(
                array(
                    'field' =>  'published',
                    'operator' =>  '#',
                    'value' =>  'n'
                )
            )
        );            
    } 
    public function build_config_general_price_type( $value = null ){
        if (is_null($value)) $value = 'f';
        return array(
            'title' => __( 'Price type'),
            'description'   =>  __( 'Here you can choose how the price is calculated. Depending on the field there various types you can choose.' ),
            'value' => $value,
            'type'      => 'dropdown',
            'options' =>    array(
                array(
                    'key'   =>  'f',
                    'text'   =>  __( 'Fixed amount')                      
                ),
                array(
                    'key'   =>  'p',
                    'text'   =>  __( 'Percent of the original price')                     
                ),
                array(
                    'key'   =>  'p+',
                    'text'   =>  __( 'Percent of the original price + options')                       
                ),
                array(
                    'key'   =>  'c',
                    'text'   =>  __( 'Current value * price'),
                    'depend'    => array(
                        array(
                            'field' =>  'data_type',
                            'operator' =>  '=',
                            'value' =>  'i'
                        ),
                        array(
                            'field' =>  'input_type',
                            'operator' =>  '#',
                            'value' =>  'u'
                        ),
                        array(
                            'field' =>  'input_type',
                            'operator' =>  '#',
                            'value' =>  't'
                        ),
                        array(
                            'field' =>  'input_type',
                            'operator' =>  '#',
                            'value' =>  'a'
                        )
                    )
                ),
                array(
                    'key'   =>  'cp',
                    'text'   =>  __( 'Price per char'),
                    'depend'    => array(
                        array(
                            'field' =>  'data_type',
                            'operator' =>  '=',
                            'value' =>  'i'
                        ),
                        array(
                            'field' =>  'input_type',
                            'operator' =>  '=',
                            'value' =>  't'
                        )
                    )
                )
            )               
        );            
    }
    public function build_config_general_depend_quantity( $value = null ){
        if (is_null($value)) $value = 'n';
        return array(
            'title' => __( 'Depend quantity breaks'),
            'description'   =>  '',
            'value' => $value,
            'type'      => 'dropdown',
            'options' =>    array(
                array(
                    'key'   =>  'y',
                    'text'   =>  __( 'Yes')
                ),
                array(
                    'key'   =>  'n',
                    'text'   =>  __( 'No')
                )
            )               
        );            
    }
    public function build_config_general_depend_qty( $value = null ){
        if (is_null($value)) $value = 'y';
        return array(
            'title' => __( 'Depend quantity'),
            'description'   =>  'If choose No additional price will be apply for cart item independently with the quantity.',
            'value' => $value,
            'type'      => 'dropdown',
            'options' =>    array(
                array(
                    'key'   =>  'y',
                    'text'   =>  __( 'Yes')
                ),
                array(
                    'key'   =>  'n',
                    'text'   =>  __( 'No, the additional price is cart item fee')
                ),
                array(
                    'key'   =>  'n2',
                    'text'   =>  __( 'No, the additional price is fixed amount for all items')
                )
            )
        );            
    }
    public function build_config_general_price( $value = null ){
        if (is_null($value)) $value = '';
        return array(
            'title' => __( 'Additional Price'),
            'description'   =>  __( 'Enter the price for this field or leave it blank for no price.' ),
            'value' => $value,
            'depend'    =>  array(
                array(
                    'field' =>  'depend_quantity',
                    'operator' =>  '#',
                    'value' =>  'y'
                ),
                array(
                    'field' =>  'data_type',
                    'operator' =>  '=',
                    'value' =>  'i'
                )                                 
            ),                                        
            'type'  => 'number'                
        );
    }
    public function build_config_general_price_breaks( $value = null ){
        if (is_null($value)) $value = array();
        return array(  
            'title'     => __( 'Price depend quantity breaks'),
            'depend'    =>  array(
                array(
                    'field'     =>  'depend_quantity',
                    'operator'  =>  '=',
                    'value'     =>  'y'
                ),
                array(
                    'field'     =>  'data_type',
                    'operator'  =>  '=',
                    'value'     =>  'i'
                )                                    
            ),
            'description'   =>  '',
            'value'         => $value,
            'type'          => 'single_quantity_depend'                  
        );
    }     
    public function build_config_general_attributes( $attributes = null ){
        if (is_null($attributes)){ $options = array(
                0 => array(
                    'name'                  =>  __( 'Attribute name'),
                    'des'                   => '',
                    'price'             =>  array(),
                    'selected'              =>  0,
                    'enable_subattr'        =>  0,
                    'preview_type'          =>  'i',
                    'image'                 =>  0,
                    'image_url'             =>  '',
                    'product_image'         =>  0,
                    'product_image_url'     =>  '',
                    'color'                 =>  '#ffffff',
                    'sub_attributes'        =>  array(),
                    'sattr_display_type'    =>  's',
                )
            );
        } else {
            $options = $attributes['options'];
        };
        foreach( $options as $key => $option){
            $options[$key]['enable_subattr']     = isset( $options[$key]['enable_subattr'] ) ? $options[$key]['enable_subattr'] : 0;
            $options[$key]['sub_attributes']     = isset( $options[$key]['sub_attributes'] ) ? $options[$key]['sub_attributes'] : array();
            $options[$key]['sattr_display_type'] = isset( $options[$key]['sattr_display_type'] ) ? $options[$key]['sattr_display_type'] : 's';

            $options[$key]['image_url']          = $this->nbd_get_image_thumbnail( $option['image'] );
            if( isset($options[$key]['product_image']) ){
                $options[$key]['product_image_url'] = $this->nbd_get_image_thumbnail( $option['product_image'] );
            }
            if( isset($attributes['bg_type']) ){
                if( $attributes['bg_type'] == 'i' ){
                    foreach( $option['bg_image'] as $k => $bg ){
                        $options[$key]['bg_image_url'][$k] = $this->nbd_get_image_thumbnail( $bg );
                    }
                }else{
                    $options[$key]['bg_image']      = array();
                    $options[$key]['bg_image_url']  = array();
                }
            }
            if( isset($option['enable_subattr']) ){
                foreach( $options[$key]['sub_attributes'] as $sak => $sa ){
                    $options[$key]['sub_attributes'][$sak]['image_url'] = $this->nbd_get_image_thumbnail( $sa['image'] );
                }
            }
        }
        $same_size = isset($attributes['same_size']) ? $attributes['same_size'] : 'y';
        $bg_type = isset($attributes['bg_type']) ? $attributes['bg_type'] : 'i';
        $number_of_sides = isset($attributes['number_of_sides']) ? $attributes['number_of_sides'] : 4;
        return array(  
            'title'           => __( 'Attributes'),
            'description'     =>  __( 'Attributes let you define extra product data, such as size or color.'),                                     
            'type'            => 'attributes',
            'same_size'       =>  $same_size,
            'bg_type'         =>  $bg_type,
            'number_of_sides' =>  $number_of_sides,
            'depend'          =>  array(
                array(
                    'field'     =>  'data_type',
                    'operator'  =>  '=',
                    'value'     =>  'm'                                        
                )
            ), 
            'options'         => $options 
        );
    }
    public function build_config_general_pb_config($configs){
        foreach( $configs as $key => $o_config){
            foreach( $o_config as $skey => $so_config){
                foreach( $so_config['views'] as $vkey => $view){
                    $configs[$key][$skey]['views'][$vkey]['display']    = (isset($view['display']) && $view['display'] == 'on') ? true : false;
                    $configs[$key][$skey]['views'][$vkey]['image_url']  = $this->nbd_get_image_thumbnail( $view['image'] );
                }
            }
        }
        return $configs;
    }
    public function build_config_general_nbpb_text_configs($configs){
        if( !isset($configs['views']) ) $configs['views'] = array();
        foreach( $configs['views'] as $key => $view){
            $configs['views'][$key]['display'] = (isset($view['display']) && $view['display'] == 'on') ? true : false;
        }
        return $configs;
    }
    public function build_config_general_nbpb_image_configs($configs){
        if( !isset($configs['views']) ) $configs['views'] = array();
        foreach( $configs['views'] as $key => $view){
            $configs['views'][$key]['display'] = (isset($view['display']) && $view['display'] == 'on') ? true : false;
        }
        return $configs;
    }
    public function build_config_appearance_display_type( $value = null ){
        if (is_null($value)) $value = 'd';
        return array(  
            'title' => __( 'Display type'),
            'description'   =>  '',
            'value' => $value,
            'type'      => 'dropdown',
            'options' =>    array(
                array(
                    'key'   =>  'd',
                    'text'  =>  __( 'Dropdown')
                ),
                array(
                    'key'   =>  'r',
                    'text'  =>  __( 'Radio button')
                ),
                array(
                    'key'   =>  's',
                    'text'  =>  __( 'Swatch')
                ),
                array(
                    'key'   =>  'l',
                    'text'  =>  __( 'Label')
                ),    
                 array(
                    'key'   =>  'ad',
                    'text'  =>  __( 'Advanced Dropdown')
                ),
                array(
                    'key'   =>  'xl',
                    'text'  =>  __( 'Large label')
                )
            )           
        );
    }
    public function build_config_appearance_change_image_product( $value = null ){
        if (is_null($value)) $value = 'n';
        return array(  
            'title' => __( 'Changes product image'),
            'description'   =>  __('Choose whether to change the product image.'),
            'type'      => 'dropdown',
            'value' => $value,
            'options' =>    array(
                array(
                    'key'   =>  'y',
                    'text'   =>  __( 'Yes')
                ),
                array(
                    'key'   =>  'n',
                    'text'   =>  __( 'No')
                )
            )           
        );
    }  
    public function build_config_appearance_show_in_archives( $value = null ){
        if (is_null($value)) $value = 'n';
        return array(  
            'title' => __( 'Show in archive pages'),
            'description'   =>  __('Show option in archive pages as swatch.'),
            'type'      => 'dropdown',
            'value' => $value,
            'options' =>    array(
                array(
                    'key'   =>  'y',
                    'text'   =>  __( 'Yes')
                ),
                array(
                    'key'   =>  'n',
                    'text'   =>  __( 'No')
                )
            )           
        );
    }  
    public function build_config_appearance_quantity_selector( $value = null ){
        if (is_null($value)) $value = 'n';
        return array(  
            'title' => __( 'Quantity selector'),
            'description'   =>  __('This will show a quantity selector for this option.'),
            'type'      => 'dropdown',
            'value' => $value,
            'options' =>    array(
                array(
                    'key'   =>  'y',
                    'text'   =>  __( 'Yes')
                ),
                array(
                    'key'   =>  'n',
                    'text'   =>  __( 'No')
                )
            )           
        );
    }
    public function build_config_conditional_enable( $value = null ){
        if (is_null($value)) $value = 'n';
        return $value;
    }
    public function build_config_conditional_show( $value = null ){
        if (is_null($value)) $value = 'n';
        return $value;
    } 
    public function build_config_conditional_logic( $value = null ){
        if (is_null($value)) $value = 'a';
        return $value;
    } 
    public function build_config_conditional_depend( $value = null ){
        if (is_null($value) || count($value) == 0) $value = array(
            0   =>  array(
                'id'    => '',
                'operator'  =>  'i',
                'val'   =>  ''
            )
        );
        return $value;
    }
    public function nbd_get_image_thumbnail( $id ){
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $_objectManager->create('Magento\Catalog\Model\Product')->load($id);
        $imageHelper  = $_objectManager->get('\Magento\Catalog\Helper\Image');
        $image_url = $imageHelper->init($product, 'product_thumbnail_image')->setImageFile($product->getFile())->resize(150, 150)->getUrl();
        return $image_url;
    }
}