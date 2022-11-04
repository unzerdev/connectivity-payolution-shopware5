//{block name="backend/pol_payment_payolution/order/view/detail/refund"}
Ext.define('Shopware.apps.PolPaymentPayolution.Order.view.detail.Refund', {
    extend: 'Ext.form.Panel',

    alias: 'widget.order-view-detail-refund',
    name: 'order-view-detail-refund',

    title: 'Refund',

    autoScroll: true,

    layout: 'column',
    bodyPadding: 4,

    formDefaults: {
        labelWidth: 120,
        style: 'margin-bottom: 10px !important;',
        labelStyle: 'font-weight: 700;',
        anchor: '100%'
    },

    initComponent: function () {
        var me = this;

        me.items = [
            me.createLeftSide(),
            me.createRightSide()
        ];

        me.callParent(arguments);
    },

    createLeftSide: function () {
        var me = this;
        return {
            xtype: 'container',
            id: 'refundLeftSide' + me.record.internalId,
            columnWidth: 0.5,
            layout: 'anchor',
            items: me.getFormElementsLeft(),
            defaults: me.formDefaults,
            margin: '5 5 5 5'
        };
    },


    getFormElementsLeft: function () {
        var me = this;
        return [
            me.createRefundGrid(),
            me.createButtonLeft()
        ];
    },

    createRefundGrid: function () {
        var me = this;

        me.refundGrid = Ext.create('Shopware.apps.PolPaymentPayolution.Order.view.list.RefundGrid', {
            id: 'refundGrid' + me.record.internalId,
            record: me.record,
            minHeight: 150,
            minWidth: 250,
            region: 'west',
            style: 'margin-bottom: 10px;'
        });

        return me.refundGrid;
    },

    createRightSide: function () {
        var me = this;
        return {
            xtype: 'container',
            id: 'refundRightSide' + me.record.internalId,
            columnWidth: 0.5,
            layout: 'fit',
            items: me.getFormElementsRight(),
            defaults: me.formDefaults,
            margin: '5 5 5 5'
        };
    },

    getFormElementsRight: function () {
        var me = this;

        return [
            /*me.createRefundDropDown(),*/
            me.createRefundAmount(),
            me.createButtonRight()
        ];
    },

    createRefundDropDown: function () {
        var me = this;
        var mode = Ext.create('Ext.data.Store', {
            fields: ['name', 'value'],
            data : [
                { "name":"Absolut", "value":"absolute" },
                { "name":"Prozentual", "value":"percentage" }
            ]
        });

        // Create the combo box, attached to the states data store
        me.refundDropDown = Ext.create('Ext.form.ComboBox', {
            fieldLabel: 'Refund Modus',
            store: mode,
            name: 'refund_mode',
            queryMode: 'local',
            displayField: 'name',
            valueField: 'value',
            value: 'absolute',
            renderTo: Ext.getBody()
        });

        return  me.refundDropDown;
    },

    createRefundAmount: function () {
        var me = this;

        me.refundField = Ext.create('Ext.form.field.Number', {
            name: 'refundAmount',
            fieldLabel: 'Betrag',
            decimalPrecision: 2,
            minValue : 0,
            allowBlank: false,
            allowNegative: false,
            allowDecimals: true,
            listeners: {
                change: function( field, newval, oldval ) {
                    Ext.getCmp('rightRefundButton' + me.record.internalId).enable(true);
                }
            }
        });

        return  me.refundField;
    },

    createButtonLeft: function () {
        var me = this;

        me.createButton = Ext.create('Ext.button.Button', {
            text: '{s name="action/create_refund"}Refund{/s}',
            action: 'create-refund-positions',
            cls: 'primary',
            id: 'leftRefundButton' + me.record.internalId,
            disabled: true,
            handler: function () {
                me.fireEvent('createRefundPositions', me.record.data.id, me.refundGrid.getSelectionModel().selected.items);
            }
        });

        return me.createButton;
    },
    createButtonRight: function () {
        var me = this;

        me.createButton = Ext.create('Ext.button.Button', {
            text: '{s name="action/create_refund"}Refund{/s}',
            action: 'create-refund-absolute',
            cls: 'primary',
            id: 'rightRefundButton' + me.record.internalId,
            disabled: true,
            record: me.record,
            handler: function () {
                me.fireEvent('createRefund', me.record.data.id, me.refundField.getValue());
            }
        });

        return me.createButton;
    }
});
//{/block}
