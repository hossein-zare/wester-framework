<?php

    use App\Transit\Http\Handler\Request;
    use Powerhouse\Routing\Route;
    use Powerhouse\Castles\App;
    use Powerhouse\Support\HtmlString;
    use Powerhouse\View\Spark;
    use Powerhouse\Footprint\Session;
    use Powerhouse\Castles\URL;
    use Powerhouse\Auth\Auth;
    use Powerhouse\Support\Arr;
    use Powerhouse\Support\Units;
    use Powerhouse\Castles\Cache;
    use Powerhouse\Castles\Redirect;
    use Powerhouse\Castles\Response;
    use Powerhouse\Support\Str;
    use Powerhouse\Footprint\Cookie;
    use Powerhouse\Footprint\DisposableToken;
    use Powerhouse\Encryption\Encrypter;
    use Carbon\Carbon;
    use Powerhouse\View\BranchCompilers\Branches\Stacks;
    use Powerhouse\Interfaces\Json\Jsonable;
    use Powerhouse\Interfaces\Json\JsonString;
    use Powerhouse\Event\Event;

    // Framework structure
    $structure = [
        'request' => new Request(),
        'route' => new Route(),
        'spark' => new Spark(),
        'routerStatus' => 0
    ];

    /**
     * Create an instance of Stacks.
     * 
     * @return \Powerhouse\View\BranchCompilers\Branches\Stacks
     */
    if (!function_exists('compileStacks')) {
        function compileStacks()
        {
            if (isset($GLOBALS['structure']['compile_stacks']))
                return $GLOBALS['structure']['compile_stacks'];

            $GLOBALS['structure']['compile_stacks'] = new Stacks();
            return $GLOBALS['structure']['compile_stacks'];
        }
    }

    /**
     * Determine whether the variable is an instance of Jsonable.
     * 
     * @param  mixed  $value
     * @return bool
     */
    if (!function_exists('isJsonable')) {
        function isJsonable($value)
        {
            if ($value instanceof Jsonable)
                return true;

            return false;
        }
    }

    /**
     * Determine whether the variable is an instance of JsonString.
     * 
     * @param  mixed  $value
     * @return bool
     */
    if (!function_exists('isJsonString')) {
        function isJsonString($value)
        {
            if (isJsonable($value) === true && $value instanceof JsonString)
                return true;
            return false;
        }
    }

    /**
     * Return the spark template.
     * 
     * @return \Powerhouse\View\Spark
     */
    if (!function_exists("spark")) {
        function spark()
        {
            return $GLOBALS['structure']['spark'];
        }
    }

    /**
     * Redirect the page.
     * 
     * @param  string|null  $url
     * @return void
     */
    if (!function_exists("redirect")) {
        function redirect($url = null)
        {
            return Redirect::redirect($url);
        }
    }

    /**
     * Redirect the page.
     * 
     * @return \Powerhouse\Http\Redirect
     */
    if (!function_exists("back")) {
        function back()
        {
            return Redirect::back();
        }
    }

    /**
     * Determine multiple files.
     * 
     * @param  array|string  $file
     * @return bool
     */
    if (!function_exists("determine_multiple_files")) {
        function determine_multiple_files($file)
        {
            if (is_array($file['name']))
                return true;

            return false;
        }
    }

    /**
     * Map arrays and get the latest values.
     * 
     * @param  array  $array
     * @param  callback  $callback
     * @return string|null
     */
    if (!function_exists("array_value")) {
        function array_value(array $array, callable $callback)
        {
            return Arr::value($array, $callback);
        }
    }

    /**
     * Get array elements by dot notation.
     * 
     * @param  array  $array
     * @param  string  $key
     * @param  string|null  $default
     * @return array|string|null
     */
    if (!function_exists("array_get")) {
        function array_get($array, $key, $default = null)
        {
            return Arr::get($array, $key, $default);
        }
    }

    /**
     * Format bytes.
     * 
     * @param  int|float  $size
     * @param  int  $precision
     * @return string
     */
    if (!function_exists("format_bytes")) {
        function formatBytes($size, $precision = 2)
        {
            return Units::formatBytes($size, $precision);
        }
    }

    /**
     * Convert bytes to kilobytes.
     * 
     * @param  int|float  $size
     * @return int|float
     */
    if (!function_exists("bytes_to_kilobytes")) {
        function bytes_to_kilobytes($size)
        {
            return Units::bytesToKilobytes($size);
        }
    }

    /**
     * Sort array by array.
     * 
     * @param  array  $array
     * @param  array  $sort_array
     * @return array
     */
    if (!function_exists('sort_array_by_array')) {
        function sort_array_by_array($array, $sort_first)
        {
            usort ($array, function ($a, $b) use ($sort_first) {
                $order_a = array_search( $a, $sort_first );
                $order_b = array_search( $b, $sort_first );
            
                if ($order_a === false && $order_b !== false) {
                    return 1;
                } elseif ($order_b === false && $order_a !== false) {
                    return -1;
                }
                // elseif ($order_a === $order_b) {
                //    return $a <=> $b;
                // }
                else {
                    return $order_a <=> $order_b;
                }
            });

            return $array;
        }
    }

    /**
     * Convert a string to an array.
     * 
     * @param  string  $string
     * @return array
     */
    if (!function_exists('toArray')) {
        function toArray($string)
        {
            if (!is_array($string))
                return [$string];
            return $string;
        }
    }

    /**
     * Capitalize only the first letter.
     * 
     * @param  string  $string
     * @return string
     */
    if (!function_exists('ucfirst_only')) {
        function ucfirst_only($string)
        {
            return Str::ucfirst(Str::lower($string));
        }
    }

    /**
     * Create an instance of Cache.
     * 
     * @param  string|array  $key
     * @param  int  $seconds
     * @return mixed
     */
    if (!function_exists('cache')) {
        function cache($data = null, $seconds = 0)
        {
            if ($data === null) {
                return new Cache();
            }

            if (is_array($data)) {
                foreach ($data as $key => $value)
                    Cache::set($key, $value, $seconds);

                return true;
            } else {
                return Cache::get($data);
            }
        }
    }

    /**
     * Create an instance of Carbon.
     * @return \Carbon\Carbon
     */
    if (!function_exists('now')) {
        function now()
        {
            return new Carbon();
        }
    }

    /**
     * Create an instance of Auth.
     * 
     * @return \Powerhouse\Auth\Auth
     */
    if (!function_exists('auth')) {
        function auth()
        {
            if (isset($GLOBALS['structure']['auth']) !== false)
                return $GLOBALS['structure']['auth'];
            
            return $GLOBALS['structure']['auth'] = new Auth();
        }
    }

    /**
     * Create and instance of Encrypter.
     * 
     * @return \Powerhouse\Encryption\Encrypter
     */
    if (!function_exists('crypto')) {
        function crypto()
        {
            if (isset($GLOBALS['structure']['encrypter']))
                return $GLOBALS['structure']['encrypter'];

            return $GLOBALS['structure']['encrypter'] = new Encrypter();
        }
    }
    
    /**
     * Create an instance of Request & sub-methods.
     * 
     * @return \Powehouse\Http\Request
     */
    if (!function_exists('request')) {
        function request()
        {
            return $GLOBALS['structure']['request'];
        }
        function old($name)
        {
            return request()->old($name);
        }
        function abort(int $code, $message = null, $errors = null, $type = null)
        {
            return Response::abort($code, $message, $errors, $type);
        }
        function response()
        {
            return request()->response();
        }
    }
    
    /**
     * Get the CSRF Token.
     * 
     * @return string
     */
    if (!function_exists('csrf_token')) {
        function csrf_token()
        {
            return session()->get('_token');
        }
    }

    /**
     * Get a Disposable Token.
     * 
     * @return string
     */
    if (!function_exists('disposable_token')) {
        function disposable_token()
        {
            if (isset($GLOBALS['structure']['disposable_token']))
                return $GLOBALS['structure']['disposable_token'];

            $GLOBALS['structure']['disposable_token'] = new DisposableToken();
            return $GLOBALS['structure']['disposable_token'];
        }
    }
    
    /**
     * Encode an array to JSON.
     * 
     * @param  array  $array
     * @return string
     */
    if (!function_exists('json_message')) {
        function json_message($array)
        {
            return json_encode($array);
        }
    }

    /**
     * Strip all horizontal whitespace in the string.
     * 
     * @param  string  $string
     * @param  string  $replaceWith
     * @return string
     */
    if (!function_exists('strip_horizontal_whitespace')) {
        function strip_horizontal_whitespace($string, $replaceWith = '')
        {
            return Str::stripHorizontalWhitespace($string, $replaceWith);
        }
    }

    /**
     * Strip all whitespace in the string.
     * 
     * @param  string  $string
     * @param  string  $replaceWith
     * @return string
     */
    if (!function_exists('strip_all_whitespace')) {
        function strip_all_whitespace($string, $replaceWith = '')
        {
            return Str::stripAllWhitespace($string, $replaceWith);
        }
    }
    
    /**
     * Set headers.
     * 
     * @param  string  $name
     * @param  string  $value
     * @return bool
     */
    if (!function_exists('set_header')) {
        function set_header($name, $value, ...$args)
        {
            return Response::header($name, $value, ...$args);
        }
    }
    
    /**
     * Get headers.
     * 
     * @param  string  $name
     * @return bool|string|null
     */
    if (!function_exists('get_header')) {
        function get_header($name)
        {
            return Response::header($name);
        }
    }

    /**
     * Get the first element of an array.
     * 
     * @param  array  $array
     * @return array
     */
    if (!function_exists('head')) {
        function head($array)
        {
            return reset($array);
        }
    }
    
    /**
     * Array to object conversion.
     * 
     * @param  array  $arr
     * @param  string  $type
     * @return object
     */
    if (!function_exists('array_to_object')) {
        function array_to_object($arr, $type = 'json')
        {
            return Arr::toObject($arr, $type);
        }
    }
        
    /**
     * The view method of Route.
     * 
     * @return mixed
     */
    if (!function_exists('view')) {
        function view()
        {
            $args = func_get_args();
            return $GLOBALS['structure']['route']->view(...$args);
        }
    }
    
    /**
     * Display without routing.
     * 
     * @param  string  $view
     * @param  array  $variables
     * @return mixed
     */
    if (!function_exists('display')) {
        function display(string $view, array $variables = [])
        {
            return (new Powerhouse\View\Make())->make([], $view, $variables);
        }
    }

    /**
     * Create an instance of App.
     * 
     * @return \Powerhouse\Localization\App
     */
    if (!function_exists('app')) {
        function app()
        {
            if (isset($GLOBALS['structure']['app']))
                return $GLOBALS['structure']['app'];

            $GLOBALS['structure']['app'] = new App();
            return $GLOBALS['structure']['app'];
        }
    }

    /**
     * Create an instanc of HtmlString.
     * 
     * @param  string  $html
     * @return \Powerhouse\Support\HtmlString
     */
    if (!function_exists('HtmlString')) {
        function HtmlString($html)
        {
            return new HtmlString($html);
        }
    }

    /**
     * Encode html special characters.
     * 
     * @param  string  $value
     * @param  bool  $doubleEncode
     * @return mixed
     */
    if (!function_exists('e')) {
        function e($value, $doubleEncode = true)
        {
            if ($value instanceof HtmlString)
                return $value->toHtml();

            return htmlspecialchars($value, ENT_QUOTES, 'UTF-8', $doubleEncode);
        }
    }

    /**
     * Number of arguments in a string.
     * 
     * @param  string  $string
     * @return int
     */
    if (!function_exists('count_string_args')) {
        function count_string_args($string)
        {
            return eval("?><?php return count_string_args_backend{$string}; ?>");
        }
        function count_string_args_backend()
        {
            return func_num_args();
        }
    }

    /**
     * Get arguments of a string.
     * 
     * @param  string  $string
     * @return array
     */
    if (!function_exists('get_string_args')) {
        function get_string_args($string)
        {
            return eval("?><?php return get_string_args_backend{$string}; ?>");
        }
        function get_string_args_backend()
        {
            return func_get_args();
        }
    }

    // Separate area (Not used Yet)
    // $vars => get_defined_vars()
    if (!function_exists('separate_area')) {
        function separate_area($vars, $callback)
        {
            $callback($vars);
        }
    }

    /**
     * Translate string.
     * 
     * @param  string  $key
     * @param  int|array|null  $arg1
     * @param  int|array|null  $arg2
     * @return string|null
     */
    if (!function_exists('__')) {
        function __($key, $arg1 = null, $arg2 = null)
        {
            return App::get($key, $arg1, $arg2);
        }
    }

    /**
     * Determine whether the locale is equal to the given value.
     * 
     * @param  string  $locale
     * @return bool
     */
    if (!function_exists('isLocale')) {
        function isLocale($locale)
        {
            return App::isLocale($locale);
        }
    }

    /**
     * Create an instance of URL.
     * 
     * @return \Powerhouse\Http\URL
     */
    if (!function_exists('url')) {
        function url()
        {
            if (isset($GLOBALS['structure']['url']))
                return $GLOBALS['structure']['url'];

            $GLOBALS['structure']['url'] = new URL();
            return $GLOBALS['structure']['url'];
        }
    }

    /**
     * Get the current path.
     * 
     * @param  string  $path
     * @return string
     */
    if (!function_exists('path')) {
        function path($path = '')
        {
            return url()->path($path);
        }
    }

    /**
     * Get the full address of the named routes.
     * 
     * @param  string  $name
     * @param  array  $parameters
     * @return string
     */
    if (!function_exists('route')) {
        function route($name = true, $parameters = [])
        {
            if ($name === true)
                return $GLOBALS['structure']['route'];

            return url()->route($name, $parameters);
        }
        function relativeRoute($name, $parameters = [])
        {
            return url()->relativeRoute($name, $parameters);
        }
    }

    /**
     * Get the host address.
     * 
     * @param  string  $uri
     * @return string
     */
    if (!function_exists('host')) {
        function host($uri)
        {
            return url()->host($uri);
        }
    }

    /**
     * Create a url with a local query.
     * 
     * @param  string  $query
     * @param  bool  $renew
     * @return string
     */
    if (!function_exists('localQuery')) {
        function localQuery($query, $renew = false)
        {
            return url()->localQuery($query, $renew);
        }
    }

    /**
     * Create a remote url with a query.
     * 
     * @param  string  $url
     * @param  string  $query
     * @param  bool  $renew
     * @return string
     */
    if (!function_exists('remoteQuery')) {
        function remoteQuery($url, $query, $renew = false)
        {
            return url()->remoteQuery($url, $query, $renew);
        }
    }

    /**
     * Get the ratio of an image.
     * 
     * @param  int|float  $a
     * @param  int|float  $b
     * @return string
     */
    if (!function_exists('ratio')) {
        function ratio($a, $b)
        {
            $gcd = function($a, $b) use (&$gcd) {
                return ($a % $b) ? $gcd($b, $a % $b) : $b;
            };
            $g = $gcd($a, $b);
            
            return $a/$g . ':' . $b/$g;
        }
    }
    
    /**
     * Create an instance of Session and the sub-methods.
     * 
     * @param  string|null  $name
     * @return mixed
     */
    if (!function_exists('session')) {
        function session($name = null)
        {
            if (isset($GLOBALS['structure']['session']) === false)
                $GLOBALS['structure']['session'] = new Session();

            if ($name !== null)
                return $GLOBALS['structure']['session']->get($name);

            return $GLOBALS['structure']['session'];
        }
        function ray($name)
        {
            return session()->flashed($name);
        }
        function flash(string $name, $value = null)
        {
            return session()->flash($name, $value);
        }
    }

    /**
     * Create an instance of Cookie.
     * 
     * @return \Powerhouse\Footprint\Cookie
     */
    if (!function_exists('cookie')) {
        function cookie()
        {
            if (isset($GLOBALS['structure']['cookie']))
                return $GLOBALS['structure']['cookie'];

            $GLOBALS['structure']['cookie'] = new Cookie();
            return $GLOBALS['structure']['cookie'];
        }
    }

    /**
     * Get the config variables.
     * 
     * @param  string  $key
     * @return mixed
     */
    if (!function_exists('config')) {
        function config($key)
        {
            global $config;
            
            return array_get($config, $key, $default = null);
        }
    }

    /**
     * Create a new event.
     * 
     * @param  object  $event
     * @return void
     */
    if (!function_exists('event')) {
        function event($event)
        {
            $event = new Event($event);
            $event->injectListeners();
        }
    }