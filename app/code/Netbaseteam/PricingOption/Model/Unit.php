<?php
namespace Netbaseteam\PricingOption\Model;

class Unit
{
	const cm	= "cm";
    const inch	= "inch";
    const mm	= "mm";
 
	public function getOptionArray()
    {
        return array(
			""				 => __('-- Please select unit --'),
            self::cm   => __('Cm'),
            self::inch  => __('Inch'),
            self::mm 		 => __('Mm')
        );
    }
	
    public function toOptionArray()
    {	
		return [
            ['value' => '', 'label' => __('-- Please select unit --')],
            ['value' => self::cm, 'label' => __('Cm')],
            ['value' => self::inch, 'label' => __('Inch')],
            ['value' => self::mm, 'label' => __('Mm')],
        ];
    }
}
