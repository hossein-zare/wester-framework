<?php

namespace Contracts\Helpers\Traits;

interface MagicCallInterface
{

    public function createCaller();
    public function __call(string $method, array $args);
    public static function __callStatic(string $method, array $args);

}