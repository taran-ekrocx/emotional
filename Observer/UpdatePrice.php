<?php
namespace Ec\Qr\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class UpdatePrice implements ObserverInterface
{

    protected $configFactory;

    public function __construct(
        \Ec\Qr\Model\ConfigFactory $configFactory
    ){
        $this->configFactory = $configFactory;
    }


    public function execute(Observer $observer)
    {
        $product = $observer->getProduct();
        if ($product->getSku() !== 'ec-qr-product') {
            return;
        }
        $price = $product->getPrice();

        $priceConfig = $this->configFactory->create()->load('price', 'name');

        if (!$priceConfig->getId()) {
            return;
        }

        $priceConfig->setValue($price);
        $priceConfig->save();
    }
}
