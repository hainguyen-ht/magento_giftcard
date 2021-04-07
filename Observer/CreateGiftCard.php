<?php
namespace Mageplaza\GiftCard\Observer;

class CreateGiftCard implements \Magento\Framework\Event\ObserverInterface
{
    protected $order;
    public function __construct(
        \Mageplaza\GiftCard\Model\OrderFactory $orderCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Sales\Model\Order\ItemFactory $itemFactory,
        \Magento\Catalog\Model\ProductFactory $productloader,
        \Mageplaza\GiftCard\Model\HistoryFactory $historyFactory,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftcardFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->customerSession = $customerSession;
        $this->_pageFactory = $pageFactory;
        $this->itemFactory = $itemFactory;
        $this->productloader = $productloader;
        $this->historyFactory = $historyFactory;
        $this->giftcardFactory = $giftcardFactory;
        $this->orderRepository = $orderRepository;
        $this->scopeConfig = $scopeConfig;
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
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customerID = $this->getCustomerID();
        $orderByCustomer = array();
        // GET ORDER BY CUSTOMER
        foreach ($this->getModelOrder()->getCollection() as $item){
            if($item['customer_id'] == $customerID){
                $orderByCustomer[] = $item->getData();
            }
        }
        // GET NEW ORDER
        $newOrder = $orderByCustomer['0'];
        foreach ($orderByCustomer as $item){
            if($newOrder['entity_id'] < $item['entity_id']){
                $newOrder = $item;
            }
        }
        // GET NEW ORDER ID
        $newOrderID = $newOrder['entity_id'];
        // GET ID PRODUCT BY ORDER-ITEM
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
        //GET AMOUNT GIFTCARD
        $giftcardAmount = $this->productloader->create()->load(2050)->getData('giftcard_amount');
        //GET INCREMENT ID
        $incrementID = $this->orderRepository->get($newOrderID)->getIncrementId();
        // Create Gift Code
        $createGiftCard = $this->getModelGiftCard()->addData([
            'code' => $this->random($this->getCodeLength()),
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
    }
}
