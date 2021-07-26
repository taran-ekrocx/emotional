<?php
namespace Ec\Qr\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade( SchemaSetupInterface $setup, ModuleContextInterface $context ) {
        $installer = $setup;

        $installer->startSetup();

        if(version_compare($context->getVersion(), '1.0.2', '<')) {
            if (!$installer->tableExists('ec_qr_order'))
            {
                $table = $installer->getConnection()->newTable(
                    $installer->getTable('ec_qr_order')
                )
                    ->addColumn(
                        'entity_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        [
                            'identity' => true,
                            'nullable' => false,
                            'primary'  => true,
                            'unsigned' => true,
                        ],
                        'Entity ID'
                    )
                    ->addColumn(
                        'order_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        [],
                        'Order Id'
                    )
                    ->addColumn(
                        'url',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        [],
                        'URL'
                    )
                    ->addColumn(
                        'qr',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        [],
                        'QR'
                    )
                    ->addColumn(
                        'created_at',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                        'Created At'
                    )->addColumn(
                        'updated_at',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                        'Updated At'
                    )
                    ->setComment('EC QR');
                $installer->getConnection()->createTable($table);
            }
        }

        if(version_compare($context->getVersion(), '1.0.3', '<')) {
            if ($installer->tableExists('ec_qr_order')) {
                $installer->getConnection()->addColumn(
                    $installer->getTable('ec_qr_order'),
                    'printed',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => true,
                        'comment' => 'Printed'
                    ]
                );
            }
        }

        if(version_compare($context->getVersion(), '1.0.4', '<')) {
            if ($installer->tableExists('ec_qr_configuration')) {
                $installer->getConnection()->changeColumn(
                      $installer->getTable('ec_qr_configuration'),
                      'value',
                      'value',
                      [
                          'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                          'length'   => '2M',
                      ]
                );
            }
        }

        $installer->endSetup();
    }
}
