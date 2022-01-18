<?php

namespace Payolution\Request\Builder\Mapper;

use Payolution\Request\Builder\RequestContext;
use Payolution\Request\Builder\RequestOptions;
use Payolution\Request\Builder\UniqueNumberTrait;

/**
 * Class BasketMapper
 *
 * @package Payolution\Request\Builder\Mapper
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class BasketMapper
{
    use UniqueNumberTrait;

    /**
     * Prefix for the trx value
     * @var string
     */
    private $usagePrefix = 'Trx ';

    /**
     * Map Request
     *
     * @param RequestOptions $options
     * @param RequestContext $context
     * @param array $request
     *
     * @return void
     */
    public function mapRequest(RequestOptions $options, RequestContext $context, array &$request)
    {
        $basket = $options->getBasket();
        $items = [];
        /**
         * @var array $content
         */
        $content = $basket['content'];
        $amount = $basket['AmountNumeric'];

        if ($options->isTaxFree()) {
            foreach ($content as $position) {
                $items[] = array(
                    'DESCR' => $position['articlename'],
                    'PRICE' => round((float) str_replace(',', '.', $position['amountnet']), 2),
                    'TAX' => '0.00',
                );
            }
        } else {
            foreach ($content as $position) {
                if (!isset($position['amountWithTax']) || empty($position['amountWithTax'])) {
                    $position['amountWithTax'] = $position['amount'];
                }

                $items[] = array(
                    'DESCR' => $position['articlename'],
                    'PRICE' => round((float) str_replace(',', '.', $position['amountWithTax']), 2),
                    'TAX' => str_replace(',', '.', $position['tax']),
                );
            }

            if (!empty($basket['AmountWithTaxNumeric']) && isset($basket['AmountWithTaxNumeric'])) {
                $amount = $basket['AmountWithTaxNumeric'];
            }
        }

        $request['PRESENTATION.AMOUNT'] = $amount;
        $request['PRESENTATION.CURRENCY'] = $context->getShop()->getCurrency()->getCurrency();
        $request['PRESENTATION.USAGE'] = $this->usagePrefix . $context->getTrxId();
        $request['CRITERION.PAYOLUTION_TAX_AMOUNT'] = $amount - $basket['AmountNetNumeric'];

        $counter = 1;
        foreach ($items as $position) {
            $request['CRITERION.PAYOLUTION_ITEM_DESCR_' .$counter] = $position['DESCR'];
            $request['CRITERION.PAYOLUTION_ITEM_PRICE_' .$counter] = $position['PRICE'];
            $request['CRITERION.PAYOLUTION_ITEM_TAX_' .$counter] = $position['TAX'];
            $counter++;
        }
    }
}
