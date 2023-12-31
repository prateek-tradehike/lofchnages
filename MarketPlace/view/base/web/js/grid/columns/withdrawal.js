
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

define([
    './column',
    'jquery',
    'mage/template',
    'text!Lof_MarketPlace/templates/grid/cells/deny/withdrawal.html',
    'Magento_Ui/js/modal/modal'
], function (Column, $, mageTemplate, denyPreviewTemplate) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'ui/grid/cells/html',
            fieldClass: {
                'data-grid-html-cell': true
            }
        },
        gethtml: function (row) {
            return row[this.index + '_html'];
        },
        getFormaction: function (row) {
            return row[this.index + '_formaction'];
        },
        getWithdrawalid: function (row) {
            return row[this.index + '_withdrawalid'];
        },
        getStatus: function (row) {
            return row[this.index + '_status'];
        },
        getSellername: function (row) {
            return row[this.index + '_sellername'];
        },
         getSellerid: function (row) {
            return row[this.index + '_sellerid'];
        },
        getBalance: function (row) {
            return row[this.index + '_balance'];
        },
        getPaymentname: function (row) {
            return row[this.index + '_paymentname'];
        },
        getEmail: function (row) {
            return row[this.index + '_email'];
        },
        getAmount: function (row) {
            return row[this.index + '_amount'];
        },
         getFee: function (row) {
            return row[this.index + '_fee'];
        },
        getNetamount: function (row) {
            return row[this.index + '_netamount'];
        },
        getComment: function (row) {
            return row[this.index + '_comment'];
        },
        getAdminMessage: function (row) {
            return row[this.index + '_adminmessage'];
        },
        getCreatedat: function (row) {
            return row[this.index + '_createdat'];
        },
        getLabel: function (row) {
            return row[this.index + '_html']
        },
        getTitle: function (row) {
            return row[this.index + '_title']
        },
        getSubmitlabel: function (row) {
            return row[this.index + '_submitlabel']
        },
        getCancellabel: function (row) {
            return row[this.index + '_cancellabel']
        },
        preview: function (row) {
            var modalHtml = mageTemplate(
                denyPreviewTemplate,
                {
                    html: this.gethtml(row),
                    title: this.getTitle(row),
                    label: this.getLabel(row),
                    formaction: this.getFormaction(row),
                    withdrawalid: this.getWithdrawalid(row),
                    status: this.getStatus(row),
                    sellerid: this.getSellerid(row),
                    sellername: this.getSellername(row),
                    balance: this.getBalance(row),
                    paymentname: this.getPaymentname(row),
                    email: this.getEmail(row),
                    amount: this.getAmount(row),
                    fee: this.getFee(row),
                    netamount: this.getNetamount(row),
                    comment: this.getComment(row),
                    adminmessage: this.getAdminMessage(row),
                    createdat: this.getCreatedat(row),
                    submitlabel: this.getSubmitlabel(row),
                    cancellabel: this.getCancellabel(row),
                    linkText: $.mage.__('Go to Details Page')
                }
            );
            var previewPopup = $('<div/>').html(modalHtml);
            previewPopup.modal({
                title: this.getTitle(row),
                innerScroll: true,
                modalClass: '_image-box',
                buttons: []}).trigger('openModal');
        },
        getFieldHandler: function (row) {
            return this.preview.bind(this, row);
        }
    });
});
