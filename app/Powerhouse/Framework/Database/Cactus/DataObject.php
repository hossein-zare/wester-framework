<?php

    namespace Cactus;

    use Countable;

    class DataObject implements Countable
    {

        /**
         * The number of rows.
         * 
         * @var int
         */
        protected $__rowCount = 0;

        /**
         * {@inheritdoc}
         */
        public function __construct($num)
        {
            $this->set($num);
        }

        /**
         * Returns the number of rows.
         * 
         * @return int
         */
        public function count()
        {
            return $this->__rowCount;
        }

        /**
         * Set the number of rows.
         * 
         * @param  int  $num
         */
        protected function set($num)
        {
            $this->__rowCount = $num;
        }

    }