<?php

    namespace Powerhouse\Cache;

    use Powerhouse\Castles\Repository;

    abstract class Resolver
    {

        /**
         * Resolve the driver instance.
         * 
         * @return object
         */
        protected function resolver()
        {
            $driver = ucfirst($this->driver);
            $attributes = "\\Powerhouse\\Cache\\Drivers\\$driver\\Attributes";

            $repository = new Repository();

            if ($repository->has('cache', $this->defaultConnection))
                return $repository->get('cache', $this->defaultConnection);

            $attr = (new $attributes($this->conn()));
            $repository->store('cache', $this->defaultConnection, $attr);

            return $repository->get('cache', $this->defaultConnection);
        }

    }
