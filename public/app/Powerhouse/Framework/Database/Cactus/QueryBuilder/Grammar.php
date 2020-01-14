<?php

    namespace Cactus\QueryBuilder;

    abstract class Grammar
    {

        /**
         * The grammar instance.
         * 
         * @var object
         */
        protected $grammar;

        /**
         * Available grammars.
         * 
         * @var array
         */
        protected $grammars = [
            'mysql' => \Cactus\QueryBuilder\Grammars\MysqlGrammar::class
        ];

        /**
         * Get the grammar instance.
         * 
         * @return \Cactus\QueryBuilder\Grammars\MysqlGrammar
         */
        protected function grammar($driver)
        {
            if ($this->grammar === null) {
                $driver = $driver ?? $this->driver;
                return $this->grammar = new $this->grammars[$driver]();
            }
            
            return $this->grammar;
        }

        /**
         * Translate expressions.
         * 
         * @param  string|null $driver
         * @return \Cactus\QueryBuilder\Grammars\MysqlGrammar
         */
        public function translate($driver = null)
        {
            $properties = get_object_vars($this);
            return $this->grammar($driver)->setProperties($properties);
        }

        /**
         * {@inheritdoc}
         */
        public function toSql($full = false)
        {
            // Prepare the statement
            $select = $this->translate()->prepareSelect();

            $statement = $select['statement'];
            $parameters = $select['parameters'];

            if ($full === true)
                return [
                    'statement' => $statement,
                    'parameters' => $parameters
                ];

            return $statement;
        }

    }
