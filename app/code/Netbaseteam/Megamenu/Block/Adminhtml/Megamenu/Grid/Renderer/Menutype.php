<?php
namespace Netbaseteam\Megamenu\Block\Adminhtml\Megamenu\Grid\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
//use Magento\Framework\Object;

class Menutype extends AbstractRenderer
{
    public function render(\Magento\Framework\DataObject $row)
    {
		$msg_type1 = "";
		$msg_type2 = "";
		$objectManagerr = \Magento\Framework\App\ObjectManager::getInstance();
		$menuFactory = $objectManagerr->get('Netbaseteam\Megamenu\Model\MegamenuFactory');
		$menuLoad = $menuFactory->create();  
		/* \zend_debug::dump($row->getCategoryId());
		\zend_debug::dump($menuLoad->load($row->getId())->getData());
		*/
		$menuID = $row->getId();
		$possition = $row->getPosition();
		
		$top_type = $menuLoad->load($menuID)->getTopContentType();
		$ver_type = $menuLoad->load($menuID)->getLeftContentType();
		
		$top_label = $menuLoad->load($menuID)->getTopLabel();
		$left_label = $menuLoad->load($menuID)->getLeftLabel();
		
		/* label */
		if($top_label == "new") {
			$top_label = $this->getViewFileUrl('Netbaseteam_Megamenu/images/icon_new.png');
		}
		if($top_label == "hot") {
			$top_label = $this->getViewFileUrl('Netbaseteam_Megamenu/images/icon_hot.png');
		}
		if($top_label == "sale") {
			$top_label = $this->getViewFileUrl('Netbaseteam_Megamenu/images/icon_sale.png');
		}
		
		if($left_label == "new") {
			$left_label = $this->getViewFileUrl('Netbaseteam_Megamenu/images/icon_new.png');
		}
		if($left_label == "hot") {
			$left_label = $this->getViewFileUrl('Netbaseteam_Megamenu/images/icon_hot.png');
		}
		if($left_label == "sale") {
			$left_label = $this->getViewFileUrl('Netbaseteam_Megamenu/images/icon_sale.png');
		}
		/* end left label */
		
		if($possition == \Netbaseteam\Megamenu\Model\Position::top_menu
		|| $possition == \Netbaseteam\Megamenu\Model\Position::both){
			switch ($top_type) {
				case \Netbaseteam\Megamenu\Model\Contenttype::Default_Category_Listing:
					$msg_type1 = __('Default Category Listing');
					break;
					
				case \Netbaseteam\Megamenu\Model\Contenttype::Dynamic_Category_Listing:
					$msg_type1 = __('Dynamic Category Listing');
					break;
				
				case \Netbaseteam\Megamenu\Model\Contenttype::Static_Category_Listing:
					$msg_type1 = __('Static Category Listing');
					break;
				
				case \Netbaseteam\Megamenu\Model\Contenttype::Product_Grid:
					$msg_type1 = __('Product Grid');
					break;
					
				case \Netbaseteam\Megamenu\Model\Contenttype::Product_Listing:
					$msg_type1 = __('Product Listing');
					break;
					
				case \Netbaseteam\Megamenu\Model\Contenttype::Product_Listing_Category:
					$msg_type1 = __('Dynamic Product Listing by Category');
					break;
				
				case \Netbaseteam\Megamenu\Model\Contenttype::Contact:
					$msg_type1 = __('Content');
					break;
			}
		}	
		
		if($possition == \Netbaseteam\Megamenu\Model\Position::left_menu
			|| $possition == \Netbaseteam\Megamenu\Model\Position::both ){
			switch ($ver_type) {
				case \Netbaseteam\Megamenu\Model\Contenttype::Default_Category_Listing:
					$msg_type2 = __('Default Category Listing');
					break;
					
				case \Netbaseteam\Megamenu\Model\Contenttype::Dynamic_Category_Listing:
					$msg_type2 = __('Dynamic Category Listing');
					break;
				
				case \Netbaseteam\Megamenu\Model\Contenttype::Static_Category_Listing:
					$msg_type2 = __('Static Category Listing');
					break;
				
				case \Netbaseteam\Megamenu\Model\Contenttype::Product_Grid:
					$msg_type2 = __('Product Grid');
					break;
					
				case \Netbaseteam\Megamenu\Model\Contenttype::Product_Listing:
					$msg_type2 = __('Product Listing');
					break;
					
				case \Netbaseteam\Megamenu\Model\Contenttype::Product_Listing_Category:
					$msg_type2 = __('Dynamic Product Listing by Category');
					break;
				
				case \Netbaseteam\Megamenu\Model\Contenttype::Contact:
					$msg_type2 = __('Content');
					break;
			}
		}	
		$msg1="";$msg2="";
		
		if($top_label != ""){
			$top_label = '<img style="margin-right: 5px" src="'.$top_label.'">';
		}
		
		if($left_label != ""){
			$left_label = '<img style="margin-right: 5px" src="'.$left_label.'">';
		}
		
        if($msg_type1 != "") $msg1 = $top_label."Menu Type Top: <b>".$msg_type1."</b><br />";
		if($msg_type2 != "") $msg2 = $left_label."Menu Type Left: <b>".$msg_type2."</b>";
		return $msg1.$msg2;
	}
}
