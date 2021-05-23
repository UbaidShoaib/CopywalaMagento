<?php
/**
* @copyright Copyright (c) 2016 www.cmsmart.net
 */
namespace Netbaseteam\PricingOption\Model;


class Option extends \Magento\Framework\Model\AbstractModel
{
    /**
     * CMS page cache tag
     */
    const CACHE_TAG = 'pricingoption_option';

    /**
     * @var string
     */
    protected $_cacheTag = 'pricingoption_option';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'pricingoption_option';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->_resourceConnection = $resourceConnection;
        $this->dateTime = $dateTime;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Netbaseteam\PricingOption\Model\ResourceModel\Option');
    }

    public function getProductOption($product_id) {
        $option_id = '';
        $resource = $this->_resourceConnection;
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('netbaseteam_pricingoption_option');
        $sql = "Select id, priority, apply_for, product_ids, product_cats, date_from, date_to FROM " . $tableName . " where `published`= 1";

        $options = $connection->fetchAll($sql);

        if($options){
            $_options = array();
            foreach( $options as $option ){
                $execute_option = true;
                $from_date = false;
                if( isset($option['date_from']) ){
                    $from_date = empty( $option['date_from'] ) ? false : $this->dateTime->gmtTimestamp($option['date_from']);
                }
                $to_date = false;
                if( isset($option['date_to']) ){
                    $to_date = empty( $option['date_to'] ) ? false : $this->dateTime->gmtTimestamp($option['date_to']);
                }
                $now  = $this->dateTime->gmtTimestamp();
                if ( $from_date && $to_date && !( $now >= $from_date && $now <= $to_date ) ) {
                                $execute_option = false;
                } elseif ( $from_date && !$to_date && !( $now >= $from_date ) ) {
                                $execute_option = false;
                } elseif ( $to_date && !$from_date && !( $now <= $to_date ) ) {
                                $execute_option = false;
                }
                if( $execute_option ){
                    if( $option['apply_for'] == 'p' ){
                        $products = unserialize($option['product_ids']);
                        if (!empty($products)) {
                            $execute_option = in_array($product_id, $products) ? true : false;
                        } else {
                            $execute_option = false;
                        }
                    }
                }
                if( $execute_option ){
                    $_options[] = $option;
                }
            }
            $_options = array_reverse( $_options );
            $option_priority = 0;
            foreach( $_options as $_option ){
                if( $_option['priority'] > $option_priority ){
                    $option_priority = $_option['priority'];
                    $option_id = $_option['id'];
                }
            }
        }
        return $option_id;
    }

    public function getOption( $optionId ){
        $storeId = $this->_storeManager->getStore()->getId();
        $resource = $this->_resourceConnection;
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('nb_pricingoption_store');
        $sql = "SELECT * FROM " . $tableName." where option_id = ".$optionId." and store_id = ".$storeId;

        $result = $connection->fetchAll($sql);
        if (empty($result)) {
            $storeId = 0;
            $sql = "SELECT * FROM " . $tableName." where option_id = ".$optionId." and store_id = ".$storeId;
            $result = $connection->fetchAll($sql);
        }
        return count($result[0]) ? $result[0] : false;
    }
}
