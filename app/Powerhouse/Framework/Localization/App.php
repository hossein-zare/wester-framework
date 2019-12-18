<?php

    namespace Powerhouse\Localization;

    use Exception;
    use Powerhouse\Support\Json;

    class App extends Attributes
    {

        /**
         * The list of language files.
         *
         * @var array
        */
        protected static $language = [];

        /**
         * Load the json file.
         * 
         * @return bool
         */
        protected function getJson()
        {
            $file = 'lang.json';
            if ($this->isset($file) === false) {
                $path = $this->getPath($file);

                if ($this->exists($path)) {
                    $this->store($file, json_decode($this->load($path), true));

                    if (Json::lastError() !== false)
                        throw new Exception(Json::lastError() . " - in {$file}");

                    return true;
                }
            } else {
                return true;
            }

            return false;
        }

        /**
         * Load the php file.
         * 
         * @param  string  $name
         * @return bool
         */
        protected function getPHP($name)
        {
            if ($name === null)
                return false;

            $file = "{$name}.php";
            if ($this->isset($file) === false) {
                $path = $this->getPath($file);

                if ($this->exists($path)) {
                    $array = include $path;
                    $this->store($file, $array);

                    return true;
                }
            } else {
                return true;
            }

            return false;
        }

        /**
         * Load the given language file.
         * 
         * @param  string  $path
         * @return string
         */
        protected function load($path)
        {
            return file_get_contents($path);
        }

        /**
         * Set extension to the name.
         * 
         * @param  string  $name
         * @param  string  $extension
         * @return string
         */
        protected function setExtension(string $name, string $extension)
        {
            return "{$name}.{$extension}";
        }

        /**
         * Get the array of the language.
         * 
         * @param  string  $name
         * @return array
         */
        public function getLanguage($name)
        {
            return self::$language[$name];
        }

        /**
         * Indicates whether the language file is set.
         * 
         * @param  string  $name
         * @return bool
         */
        protected function isset($name)
        {
            return isset(self::$language[$name]);
        }

        /**
         * Store the language.
         * 
         * @param  string  $name
         * @param  array  $set
         * @return void
         */
        protected function store(string $name, array $set)
        {
            self::$language[$name] = $set;
        }

        /**
         * Unset a language file.
         * 
         * @return void
         */
        public function unset($name)
        {
            unset(self::$language[$name]);
        }

        /**
         * Get the full path.
         * 
         * @param  string  $name
         * @return string
         */
        protected function getPath($name = null)
        {
            global $config;

            $locale = $config['locale'];
            return "./app/Resources/lang/{$locale}/{$name}";
        }

        /**
         * Determine whether the file exists.
         * 
         * @param  string  $path
         * @return bool
         */
        protected function exists($path)
        {
            return file_exists($path);
        }

    }
