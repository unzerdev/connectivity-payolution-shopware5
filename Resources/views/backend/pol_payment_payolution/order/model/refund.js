//{block name="backend/pol_payment_payolution/order/model/refund"}
Ext.define('Shopware.apps.PolPaymentPayolution.Order.model.Refund', {

    extend: 'Ext.data.Model',

    fields: [
        {
            name: 'id',
            type: 'int'
        },
        {
            name: 'additionalId',
            type: 'int'
        },
        {
            name: 'name',
            type: 'string'
        },
        {
            name: 'quantity',
            type: 'int'
        },
        {
            name: 'amount',
            type: 'string'
        }
    ],

    proxy: {
        type: 'ajax',
        api: {
            read: '{url controller="PayolutionRefund" action="getRefunds"}'
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
