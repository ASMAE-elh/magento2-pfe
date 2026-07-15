<?php
namespace Chaymae\OneStepCheckout\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;

class PlaceOrder extends Action implements HttpPostActionInterface, CsrfAwareActionInterface
{
    protected $jsonFactory;
    protected $checkoutSession;
    protected $cartManagement;
    protected $cartRepository;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        CheckoutSession $checkoutSession,
        CartManagementInterface $cartManagement,
        CartRepositoryInterface $cartRepository
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->checkoutSession = $checkoutSession;
        $this->cartManagement = $cartManagement;
        $this->cartRepository = $cartRepository;
        parent::__construct($context);
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    public function execute()
    {
        $result = $this->jsonFactory->create();
        try {
            $data = json_decode($this->getRequest()->getContent(), true);
            $quote = $this->checkoutSession->getQuote();

            $quote->setCustomerEmail($data['email']);
            $quote->setCustomerIsGuest(true);
            $quote->setCustomerFirstname($data['firstname']);
            $quote->setCustomerLastname($data['lastname']);

            $shippingAddress = $quote->getShippingAddress();
            $shippingAddress->setFirstname($data['firstname']);
            $shippingAddress->setLastname($data['lastname']);
            $shippingAddress->setStreet([$data['street']]);
            $shippingAddress->setCity($data['city']);
            $shippingAddress->setPostcode($data['postcode']);
            $shippingAddress->setCountryId($data['country_id']);
            $shippingAddress->setTelephone($data['telephone']);
            $shippingAddress->setEmail($data['email']);
            $shippingAddress->setShippingMethod($data['shipping_method']);
            $shippingAddress->setCollectShippingRates(true)->collectShippingRates();

            $billingAddress = $quote->getBillingAddress();
            $billingAddress->setFirstname($data['firstname']);
            $billingAddress->setLastname($data['lastname']);
            $billingAddress->setStreet([$data['street']]);
            $billingAddress->setCity($data['city']);
            $billingAddress->setPostcode($data['postcode']);
            $billingAddress->setCountryId($data['country_id']);
            $billingAddress->setTelephone($data['telephone']);
            $billingAddress->setEmail($data['email']);

            $quote->getPayment()->setMethod($data['payment_method']);
            $quote->setPaymentMethod($data['payment_method']);

            $this->cartRepository->save($quote);
            $orderId = $this->cartManagement->placeOrder($quote->getId());

            return $result->setData([
                'success' => true,
                'order_id' => $orderId,
                'redirect' => '/checkout/onepage/success/'
            ]);
        } catch (\Exception $e) {
            return $result->setData(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
