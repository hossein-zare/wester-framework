<?php

    namespace Powerhouse\Http\Validation;

    abstract class Segments extends Rules\Rules
    {

        /**
         * The list of types.
         * 
         * @var array
         */
        protected $typeList = [
            'number' => ['integer', 'float'],
            'string' => ['string'],
            'array' => ['array', 'file_array'],
            'file' => ['file', 'image']
        ];

        /**
         * Validate the input type.
         * 
         * @param  string  $inputType
         * @param  string  $definedType
         * @return bool
         */
        protected function typeValidation($inputType, $definedType)
        {
            if ($definedType !== null) {

                // Strings can contain floats and integers too
                if ($definedType === 'string' && in_array($inputType, ['integer', 'float']))
                    return true;

                // File and Image
                if ($inputType === 'file' && $definedType === 'image')
                    return true;

                if ($inputType === $definedType)
                    return true;
                else
                    return false;

            } else
                return true;
        }

        /**
         * Get field value.
         * 
         * @param  string  $field
         * @param  string  $definedType
         * @return string
         */
        protected function getFieldValue($field, $definedType)
        {
            if ($this->fieldValue === false || isset($this->fieldValue[$field]) === false) {
                if (in_array($definedType, ['file', 'file_array', 'image']) === false) {
                    if (strpos($field, '.') === false) {
                        $value = request()->retrieveByMethod($this->method, $this->onlyName($field));
                        $value = $this->convertType($value, $definedType);
                    } else {
                        $parentName = $this->onlyName($field);
                        $input = [$parentName => request()->retrieveByMethod($this->method, $this->onlyName($field))];
                        $value = array_get($input, $field, null);
                    }
                } else {
                    $value = request()->file($this->onlyName($field))->raw();
                    if ($definedType === 'file_array')
                        $value = $value['name'];
                }

                $this->fieldValue[$field] = $value;
            }

            return $this->fieldValue[$field];
        }

        /**
         * The loop of files.
         * 
         * @param  string  $field
         * @param  string  $definedType
         * @param  string  $segment
         * @param  string|null  $value
         * @return  bool
         */
        protected function fileLoop($field, $definedType, $segment, $value)
        {
            $types = ['file', 'image'];
            if (in_array($definedType, $types) === true) {
                $segments = ['image', 'mimes', 'mimetypes'];
                if (in_array($segment, $segments) === true) {
                    $file = request()->file($this->onlyName($field))->raw();

                    if ($this->isParent($field)) {
                        $files = [$file['name']];
                        $tmps = [$file['tmp_name']];
                    } else {
                        $files = $file['name'];
                        $tmps = $file['tmp_name'];
                    }

                    for ($i = 0; $i < count($files); $i++) {
                        $ext = $this->getExtension($files[$i]);
                        if ($segment === 'image' && $this->validImage($tmps[$i], $ext) === false) {
                            $this->pushError('image', [
                                'attribute' => $this->onlyCustomName($field, true)
                            ], $field);
                            break;
                        }

                        if ($segment === 'mimes') {
                            $mimeType = $this->getMimeType($tmps[$i]);
                            $types = explode(',', $value);
                            $expectedMimeTypes = array_map(function ($ext) {
                                return $this->guessMimeType($ext);
                            }, $types);

                            if (in_array($mimeType, $expectedMimeTypes) === false || in_array($ext, $types) === false) {
                                $this->pushError('mimes', [
                                    'attribute' => $this->onlyCustomName($field, true),
                                    'values' => implode(', ', $types)
                                ], $field);
                                break;
                            }
                        }

                        if ($segment === 'mimetypes') {
                            $types = explode(',', $value);
                            if (! in_array($this->getMimeType($tmps[$i]), $types)) {
                                $values = array_map(function ($type) {
                                    return $this->guessExtension($type);
                                }, $types);

                                $this->pushError('mimetypes', [
                                    'attribute' => $this->onlyCustomName($field, true),
                                    'values' => implode(', ', $values)
                                ], $field);
                                break;
                            }
                        }
                    }
                }
            }
        }

    }
