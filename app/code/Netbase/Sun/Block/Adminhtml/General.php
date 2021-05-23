<?php
/**
 * @copyright Copyright (c) 2016 www.netbasejsc.com
 */

namespace Netbase\Sun\Block\Adminhtml;

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

class General extends \Magento\Backend\Block\Template
{

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Filesystem\DirectoryList $directory_list,
        array $data = []
    )
    {
        $this->_objectManager = $objectManager;
        $this->directory_list = $directory_list;
        parent::__construct($context, $data);
    }

    public function getThemeDetails() {
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $version = $this->_objectManager->get('Magento\Framework\App\ProductMetadataInterface');

        $theme_table = $connection->getTableName('theme');   
        $theme_data = $connection->fetchAll("SELECT * FROM ".$theme_table);
        
        return $theme_data;
    }

     public function render_google_font_option( $id ='', $name = '', $class='', $data_name ='', $selected ){
        $dir = dirname(__FILE__);        
        $path = $dir.DS.'fonts'.DS.'google-fonts.json';
        $fonts = json_decode(file_get_contents($path))->items;
        $html = '<select name = '.$name.' id="'.$id.'" class="'.$class.'" data-name="'.$data_name.'">';
        $html .= '<option value="usedefault" >Default</option>';
        foreach ($fonts as $font) {
            $_selected = ( $selected != '' && $font->family == $selected ) ? ' selected="selected"' : '';
            $html .= '<option value="'.$font->family.'" '.$_selected.'>'.$font->family.'</option>';
        }
        $html .= '</select>';
        return $html;
    }
    public function getConfigData() {
        $themeRequest = $this->getRequest()->getParam('current_theme_path');
        $themePath = str_replace('/',DS,$themeRequest);
        $fileconfig = str_replace('/',DS,$this->directory_list->getPath('app')).DS.'design'.DS.'frontend'.DS.$themePath.DS.'web'.DS.'css'.DS.'source'.DS.'config'.DS.'_general.less';
        $filejsonconfig = str_replace('/',DS,$this->directory_list->getPath('app')).DS.'design'.DS.'frontend'.DS.$themePath.DS.'web'.DS.'css'.DS.'source'.DS.'config'.DS.'general.json';
        if (file_exists($filejsonconfig)) { 
            $configData = json_decode(file_get_contents($filejsonconfig));
        }else{
            $configData = array(
                'typo_general' => array(
                    '0' =>  array(
                        'font-family-name__base' => 'usedefault'
                    )
                ),
                'color_general' => array(
                    '0' =>  array(
                        'font-family-name__base' => 'usedefault'
                    )
                ),
                'color_grays_general' => array(
                    '0' =>  array(
                        'main_color_grays__general' => '#333333'
                    )
                ),
                'components_general' => array(
                    '0' =>  array(
                        'gutter__layout' => '1'
                    )
                )
            );
        }
        
        return $configData;
    }
}
