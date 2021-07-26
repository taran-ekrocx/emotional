<?php
namespace Ec\Qr\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class CartRemoveItem implements ObserverInterface
{

    protected $checkoutSession;
    protected $ecOrder;
    protected $apiHelper;

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Ec\Qr\Model\EcOrderFactory $ecOrder,
        \Ec\Qr\Helper\Api $apiHelper
    ){
        $this->checkoutSession = $checkoutSession;
        $this->ecOrder = $ecOrder;
        $this->apiHelper = $apiHelper;
    }


    public function execute(Observer $observer)
    {
        $item = $observer->getEvent()->getQuoteItem();

        $qrFile = $this->checkoutSession->getData('ec_qr');

        if ($item->getSku() === 'ec-qr-product' && $qrFile) {
            unlink($qrFile);
            $this->checkoutSession->setData('ec_qr', 0);
        }
    }
}
