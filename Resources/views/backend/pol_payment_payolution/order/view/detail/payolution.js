//{block name="backend/pol_payment_payolution/order/view/detail/payolution"}
Ext.define('Shopware.apps.PolPaymentPayolution.Order.view.detail.Payolution', {
    extend: 'Ext.container.Container',

    layout: 'auto',

    alias: 'widget.order-payolution-panel',

    cls: Ext.baseCSSPrefix + 'document-panel',

    padding: 10,

    autoScroll: true,

    refundGrid: null,

    captureGrid: null,

    logForm: null,

    /**
     * Create Items
     *
     * @return void
     */
    createItems: function () {
        var me = this;

        me.removeAll();
        me.add(me.createCaptureForm());
        me.add(me.createRefundForm());
        me.add(me.createLogForm());
    },

    /**
     * Create Capture Form
     *
     * @return null
     */
    createCaptureForm: function () {
        var me = this;

        me.captureGrid =  Ext.create('Shopware.apps.PolPaymentPayolution.Order.view.detail.Capture', {
            region: 'top',
            id: 'detailCaptureGrid' + me.record.internalId,
            record: me.record
        });

        return me.captureGrid;
    },

    /**
     * Create Refund Form
     *
     * @return null
     */
    createRefundForm: function () {
        var me = this;

        me.refundGrid =  Ext.create('Shopware.apps.PolPaymentPayolution.Order.view.detail.Refund', {
            region: 'top',
            id: 'detailRefundGrid' + me.record.internalId,
            record: me.record
        });

        return me.refundGrid;
    },

    /**
     * Create Refund Form
     *
     * @return null
     */
    createLogForm: function () {
        var me = this;

        me.logForm = Ext.create('Shopware.apps.PolPaymentPayolution.Order.view.list.LogGrid', {
            region: 'top',
            id: 'logForm' + me.record.internalId,
            record: me.record,
            listeners: {
                cellclick: function (grd, rowIndex, colIndex, e) {
                    me.fireEvent('payolutionLogClick', e.data);
                }
            }
        });

        return me.logForm;
    }
});
//{/block}