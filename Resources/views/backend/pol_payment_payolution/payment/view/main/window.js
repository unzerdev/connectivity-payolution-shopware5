//{block name="backend/payment/view/main/window"}
// {$smarty.block.parent}
Ext.define('Shopware.apps.PolPaymentPayolution.Payment.view.main.Window', {
    override: 'Shopware.apps.Payment.view.main.Window',

    /**
     * Creates the tab panel for the detail page.
     * @return Ext.tab.Panel
     */
    createTabPanel: function() {
        var me = this,
            result = me.callParent(arguments);

        result.add(Ext.create('Shopware.apps.PolPaymentPayolution.Payment.view.main.Payolution', {
            record: me.record,
        }));

        return result;
    }
});
//{/block}
