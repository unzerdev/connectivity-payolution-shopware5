<?php

namespace Payolution\Request\Builder;

use Payolution\Request\Builder\Mapper\BasketMapper;
use Payolution\Request\Builder\Mapper\SystemMapper;
use Payolution\Request\Builder\Mapper\UserMapper;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class RequestBuilderAbstract
 *
 * @package Payolution\Request\Builder
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
abstract class RequestBuilderAbstract implements RequestBuilderInterface
{
    /**
     * @var string
     */
    protected $paymentType;

    /**
     * @var BasketMapper
     */
    protected $basketMapper;

    /**
     * @var SystemMapper
     */
    protected $systemMapper;

    /**
     * @var UserMapper
     */
    protected $userMapper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * RequestBuilderAbstract constructor.
     *
     * @param BasketMapper $basketMapper
     * @param SystemMapper $systemMapper
     * @param UserMapper $userMapper
     * @param LoggerInterface $logger
     */
    public function __construct(
        BasketMapper $basketMapper,
        SystemMapper $systemMapper,
        UserMapper $userMapper,
        LoggerInterface $logger
    ) {
        $this->basketMapper = $basketMapper;
        $this->systemMapper = $systemMapper;
        $this->userMapper = $userMapper;
        $this->logger = $logger;
    }

    /**
     * Supports Builder Request
     *
     * @param string $mode
     * @return bool
     */
    public function supports($mode)
    {
        return $mode === $this->paymentType;
    }

    /**
     * Build Request Array
     *
     * @param RequestOptions $options
     * @param RequestContext $context
     * @return array
     */
    public function buildRequest(RequestOptions $options, RequestContext $context)
    {
        $this->logBuildProcess($options);

        $request = [];
        $this->systemMapper->mapRequest($context, $request);
        $this->basketMapper->mapRequest($options, $context, $request);
        $this->userMapper->mapRequest($options, $context, $request);
        $this->mapArray($options, $context, $request);

        if ($options->isPreCheck()) {
            $this->logger->debug('add preCheck channel to request');
            $request['CRITERION.PAYOLUTION_PRE_CHECK'] = 'TRUE';
            $request['TRANSACTION.CHANNEL'] = $context->getConfig()->getChannelPrecheck();
        }

        if (!$options->isPreCheck() && ($preCheckId = $context->getPreCheckId())) {
            $this->logger->debug(sprintf('add preCheck id "%s" to request', $preCheckId));
            $request['CRITERION.PAYOLUTION_PRE_CHECK_ID'] = $preCheckId;
        }

        if ($referenceId = $context->getReferenceId()) {
            $this->logger->debug(sprintf('add reference id "%s" to request', $referenceId));
            $request['IDENTIFICATION.REFERENCEID'] = $referenceId;
        }

        ksort($request);

        return $request;
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
    abstract protected function mapArray(RequestOptions $options, RequestContext $context, array &$request);

    /**
     * Log Build Process
     *
     * @param RequestOptions $options
     *
     * @return void
     */
    private function logBuildProcess(RequestOptions $options)
    {
        $serializer = new Serializer([new ObjectNormalizer()]);

        $this->logger->debug(
            sprintf(
                'Build request with options "%s"',
                json_encode($serializer->normalize($options))
            )
        );
    }
}
