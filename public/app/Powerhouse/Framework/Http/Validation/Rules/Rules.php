<?php

    namespace Powerhouse\Http\Validation\Rules;

    use DateTime;
    use Powerhouse\Castles\DB;

    abstract class Rules
    {

        /**
         * The minimum segment.
         * 
         * @param  string  $field
         * @param  string  $definedType
         * @param  string|array  $limit
         * @return void
         */
        protected function minimumSegment($field, $definedType, $limit)
        {
            $value = $this->getFieldValue($field, $definedType);

            // Numbers
            if (in_array($definedType, $this->typeList['number']) === true) {
                if (! ($value >= $limit) ) {
                    $this->pushError('min.numeric', [
                        'attribute' => $this->onlyCustomName($field, true),
                        'min' => $limit
                    ], $field);
                }
            }

            // Strings
            if (in_array($definedType, $this->typeList['string']) === true) {
                if (! (mb_strlen($value) >= $limit) ) {
                    $this->pushError('min.string', [
                        'attribute' => $this->onlyCustomName($field, true),
                        'min' => $limit
                    ], $field);
                }
            }

            // Arrays
            if (in_array($definedType, $this->typeList['array']) === true) {
                if (! (count($value) >= $limit) ) {
                    $this->pushError('min.array', [
                        'attribute' => $this->onlyCustomName($field, true),
                        'min' => $limit
                    ], $field);
                }
            }

            // File
            if (in_array($definedType, $this->typeList['file']) === true) {

                // Get the size (Convert it to an array of sizes)
                if ($this->isParent($field)) {
                    $value = $this->valueToArray($value['size']);
                } else {
                    $value = $value['size'];
                }

                foreach ($value as $size) {
                    $sizeKB = bytes_to_kilobytes($size);
                    if (! ($sizeKB >= $limit)) {
                        $this->pushError('min.file', [
                            'attribute' => $this->onlyCustomName($field, true),
                            'min' => $limit
                        ], $field);

                        break;
                    }
                }
            }
        }

        /**
         * The maximum segment.
         * 
         * @param  string  $field
         * @param  string  $definedType
         * @param  string|array  $limit
         * @return void
         */
        protected function maximumSegment($field, $definedType, $limit)
        {
            $value = $this->getFieldValue($field, $definedType);

            // Numbers
            if (in_array($definedType, $this->typeList['number']) === true) {
                if (! ($value <= $limit) ) {
                    $this->pushError('max.numeric', [
                        'attribute' => $this->onlyCustomName($field, true),
                        'max' => $limit
                    ], $field);
                }
            }

            // Strings
            if (in_array($definedType, $this->typeList['string']) === true) {
                if (! (mb_strlen($value) <= $limit) ) {
                    $this->pushError('max.string', [
                        'attribute' => $this->onlyCustomName($field, true),
                        'max' => $limit
                    ], $field);
                }
            }

            // Arrays
            if (in_array($definedType, $this->typeList['array']) === true) {
                if (! (count($value) <= $limit) ) {
                    $this->pushError('max.array', [
                        'attribute' => $this->onlyCustomName($field, true),
                        'max' => $limit
                    ], $field);
                }
            }

            // File
            if (in_array($definedType, $this->typeList['file']) === true) {

                // Get the size (Convert it to an array of sizes)
                if ($this->isParent($field)) {
                    $value = $this->valueToArray($value['size']);
                } else {
                    $value = $value['size'];
                }

                foreach ($value as $size) {
                    $sizeKB = bytes_to_kilobytes($size);
                    if (! ($sizeKB <= $limit)) {
                        $this->pushError('max.file', [
                            'attribute' => $this->onlyCustomName($field, true),
                            'max' => $limit
                        ], $field);

                        break;
                    }
                }
            }
        }

        /**
         * Dimentions for images.
         * 
         * @param  string  $field
         * @param  string  $definedType
         * @param  string|array  $limit
         * @return void
         */
        protected function dimensionsSegment($field, $definedType, $value)
        {
            $attributes = explode(',', $value);

            // Get image size
            $file = request()->file($this->onlyName($field))->get();
            list($width, $height) = getimagesize($file);

            $error = 0;
            foreach ($attributes as $attr) {
                list($name, $value) = explode('=', $attr);

                if ($name === 'min_width') {
                    if ($width < $value) {
                        $error = 1;
                        break;
                    }
                } elseif ($name === 'max_width') {
                    if ($width > $value) {
                        $error = 1;
                        break;
                    }
                } elseif ($name === 'min_height') {
                    if ($height < $value) {
                        $error = 1;
                        break;
                    }
                } elseif ($name === 'max_height') {
                    if ($height > $value) {
                        $error = 1;
                        break;
                    }
                } elseif ($name === 'width') {
                    if ($height != $value) {
                        $error = 1;
                        break;
                    }
                } elseif ($name === 'height') {
                    if ($height != $value) {
                        $error = 1;
                        break;
                    }
                }  elseif ($name === 'ratio') {
                    $image_ratio = round($width / $height, 1);

                    if (strpos($value, '/') !== false) {
                        list($num_1, $num_2) = explode('/', $value);
                        $ratio = round($num_1 / $num_2, 1);
                    } else {
                        $ratio = $value;
                    }

                    if ($ratio !== $image_ratio) {
                        $error = 1;
                        break;
                    }
                }
            }

            if ($error === 1) {
                $this->pushError('dimensions', [
                    'attribute' => $this->onlyCustomName($field, true)
                ], $field);
            }
        }

        /**
         * The between segment.
         * 
         * @param  string  $field
         * @param  string  $definedType
         * @param  string|array  $limit
         * @return void
         */
        protected function betweenSegment($field, $definedType, $limit)
        {
            $value = $this->getFieldValue($field, $definedType);
            list($limitMin, $limitMax) = explode(',', $limit);

            // Numbers
            if (in_array($definedType, $this->typeList['number']) === true) {
                if (! ($value >= $limitMin) || ! ($value <= $limitMax)) {
                    $this->pushError('between.numeric', [
                        'attribute' => $this->onlyCustomName($field, true),
                        'min' => $limitMin,
                        'max' => $limitMax
                    ], $field);
                }
            }

            // Strings
            if (in_array($definedType, $this->typeList['string']) === true) {
                if (! (mb_strlen($value) >= $limitMin) || ! (mb_strlen($value) <= $limitMax) ) {
                    $this->pushError('max.string', [
                        'attribute' => $this->onlyCustomName($field, true),
                        'max' => $limit
                    ], $field);
                }
            }

            // Arrays
            if (in_array($definedType, $this->typeList['array']) === true) {
                if (! (count($value) >= $limitMin) || ! (count($value) <= $limitMax) ) {
                    $this->pushError('max.array', [
                        'attribute' => $this->onlyCustomName($field, true),
                        'max' => $limit
                    ], $field);
                }
            }

            // File
            if (in_array($definedType, $this->typeList['file']) === true) {

                // Get the size (Convert it to an array of sizes)
                if ($this->isParent($field)) {
                    $value = $this->valueToArray($value['size']);
                } else {
                    $value = $value['size'];
                }

                foreach ($value as $size) {
                    $sizeKB = bytes_to_kilobytes($size);
                    if (! ($sizeKB >= $limitMin) || ! ($sizeKB <= $limitMax)) {
                        $this->pushError('max.file', [
                            'attribute' => $this->onlyCustomName($field, true),
                            'max' => $limit
                        ], $field);

                        break;
                    }
                }
            }
        }

        /**
         * Accepted.
         * 
         * @param  string  $field
         * @param  string|null  $definedType
         * @return void
         */
        protected function acceptedSegment($field, $definedType)
        {
            $validValues = ['yes', 'on', 'true', true, '1'];
            $value = $this->getFieldValue($field, $definedType);
            if (in_array($value, $validValues) === false) {
                $this->pushError('accepted', [
                    'attribute' => $this->onlyCustomName($field, true),
                ], $field);
            }
        }

        /**
         * Boolean.
         * 
         * @param  string  $field
         * @param  string|null  $definedType
         * @return void
         */
        protected function booleanSegment($field, $definedType)
        {
            $validValues = ['yes', 'no', 'on', 'off', true, false, '0', '1'];
            $value = $this->getFieldValue($field, $definedType);

            if (in_array($value, $validValues) === false) {
                $this->pushError('boolean', [
                    'attribute' => $this->onlyCustomName($field, true),
                ], $field);
            }
        }

        /**
         * Email-address.
         * 
         * @param  string  $field
         * @param  string|null  $definedType
         * @return void
         */
        protected function emailSegment($field, $definedType)
        {
            $value = $this->getFieldValue($field, $definedType);
            if (! preg_match('/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/i', $value)) {
                $this->pushError('email', [
                    'attribute' => $this->onlyCustomName($field, true),
                ], $field);
            }
        }

        /**
         * Username.
         * 
         * @param  string  $field
         * @param  string|null  $definedType
         * @return void
         */
        protected function usernameSegment($field, $definedType)
        {
            $value = $this->getFieldValue($field, $definedType);
            if (preg_match('/[^a-z0-9_]/i', $value)) {
                $this->pushError('username', [
                    'attribute' => $this->onlyCustomName($field, true),
                ], $field);
            }
        }

        /**
         * No Whitespace.
         * 
         * @param  string  $field
         * @param  string|null  $definedType
         * @return void
         */
        protected function no_whitespaceSegment($field, $definedType)
        {
            $value = $this->getFieldValue($field, $definedType);
            if (preg_match('/\s+/', $value)) {
                $this->pushError('no_whitespace', [
                    'attribute' => $this->onlyCustomName($field, true),
                ], $field);
            }
        }

        /**
         * Single line.
         * 
         * @param  string  $field
         * @param  string|null  $definedType
         * @return void
         */
        protected function singlelineSegment($field, $definedType)
        {
            $value = $this->getFieldValue($field, $definedType);
            if(strstr($value, PHP_EOL) !== false) {
                $this->pushError('singleline', [
                    'attribute' => $this->onlyCustomName($field, true),
                ], $field);
            }
        }

        /**
         * One Whitespace.
         * 
         * @param  string  $field
         * @param  string|null  $definedType
         * @return void
         */
        protected function one_whitespaceSegment($field, $definedType)
        {
            $value = $this->getFieldValue($field, $definedType);
            if (preg_match('/\s\s+/', $value)) {
                $this->pushError('one_whitespace', [
                    'attribute' => $this->onlyCustomName($field, true),
                ], $field);
            }
        }

        /**
         * String length.
         * 
         * @param  string  $field
         * @param  string|null  $definedType
         * @param  string  $limit
         * @return void
         */
        protected function lengthSegment($field, $definedType, $limit)
        {
            $value = $this->getFieldValue($field, $definedType);
            if (mb_strlen($value) !== (int) $limit) {
                $this->pushError('length', [
                    'attribute' => $this->onlyCustomName($field, true),
                    'length' => $limit
                ], $field);
            }
        }

        /**
         * String length between.
         * 
         * @param  string  $field
         * @param  string|null  $definedType
         * @param  string  $limit
         * @return void
         */
        protected function length_betweenSegment($field, $definedType, $limit)
        {
            $value = $this->getFieldValue($field, $definedType);
            list($limitMin, $limitMax) = explode(',', $limit);

            if (! (mb_strlen($value) >= (int) $limitMin) || ! (mb_strlen($value) <= (int) $limitMax)) {
                $this->pushError('length_between', [
                    'attribute' => $this->onlyCustomName($field, true),
                    'min' => $limitMin,
                    'max' => $limitMax
                ], $field);
            }
        }

        /**
         * Validate JSON.
         * 
         * @param  string  $field
         * @param  string|null  $definedType
         * @return void
         */
        protected function jsonSegment($field, $definedType)
        {
            $value = $this->getFieldValue($field, $definedType);
            @json_decode($value);

            if ((json_last_error() !== JSON_ERROR_NONE)) {
                $this->pushError('json', [
                    'attribute' => $this->onlyCustomName($field, true)
                ], $field);
            }
        }

        /**
         * Field confirmation.
         * 
         * @param  string  $field
         * @param  string|null  $definedType
         * @return void
         */
        protected function confirmedSegment($field, $definedType)
        {
            $value = $this->getFieldValue($field, $definedType);
            $confirmation = $this->getFieldValue($field. '_confirmation', $definedType);

            if ($value !== $confirmation) {
                $this->pushError('confirmed', [
                    'attribute' => $this->onlyCustomName($field, true)
                ], $field);
            }
        }

        /**
         * Date format.
         * 
         * @param  string  $field
         * @param  string|null  $definedType
         * @param  string  $format
         * @return void
         */
        protected function date_formatSegment($field, $definedType, $format)
        {
            $value = $this->getFieldValue($field, $definedType);
            $dt = DateTime::createFromFormat($format, $value);
            $this->cache[$field]['date_format'] = $format;

            if ($dt === false || array_sum($dt->getLastErrors())) {
                $this->cache[$field]['status'] = false;
                $this->pushError('date_format', [
                    'attribute' => $this->onlyCustomName($field, true),
                    'format' => $format
                ], $field);
            } else {
                $this->cache[$field]['status'] = true;
            }
        }

        /**
         * After.
         * 
         * @param  string  $field
         * @param  string|null  $definedType
         * @param  string  $statement
         * @return void
         */
        protected function afterSegment($field, $definedType, $statement)
        {
            if (isset($this->cache[$field]['status']) && $this->cache[$field]['status'] === false) {
                return;
            }

            $value = strtotime($this->getFieldValue($field, $definedType));
            $rule = strtotime($statement);
            $format = $this->cache[$field]['date_format'];

            if ($rule >= $value) {
                $this->pushError('after', [
                    'attribute' => $this->onlyCustomName($field, true),
                    'date' => date($format, $rule)
                ], $field);
            }
        }

        /**
         * After or equal.
         * 
         * @param  string  $field
         * @param  string|null  $definedType
         * @param  string  $statement
         * @return void
         */
        protected function after_or_equalSegment($field, $definedType, $statement)
        {
            if (isset($this->cache[$field]['status']) && $this->cache[$field]['status'] === false) {
                return;
            }

            $value = strtotime($this->getFieldValue($field, $definedType));
            $rule = strtotime($statement);
            $format = $this->cache[$field]['date_format'];

            if ($rule > $value) {
                $this->pushError('after_or_equal', [
                    'attribute' => $this->onlyCustomName($field, true),
                    'date' => date($format, $rule)
                ], $field);
            }
        }

        /**
         * Before.
         * 
         * @param  string  $field
         * @param  string|null  $definedType
         * @param  string  $statement
         * @return void
         */
        protected function beforeSegment($field, $definedType, $statement)
        {
            if (isset($this->cache[$field]['status']) && $this->cache[$field]['status'] === false) {
                return;
            }

            $value = strtotime($this->getFieldValue($field, $definedType));
            $rule = strtotime($statement);
            $format = $this->cache[$field]['date_format'];

            if ($rule <= $value) {
                $this->pushError('before', [
                    'attribute' => $this->onlyCustomName($field, true),
                    'date' => date($format, $rule)
                ], $field);
            }
        }

        /**
         * Before or equal.
         * 
         * @param  string  $field
         * @param  string|null  $definedType
         * @param  string  $statement
         * @return void
         */
        protected function before_or_equalSegment($field, $definedType, $statement)
        {
            if (isset($this->cache[$field]['status']) && $this->cache[$field]['status'] === false) {
                return;
            }

            $value = strtotime($this->getFieldValue($field, $definedType));
            $rule = strtotime($statement);
            $format = $this->cache[$field]['date_format'];

            if ($rule < $value) {
                $this->pushError('before_or_equal', [
                    'attribute' => $this->onlyCustomName($field, true),
                    'date' => date($format, $rule)
                ], $field);
            }
        }

        /**
         * Validate URL.
         * 
         * @param  string  $field
         * @param  string|null  $definedType
         * @param  string  $format
         * @return void
         */
        protected function urlSegment($field, $definedType)
        {
            $value = $this->getFieldValue($field, $definedType);

            if (! preg_match('/^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/', $value)) {
                $this->pushError('url', [
                    'attribute' => $this->onlyCustomName($field, true)
                ], $field);
            }
        }

        /**
         * Determine whether the value already exists in the given table.
         * 
         * @param  string  $field
         * @param  string|null  $definedType
         * @param  string  $info
         * @return void
         */
        protected function uniqueSegment($field, $definedType, $info)
        {
            $value = $this->getFieldValue($field, $definedType);
            $info = explode(',', $info);

            $table = $info[0];
            $column = $info[1];
            $ignore = isset($info[2]) ? $info[2] : null;
            $idColumn = isset($info[3]) ? $info[3] : 'id';

            $count = DB::table($table)->where($column, $value);
            if ($ignore !== null) {
                $count->where($idColumn, '!=', $ignore);
            }
            
            if ($count->count() > 0) {
                $this->pushError('unique', [
                    'attribute' => $this->onlyCustomName($field, true)
                ], $field);
            }
        }

        /**
         * Determine whether the value already exists in the given table
         * 
         * @param  string  $field
         * @param  string|null  $definedType
         * @param  string  $info
         * @return void
         */
        protected function existsSegment($field, $definedType, $info)
        {
            $value = $this->getFieldValue($field, $definedType);
            $info = explode(',', $info);

            list($table, $column) = $info;

            $count = DB::table($table)->where($column, $value)->count();

            if ($count === 0) {
                $this->pushError('exists', [
                    'attribute' => $this->onlyCustomName($field, true)
                ], $field);
            }
        }

    }
