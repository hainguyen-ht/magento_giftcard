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
use Magento\Backend\App\Action\Context;

/**
 * Delete Controller
 */
class Delete extends \Magento\Backend\App\Action
{

    /**
     * @var \Mageplaza\GiftCard\Model\GiftCardFactory
     */
    protected $giftcardFactory;

    /**
     * @param Context                    $context
     * @param \Mageplaza\GiftCard\Model\GiftCardFactory $giftcardFactory
     */
    public function __construct(
        Context $context,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftcardFactory
    ) {
        parent::__construct($context);
        $this->giftcardFactory = $giftcardFactory;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mageplaza_GiftCard::giftcard');
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $postObj = $this->getRequest()->getPostValue();
        $id = $this->getRequest()->getParams('id')['id'] ?? null;
        $ids = $postObj['selected'] ?? null;
        $resultRedirect = $this->resultRedirectFactory->create();
        if($ids){
            foreach ($ids as $id){
                try {
                    $model = $this->giftcardFactory->create();
                    $model->load($id);
                    $model->delete();
                    $this->messageManager->addSuccess(__('The post has been deleted.'));
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    return $resultRedirect->setPath('*/*/index', ['id' => $id]);
                }
            }
            return $resultRedirect->setPath('*/*/');
        }

        if ($id) {
            try {
                $model = $this->giftcardFactory->create();
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
