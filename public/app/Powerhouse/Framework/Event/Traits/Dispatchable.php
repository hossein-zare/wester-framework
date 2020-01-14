<?php

    namespace Powerhouse\Event\Traits;

    trait Dispatchable
    {

        /**
         * Set the listeners.
         * 
         * @param  array  $listeners
         * @return void
         */
        public function listeners(array $listeners)
        {
            foreach ($listeners as $listener) {
                $listener = new $listener;
                $listener->handle($this);
            }
        }

    }
