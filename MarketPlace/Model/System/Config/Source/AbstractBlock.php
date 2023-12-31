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

namespace Lof\MarketPlace\Model\System\Config\Source;

class AbstractBlock extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * AbstractBlock constructor.
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory);
        $this->_objectManager = $objectManager;
    }

    /**
     * Retrieve Full Option values array
     *
     * @param bool $withEmpty Add empty option to array
     * @param bool $defaultValues
     * @return array
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        $storeId = $this->getAttribute()->getStoreId();
        // @phpstan-ignore-next-line
        $this->_options[$storeId] = $this->_optionsDefault[$storeId] = $this->toOptionArray($defaultValues);
        if (!is_array($this->_options)) {
            $this->_options = [];
        }
        if (!is_array($this->_optionsDefault)) {
            $this->_optionsDefault = [];
        }
        if (!isset($this->_options[$storeId])) {
            $collection = $this->_attrOptionCollectionFactory->create()
                ->setPositionOrder('asc')
                ->setAttributeFilter($this->getAttribute()->getId())
                ->setStoreFilter($this->getAttribute()->getStoreId())
                ->load();
            $this->_options[$storeId] = $collection->toOptionArray($defaultValues);
            $this->_optionsDefault[$storeId] = $collection->toOptionArray($defaultValues);
        }
        $options = ($defaultValues ? $this->_optionsDefault[$storeId] : $this->_options[$storeId]);
        if ($withEmpty) {
            array_unshift($options, ['label' => '', 'value' => '']);
        }
        return $options;
    }

    /**
     * @param int|string $value
     * @return array|bool|mixed|string
     */
    public function getOptionText($value)
    {
        $isMultiple = false;
        if (strpos($value, ',') !== false) {
            $isMultiple = true;
            $value = explode(',', $value);
        }

        $options = $this->getAllOptions(false);

        if ($isMultiple) {
            $values = [];
            foreach ($options as $key => $item) {
                if (in_array($key, $value)) {
                    $values[] = $item;
                }
            }
            return $values;
        }
        foreach ($options as $key => $item) {
            if ($key == $value) {
                return $item;
            }
        }
        return false;
    }

    /**
     * @param bool $defaultValues
     * @param bool $withEmpty
     * @param null $storeId
     * @return array
     */
    public function toFilterOptionArray($defaultValues = false, $withEmpty = false, $storeId = null)
    {
        if ($storeId == null) {
            // @phpstan-ignore-next-line
            $options = $this->toOptionArray($defaultValues, $withEmpty);
        } else {
            // @phpstan-ignore-next-line
            $options = $this->toOptionArray($defaultValues, $withEmpty, $storeId);
        }
        $filterOptions = [];
        if (count($options)) {
            foreach ($options as $option) {
                if (isset($option['value']) && isset($option['label'])) {
                    $filterOptions[$option['value']] = $option['label'];
                }
            }
        }
        return $filterOptions;
    }

    /**
     * @param string $value
     * @return mixed|string
     */
    public function getLabelByValue($value = '')
    {
        $options = $this->toOptionArray();
        if (count($options)) {
            foreach ($options as $option) {
                if (isset($option['value']) && $option['value'] == $value) {
                    $value = isset($option['label']) ? $option['label'] : $value;
                    break;
                }
            }
        }
        return $value;
    }
}
