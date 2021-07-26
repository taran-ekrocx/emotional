<?php

namespace Ec\Qr\Setup;

use Magento\Cms\Model\PageFactory;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\State;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * Category setup factory
     *
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * EAV setup factory
     *
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Catalog Product
     *
     * @var \Magento\Catalog\Model\Product
     */
    private $product;

    /**
     * App State for Area Code
     *
     * @var \Magento\Framework\App\State
     **/
    private $state;

    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * Constructor
     *
     * @param CategorySetupFactory $categorySetupFactory
     * @param EavSetupFactory $eavSetupFactory
     * @param Product $product
     * @param State $state
     * @param PageFactory $pageFactory
     */
    public function __construct(
        CategorySetupFactory $categorySetupFactory,
        EavSetupFactory $eavSetupFactory,
        Product $product,
        State $state,
        PageFactory $pageFactory
    ) {
        $this->categorySetupFactory = $categorySetupFactory;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->product = $product;
        $this->state = $state;
        $this->pageFactory = $pageFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '1.0.0') < 0) {
            $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
            $attributeSetId = $categorySetup->getDefaultAttributeSetId(\Magento\Catalog\Model\Product::ENTITY);

            $this->product->setSku('ec-qr-product');
            $this->product->setName('Emotional Commerce Video');
            $this->product->setUrlKey('ec-qr-product');
            $this->product->setAttributeSetId($attributeSetId);
            $this->product->setStatus(1);
            $this->product->setVisibility(1);
            $this->product->setTaxClassId(0);
            $this->product->setTypeId('virtual');
            $this->product->setPrice(0);
            $this->product->setWebsiteIds([1]);

            $this->product->setStockData(
                [
                    'use_config_manage_stock' => 0,
                    'manage_stock' => 0,
                ]
            );

            $this->product->save();
        }
        if (version_compare($context->getVersion(), '1.0.1') < 0) {
        }
    }
}
