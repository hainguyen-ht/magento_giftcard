<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Created By : Rohan Hapani
 */
namespace Mageplaza\GiftCard\Block\Adminhtml\Code\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
/**
 * Blog form block
 */
class Main extends Generic implements TabInterface
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_adminSession;

    /**
     * @var \Mageplaza\GiftCard\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param \Magento\Backend\Model\Auth\Session     $adminSession
     * @param \Mageplaza\GiftCard\Model\Status        $status
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Mageplaza\GiftCard\Model\Status $status,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->_adminSession = $adminSession;
        $this->_status = $status;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare the form.
     *
     * @return $this
     */

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('giftcard_form_data');

        $isElementDisabled = false;

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('GiftCard Information')]);

        if ($model->getId()) {
            $fieldset->addField(
                'giftcard_id',
                'hidden',
                [
                    'name' => 'giftcard_id',
                    'label' => __('ID'),
                    'title' => __('ID'),
                    'disabled' => false,
                ]
            );
        }

        if ($model->getId()) {
            $fieldset->addField(
                'code',
                'label',
                [
                    'name' => 'code',
                    'label' => __('Code'),
                    'title' => __('Code')
                ]
            );
        } else {
            $codeLength = $this->scopeConfig->getValue('giftcard/code_config/code_length',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $fieldset->addField(
                'code_length',
                'text',
                [
                    'name' => 'code_length',
                    'label' => __('Code Length'),
                    'title' => __('Code Length'),
                    'value' => $codeLength,
                    'disabled' => false,
                ]
            );
            $model->addData(['code_length'=> $codeLength]);

        }

        $fieldset->addField(
            'balance',
            'text',
            [
                'name' => 'balance',
                'label' => __('Balance'),
                'title' => __('Balance'),
                'required' => true,
                'disabled' => $isElementDisabled,
            ]
        );

        if ($model->getId()) {
            $fieldset->addField(
                'create_from',
                'label',
                [
                    'name' => 'create_from',
                    'label' => __('Created From'),
                    'title' => __('Created From'),
                    'disabled' => true,
                ]
            );
        }

        $form->addValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Return Tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('GiftCard Information');
    }

    /**
     * Return Tab title
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('GiftCard Information');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
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
