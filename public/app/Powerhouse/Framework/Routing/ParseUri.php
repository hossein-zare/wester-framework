<?php

	namespace Powerhouse\Routing;

    use stdClass;

	abstract class ParseUri extends Callback
	{

		/**
         * Handle the uri to get the parameters.
         *
         * @param  string  $uri
         * @param  string  $request_uri
         * @return bool
         */
        protected function parseUri($uri, $request_uri)
        {
            $current_uri = $this->prepareUri($uri);

            $storage = new stdClass();
            $storage->optional_parameters = $this->optionalParametersNum($current_uri);
            $storage->optional_parameters_passed = 0;
            $storage->uri_num = count($current_uri);
            $storage->request_uri_num = count($request_uri);
            $storage->broken = false;

            for ($i = 0; $i < $storage->uri_num; $i++) {
                // Stop when one of the taken names has been used in the first element of the current uri.
                if ($i==0 && $this->takenUri($current_uri[0]) === true && $current_uri[0] !== 'api') {
                    $storage->broken = true;
                    break;
                }

                // Stop when the first element of the current uri is an optional parameter.
                if ($i==0 && preg_match($this->optionalParamaterRegex, $current_uri[0])) {
                    $storage->broken = true;
                    break;
                }

                // Break the loop when both the current uri and request url parameters aren't matching eachother.
                if (isset($request_uri[$i]))
                    // Both with or without [?]
                    if (! preg_match("/{(.*?)}/", $current_uri[$i]) && $current_uri[$i] !== $request_uri[$i]) {
                        $storage->broken = true;
                        break;
                    }
                
                // Break when required parameters aren't passed.
                if (preg_match($this->parameterRegex, $current_uri[$i]) && !isset($request_uri[$i])) {
                    $storage->broken = true;
                    break;
                }

                // Count passed optional parameters.
                if (preg_match($this->optionalParamaterRegex, $current_uri[$i]) === 1 && isset($request_uri[$i]))
                    $storage->optional_parameters_passed++;

                // Where statement
                if (count(static::$routes[$uri]['wheres']) > 0) {
                    if (preg_match("/{([a-z_]+)[?]*}/", $current_uri[$i], $matches)) {
                        if (isset(static::$routes[$uri]['wheres'][$matches[1]])) {

                            // Regex validation
                            $condition = static::$routes[$uri]['wheres'][$matches[1]];
                            if (!preg_match("/^{$condition}$/", $request_uri[$i])) {
                                $storage->broken = true;
                                break;
                            }

                            // Encoded forward slashes
                            if ($condition === '.*') {
                                $statement = implode('/', array_slice($request_uri, $i));
                                preg_match("/^{$condition}$/", $statement, $matches);
                                static::$routes[$uri]['parameters'][] = $statement;
                                break;
                            }

                        }
                    }
                }

                // Check optional parameters.
                if ($i === $storage->uri_num - 1) {
                    $optCondition = $storage->uri_num !== $storage->request_uri_num;
                    if ($optCondition && $storage->uri_num - ($storage->optional_parameters - $storage->optional_parameters_passed) !== $storage->request_uri_num) {
                        $storage->broken = true;
                        break;
                    }
                }

                // Save the provided parameters as arguments.
                if (preg_match("/{(.*?)}/", $current_uri[$i], $matches) && isset($request_uri[$i]))
                    static::$routes[$uri]['parameters'][] = $request_uri[$i];
            }

            if ($storage->broken === true)
                static::$routes[$uri]['parameters'] = [];

            return ! ($storage->broken);
        }

	}
