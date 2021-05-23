<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Block\Onepage;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\View\Element\Template;

class Head extends Template
{
    /**
     * @var \Netbaseteam\Onestepcheckout\Model\Field
     */
    protected $fieldSingleton;

    /**
     * Head constructor.
     *
     * @param Template\Context             $context
     * @param \Netbaseteam\Onestepcheckout\Model\Field $fieldSingleton
     * @param array                        $data
     */
    public function __construct(
        Template\Context $context,

        \Netbaseteam\Onestepcheckout\Model\Field $fieldSingleton,

        array $data
    ) {
        parent::__construct($context, $data);
        $this->fieldSingleton = $fieldSingleton;
    }

    public function getFields()
    {
        $result = [];

        /** @var \Netbaseteam\Onestepcheckout\Model\Field $field */
        foreach ($this->fieldSingleton->getConfig() as $field) {
            $result[$field->getData('attribute_code')] = $field->getData('width');
        }
        
        return $result;
    }
    
    public function getCustomFont()
    {
        $font = $this->_scopeConfig->getValue(
            'netbaseteam_onestepcheckout/design/font',
            ScopeInterface::SCOPE_STORE
        );

        return $this->escapeHtml(strtok(trim($font), ':'));
    }

    public function getHeadingTextColor()
    {
        return $this->getRgbSetting('netbaseteam_onestepcheckout/design/heading_color');
    }

    public function getSummaryBackgroundColor()
    {
        return $this->getRgbSetting('netbaseteam_onestepcheckout/design/summary_color');
    }

    public function getBackgroundColor()
    {
        return $this->getRgbSetting('netbaseteam_onestepcheckout/design/bg_color');
    }

    public function getButtonColor()
    {
        $colorCode = $this->getRgbSetting('netbaseteam_onestepcheckout/design/button_color');

        if ($colorCode) {
            $less = new \Less_Functions(null);
            $darken = new \Less_Tree_Dimension(10, '%');
            $color = new \Less_Tree_Color(ltrim($colorCode, '#'));

            $hoverColor = $less->darken($color, $darken);

            return [
                'normal' => $colorCode,
                'hover' => $hoverColor->toRGB()
            ];
        }

        return false;
    }

    protected function getRgbSetting($setting)
    {
        $code = $this->_scopeConfig->getValue($setting, ScopeInterface::SCOPE_STORE);

        $code = trim($code);

        if (!preg_match('|#[0-9a-fA-F]{3,6}|', $code)) {
            return false;
        }

        return $code;
    }
}
