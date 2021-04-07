<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Created By : Rohan Hapani
 */
namespace Mageplaza\DevelopmentGuide\Controller\Adminhtml\Grid;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

/**
 * Edit form controller
 */
class Edit extends \Magento\Backend\App\Action
{

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
     * @var \Mageplaza\DevelopmentGuide\Model\BlogFactory
     */
    protected $blogFactory;

    /**
     * @param Action\Context                 $context
     * @param \Magento\Framework\Registry    $registry
     * @param \Magento\Backend\Model\Session $adminSession
     * @param \Mageplaza\DevelopmentGuide\Model\BlogFactory     $blogFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\Session $adminSession,
        \Mageplaza\DevelopmentGuide\Model\BlogFactory $blogFactory
    ) {
        $this->_coreRegistry = $registry;
        $this->adminSession = $adminSession;
        $this->blogFactory = $blogFactory;
        parent::__construct($context);
    }

    /**
     * @return boolean
     */
    protected function _isAllowed()
    {
        return true;
    }

    /**
     * Add blog breadcrumbs
     *
     * @return $this
     */
    protected function _initAction()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Mageplaza_DevelopmentGuide::grid')->addBreadcrumb(__('Blog'), __('Blog'))->addBreadcrumb(__('Manage Blog'), __('Manage Blog'));
        return $resultPage;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->blogFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This blog record no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $data = $this->adminSession->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->_coreRegistry->register('development_guide_form_data', $model);

        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb($id ? __('Edit Post') : __('New Blog'), $id ? __('Edit Post') : __('New Blog'));
        $resultPage->getConfig()->getTitle()->prepend(__('Grids'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getTitle() : __('New Blog'));

        return $resultPage;
    }
}
