<?php
namespace Ec\Qr\Controller\Adminhtml\Order;

/**
 * Controller class for the images upload form
 */
class Printqr extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        $layout = $this->resultPageFactory->create();
        $layout->getLayout()->getUpdate()->removeHandle('default');
        return $layout;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ec_Qr::config_child_admin');
    }

}
