<?php

namespace Payolution\Request\B2B;

use Enlight_Components_Db_Adapter_Pdo_Mysql;
use Exception;
use Payolution\Enum\B2B;
use Payolution\Request\Builder\Mapper\BasketMapper;
use Payolution\Request\Builder\Mapper\SystemMapper;
use Payolution\Request\Builder\Mapper\UserMapper;
use Payolution\Request\Builder\RequestBuilderAbstract;
use Payolution\Request\Builder\RequestContext;
use Payolution\Request\Builder\RequestOptions;
use PolPaymentPayolution\ComponentManager\ComponentManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class RequestBuilder
 *
 * @package Payolution\Request\B2B
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class RequestBuilder extends RequestBuilderAbstract
{
    /**
     * @var ComponentManagerInterface
     */
    private $componentManager;

    /**
     * RequestBuilder constructor.
     *
     * @param BasketMapper $basketMapper
     * @param SystemMapper $systemMapper
     * @param UserMapper $userMapper
     * @param ComponentManagerInterface $componentManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        BasketMapper $basketMapper,
        SystemMapper $systemMapper,
        UserMapper $userMapper,
        ComponentManagerInterface $componentManager,
        LoggerInterface $logger
    ) {
        $this->paymentType = 'PAYOLUTION_INVOICE_B2B';
        $this->componentManager = $componentManager;
        parent::__construct($basketMapper, $systemMapper, $userMapper, $logger);
    }

    /**
     * Map Request
     *
     * @param RequestOptions $options
     * @param RequestContext $context
     * @param array $request
     *
     * @return void
     */
    protected function mapArray(RequestOptions $options, RequestContext $context, array &$request)
    {
        $request['CRITERION.PAYOLUTION_TRX_TYPE'] = 'B2B';
        $request['TRANSACTION.CHANNEL'] = $context->getConfig()->getChannelB2bInvoice();

        $infos = $this->loadB2BInfos($options);

        if (!$infos || !is_array($infos)) {
            $infos = [];

            $this->logger->error('no b2b infos found');
        }

        try {
            $normalizedInfos = (new InfoResolver())->resolve($infos);
        } catch (Exception $e) {
            $this->logger->error(sprintf('Error in normalized b2b info array: "%s"', $e->getMessage()));
            return;
        }

        $request['CRITERION.PAYOLUTION_COMPANY_UID'] = $normalizedInfos['vat'];
        $request['CRITERION.PAYOLUTION_COMPANY_NAME'] = $normalizedInfos['company'];
        $type = $normalizedInfos['type'];
        $mapping = B2B::TYPE_MAPPING;
        if (isset($mapping[$type])) {
            $request['CRITERION.PAYOLUTION_COMPANY_TYPE'] = $mapping[$type];
            if ($infos['type'] === 'soletrader') {
                $this->mapSoleTrader($normalizedInfos, $request);
            }
        }
    }

    /**
     * Map Sole Trader
     *
     * @param array $b2bInfos
     * @param array $request
     *
     * @return void
     */
    private function mapSoleTrader(array $b2bInfos, array &$request)
    {
        $request['CRITERION.PAYOLUTION_COMPANY_OWNER_FAMILY'] = $b2bInfos['lastName'];
        $request['CRITERION.PAYOLUTION_COMPANY_OWNER_GIVEN'] = $b2bInfos['firstName'];
        $request['CRITERION.PAYOLUTION_COMPANY_OWNER_BIRTHDATE'] = $b2bInfos['birthday'];
    }

    /**
     * Load B2B Info
     *
     * @param RequestOptions $options
     *
     * @return array
     */
    private function loadB2BInfos(RequestOptions $options)
    {
        $user = $options->getUser();
        $userId = $user['additional']['user']['userID'];

        return $this
            ->componentManager
            ->getDatabase()
            ->fetchRow('SELECT * FROM bestit_payolution_b2b WHERE userId = ?', $userId);
    }
}
