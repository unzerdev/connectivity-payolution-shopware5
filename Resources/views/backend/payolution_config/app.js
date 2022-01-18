Ext.define('Shopware.apps.PayolutionConfig', {
    /**
     * Extends from our special controller, which handles the
     * sub-application behavior and the event bus
     * @string
     */
    extend: 'Enlight.app.SubApplication',

    /**
     * The name of the module. Used for internal purpose
     * @string
     */
    name: 'Shopware.apps.PayolutionConfig',
    bulkLoad: true,
    loadPath: '{url action=load}',

    /**
     * Required controllers for sub-application
     * @array
     */
    controllers: ['Main'],

    /**
     * Requires models for sub-application
     * @array
     */
    models: ['Config'],

    /**
     * Required views for this sub-application
     * @array
     */
    views: ['main.Window'],

    /**
     * Required stores for sub-application
     * @array
     */
    stores: ['Config'],

    /**
     * @private
     * @return [object] mainWindow - the main application window based on Enlight.app.Window
     */
    launch: function () {
        var me = this,
            mainController = me.getController('Main');
        return mainController.mainWindow;
    }
});