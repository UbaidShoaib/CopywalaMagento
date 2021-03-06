<?php
namespace Netbaseteam\Megamenu\Block\Adminhtml\Megamenu\Edit\Tab;

/**
 * Cms page edit form main tab
 */
class Horizontal extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
	protected $_megaHelper;
	/**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
		\Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
		$this->_wysiwygConfig = $wysiwygConfig;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('megamenu');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Netbaseteam_Megamenu::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('megamenu_horizontal_');

        $fieldset = $form->addFieldset('horizontal_fieldset', ['legend' => __('Horizontal Information')]);

        if ($model->getId()) {
            $fieldset->addField('megamenu_id', 'hidden', ['name' => 'megamenu_id']);
        }
		
		$objectManagerr = \Magento\Framework\App\ObjectManager::getInstance();
		
		/* $layoutFieldset = $form->addFieldset(
            'default_fieldset',
            ['legend' => __('Default Category'), 'class' => 'fieldset-wide', 'disabled' => $isElementDisabled]
        ); */
		
		$labelFactory = $objectManagerr->create('\Netbaseteam\Megamenu\Model\Label');
		$fieldset->addField(
			'top_label',
			'select',
			[
				'name' => 'top_label',
				'label' => __('Category Label Type'),
				'title' => __('Category Label Type'),
				'values' => $labelFactory->getLabel(),
			]
		);
		
		$typeFactory = $objectManagerr->create('\Netbaseteam\Megamenu\Model\Contenttype');
		$fieldset->addField(
			'top_content_type',
			'select',
			[
				'name' => 'top_content_type',
				'label' => __('Menu Content Type'),
				'title' => __('Menu Content Type'),
				'values' => $typeFactory->getTypes(),
			]
		);
		
		$wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);

		$layoutTop = $form->addFieldset(
            'top_fieldset',
            ['legend' => __('Top Container'), 'class' => 'fieldset-wide', 'disabled' => $isElementDisabled]
        );
		
        $contentField = $layoutTop->addField(
            'top_block_top',
            'editor',
            [
                'name' => 'top_block_top',
				'label' => __('Top Block'),
				'title' => __('Top Block'),
                'style' => 'height:18em;',
                'disabled' => $isElementDisabled,
                'config' => $wysiwygConfig
            ]
        );
		
		$layoutLeft = $form->addFieldset(
            'left_fieldset',
            ['legend' => __('Left Container'), 'class' => 'fieldset-wide', 'disabled' => $isElementDisabled]
        );
		
		$contentField = $layoutLeft->addField(
            'top_block_left',
            'editor',
            [
                'name' => 'top_block_left',
				'label' => __('Left Block'),
				'title' => __('Left Block'),
                'style' => 'height:18em;',
                'disabled' => $isElementDisabled,
				/* 'note'	 => 'If block is product feature, please enter by format: [sku][title block][your_product_sku][sku]', */
                'config' => $wysiwygConfig
            ]
        );
		
		/* if ($model->getTopLeftSkuTitle() || $model->getTopLeftBlockSku()) {
			$fieldset->addField(
				'feature_1',
				'note',
				[
					'name' => 'feature_1',
					'label' => __('Left Block SKU'),
					'title' => __('Left Block SKU'),
					'text'	=> __('Title: ').$model->getTopLeftSkuTitle() . __('. SKU: ') . $model->getTopLeftBlockSku(),
					'disabled' => $isElementDisabled
				]
			);
		} */
		
		$field = $layoutLeft->addField(
            'top_left_sku_title',
            'text',
            [
                'name' => 'top_left_sku_title',
                'label' => __('Left SKU Title'),
                'title' => __('Left SKU Title'),
            ]
        );
		
		$field = $layoutLeft->addField(
            'top_left_block_sku',
            'text',
            [
                'name' => 'top_left_block_sku',
                'label' => __('Left SKU'),
                'title' => __('Left SKU'),
				'note'	=> __('If you push both: <b>Left Block</b> and <b>Left SKU</b>, the menu only show <b>Left SKU</b>'),
            ]
        );
		
		$layoutRight = $form->addFieldset(
            'right_fieldset',
            ['legend' => __('Right Container'), 'class' => 'fieldset-wide', 'disabled' => $isElementDisabled]
        );
		
		$contentField = $layoutRight->addField(
            'top_block_right',
            'editor',
            [
                'name' => 'top_block_right',
				'label' => __('Right Block'),
				'title' => __('Right Block'),
                'style' => 'height:18em;',
                'disabled' => $isElementDisabled,
				/* 'note'	 => 'If block is product feature, please enter by format: [sku][title block][your_product_sku][sku]', */
                'config' => $wysiwygConfig
            ]
        );
		
		/* if ($model->getTopRightSkuTitle() || $model->getTopRightBlockSku()) {
			$fieldset->addField(
				'feature_2',
				'note',
				[
					'name' => 'feature_2',
					'label' => __('Right Block SKU'),
					'title' => __('Right Block SKU'),
					'text'	=> __('Title: ').$model->getTopRightSkuTitle() . __('. SKU: ') . $model->getTopRightBlockSku(),
					'disabled' => $isElementDisabled
				]
			);
		} */
		
		$field = $layoutRight->addField(
            'top_right_sku_title',
            'text',
            [
                'name' => 'top_right_sku_title',
                'label' => __('Right SKU Title'),
                'title' => __('Right SKU Title'),
            ]
        );
		
		$field = $layoutRight->addField(
            'top_right_block_sku',
            'text',
            [
                'name' => 'top_right_block_sku',
                'label' => __('Right SKU'),
                'title' => __('Right SKU'),
				'note'	=> __('If you push both: <b>Right Block</b> and Right SKU, the menu only show <b>Right SKU</b>'),
            ]
        );
		
		$layoutBottom = $form->addFieldset(
            'bottom_fieldset',
            ['legend' => __('Bottom Container'), 'class' => 'fieldset-wide', 'disabled' => $isElementDisabled]
        );
		
		$contentField = $layoutBottom->addField(
            'top_block_bottom',
            'editor',
            [
                'name' => 'top_block_bottom',
				'label' => __('Bottom Block'),
				'title' => __('Bottom Block'),
                'style' => 'height:18em;',
                'disabled' => $isElementDisabled,
                'config' => $wysiwygConfig
            ]
        );
		
		$layoutSpec = $form->addFieldset(
            'spec_fieldset',
            ['legend' => __('Spectical Product Container'), 'class' => 'fieldset-wide', 'disabled' => $isElementDisabled]
        );
		
		$layoutSpec->addField(
            'top_label_container',
            'text',
            [
                'name' => 'top_label_container',
                'label' => __('Title SKU Specical'),
                'title' => __('Title SKU Specical'),
                'disabled' => $isElementDisabled
            ]
        );
		
		$layoutSpec->addField(
            'top_sku',
            'text',
            [
                'name' => 'top_sku',
                'label' => __('SKU Specical'),
                'title' => __('SKU Specical'),
                'disabled' => $isElementDisabled
            ]
        );
		
		$fieldset->addField(
            'top_pgrid_num_columns',
            'text',
            [
                'name' => 'top_pgrid_num_columns',
                'label' => __('Number of Columns'),
                'title' => __('Number of Columns'),
                'disabled' => $isElementDisabled
            ]
        );
		
		$field = $fieldset->addField(
            'top_pgrid_cats',
            'text',
            [
                'name' => 'top_pgrid_cats',
                'label' => __('Categories'),
                'title' => __('Categories'),
            ]
        );
		
		$renderer = $this->getLayout()->createBlock(
			   'Netbaseteam\Megamenu\Block\Adminhtml\Form\Renderer\Topcategories'
		);
		$field->setRenderer($renderer);
		
		$fieldset->addField(
            'top_pgrid_box_title',
            'text',
            [
                'name' => 'top_pgrid_box_title',
                'label' => __('Products Box Title'),
                'title' => __('Products Box Title'),
                'disabled' => $isElementDisabled
            ]
        );
		
		$field = $fieldset->addField(
            'top_pgrid_products',
            'text',
            [
                'name' => 'top_pgrid_products',
                'label' => __('Products'),
                'title' => __('Products'),
            ]
        );
		$renderer = $this->getLayout()->createBlock(
			   'Netbaseteam\Megamenu\Block\Adminhtml\Form\Renderer\Topproducts'
		);
		 
		$field->setRenderer($renderer);
		
		$fieldset->addField(
            'top_hot_products',
            'text',
            [
                'name' => 'top_hot_products',
                'label' => __('+ Hot Products IDs (separate by comma): '),
                'title' => __('+ Hot Products IDs (separate by comma): '),
                'disabled' => $isElementDisabled
            ]
        );
		
		$fieldset->addField(
            'top_new_products',
            'text',
            [
                'name' => 'top_new_products',
                'label' => __('+ New Products IDs (separate by comma):'),
                'title' => __('+ New Products IDs (separate by comma):'),
                'disabled' => $isElementDisabled
            ]
        );
		
		$fieldset->addField(
            'top_sale_products',
            'text',
            [
                'name' => 'top_sale_products',
                'label' => __('+ Sale Products IDs (separate by comma):'),
                'title' => __('+ Sale Products IDs (separate by comma):'),
                'disabled' => $isElementDisabled
            ]
        );
		
		$contentField = $fieldset->addField(
            'top_content_block',
            'editor',
            [
                'name' => 'top_content_block',
				'label' => __('Static Block Content'),
				'title' => __('Static Block Content'),
                'style' => 'height:18em;',
                'disabled' => $isElementDisabled,
				/* 'note'	 => 'If block is product feature, please enter by format: [sku][title block][your_product_sku][sku]', */
                'config' => $wysiwygConfig
            ]
        );
		
        $this->_eventManager->dispatch('adminhtml_megamenu_edit_tab_main_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
		
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Horizontal Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Horizontal Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
