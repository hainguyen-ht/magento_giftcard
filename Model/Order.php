<?php
namespace Mageplaza\GiftCard\Model;

class Order extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'sales_order';

    protected $_cacheTag = 'sales_order';

    protected $_eventPrefix = 'sales_order';

    protected function _construct()
    {
        $this->_init('Mageplaza\GiftCard\Model\ResourceModel\Order');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}
