<?php
namespace Mageplaza\GiftCard\Controller\Index;
USE \Mageplaza\GiftCard\Model\GiftCardFactory;
class Test extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    protected $_giftCardFactory;
    public function __construct(
        \Mageplaza\GiftCard\Model\OrderFactory $orderCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Sales\Model\Order\ItemFactory $itemFactory,
        \Magento\Catalog\Model\ProductFactory $productloader,
        \Mageplaza\GiftCard\Model\HistoryFactory $historyFactory,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftcardFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        GiftCardFactory $GiftCardFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->customerSession = $customerSession;
        $this->_pageFactory = $pageFactory;
        $this->_giftCardFactory = $GiftCardFactory;
        $this->itemFactory = $itemFactory;
        $this->productloader = $productloader;
        $this->historyFactory = $historyFactory;
        $this->giftcardFactory = $giftcardFactory;
        $this->orderRepository = $orderRepository;
        $this->scopeConfig = $scopeConfig;
        return parent::__construct($context);
    }
    public function getCustomerID(){
        return $this->customerSession->getCustomer()->getId();
    }
    public function getModelOrder(){
        return $this->orderCollectionFactory->create();
    }
    public function getModelGiftCard(){
        return $this->giftcardFactory->create();
    }
    public function getModelHistory(){
        return $this->historyFactory->create();
    }
    protected function random($length){
        $iString = 'ABCDEFGHIJKLMLOPQRSTUVXYZ0123456789';
        $oString = '';
        for($i = 0; $i < $length; $i++) {
            $charS = $iString[mt_rand(0, strlen($iString) - 1)];
            $oString .= $charS;
        }
        return $oString;
    }
    public function getCodeLength(){
        return $this->scopeConfig->getValue('giftcard/code_config/code_length',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function execute()
    {
        $customerID = $this->getCustomerID();
        $orderByCustomer = array();
        $data = $this->getModelOrder()->getCollection();
        foreach ($data as $item){
            if($item['customer_id'] == $customerID){
                $orderByCustomer[] = $item->getData();
            }
        }
        // GET NEW ORDER
        $newOrder = $orderByCustomer['0'];
        // SORT
        foreach ($orderByCustomer as $item){
            if($newOrder['entity_id'] < $item['entity_id']){
                $newOrder = $item;
            }
        }
        // ID NEW ORDER
        $newOrderID = $newOrder['entity_id'];
        // GET ID PRODUCT BY PRODUCT LIST
        $itemOrder = array();
        $listItemOrder = $this->itemFactory->create()->getCollection();
        foreach ($listItemOrder as $item){
            if($item['order_id'] == $newOrderID){
                $itemOrder[] = $item['product_id'];
            }
        }
        // GET INFO PRODUCT
        $listProduct = array();
        $product = $this->productloader->create()->getCollection();
        foreach ($product as $item){
            foreach ($itemOrder as $orderID){
                if($item->getData()['entity_id'] == $orderID){
                    $listProduct[] = $item->getData();
                }
            }
        }
        $itemVirtual = array();
        foreach ($listProduct as $item){
            if($item['type_id'] == 'virtual'){
                $itemVirtual[] = $item;
            }
        }
        echo "<pre>";
        print_r($itemVirtual);
        echo "</pre>";
        die();
        //GET AMOUNT GIFTCARD
        $giftcardAmount = $this->productloader->create()->load(2050)->getData('giftcard_amount');
        //GET INCREMENT ID
        $incrementID = $this->orderRepository->get($newOrderID)->getIncrementId();
        $createGiftCard = $this->getModelGiftCard()->addData([
            'code' => $this->random(12),
            'balance' => $giftcardAmount,
            'create_from' => $incrementID
        ])->save();
        if($createGiftCard){
            $this->getModelHistory()->addData([
                'giftcard_id' => $createGiftCard->getId(),
                'customer_id' => $customerID,
                'amount' => $giftcardAmount,
                'action' => 'create'
            ])->save();
        }
        echo "<pre>";
        print_r($itemVirtual);
        echo "</pre>";
        die();










        $code = $this->_giftCardFactory->create();
        //        ADD
        $data = [
            'code' => 'LAP2020',
            'balance' => '200',
            'amount_used' => '4'
        ];
//        $this->insertData($code, $data);
        //        UPDATE
//        $this->updateData($code, 4);
        //        DELETE
//        $this->deleteData($code, 7);
        $collection = $code->getCollection();
        foreach($collection as $item){
            echo "<pre>";
            print_r($item->getData());
            echo "</pre>";
        }
        exit();
        return $this->_pageFactory->create();
    }
    public function insertData($code, $data){
        if(!$code->addData($data)->save()) return false;
        return true;
    }
    public function updateData($code, $giftcardID){
        if(!$code->load($giftcardID)->getData()){
            return false;
        }else{
            if($code->setData('code', 'GCUPDATED')->save()){
                return true;
            }else return false;
        }
    }
    public function deleteData($code, $giftcardID){
        if(!$code->load($giftcardID)->getData()){
            return false;
        }else{
            if($code->delete($giftcardID)){
                return true;
            }else return false;
        }
    }
}
