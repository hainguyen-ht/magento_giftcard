<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Created By : Rohan Hapani
 */
namespace Mageplaza\GiftCard\Controller\Adminhtml\Code;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
/**
 * Edit form controller
 */
class Create extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Mageplaza_GiftCard::giftcard_id';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $adminSession;

    /**
     * @var \Mageplaza\GiftCard\Model\GiftCardFactory
     */
    protected $giftcardFactory;

    /**
     * @param Action\Context                 $context
     * @param \Magento\Framework\Registry    $registry
     * @param \Magento\Backend\Model\Session $adminSession
     * @param \Mageplaza\GiftCard\Model\GiftCardFactory     $giftcardFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\Session $adminSession,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftcardFactory
    ) {
        $this->_coreRegistry = $registry;
        $this->adminSession = $adminSession;
        $this->giftcardFactory = $giftcardFactory;
        parent::__construct($context);
    }

    /**
     * @return boolean
     */
//    protected function _isAllowed()
//    {
//        return true;
//    }

    /**
     * Add giftcard breadcrumbs
     *
     * @return $this
     */
//    protected function _initAction()
//    {
//        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
//        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
//        $resultPage->setActiveMenu('Mageplaza_GiftCard::giftcard')->addBreadcrumb(__('GiftCard'), __('GiftCard'))->addBreadcrumb(__('Manage GiftCard'), __('Manage GiftCard'));
//        return $resultPage;
//    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $this->_forward('edit');

    }
}
