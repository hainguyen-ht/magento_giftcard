<?php
namespace Mageplaza\GiftCard\Plugin;

class Coupons{
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession
    )
    {
        $this->checkoutSession= $checkoutSession;
    }
    public function afterGetCouponCode(\Magento\Checkout\Block\Cart\Coupon $subject){
        if($code = $this->checkoutSession->getCode()) {
            return $code;
        }
    }
}
?>
