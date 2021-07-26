<?php
namespace Ec\Qr\Controller\Adminhtml\Config;

class Install extends \Magento\Backend\App\Action
{

    /**
    * @var \Magento\Framework\View\Result\PageFactory
    */
    protected $resultPageFactory;

    /**
     * Product Model
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Config Factory
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $configFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Ec\Qr\Model\ConfigFactory $configFactory
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
        $this->messageManager = $messageManager;
        $this->configFactory = $configFactory;
    }

    public function execute()
    {
        $post = $this->getRequest()->getPostValue();

        $key = $post['key'];
        $secret = $post['secret'];

        $domain = $post['domain'];
        $domain = str_replace('http://', '', $domain);
        $domain = str_replace('https://', '', $domain);
        $domain = str_replace('www.', '', $domain);
        $domain = explode('.', $domain);
        $post['domain'] = $domain[0];

        $keyModel = $this->configFactory->create();
        $keyModel->setData(
            [
                'name' => 'key',
                'value' => $key,
            ]
        );
        $keyModel->save();

        $secretModel = $this->configFactory->create();
        $secretModel->setData(
            [
                'name' => 'secret',
                'value' => $secret,
            ]
        );
        $secretModel->save();

        $domainModel = $this->configFactory->create();
        $domainModel->setData(
            [
                'name' => 'domain',
                'value' => $post['domain'],
            ]
        );
        $domainModel->save();

        $resultRedirect = $this->resultRedirectFactory->create();
        $url = $this->_url->getUrl('ecqr/config');

        $this->messageManager->addSuccess(__("Module Installed"));
        return $resultRedirect->setPath($url);
    }

}
