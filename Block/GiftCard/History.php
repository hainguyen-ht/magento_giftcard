<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\GiftCard\Block\GiftCard;

use \Magento\Framework\App\ObjectManager;

/**
 * Sales order history block
 *
 * @api
 * @since 100.0.2
 */
class History extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Mageplaza\GiftCard\Model\ResourceModel\History\CollectionFactory $historyCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Mageplaza\GiftCard\Model\ResourceModel\History\CollectionFactory $historyCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\ResourceConnection $Resource,
        array $data = []
    ) {
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->_customerSession = $customerSession;
        parent::__construct($context, $data);
    }
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
    }

    public function getModels(){
        $model = $this->historyCollectionFactory->create();
        return $model->join(
            [
                'c' => 'mageplaza_giftcard_code'
            ],
            'main_table.giftcard_id=c.giftcard_id'
        )->getData();
    }
//    public function getGiftCardF(){
//        $code = $this->_giftCardFactory->create();
//        $collection = $code->getCollection();
//        foreach($collection as $item){
//            echo "<pre>";
//            print_r($item->getData());
//            echo "</pre>";
//        }
//    }
    /**
     * Get Pager child block output
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
