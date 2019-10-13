<?php

    namespace Powerhouse\Support;
    
    use Packages\Laravel\Support\Str as StrLaravel;

    class Str extends StrLaravel
    {

        /**
         * Strip all horizontal whitespace in the string.
         *
         * @param  string  $string
         * @param  string  $replaceWith
         * @return string
         */
        public static function stripHorizontalWhitespace($string, $replaceWith = '')
        {
            $string = trim($string);
            return preg_replace('/\h+/', $replaceWith, $string);
        }

        /**
         * Strip all whitespace in the string.
         *
         * @param  string  $string
         * @param  string  $replaceWith
         * @return string
         */
        public static function stripAllWhitespace($string, $replaceWith = '')
        {
            $string = trim($string);
            return preg_replace('/\s+/', $replaceWith, $string);
        }

        /**
         * Wrap string.
         * 
         * @param  mixed  $string
         * @return string
         */
        public static function wrap($string)
        {
            if (is_array($string)) {
                return array_map(function ($item) {
                    return self::wrap($item);
                }, $string);
            }
            elseif (preg_match('/(.*?)\(([^`]+)\)/', $string) !== 0) {
                return self::wrap(preg_replace_callback('/(.*?)\(([^`]+)\)/', function ($matches) {
                    $parts = explode(' ', $matches[2]);
                    $parent = $parts[0];
                    array_splice($parts, 0, 1);
                    $rest = count($parts) ? ' '. implode(' ', $parts) : null;

                    return sprintf("%s(%s)", $matches[1], self::wrap($parent) . $rest);
                }, $string));
            }
            elseif (stripos($string, ' as ') !== false) {
                $string = str_replace(' AS ', ' as ', $string);
                $section = explode(' as ', $string);

                return self::wrap($section[0]) . ' as ' . self::wrap($section[1]);
            }
            elseif (strpos($string, '.') !== false && $func = preg_match('/(.*?)\((.*?)\)/', $string) === 0) {
                $section = explode('.', $string);

                return self::wrap($section[0]) . '.' . self::wrap($section[1]);
            } elseif (preg_match('/(.*?)\((.*?)\)/', $string) === 0) {
                return $string !== '*' ? sprintf("`%s`", $string) : $string;
            } else {
                return $string;
            }
        }

    }
