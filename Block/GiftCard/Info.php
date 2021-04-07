<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\GiftCard\Block\GiftCard;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Dashboard Customer Info
 *
 * @api
 * @since 100.0.2
 */
class Info extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * Constructor
     * @param \Mageplaza\GiftCard\Model\ResourceModel\Customer\CollectionFactory $customerFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Mageplaza\GiftCard\Model\ResourceModel\Customer\CollectionFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,

        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
        $this->scopeConfig = $scopeConfig;


        parent::__construct($context, $data);
    }
    public function getCustomerID(){
        return $this->customerSession->getCustomer()->getId();
    }
    public function getCustomers(){
        return $model = $this->customerFactory->create();
    }
    public function isEnableConfig(){
        return $this->scopeConfig->getValue('giftcard/general/enable_giftcard',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getFormAction()
    {
        return $this->getUrl('giftcard/customer/save', ['_secure' => true]);
    }



}
