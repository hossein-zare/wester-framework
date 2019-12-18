<?php

    namespace Cactus\QueryBuilder;

    abstract class Limit extends OrderBy
    {

        /**
         * {@inheritdoc}
         */
        protected $limit;

        /**
         * {@inheritdoc}
         */
        protected $offset;

        /**
         * {@inheritdoc}
         */
        public function limit($limit, $offset = null)
        {
            $this->limit = $limit;
            if ($offset !== null)
                $this->offset($offset);
            return $this;
        }

        /**
         * @inheritdoc
         */
        public function offset($offset) {
            $this->offset = $offset;

            return $this;
        }

    }
