<?php

    namespace Powerhouse\Cache\Drivers\Memcache;

    use Memcache;
    use Powerhouse\Interfaces\Cache\CacheDriverAttribute;
    use Packages\Laravel\Support\InteractsWithTime;

    class Attributes implements CacheDriverAttribute
    {
        use InteractsWithTime;

        /**
         * The Memcache instance
         *
         * @var \Memcache
         */
        protected $memcache;

        /**
         * Create a new instance.
         * 
         * @param  \Memcache  $memcache
         * @return void
         */
        public function __construct($memcache)
        {
            $this->memcache = $memcache;
        }

        /**
         * Get the value.
         * 
         * @param  string  $key
         * @return mixed
         */
        public function get(string $key)
        {
            return $this->memcache->get($key);
        }

        /**
         * Retrieve multiple items.
         * 
         * @param  string  $key
         * @return mixed
         */
        public function getMulti(array $keys)
        {
            return $this->memcache->get($keys);
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
            return $this->memcache->set($key, $value, false, $expiration);
        }

        /**
         * Store multiple items.
         * 
         * @param  array  $items
         * @param  int  $expiration
         * @return bool
         */
        public function setMulti(array $items, int $expiration = 0)
        {
            foreach ($items as $item) {
                $this->set($item['0'], $item['1'], $expiration);
            }

            return true;
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
            return $this->memcache->add($key, $value, false, $expiration);
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
            return $this->memcache->increment($key, $value);
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
            return $this->memcache->decrement($key, $value);
        }

        /**
         * Get a lock instance.
         *
         * @param  string  $name
         * @param  int  $seconds
         * @param  string|null  $owner
         * @return \Powerhouse\Cache\Drivers\Memcache\Lock
         */
        public function lock($name, $seconds = 0, $owner = null)
        {
            return new Lock($this->memcache, $name, $seconds, $owner);
        }

        /**
         * Restore a lock instance using the owner identifier.
         *
         * @param  string  $name
         * @param  string  $owner
         * @return \Powerhouse\Cache\Drivers\Memcached\Lock
         */
        public function restoreLock($name, $owner)
        {
            return $this->lock($name, 0, $owner);
        }

        /**
         * Add the value.
         * 
         * @param  string  $key
         * @return bool
         */
        public function delete(string $key)
        {
            return $this->memcache->delete($key);
        }

        /**
         * Invalidate all items in the cache.
         * 
         * @return bool
         */
        public function flush()
        {
            return $this->memcache->flush();
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
