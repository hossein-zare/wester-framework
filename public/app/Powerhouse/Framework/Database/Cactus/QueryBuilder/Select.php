<?php

    namespace Cactus\QueryBuilder;

    use Powerhouse\Support\Str;
    use Powerhouse\Interfaces\Database\RawQuery;
    use Powerhouse\Interfaces\Database\FullRawQuery;

    abstract class Select extends Aggregate
    {

        /**
         * {@inheritdoc}
         */
        protected $select = [];

        /**
         * {@inheritdoc}
         */
        protected $selectParameters = [];

        /**
         * {@inheritdoc}
         */
        public function select(...$columns)
        {
            $this->select = [];
            foreach ($columns as $column) {
                if ($column instanceof RawQuery) {
                    $this->select[] = Str::wrap($column->statement());
                } elseif ($column instanceof FullRawQuery) {
                    $this->select[] = $column->statement();
                } else {
                    $this->select = array_merge($this->select, Str::wrap(toArray($column)));
                }
            }

            return $this;
        }

        /**
         * {@inheritdoc}
         */
        public function addSelect(...$columns)
        {
            foreach ($columns as $column) {
                if ($column instanceof RawQuery) {
                    $this->select[] = $column->statement();
                } else {
                    $this->select = array_merge($this->select, Str::wrap(toArray($column)));
                }
            }
        }

        /**
         * {@inheritdoc}
         */
        public function selectRaw($statement, $parameters = [])
        {
            $parameters = toArray($parameters);
            $this->selectParameters = array_merge($this->selectParameters, $parameters);
            $this->select[] = $statement;

            return $this;
        }

    }
