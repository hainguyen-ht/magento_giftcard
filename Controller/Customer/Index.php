<?php
namespace Mageplaza\GiftCard\Controller\Customer;

use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action {
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customer,
        \Magento\Framework\App\Response\Http $response,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
    )
    {
        $this->customer = $customer;
        $this->redirectFactory = $redirectFactory;
        $this->response = $response;
        $this->redirect = $redirect;
        parent::__construct($context);
    }
    public function getCustomerID(){
        return $this->customer->getCustomer()->getId();
    }
    public function redirect(){
        return $this->redirect->redirect($this->response, 'customer/account/login');
    }
    public function execute() {
        if(!$this->getCustomerID()){
            return $this->redirect();
        }
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
?>
