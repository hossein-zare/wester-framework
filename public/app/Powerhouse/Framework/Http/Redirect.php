<?php

    namespace Powerhouse\Http;

    use Powerhouse\Castles\Response;

    class Redirect
    {

        /**
         * The destination of the redirection.
         * 
         * @var string
         */
        protected static $destination = '';

        /**
         * Redirect the page.
         * 
         * @param  string|null  $url
         * @return void
         */
        public function redirect($url = null)
        {
            if ($url === null)
                self::$destination = 'location: ' . $_SERVER['REQUEST_URI'];
            else
                self::$destination = 'location: ' . $url;

            return $this;
        }

        /**
         * Redirect the page.
         * 
         * @return $this
         */
        public function back()
        {
            if (!isset($_SERVER['HTTP_REFERER']))
                abort(406);
            self::$destination = 'location: ' . $_SERVER['HTTP_REFERER'];

            return $this;
        }

        /**
         * Generate urls to named routes.
         * 
         * @param  string  $name
         * @param  array  $parameters
         * @return $this
         */
        public function route($name, $parameters = [])
        {
            self::$destination = 'location: ' . route($name, $parameters);

            return $this;
        }

        /**
         * Flash messages.
         * 
         * @param  string  $name
         * @param  mixed  $value
         * @return $this
         */
        public function with($name, $value)
        {
            flash($name, $value);

            return $this;
        }

        /**
         * Flash all post inputs.
         * 
         * @return $this
         */
        public function withInput()
        {
            flash('input', request()->all()['post']);

            return $this;
        }

        /**
         * Redirect the user.
         * 
         * @return void
         */
        public function do()
        {
            while (ob_get_level() > 0)
                ob_end_clean();

            Response::status(302);
            header(self::$destination);
            die();
        }

        /**
         * Take action on echo (last ending).
         * 
         * @return string
         */
        public function __toString()
        {
            $this->do();

            return '';
        }
    }
