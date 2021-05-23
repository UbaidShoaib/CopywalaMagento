<?php
/**
 * @copyright Copyright (c) 2016 www.cmsmart.net
 */

namespace Netbase\Sun\Controller\Adminhtml\Designsetting;

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
use Magento\Backend\App\Action\Context;
use Magento\Framework\Webapi\Exception;
use Magento\TestFramework\ErrorLog\Logger;


class Save extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Netbase_Sun::design_settings';

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\Filesystem\DirectoryList $directory_list,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory,
        \Magento\Framework\Locale\Resolver $store,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
    )
    {
        $this->directory_list = $directory_list;
        $this->messageManager = $messageManager;
        $this->resultRedirectFactory = $redirectFactory;
        $this->_store = $store;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        if (!empty($data)) {
            $save_vari = $this->createStyle($data);
            if($save_vari=="success"){
                $this->messageManager->addSuccess('success');
                $this->delete_cache($data);
                $types = array('config','layout','block_html','collections','reflection','db_ddl','eav','config_integration','config_integration_api','full_page','translate','config_webservice');
                foreach ($types as $type) {
                    $this->_cacheTypeList->cleanType($type);
                }
                foreach ($this->_cacheFrontendPool as $cacheFrontend) {
                    $cacheFrontend->getBackend()->clean();
                }
            } else {
                $this->messageManager->addError('Error');
            }
        }
        return $this->resultRedirectFactory->create()->setPath('netbasesun/system/design', ['_current' => true]);
    } 
    public function delete_cache($data){
        $localeCode = $this->getLocaleCode();
        $themePath = str_replace('/',DS,$data['theme_path']);        
        $cssPath_m = str_replace('/',DS,$this->directory_list->getPath('pub')).DS.'static'.DS.'frontend'.DS.$themePath.DS.$localeCode.DS.'css'.DS.'styles-m.css';  
        $this->delete_folder($cssPath_m);  
        $cssPath_l = str_replace('/',DS,$this->directory_list->getPath('pub')).DS.'static'.DS.'frontend'.DS.$themePath.DS.$localeCode.DS.'css'.DS.'styles-l.css';  
        $this->delete_folder($cssPath_l);

        $cache_path = str_replace('/',DS,$this->directory_list->getPath('var')).DS.'cache';
        $preprocessed_path = str_replace('/',DS,$this->directory_list->getPath('var')).DS.'view_preprocessed';
        $this->delete_folder($cache_path);  
        $this->delete_folder($preprocessed_path);  
    }
    public function save_google_font_used($path, $name){
        if( file_exists($path) ){
            $google_fonts = json_decode(file_get_contents($path));
        }else{
            $google_fonts = array();
        }        
        $exist = false;
        foreach ($google_fonts as $font) {
            if( $font == $name ) {
                $exist = true;
                break;
            }
        }
        if( !$exist ) $google_fonts[] = $name;
        file_put_contents($path, json_encode($google_fonts));
    } 
    public function createStyle($data) {
        $themePath = str_replace('/',DS,$data['theme_path']);
        $fileconfig = str_replace('/',DS,$this->directory_list->getPath('app')).DS.'design'.DS.'frontend'.DS.$themePath.DS.'web'.DS.'css'.DS.'source'.DS.'config'.DS.'_design.less';
         $filejsonconfig = str_replace('/',DS,$this->directory_list->getPath('app')).DS.'design'.DS.'frontend'.DS.$themePath.DS.'web'.DS.'css'.DS.'source'.DS.'config'.DS.'design.json';
        $directPath = str_replace('/',DS,$this->directory_list->getPath('app')).DS.'design'.DS.'frontend'.DS.$themePath.DS.'web'.DS.'css'.DS.'source'.DS.'config'.DS;
        if (!file_exists($directPath)) {
            mkdir($directPath, 0777, true);
        }
        file_put_contents($filejsonconfig, json_encode($data));   
        $file = fopen($fileconfig, 'w+');
        $text = '';
        unset($data['theme_path']);
        unset($data['key']); 
        $google_font_config = str_replace('/',DS,$this->directory_list->getPath('app')).DS.'design'.DS.'frontend'.DS.$themePath.DS.'web'.DS.'css'.DS.'source'.DS.'config'.DS.'google_font_used.json';
        //var_dump($path);die();
        foreach ($data['typo_design'] as $k => $typo_design) {
            foreach ( $typo_design as $key => $value) {
                if ($value != null || $value != ''){
                    if(($key != 'h1__line-height__design') && ($key != 'h2__line-height__design') && ($key != 'h3__line-height__design') && ($key != 'h4__line-height__design') && ($key != 'h5__line-height__design') && ($key != 'h6__line-height__design') && ($key != 'h1__font-size__design') && ($key != 'h2__font-size__design') && ($key != 'h3__font-size__design') && ($key != 'h4__font-size__design') && ($key != 'h5__font-size__design') && ($key != 'h6__font-size__design') && ($key != 'width__border-button__design') && ($key != 'vertical__padding-button__design') && ($key != 'hozital__padding-button__design') && ($key != 'border_radius-button__design') && ($key != 'width__border-button-cta__design')){
                        $text .= "@$key : $value;\n"; 
                    }else{
                        $text .= "@$key : $value"."px;"."\n";
                    }  
                    if( strpos($key, 'font-family') !== false ) {
                        if($value != 'usedefault'){
                            $this->save_google_font_used( $google_font_config, $value  );
                        } 
                    }
                }        
            } 
        } 
        foreach ($data['link_design'] as $k => $link_design) {
            foreach ( $link_design as $key => $value) {
                if ($value != null || $value != ''){
                    if(($key != 'h1__line-height__design') && ($key != 'h2__line-height__design') && ($key != 'h3__line-height__design') && ($key != 'h4__line-height__design') && ($key != 'h5__line-height__design') && ($key != 'h6__line-height__design') && ($key != 'h1__font-size__design') && ($key != 'h2__font-size__design') && ($key != 'h3__font-size__design') && ($key != 'h4__font-size__design') && ($key != 'h5__font-size__design') && ($key != 'h6__font-size__design') && ($key != 'width__border-button__design') && ($key != 'vertical__padding-button__design') && ($key != 'hozital__padding-button__design') && ($key != 'border_radius-button__design') && ($key != 'width__border-button-cta__design')){
                        $text .= "@$key : $value;\n"; 
                    }else{
                        $text .= "@$key : $value"."px;"."\n";
                    }  
                    if( strpos($key, 'font-family') !== false ) {
                        if($value != 'usedefault'){
                            $this->save_google_font_used( $google_font_config, $value  );
                        } 
                    }
                }        
            } 
        } 
        foreach ($data['button_design'] as $k => $button_design) {
            foreach ( $button_design as $key => $value) {
                if ($value != null || $value != ''){
                    if(($key != 'h1__line-height__design') && ($key != 'h2__line-height__design') && ($key != 'h3__line-height__design') && ($key != 'h4__line-height__design') && ($key != 'h5__line-height__design') && ($key != 'h6__line-height__design') && ($key != 'h1__font-size__design') && ($key != 'h2__font-size__design') && ($key != 'h3__font-size__design') && ($key != 'h4__font-size__design') && ($key != 'h5__font-size__design') && ($key != 'h6__font-size__design') && ($key != 'width__border-button__design') && ($key != 'vertical__padding-button__design') && ($key != 'hozital__padding-button__design') && ($key != 'border_radius-button__design') && ($key != 'width__border-button-cta__design')){
                        $text .= "@$key : $value;\n"; 
                    }else{
                        $text .= "@$key : $value"."px;"."\n";
                    }  
                    if( strpos($key, 'font-family') !== false ) {
                        if($value != 'usedefault'){
                            $this->save_google_font_used( $google_font_config, $value  );
                        } 
                    }
                }        
            } 
        } 
        foreach ($data['button_design_cta'] as $k => $button_design_cta) {
            foreach ( $button_design_cta as $key => $value) {
                if ($value != null || $value != ''){
                    if(($key != 'h1__line-height__design') && ($key != 'h2__line-height__design') && ($key != 'h3__line-height__design') && ($key != 'h4__line-height__design') && ($key != 'h5__line-height__design') && ($key != 'h6__line-height__design') && ($key != 'h1__font-size__design') && ($key != 'h2__font-size__design') && ($key != 'h3__font-size__design') && ($key != 'h4__font-size__design') && ($key != 'h5__font-size__design') && ($key != 'h6__font-size__design') && ($key != 'width__border-button__design') && ($key != 'vertical__padding-button__design') && ($key != 'hozital__padding-button__design') && ($key != 'border_radius-button__design') && ($key != 'width__border-button-cta__design')){
                        $text .= "@$key : $value;\n"; 
                    }else{
                        $text .= "@$key : $value"."px;"."\n";
                    }  
                    if( strpos($key, 'font-family') !== false ) {
                        if($value != 'usedefault'){
                            $this->save_google_font_used( $google_font_config, $value  );
                        } 
                    }
                }        
            } 
        } 
        if (fwrite($file, $text)) {
            return $response = 'success';
        } else {
            return $response = 'warning: ';
        }
    }
    public static function delete_folder($path) {
        if (is_dir($path) === true) {
            $files = array_diff(scandir($path), array('.', '..'));
            foreach ($files as $file) {
                self::delete_folder(realpath($path) . '/' . $file);
            }
            return rmdir($path);
        } else if (is_file($path) === true) {
            return unlink($path);
        }
        return false;
    }
    public function getLocaleCode()
    {
        return $this->_store->getLocale();
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }
}