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
use Magento\Backend\Model\Auth\Session;

class Save extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_adminSession;

    /**
     * @var \Mageplaza\GiftCard\Model\GiftCardFactory
     */
    protected $giftcardFactory;

    /**
     * @param Action\Context                      $context
     * @param \Magento\Backend\Model\Auth\Session $adminSession
     * @param \Mageplaza\GiftCard\Model\GiftCardFactory          $giftcardFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftcardFactory
    ) {
        parent::__construct($context);
        $this->_adminSession = $adminSession;
        $this->giftcardFactory = $giftcardFactory;
    }

    /**
     * Save giftcard record action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function random($length){
        $iString = 'ABCDEFGHIJKLMLOPQRSTUVXYZ0123456789';
        $oString = '';
        for($i = 0; $i < $length; $i++) {
            $charS = $iString[mt_rand(0, strlen($iString) - 1)];
            $oString .= $charS;
        }
        return $oString;
    }
    public function execute()
    {

        $postObj = $this->getRequest()->getPostValue();

        $date = date("Y-m-d");
        $username = $this->_adminSession->getUser()->getFirstname();
        $userDetail = ["name" => $username, "created_at" => $date];
        $data = array_merge($postObj, $userDetail);
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->giftcardFactory->create();
            $id = $postObj['giftcard_id'] ?? null;
            if ($id) {
                // EDIT GIFT CARD
                if(!$model->load($id)->getData()){
                    $this->messageManager->addWarning(__('Gift Card not found!'));
                    return $resultRedirect->setPath('*/*/');
                }
                try {
                    $model->addData(['balance'=> $data['balance']])->save();
                    $this->messageManager->addSuccess(__('The data has been saved.'));
                    $this->_adminSession->setFormData(false);
                    if ($this->getRequest()->getParam('back')) {
                        return $resultRedirect->setPath('giftcard/*/edit', ['id' => $model->getId(), '_current' => true]);
                    }
                    return $resultRedirect->setPath('*/*/');
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Something went wrong while saving the data.'));
                }
            } else {
                //CREATE GIFT CARD
                //auto generate code => code
                    $codeLength = $this->random($postObj['code_length']);
                    $model->addData([
                        'code'=> $codeLength,
                        'balance' => $data['balance'],
                        'create_from' => 'admin'
                    ])->save();
                    $this->messageManager->addSuccess(__('The data has been saved.'));
                    return $resultRedirect->setPath('*/*/');
            }
            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
