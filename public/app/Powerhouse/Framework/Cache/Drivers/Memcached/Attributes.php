<?php

    /**
     * This file has derived somewhat from Laravel.
     * 
     * @with    Compatibility Changes
     * @author  Wester, Laravel
     */

    namespace Powerhouse\Cache\Drivers\Memcached;

    use Memcached;
    use Powerhouse\Interfaces\Cache\CacheDriverAttribute;

    class Attributes implements CacheDriverAttribute
    {

        /**
         * The Memcached instance
         *
         * @var \Memcached
         */
        protected $memcached;

        /**
         * Indicates whether we are using Memcached version >= 3.0.0.
         *
         * @var bool
         */
        protected $onVersionThree;

        /**
         * Create a new instance.
         * 
         * @param  \Memcached  $memcached
         * @return void
         */
        public function __construct($memcached)
        {
            $this->memcached = $memcached;
            $this->onVersionThree = (new ReflectionMethod('Memcached', 'getMulti'))
                            ->getNumberOfParameters() == 2;
        }

        /**
         * Get the value.
         * 
         * @param  string  $key
         * @return mixed
         */
        public function get(string $key)
        {
            $value = $this->memcached->get($key);
            if ($this->memcached->getResultCode() == 0) {
                return $value;
            }
        }

        /**
         * Retrieve multiple items.
         * 
         * @param  string  $key
         * @return mixed
         */
        public function getMulti(array $keys)
        {
            if ($this->onVersionThree) {
                $values = $this->memcached->getMulti($keys, Memcached::GET_PRESERVE_ORDER);
            } else {
                $values = $this->memcached->getMulti($keys, null, Memcached::GET_PRESERVE_ORDER);
            }
    
            if ($this->memcached->getResultCode() != 0) {
                return array_fill_keys($keys, null);
            }
    
            return array_combine($keys, $values);
        }

        /**
         * Store an item.
         * 
         * @param  string  $key
         * @param  mixed  $value
         * @param  int  $expiration
         * @return bool
         */
        public function set(string $key, $value, int $expiration = 0)
        {
            return $this->memcached->set($key, $value, $this->calculateExpiration($expiration));
        }

        /**
         * Store multiple items.
         * 
         * @param  array  $items
         * @param  int  $expiration
         * @return bool
         */
        public function setMulti(array $items, int $expiration)
        {
            return $this->memcached->setMulti(
                $items, $this->calculateExpiration($expiration)
            );
        }

        /**
         * Add a key.
         * 
         * @param  string  $key
         * @param  mixed  $value
         * @param  int  $expiration
         * @return bool
         */
        public function add(string $key, $value, int $expiration = 0)
        {
            return $this->memcached->add($key, $value, $this->calculateExpiration($expiration));
        }

        /**
         * Increment numeric item's value.
         *
         * @param  string  $key
         * @param  int  $value
         * @return int|bool
         */
        public function increment(string $key, int $value = 1)
        {
            return $this->memcached->increment($key, $value);
        }

        /**
         * Decrement numeric item's value.
         *
         * @param  string  $key
         * @param  int  $value
         * @return int|bool
         */
        public function decrement(string $key, int $value = 1)
        {
            return $this->memcached->decrement($key, $value);
        }

        /**
         * Get a lock instance.
         *
         * @param  string  $name
         * @param  int  $seconds
         * @param  string|null  $owner
         * @return \Powerhouse\Cache\Drivers\Memcached\Lock
         */
        public function lock($name, $seconds = 0, $owner = null)
        {
            return new Lock($this->memcached, $name, $seconds, $owner);
        }

        /**
         * Add the value.
         * 
         * @param  string  $key
         * @return bool
         */
        public function delete(string $key)
        {
            return $this->memcached->delete($key);
        }

        /**
         * Invalidate all items in the cache.
         * 
         * @return bool
         */
        public function flush()
        {
            return $this->memcached->flush();
        }

        /**
         * Set a new expiration on an item.
         * 
         * @param  string  $key
         * @param  int  $expiration
         * @return bool
         */
        public function touch(string $key, int $expiration = 0)
        {
            return $this->memcached->touch($key, $expiration);
        }

        /**
         * Get the expiration time of the key.
         *
         * @param  int  $seconds
         * @return int
         */
        protected function calculateExpiration($seconds)
        {
            return $this->toTimestamp($seconds);
        }

        /**
         * Get the UNIX timestamp for the given number of seconds.
         *
         * @param  int  $seconds
         * @return int
         */
        protected function toTimestamp($seconds)
        {
            return $seconds > 0 ? $this->availableAt($seconds) : 0;
        }

    }
