//{block name="backend/pol_payment_payolution/order/view/detail/capture"}
Ext.define('Shopware.apps.PolPaymentPayolution.Order.view.detail.Capture', {
    extend: 'Ext.form.Panel',

    alias: 'widget.order-view-detail-capture',
    name: 'order-view-detail-capture',

    title: 'Capture',

    autoScroll: true,

    layout: 'column',
    bodyPadding: 4,

    url: '{url controller="PayolutionCapture" action="createCapture"}',

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
            id: 'captureLeftSide' + me.record.internalId,
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
            me.createCaptureGrid(),
            me.createButtonLeft()
        ];
    },

    createCaptureGrid: function () {
        me = this;

        me.captureGrid = Ext.create('Shopware.apps.PolPaymentPayolution.Order.view.list.CaptureGrid', {
            id: 'captureGrid' + me.record.internalId,
            record: me.record,
            minHeight: 150,
            minWidth: 250,
            region: 'west',
            style: 'margin-bottom: 10px;'
        });

        return me.captureGrid;
    },

    createRightSide: function () {
        var me = this;
        return {
            xtype: 'container',
            id: 'captureRightSide' + me.record.internalId,
            columnWidth: 0.5,
            layout: 'anchor',
            items: me.getFormElementsRight(),
            defaults: me.formDefaults,
            margin: '5 5 5 5'
        };
    },

    getFormElementsRight: function () {
        var me = this;

        return [
            me.createCaptureAmount(),
            me.createButtonRight()
        ];
    },

    createCaptureAmount: function () {
        var me = this;

        me.captureField = Ext.create('Ext.form.field.Number', {
            name: 'captureAmount',
            fieldLabel: 'Betrag',
            decimalPrecision: 2,
            minValue : 0,
            allowBlank: false,
            allowNegative: false,
            allowDecimals: true,
            listeners: {
                change: function( field, newval, oldval ) {
                    Ext.getCmp('rightCaptureButton' + me.record.internalId).enable(true);
                }
            }
        });

        return  me.captureField;
    },

    createButtonLeft: function () {
        var me = this;

        me.createButton = Ext.create('Ext.button.Button', {
            text: '{s name="action/create_capture"}Capturen{/s}',
            action: 'create-capture-positions',
            id: 'leftCaptureButton' + me.record.internalId,
            disabled: true,
            cls: 'primary',
            handler: function () {
                me.fireEvent('createCapturePositions', me.record.data.id, me.captureGrid.getSelectionModel().selected.items);
            }
        });

        return me.createButton;
    },
    createButtonRight: function () {
        var me = this;

        me.createButton = Ext.create('Ext.button.Button', {
            text: '{s name="action/create_capture"}Capturen{/s}',
            id: 'rightCaptureButton' + me.record.internalId,
            action: 'create-capture-absolute',
            disabled: true,
            cls: 'primary',
            record: me.record,
            handler: function () {
                me.fireEvent('createCapture',me.record.data.id, me.captureField.getValue());
            }
        });

        return me.createButton;
    }
});
//{/block}
