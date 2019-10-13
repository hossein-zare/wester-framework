<?php

    namespace Database;

    use PDO;
    use PDOException;
    use Exception;
    use Cactus\QueryBuilder\Builder;
    use Packages\Laravel\Support\Traits\ForwardsCalls;
    use Powerhouse\Freeway\ArrayIterator;
    use Powerhouse\Interfaces\Json\Jsonable;
    use Powerhouse\Interfaces\Json\JsonString;

    class DB
    {
        use ForwardsCalls;

        /**
         * Create a raw query.
         * 
         * @param  string  $statement
         * @param  array  $parameters
         * @return \Database\Raw
         */
        public function raw($statement, $parameters = [])
        {
            return (new Raw($statement, $parameters));
        }

        /**
         * Create a full raw query.
         * 
         * @param  string  $statement
         * @param  array  $parameters
         * @return \Database\Raw
         */
        public function fullRaw($statement, $parameters = [])
        {
            return (new FullRaw($statement, $parameters));
        }

        /**
         * Perform a dynamic call.
         * 
         * @param  string  $method
         * @param  array  $arguments
         * @return \Cactus\QueryBuilder\Builder
         */
        public function __call($method, $arguments)
        {
            $builder = new Builder();
            return $this->forwardCallTo($builder, $method, $arguments);
        }

        /**
         * Perform a dynamic static call.
         * 
         * @param  string  $method
         * @param  array  $arguments
         * @return  $this
         */
        public static function __callStatic($method, $arguments)
        {
            return (new static())->$method(...$arguments);
        }

    }
