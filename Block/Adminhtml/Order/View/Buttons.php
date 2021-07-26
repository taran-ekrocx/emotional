<?php
namespace Ec\Qr\Block\Adminhtml\Order\View;

class Buttons extends \Magento\Sales\Block\Adminhtml\Order\View
{

    protected function _construct()
    {
        parent::_construct();

        if(!$this->getOrderId()) {
            return $this;
        }

        $buttonUrl = $this->_urlBuilder->getUrl(
            'ecqr/order/printqr',
            ['order_id' => $this->getOrderId()]
        );

        $this->addButton(
            'ecqr_print_button',
            ['label' => __('Print Qr'), 'onclick' => 'setLocation(\'' . $buttonUrl . '\')']
        );

        return $this;
    }

}
