//{block name="backend/order/controller/main"}
// {$smarty.block.parent}
Ext.define('Shopware.apps.PolPaymentPayolution.Order.controller.Main', {
    override: 'Shopware.apps.Order.controller.Main',

    /**
     * @override
     *
     * A template method that is called when your application boots.
     * It is called before the Application's launch function is executed
     * so gives a hook point to run any code before your Viewport is created.
     *
     * @params orderId - The main controller can handle a orderId parameter to open the order detail page directly
     * @return void
     */
    init: function () {
        var me = this;

        me.control({
            'order-view-detail-capture': {
                'createCapturePositions': me.onCreateCapturePositions,
                'createCapture': me.onCreateCapture
            }, 'order-view-detail-refund': {
                'createRefundPositions': me.onCreateRefundPositions,
                'createRefund': me.onCreateRefund
            }, 'order-payolution-panel': {
                'payolutionLogClick': me.onLogClick,
            }, 'order-detail-window': {
                'payolutionTabClick' : me.onPayolutionTabClick
            }
        });

        me.callParent();
    },

    /**
     * On payolution Tab Click
     *
     * @param tab
     * @param orderId
     *
     * @return void
     */
    onPayolutionTabClick: function (tab, orderId) {
        var me = this;

        me.payolutionCapture = Ext.create('Ext.data.Store', {
            model: 'Shopware.apps.PolPaymentPayolution.Order.model.Capture',
            storeId: 'PayolutionCapture' + orderId,
            autoLoad: false
        });
        me.payolutionRefund = Ext.create('Ext.data.Store', {
            model: 'Shopware.apps.PolPaymentPayolution.Order.model.Refund',
            storeId: 'PayolutionRefund' + orderId,
            autoLoad: false
        });
        me.payolutionLog = Ext.create('Ext.data.Store', {
            model: 'Shopware.apps.PolPaymentPayolution.Order.model.Log',
            storeId: 'PayolutionLog' + orderId,
            autoLoad: false
        });

        tab.createItems();
        me.setWorkflowState(orderId);
    },

    /**
     * Set WorkflowState
     *
     * @param orderId
     */
    setWorkflowState: function (orderId) {
        Ext.Ajax.request({
            url: '{url controller="PayolutionWorkflow" action="workflowState"}',
            method: 'POST',
            params: {
                orderId: orderId
            },
            success: function ( response ) {
                var result = Ext.decode(response.responseText);

                Ext.getCmp('detailCaptureGrid' + orderId).setTitle(result.captureAmount);
                Ext.getCmp('detailRefundGrid' + orderId).setTitle(result.refundAmount);

                if(result.refundActive === false) {
                    Ext.getCmp('detailRefundGrid' + orderId).disable(true);
                    Ext.getCmp('refundLeftSide' + orderId).disable(true);
                } else {
                    Ext.getCmp('detailRefundGrid' + orderId).enable(true);
                    Ext.getCmp('refundLeftSide' + orderId).enable(true);
                }
                if(result.captureActive === false) {
                    Ext.getCmp('detailCaptureGrid' + orderId).disable(true);
                    Ext.getCmp('captureLeftSide' + orderId).disable(true);
                } else {
                    Ext.getCmp('detailCaptureGrid' + orderId).enable(true);
                    Ext.getCmp('captureLeftSide' + orderId).enable(true);
                }
            }
        });
    },

    /**
     * On Log Click
     *
     * @param data
     */
    onLogClick: function (data) {
        Ext.Msg.alert('Log Message', data.message);
    },

    onCreateCapture: function (orderId, amount) {
        var me = this;

        Ext.Ajax.request({
            url: '{url controller="PayolutionCapture" action="createCaptureAbsolute"}',
            method: 'POST',
            params: {
                orderId: orderId,
                amount: amount
            },
            success: function () {
                me.reloadStore('PayolutionCapture', orderId);
                me.reloadStore('PayolutionRefund', orderId);
                me.reloadStore('PayolutionLog', orderId);
                me.setWorkflowState(orderId);
            }
        });
    },

    onCreateCapturePositions: function (orderId, items) {
        var me = this;

        var itemData = new Array();

        Ext.each(items, function (item) {
            itemData.push({
                id: item.data.id,
                quantity: item.data.quantity,
                additionalId: item.data.additionalId
            });
        });

        Ext.Ajax.request({
            url: '{url controller="PayolutionCapture" action="createCapturePositions"}',
            method: 'POST',
            params: {
                orderId: orderId,
                positions: Ext.encode(itemData)
            },
            success: function () {
                me.reloadStore('PayolutionCapture', orderId);
                me.reloadStore('PayolutionRefund', orderId);
                me.reloadStore('PayolutionLog', orderId);
                me.setWorkflowState(orderId);
            }
        });
    },

    onCreateRefund: function (orderId, amount) {
        var me = this;
        Ext.Ajax.request({
            url: '{url controller="PayolutionRefund" action="createRefundAbsolute"}',
            method: 'POST',
            params: {
                orderId: orderId,
                amount: amount
            },
            success: function () {
                me.reloadStore('PayolutionCapture', orderId);
                me.reloadStore('PayolutionRefund', orderId);
                me.reloadStore('PayolutionLog', orderId);
                me.setWorkflowState(orderId);
            }
        });
    },

    onCreateRefundPositions: function (orderId, items) {
        var me = this;

        var itemData = new Array();

        Ext.each(items, function (item) {
            itemData.push({
                id: item.data.id,
                quantity: item.data.quantity,
                additionalId: item.data.additionalId
            });
        });

        Ext.Ajax.request({
            url: '{url controller="PayolutionRefund" action="createRefundPositions"}',
            method: 'POST',
            params: {
                orderId: orderId,
                positions: Ext.encode(itemData)
            },
            success: function () {
                me.reloadStore('PayolutionCapture', orderId);
                me.reloadStore('PayolutionRefund', orderId);
                me.reloadStore('PayolutionLog', orderId);
                me.setWorkflowState(orderId);
            }
        });
    },

    /**
     * Reload an specific store
     *
     * @param store
     * @param orderId
     */
    reloadStore:function (store, orderId) {
        var extJsStore = Ext.data.StoreManager.lookup(store + orderId);

        extJsStore.load({
            params: {
                id: orderId
            }
        });
    }
});
//{/block}