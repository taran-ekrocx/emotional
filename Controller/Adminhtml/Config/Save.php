<?php
namespace Ec\Qr\Controller\Adminhtml\Config;

class Save extends \Magento\Backend\App\Action
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
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    protected $directoryList;

    protected $file;

    protected $moduleReader;

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
        \Ec\Qr\Model\ConfigFactory $configFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\Module\Dir\Reader $moduleReader
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
        $this->messageManager = $messageManager;
        $this->configFactory = $configFactory;
        $this->productRepository = $productRepository;
        $this->directoryList = $directoryList;
        $this->file = $file;
        $this->moduleReader = $moduleReader;
    }

    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        $configFactory = $this->configFactory->create();
        $collection = $configFactory->getCollection();

        if (isset($post['price'])) {
            $ecProduct = $this->productRepository->get('ec-qr-product');
            $ecProduct->setPrice($post['price']);

            if($ecProduct->getData('image') === 'no_selection' || !$ecProduct->getData('image')) {
                $tmpDir = $this->getMediaDirTmpDir();
                $this->file->checkAndCreateFolder($tmpDir);
                $newFileName = $tmpDir . 'product-image.jpg';
                $img = $this->moduleReader->getModuleDir(
                    \Magento\Framework\Module\Dir::MODULE_VIEW_DIR,
                    'Ec_Qr'
                ) . DIRECTORY_SEPARATOR . 'frontend' . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'product-image.jpg';
                $this->file->read($img, $newFileName);

                $ecProduct->addImageToMediaGallery(
                    $newFileName,
                    ['image', 'small_image', 'thumbnail'],
                    true,
                    true
                );
            }

            $this->productRepository->save($ecProduct);
        }

        $domain = $post['domain'];
        $domain = str_replace('http://', '', $domain);
        $domain = str_replace('https://', '', $domain);
        $domain = str_replace('www.', '', $domain);
        $domain = explode('.', $domain);
        $post['domain'] = $domain[0];

        unset($post['form_key']);
        foreach ($collection as $config) {
            if (isset($post[$config->getName()])) {
                $config->setValue($post[$config->getName()]);
                $config->save();
                unset($post[$config->getName()]);
            }
        }


        foreach($post as $key => $config) {
            $configModel = $this->configFactory->create();
            $configModel->setData(
                [
                    'name' => $key,
                    'value' => $config,
                ]
            );
            $configModel->save();
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $url = $this->_url->getUrl('ecqr/config');

        $this->messageManager->addSuccess(__("Module Updated"));
        return $resultRedirect->setPath($url);
    }

    protected function getMediaDirTmpDir()
    {
        return $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA) . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
    }

}
