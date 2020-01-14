<?php

    namespace Cactus\QueryBuilder;

    use stdClass;
    use Powerhouse\Support\Str;
    use Powerhouse\Interfaces\Database\QueryBuilder;
    use Powerhouse\Interfaces\Database\RawQuery;
    use Powerhouse\Interfaces\Database\FullRawQuery;

    class Join extends Where
    {

        /**
         * {@inheritdoc}
         */
        protected $joins = [];

        /**
         * {@inheritdoc}
         */
        private $conditions = [];

        /**
         * {@inheritdoc}
         */
        protected $joinParameters = [];

        /**
         * {@inheritdoc}
         */
        private function queryBuilder($table_a, $on, $operator, $table_b, $flag)
        {
            if (is_callable($on)) {
                $join = new Join();
                call_user_func_array($on, [&$join]);

                $obj = $join->joinsObject($this->driver);
                $conditions = $obj->conditions;

                $this->joinParameters = array_merge($this->joinParameters, $obj->parameters);
            }
            elseif (is_callable($operator)) {
                $join = new Join();
                call_user_func_array($operator, [&$join]);

                // SubQuery
                if ($table_a instanceof QueryBuilder) {
                    $query = $table_a->toSql(true);
                } elseif ($table_a instanceof RawQuery) {
                    $query = $table_a->statement(true);
                } elseif ($table_a instanceof FullRawQuery) {
                    $query = $table_a->statement(true);
                }

                $statement = $query['statement'];
                $parameters = $query['parameters'];

                $obj = new stdClass();
                $obj->statement = "({$statement}) AS ". Str::wrap($on);
                $table_a = $obj;

                $obj = $join->joinsObject($this->driver);
                $conditions = $obj->conditions;

                $this->joinParameters = array_merge($this->joinParameters, array_merge($parameters, $obj->parameters));
            } else {
                $conditions = [
                    [
                        'on' => $on,
                        'operator' => $operator,
                        'table_b' => $table_b,
                        'type' => 'AND'
                    ]
                ];
            }

            $this->joins[] = [
                'table_a' => $table_a,
                'conditions' => $conditions,
                'flag' => $flag
            ];

            return $this;
        }

        /**
         * {@inheritdoc}
         */
        public function join($table_a, $on = null, $operator = null, $table_b = null)
        {
            return $this->queryBuilder($table_a, $on, $operator, $table_b, 'inner');
        }

        /**
         * {@inheritdoc}
         */
        public function on($on, $operator, $table_b)
        {
            $this->conditions[] = [
                'on' => $on,
                'operator' => $operator,
                'table_b' => $table_b,
                'type' => 'AND'
            ];

            return $this;
        }

        /**
         * {@inheritdoc}
         */
        public function orOn($on, $operator, $table_b)
        {
            $this->conditions[] = [
                'on' => $on,
                'operator' => $operator,
                'table_b' => $table_b,
                'type' => 'OR'
            ];

            return $this;
        }

        /**
         * {@inheritdoc}
         */
        public function leftJoin($table_a, $on = null, $operator = null, $table_b = null)
        {
            return $this->queryBuilder($table_a, $on, $operator, $table_b, 'left');
        }

        /**
         * {@inheritdoc}
         */
        public function rightJoin($table_a, $on = null, $operator = null, $table_b = null)
        {
            return $this->queryBuilder($table_a, $on, $operator, $table_b, 'right');
        }

        /**
         * {@inheritdoc}
         */
        public function crossJoin($table_a, $on = null, $operator = null, $table_b = null)
        {
            return $this->queryBuilder($table_a, $on, $operator, $table_b, 'cross');
        }

        /**
         * {@inheritdoc}
         */
        public function joinSub($table_a, $on, $operator = null, $table_b = null)
        {
            return $this->join($table_a, $on, $operator, $table_b);
        }

        /**
         * {@inheritdoc}
         */
        public function leftJoinSub($table_a, $on, $operator = null, $table_b = null)
        {
            return $this->leftJoin($table_a, $on, $operator, $table_b);
        }

        /**
         * {@inheritdoc}
         */
        public function rightJoinSub($table_a, $on, $operator = null, $table_b = null)
        {
            return $this->rightJoin($table_a, $on, $operator, $table_b);
        }

        /**
         * Get an object of joins
         * 
         * @return   object
         */
        public function joinsObject($driver = null)
        {
            $obj = new StdClass();
            $obj->conditions = $this->conditions;
            $obj->parameters = $this->joinParameters;
            
            return $obj;
        }

    }
