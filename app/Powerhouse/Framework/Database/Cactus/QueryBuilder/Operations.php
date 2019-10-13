<?php

    namespace Cactus\QueryBuilder;

    use PDO;
    use PDOStatement;
    use Exception;
    use Powerhouse\Castles\DB;
    use Cactus\DataObject;
    use Powerhouse\Support\Collection;

    abstract class Operations extends Components\Pagination
    {

        /**
         * {@inheritdoc}
         */
        protected $first = false;

        /**
         * {@inheritdoc}
         */
        public function first()
        {
            $this->first = true;
            return $this;
        }

        /**
         * {@inheritdoc}
         */
        protected function fetchData(PDOStatement $stmt)
        {
            $asObject = false;

            if ($this->first) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            }

            return (new Collection($result));
        }

        /**
         * Create a new record.
         * 
         * @param  array  $fields
         * @return object
         */
        public function create(array $fields)
        {
            // Default attributes
            if (is_array($this->attributes))
                $fields = array_merge($fields, $this->attributes);

            // Only one of the fillable or guarded properties can be set in the model.
            if ($this->fillable !== null)
            {
                $fields = array_intersect_key($fields, array_flip($this->fillable));
            } elseif ($this->guarded !== null) {
                $fields = array_diff_key($fields, array_flip($this->guarded));
            }

            // Set array properties
            $this->setArrayProperties($fields);

            // Last inserted row id
            $id = $this->saveGetId();

            if ($id !== false) {
                // Reset the properties
                $this->setArrayProperties([]);

                // Find the record
                $this->find($id);

                return $this->first()->get();
            }

            return false;
        }

        /**
         * {@inheritdoc}
         */
        public function get()
        {
            // Prepare the statement
            $select = $this->translate()->prepareSelect();

            $statement = $select['statement'];
            $parameters = $select['parameters'];

            $stmt = $this->conn()->prepare($statement);
            $stmt->execute($parameters);

            return $this->fetchData($stmt);
        }

        /**
         * {@inheritdoc}
         */
        public function save()
        {
            $parameters = $this->valueProperties;

            // Prepare the statement
            $insert = $this->translate()->prepareInsert($parameters);

            $statement = $insert['statement'];
            $parameters = $insert['parameters'];

            $stmt = $this->conn()->prepare($statement);
            return $stmt->execute($parameters);
        }

        /**
         * {@inheritdoc}
         */
        public function saveGetId()
        {
            $result = $this->save();
            if ($result !== false)
                return $this->conn()->lastInsertId();
            return false;
        }

        /**
         * {@inheritdoc}
         */
        public function insert($parameters)
        {
            // Prepare the statement
            $insert = $this->translate()->prepareInsert($parameters);

            $statement = $insert['statement'];
            $parameters = $insert['parameters'];

            $stmt = $this->conn()->prepare($statement);
            return $stmt->execute($parameters);
        }

        /**
         * {@inheritdoc}
         */
        public function insertGetId($parameters)
        {
            $result = $this->insert($parameters);
            if ($result !== false)
                return $this->conn()->lastInsertId();
            return false;
        }

        /**
         * {@inheritdoc}
         */
        public function update($parameters = null)
        {
            $jsonUpdater = true;

            if ($parameters === null) {
                $parameters = $this->valueProperties;
                $jsonUpdater = false;
            }

            // Prepare the statement
            $update = $this->translate()->prepareUpdate($parameters, $jsonUpdater);

            $statement = $update['statement'];
            $parameters = $update['parameters'];

            $stmt = $this->conn()->prepare($statement);
            return $stmt->execute($parameters);
        }

        /**
         * {@inheritdoc}
         */
        public function increment($column, $i = 1, $parameters = [])
        {
            $parameters = array_merge([$column => DB::fullRaw("`{$column}` + {$i}")], $parameters);
            return $this->update($parameters);
        }

        /**
         * {@inheritdoc}
         */
        public function decrement($column, $i = 1, $parameters = [])
        {
            $parameters = array_merge([$column => DB::fullRaw("`{$column}` - {$i}")], $parameters);
            return $this->update($parameters);
        }

        /**
         * {@inheritdoc}
         */
        public function delete($rows = null)
        {
            // Prepare the statement
            $delete = $this->translate()->prepareDelete($rows);

            $statement = $delete['statement'];
            $parameters = $delete['parameters'];

            $stmt = $this->conn()->prepare($statement);
            return $stmt->execute($parameters);
        }

        /**
         * {@inheritdoc}
         */
        public function selectQuery($statement, $parameters = [])
        {
            $parameters = toArray($parameters);
            $stmt = $this->conn()->prepare($statement);
            $stmt->execute($parameters);

            if ($this->first) {
                return $stmt->fetch($this->getFetchMode());
            } else {
                return $stmt->fetchAll($this->getFetchMode());
            }
        }

        /**
         * {@inheritdoc}
         */
        public function insertQuery($statement, $parameters = [])
        {
            $parameters = toArray($parameters);

            if (stripos($statement, 'INSERT') !== 0)
                throw new Exception("Please enter a query that only inserts new rows!");

            $stmt = $this->conn()->prepare($statement);
            return $stmt->execute($parameters);
        }

        /**
         * {@inheritdoc}
         */
        public function updateQuery($statement, $parameters = [])
        {
            $parameters = toArray($parameters);

            if (stripos($statement, 'UPDATE') !== 0)
                throw new Exception("Please enter a query that only updates rows!");

            $stmt = $this->conn()->prepare($statement);
            return $stmt->execute($parameters);
        }

        /**
         * {@inheritdoc}
         */
        public function deleteQuery($statement, $parameters = [])
        {
            $parameters = toArray($parameters);

            if (stripos($statement, 'DELETE') !== 0)
                throw new Exception("Please enter a query that only deletes rows!");

            $stmt = $this->conn()->prepare($statement);
            return $stmt->execute($parameters);
        }

    }
