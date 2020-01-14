<?php

    namespace Cactus\QueryBuilder;

    abstract class Having extends Limit
    {

        /**
         * Holds conditions.
         * 
         * @var array
         */
        protected $havings = [];

        /**
         * The parameters of having statements.
         * 
         * @var array
         */
        protected $havingParameters = [];

        /**
         * {@inheritdoc}
         */
        private function queryBuilder($column, $operator = '=', $value = null, $type = 'AND', $argsNum = null, $itemsNum = 0)
        {
            if (is_string($column)) {
                if (is_numeric($argsNum) && $argsNum <= 2 && $itemsNum < 3) {
                    [$value, $operator] = [$operator, '='];
                }

                if ($value !== null) {
                    $this->havingParameters[] = $value;
                }

                $this->havings[] = [
                    'splitter' => $type,
                    'column' => $column,
                    'operator' => $operator,
                    'flag' => 'regular'
                ];
            }
            elseif (is_array($column)) {
                foreach ($column as $item) {
                    $value = $item[2] ?? null;
                    $type = $argsNum === 2 && count($item) < 4 ? $operator : $type;

                    $this->queryBuilder($item[0], $item[1] ?? null, $value, $item[3] ?? $type, $argsNum, count($item));
                }
            }

            return $this;
        }

        /**
         * {@inheritdoc}
         */
        public function having($column, $operator = '=', $value = null, $type = 'AND')
        {
            return $this->queryBuilder($column, $operator, $value, $type, func_num_args());
        }

        /**
         * {@inheritdoc}
         */
        public function orHaving($column, $operator = '=', $value = null)
        {
            return $this->queryBuilder($column, $operator, $value, 'OR', func_num_args());
        }

        /**
         * {@inheritdoc}
         */
        public function havingRaw($statement, $parameters = [], $type = 'AND')
        {
            $parameters = toArray($parameters);
            if (count($parameters) > 0)
                $this->havingParameters = array_merge($this->havingParameters, $parameters);

            $this->havings[] = [
                'splitter' => $type,
                'expression' => $statement,
                'flag' => 'raw'
            ];

            return $this;
        }

        /**
         * {@inheritdoc}
         */
        public function orHavingRaw($statement, $parameters = [])
        {
            return $this->havingRaw($statement, $parameters, 'OR');
        }

    }
