<?php

    namespace Powerhouse\Interfaces\Cache;

    interface CacheDriverAttribute
    {

        public function getMulti(array $keys);
        public function get(string $key);
        public function set(string $key, $value, int $expiration);
        public function add(string $key, $value, int $expiration);
        public function delete(string $key);
        public function flush();
        public function setMulti(array $items, int $expiration);
        public function increment(string $key, int $value);
        public function decrement(string $key, int $value);

    }
