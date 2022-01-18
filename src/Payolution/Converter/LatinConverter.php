<?php

namespace Payolution\Converter;

use Transliterator\Settings;
use Transliterator\Transliterator;

/**
 * Helper to convert chars to latin
 *
 * @package Payolution\Converter
 * @author Martin Knoop <martin.knoop@bestit-online.de>
 */
class LatinConverter
{
    /**
     * Convert to latin
     *
     * @param mixed $value
     * @return string
     */
    public function convert($value)
    {
        $result = null;
        if (is_array($value)) {
            $result = [];
            foreach ($value as $key => $item) {
                $result[$this->convert($key)] = $this->convert($item);
            }
        } else {
            $transliterator = new Transliterator(Settings::LANG_RU);
            $result = $transliterator->cyr2Lat($value);
        }

        return $result;
    }
}