<?php

namespace PolPaymentPayolution\Normalizer;

use PolPaymentPayolution\Payment\Order\OrderPosition;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class PositionNormalizer
 *
 * @package PolPaymentPayolution\Normalizer
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class PositionNormalizer implements NormalizerInterface
{
    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param object $object object to normalize
     * @param string $format format the normalization result will be encoded as
     * @param array $context Context options for the normalizer
     *
     * @return array|scalar
     */
    public function normalize($object, $format = null, array $context = array())
    {
        /**
         * @var OrderPosition $position
         */
        $position = $object;

        return [
            'id' => $position->getIdentifier()->getIdentifier(),
            'additionalId' => $position->getIdentifier()->getAdditionalIdentifier(),
            'amount' => $position->getAmount()->getValue(),
            'quantity' => $position->getQuantity(),
            'name' => $position->getName()
        ];
    }

    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed $data Data to normalize
     * @param string $format The format being (de-)serialized from or into
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof OrderPosition;
    }
}