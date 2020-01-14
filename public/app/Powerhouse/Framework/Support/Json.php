<?php

    namespace Powerhouse\Support;

    use Exception;

    class Json
    {

        /**
         * Get the last error occured.
         * 
         * @return string|bool
         */
        public static function lastError()
        {
            switch (json_last_error()) {
                case JSON_ERROR_DEPTH:
                    return 'The maximum stack depth has been exceeded';
                break;
                case JSON_ERROR_STATE_MISMATCH:
                    return 'Invalid or malformed JSON';
                break;
                case JSON_ERROR_CTRL_CHAR:
                    return 'Control character error, possibly incorrectly encoded';
                break;
                case JSON_ERROR_SYNTAX:
                    return 'Syntax error';
                break;
                case JSON_ERROR_UTF8:
                    return 'Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            }

            return false;
        }

    }
