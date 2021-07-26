<?php
namespace Ec\Qr\Block\Adminhtml\Order;

/**
 * Block for the images upload form
 */
class Printqr extends \Magento\Backend\Block\Template
{

    /**
     * Product repository API interface
     *
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $context;

    /**
     * @var \Ec\Qr\Model\EcOrder
     */
    protected $ecOrderFactory;

    protected $request;

    protected $apiHelper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Ec\Qr\Model\EcOrderFactory $ecOrderFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Ec\Qr\Helper\Api $apiHelper
    ) {
        $this->context = $context;
        $this->ecOrderFactory = $ecOrderFactory;
        $this->request = $request;
        $this->apiHelper = $apiHelper;

        parent::__construct($context);
    }

    /**
     * Returns the form action url
     *
     * @return array
     */
    public function getTemplateHtml()
    {
        $orderId = $this->request->get('order_id');

        $ecOrder = $this->ecOrderFactory->create()->load($orderId, 'order_id');

        if (!$ecOrder->getId()) {
            return '<p>This order does not contain a QR Order</p>';
        }

        if (!$ecOrder->getPrinted()) {
            $response = $this->apiHelper->createEvent($ecOrder);
            if (!$response['success']) {
                return '<p>It seems Something went wrong, please try again or contact our support</p>';
            }
        }

        $config = $this->apiHelper->getConfig();

        $template = str_replace(
            '{{qr}}',
            '<span class="qr-image"><span class="qr-swoosh"></span><img width="'.$config['width'].'" src="'.$ecOrder->getQr().'" /></span>',
            $config['template']
        );
        $template = str_replace(
            '{{qr-title}}',
            '<span>'.$config['qr-title'].'</span>',
            $template
        );

        return $template;
    }

}
