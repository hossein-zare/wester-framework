<?php

    namespace Cactus;

    use PDO;
    use PDOException;
    use Exception;
    use Database\Cache;
    use Cactus\QueryBuilder\Builder;
    use Packages\Laravel\Support\Traits\ForwardsCalls;

    abstract class Model extends Model\Extension
    {
        use ForwardsCalls;

        /**
         * Table name.
         * 
         * @var string
         */
        protected $table;

        /**
         * Primary key.
         * 
         * @var string
         */
        protected $primaryKey = 'id';

        /**
         * The list of properties.
         * 
         * @var array
         */
        protected $valueProperties = [];

        /**
         * Default connection/
         * 
         * @var string
         */
        protected $connection = null;

        /**
         * Query builder.
         * 
         * @var \Cactus\QuilderBuilder\Builder
         */
        protected $builder;

        /**
         * Create a model instance.
         * 
         * @return void
         */
        public function __construct()
        {
            $this->table = $this->getTable();
        }

        /**
         * Get instance.
         * 
         * @return $this
         */
        public static function getInstance()
        {
            return new static();
        }

        /**
         * Get the name of the table.
         * 
         * @return string
         */
        protected function getTable()
        {
            return $this->table ?? strtolower((new \ReflectionClass($this))->getShortName()) . 's';
        }

        /**
         * Set new value properties.
         * 
         * @param  string  $name
         * @param  mixed  $value
         * @return void
         */
        protected function setValueProperties($name, $value)
        {
            $this->valueProperties = array_merge($this->valueProperties, [$name => $value]);
        }

        /**
         * Set a new property.
         * 
         * @param  string  $name
         * @param  mixed  $value
         * @return void
         */
        public function __set($name, $value) {
            $this->setValueProperties($name, $value);
        }

        /**
         * Look for the scope.
         * 
         * @param  string  $method
         * @param  array  $arguments
         * @return void
         */
        protected function findScope($method)
        {
            // Get the model
            $model = get_called_class();

            // Create a scope method
            $scope = 'scope' . ucfirst($method);

            if (method_exists($model, $scope)) {
                return array_to_object(['found' => true, 'method' => $scope]);
            } else {
                return array_to_object(['found' => false]);
            }
        }

        /**
         * Forward all calls to the query builder.
         * 
         * @param  string  $method
         * @param  array  $arguments
         * @return \Cactus\QueryBuilder\Builder
         */
        protected function callQueryBuilder($method, $arguments)
        {
            // Look for the scope
            $scope = $this->findScope($method);
            if ($scope->found === true) {
                return $this->{$scope->method}(...$arguments);
            }

            if ($this->builder === null) {
                $builder = new Builder($this->connection);
                $builder->setProperties(get_object_vars($this));

                $this->builder = $builder;
            } else {
                $this->builder->valueProperties($this->valueProperties);
            }

            return $this->forwardCallTo($this->builder, $method, $arguments);
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
            return $this->callQueryBuilder($method, $arguments);
        }

        /**
         * Perform a dynamic static call.
         * 
         * @param  string  $method
         * @param  array  $arguments
         * @return $this
         */
        public static function __callStatic($method, $arguments)
        {
            return (new static())->$method(...$arguments);
        }

    }