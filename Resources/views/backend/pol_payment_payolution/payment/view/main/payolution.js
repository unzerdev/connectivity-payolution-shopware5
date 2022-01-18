//{block name="backend/pol_payment_payolution/payment/view/main/payolution"}
Ext.define('Shopware.apps.PolPaymentPayolution.Payment.view.main.Payolution', {
    extend: 'Ext.container.Container',

    tabConfig: {
      cls: 'payolutionConfig'
    },
    title: '{s name=window_title}Payolution Konfiguration{/s}',
    alias: 'widget.payolution-config-window',
    autoShow: true,
    layout: 'auto',
    cls: Ext.baseCSSPrefix + 'document-panel',

    initComponent: function () {
        var me = this;

        me.payolutionConfig = Ext.create('Shopware.apps.PolPaymentPayolution.Payment.store.Config');
        me.payolutionCurrency = Ext.create('Shopware.apps.PolPaymentPayolution.Payment.store.Currency');
        me.payolutionShop = Ext.create('Shopware.apps.PolPaymentPayolution.Payment.store.Shop');

        me.rowEditingConfig = Ext.create('Ext.grid.plugin.RowEditing', {
            clicksToEdit: 2
        });

        me.configGrid = me.createConfigGrid();

        me.payolutionShop.load();
        me.payolutionCurrency.load();

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
            store: me.payolutionConfig,
            bbar: Ext.create('Ext.PagingToolbar', {
                store: me.payolutionConfig
            }),
            columns: [
                {
                    header: 'Shopname',
                    dataIndex: 'shopId',
                    flex: 1,
                    renderer: function (id) {
                        if (id > 0) {
                            return me.payolutionShop.getById(id).data.name;
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
                            return me.payolutionCurrency.getById(id).data.name;
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
//{/block}