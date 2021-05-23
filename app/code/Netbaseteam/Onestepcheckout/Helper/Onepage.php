<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Helper;

use Netbaseteam\Onestepcheckout\Model\ResourceModel\Region\CollectionFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;


use Magento\Framework\App\Helper\Context;

class Onepage extends AbstractHelper
{
    const DISPLAY_AGREEMENTS_PLACE = 'netbaseteam_onestepcheckout/additional_options/display_agreements';

    const VALUE_ORDER_TOTALS = 'order_totals';

    /**
     * @var CollectionFactory
     */
    protected $regionsFactory;
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;
    /**
     * @var \Netbaseteam\Onestepcheckout\Plugin\AttributeMerger
     */
    protected $attributeMerger;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        Context $context,
        CollectionFactory $regionsFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Netbaseteam\Onestepcheckout\Plugin\AttributeMerger $attributeMerger,
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        parent::__construct($context);
        $this->regionsFactory = $regionsFactory;
        $this->jsonHelper = $jsonHelper;
        $this->attributeMerger = $attributeMerger;
        $this->objectManager = $objectManager;
    }

    public function getTitle()
    {
        return $this->scopeConfig->getValue(
            'netbaseteam_onestepcheckout/general/title', ScopeInterface::SCOPE_STORE
        );
    }

    public function getDescription()
    {
        return $this->scopeConfig->getValue(
            'netbaseteam_onestepcheckout/general/description', ScopeInterface::SCOPE_STORE
        );
    }

    public function isAddressSuggestionEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            'netbaseteam_onestepcheckout/geolocation/google_address_suggestion', ScopeInterface::SCOPE_STORE
        );
    }

    public function getGoogleMapsKey()
    {
        return $this->scopeConfig->getValue(
            'netbaseteam_onestepcheckout/geolocation/google_api_key', ScopeInterface::SCOPE_STORE
        );
    }

    public function getRegionsJson()
    {
        return $this->jsonHelper->jsonEncode($this->getRegions());
    }

    public function getRegions()
    {
        /** @var \Netbaseteam\Onestepcheckout\Model\ResourceModel\Region\Collection $collection */
        $collection = $this->regionsFactory->create();

        return $collection->fetchRegions();
    }

    public function getDefaultShippingMethod()
    {
        return $this->scopeConfig->getValue(
            'netbaseteam_onestepcheckout/default_values/shipping_method',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getDefaultPaymentMethod()
    {
        return $this->scopeConfig->getValue(
            'netbaseteam_onestepcheckout/default_values/payment_method',
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getDefaultAddress()
    {
        $data = $this->attributeMerger->getDefaultData();

        foreach ($data as $key => $value) {
            if (!$value || $value == 'null') {
                unset($data[$key]);
            }
        }

        return empty($data) ? null : $data;
    }

    /**
     * @return mixed
     */
    public function getAdditionalOptions()
    {
        return $this->scopeConfig->getValue(
            'netbaseteam_onestepcheckout/additional_options',
            ScopeInterface::SCOPE_STORE
        );
    }
}
