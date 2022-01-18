//{block name="backend/pol_payment_payolution/order/view/list/log_grid"}
Ext.define('Shopware.apps.PolPaymentPayolution.Order.view.list.LogGrid', {
    extend: 'Ext.grid.Panel',

    alias: 'widget.payolution-list',

    title: '',

    region: 'center',

    autoScroll: true,

    viewConfig: {
        enableTextSelection: true
    },

    initComponent: function () {
        var me = this,
            orderId = me.record.get('id');

        me.columns = me.getColumns();
        me.store = Ext.data.StoreManager.lookup('PayolutionLog' + orderId);

        me.callParent(arguments);

        me.store.load({
            params: {
                id: orderId
            }
        });
    },

    getColumns: function () {
        var me = this;

        return [
            {
                header: 'Datum/Uhrzeit',
                dataIndex: 'date',
                flex: 1
            },
            {
                header: 'Type',
                dataIndex: 'type',
                flex: 1
            },
            {
                header: 'Status',
                dataIndex: 'state',
                flex: 1
            },
            {
                header: 'Bezeichnung',
                dataIndex: 'articlename',
                flex: 2
            },
            {
                header: 'Anzahl',
                dataIndex: 'quantity',
                flex: 1
            },
            {
                header: 'Betrag',
                dataIndex: 'amount',
                flex: 2
            },
            {
                header: 'Anfrage-Nummer',
                dataIndex: 'requestid',
                flex: 2
            }
        ];
    }
});
//{/block}