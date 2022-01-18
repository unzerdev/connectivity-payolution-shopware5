//{block name="backend/order/view/detail/window"}
//      {$smarty.block.parent}
Ext.define('Shopware.apps.PolPaymentPayolution.Order.view.detail.Window', {
    override: 'Shopware.apps.Order.view.detail.Window',

    payolutionTabName: '{s name=order/payolution_window_title}Payolution{/s}',

    /**
     * @override
     *
     * Creates the tab panel for the detail page.
     * @return Ext.tab.Panel
     */
    createTabPanel: function () {
        var me = this, result;
        result = me.callParent(arguments);
        result.add(me.createPolPaymentPayolutionTab());
        result.addListener(me.createPayolutionEventListener());

        return result;
    },

    createPayolutionEventListener: function () {
        var me = this;
        return {
            beforetabchange: function ( tabPanel, newCard, oldCard, eOpts) {
                if (newCard.title === me.payolutionTabName) {
                    me.fireEvent('payolutionTabClick', newCard, me.record.data.id);
                }
            }
        }
    },

    /**
     * @return Ext.container.Container
     */
    createPolPaymentPayolutionTab: function () {
        var me = this;

        return Ext.create('Shopware.apps.PolPaymentPayolution.Order.view.detail.Payolution', {
            id: 'payolutionTab' + me.record.data.id,
            record: me.record,
            title: me.payolutionTabName
        });
    }
});
//{/block}
