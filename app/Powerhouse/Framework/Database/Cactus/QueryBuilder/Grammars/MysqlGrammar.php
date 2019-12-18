<?php

    namespace Cactus\QueryBuilder\Grammars;

    use Powerhouse\Support\Str;
    use Powerhouse\Interfaces\Database\QueryBuilder;
    use Powerhouse\Interfaces\Database\RawQuery;
    use Powerhouse\Interfaces\Database\FullRawQuery;

    class MysqlGrammar
    {

        /**
         * The properties of the builder.
         * 
         * @var object
         */
        protected $properties = [];

        /**
         * Parameters.
         * 
         * @var array
         */
        public $parameters = [];

        /**
         * Available join prefixes.
         * 
         * @var array
         */
        protected $joinPrefixes = [
            'inner' => 'INNER JOIN',
            'left' => 'LEFT JOIN',
            'right' => 'RIGHT JOIN',
            'cross' => 'CROSS JOIN'
        ];

        /**
         * Set the properties of the builder.
         * 
         * @param  array  $properties
         * @return $this
         */
        public function setProperties(array $properties)
        {
            $this->properties = (object) $properties;
            return $this;
        }

        /**
         * Store parameters.
         * 
         * @param  array  $parameters
         * @return void
         */
        protected function storeParameters($parameters)
        {
            $this->parameters = array_merge($this->parameters, $parameters);
        }

        /**
         * Translate select expressions.
         * 
         * @return string
         */
        public function translateSelect()
        {
            // Parameters
            $this->storeParameters($this->properties->selectParameters);

            $select = $this->properties->select;
            if (count($select) > 0)
                $statement = implode(' , ', $select);
            else
                $statement = '*';

            return ($this->properties->distinct ? 'DISTINCT ' : ''). $statement;
        }

        /**
         * Translate where expressions.
         * 
         * @param  string  $expression
         * @param  array  $array
         * @return string
         */
        public function translateWheres($expression = null, $wheres = null, $nested = false)
        {
            if ($wheres === null) {
                // Parameters
                $this->storeParameters($this->properties->whereParameters);

                $wheres = $this->properties->wheres;
            }

            foreach ($wheres as $condition) {

                if (is_object($condition)) {
                    
                    /* Object */
                    $nest = $condition->wheres;

                    if ($expression !== null)
                        $expression.= ' '. $nest[0]['splitter'] .' ';

                    $expression.= '( '. $this->translateWheres(null, $nest, true) .' )';
                } else {
                    if ($expression !== null)
                        $expression.= ' '. $condition['splitter'] .' ';
                    
                    if (in_array($condition['flag'], ['json_length', 'json_contains']))
                        $expression.= $this->translateJsonCondition($condition);

                    elseif ($condition['flag'] !== 'raw') {
                        if (strpos($condition['column'], '->') !== false)
                            $expression.= $this->translateJsonCondition($condition, null);
                        else
                            $expression.= Str::wrap($condition['column']);

                        $expression.= ' '. $condition['operator'];
    
                        if ($condition['isColumn'] === true)
                            $expression.= ' '. Str::wrap($condition['value']);
                        elseif ($condition['flag'] !== 'null') {
                            if ($condition['flag'] === 'in_between') {
                                if (in_array($condition['operator'], ['IN', 'NOT IN'])) {
                                    $marks = array_fill(0, count($condition['value']), '?');
                                    $expression.= ' ('. implode(', ', $marks) .')';
                                } else {
                                    // BETWEEN, NOT BETWEEN
                                    $expression.= ' ? AND ?';
                                }
                            } else
                                $expression.= ' ?';
                        }
                    } else
                        $expression.= $condition['expression'];
                }

            }

            $prefix = ! $nested ? 'WHERE ' : '';
            return $expression !== null ? trim($prefix . $expression) : '';
        }

        /**
         * Get the where expressions with parameters.
         * 
         * @param  string  $expression
         * @param  array  $array
         * @return array
         */
        public function getWheres($expression = null, $wheres = null)
        {
            return [
                'statement' => $this->translateWheres($expression, $wheres),
                'parameters' => $this->parameters
            ];
        }

        /**
         * Translate the json condition of where expressions.
         * 
         * @param  array  $condition
         * @param  string  $flag
         * @return string
         */
        public function translateJsonCondition($condition, $flag = 'json')
        {
            $jsonParameters = $this->jsonParameters($condition);
            $column = $jsonParameters['column'];
            $identifier = $jsonParameters['identifier'];

            $expression = '';

            if ($flag === 'json') {
                if ($condition['flag'] === 'json_contains') {
                    if ($identifier !== null)
                        $expression.= "json_contains(". Str::wrap($column) .", ?, '". $identifier ."')";
                    else
                        $expression.= "json_contains(". Str::wrap($column) .", ?)";
                }
                elseif ($condition['flag'] === 'json_length') {
                    if ($identifier !== null)
                        $expression.= "json_length(". Str::wrap($column) .", '". $identifier ."')";
                    else
                        $expression.= "json_length(". Str::wrap($column) .")";

                    $expression.= ' '. $condition['operator'] . ' ?';
                }
            } else {
                if (preg_match('/(.*?)\((.*?)\)/', $condition['column']) === 0)
                    $expression.= "json_unquote(json_extract(". Str::wrap($column) .", '". $identifier ."'))";
                
                else
                    $expression.= preg_replace_callback('/(.*?)\((.*?)\)/', function ($matches) {
                        $jsonParameters = $this->jsonParameters($matches[2]);
                        $column = $jsonParameters['column'];
                        $identifier = $jsonParameters['identifier'];

                        return $matches[1] ."(json_unquote(json_extract(". Str::wrap($column) .", '". $identifier ."')))";
                    }, $condition['column']);
            }

            return trim($expression);
        }

        /**
         * Get json parameters.
         * 
         * @param  array  $condition
         * @return array
         */
        public function jsonParameters($condition)
        {
            // Let's see if the column name contains pointers
            if (strpos($condition['column'], '->') !== false) {
                $section = explode('->', $condition['column']);
                $column = $section[0];
                $identifier = '$';

                for ($i = 1; $i < count($section); $i++)
                    $identifier.= '."'. $section[$i] .'"';
            } else {
                $column = $condition['column'];
                $identifier = null;
            }

            return [
                'column' => $column,
                'identifier' => $identifier
            ];
        }

        /**
         * Tranlate join expressions.
         * 
         * @return string
         */
        public function translateJoins()
        {
            // Parameters
            $this->storeParameters($this->properties->joinParameters);

            $expression = '';

            $joins = $this->properties->joins;

            foreach ($joins as $join) {
                // Prefix
                $expression.= ' '. $this->joinPrefixes[$join['flag']];

                // Table
                if (! is_object($join['table_a']))
                    $expression.= ' '. Str::wrap($join['table_a']);
                else{
                    $expression.= ' '. $join['table_a']->statement;
                    //var_dump($join['table_a']->statement);
                }

                $expression.= ' ON ';
                $counter = 0;
                foreach ($join['conditions'] as $condition) {
                    // AND [OR] OR, First ANDs are avoided.
                    if ($counter > 0) {
                        $expression.= ' ' . $condition['type'] . ' ';
                    }

                    // On
                    $expression.= Str::wrap($condition['on']);

                    // Operator
                    $expression.= ' '. $condition['operator'];

                    // Table
                    if ($condition['table_b'] instanceof RawQuery) {
                        $statement = $condition['table_b']->statement(true);
                        $expression.= ' '. $statement['statement'];
                        
                        $this->storeParameters($statement['parameters']);
                    } elseif ($condition['table_b'] instanceof FullRawQuery) {
                        $statement = $condition['table_b']->statement(true);
                        $expression.= ' '. $statement['statement'];
                        
                        $this->storeParameters($statement['parameters']);
                    } else {
                        $expression.= ' '. Str::wrap($condition['table_b']);
                    }

                    // Increment
                    $counter++;
                }
            }

            return trim($expression);
        }

        /**
         * Translate order expressions.
         * 
         * @return string
         */
        public function translateOrders()
        {
            $temp = '';

            $orders = $this->properties->orders;

            $list = [];
            foreach ($orders as $order) {
                $temp = '';

                if ($order['flag'] === 'regular') {
                    $temp.= Str::wrap($order['column']);
                    $temp.= ' '. strtoupper($order['sorting']);
                }
                elseif ($order['flag'] === 'raw') {
                    $temp.= $order['expression'];
                }
                elseif ($order['flag'] === 'rand') {
                    $temp.= 'RAND()';
                }

                $list[] = $temp;
            }

            if (count($list) > 0)
                return 'ORDER BY '. implode(', ', $list);
            return '';
        }

        /**
         * Translate group expressions.
         * 
         * @return string
         */
        public function translateGroups()
        {
            $groups = $this->properties->groups;

            if (count($groups) > 0) {
                $groups = array_map(function ($group) {
                    return Str::wrap($group);
                }, $groups);

                return 'GROUP BY '. implode(', ', $groups);
            }

            return '';
        }

        /**
         * Translate having expressions.
         *
         * @return string
         */
        public function translateHavings()
        {
            // Parameters
            $this->storeParameters($this->properties->havingParameters);

            $expression = null;
            $havings = $this->properties->havings;

            foreach ($havings as $having) {

                if ($expression !== null) {
                    $expression.= ' '. $having['splitter'] .' ';
                }

                if ($having['flag'] === 'regular') {
                    $expression.= Str::wrap($having['column']);
                    $expression.= ' '. $having['operator'];
                    $expression.= ' ?';
                }
                elseif ($having['flag'] === 'raw') {
                    $expression.= $having['expression'];
                }
            }

            return $expression !== null ? trim('HAVING '. $expression) : '';
        }

        /**
         * Translate limit expressions.
         * 
         * @return string
         */
        public function translateLimit()
        {
            $expression = '';
            if ($this->properties->limit !== null) {
                $expression.= 'LIMIT '. $this->properties->limit;
            }

            if ($this->properties->offset !== null) {
                $expression.= ' OFFSET '. $this->properties->offset;
            }

            return trim($expression);
        }

        /**
         * Translate union expression.
         * 
         * @return string
         */
        public function translateUnions()
        {
            $expression = '';
            foreach ($this->properties->unions as $union) {
                $type = $union['type'] ? $union['type'] .' ' : '';
                $expression.= " UNION {$type}";

                $expression.= "( ". $union['statement'] ." )";
                $this->storeParameters($union['parameters']);
            }

            return empty($expression) ? null : preg_replace('!\s+!', ' ', $expression);
        }

        /**
         * Format a union statement.
         * 
         * @param  string  $statement
         * @param  string  $unions
         * @return string
         */
        protected function formatUnionStatement($statement, $unions)
        {
            if ($unions === null)
                return preg_replace('!\s+!', ' ', $statement);
            
            return preg_replace('!\s+!', ' ', "( {$statement} ){$unions}");
        }

        /**
         * Prepare a select statement.
         * 
         * @return array
         */
        public function prepareSelect()
        {
            $this->resetParameters();

            $select = $this->translateSelect();
            $table = $this->properties->table;
            $joins = $this->translateJoins();
            $wheres = $this->translateWheres();
            $groups = $this->translateGroups();
            $havings = $this->translateHavings();
            $orders = $this->translateOrders();
            $limit = $this->translateLimit();
            $unions = $this->translateUnions();

            $sentence = "SELECT {$select} FROM {$table} {$joins} {$wheres} {$groups} {$havings} {$orders} {$limit}";
            return [
                'statement' => $this->formatUnionStatement($sentence, $unions),
                'parameters' => $this->parameters
            ];
        }

        /**
         * Prepare an update statement.
         * 
         * @param  array  $set
         * @param  array  $jsonUpdater
         * @return array
         */
        public function prepareUpdate($set, $jsonUpdater = false)
        {
            $this->resetParameters();

            $list = [];
            foreach ($set as $key => $value) {
                if (strpos($key, '->') === false) {
                    $statement = Str::wrap($key) . ' = ';
                } else {
                    $section = explode('->', $key);
                    $column = Str::wrap($section[0]);
                    $identifier = '$';

                    for ($i = 1; $i < count($section); $i++)
                        $identifier.= '."'. $section[$i] .'"';

                    $statement =  "{$column} = JSON_SET({$column}, '{$identifier}', ?)";
                }

                if ($value instanceof RawQuery) {
                    $statement.= $value->statement();
                } elseif ($value instanceof FullRawQuery) {
                    $statement.= $value->statement();
                } else {
                    $statement.= '?';
                    $this->parameters[] = $value;
                }

                $list[] = $statement;
            }

            $table = $this->properties->table;
            $expression = implode(', ', $list);
            $wheres = $this->translateWheres();
            $orders = $this->translateOrders();
            $limit = $this->translateLimit();

            $sentence = "UPDATE {$table} SET {$expression} {$wheres} {$orders} {$limit}";
            return [
                'statement' => preg_replace('!\s+!', ' ', $sentence),
                'parameters' => $this->parameters
            ];
        }

        /**
         * Prepare an insert statement.
         * 
         * @param  array  $set
         * @return array
         */
        public function prepareInsert($set)
        {
            $this->resetParameters();

            $temp = [];
            $columns = [];
            $marks = [];

            if (! is_array(head($set))) {
                foreach ($set as $key => $value) {
                    $columns[] = Str::wrap($key);
                    $this->parameters[] = $value;
                    $marks[] = '?';
                }

                $values = '('. implode(', ', $marks) .')';
            } else {
                $columns = array_map(function ($item) {
                    return Str::wrap($item);
                }, array_keys($set[0]));

                foreach ($set as $item) {
                    foreach ($item as $key => $value) {
                        $this->parameters[] = $value;
                        $temp[] = '?';
                    }

                    $marks[] = '('. implode(', ', $temp) .')';
                    $temp = [];
                }

                $values = implode(', ', $marks);
            }

            $table = $this->properties->table;
            $columns = implode(', ', $columns);

            $sentence = "INSERT INTO {$table} ({$columns}) VALUES {$values}";
            return [
                'statement' => preg_replace('!\s+!', ' ', $sentence),
                'parameters' => $this->parameters
            ];
        }

        /**
         * Prepare a delete statement.
         * 
         * @return array
         */
        public function prepareDelete()
        {
            $this->resetParameters();

            $select = $this->translateSelect();
            $table = $this->properties->table;
            $joins = $this->translateJoins();
            $wheres = $this->translateWheres();
            $orders = $this->translateOrders();
            $limit = $this->translateLimit();

            $sentence = "DELETE FROM {$table} {$joins} {$wheres} {$orders} {$limit}";
            return [
                'statement' => preg_replace('!\s+!', ' ', $sentence),
                'parameters' => $this->parameters
            ];
        }

        /**
         * Reset parameters.
         * 
         * @return void
         */
        public function resetParameters()
        {
            $this->parameters = [];
        }

    }
