//{block name="backend/pol_payment_payolution/payment/store/currency"}
Ext.define('Shopware.apps.PolPaymentPayolution.Payment.store.Currency', {
    extend:'Ext.data.Store',
    storeId: 'PayolutionConfigCurrency',
    autoLoad: true,
    autoSave: true,
    autoSync: true,
    model: 'Shopware.apps.PolPaymentPayolution.Payment.model.Currency'
});
//{/block}
