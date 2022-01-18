//{block name="backend/pol_payment_payolution/order/view/list/capture_grid"}
Ext.define('Shopware.apps.PolPaymentPayolution.Order.view.list.CaptureGrid', {
    extend: 'Ext.grid.Panel',

    alias: 'widget.payolution-list',
    name: 'payolution-list-capture',
    region: 'center',

    autoScroll: true,

    viewConfig: {
        enableTextSelection: true
    },

    initComponent: function () {
        var me = this,
            orderId = me.record.get('id');
        me.store = Ext.data.StoreManager.lookup('PayolutionCapture' + orderId);
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
                    Ext.getCmp('leftCaptureButton' + me.record.internalId).enable(true);
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
                flex: 1,
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