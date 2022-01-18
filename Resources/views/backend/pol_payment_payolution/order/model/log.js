//{block name="backend/pol_payment_payolution/order/model/log"}
Ext.define('Shopware.apps.PolPaymentPayolution.Order.model.Log', {

    extend: 'Ext.data.Model',

    fields: [
        {
            name: 'id',
            type: 'int'
        },
        {
            name: 'date',
            type: 'string'
        },
        {
            name: 'articlename',
            type: 'string'
        },
        {
            name: 'quantity',
            type: 'int'
        },
        {
            name: 'amount',
            type: 'double'
        },
        {
            name: 'state',
            type: 'string'
        },
        {
            name: 'type',
            type: 'string'
        },
        {
            name: 'message',
            type: 'string'
        },
        {
            name: 'requestid',
            type: 'int'
        }
    ],

    proxy: {
        type: 'ajax',
        api: {
            read: '{url controller="PayolutionLog" action="getLogs"}'
        },
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total',
            successProperty: 'success'
        }
    }
});
//{/block}
