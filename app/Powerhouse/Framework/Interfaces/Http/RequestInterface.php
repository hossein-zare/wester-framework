<?php

    namespace Powerhouse\Interfaces\Http;

    interface RequestInterface
    {

        public function process();
        public function authorize();
        public function messages();
        public function attributes();
        public function rules();

    }
