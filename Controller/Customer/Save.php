<?php
namespace Mageplaza\GiftCard\Controller\Customer;
use Magento\Framework\App\Action\Context;

class Save extends \Magento\Framework\App\Action\Action {
    protected $_customerSession;
    function __construct(
        Context $context,
        \Mageplaza\GiftCard\Model\HistoryFactory $historyCollectionFactory,
        \Mageplaza\GiftCard\Model\CustomerFactory $customerFactory,
        \Mageplaza\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory $giftCardCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Response\Http $response,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        array $data = []
    )
    {
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->customerFactory = $customerFactory;
        $this->giftCardCollectionFactory = $giftCardCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->response = $response;
        $this->redirect = $redirect;
        parent::__construct($context, $data);
    }
    public function redirect(){
        return $this->redirect->redirect($this->response, 'customer/account/login');
    }
    public function getModelHistory(){
        return $modelHistory = $this->historyCollectionFactory->create();
    }
    public function getModelCustomer(){
        return $modelCustomer = $this->customerFactory->create();
    }
    public function getModelGiftCard(){
       return  $modelGiftCard = $this->giftCardCollectionFactory->create();
    }
    //Get ID customer
    public function getCustomerID(){
        return $this->_customerSession->getCustomer()->getId();
    }
    //get Redeem form
    public function getRedeem(){
        $dataRequest = $this->getRequest()->getPost();
        return $redeem = $dataRequest['redeem'];
    }
    public function execute()
    {
        if(!$this->getCustomerID()){
            return $this->redirect();
        }
        $code = $this->getRedeem();
        $giftcards = $this->getModelGiftCard()->getData();
        $resultRedirect = $this->resultRedirectFactory->create();
        $giftcard = null;
        // GET DATA BY GIFT CARD return giftcard
        foreach ($giftcards as $item){
            if($item['code'] == $code){
                $giftcard = $item;
            }
        }
        if($giftcard === null){
            $this->messageManager->addError(__('Code do not exist'));
            return $resultRedirect->setPath('*/*/index');
        }
        foreach ($this->getModelHistory()->getCollection()->getData() as $history){
            if($giftcard['giftcard_id'] == $history['giftcard_id'] && $this->getCustomerID() == $history['customer_id']){
                $this->messageManager->addWarning(__('Code have used!'));
                return $resultRedirect->setPath('*/*/index');
            }
        }
        if($this->getModelHistory()->addData(
            [
                'giftcard_id' => $giftcard['giftcard_id'],
                'customer_id' => $this->getCustomerID(),
                'amount' => $giftcard['balance'],
                'action' => 'redeem'
            ])->save()){
            $this->getModelCustomer()
                ->load($this->getCustomerID())
                ->addData([
                    'giftcard_balance'
                    => $this->getModelCustomer()
                            ->load($this->getCustomerID())
                            ->getData()['giftcard_balance'] + $giftcard['balance']
                ])
                ->save();
            $this->messageManager->addSuccess(__('Redeem Code success'));
            return $resultRedirect->setPath('*/*/index');
        }
    }
}

