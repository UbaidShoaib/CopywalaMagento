<?php
/*
* author: Netbase
*/
namespace Netbase\Product\Block;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
use Magento\Catalog\Model\Product\Type;
use Magento\Downloadable\Model\Product\Type as DownloadableType;
use Magento\GroupedProduct\Model\Product\Type\Grouped as GroupedType;



class Featured extends \Magento\Framework\View\Element\Template
{
    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * Product Status
     *
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    private $productStatus;

    /**
     * Product Visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    private $productVisibility;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * ImageFactory
     *
     * @var \Magento\Catalog\Helper\ImageFactory
     */
    private $imageHelper;

    /**
     * Path to template file in theme.
     *
     * @var string
     */
   
    public function __construct(

        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Helper\ImageFactory $imageHelper,
        array $data = []
    ) {

        $this->productCollectionFactory = $productCollectionFactory;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
        $this->storeManager = $storeManager;
        $this->imageHelper = $imageHelper;
        parent::__construct($context, $data);
    }

    public function getProductCollection($cateId = null){
       $collection = $this->productCollectionFactory->create();
        // get all the product's attributes
        $collection = $collection->addAttributeToSelect('*');
        // filtering the products by status
        $collection = $collection->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()]);
        // filtering the products by visibility
        $collection = $collection->addAttributeToFilter('visibility', ['in' => $this->productVisibility->getVisibleInSiteIds()]);
        // filtering the products by the type id
        $collection = $collection->addAttributeToFilter('type_id', ['in' => $this->getProductTypeId()]);
        $collection = $collection->addAttributeToFilter('nb_featured', ["eq" => 1]);
        // filtering the products by the current store view
        $collection = $collection->addStoreFilter($this->storeManager->getStore()->getStoreId());
        $collection = $collection->setPageSize(4);
        // set the current page
        $collection = $collection->setCurPage(1);
        $collection = $collection->addAttributeToSort('name', 'ASC');
        return $collection;
    }

    public function getRatingSummary($product)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $reviewFactory = $objectManager->create('Magento\Review\Model\Review');

        $storeId = $this->_storeManager->getStore(true)->getId();
        $reviewFactory->getEntitySummary($product, $storeId);

        $ratingSummary = $product->getRatingSummary()->getRatingSummary();
        return $ratingSummary;
    }

    protected function getProductTypeId()
    {
        return [
            ConfigurableType::TYPE_CODE,
            Type::TYPE_SIMPLE,
            Type::TYPE_BUNDLE,
            Type::TYPE_VIRTUAL,
            DownloadableType::TYPE_DOWNLOADABLE,
            GroupedType::TYPE_CODE
        ];
    }
}
?>