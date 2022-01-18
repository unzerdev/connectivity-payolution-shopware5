<?php

namespace Payolution\Request\B2B;

use DateTime;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class InfoResolver
 *
 * @package Payolution\Request\B2B
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class InfoResolver extends OptionsResolver
{
    /**
     * InfoResolver constructor.
     */
    public function __construct()
    {
        $this->setRequired('lastName');
        $this->setRequired('firstName');
        $this->setRequired('birthday');
        $this->setRequired('type');
        $this->setRequired('company');
        $this->setRequired('userId');
        $this->setRequired('vat');

        $this->setNormalizer('birthday', function (OptionsResolver $optionsResolver, $optionValue) {
            return (new DateTime($optionValue))->format('Y-m-d');
        });
    }
}