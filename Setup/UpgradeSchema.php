<?php
namespace Mageplaza\GiftCard\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade( SchemaSetupInterface $setup, ModuleContextInterface $context ) {
        $installer = $setup;

        $installer->startSetup();
        if (!$installer->tableExists('mageplaza_giftcard_history')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('mageplaza_giftcard_history')
            )
                ->addColumn(
                    'history_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary'  => true,
                        'unsigned' => true,
                    ],
                    'history ID'
                )
                ->addColumn(
                    'giftcard_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                        'index' => true
                    ],
                    'giftcard_id'
                )
                ->addColumn(
                    'customer_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'index' => true],
                    'customer_id'
                )
                ->addColumn(
                    'amount',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '12,4',
                    [
                        'nullable' => false
                    ],
                    'Lượng amount bị thay đổi'
                )
                ->addColumn(
                    'action',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => false
                    ],
                    'hành động thay đổi - create/redeem/Used for order'
                )
                ->addColumn(
                    'action_time',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    [
                        'nullable' => false,
                        'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE
                    ],
                    'thời gian diễn ra thay đổi'
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'mageplaza_giftcard_history',
                        'giftcard_id',
                        'mageplaza_giftcard_code',
                        'giftcard_id'
                    ),
                    'giftcard_id',
                    $installer->getTable('mageplaza_giftcard_code'),
                    'giftcard_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $installer->getFkName(
                        'mageplaza_giftcard_history',
                        'customer_id',
                        'customer_entity',
                        'entity_id'
                    ),
                    'customer_id',
                    $installer->getTable('customer_entity'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->setComment('lịch sử sử dụng của từng gift code');
            $installer->getConnection()->createTable($table);

            $installer->getConnection()->addIndex(
                $installer->getTable('mageplaza_giftcard_history'),
                $setup->getIdxName(
                    $installer->getTable('mageplaza_giftcard_history'),
                    ['giftcard_id', 'customer_id', 'amount', 'action', 'action_time'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['giftcard_id', 'customer_id', 'amount', 'action', 'action_time'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            );
        }
        if(version_compare($context->getVersion(), '2.0.0', '<')) {
            $installer->getConnection()->addColumn(
                $installer->getTable( 'customer_entity' ),
                'giftcard_balance',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'nullable' => false,
                    'length' => '12,4',
                    'comment' => 'balance của customer'
                ]
            );
        }

        $installer->endSetup();
    }
}
