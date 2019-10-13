<?php

    namespace Powerhouse\Http;

    use Powerhouse\Routing\Route;
    use Powerhouse\Http\ErrorPages;
    use Powerhouse\Http\Cache\File\FileCache;
    use Packages\Laravel\Support\Traits\Macroable;
    use Powerhouse\Castles\URL;

    abstract class Request extends Validation\Validator
    {
        use Macroable;

        /**
         * Store the values also known as data.
         *
         * @var array
         */
        protected static $dataset = [
            'get' => [],
            'post' => [],
            'input' => [],
            'files' => []
        ];
        
        /**
         * Already stored?.
         *
         * @var bool
         */
        protected static $alreadyStored = false;
        
        /**
         * Temporary method holder.
         *
         * @var string
         */
        protected static $temporaryMethod;

        /**
         * Create variables from The REQUEST values.
         *
         * @return void
         */
        public function __construct()
        {
            if (static::$alreadyStored === false) {
                foreach ($_GET as $key=>$value)
                    static::$dataset['get'][$key] = $value;
                
                foreach ($_POST as $key => $value)
                    static::$dataset['post'][$key] = $value;

                parse_str(file_get_contents("php://input"), static::$dataset['input']);

                foreach ($_FILES as $key => $value)
                    static::$dataset['files'][$key] = $value;
                
                static::$alreadyStored = true;
            }
        }

        /**
         * Get every value given in The GET Method.
         *
         * @param  string  $name
         * @return null|string
         */
        public function get($name = null)
        {
            if ($name !== null) {
                if (isset(static::$dataset['get'][$name]))
                    return static::$dataset['get'][$name];
            } elseif ($name === null) {
                static::$temporaryMethod = 'get';
                return $this;
            }
            
            return null;
        }
        
        /**
         * Get every value given in The POST Method.
         *
         * @param  string  $name
         * @return bool|string
         */
        public function post($name = null)
        {
            if ($name !== null) {
                if (isset(static::$dataset['post'][$name]))
                    return static::$dataset['post'][$name];
            } elseif ($name === null) {
                static::$temporaryMethod = 'post';
                return $this;
            }
            
            return null;
        }

        /**
         * Get every value given in The HTTP +1.1 Protocol.
         *
         * @param  string  $name
         * @return null|string
         */
        public function input($name = null)
        {
            if ($name !== null) {
                if (isset(static::$dataset['input'][$name]))
                    return static::$dataset['input'][$name];
            } elseif ($name === null) {
                static::$temporaryMethod = 'input';
                return $this;
            }
            
            return null;
        }

        /**
         * Get a file.
         *
         * @param  string  $name
         * @return bool|string
         */
        public function file($name = null)
        {
            if ($name !== null) {
                if (isset(static::$dataset['files'][$name]))
                    return new FileCache($name);
            } elseif ($name === null) {
                static::$temporaryMethod = 'files';
                return $this;
            }
            
            return null;
        }

        /**
         * Get the old inputs.
         * 
         * @param  string  $name
         * @return mixed
         */
        public function old($name)
        {
            $input = session()->flashed('input');

            if (isset($input[$name]) === true)
                return session()->flashed('input')[$name];

            return null;
        }

        /**
         * Retrieve values by the method.
         *
         * @param  string  $name
         * @return mixed
         */
        public function retrieveByMethod($method, $name)
        {
            $method = strtolower($method);

            if ($method === 'get')
                return $this->get($name);

            if ($method === 'post')
                return $this->post($name);

            if ($method === 'input')
                return $this->input($name);
        }
        
        /**
         * Change the values of variables.
         *
         * @param  string  $method
         * @param  string  $name
         * @param  string  $value
         * @return bool
         */
        public function change($method, $name, $value)
        {
            if (isset(static::$dataset[$method][$name])) {
                static::$dataset[$method][$name] = $value;
                return true;
            }
            
            return false;
        }

        /**
         * Change the dataset.
         * 
         * @param  array  $array
         * @return bool 
         */
        public function changeDataset($array)
        {
            static::$dataset = $array;
            return true;
        }
        
        /**
         * Get the HTTP Method.
         *
         * @param  string  $routingType
         * @return string
         */
        public function method($routingType = null)
        {
            $routingType = $routingType ?? URL::routingType();

            if ($routingType !== 'api') {
                // Form method in a hidden input
                if (isset($_POST['_method']) && $_SERVER['REQUEST_METHOD'] === 'POST' && is_string($_POST['_method'])) {
                    $request_method = strtoupper(substr(trim($_POST['_method']), 0, 7));
                } else
                    $request_method = $_SERVER['REQUEST_METHOD'];
            } else
                $request_method = $_SERVER['REQUEST_METHOD'];

            return $request_method;
        }

        /**
         * Get the real HTTP Method.
         * 
         * @return string
         */
        public function realMethod()
        {
            $method = $this->method();
            if ($_SERVER['REQUEST_METHOD'] === 'POST')
                return 'POST';

            return $method;
        }
        
        /**
         * Get all values.
         *
         * @return array
         */
        public function all()
        {
            return static::$dataset;
        }

        /**
         * Check if the variable exists.
         *
         * @param  string  $name
         * @return bool
         */
        public function exists($name)
        {
            if (isset(static::$dataset[static::$temporaryMethod][$name]))
                return true;
            
            return false;
        }
        
        /**
         * Check if the variable exists.
         *
         * @param  string  $name
         * @return bool
         */
        public function has($name)
        {
            if (isset(static::$dataset[static::$temporaryMethod][$name]))
                return true;
            
            return false;
        }

        /**
         * Check if the request is Ajax.
         *
         * @return bool
         */
        public function ajax()
        {
            return $this->response()->ajax();
        }

        /**
         * Check or get the http version
         * We detect the version by the request method.
         * 
         * @param  string  $check
         * @param bool
         */
        public function httpVersion($check = null)
        {
            return $this->response()->httpVersion($check);
        }

        /**
         * Get the response.
         *
         * @return object
         */
        public function response()
        {
            return new Response();
        }

        /**
         * Get the route.
         *
         * @return object
         */
        public function route()
        {
            return new Route();
        }

        /**
         * Take action on echo (last ending).
         * 
         * @return string
         */
        public function __tostring(){
            return $this->data;
        }
        
    }
