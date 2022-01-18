<?php
use PolPaymentPayolution\Config\Config;
use PolPaymentPayolution\Config\ConfigProvider;
use PolPaymentPayolution\Models\Payolution\Payment\WorkflowHistory;
use PolPaymentPayolution\Models\Payolution\Payment\WorkflowHistoryRepository;

/**
 * Class Shopware_Controllers_Backend_PayolutionLog
 *
 * Provides functions for payment logs.
 */
class Shopware_Controllers_Backend_PayolutionLog extends Shopware_Controllers_Backend_ExtJs
{
    /**
     * @var WorkflowHistoryRepository
     */
    private $workflowHistoryRepository;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * Get WorkflowHistoryRepository
     *
     * @return WorkflowHistoryRepository
     */
    private function getWorkflowHistoryRepo()
    {
        if (!$this->workflowHistoryRepository) {
            $this->workflowHistoryRepository = $this->container->get('pol_payment_payolution.repository.workflowhistory');
        }

        return $this->workflowHistoryRepository;
    }

    /**
     * Get PluginConfig
     *
     * @return Config
     */
    private function getPluginConfig()
    {
        if (!$this->configProvider) {
            $this->configProvider = $this->container->get('pol_payment_payolution.config.config_provider');
        }

        return $this->configProvider->getConfig();
    }

    /**
     * Get Logs Action
     *
     * @return void
     */
    public function getLogsAction()
    {
        $orderId = $this->Request()->id;

        /**
         * @var WorkflowHistory[]
         */
        $entries = $this->getWorkflowHistoryRepo()->getAllEntriesByOrderId($orderId);

        $data = [];

        $lastRequest = '';
        $requestNumber = 0;

        /**
         * @var WorkflowHistory $entry
         */
        foreach ($entries as $entry) {
            $data[] = [
                'requestid' => $requestNumber =  $this->createRequestId($entry, $requestNumber, $lastRequest),
                'orderId' => $entry->getOrderId(),
                'date' => $entry->getCaptureDate()->format('Y-m-d'),
                'articlename' => $entry->getName(),
                'quantity' => $entry->getQuantity(),
                'amount' => $entry->getAmount(),
                'state' => $entry->isSuccess() ? 'Success' : 'Error',
                'type' => ucfirst($entry->getType()),
                'message' => $entry->getMessage()
            ];
        }

        $this->View()->assign([
            'success' => true,
            'data' => $this->createLogEntry($data),
            'total' => count($data)
        ]);
    }

    /**
     * Create Log Entry
     *
     * @param array $data
     *
     * @return array
     */
    private function createLogEntry(array $data)
    {
        if ($this->getPluginConfig()->isHistorySimpleView()) {
            $logEntries = [];

            foreach ($data as $datum) {
                $amount = isset($logEntries[$datum['requestid']]['amount']) ? $logEntries[$datum['requestid']]['amount'] : 0;
                $amount += $datum['amount'];
                $logEntries[$datum['requestid']]['requestid'] = $datum['requestid'];
                $logEntries[$datum['requestid']]['amount'] = $amount;
                $logEntries[$datum['requestid']]['articlename'] = $datum['type'];
                $logEntries[$datum['requestid']]['quantity'] = $datum['quantity'];
                $logEntries[$datum['requestid']]['message'] = $datum['message'];
                $logEntries[$datum['requestid']]['type'] = $datum['type'];
                $logEntries[$datum['requestid']]['date'] = $datum['date'];
                $logEntries[$datum['requestid']]['orderId'] = $datum['orderId'];
                $logEntries[$datum['requestid']]['state'] = $datum['state'];
            }

            return array_values($logEntries);
        }

        return array_values($data);
    }

    /**
     * Create Request Id
     *
     * @param WorkflowHistory $entry
     * @param int $requestNumber
     * @param string $lastRequest
     *
     * @return int
     */
    private function createRequestId(WorkflowHistory $entry, $requestNumber, &$lastRequest)
    {
        if ($lastRequest !== $request = $entry->getRequest()) {
            $requestNumber++;
        }

        $lastRequest = $request;

        return $requestNumber;
    }
}
