<?php

    namespace Powerhouse\Storage;

    use Exception;

    abstract class Attributes extends Uploader
    {

        /**
         * Advanced file management.
         * 
         * @param  string  $path
         * @param  string  $mode
         * @param  callable  $callback
         * @return $this
         */
        public function fileBack($path, $mode, callable $callback)
        {
            $address = $this->merge($path);
            $file = fopen($address, $mode);
            
            // Callback
            $data = $callback($file);

            fclose($file);

            return $data;
        }

        /**
         * Create a new file with some contents.
         * 
         * @param  string  $path
         * @param  string  $contents
         * @return $this
         */
        public function put($path, $contents)
        {
            $address = $this->merge($path);
            $file = fopen($address, 'w');
            fwrite($file, $contents);
            fclose($file);

            return $this;
        }

        /**
         * Delete a file.
         * 
         * @param  string|array  $path
         * @return bool|array
         */
        public function delete($path)
        {
            if (is_array($path) !== true) {
                $address = $this->merge($path);

                if (is_file($address) === true) {
                    if (unlink($address) === true)
                        return true;
                    return false;
                }

                return false;
            } else {
                $results = [];
                foreach ($path as $dir) {
                    $address = $this->merge($dir);

                    if (is_file($address) === true) {
                        if (unlink($address) === true)
                            $results[] = true;
                        else
                            $results[] = false;
                    } else
                        $results[] = false;
                }

                return $results;
            }
        }

        /**
         * Write to the end of the file.
         * 
         * @param  string  $path
         * @param  string  $contents
         * @return $this
         */
        public function append($path, $contents)
        {
            $address = $this->merge($path);
            $file = fopen($address, 'a');
            fputs($file, $contents);
            fclose($file);

            return $this;
        }

        /**
         * Download the file.
         * 
         * @param  string  $path
         * @return $this
         */
        public function download($path)
        {
            $address = $this->merge($path);

            if ($this->exists($path) === false)
                abort(404);

            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary"); 
            header("Content-disposition: attachment; filename=\"" . basename($address) . "\""); 
            readfile($address);
            exit;
        }

        /**
         * Get a file contents.
         * 
         * @param  string  $path
         * @param  string  $type
         * @return $this
         */
        public function get($path, $type = 'fread')
        {
            $address = $this->merge($path);

            if ($this->exists($path)) {
                switch ($type) {
                    case 'fread':
                        $file = fopen($address, 'r');
                        $size = filesize($address);
                        $contents = $size > 0 ? fread($file, $size) : '';
                        fclose($file);
                    break;
                    case 'file_get_contents':
                        $contents = file_get_contents($address);
                    break;
                    default:
                        throw new Exception("Type '<b>{$type}</b>' is invalid to get file contents!");
                }
            } else
                return null;

            return $contents;
        }

        /**
         * Get a file size.
         * 
         * @param  string  $path
         * @return int|float
         */
        public function size($path)
        {
            $address = $this->merge($path);
            return filesize($address);
        }

        /**
         * Get a file last modified time.
         * 
         * @param  string  $path
         * @return int
         */
        public function lastModified($path)
        {
            $address = $this->merge($path);
            return filemtime($address);
        }

        /**
         * Determine whether the file exists.
         * 
         * @param  string  $path
         * @return bool
         */
        public function exists($path)
        {
            $address = $this->merge($path);

            if (file_exists($address) === true)
                return true;
            return false;
        }

        /**
         * Make a directory.
         * 
         * @param  string  $folder
         * @param  string  $asDefault
         * @return bool
         */
        public function makeDir($folder, $asDefault = false)
        {
            $dirname = $this->merge($folder);

            if (!is_dir($dirname))
                mkdir($dirname, 0755, true);

            if ($asDefault === true)
                $this->setDir($folder);

            return $this;
        }

        /**
         * Delete a directory.
         * 
         * @param  string  $folder
         * @return bool
         */
        public function deleteDir($folder)
        {
            $dirname = $this->merge($folder);

            if (strpos($dirname, '/') === false)
                throw new Exception('Please specify a correct directory name!');

            if (is_dir($dirname) === true) {
                if (rmdir($dirname))
                    return true;
                return false;
            }

            return false;
        }

        /**
         * Set the directory as default.
         * 
         * @param  string  $folder
         * @return bool
         */
        public function setDir($folder)
        {
            $dirname = $this->merge($folder);
            $this->disk->root = $dirname;

            return $this;
        }

        /**
         * Get all files in the directory.
         * 
         * @param  string  $path
         * @return bool
         */
        public function files($path)
        {
            $path = $this->merge($path);
            $list = glob($path);

            $list_temp = [];
            foreach ($list as $element)
                if (is_file($element) === true)
                    $list_temp[] = $element;

            return $list_temp;
        }

        /**
         * Get all directories in the directory.
         * 
         * @param  string  $path
         * @return bool
         */
        public function directories($path)
        {
            $path = $this->merge($path);
            $list = glob($path);

            $list_temp = [];
            foreach ($list as $element)
                if (is_dir($element) === true)
                    $list_temp[] = $element;

            return $list_temp;
        }

    }
