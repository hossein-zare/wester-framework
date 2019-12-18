<?php

namespace Powerhouse\Helpers;

class ArrayHelper
{

    /**
     * Get the index of the last element of an array.
     * 
     * @param  array  $array
     * @return int|null
     */
    public static function getLastIndex(array $array)
    {
        $length = count($array);
        return $length > 0 ? (int) ($length - 1) : null;
    }

}
