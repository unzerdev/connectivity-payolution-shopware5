//{block name="backend/pol_payment_payolution/order/view/list/refund_grid"}
Ext.define('Shopware.apps.PolPaymentPayolution.Order.view.list.RefundGrid', {
    extend: 'Ext.grid.Panel',

    alias: 'widget.payolution-list',

    region: 'center',

    autoScroll: true,

    viewConfig: {
        enableTextSelection: true
    },

    initComponent: function () {
        var me = this,
            orderId = me.record.get('id');

        me.store = Ext.data.StoreManager.lookup('PayolutionRefund' + orderId);
        me.selModel = me.getGridSelModel();
        me.columns = me.getColumns();
        me.plugins = me.createPlugins();

        me.store.load({
            params: {
                id: orderId
            }
        });

        me.callParent(arguments);
    },

    createPlugins: function() {
        return Ext.create('Ext.grid.plugin.RowEditing', {
                clicksToEdit: 2,
                autoCancel: true
            });
    },

    getGridSelModel: function () {
        var me = this;
        return Ext.create('Ext.selection.CheckboxModel', {
            listeners: {
                select: function(model, record, index) {
                    Ext.getCmp('leftRefundButton' + me.record.internalId).enable(true);
                }
            }
        });
    },

    getColumns: function () {
        var me = this;

        return [
            {
                header: 'Bezeichnung',
                dataIndex: 'name',
                flex: 1
            },
            {
                xtype: 'numbercolumn',
                header: 'Menge',
                dataIndex: 'quantity',
                format: '0',
                flex: 2,
                editor: {
                    xtype: 'numberfield',
                    allowBlank: false,
                    minValue: 1
                }
            },
            {
                header: 'offener Betrag',
                dataIndex: 'amount',
                flex: 1
            }
        ];
    }
});
//{/block}