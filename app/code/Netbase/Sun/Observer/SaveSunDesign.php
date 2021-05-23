<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Netbase\Sun\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveSunDesign implements ObserverInterface
{
    protected $_messageManager;
    /**
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
       \Netbase\Sun\Helper\Data $sunHelper,
       \Magento\Framework\App\Filesystem\DirectoryList $directory_list
    ) {
        $this->sunHelper = $sunHelper;
        $this->directory_list = $directory_list;
    }

    /**
     * Log out user and redirect to new admin custom url
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.ExitExpression)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $configDesign = $this->sunHelper->getConfigDesign();

        $post = $configDesign;

        // print_r($this->directory_list->getPath('app').'/design/frontend/Netbase');die;
        // if (!empty($post)) {
        //     $save_vari = createStyle($post);
        //     if($save_vari=="success"){
        //         convert_less();
        //     }
        // }
    }

    function createStyle($data) {
        $fileconfig = PATH_LESS . DS . 'less' . DS . 'theme.less';

        $file = fopen($fileconfig, 'w');
        $text = '';

        foreach ($data as $key => $value) {
            if ($value != null || $value != '')
                $text .= "@$key : $value;\n";
        }
        $text .= $customelement;
        if (fwrite($file, $text)) {
            return $response = 'success';
        } else {
            return $response = 'warning: ';
        }
    }
    function convert_less() {
        $less = new lessc;
        try{
            $less->setFormatter("compressed");
            $less->compileFile(PATH_LESS . '/less/combine.less', PATH_LESS . '/css/template.css');
        } catch (exception $e) {
            echo "fatal error: " . $e->getMessage();
        }
    }
}
