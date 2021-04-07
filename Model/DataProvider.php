<?php
namespace Mageplaza\GiftCard\Model;
use Mageplaza\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory;
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $giftcardCollectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $giftcardCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $giftcardCollectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        $this->loadedData = array();
        /** @var Customer $customer */
        foreach ($items as $giftcard) {
            $this->loadedData[$giftcard->getId()] = $giftcard->getData();
        }
        return $this->loadedData;

    }
}
