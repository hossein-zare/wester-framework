<?php

    namespace Cactus\QueryBuilder;

    abstract class Termination extends Transaction
    {

        /**
         * Truncate a table.
         * 
         * @param  string  $mode
         * @return bool
         */
        public function truncate($mode = null)
        {
            return $this->exec("TRUNCATE TABLE {$this->table} {$mode};");
        }

    }
