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
    'text!Lof_MarketPlace/templates/grid/cells/deny/sellerpay.html',
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
        getFlag: function (row) {
            return row[this.index + '_flag'];
        },
        gethtml: function (row) {
            return row[this.index + '_html'];
        },
        getFormaction: function (row) {
            return row[this.index + '_formaction'];
        },
        getSellerid: function (row) {
            return row[this.index + '_sellerid'];
        },
        getAutoorderid: function (row) {
            return row[this.index + '_autoorderid'];
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
            if (this.getFlag(row)==1) {
                var modalHtml = mageTemplate(
                    denyPreviewTemplate,
                    {
                        flag: this.getFlag(row),
                        html: this.gethtml(row),
                        title: this.getTitle(row),
                        label: this.getLabel(row),
                        formaction: this.getFormaction(row),
                        autoorderid: this.getAutoorderid(row),
                        sellerid: this.getSellerid(row),
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
            }
        },
        getFieldHandler: function (row) {
            return this.preview.bind(this, row);
        }
    });
});
