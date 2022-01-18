<?php

namespace PolPaymentPayolution\Bundle\StoreFrontBundle;

use Shopware\Bundle\StoreFrontBundle\Service\ListProductServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct;

class ListProductService implements ListProductServiceInterface
{
    /**
     * @var ListProductServiceInterface
     */
    private $coreService;

    /**
     * @param ListProductServiceInterface $coreService
     */
    function __construct(ListProductServiceInterface $coreService)
    {
        $this->coreService = $coreService;
    }

    /**
     * @param array $numbers
     * @param Struct\ProductContextInterface $context
     * @return Struct\ListProduct[]
     * @throws \Exception
     */
    public function getList(array $numbers, Struct\ProductContextInterface $context)
    {
        $products = $this->coreService->getList($numbers, $context);

        foreach ($products as $product) {
            $attribute = new Struct\Attribute();
            $prices = $product->getPrices();
            $tax = $product->getTax();

            foreach ($prices as $price) {
                if (!$context->getCurrentCustomerGroup()->displayGrossPrices()) {
                    $attribute->set(
                        'price',
                        round($price->getCalculatedPrice() * (1 + ($tax->getTax() / 100)), 2)
                    );
                } else {
                    $attribute->set(
                        'price',
                        round($price->getCalculatedPrice(), 2)
                    );
                }
                $product->addAttribute('payolution', $attribute);
                break;
            }
        }


        return $products;
    }

    /**
     * @param string $number
     * @param Struct\ProductContextInterface $context
     * @return mixed
     */
    public function get($number, Struct\ProductContextInterface $context)
    {
        $products = $this->getList([$number], $context);
        return array_shift($products);
    }

    /**
     * @return ListProductServiceInterface
     */
    public function getCoreService()
    {
        return $this->coreService;
    }
}