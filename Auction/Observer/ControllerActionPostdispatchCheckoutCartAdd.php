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
 * @package    Lof_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Auction\Observer;

use Lof\Auction\Model\ProductFactory;
use Lof\Auction\Model\ResourceModel\Product\Source\AuctionStatus;
use Lof\Auction\Helper\Data;
use Magento\Checkout\Helper\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class ControllerActionPostdispatchCheckoutCartAdd
 * @package Lof\Auction\Observer
 */
class ControllerActionPostdispatchCheckoutCartAdd implements ObserverInterface
{
    /**
     * @var ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var Session
     */
    protected $_checkoutSession;

    /**
     * @var Cart
     */
    protected $_cartHelper;

    /**
     * @var Data
     */
    protected $auctionHelperData;

    /**
     * @var ProductFactory
     */
    protected $_auctionProductFactory;

    /**
     * @param ManagerInterface $messageManager
     * @param Session $checkoutSession
     * @param Cart $cartHelper
     * @param ProductFactory $auctionProductFactory
     * @param Data $auctionHelperData
     */
    public function __construct(
        ManagerInterface $messageManager,
        Session $checkoutSession,
        Cart $cartHelper,
        ProductFactory $auctionProductFactory,
        Data $auctionHelperData
    ) {
        $this->_messageManager = $messageManager;
        $this->_checkoutSession = $checkoutSession;
        $this->_cartHelper = $cartHelper;
        $this->_auctionProductFactory = $auctionProductFactory;
        $this->auctionHelperData = $auctionHelperData;
    }

    /**
     * add to cart event handler.
     *
     * @param Observer $observer
     *
     * @return $this
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        if ( $this->auctionHelperData->isEnabled() && $this->auctionHelperData->getConfig("general_settings/only_one_addtocart") ) {
            $cartItems = $this->_checkoutSession->getQuote()->getAllItems();
            $proTypeArray = ['configurable', 'bundle'];
            $flag = false;
            foreach ($cartItems as $item) {
                if (!in_array($item->getProduct()->getTypeId(), $proTypeArray)) {
                    $auctionProduct = $this->_auctionProductFactory->create()
                        ->getCollection()
                        ->addFieldToFilter('product_id', ['eq' => $item->getProductId()])
                        ->addFieldToFilter('auction_status', ['eq' => AuctionStatus::STATUS_TIME_END])
                        ->getFirstItem();
                    //is auction available
                    if ($auctionProduct->getEntityId()) {
                        $cartAddedItems = $this->_cartHelper->getCart()->getItems();

                        /*remove product from cart if that product
                        *is not auction product*/
                        foreach ($cartAddedItems as $cartItem) {
                            if ($item->getItemId() != $cartItem->getItemId()) {
                                $this->_cartHelper->getCart()->removeItem($cartItem->getItemId())->save();
                                $flag = true;
                            }
                        }
                    }
                }
            }
            if ($flag) {
                $msg = 'Only Auction product can be added to cart!.';
                $this->_messageManager->addError(__($msg));
            }
        }
        return $this;
    }
}
