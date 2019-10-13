<?php

    namespace Cactus\QueryBuilder;

    use IteratorAggregate;
    use ArrayIterator;
    use Database\Cache;
    use Powerhouse\Support\Str;
    use Database\Traits\ConnectorFinder;
    use Powerhouse\Interfaces\Database\QueryBuilder;
    use Powerhouse\Interfaces\Database\RawQuery;
    use Powerhouse\Interfaces\Database\FullRawQuery;
    use Powerhouse\Exceptions\Modal\ModalNotFoundException;

    class Builder extends Select implements QueryBuilder, IteratorAggregate
    {
        use ConnectorFinder;

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
        protected $primaryKey;

        /**
         * Indicates whether it contains distinct results.
         * 
         * @var bool
         */
        protected $distinct = false;

        /**
         * The list of properties.
         * 
         * @var array
         */
        protected $valueProperties = [];

        /**
         * The attributes that are mass assignable.
         * 
         * @var array
         */
        protected $fillable = null;

        /**
         * The attributes that aren't mass assignable.
         *
         * @var array
         */
        protected $guarded = null;

        /**
         * Default values for the attributes.
         *
         * @var array
         */
        protected $attributes = null;

        /**
         * The results.
         * 
         * @var array
         */
        protected $items = [];

        /**
         * Create a builder instance.
         * 
         * @param  string  $connection
         * @return void
         */
        public function __construct($connection = null)
        {
            $this->setConnection($connection);

            if ($connection === null) {

                // Create a connection
                if (Cache::getConnection() === null) {
                    $pdo = $this->createConnector();
                    $pdo->connect();
                }

            } else {
                $this->connection($connection);
            }
        }

        /**
         * Connect to another database.
         * 
         * @param  string  $connection
         * @return $this
         */
        public function connection(string $connection)
        {
            $this->setConnection($connection);

            // Create a new connector
            $pdo = $this->createConnector($connection);
            $pdo->connect();

            return $this;
        }

        /**
         * Set the table name.
         * 
         * @param  mixed  $name
         * @return $this
         */
        public function table($name)
        {
            if ($name instanceof RawQuery) {
                $this->table = "({$name->statement()})";
            } elseif ($name instanceof FullRawQuery) {
                $this->table = "({$name->statement()})";
            } elseif ($name instanceof QueryBuilder) {
                $this->table = "({$name->toSql()})";
            } elseif (is_string($name)) {
                $this->table = Str::wrap($name);
            }

            return $this;
        }

        /**
         * Set the table name.
         * 
         * @param  string  $name
         * @return $this
         */
        public function from($name)
        {
            $this->table = Str::wrap($name);

            return $this;
        }

        /**
         * Set the primary key.
         * 
         * @param  string  $primaryKey
         * @return $this
         */
        public function primaryKey($key)
        {
            $this->primaryKey = $key;
            return $this;
        }

        /**
         * Set the value properties.
         * 
         * @param  array  $properties
         * @return $this
         */
        public function valueProperties($properties)
        {
            $this->valueProperties = $properties;
        }

        /**
         * Set new array properties.
         * 
         * @param  array  $array
         * @return void
         */
        protected function setArrayProperties($array)
        {
            $this->valueProperties = array_merge($this->valueProperties, $array);
        }

        /**
         * Set the inherited properties.
         * 
         * @param  array  $properties
         * @return $this
         */
        public function setProperties(array $array)
        {
            foreach ($array as $property => $value) {
                $this->{$property} = $value;
            }
        }

        /**
         * Get the cached connection.
         * 
         * @return \PDO
         */
        protected function conn()
        {
            return Cache::getConnection($this->defaultConnection, $this->driver);
        }

        /**
         * Declare a distinct expression.
         * 
         * @return $this
         */
        public function distinct()
        {
            $this->distinct = true;
            return $this;
        }

        /**
         * Find the row by the primary key.
         * 
         * @param  int  $id
         * @return $this
         */
        public function find($id)
        {
            $this->where($this->primaryKey, $id);
            return $this;
        }

        /**
         * Find the row by the primary key or fail.
         * 
         * @param  int  $id
         * @return $this
         */
        public function findOrFail($id)
        {
            $this->where($this->primaryKey, $id);

            if ($this->count() === 0) {
                throw new ModalNotFoundException("MYSQL Record with Primary Key '{$id}' couldn't be found!");
            }

            // Refresh the array
            $this->select();

            return $this;
        }

        /**
         * Excute a query.
         * 
         * @param  string  $statement
         * @return void
         */
        public function exec($statement)
        {
            $this->conn()->exec($statement);
        }

        /**
         * Get the array iterator.
         * 
         * @return \ArrayIterator
         */
        public function getIterator() {
            return new ArrayIterator($this->items);
        }

        /**
         * Get the items.
         * 
         * @param  string  $name
         * @return mixed
         */
        public function __get($name)
        {
            return $this->items[$name];
        }

    }
