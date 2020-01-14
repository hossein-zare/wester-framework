<?php

    namespace Powerhouse\Storage;

    abstract class Uploader
    {

        /**
         * Put file to the disk (An alias of the uploadFile function).
         * 
         * @param  object  $file
         * @return bool|array
         */
        public function putFile($file)
        {
            return $this->splitFile($file);
        }

        /**
         * Put file as to the disk (An alias of the uploadFile function).
         * 
         * @param  object  $file
         * @param  string  $manualName
         * @return bool|array
         */
        public function putFileAs($file, $manualName)
        {
            return $this->splitFile($file, $manualName);
        }

        /**
         * Split the file before uploading.
         * 
         * @param  object  $file
         * @param  string  $manualName
         * @return bool|array
         */
        protected function splitFile($file, $manualName = null)
        {
            $name = $file->name();

            // Determine string or array
            if (is_array($name) === false && is_string($name) === true){
                $result = $this->upload($file->get(), $name, $file->extension(), $manualName);
                return [$result];
            }
            else {
                $results = [];
                $count = count($name);
                for ($i = 0; $i < $count; $i++)
                    $results[] = $this->upload($file->get()[$i], $name[$i], $file->extension()[$i], $manualName);
                return $results;
            }

        }

        /**
         * Upload the given file.
         * 
         * @param  object  $object
         * @param  string  $defaultName
         * @param  string  $extension
         * @param  string  $name
         * @return bool|string
         */
        private function upload($file, $defaultName, $extension, $name = null)
        {
            $filename = $defaultName;
            $random_name = time().uniqid(rand());

            // Manual name
            if ($name !== null) {
                // Random name
                if (strpos($name, '*') !== false)
                    $filename = str_replace('*', $random_name, $name) . '.' . $extension;
                else {
                    $path = $this->merge($name . '.' . $extension);
                    if (file_exists($path) === true)
                        $filename = $name . '_' . $random_name . '.' . $extension;
                    else
                        $filename = $name . '.' . $extension;
                }
            } else {
                $filename .= '.' . $extension;
                $path = $this->merge($filename);
                if (file_exists($path) === true)
                    $filename = $defaultName . '_' . $random_name . '.' . $extension;
            }

            $target = $this->merge($filename);
            if (move_uploaded_file($file, $target))
                return $filename;
            else
                return false;
        }

    }
