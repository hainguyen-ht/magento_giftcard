<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageplaza\GiftCard\Model\Quote;

class Discount extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * Discount calculation object
     *
     * @var \Magento\SalesRule\Model\Validator
     */
    protected $calculator;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager = null;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\SalesRule\Model\Validator $validator
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\SalesRule\Model\Validator $validator,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftcardFactory,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->setCode('testdiscount');
        $this->eventManager = $eventManager;
        $this->calculator = $validator;
        $this->storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
        $this->giftcardFactory = $giftcardFactory;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Collect address discount amount
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    )
    {
        parent::collect($quote, $shippingAssignment, $total);
        $modelGiftCard = $this->giftcardFactory->create();
        $code = $this->checkoutSession->getCode();
        if ($code) {
            $amount = 0;
            foreach ($modelGiftCard->getCollection() as $item) {
                if($item->getData()['code'] == $code){
                    $amount = $item->getData()['balance'];
                }
            }
            if(($total->getData()['subtotal'] - $amount) <= 0){
                $amount = $total->getData()['subtotal'];
            }
            $address = $shippingAssignment->getShipping()->getAddress();
            $label = 'Gift Card';
            $discountAmount = 0 - $amount;
            $appliedCartDiscount = 0;
            if ($total->getDiscountDescription()) {
                // If a discount exists in cart and another discount is applied, the add both discounts.
                $appliedCartDiscount = $total->getDiscountAmount();
                $discountAmount = $total->getDiscountAmount() + $discountAmount;
                $label = $total->getDiscountDescription() . ', ' . $label;
            }
//            print_r($total->getData()['subtotal']);

            $total->setDiscountDescription($label);
            $total->setDiscountAmount($discountAmount);
            $total->setBaseDiscountAmount($discountAmount);
            $total->setSubtotalWithDiscount($total->getSubtotal() + $discountAmount);
            $total->setBaseSubtotalWithDiscount($total->getBaseSubtotal() + $discountAmount);
            if (isset($appliedCartDiscount)) {
                $total->addTotalAmount($this->getCode(), $discountAmount - $appliedCartDiscount);
                $total->addBaseTotalAmount($this->getCode(), $discountAmount - $appliedCartDiscount);
            } else {
                $total->addTotalAmount($this->getCode(), $discountAmount);
                $total->addBaseTotalAmount($this->getCode(), $discountAmount);
            }
            return $this;
        }
    }
    /**
     * Add discount total information to address
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array|null
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $result = null;
        $amount = $total->getDiscountAmount();

        // ONLY return 1 discount. Need to append existing
        //see app/code/Magento/Quote/Model/Quote/Address.php

        if ($amount != 0) {
            $description = $total->getDiscountDescription();
            $result = [
                'code' => $this->getCode(),
                'title' => strlen($description) ? __('Discount (%1)', $description) : __('Discount'),
                'value' => $amount
            ];
        }
        return $result;
    }
}
