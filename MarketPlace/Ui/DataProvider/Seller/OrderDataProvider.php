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

namespace Lof\MarketPlace\Ui\DataProvider\Seller;

use Lof\MarketPlace\Model\ResourceModel\Order\CollectionFactory;

class OrderDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * Product collection
     *
     * @var \Lof\MarketPlace\Model\ResourceModel\Order\Collection
     */
    protected $collection;

    /**
     * @var \Magento\Ui\DataProvider\AddFieldToCollectionInterface[]
     */
    protected $addFieldStrategies;

    /**
     * @var \Magento\Ui\DataProvider\AddFilterToCollectionInterface[]
     */
    protected $addFilterStrategies;

    /**
     * OrderDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param array $addFieldStrategies
     * @param array $addFilterStrategies
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );

        $this->collection = $collectionFactory->create();
        $this->addFieldStrategies = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->joinOrderGridTable();
            $this->setDefaultSortOrder();
            $this->getCollection()->load();
        }

        $items = $this->getCollection()->toArray();

        return [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => array_values($items['items']),
        ];
    }

    /**
     * Add field to select
     *
     * @param string|array $field
     * @param string|null $alias
     * @return void
     */
    public function addField($field, $alias = null)
    {
        if (isset($this->addFieldStrategies[$field])) {
            $this->addFieldStrategies[$field]->addField($this->getCollection(), $field, $alias);
        } else {
            parent::addField($field, $alias);
        }
    }

    /**
     * @param \Magento\Framework\Api\Filter $filter
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if (isset($this->addFilterStrategies[$filter->getField()])) {
            $this->addFilterStrategies[$filter->getField()]
                ->addFilter(
                    $this->getCollection(),
                    $filter->getField(),
                    [$filter->getConditionType() => $filter->getValue()]
                );
        } else {
            parent::addFilter($filter);
        }
    }

    /**
     * Join order grid table
     *
     * @return $this
     */
    public function joinOrderGridTable()
    {
        $this->getCollection()
            ->getSelect()
            ->join(
                ['order_grid_table' => $this->getCollection()->getResource()->getTable("sales_order_grid")],
                'main_table.order_id = order_grid_table.entity_id',
                [
                    'shipping_information',
                    'store_id',
                    'store_name',
                    'shipping_name',
                    'billing_name',
                    'created_at',
                    'updated_at',
                    'billing_address',
                    'shipping_address',
                    'customer_name',
                    'base_grand_total',
                    'grand_total',
                    'subtotal',
                    'shipping_and_handling',
                    'status'
                ]
            )
            ->group(
                'main_table.order_id'
            );

        return $this;
    }

    /**
     * Set default sort order
     *
     * @return OrderDataProvider
     */
    public function setDefaultSortOrder()
    {
        $this->getCollection()->addOrder("main_table.increment_id", "DESC");
        return $this;
    }

    /**
     * Get Correct filter field name
     *
     * @param string
     * @return string
     */
    protected function getCorrectFilterField($fieldName)
    {
        $mappingField = [
            "status" => "main_table.status",
            "increment_id" => "main_table.increment_id",
            "customer_id" => "main_table.customer_id"
        ];
        return isset($mappingField[$fieldName]) ? $mappingField[$fieldName] : $fieldName;
    }
}