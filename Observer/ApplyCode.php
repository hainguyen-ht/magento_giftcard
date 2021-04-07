<?php
namespace Mageplaza\GiftCard\Observer;
class ApplyCode implements \Magento\Framework\Event\ObserverInterface
{
    public function __construct(
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftcardFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url
    )
    {
        $this->giftcardFactory = $giftcardFactory;
        $this->checkoutSession = $checkoutSession;
        $this->messageManager = $messageManager;
        $this->actionFlag = $actionFlag;
        $this->responseFactory = $responseFactory;
        $this->url = $url;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $code = $observer->getControllerAction()->getRequest()->getParam('coupon_code');
        $actionS = $observer->getControllerAction()->getRequest()->getPostValue('remove');
//        var_dump($observer->getControllerAction()->getRequest()->getPostValue());
//        var_dump($observer->getControllerAction());
//        die();
        $giftcard = $this->giftcardFactory->create()->getCollection();
        if($actionS == 0){
            foreach ($giftcard as $item){
                if($item->getData()['code'] == $code){
                    $this->checkoutSession->setCode($code);
                    $this->messageManager->addSuccess('Gift code applied successfully');
                    $this->actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
                }
            }
        }else if($actionS == 1){
            $this->checkoutSession->setCode(null);
            $this->messageManager->addWarning('You cancel coupon code!');
            $this->actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
        }
        $redirectionUrl = $this->url->getUrl('checkout/cart/index/');
        return $observer->getControllerAction()->getResponse()->setRedirect($redirectionUrl);
    }
}
