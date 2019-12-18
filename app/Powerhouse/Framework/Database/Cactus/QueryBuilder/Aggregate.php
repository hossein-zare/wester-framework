<?php

    namespace Cactus\QueryBuilder;

    use Powerhouse\Support\Str;

    abstract class Aggregate extends GroupBy
    {

        /**
         * {@inheritdoc}
         */
        private function queryBuilder($function, $row, $name = 'aggregate')
        {
            $row = Str::wrap($row);
            $name = $name;

            $this->select = [sprintf("%s(%s) as %s", $function, $row, $name)];
            return $this;
        }

        /**
         * {@inheritdoc}
         */
        public function min($row)
        {
            return $this->queryBuilder('min', $row);
            return (float) $this->first()->get()->aggregate;
        }

        /**
         * {@inheritdoc}
         */
        public function max($row)
        {
            return $this->queryBuilder('max', $row);
            return (float) $this->first()->get()->aggregate;
        }

        /**
         * {@inheritdoc}
         */
        public function sum($row)
        {
            return $this->queryBuilder('sum', $row);
            return (float) $this->first()->get()->aggregate;
        }

        /**
         * {@inheritdoc}
         */
        public function avg($row)
        {
            $this->queryBuilder('avg', $row);
            return (float) $this->first()->get()->aggregate;
        }

        /**
         * {@inheritdoc}
         */
        public function count($row = '*')
        {
            $this->queryBuilder('count', $row);
            return (int) $this->first()->get()->aggregate;
        }
    }
