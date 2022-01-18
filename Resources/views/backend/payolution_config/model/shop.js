Ext.define('Shopware.apps.PayolutionConfig.model.Shop', {
    extend: 'Shopware.data.Model',



    fields: [
        { name : 'id', type: 'int' },
        { name : 'name', type: 'string' }
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
            read:    '{url controller="PayolutionConfig" action="getShops"}'
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