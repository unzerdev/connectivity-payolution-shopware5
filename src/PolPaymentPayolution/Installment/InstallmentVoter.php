<?php

namespace PolPaymentPayolution\Installment;

use Payolution\Config\AbstractConfig;
use PolPaymentPayolution\ComponentManager\ComponentManagerInterface;
use PolPaymentPayolution\Payment\PaymentProvider;

/**
 * Class InstallmentVoter
 *
 * @package PolPaymentPayolution\Installment
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class InstallmentVoter
{
    /**
     * @var AbstractConfig
     */
    private $config;

    /**
     * @var PaymentProvider
     */
    private $paymentProvider;

    /**
     * InstallmentVoter constructor.
     *
     * @param AbstractConfig $config
     * @param PaymentProvider $paymentProvider
     */
    public function __construct(AbstractConfig $config, PaymentProvider $paymentProvider)
    {
        $this->config = $config;
        $this->paymentProvider = $paymentProvider;
    }

    /**
     * Vote on Article
     *
     * @param array $article
     *
     * @return bool
     */
    public function vote(array $article)
    {
        $price = $this->normalizePrice($this->extractPriceFromArticle($article));

        return $this->paymentProvider->isPaymentActive('payolution_installment')
            && (
                $price >= $this->normalizePrice($this->config->getMinInstallmentDetailValue())
                &&  $price <= $this->normalizePrice($this->config->getMaxInstallmentDetailValue())
            );
    }

    /**
     * Extract Price from Article
     *
     * @param array $article
     *
     * @return string
     */
    private function extractPriceFromArticle(array $article)
    {
        $price = 0;
        if (isset($article['price'])) {
            $price = $article['price'];
        }

        return (string) $price;
    }

    /**
     * Normalize Price
     *
     * @param string $price
     *
     * @return string
     */
    private function normalizePrice($price)
    {
        return (float) str_replace(',', '.', $price);
    }
}