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
use Magento\Quote\Api\ShipmentEstimationInterface;
use Magento\Quote\Api\Data\AddressInterfaceFactory;

class ShippingMethods extends Action implements HttpPostActionInterface, CsrfAwareActionInterface
{
    protected $jsonFactory;
    protected $checkoutSession;
    protected $shipmentEstimation;
    protected $addressFactory;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        CheckoutSession $checkoutSession,
        ShipmentEstimationInterface $shipmentEstimation,
        AddressInterfaceFactory $addressFactory
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->checkoutSession = $checkoutSession;
        $this->shipmentEstimation = $shipmentEstimation;
        $this->addressFactory = $addressFactory;
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
            $address = $this->addressFactory->create();
            $address->setCountryId($data['country_id'] ?? 'MA');
            $address->setPostcode($data['postcode'] ?? '');
            $address->setCity($data['city'] ?? '');
            $methods = $this->shipmentEstimation->estimateByExtendedAddress($quote->getId(), $address);
            $output = [];
            foreach ($methods as $method) {
                $output[] = [
                    'carrier_code' => $method->getCarrierCode(),
                    'method_code' => $method->getMethodCode(),
                    'carrier_title' => $method->getCarrierTitle(),
                    'method_title' => $method->getMethodTitle(),
                    'amount' => $method->getAmount(),
                ];
            }
            return $result->setData(['success' => true, 'methods' => $output]);
        } catch (\Exception $e) {
            return $result->setData(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
