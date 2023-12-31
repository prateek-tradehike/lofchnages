<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Model\ResourceModel;

abstract class AbstractCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    protected $metadataPool;

    /**
     * AbstractCollection constructor.
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->storeManager = $storeManager;
        $this->metadataPool = $metadataPool;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Perform operations after collection load
     *
     * @param string $tableName
     * @param string $columnName
     * @return void
     */
    protected function performAfterLoad($tableName, $columnName)
    {
        $items = $this->getColumnValues($columnName);
        if (count($items)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(['seller_entity_store' => $this->getTable($tableName)])
                ->where('seller_entity_store.' . $columnName . ' IN (?)', $items);
            $result = $connection->fetchAll($select);
            $store = [];
            foreach ($result as $_result) {
                $store[] = $_result['store_id'];
            }

            if ($result) {
                foreach ($this as $item) {

                    if (in_array(0, $store)) {
                        $stores = $this->storeManager->getStores(false, true);
                        $storeId = current($stores)->getId();
                    } else {
                        $storeId = $result[0]['store_id'];
                    }

                    $item->setData('_first_store_id', $storeId);
                    $item->setData('store_code', $store);
                    $item->setData('store', $store);
                    $item->setData('store_id', $store);
                }
            }
        }
    }

    /**
     * @param $tableName
     * @param $columnName
     */
    protected function performAfterLoadCommission($tableName, $columnName)
    {
        $items = $this->getColumnValues($columnName);
        if (count($items)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(['lof_marketplace_commission_store' => $this->getTable($tableName)])
                ->where('lof_marketplace_commission_store.' . $columnName . ' IN (?)', $items);
            $result = $connection->fetchAll($select);
            $store = [];
            foreach ($result as $_result) {
                $store[] = $_result['store_id'];
            }

            if ($result) {
                $stores = $this->storeManager->getStores(false, true);
                foreach ($this as $item) {

                    if (in_array(0, $store)) {
                        $storeId = current($stores)->getId();
                    } else {
                        $storeId = $result[0]['store_id'];
                    }
                    $item->setData('_first_store_id', $storeId);
                    $item->setData('store_code', $store);
                    $item->setData('store', $store);
                    $item->setData('store_id', $store);
                }
            }
        }
    }

    /**
     * @param $tableName
     * @param $columnName
     */
    protected function performAfterLoadGroup($tableName, $columnName)
    {
        $items = $this->getColumnValues($columnName);
        if (count($items)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(['lof_marketplace_commission_group' => $this->getTable($tableName)])
                ->where('lof_marketplace_commission_group.' . $columnName . ' IN (?)', $items);
            $result = $connection->fetchAll($select);
            if ($result) {
                $groupIds = [];
                foreach ($result as $_result) {
                    if (!isset($groupIds[$_result[$columnName]])) {
                        $groupIds[$_result[$columnName]] = [];
                    }
                    $groupIds[$_result[$columnName]][] = $_result['group_id'];
                }

                foreach ($this as $item) {
                    $entityId = $item->getId();
                    if (!isset($groupIds[$entityId])) {
                        continue;
                    }
                    $item->setData('group_id', $groupIds[$entityId]);
                    $item->setData('groups', $groupIds[$entityId]);
                }
            }
        }
    }

    /**
     * Add field filter to collection
     *
     * @param array|string $field
     * @param string|int|array|null $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'store_id') {
            return $this->addStoreFilter($condition, false);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    abstract public function addStoreFilter($store, $withAdmin = true);

    /**
     * Perform adding filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return void
     */
    protected function performAddStoreFilter($store, $withAdmin = true)
    {
        if ($store instanceof \Magento\Store\Model\Store) {
            $store = [$store->getId()];
        }

        if (!is_array($store)) {
            $store = [$store];
        }

        if ($withAdmin) {
            $store[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        }

        $this->addFilter('store', ['in' => $store], 'public');
    }

    /**
     * Join store relation table if there is store filter
     *
     * @param string $tableName
     * @param string $columnName
     * @return void
     */
    protected function joinStoreRelationTable($tableName, $columnName)
    {
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                ['store_table' => $this->getTable($tableName)],
                'main_table.' . $columnName . ' = store_table.' . $columnName,
                []
            )->group(
                'main_table.' . $columnName
            );
        }
        parent::_renderFiltersBefore();
    }

    /**
     * Get SQL for get record count
     *
     * Extra GROUP BY strip added.
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(\Magento\Framework\DB\Select::GROUP);

        return $countSelect;
    }

    /**
     * Create all ids retrieving select with limitation
     * Backward compatibility with EAV collection
     *
     * @param int $limit
     * @param int $offset
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection
     */
    protected function _getAllIdsSelect($limit = null, $offset = null)
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $idsSelect->reset(\Magento\Framework\DB\Select::COLUMNS);
        $idsSelect->columns($this->getResource()->getIdFieldName(), 'main_table');
        $idsSelect->limit($limit, $offset);
        return $idsSelect;
    }
}
