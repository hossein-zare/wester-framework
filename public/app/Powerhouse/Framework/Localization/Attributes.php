<?php

    namespace Powerhouse\Localization;

    use Powerhouse\Support\Arr;

    abstract class Attributes extends DynamicText
    {

        /**
         * Set locale.
         *
         * @param  string  $locale
         * @return void
         */
        public function setLocale($locale)
        {
            global $config;

            $path = $this->getPath();
            if ($this->exists($path))
                $config['locale'] = $locale;
            else
                $config['locale'] = $config['fallback_locale'];
        }

        /**
         * Get the current locale.
         *
         * @return void
         */
        public function getLocale()
        {
            global $config;

            return $config['locale'];
        }

        /**
         * Check the locale.
         * 
         * @param  string  $locale
         * @return bool
         */
        public function isLocale($locale)
        {
            global $config;

            return $config['locale'] === $locale;
        }

        /**
         * Add more language arrays.
         * 
         * @param  string  $name
         * @param  array  $set
         * @return void
         */
        protected function add(string $name, array $set)
        {
            if ($this->isset($name))
                $set = array_merge($this->getLanguage($name), $set);

            $this->store($name, $set);
        }

        /**
         * Customize languages.
         * 
         * @param  string  $name
         * @param  array  $set
         * @return void
         */
        public function customize(string $name, array $set)
        {
            $this->add($name, $set);
        }

        /**
         * Get the language file and the text out of the key.
         * 
         * @param  string  $key
         * @return array|null
         */
        protected function extractKey(string $key)
        {
            $dot = strpos($key, '.');
            if ($dot !== false) {
                $key = array_map(function ($item) {
                    return trim($item);
                }, explode('.', $key));

                return $key;
            }
            
            return $key;
        }

        /**
         * Get the key name.
         * 
         * @param  array|string  $key
         * @return void
         */
        protected function getKeyName($key)
        {
            if (is_array($key)) {
                $name = $key[0];
            } else {
                $name = null;
            }

            return $name;
        }

        /**
         * Get the key value.
         * 
         * @param  array|string  $key
         * @return void
         */
        protected function getKeyValue($key)
        {
            if (is_array($key)) {
                $count = count($key);

                if ($count === 2)
                    return $key[1];

                unset($key[0]);
                return implode('.', $key);
            } else {
                return null;
            }
        }

        /**
         * Get a translation from the json file.
         * 
         * @param  string  $sentence
         * @return string|bool
         */
        protected function getSentence($sentence)
        {
            if (! $this->getJson())
                return false;

            if ($this->isset('lang.json'))
                return $this->getLanguage('lang.json')[$sentence] ?? false;

            return false;
        }

        /**
         * Get a translation from the a php file.
         * 
         * @param  string  $sentence
         * @return string|bool
         */
        protected function getWord($key)
        {
            $key = $this->extractKey($key);

            $name = $this->getKeyName($key);
            if (! $this->getPHP($name))
                return false;

            $value = $this->getKeyValue($key);
            $name = $this->setExtension($name, 'php');

            if ($this->isset($name))
                return Arr::get($this->getLanguage($name), $value, false);


            return false;
        }

        /**
         * Get a translation from the custom messages.
         * 
         * @param  string  $key
         * @return string|bool
         */
        protected function getCustomMessage($key)
        {
            $key = $this->extractKey($key);

            $name = $this->getKeyName($key);
            if ($name === null)
                return false;

            // This value may be a dot notation nested array string
            // so we shall dive into the nest.
            $value = $this->getKeyValue($key);

            if ($this->isset('custom') && isset($this->getLanguage('custom')[$name]))
                return Arr::get($this->getLanguage('custom')[$name], $value, false);

            return false;
        }

        /**
         * Get a translation.
         * 
         * @param  string  $key
         * @param  mixed  $a
         * @param  mixed  $b
         * @return string|null
         */
        public function get(string $key, $a = null, $b = null)
        {
            $selection = null;
            $variables = [];

            if (! is_array($a) && $a !== null)
                $selection = $a;

            if (is_array($a))
                $variables = $a;

            if (! is_array($b) && $b !== null)
                $selection = $b;

            if (is_array($b))
                $variables = $b;

            $translation = $this->getCustomMessage($key) ?: $this->getSentence($key) ?: $this->getWord($key) ?: null;
            $translation = $this->chooseSentence($translation, $selection);
            $translation = $this->replaceTags($translation, $variables);

            return $translation ?: $key;
        }

    }
