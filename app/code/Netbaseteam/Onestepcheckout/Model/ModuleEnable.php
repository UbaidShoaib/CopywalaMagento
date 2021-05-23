<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Model;

class ModuleEnable
{
    const TIG_POSTNL_MODULE_NAMESPACE = 'TIG_PostNL';

    const NETBASETEAM_STOCKSTATUS_MODULE_NAMESPACE = 'Netbaseteam_Stockstatus';

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    public function __construct(\Magento\Framework\Module\Manager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    /**
     * @return bool
     */
    public function isPostNlEnable()
    {
        return $this->moduleManager->isEnabled(self::TIG_POSTNL_MODULE_NAMESPACE);
    }

    /**
     * @return bool
     */
    public function isStockStatusEnable()
    {
        return $this->moduleManager->isEnabled(self::NETBASETEAM_STOCKSTATUS_MODULE_NAMESPACE);
    }
}
