<?php
/**
 * @author Netbaseteam Team
 * @copyright Copyright (c) 2018 Cmsmart (http://www.cmsmart.net)
 * @package Netbaseteam_Onestepcheckout
 */


namespace Netbaseteam\Onestepcheckout\Block\Adminhtml\Field\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var GroupFactory
     */
    protected $groupFactory;
    /**
     * @var \Netbaseteam\Onestepcheckout\Model\Field
     */
    protected $fieldSingleton;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Netbaseteam\Onestepcheckout\Block\Adminhtml\Field\Edit\GroupFactory $groupFactory,
        \Netbaseteam\Onestepcheckout\Model\Field $fieldSingleton,
        array $data
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->groupFactory = $groupFactory;
        $this->fieldSingleton = $fieldSingleton;
    }

    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('*/*/save', ['_current' => true]),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
                ],
            ]
        );
        $form->setHtmlIdPrefix('field_');
        $form->setUseContainer(true);
        
        $visible = $this->addGroup(
            $form,
            'visible_fields',
            __('Enabled Checkout Fields'),
            1
        );
        
        $invisible = $this->addGroup(
            $form,
            'invisible_fields',
            __('Disabled Checkout Fields'),
            0
        );

        $storeId = $this->_request->getParam('store', null);

        /** @var \Netbaseteam\Onestepcheckout\Model\Field $field */
        foreach ($this->fieldSingleton->getConfig($storeId) as $field) {
            $targetGroup = $field->getData('enabled') ? $visible : $invisible;

            $targetGroup->addRow('field_' . $field->getData('attribute_id'), ['field' => $field]);
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }

    public function addGroup(\Magento\Framework\Data\Form $form, $id, $title, $enabled)
    {
        /** @var \Netbaseteam\Onestepcheckout\Block\Adminhtml\Field\Edit\Group $group */
        $group = $this->groupFactory->create();
        $group->setId($id);
        $group->setRenderer(\Netbaseteam\Onestepcheckout\Block\Adminhtml\Field\Edit\Group::getGroupRenderer());
        $group->setData('title', $title);
        $group->setData('enabled', $enabled);

        $form->addElement($group);

        return $group;
    }

    protected function _prepareLayout()
    {
        \Netbaseteam\Onestepcheckout\Block\Adminhtml\Field\Edit\Group::setRowRenderer(
            $this->getLayout()->createBlock(
                'Netbaseteam\Onestepcheckout\Block\Adminhtml\Field\Edit\Group\Row\Renderer',
                $this->getNameInLayout() . '_row_element'
            )
        );

        \Netbaseteam\Onestepcheckout\Block\Adminhtml\Field\Edit\Group::setGroupRenderer(
            $this->getLayout()->createBlock(
                'Netbaseteam\Onestepcheckout\Block\Adminhtml\Field\Edit\Group\Renderer',
                $this->getNameInLayout() . '_group_element'
            )
        );

        return parent::_prepareLayout();
    }
}
