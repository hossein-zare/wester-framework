<?php

    namespace Powerhouse\Http;

    use Powerhouse\Castles\URL;
    use App\Transit\Http\Handler\Request;
    use Powerhouse\Http\Validation\Validator;
    use Powerhouse\Interfaces\Http\RequestInterface;

    abstract class FormRequest extends Request implements RequestInterface
    {

        /**
         * Run the applied request on the route.
         * 
         * @return bool
         */
        public function process()
        {
            if ($this->authorize() === false)
                return abort(406);

            return $this->formValidation();
        }

        /**
         * Validate the form requests.
         * 
         * @return mixed
         */
        protected function formValidation()
        {
            $validation = $this->rules();
            if (count($validation) > 0) {
                $method = $validation['method'];
                $rules = $validation['rules'];

                $flash = ! ($this->ajax() || $this->httpVersion('1.1') || URL::routingType() === 'api');
                $messages = $this->languageArray();
                Validator::make($method, $rules, $messages, $flash);

                if (count($this->getErrors()) > 0)
                    return abort(406, 'Validation failed!', $this->getErrors(), 'validation');
            }

            return true;
        }

        /**
         * {@inheritdoc}
         * @return  array
         */
        public function rules()
        {
            return [];
        }

        /**
         * Get the custom language array.
         * 
         * @return array
         */
        protected function languageArray()
        {
            return array_merge($this->messages(), ['attributes' => $this->attributes()]);
        }

        /**
         * {@inheritdoc}
         * @return array
         */
        public function messages()
        {
            return [];
        }

        /**
         * {@inheritdoc}
         * @return array
         */
        public function attributes()
        {
            return [];
        }

    }