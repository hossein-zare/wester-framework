<?php

    namespace Cactus\QueryBuilder;

    abstract class GroupBy extends Having
    {

        /**
         * The query groups.
         * 
         * @var array
         */
        protected $groups = [];

        /**
         * Group the results by.
         * 
         * @param  mixed  $column
         * @return $this
         */
        public function groupBy($column)
        {
            if (is_array($column) === true) {
                $this->groups = array_merge($this->groups, $column);
            } else {
                $this->groups[] = $column;
            }

            return $this;
        }

    }
