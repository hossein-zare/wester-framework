<?php

    namespace Powerhouse\Foundation\Middleware;

    abstract class EmptyStringNuller
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
                return (empty($value) && $value !== '0') ? null : $value;
            });

            $dataset['post'] = array_value($dataset['post'], function($key, $value){
                return (empty($value) && $value !== '0') ? null : $value;
            });

            $dataset['input'] = array_value($dataset['input'], function($key, $value){
                return (empty($value) && $value !== '0') ? null : $value;
            });

            $request->changeDataset($dataset);
            return true;
        }

    }
