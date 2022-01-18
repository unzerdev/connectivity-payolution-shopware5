Ext.define('Shopware.apps.PayolutionConfig.controller.Main', {
    extend: 'Enlight.app.Controller',

    mainWindow: null,

    /**
     * Creates the necessary event listener for this
     * specific controller and opens a new Ext.window.Window
     * to display the subapplication
     *
     * @return void
     */
    init: function () {
        var me = this;
        me.mainWindow = me.getView('main.Window').create({
            shopStore: me.subApplication.getStore('Shop'),
            currencyStore: me.subApplication.getStore('Currency'),
            configStore: me.subApplication.getStore('Config')
        });
        me.callParent(arguments);
    }
});