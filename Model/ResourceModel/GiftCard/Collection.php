<?php
namespace Mageplaza\GiftCard\Model\ResourceModel\GiftCard;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'giftcard_id';
    protected $_eventPrefix = 'mageplaza_giftcard_code_collection';
    protected $_eventObject = 'code_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mageplaza\GiftCard\Model\GiftCard', 'Mageplaza\GiftCard\Model\ResourceModel\GiftCard');
    }

}
