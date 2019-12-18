<?php

    /**
     * @with    Compatibility Changes
     * @author  Laravel
     */

    namespace Powerhouse\Cache\Drivers\Memcache;

    use Powerhouse\Cache\Lock as LockCapsule;

    class Lock extends LockCapsule
    {

        /**
         * The Memcache instance.
         *
         * @var \Memcache
         */
        protected $memcache;

        /**
         * Create a new lock instance.
         *
         * @param  \Memcache  $memcache
         * @param  string  $name
         * @param  int  $seconds
         * @param  string|null  $owner
         * @return void
         */
        public function __construct($memcache, $name, $seconds, $owner = null)
        {
            parent::__construct($name, $seconds, $owner);

            $this->memcache = $memcache;
        }

        /**
         * Attempt to acquire the lock.
         *
         * @return bool
         */
        public function acquire()
        {
            return $this->memcache->add(
                $this->name, $this->owner, false, $this->seconds
            );
        }

        /**
         * Release the lock.
         *
         * @return void
         */
        public function release()
        {
            if ($this->isOwnedByCurrentProcess()) {
                $this->memcache->delete($this->name);
            }
        }

        /**
         * Releases this lock in disregard of ownership.
         *
         * @return void
         */
        public function forceRelease()
        {
            $this->memcache->delete($this->name);
        }

        /**
         * Returns the owner value written into the driver for this lock.
         *
         * @return mixed
         */
        protected function getCurrentOwner()
        {
            return $this->memcache->get($this->name);
        }
    }
