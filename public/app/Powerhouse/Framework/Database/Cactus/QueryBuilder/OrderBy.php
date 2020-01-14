<?php

    namespace Cactus\QueryBuilder;

    abstract class OrderBy extends Union
    {

        /**
         * {@inheritdoc}
         */
        protected $orders = [];

        /**
         * {@inheritdoc}
         */
        public function orderBy($column, $sorting = 'desc')
        {
            if (is_array($column)) {
                foreach ($column as $item) {
                    $this->orders[] = [
                        'column' => $item[0],
                        'sorting' => $item[1] ?? $sorting,
                        'flag' => 'regular'
                    ];
                }
            } else {
                $this->orders[] = ['column' => $column, 'sorting' => $sorting, 'flag' => 'regular'];
            }

            return $this;
        }

        /**
         * {@inheritdoc}
         */
        public function orderByRaw($statement)
        {
            $this->orders[] = [
                'expression' => $statement,
                'flag' => 'raw'
            ];
            
            return $this;
        }

        /**
         * {@inheritdoc}
         */
        public function inRandomOrder()
        {
            $this->orders[] = [
                'flag' => 'rand'
            ];

            return $this;
        }

    }
