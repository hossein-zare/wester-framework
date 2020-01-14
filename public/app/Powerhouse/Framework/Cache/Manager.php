<?php

    namespace Powerhouse\Cache;

    abstract class Manager extends Resolver
    {

        /**
         * Set a new cache store.
         * 
         * @param  string  $name
         * @return $this
         */
        public function store($name)
        {
            $this->setConnection($name);
            return $this;
        }

        /**
         * Get the value.
         * 
         * @param  string  $key
         * @param  string|null|callback  $default
         * @return mixed
         */
        public function get(string $key, $default = null)
        {
            return $this->resolver()->get($key) ?? $default;
        }

        /**
         * Retrieve multiple items.
         * 
         * @param  string  $key
         * @return mixed
         */
        public function getMulti(array $keys)
        {
            return $this->resolver()->getMulti($keys);
        }

        /**
         * Set the value.
         * 
         * @param  string  $key
         * @param  mixed  $value
         * @param  int  $expiration
         * @return bool
         */
        public function set(string $key, $value, int $expiration = 0)
        {
            return $this->resolver()->set($key, $value, $expiration);
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
            return $this->resolver()->setMulti(
                $items, $expiration
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
            return $this->resolver()->add($key, $value, $expiration);
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
            return $this->resolver()->increment($key, $value);
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
            return $this->resolver()->decrement($key, $value);
        }

        /**
         * Delete the value.
         * 
         * @param  string  $key
         * @return bool
         */
        public function delete(string $key)
        {
            return $this->resolver()->delete($key);
        }

        /**
         * Invalidate all items in the cache.
         * 
         * @return bool
         */
        public function flush()
        {
            return $this->resolver()->flush();
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
            return $this->resolver()->touch($key, $expiration);
        }

        /**
         * Get a lock instance.
         *
         * @param  string $name
         * @param  int $seconds
         * @param  string|null $owner
         * @return \Powerhouse\Cache\Drivers\Memcache\Lock
         */
        public function lock($name, $seconds = 0, $owner = null)
        {
            return $this->resolver()->lock($name, $seconds, $owner);
        }

        /**
         * Restore a lock instance using the owner identifier.
         *
         * @param  string  $name
         * @param  string  $owner
         * @return \Powerhouse\Cache\Drivers\Memcache\Lock
         */
        public function restoreLock($name, $owner)
        {
            return $this->lock($name, 0, $owner);
        }

        /**
         * Schedule the key expiration and recreate it.
         * 
         * @param  string  $key
         * @param  int  $seconds
         * @param  callback  $callback
         * @return mixed
         */
        public function remember($key, $seconds, $callback)
        {
            $value = $this->get($key);
            
            if ($value === false) {
                $value = $callback();
                $this->set($key, $value, $seconds);
            }

            return $value;
        }

    }
