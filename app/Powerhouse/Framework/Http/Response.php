<?php

    namespace Powerhouse\Http;

    use Powerhouse\Castles\URL;
    use Powerhouse\Http\ErrorPages;

    class Response
    {
        /**
         * The List of HTTP Responses.
         *
         * @var string
         */
        protected static $responses = [
            /* Successful responses */
            200 => [true, 'OK'],
            201 => [true, 'Created'],
            202 => [true, 'Accepted'],
            203 => [true, 'Non-Authoritative Information'],
            204 => [true, 'No Content'],
            205 => [true, 'Reset Content'],
            206 => [true, 'Partial Content'],
            207 => [true, 'Multi-Status'],
            208 => [true, 'Multi-Status'],
            226 => [true, 'IM Used'],
            
            /* Client error responses */
            400 => [false, 'Bad Request'],
            401 => [false, 'Unauthorized'],
            402 => [false, 'Payment Required'],
            403 => [false, 'Forbidden'],
            404 => [false, 'Not Found'],
            405 => [false, 'Method Not Allowed'],
            406 => [false, 'Not Acceptable'],
            407 => [false, 'Proxy Authentication Required'],
            408 => [false, 'Request Timeout'],
            409 => [false, 'Conflict'],
            410 => [false, 'Gone'],
            411 => [false, 'Length Required'],
            412 => [false, 'Precondition Failed'],
            413 => [false, 'Payload Too Large'],
            414 => [false, 'URI Too Long'],
            415 => [false, 'Unsupported Media Type'],
            416 => [false, 'Requested Range Not Satisfiable'],
            417 => [false, 'Expectation Failed'],
            418 => [false, 'I\'m a teapot'],
            421 => [false, 'Misdirected Request'],
            422 => [false, 'Unprocessable Entity'],
            423 => [false, 'Locked'],
            424 => [false, 'Failed Dependency'],
            425 => [false, 'Too Early'],
            426 => [false, 'Upgrade Required'],
            428 => [false, 'Precondition Required'],
            429 => [false, 'Too Many Requests'],
            431 => [false, 'Request Header Fields Too Large'],
            451 => [false, 'Unavailable For Legal Reasons'],
            
            /* Server error responses */
            500 => [false, 'Internal Server Error'],
            501 => [false, 'Not Implemented'],
            502 => [false, 'Bad Gateway'],
            503 => [false, 'Service Unavailable'],
            504 => [false, 'Gateway Timeout'],
            505 => [false, 'HTTP Version Not Supported'],
            506 => [false, 'Variant Also Negotiates'],
            507 => [false, 'Insufficient Storage'],
            508 => [false, 'Loop Detected'],
            510 => [false, 'Not Extended'],
            511 => [false, 'Network Authentication Required']
        ];
        
        /**
         * HTTP Response Status.
         *
         * @var string
         */
        protected static $responseCode = 200;

        /**
         * Holds the headers.
         * 
         * @var array
         */
        protected static $repository = [];

        /**
         * Get or Set Headers.
         *
         * @param  string  $name
         * @param  string  $value
         * @return null|string
         */
        public function header($name, $value = null, ...$args)
        {
            // Set headers
            if ($value !== null) {
                header("{$name}: {$value}", ...$args);
                
                // Add the header to the repository
                $this->addToRepository($name, $value);

                return true;
            }
            
            // Get headers
            $name = strtoupper($name);
            $name = str_replace('-', '_', $name);
            
            if (isset($_SERVER[$name]))
                if (!is_array($_SERVER[$name]))
                    return $_SERVER[$name];

            return null;
        }

        /**
         * Add headers to the repository.
         * 
         * @param  string  $name
         * @param  string  $value
         * @return void
         */
        protected function addToRepository(string $name, $value)
        {
            $name = strtolower($name);
            self::$repository[$name] = $value;
        }

        /**
         * Pull a header from the repository.
         * 
         * @param  string  $name
         * @return string
         */
        public function repository(string $name)
        {
            $name = strtolower($name);
            return self::$repository[$name] ?? null;
        }

        /**
         * Set or get http response code.
         * 
         * @param  int  $code
         * @return int|void
         */
        public function status($code = null)
        {
            if ($code === null) 
                return static::$responseCode;

            static::$responseCode = $code;
            http_response_code($code);
        }

        /**
         * Set content-type.
         * 
         * @param  string  $type
         * @return void
         */
        public function contentType(string $type)
        {
            switch ($type) {
                case 'json':
                    $this->asJson();
                break;
            }
        }

        /**
         * Set json header.
         * 
         * @return void
         */
        public function asJson()
        {
            $this->header('Content-Type', 'application/json');
        }

        /**
         * Determine whether the content type is json.
         * 
         * @return bool
         */
        public function isJson()
        {
            return $this->repository('content-type') === 'application/json';
        }

        /**
         * Abort.
         * 
         * @param  int  $code
         * @return void
         */
        public function abort($code, $message = null, $errors = null, $type = null)
        {
            $this->status($code);

            if ($this->expectsJson()) {
                $this->asJson();
                echo json_message([
                    'code' => $code,
                    'message' => $message !== null ? $message : static::$responses[$code][1],
                    'type' => $type,
                    'errors' => $errors !== null ? $errors : null
                ]);
            } else {
                echo (new ErrorPages($code, static::$responses[$code][1]))->show();
            }

            // Shutdown
            die();
        }

        /**
         * Determine whether json output is expected
         * 
         * @return bool
         */
        public function expectsJson()
        {
            return $this->ajax() === true || $this->httpVersion('1.1') || URL::routingType() === 'api' || $this->isJson();
        }

        /**
         * Get all information.
         * 
         * @return object
         */
        public function data()
        {
            $code = self::$responseCode;
            $status = self::$responses[$code];

            return array_to_object([
                'code' => $code,
                'status' => $status[0],
                'message' => $status[1]
            ]);
        }

        /**
         * Check if the request is Ajax.
         * 
         * @return bool
         */
        public function ajax()
        {
            if (strtolower($this->header('HTTP-X-REQUESTED-WITH')) === 'xmlhttprequest')
                return true;
            
            return false;
        }

        /**
         * Check or get the http version
         * We detect the version by the request method.
         * 
         * @param  string  $check
         * @return string
         */
        public function httpVersion($check = null)
        {
            $method = $_SERVER['REQUEST_METHOD'];

            $version = '1.0';
            if (!in_array($method, ['POST', 'GET']))
                $version = '1.1';
            
            if ($check !== null) {
                if ($check === '1')
                    $check = '1.0';

                if ($check === $version)
                    return true;
                return false;
            }

            return $version;
        }

    }
