<?php
namespace Mageplaza\GiftCard\Model\ResourceModel\Order;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    protected $_eventPrefix = 'mageplaza_giftcard_order_collection';
    protected $_eventObject = 'order_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mageplaza\GiftCard\Model\Order', 'Mageplaza\GiftCard\Model\ResourceModel\Order');
    }

}
