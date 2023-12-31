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

namespace Lof\MarketPlace\Block\Account;

class Message extends \Magento\Framework\View\Element\Html\Link\Current
{
    /**
     *
     * @var Magento\Framework\App\Action\Session
     */
    protected $session;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $marketplaceHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Helper\Data $marketplaceHelper
     * @param array $data
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Helper\Data $marketplaceHelper,
        array $data = []
    ) {
        $this->session = $customerSession;
        $this->marketplaceHelper = $marketplaceHelper;
        parent::__construct($context, $defaultPath);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $seller = $this->marketplaceHelper->getSellerId();
        if ($seller) {
            return '';
        }
        if (false != $this->getTemplate()) {
            return parent::_toHtml();
        }

        $highlight = '';

        if ($this->getIsHighlighted()) {
            $highlight = ' current';
        }

        if ($this->isCurrent()) {
            $html = '<li class="nav item current lrw-nav-item">';
            $html .= '<strong>'
                . '<span>' . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getLabel())) . '</span>';
            $html .= '</strong>';
            $html .= '</li>';
        } else {
            // phpcs:disable Generic.Files.LineLength.TooLong
            $html = '<li class="nav item' . $highlight . ' lrw-nav-item"><a href="' . $this->escapeHtml($this->getHref()) . '"';
            $html .= $this->getTitle()
                ? ' title="' . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getTitle())) . '"'
                : '';
            $html .= $this->getAttributesHtml() . '>';

            if ($this->getIsHighlighted()) {
                $html .= '<strong>';
            }

            $html .= '<span>' . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getLabel())) . '</span>';

            if ($this->getIsHighlighted()) {
                $html .= '</strong>';
            }
            $html .= '</a></li>';
        }

        return $html;
    }
}
