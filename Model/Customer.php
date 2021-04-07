<?php
namespace Mageplaza\GiftCard\Model;

class Customer extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'customer_entity';

    protected $_cacheTag = 'customer_entity';

    protected $_eventPrefix = 'customer_entity';

    protected function _construct()
    {
        $this->_init('Mageplaza\GiftCard\Model\ResourceModel\Customer');
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
