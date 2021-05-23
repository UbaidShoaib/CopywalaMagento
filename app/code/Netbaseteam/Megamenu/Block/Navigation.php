<?php

namespace Netbaseteam\Megamenu\Block;

use \Magento\Store\Model\StoreManagerInterface;

/**
 * Megamenu content block
 */
class Navigation extends \Magento\Framework\View\Element\Template
{
	/**
     * Megamenu collection
     *
     * @var Netbaseteam\Megamenu\Model\ResourceModel\Megamenu\Collection
     */
    protected $_megamenuCollection = null;
    
    protected $_storeManager;
    /**
     * Megamenu factory
     *
     * @var \Netbaseteam\Megamenu\Model\MegamenuFactory
     */
    protected $_megamenuCollectionFactory;
    
    /** @var \Netbaseteam\Megamenu\Helper\Data */
    protected $_dataHelper;

	protected $_categoryHelper;
	protected $_productFactory;
	protected $_objectManagerr;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Netbaseteam\Megamenu\Model\ResourceModel\Megamenu\CollectionFactory $megamenuCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Netbaseteam\Megamenu\Model\ResourceModel\Megamenu\CollectionFactory $megamenuCollectionFactory,
		\Magento\Catalog\Helper\Category $categoryHelper,
        \Netbaseteam\Megamenu\Helper\Data $dataHelper,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		StoreManagerInterface $storeManager,
        array $data = []
    ) {
    	$this->_storeManager = $storeManager;
        $this->_megamenuCollectionFactory = $megamenuCollectionFactory;
        $this->_dataHelper = $dataHelper;
		$this->_categoryHelper = $categoryHelper;
		$this->_productFactory = $productFactory;
		$this->_objectManagerr = \Magento\Framework\App\ObjectManager::getInstance();
		
        parent::__construct(
            $context,
            $data
        );
    }
    
    /**
     * Retrieve megamenu collection
     *
     * @return Netbaseteam\Megamenu\Model\ResourceModel\Megamenu\Collection
     */
    protected function _getCollection()
    {
        $collection = $this->_megamenuCollectionFactory->create();
        return $collection;
    }
    
    /**
     * Retrieve prepared megamenu collection
     *
     * @return Netbaseteam\Megamenu\Model\ResourceModel\Megamenu\Collection
     */
    public function getCollection()
    {
        if (is_null($this->_megamenuCollection)) {
            $this->_megamenuCollection = $this->_getCollection();
        }
        return $this->_megamenuCollection;
    }
	
	public function _getTopLabel($cat_id, $pid, $lbl_type){
		$menu = $this->getCollection();
		foreach($menu as $m) {
			if($m->getCategoryId() == $cat_id) {
				if($lbl_type == \Netbaseteam\Megamenu\Model\Label::lblHot) {
					$p_ids = explode(",", $m->getTopHotProducts());
				} else if($lbl_type == \Netbaseteam\Megamenu\Model\Label::lblNew) {
					$p_ids = explode(",", $m->getTopNewProducts());
				} else if($lbl_type == \Netbaseteam\Megamenu\Model\Label::lblSale) {
					$p_ids = explode(",", $m->getTopSaleProducts());
				}
				break;
			}
		}
		if(in_array($pid, $p_ids)) return true;
		return;
	}
	public function getCustomContentMenu()
	{
		$ContentMenuIds = [];
		$menu = $this->getCollection();
		foreach($menu as $m) {
			if($m->getCategoryId() == "0") {
				array_push($ContentMenuIds,$m->getMegamenuId());
			}
		}
		return $ContentMenuIds;
	}
	
	public function _getVerLabel($cat_id, $pid, $lbl_type){
		$mnColection = $this->getCollection();
		
		$value = "";
        foreach ($mnColection as $m) {
			
			if(($m->getCategoryId() == $cat_id && $m->getMegamenuId() != $cat_id) || ($m->getCategoryId() != $cat_id && $m->getMegamenuId() == $cat_id)){
				if($lbl_type == \Netbaseteam\Megamenu\Model\Label::lblHot) {
					$p_ids = explode(",", $m->getLeftHotProducts());
				} else if($lbl_type == \Netbaseteam\Megamenu\Model\Label::lblNew) {
					$p_ids = explode(",", $m->getLeftNewProducts());
				} else if($lbl_type == \Netbaseteam\Megamenu\Model\Label::lblSale) {
					$p_ids = explode(",", $m->getLeftSaleProducts());
				}
				break;
			}
		}
		if(in_array($pid, $p_ids)) return true;
		return;
	}
	
	public function getMenuField($field_name, $cat_id)
    {
		$mnColection = $this->getCollection();
		
		$value = "";
        foreach ($mnColection as $menu) {
			
			if(($menu->getCategoryId() == $cat_id && $menu->getMegamenuId() != $cat_id) || ($menu->getCategoryId() != $cat_id && $menu->getMegamenuId() == $cat_id)){
				switch ($field_name) {
					case \Netbaseteam\Megamenu\Model\Fields::top_label:
						$value = $menu->getTopLabel();
						if($value == "new") {
							$value = $this->getViewFileUrl('Netbaseteam_Megamenu/images/icon_new.png');
						}
						if($value == "hot") {
							$value = $this->getViewFileUrl('Netbaseteam_Megamenu/images/icon_hot.png');
						}
						if($value == "sale") {
							$value = $this->getViewFileUrl('Netbaseteam_Megamenu/images/icon_sale.png');
						}
						break;
					
					case \Netbaseteam\Megamenu\Model\Fields::top_block_top:
						$value = $menu->getTopBlockTop();
						break;
					
					case \Netbaseteam\Megamenu\Model\Fields::top_block_bottom:
						$value = $menu->getTopBlockBottom();
						break;
					
					case \Netbaseteam\Megamenu\Model\Fields::top_block_left:
						$value = $menu->getTopBlockLeft();
						break;
						
					case \Netbaseteam\Megamenu\Model\Fields::top_block_right:
						$value = $menu->getTopBlockRight();
						break;
					
					case \Netbaseteam\Megamenu\Model\Fields::top_sku:
						$value = $menu->getTopSku();
						break;
					
					case \Netbaseteam\Megamenu\Model\Fields::top_label_container:
						$value = $menu->getTopLabelContainer();
						break;
						
					case \Netbaseteam\Megamenu\Model\Fields::top_left_block_sku:
						$value = $menu->getTopLeftBlockSku();
						break;
					
					case \Netbaseteam\Megamenu\Model\Fields::top_left_sku_title:
						$value = $menu->getTopLeftSkuTitle();
						break;
						
					case \Netbaseteam\Megamenu\Model\Fields::top_right_block_sku:
						$value = $menu->getTopRightBlockSku();
						break;
						
					case \Netbaseteam\Megamenu\Model\Fields::top_right_sku_title:
						$value = $menu->getTopRightSkuTitle();
						break;
						
					case \Netbaseteam\Megamenu\Model\Fields::left_label:
						$value = $menu->getLeftLabel();
						if($value == "new") {
							$value = $this->getViewFileUrl('Netbaseteam_Megamenu/images/icon_new.png');
						}
						if($value == "hot") {
							$value = $this->getViewFileUrl('Netbaseteam_Megamenu/images/icon_hot.png');
						}
						if($value == "sale") {
							$value = $this->getViewFileUrl('Netbaseteam_Megamenu/images/icon_sale.png');
						}
						break;
					
					case \Netbaseteam\Megamenu\Model\Fields::left_cat_icon:
						$value = $menu->getLeftCatIcon();
						break;
					
					case \Netbaseteam\Megamenu\Model\Fields::left_block_top:
						$value = $menu->getLeftBlockTop();
						break;
					
					case \Netbaseteam\Megamenu\Model\Fields::left_block_left:
						$value = $menu->getLeftBlockLeft();
						break;
					
					case \Netbaseteam\Megamenu\Model\Fields::left_left_sku:
						$value = $menu->getLeftLeftSku();
						break;
					
					case \Netbaseteam\Megamenu\Model\Fields::left_block_right:
						$value = $menu->getLeftBlockRight();
						break;
						
					case \Netbaseteam\Megamenu\Model\Fields::left_right_sku:
						$value = $menu->getLeftRightSku();
						break;
						
					case \Netbaseteam\Megamenu\Model\Fields::left_sku:
						$value = $menu->getLeftSku();
						break;
					
					case \Netbaseteam\Megamenu\Model\Fields::top_content_type:
						$value = $menu->getTopContentType();
						break;
					
					case \Netbaseteam\Megamenu\Model\Fields::top_content_block:
						$value = $menu->getTopContentBlock();
						break;
						
					case \Netbaseteam\Megamenu\Model\Fields::top_pgrid_box_title:
						$value = $menu->getTopPgridBoxTitle();
						break;
						
					case \Netbaseteam\Megamenu\Model\Fields::top_pgrid_products:
						$value = $menu->getTopPgridProducts();
						break;
					
					case \Netbaseteam\Megamenu\Model\Fields::top_pgrid_num_columns:
						$value = $menu->getTopPgridNumColumns();
						break;
						
					case \Netbaseteam\Megamenu\Model\Fields::top_pgrid_cats:
						$value = $menu->getTopPgridCats();
						break;
						
					case \Netbaseteam\Megamenu\Model\Fields::left_content_block:
						$value = $menu->getLeftContentBlock();
						break;
					
					case \Netbaseteam\Megamenu\Model\Fields::left_content_type:
						$value = $menu->getLeftContentType();
						break;
					
					case \Netbaseteam\Megamenu\Model\Fields::left_pgrid_box_title:
						$value = $menu->getLeftPgridBoxTitle();
						break;
					
					case \Netbaseteam\Megamenu\Model\Fields::left_pgrid_products:
						$value = $menu->getLeftPgridProducts();
						break;
					
					case \Netbaseteam\Megamenu\Model\Fields::left_pgrid_num_columns:
						$value = $menu->getLeftPgridNumColumns();
						break;
						
					case \Netbaseteam\Megamenu\Model\Fields::left_pgrid_cats:
						$value = $menu->getLeftPgridCats();
						break;
						
					case \Netbaseteam\Megamenu\Model\Fields::position:
						$value = $menu->getPosition();
						break;
					
					case \Netbaseteam\Megamenu\Model\Fields::left_block_bottom:
						$value = $menu->getLeftBlockBottom();
						break;
					
					case \Netbaseteam\Megamenu\Model\Fields::left_label_container:
						$value = $menu->getLeftLabelContainer();
						break;	

					case \Netbaseteam\Megamenu\Model\Fields::left_left_sku_title:
						$value = $menu->getLeftLeftSkuTitle();
						break;
					
					case \Netbaseteam\Megamenu\Model\Fields::left_right_sku_title:
						$value = $menu->getLeftRightSkuTitle();
						break;	
				}
			}
		}
        return $value;
    }
	
	public function _getCatUrl($category){
		return $this->_categoryHelper->getCategoryUrl($category);
	}

	public function getcusLinkUrl($category){
		$mnColection = $this->getCollection();
		
		$value = "";
        foreach ($mnColection as $menu) {
			if(($menu->getCategoryId() == $category && $menu->getMegamenuId() != $category) || ($menu->getCategoryId() != $category && $menu->getMegamenuId() == $category)){
				$value = $menu->getLink();
				return $value;
			}
		}
	}

	public function getMenuName($category){
		$mnColection = $this->getCollection();
		
		$value = "";
        foreach ($mnColection as $menu) {
			if($menu->getMegamenuId() == $category){
				$value = $menu->getMenuName();
				return $value;
			}
		}
	}

	public function getCurrentStoreId(){
        return $this->_storeManager->getStore()->getId();
    }

	public function getStoreIds($category){
		$mnColection = $this->getCollection();
		
		$value = "";
        foreach ($mnColection as $menu) {
			if(($menu->getCategoryId() == $category && $menu->getMegamenuId() != $category) || ($menu->getCategoryId() != $category && $menu->getMegamenuId() == $category)){
				$value = $menu->getStoreIds();
				return $value;
			}
		}
	}

	public function getImageUrl($category){
		$mnColection = $this->getCollection();
		$value = "";
        foreach ($mnColection as $menu) {
			if(($menu->getCategoryId() == $category && $menu->getMegamenuId() != $category) || ($menu->getCategoryId() != $category && $menu->getMegamenuId() == $category)){
				$value = $menu->getIcon();
				return $value;
			}
		}
	}

	public function getIconselect($category){
		$mnColection = $this->getCollection();
		$value = "";
        foreach ($mnColection as $menu) {
			if(($menu->getCategoryId() == $category && $menu->getMegamenuId() != $category) || ($menu->getCategoryId() != $category && $menu->getMegamenuId() == $category)){
				$value = $menu->getIconselectClass();
				return $value;
			}
		}
	}

	public function _getCustomUrl($category){
		return $this->_categoryHelper->getCategoryUrl($category);
	}
	
	public function getSubCategories($cat_id){
		$categoryFactory = $this->_objectManagerr->create('Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
		
		$categories = $categoryFactory->create()                              
					->addAttributeToSelect('*')
					->addFieldToFilter("parent_id", $cat_id)
					->addFieldToFilter("is_active", 1)
					->addFieldToFilter("include_in_menu", 1)
					->setOrder('position', 'ASC')
					;		
		return $categories;
	}
	
	public function getHtmlContent($htmlContent){
		return $this->_objectManagerr->get('Magento\Framework\Filter\Template')->getTemplateProcessor()->filter($htmlContent);
	}
	
	public function _getProductData($sku){
		$product = $this->_productFactory->create();
		return $product->loadByAttribute('sku', $sku);
	}
	
	public function _loadProduct($id){
		$product = $this->_productFactory->create();
		return $product->load($id);
	}
	
	public function _loadCat($id){
		$categoryFactory = $this->_objectManagerr->get('Magento\Catalog\Model\CategoryFactory');
		$category = $categoryFactory->create();  
		return $category->load($id);
	}
	
	public function getMy(){
		return "test here";
	}
	
	public function showPrice($price){
		return $this->_objectManagerr->get('\Magento\Framework\Pricing\Helper\Data')->currency($price, true, false);
	}
	
	public function _getAddToCartUrl($product){
		$postDataHelper = $this->_objectManagerr->get('Magento\Framework\Data\Helper\PostHelper');
		$postData = $postDataHelper->getPostData($this->_objectManagerr->get('Magento\Checkout\Helper\Cart')->getAddUrl($product), ['product' => $product->getEntityId()]);
		return $postData;
	}
}
