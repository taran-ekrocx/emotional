<?php
namespace Ec\Qr\Block\Adminhtml;

/**
 * Block for the images upload form
 */
class Config extends \Magento\Backend\Block\Template
{

    /**
     * Product repository API interface
     *
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $context;

    /**
     * Product repository API interface
     *
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $formKey;

    /**
     * Product repository API interface
     *
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    public $coreSessions;

    /**
     * ConfigFactory
     *
     * @var \Ec\Qr\Model\ConfigFactory
     */
    protected $configFactory;

    /**
     * apiHelper
     *
     * @var \Ec\Qr\Helper\Api
     */
    protected $apiHelper;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $pageFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Ec\Qr\Model\ConfigFactory $configFactory,
        \Ec\Qr\Helper\Api $apiHelper,
        \Magento\Cms\Model\PageFactory $pageFactory
    ) {
        $this->context = $context;
        $this->formKey = $formKey;
        $this->coreSession = $coreSession;
        $this->configFactory = $configFactory;
        $this->apiHelper = $apiHelper;
        $this->pageFactory = $pageFactory;

        parent::__construct($context);
    }

    /**
     * Returns the form action url
     *
     * @return array
     */
    public function getActionUrl()
    {
        $url = $this->context->getUrlBuilder();

        return $url->getUrl('ecqr/config/save');
    }

    /**
     * Returns the form action url
     *
     * @return array
     */
    public function getInstallActionUrl()
    {
        $url = $this->context->getUrlBuilder();

        return $url->getUrl('ecqr/config/install');
    }


    /**
     * Generates and returns a form key
     *
     * @return array
     */
    public function getFormKey()
    {
         return $this->formKey->getFormKey();
    }

    public function getConfig()
    {
          $configFactory = $this->configFactory->create();
          $collection = $configFactory->getCollection();

          $configData = [
              'key' => false,
              'secret' => false,
              'domain' => false,
              'campaign' => false,
              'template' => false,
              'price' => false,
              'width' => 720,
              'height' => 720,
              'title' => __('Add Video'),
              'subtitle' => false,
              'button-title' => __('Add Video'),
              'button-color' => false,
              'button-background' => false,
              'enabled' => 0,
              'qr-title' => __('Scan the QR to see the <br /> Emotional Message'),
          ];

          foreach ($collection as $config) {
              $configData[$config->getName()] = $config->getValue();
          }

          return $configData;
    }

    public function getCampaigns($key, $secret, $domain)
    {
        $campaigns = $this->apiHelper->getCampaigns($key, $secret, $domain);

        if (!$campaigns['success']) {
            return false;
        }

        return $campaigns;
    }

    public function getPages()
    {
        $page = $this->pageFactory->create();
        return $page->getCollection();
    }

}
