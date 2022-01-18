//{block name="backend/pol_payment_payolution/payment/store/config"}
Ext.define('Shopware.apps.PolPaymentPayolution.Payment.store.Config', {
    extend:'Ext.data.Store',
    storeId: 'PayolutionConfigConfig',
    autoLoad: true,
    autoSave: true,
    autoSync: true,
    model: 'Shopware.apps.PolPaymentPayolution.Payment.model.Config'
});
//{/block}
