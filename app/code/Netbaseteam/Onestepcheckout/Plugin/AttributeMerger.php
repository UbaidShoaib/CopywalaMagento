<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Plugin;

use Netbaseteam\Onestepcheckout\Model\Field;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class AttributeMerger
{
    protected $defaultData = null;
    protected $fieldsConfig = null;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var RemoteAddress
     */
    protected $remoteAddress;
    /**
     * @var Field
     */
    protected $fieldSingleton;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var LayoutProcessor
     */
    protected $layoutProcessorPlugin;

    /**
     * LayoutProcessor constructor.
     *
     * @param ScopeConfigInterface  $scopeConfig
     * @param Geolocation           $geolocation
     * @param RemoteAddress         $remoteAddress
     * @param Field                 $fieldSingleton
     * @param StoreManagerInterface $storeManager
     * @param LayoutProcessor       $layoutProcessorPlugin
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        RemoteAddress $remoteAddress,
        Field $fieldSingleton,
        StoreManagerInterface $storeManager,
        \Netbaseteam\Onestepcheckout\Plugin\LayoutProcessor $layoutProcessorPlugin
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->remoteAddress = $remoteAddress;
        $this->fieldSingleton = $fieldSingleton;
        $this->storeManager = $storeManager;
        $this->layoutProcessorPlugin = $layoutProcessorPlugin;
    }

    public function getDefaultData()
    {
        if ($this->defaultData === null) {
            $this->defaultData = [];

            $defaultValues = $this->scopeConfig->getValue(
                'netbaseteam_onestepcheckout/default_values',
                ScopeInterface::SCOPE_STORE
            );

            if (is_array($defaultValues)) {
                foreach ($defaultValues as $code => $value) {
                    if (preg_match('#^address_(?P<field>.+)$#', $code, $matches)) {
                        $this->defaultData[$matches['field']] = $value;
                    }
                }
            }
        }

        return $this->defaultData;
    }
    
    public function getFieldConfig()
    {
        if ($this->fieldsConfig === null) {
            $this->fieldsConfig = $this->fieldSingleton->getConfig(
                $this->storeManager->getStore()->getId()
            );
        }
        
        return $this->fieldsConfig;
    }

    /**
     * @see \Magento\Checkout\Block\Checkout\AttributeMerger:getFieldConfig to understand wth is going on here
     */
    public function aroundMerge(
        \Magento\Checkout\Block\Checkout\AttributeMerger $subject,
        \Closure $proceed,
        $elements, $providerName, $dataScopePrefix, array $fields = []
    ) {
        if (!$this->scopeConfig->isSetFlag('netbaseteam_onestepcheckout/general/enabled', ScopeInterface::SCOPE_STORE)) {
            return $proceed($elements, $providerName, $dataScopePrefix, $fields);
        }
        
        $defaultData = $this->getDefaultData();
        $fieldConfig = $this->getFieldConfig();
        $fieldConfig['region_id'] = $fieldConfig['region'];
        $inheritedAttributes = $this->fieldSingleton->getInheritedAttributes();

        foreach ($elements as $attributeCode => &$attributeConfig) {
            if (isset($defaultData[$attributeCode])) {
                $attributeConfig['default'] = $defaultData[$attributeCode];
            }

            if (isset($inheritedAttributes[$attributeCode])) {
                $parent = $inheritedAttributes[$attributeCode];

                if (isset($fieldConfig[$parent])) {
                    $attributeConfig['sortOrder'] = $fieldConfig[$parent]->getData('sort_order');
                    $attributeConfig['visible'] = $fieldConfig[$parent]->getData('enabled');
                }
            }

            if (isset($fieldConfig[$attributeCode])) {
                $field = $fieldConfig[$attributeCode];

                if (!(int)$field->getData('enabled')) {
                    unset($elements[$attributeCode]);
                    unset($fields[$attributeCode]);
                    continue;
                }

                /** @var \Netbaseteam\Onestepcheckout\Model\Field $field */
                $attributeConfig['sortOrder'] = $field->getData('sort_order');
                $this->layoutProcessorPlugin->setOrder($attributeCode, $field->getData('sort_order'));
                $attributeConfig['required'] = $field->getData('required');
                $attributeConfig['validation']['required-entry'] = (bool)$field->getData('required');

                $label = $field->getData('label');

                if ($label != $field->getData('default_label')) {
                    $attributeConfig['label'] = $label;
                }
            }
        }
        unset($attributeConfig);

        $config = $proceed($elements, $providerName, $dataScopePrefix, $fields);

        foreach ($config as $code => $configItem) {
            if (isset($fieldConfig[$code])) {
                $config[$code]['sortOrder'] = $fieldConfig[$code]->getData('sort_order');
            }
        }

        if (isset($config['postcode']) && isset($fieldConfig['postcode'])) {
            $config['postcode']['component'] = 'Netbaseteam_Onestepcheckout/js/form/element/post-code';
            $config['postcode']['skipValidation'] = !$fieldConfig['postcode']->getData('required');
        }
        if (isset($config['region_id'])) {
            $config['region_id']['component'] = 'Netbaseteam_Onestepcheckout/js/form/element/region';
        }

        if (isset($config['street'])) {
            $config['street']['children'][0]['component'] = 'Netbaseteam_Onestepcheckout/js/form/element/autocomplete';
        }

        return $config;
    }
}
