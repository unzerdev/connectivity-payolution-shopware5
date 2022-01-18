//{block name="backend/payment/controller/payment"}
//      {$smarty.block.parent}
Ext.define('Shopware.apps.PolPaymentPayolution.Payment.controller.Payment', {
    override: 'Shopware.apps.Payment.controller.Payment',

    onItemClick: function (view, record) {
        var me = this;
        me.callParent(arguments);

        if (record.data.action == "PolPaymentPayolution") {
            Ext.select('.payolutionConfig').show();
        } else {
            Ext.select('.payolutionConfig').hide();
        }
    }
});
//{/block}
