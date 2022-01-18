//{block name="backend/pol_payment_payolution/payment/store/shop"}
Ext.define('Shopware.apps.PolPaymentPayolution.Payment.store.Shop', {
    extend:'Ext.data.Store',
    storeId: 'PayolutionConfigShop',
    autoLoad: true,
    autoSave: true,
    autoSync: true,
    model: 'Shopware.apps.PolPaymentPayolution.Payment.model.Shop'
});
//{/block}