<?php
/*
* author: Netbase
*/
namespace Netbase\Product\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\DataObject\IdentityInterface;


class NewProduct extends \Magento\Framework\View\Element\Template
{
    protected $_productCollectionFactory;
    protected $_categoryModel;
    protected $_storeManager;
    protected $_catalogProductVisibility;
    protected $_localeDate;
    protected $_date;
    protected $_resource;
    protected $_NetbaseProductHelper;
    protected $_reviewFactory;
    protected $_coreRegistry;
    protected $listProductBlock;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, ///add injector
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        // \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\ResourceConnection $resource,
        \Netbase\Product\Helper\Data $NetbaseProductHelper,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Catalog\Block\Product\ListProduct $listProductBlock,
        \Magento\Catalog\Block\Product\Context $gridcontext,
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_storeManager = $context->getStoreManager();
        $this->_date = $date;
        $this->_resource = $resource;
        $this->_NetbaseProductHelper = $NetbaseProductHelper;
        $this->_reviewFactory = $reviewFactory;
        $this->_coreRegistry = $registry;
        $this->listProductBlock = $listProductBlock;
        $this->_compareProduct = $gridcontext->getCompareProduct();
        $this->_wishlistHelper = $gridcontext->getWishlistHelper();
        parent::__construct($context, $data);
    }

    /*public function getProductCollection($cateId = null){
        $todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');

        $allcategoryproduct = $this->_categoryFactory->create()->load($cateId)->getProductCollection()->addAttributeToSelect('*');

        $allcategoryproduct->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds())
        ->addAttributeToSelect('*')
        ->addStoreFilter()
        ->addAttributeToFilter(
            'news_from_date',
            [
                'or' => [
                    0 => ['date' => true, 'to' => $todayEndOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
            )->addAttributeToFilter(
                'news_to_date',
                [
                    'or' => [
                        0 => ['date' => true, 'from' => $todayStartOfDayDate],
                        1 => ['is' => new \Zend_Db_Expr('not null')],
                    ]
                ],
                'left'
            )->addAttributeToFilter(
                [
                    ['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
                    ['attribute' => 'news_to_date', 'is' => new \Zend_Db_Expr('not null')],
                ]
            )->addAttributeToSort(
                'news_from_date',
                'desc'
            )
        ->setPageSize(isset($config['pagesize'])?$config['pagesize']:12)
        ->setCurPage(isset($config['curpage'])?$config['curpage']:1)
        ->getSelect()->group("e.entity_id");

        return $allcategoryproduct;
    }*/

    public function getRatingSummary($product)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $reviewFactory = $objectManager->create('Magento\Review\Model\Review');

        $storeId = $this->_storeManager->getStore(true)->getId();
        $reviewFactory->getEntitySummary($product, $storeId);

        $ratingSummary = $product->getRatingSummary()->getRatingSummary();
        return $ratingSummary;
    }

    public function getProductDetailsHtml(\Magento\Catalog\Model\Product $product)
    {
        $renderer = $this->getDetailsRenderer($product->getTypeId());
        if ($renderer) {
            $renderer->setProduct($product);
            return $renderer->toHtml();
        }
        return '';
    }

    public function getDetailsRenderer($type = null)
    {
        if ($type === null) {
            $type = 'default';
        }
        $rendererList = $this->getDetailsRendererList();
        if ($rendererList) {
            return $rendererList->getRenderer($type, 'default');
        }
        return null;
    }

    protected function getDetailsRendererList()
    {
        return $this->getDetailsRendererListName() ? $this->getLayout()->getBlock(
            $this->getDetailsRendererListName()
        ) : $this->getChildBlock(
            'homepage.toprenderers'
        );
    }
    public function getProductPricetoHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType = null
    ) {
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');
        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product
            );
        }
        return $price;
    }

    public function getCategory($categoryId)
    {
        $category = $this->_categoryFactory->create();
        $category->load($categoryId);
        return $category;
    }

    public function getProductCollection($categoryId)
    {
        $products = $this->getCategory($categoryId)->getProductCollection();
        $products->addAttributeToSelect('*');
        return $products;
    }

    public function getAddToCartPostParams($product)
    {
        return $this->listProductBlock->getAddToCartPostParams($product);
    }
}
