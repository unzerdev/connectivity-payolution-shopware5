//{block name="backend/config/view/form/document"}
//      {$smarty.block.parent}
Ext.define('Shopware.apps.PolPaymentPayolution.Config.view.form.Document', {
    override: 'Shopware.apps.Config.view.form.Document',

    /**
     * @override
     *
     * @return Array
     */
    getFormItems: function () {
        var me = this, result;
        result = me.callParent(arguments);

        result.forEach(function(item) {
            if (item.name === 'elementFieldSet') {
                item.items.push(
                    {
                        xtype: 'tinymce',
                        fieldLabel: '{s name="document/detail/value_payolution_invoice_template_label"}"Payolution-Info-Inhalt{/s}',
                        labelWidth: 100,
                        name: 'payolution_invoice_template_Value',
                        hidden: true,
                        translatable: true
                    },
                    {
                        xtype: 'textarea',
                        fieldLabel: '{s name="document/detail/style_payolution_invoice_template_label"}Payolution-Info-Style{/s}',
                        labelWidth: 100,
                        name: 'payolution_invoice_template_Style',
                        hidden: true,
                        translatable: true
                    },
                    {
                        xtype: 'tinymce',
                        fieldLabel: '{s name="document/detail/value_payolution_invoice_template_footer_label"}Payolution-Footer-Inhalt{/s}',
                        labelWidth: 100,
                        name: 'payolution_invoice_template_footer_Value',
                        hidden: true,
                        translatable: true
                    },
                    {
                        xtype: 'textarea',
                        fieldLabel: '{s name="document/detail/style_payolution_invoice_template_footer_label"}Payolution-Footer-Style{/s}',
                        labelWidth: 100,
                        name: 'payolution_invoice_template_footer_Style',
                        hidden: true,
                        translatable: true
                    }
                );
            }
        });

        return result;
    }
});
//{/block}
