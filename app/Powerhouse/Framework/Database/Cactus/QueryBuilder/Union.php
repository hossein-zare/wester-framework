<?php

    namespace Cactus\QueryBuilder;

    use Exception;
    use Powerhouse\Interfaces\Database\QueryBuilder;
    use Powerhouse\Interfaces\Database\RawQuery;
    use Powerhouse\Interfaces\Database\FullRawQuery;

    abstract class Union extends Lock
    {

        /**
         * {@inheritdoc}
         */
        protected $unions = [];
        
        /**
         * {@inheritdoc}
         */
        private function queryBuilder($data, $type = null)
        {
            if ($data instanceof QueryBuilder) {
                $sql = $data->toSql(true);
            }
            elseif ($data instanceof RawQuery) {
                $sql = $data->statement(true);
            }
            elseif ($data instanceof FullRawQuery) {
                $sql = $data->statement(true);
            } else {
                throw new Exception("Please provide data!");
            }

            // Push the data to unions
            $statement = $sql['statement'];
            $parameters = $sql['parameters'];
            $this->unions[] = ['statement' => $statement, 'parameters' => $parameters, 'type' => $type];

            return $this;
        }

        /**
         * {@inheritdoc}
         */
        public function union($query)
        {
            return $this->queryBuilder($query);
        }

        /**
         * {@inheritdoc}
         */
        public function unionAll($query)
        {
            return $this->queryBuilder($query, 'ALL');
        }

    }
