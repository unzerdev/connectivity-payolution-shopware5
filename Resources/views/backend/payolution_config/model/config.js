Ext.define('Shopware.apps.PayolutionConfig.model.Config', {
    extend: 'Shopware.data.Model',



    fields: [
        { name : 'id', type: 'int' },
        { name : 'shopId', type: 'int' },
        { name : 'currencyId', type: 'int' },
        { name : 'name', type: 'string' },
        { name : 'value', type: 'string' }
    ],


    /**
     * Configure the data communication
     * @object
     */
    proxy: {
        type: 'ajax',

        /**
         * Configure the url mapping for the different
         * store operations based on
         * @object
         */
        api: {
            read:    '{url controller="PayolutionConfig" action="getConfig"}',
            update:  '{url controller="PayolutionConfig" action="updateConfig"}'
        },

        /**
         * Configure the data reader
         * @object
         */
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});