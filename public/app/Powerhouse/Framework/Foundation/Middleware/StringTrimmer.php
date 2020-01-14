<?php

    namespace Powerhouse\Foundation\Middleware;

    abstract class StringTrimmer
    {

        /**
         * Handle requests.
         * 
         * @param  object  $request
         * @return bool
         */
        final public function handle($request){
            $dataset = $request->all();

            $dataset['get'] = array_value($dataset['get'], function($key, $value){
                return (in_array($key, $this->except) === false) ? trim($value) : $value;
            });

            $dataset['post'] = array_value($dataset['post'], function($key, $value){
                return (in_array($key, $this->except) === false) ? trim($value) : $value;
            });

            $dataset['input'] = array_value($dataset['input'], function($key, $value){
                return (in_array($key, $this->except) === false) ? trim($value) : $value;
            });

            $request->changeDataset($dataset);
            return true;
        }

    }
