<?php

    namespace Powerhouse\Http\Validation;

    use Exception;
    use Powerhouse\Support\Extendable\Validator;

    abstract class Injection extends Segments
    {

        /**
         * Get the injected rules.
         * 
         * @param  array  $rulesArray
         * @return bool
         */
        protected function injectRules(array $rulesArray)
        {
            foreach ($rulesArray as $field => $rules)
            {
                // Parse rules of the field
                $this->parseRules($field, $rules);
            }
        }

        /**
         * Set default rules.
         * 
         * @param  array  $rules
         * @return array
         */
        protected function defaultRules(array $rules)
        {
            // Max length : 255
            if (! preg_grep('/max:\d+/', $rules) && !! preg_grep('/(string|float|integer)/', $rules)) {
                array_push($rules, 'max:255');
            }

            // Single line
            if (!! preg_grep('/(string)/', $rules) && ! preg_grep('/(singleline|multiline)/', $rules)) {
                array_push($rules, 'singleline');
            }

            return $rules;
        }

        /**
         * Parse the rules.
         * 
         * @param  string  $field
         * @param  string|array  $rules
         * @return bool
         */
        protected function parseRules(string $field, $rules)
        {
            // Split the rules into an array
            if (! is_array($rules))
                $rules = explode('|', $rules);

            // Sort the rules by an array of rules
            $rules = sort_array_by_array($rules, ['required', 'integer', 'float', 'string', 'file', 'array', 'file_array', 'image']);
            
            // Set default rules
            $rules = $this->defaultRules($rules);

            // Check if the field is an item or item container
            $continue = true;
            if (strpos($field, '.') !== false) {
                $parentName = $this->onlyName($field);
                if (isset($this->cache[$parentName]['validation']) && $this->cache[$parentName]['validation'] === true)
                    $continue = true;
                else
                    $continue = false;
            }

            // Continue
            if ($continue === true) {
                // Sub type
                $this->cache[$field]['subType'] = $subType = $this->subType($field);

                // Get the input type
                if ($subType === false) {
                    $result = $this->inputType($field);
                    $this->cache[$field]['inputType'][] = $result;
                    $inputType[] = $result;
                } else
                    $this->cache[$field]['inputType'] = $inputType = ($subType ? $this->subInputType($field) : $this->inputType($field));

                // Get the defined type
                $this->cache[$field]['definedType'] = $definedType = $this->getDefinedType($field, $rules);

                // Check if the input type is null (if the loop hasn't been locked)
                $lock = false;
                foreach ($inputType as $type) {
                    if ($type === null) {
                        // If the field is required push an error
                        if ($rules[0] === 'required')
                            $this->pushError('required', [
                                'attribute' => $this->onlyCustomName($field, true)
                            ], $field);

                        // lock the process
                        $lock = true;
                        break;
                    }
                }

                // Type validation (if the loop hasn't been locked)
                if ($lock === false) {
                    foreach ($inputType as $type)
                        if ($this->typeValidation($type, $definedType) === false) {
                            $this->pushError($definedType, [
                                'attribute' => $this->onlyCustomName($field, true)
                            ], $field);

                            // lock the process
                            $lock = true;
                            break;
                        }
                }

                if ($lock === false) {
                    // Validation status only for ['array', 'file_array']
                    if (in_array($definedType, ['array', 'file_array']))
                        $this->cache[$field]['validation'] = false;
                    
                    // Get the field value
                    $this->getFieldValue($field, $definedType);

                    $this->matchRules($field, $definedType,  $rules);
                }
            }
        }

        /**
         * Match the rules.
         * 
         * @param  string  $field
         * @param  string|null  $definedType
         * @param  array  $rules
         * @return bool
         */
        protected function matchRules(string $field, $definedType, array $rules)
        {
            // Loop permission
            $loop = true;

            // The loop of the rules (if the loop hasn't been locked)
            if ($loop === true){
                foreach ($rules as $segment) {
                    // Split the segment into two variables
                    $rule = $this->splitSegment($segment);
                    if (count($rule) === 1) {
                        $segment = $rule[0];
                        $value = null;
                    } else {
                        $segment = $rule[0];
                        $value = $rule[1];
                    }

                    // File loop
                    $this->fileLoop($field, $definedType, $segment, $value);

                    // Rules
                    switch ($segment) {
                        case 'accepted':
                            $this->acceptedSegment($field, $definedType);
                        break;
                        case 'min':
                            $this->minimumSegment($field, $definedType, $value);
                        break;
                        case 'max':
                            $this->maximumSegment($field, $definedType, $value);
                        break;
                        case 'between':
                            $this->betweenSegment($field, $definedType, $value);
                        break;
                        case 'boolean':
                            $this->booleanSegment($field, $definedType);
                        break;
                        case 'email':
                            $this->emailSegment($field, $definedType);
                        break;
                        case 'username':
                            $this->usernameSegment($field, $definedType);
                        break;
                        case 'no_whitespace':
                            $this->no_whitespaceSegment($field, $definedType);
                        break;
                        case 'singleline':
                            $this->singlelineSegment($field, $definedType);
                        break;
                        case 'one_whitespace':
                            $this->one_whitespaceSegment($field, $definedType);
                        break;
                        case 'length':
                            $this->lengthSegment($field, $definedType, $value);
                        break;
                        case 'length_between':
                            $this->length_betweenSegment($field, $definedType, $value);
                        break;
                        case 'json':
                            $this->jsonSegment($field, $definedType);
                        break;
                        case 'confirmed':
                            $this->confirmedSegment($field, $definedType);
                        break;
                        case 'date_format':
                            $this->date_formatSegment($field, $definedType, $value);
                        break;
                        case 'after':
                            $this->afterSegment($field, $definedType, $value);
                        break;
                        case 'after_or_equal':
                            $this->after_or_equalSegment($field, $definedType, $value);
                        break;
                        case 'before':
                            $this->beforeSegment($field, $definedType, $value);
                        break;
                        case 'before_or_equal':
                            $this->before_or_equalSegment($field, $definedType, $value);
                        break;
                        case 'dimensions':
                            $this->dimensionsSegment($field, $definedType, $value);
                        break;
                        case 'url':
                            $this->urlSegment($field, $definedType);
                        break;
                        case 'unique':
                            $this->uniqueSegment($field, $definedType, $value);
                        break;
                        case 'exists':
                            $this->existsSegment($field, $definedType, $value);
                        break;
                    }

                    // Custom rules
                    $extensions = Validator::getExtensions();
                    if (isset($extensions[$segment]) === true) {
                        $callback = $extensions[$segment]($field, $definedType, $value, $this->getFieldValue($field, $definedType));
                        if (is_bool($callback) === false)
                            throw new Exception("The validator extension returns a non-boolean value!");

                        if ($callback === false)
                            $this->pushError($segment, [
                                'attribute' => $this->onlyCustomName($field, true)
                            ], $field);
                    }
                }
            }

            // Verification
            if ($loop === true)
                if (isset($this->cache[$field]['validation']))
                    $this->cache[$field]['validation'] = true;
        }

        /**
         * Split the segment.
         * 
         * @param  string  $segment
         * @return array
         */
        protected function splitSegment($segment)
        {
            return explode(':', $segment);
        }

    }
