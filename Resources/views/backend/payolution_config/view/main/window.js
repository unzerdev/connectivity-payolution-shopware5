Ext.define('Shopware.apps.PayolutionConfig.view.main.Window', {
    extend: 'Enlight.app.Window',
    title: '{s name=window_title}Payolution Konfiguration{/s}',
    alias: 'widget.payolution-config-window',
    autoShow: true,
    layout: 'fit',

    initComponent: function () {
        var me = this;
        me.rowEditingConfig = Ext.create('Ext.grid.plugin.RowEditing', {
            clicksToEdit: 2
        });

        me.configGrid = me.createConfigGrid();

        me.shopStore.load();
        me.currencyStore.load();

        me.items = [
            me.configGrid
        ];
        me.callParent(arguments);
    },

    createConfigGrid: function () {
        var me = this;
        return Ext.create('Ext.grid.Panel', {
            flex: 1,
            autoScroll:true,
            store: me.configStore,
            columns: [
                {
                    header: 'Shopname',
                    dataIndex: 'shopId',
                    flex: 1,
                    renderer: function (id) {
                        if (id > 0) {
                            return me.shopStore.getById(id).data.name;
                        } else {
                            return id;
                        }
                    }
                },
                {
                    header: 'W&auml;hrung',
                    dataIndex: 'currencyId',
                    flex: 1,
                    renderer: function (id) {
                        if (id > 0) {
                            return me.currencyStore.getById(id).data.name;
                        } else {
                            return id;
                        }
                    }
                },
                {
                    header: 'Name',
                    dataIndex: 'name',
                    flex: 3
                },
                {
                    header: 'Value',
                    dataIndex: 'value',
                    flex: 4,
                    editor: {
                        width: 85,
                        xtype: 'textfield',
                        allowBlank: false
                    }
                }
            ],
            editable: true,
            selType: 'rowmodel',
            plugins: [
                me.rowEditingConfig
            ]
        });
    }
});