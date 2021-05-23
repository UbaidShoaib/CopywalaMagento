<?php

namespace Netbase\Sun\Plugin;


class ConfigPlugin
{
    public function aroundSave(
        \Magento\Config\Model\Config $subject,
        \Closure $proceed
    ) {
        return $proceed();
    }
}