<?php

    namespace Powerhouse\View;

    use Exception;

    class Spark extends Engine
    {
        use Traits\ViewDetails;

        /**
         * The suffix of view files.
         * 
         * @var string
         */
        protected $suffix = '.spark.php';

        /**
         * The arguments of the view.
         * 
         * @var array
         */
        protected $arguments = [];

        /**
         * Create a new spark instance.
         * 
         * @param  string  $view
         * @param  array  $arguments
         * @return void
         */
        public function config(string $view, array $arguments = [])
        {
            $this->view = $view;
            $this->arguments = $arguments;
        }

        /**
         * Set arguments.
         * 
         * @param  array  $array
         * @param  bool  $reset
         * @return bool
         */
        public function setArguments($array, $reset = false)
        {
            if ($reset === false)
                $this->arguments = array_merge($this->arguments, $array);
            else
                $this->arguments = $array;

            return true;
        }

        /**
         * Get contents of the view.
         * 
         * @param  string  $view
         * @return string
         * 
         * @throws \Powerhouse\Exceptions\Exception
         */
        protected function getContents($view)
        {
            if (file_exists($view))
                return file_get_contents($view);
            else
                throw new Exception("The view doesn't exist!");
        }

        /**
         * Get the name of the view.
         * 
         * @return string
         */
        protected function name()
        {
            return $this->getName();
        }

        /**
         * Get the name of the view.
         * 
         * @return string
         */
        protected function getName()
        {
            return $this->view;
        }

        /**
         * Get the path of the view.
         * 
         * @return string
         */
        protected function getViewPath()
        {
            return $this->getPath() . $this->getName() . $this->getSuffix();
        }

        /**
         * Get the path to the view file.
         *
         * @return string
         */
        public function getPath()
        {
            return $this->path;
        }

        /**
         * Get the suffix of the view file.
         *
         * @return string
         */
        public function getSuffix()
        {
            return $this->suffix;
        }

        /**
         * Render the view.
         * 
         * @return string
         */
        protected function render()
        {
            return $this->renderContents();
        }

        /**
         * Get the string contents of the view.
         *
         * @return string
         */
        public function getRendered()
        {
            return $this->render();
        }

        /**
         * Get the string contents of the view.
         *
         * @return string
         */
        public function __toString()
        {
            return $this->render();
        }

    }