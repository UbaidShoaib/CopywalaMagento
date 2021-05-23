<?php

namespace Netbaseteam\PricingOption\Block\Adminhtml\Option\Edit\Tab;

/**
 * Cms page edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
    protected $_productloader;
    protected $_optionCollectionFactory;
    protected $_resourceModel;

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
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Netbaseteam\PricingOption\Model\ResourceModel\Option\CollectionFactory $optionCollectionFactory,
        \Netbaseteam\PricingOption\Model\ResourceModel\Option $resourceModel,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_productloader = $_productloader;
        $this->_optionCollectionFactory = $optionCollectionFactory;
        $this->_resourceModel = $resourceModel;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /* @var $model \Netbaseteam\PricingOption\Model\PricingOption */
        $model = $this->_coreRegistry->registry('current_option');

        $params = $this->getRequest()->getParams();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('pricingoption_main_');

        $fieldset = $form->addFieldset('base_fieldset', ['class' => 'fieldset-wide']);

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'pricingoption_id']);
        }

        $renderer = $this->getLayout()->createBlock(
            'Netbaseteam\PricingOption\Block\Adminhtml\Option\Edit\Renderer'
        )->setTemplate(
            'Netbaseteam_PricingOption::option/views/options/edit-option.phtml'
        );
        $fieldset->setRenderer($renderer);
        
        $this->_eventManager->dispatch('adminhtml_pricingoption_edit_tab_main_prepare_form', ['form' => $form]);

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
        return __('General');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('General');
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