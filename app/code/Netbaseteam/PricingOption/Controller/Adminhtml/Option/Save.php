<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbaseteam\PricingOption\Controller\Adminhtml\Option;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;

class Save extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Netbaseteam_PricingOption::pricing_option';

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $jsHelper;

    /**
     * @var \Netbaseteam\PricingOption\Helper\Data
     */
    protected $_helper;

    /**
     * @param Action\Context $context
     * @param \Magento\Backend\Helper\Js $jsHelper
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Backend\Helper\Js $jsHelper,
        \Netbaseteam\PricingOption\Helper\Data $helper,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory
    )
    {
        parent::__construct($context);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->jsHelper = $jsHelper;
        $this->_helper = $helper;
        $this->_dateFactory = $dateFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            /** @var \Netbaseteam\PricingOption\Model\Option $model */
            $model = $this->_objectManager->create('Netbaseteam\PricingOption\Model\Option');

            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
            }
            $arr = array(
                'id' => $id,
                'title' =>  $data['title'],
                'published' =>  $data['published'],
                'priority' =>  $data['priority'],
                'date_from' =>  $data['date_from'],
                'date_to' =>  $data['date_to'],
                'apply_for' =>  'p',
                'product_cats' =>  isset($data['product_cats']) ? serialize($data['product_cats']) : serialize(array()),
                'modified'  => $this->_dateFactory->create()->gmtDate(),
                'fields'    => serialize($data['options'])
            );

            if(isset($data['products'])) $arr['product_ids'] = serialize(explode('&',$data['products']));
            $arr['fields'] = serialize( $this->validate_option($data['options']) );
            
            if($id){
                $arr['modified'] = $this->_dateFactory->create()->gmtDate();
            }else{
                $arr['created'] = $this->_dateFactory->create()->gmtDate();
            }

            $arr['stores'] = $data['store_id_hidden'];

            $model->setData($arr);

            $this->_eventManager->dispatch(
                'pricingoption_option_prepare_save',
                ['option' => $model, 'request' => $this->getRequest()]
            );

            try {
                $model->save();
                $this->_cacheTypeList->cleanType('full_page');

                $this->messageManager->addSuccess(__('You saved this Option.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Option.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
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
}
