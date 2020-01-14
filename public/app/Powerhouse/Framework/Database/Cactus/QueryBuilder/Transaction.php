<?php

    namespace Cactus\QueryBuilder;

    abstract class Transaction extends Operations
    {

        /**
         * {@inheritdoc}
         */
        public function transaction($closure)
        {
            try {
                $this->beginTransaction();
                $closure();
                $this->commit();
            } catch (\PDOException $e) {
                $this->rollBack();
            }
        }

        /**
         * {@inheritdoc}
         */
        public function beginTransaction()
        {
            $this->conn()->beginTransaction();
        }

        /**
         * {@inheritdoc}
         */
        public function rollBack()
        {
            $this->conn()->rollBack();
        }

        /**
         * {@inheritdoc}
         */
        public function commit()
        {
            $this->conn()->commit();
        }

    }
