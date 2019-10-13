<?php

    namespace Cactus\QueryBuilder;

    abstract class Lock extends Termination
    {

        /**
         * {@inheritdoc}
         */
        protected $lock;

        /**
         * {@inheritdoc}
         */
        public function sharedLock()
        {
            $this->lock = "lock in share mode";
        }

        /**
         * {@inheritdoc}
         */
        public function lockForUpdate()
        {
            $this->lock = "for update";
        }

        /**
         * {@inheritdoc}
         */
        public function lockTable($table, $method, $closure)
        {
            $this->conn()->exec("LOCK TABLES {$table} {$method}");
            $closure();
            $this->conn()->exec("UNLOCK TABLES;");
        }

    }
