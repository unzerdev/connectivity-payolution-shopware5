Ext.define('Shopware.apps.PayolutionConfig.store.Config', {
    extend:'Ext.data.Store',
    autoLoad: true,
    autoSave: true,
    autoSync: true,
    model: 'Shopware.apps.PayolutionConfig.model.Config'
});

