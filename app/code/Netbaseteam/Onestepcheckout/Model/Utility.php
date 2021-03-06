<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Model;

class Utility
{
    /**
     * The method inserts a new array before some key
     *
     * @param array  $origin
     * @param string $wantedKey
     * @param array  $insert
     *
     * @return array
     */
    public function arrayInsertBeforeKey(
        $origin = [],
        $wantedKey = '',
        $insert = []
    ) {
        $availabelKeys = array_keys($origin);
        $index = array_search($wantedKey, $availabelKeys);
        $position = $index ?: count($origin);
        $derivative = array_merge(
            array_slice($origin, 0, $position), $insert,
            array_slice($origin, $position)
        );

        return $derivative;
    }
}
