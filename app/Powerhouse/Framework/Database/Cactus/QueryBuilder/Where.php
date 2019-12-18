<?php

    namespace Cactus\QueryBuilder;

    use stdClass;
    use Exception;
    use Powerhouse\Interfaces\Database\QueryBuilder;
    use Powerhouse\Interfaces\Database\RawQuery;
    use Powerhouse\Interfaces\Database\FullRawQuery;
    
    class Where extends Grammar
    {

        /**
         * {@inheritdoc}
         */
        protected $wheres = [];

        /**
         * {@inheritdoc}
         */
        protected $whereParameters = [];

        /**
         * {@inheritdoc}
         */
        private function queryBuilder($column, $operator = null, $value = null, $type = 'AND', $argsNum = null, $itemsNum = 0, $columnValue = false, $dateCondition = false, $flag = null)
        {
            if (is_string($column)) {
                $this->storeExpression($column, $operator, $value, $type, $argsNum, $itemsNum, $columnValue, $dateCondition, $flag);
            }
            elseif (is_array($column)) {
                if (in_array($operator, ['IS NULL', 'IS NOT NULL']) === false) {
                    if (in_array($operator, ['IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN']) === true) {
                        foreach ($column as $item) {
                            $value = $item[1];
                            $this->queryBuilder($item[0], $operator, $value, $type, $argsNum, count($item), $columnValue, $dateCondition, $flag);
                        }
                    } else {
                        foreach ($column as $item) {
                            $value = $item[2] ?? null;
                            $this->queryBuilder($item[0], $item[1] ?? null, $value, $type, $argsNum, count($item), $columnValue, $dateCondition, $flag);
                        }
                    }
                } else {
                    foreach ($column as $item) {
                        $this->queryBuilder($item, $operator, $value, $type, $argsNum, count($column), $columnValue, $dateCondition, $flag);
                    }
                }
            }
            elseif (is_callable($column)) {
                $query = new Where();
                call_user_func_array($column, [&$query]);

                $this->wheres[] = $query->wheresObject();
                $this->storeParameters($query->whereParameters(), null);
            }

            return $this;
        }

        /**
         * {@inheritdoc}
         */
        private function storeExpression($column, $operator, $value, $type, $argsNum, $itemsNum, $columnValue, $dateCondition, $flag)
        {
            if (is_numeric($argsNum) && $argsNum <= 2 && $itemsNum < 3 && $flag !== 'in_between') {
                [$value, $operator] = [$operator, '='];
            }

            $this->storeParameters($value, $flag);

            $array = [
                'splitter' => $type,
                'column' => $column,
                'operator' => $operator,
                'isColumn' => $columnValue,
                'isDate' => $dateCondition,
                'flag' => $flag
            ];

            if ($columnValue === true OR $flag === 'in_between') {
                $array = array_merge($array, ['value' => $value]);
            }

            $this->wheres[] = $array;
        }

        /**
         * {@inheritdoc}
         */
        private function storeParameters($value, $flag)
        {
            // Get the parameters
            if (in_array($flag, ['null', 'column']) === false) {
                if (is_array($value)) {
                    $this->whereParameters = array_merge($this->whereParameters, $value);
                } else {
                    $this->whereParameters[] = $value;
                }
            }
        }

        /**
         * {@inheritdoc}
         */
        public function where($column, $operator = null, $value = null, $type = 'AND')
        {
            return $this->queryBuilder($column, $operator, $value, $type, func_num_args());
        }

        /**
         * {@inheritdoc}
         */
        public function orWhere($column, $operator = null, $value = null)
        {
            return $this->queryBuilder($column, $operator, $value, 'OR', func_num_args());
        }

        /**
         * {@inheritdoc}
         */
        public function andWhere($column, $operator = null, $value = null)
        {
            return $this->queryBuilder($column, $operator, $value, 'AND', func_num_args());
        }

        /**
         * {@inheritdoc}
         */
        public function whereIn($column, $value = null)
        {
            return $this->queryBuilder($column, 'IN', $value, 'AND', func_num_args(), 0, false, false, 'in_between');
        }

        /**
         * {@inheritdoc}
         */
        public function andWhereIn($column, $value = null)
        {
            return $this->queryBuilder($column, 'IN', $value, 'AND', func_num_args(), 0, false, false, 'in_between');
        }

        /**
         * {@inheritdoc}
         */
        public function whereNotIn($column, $value = null)
        {
            return $this->queryBuilder($column, 'NOT IN', $value, 'AND', func_num_args(), 0, false, false, 'in_between');
        }

        /**
         * {@inheritdoc}
         */
        public function andWhereNotIn($column, $value = null)
        {
            return $this->queryBuilder($column, 'NOT IN', $value, 'AND', func_num_args(), 0, false, false, 'in_between');
        }

        /**
         * {@inheritdoc}
         */
        public function orWhereIn($column, $value = null)
        {
            return $this->queryBuilder($column, 'IN', $value, 'OR', func_num_args(), 0, false, false, 'in_between');
        }

        /**
         * {@inheritdoc}
         */
        public function orWhereNotIn($column, $value = null)
        {
            return $this->queryBuilder($column, 'NOT IN', $value, 'OR', func_num_args(), 0, false, false, 'in_between');
        }

        /**
         * {@inheritdoc}
         */
        public function whereBetween($column, $value = null)
        {
            return $this->queryBuilder($column, 'BETWEEN', $value, 'AND', func_num_args(), 0, false, false, 'in_between');
        }

        /**
         * {@inheritdoc}
         */
        public function andWhereBetween($column, $value = null)
        {
            return $this->queryBuilder($column, 'BETWEEN', $value, 'AND', func_num_args(), 0, false, false, 'in_between');
        }

        /**
         * {@inheritdoc}
         */
        public function whereNotBetween($column, $value = null)
        {
            return $this->queryBuilder($column, 'NOT BETWEEN', $value, 'AND', func_num_args(), 0, false, false, 'in_between');
        }

        /**
         * {@inheritdoc}
         */
        public function andWhereNotBetween($column, $value = null)
        {
            return $this->queryBuilder($column, 'NOT BETWEEN', $value, 'AND', func_num_args(), 0, false, false, 'in_between');
        }

        /**
         * {@inheritdoc}
         */
        public function orWhereBetween($column, $value = null)
        {
            return $this->queryBuilder($column, 'BETWEEN', $value, 'OR', func_num_args(), 0, false, false, 'in_between');
        }

        /**
         * {@inheritdoc}
         */
        public function orWhereNotBetween($column, $value = null)
        {
            return $this->queryBuilder($column, 'NOT BETWEEN', $value, 'OR', func_num_args(), 0, false, false, 'in_between');
        }

        /**
         * {@inheritdoc}
         */
        public function whereNull($column)
        {
            return $this->queryBuilder($column, 'IS NULL', null, 'AND', null, 0, false, false, 'null');
        }

        /**
         * {@inheritdoc}
         */
        public function andWhereNull($column)
        {
            return $this->queryBuilder($column, 'IS NULL', null, 'AND', null, 0, false, false, 'null');
        }

        /**
         * {@inheritdoc}
         */
        public function whereNotNull($column)
        {
            return $this->queryBuilder($column, 'IS NOT NULL', null, 'AND', null, 0, false, false, 'null');
        }

        /**
         * {@inheritdoc}
         */
        public function andWhereNotNull($column)
        {
            return $this->queryBuilder($column, 'IS NOT NULL', null, 'AND', null, 0, false, false, 'null');
        }

        /**
         * {@inheritdoc}
         */
        public function orWhereNull($column)
        {
            return $this->queryBuilder($column, 'IS NULL', null, 'OR', null, 0, false, false, 'null');
        }

        /**
         * {@inheritdoc}
         */
        public function orWhereNotNull($column)
        {
            return $this->queryBuilder($column, 'IS NOT NULL', null, 'OR', null, 0, false, false, 'null');
        }

        /**
         * {@inheritdoc}
         */
        public function whereColumn($column, $operator = null, $value = null)
        {
            return $this->queryBuilder($column, $operator, $value, 'AND', func_num_args(), 0, true, false, 'column');
        }

        /**
         * {@inheritdoc}
         */
        public function orWhereColumn($column, $operator = null, $value = null)
        {
            return $this->queryBuilder($column, $operator, $value, 'OR', func_num_args(), 0, true, false, 'column');
        }

        /**
         * {@inheritdoc}
         */
        public function whereYear($column, $operator = '=', $value = null, $type = 'AND')
        {
            $this->classifyVariables(func_num_args(), $column, $operator, $value, $type);
            return $this->dateAssigner('YEAR', $column, $operator, $value, $type);
        }

        /**
         * {@inheritdoc}
         */
        public function whereMonth($column, $operator = '=', $value = null, $type = 'AND')
        {
            $this->classifyVariables(func_num_args(), $column, $operator, $value, $type);
            return $this->dateAssigner('MONTH', $column, $operator, $value, $type);
        }

        /**
         * {@inheritdoc}
         */
        public function whereDay($column, $operator = '=', $value = null, $type = 'AND')
        {
            $this->classifyVariables(func_num_args(), $column, $operator, $value, $type);
            return $this->dateAssigner('DAY', $column, $operator, $value, $type);
        }

        /**
         * {@inheritdoc}
         */
        public function whereDate($column, $operator = '=', $value = null, $type = 'AND')
        {
            $this->classifyVariables(func_num_args(), $column, $operator, $value, $type);
            return $this->dateAssigner('DATE', $column, $operator, $value, $type);
        }

        /**
         * {@inheritdoc}
         */
        public function whereTime($column, $operator = '=', $value = null, $type = 'AND')
        {
            $this->classifyVariables(func_num_args(), $column, $operator, $value, $type);
            return $this->dateAssigner('TIME', $column, $operator, $value, $type);
        }

        /**
         * {@inheritdoc}
         */
        private function dateAssigner($function, $column, $operator = '=', $value = null, $type = null)
        {
            if (is_array($column)) {
                foreach ($column as $item) {
                    if (count($item) === 2) {
                        [$value, $operator] = [$item[1], '='];
                    } else {
                        [$value, $operator] = [$item[2], $item[1]];
                    }

                    $this->dateAssigner($function, $item[0], $operator, $value, $item[3] ?? $type);
                }
            } else {
                $column = sprintf('%s(%s)', $function, $column);
                return $this->queryBuilder($column, $operator, $value, $type, null, 0, false, true);
            }

            return $this;
        }

        /**
         * {@inheritdoc}
         */
        public function whereJsonContains($column, $value = null, $type = 'AND')
        {
            $argsNum = func_num_args();

            if (is_array($column)) {
                if ($value !== null) {
                    $type = $value;
                }

                foreach ($column as $item) {
                    $this->queryBuilder($item[0], '=', $item[1], $item[2] ?? $type, func_num_args() + 1, 0, false, false, 'json_contains');
                }

                return $this;
            } else {
                return $this->queryBuilder($column, '=', $value, $type, func_num_args() + 1, 0, false, false, 'json_contains');
            }
        }

        /**
         * {@inheritdoc}
         */
        public function whereJsonLength($column, $operator = '=', $value = null, $type = 'AND')
        {
            return $this->queryBuilder($column, $operator, $value, $type, func_num_args(), 0, false, false, 'json_length');
        }

        /**
         * {@inheritdoc}
         */
        public function whereRaw($statement, $parameters = [], $type = 'AND')
        {
            $parameters = toArray($parameters);
            if (count($parameters) > 0)
                $this->whereParameters = array_merge($this->whereParameters, $parameters);

            $this->wheres[] = [
                'splitter' => $type,
                'expression' => $statement,
                'flag' => 'raw'
            ];

            return $this;
        }

        /**
         * {@inheritdoc}
         */
        public function orWhereRaw($statement, $parameters = [])
        {
            return $this->whereRaw($statement, $parameters, 'OR');
        }

        /**
         * {@inheritdoc}
         */
        public function existenceQueryBuilder($object, $parent, $type)
        {
            if ($object instanceof RawQuery) {
                $sql = $object->statement(true);
            } elseif ($object instanceof FullRawQuery) {
                $sql = $object->statement(true);
            } elseif ($object instanceof QueryBuilder) {
                $sql = $object->toSql(true);
            } elseif (is_callable($object)) {
                $query = new Builder();
                call_user_func_array($object, [&$query]);

                $sql = $query->toSql(true);
            } else {
                throw new Exception('The statement does\'nt meet the correct interface.');
            }

            $statement = $sql['statement'];
            $parameters = $sql['parameters'];

            return $this->whereRaw("{$parent} ({$statement})", $parameters, $type);
        }

        /**
         * {@inheritdoc}
         */
         public function whereExists($object)
         {
            return $this->existenceQueryBuilder($object, 'EXISTS', 'AND');
         }

        /**
         * {@inheritdoc}
         */
         public function orWhereExists($object)
         {
            return $this->existenceQueryBuilder($object, 'EXISTS', 'OR');
         }

        /**
         * {@inheritdoc}
         */
         public function whereNotExists($object)
         {
            return $this->existenceQueryBuilder($object, 'NOT EXISTS', 'AND');
         }

        /**
         * {@inheritdoc}
         */
         public function orWhereNotExists($object)
         {
            return $this->existenceQueryBuilder($object, 'NOT EXISTS', 'OR');
         }

        /**
         * {@inheritdoc}
         */
        private function classifyVariables($num, $column, &$operator, &$value, &$type)
        {
            if ($num === 2 && is_array($column)) {
                [$type, $operator] = [$operator, '='];
            } elseif ($num === 2) {
                [$value, $operator] = [$operator, '='];
            }
        }

        /**
         * Get an object of wheres.
         * 
         * @return object
         */
        public function wheresObject()
        {
            $obj = new stdClass();
            $obj->wheres = $this->wheres;

            return $obj;
        }

        /**
         * Get the parameters.
         * 
         * @return object
         */
        public function whereParameters()
        {
            return $this->whereParameters;
        }

    }
