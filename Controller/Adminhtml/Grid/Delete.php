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
use Magento\Backend\App\Action\Context;

/**
 * Delete Controller
 */
class Delete extends \Magento\Backend\App\Action
{

    /**
     * @var \Mageplaza\DevelopmentGuide\Model\BlogFactory
     */
    protected $blogFactory;

    /**
     * @param Context                    $context
     * @param \Mageplaza\DevelopmentGuide\Model\BlogFactory $blogFactory
     */
    public function __construct(
        Context $context,
        \Mageplaza\DevelopmentGuide\Model\BlogFactory $blogFactory
    ) {
        parent::__construct($context);
        $this->blogFactory = $blogFactory;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mageplaza_DevelopmentGuide::view');
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->blogFactory->create();
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('The post has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/index', ['id' => $id]);
            }
        }
        $this->messageManager->addError(__('We can\'t find a post to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
