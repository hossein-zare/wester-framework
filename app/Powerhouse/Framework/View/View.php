<?php

    namespace Powerhouse\View;

    use Exception;

    class View extends Castle\Composer
    {
        use Traits\ViewDetails;

        /**
         * Inject arguments to the view.
         * 
         * @param  string|array  $arg1
         * @param  string  $arg2
         * @return \Powerhouse\View\Spark
         */
        public function with($arg1, $arg2 = null)
        {
            if (is_string($arg1))
                $array = [$arg1 => $arg2];
            else 
                $array = $arg1;

            return spark()->setArguments($array, false);
        }

        /**
         * Determine whether the view exists.
         * 
         * @param  string  $name
         * @return bool
         */
        public function exists($name)
        {
            $name = trim($name);
            $name = trim($name, '/');
            if (! is_string($name))
                return fale;

            return file_exists($this->path . $name . $this->suffix);
        }

    }
