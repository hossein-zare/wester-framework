<?php

    namespace Powerhouse\Services;

    use Exception;
    use Powerhouse\Interfaces\ServiceProvider\ServiceProviderInterface;
    use App\Transit\Providers\ServiceContainer;

    class ServiceProvider extends ServiceContainer
    {
    
        /**
         * The name of the group.
         *
         * @var string|null
         */
        private $name = null;
    
        /**
         * Configure the service provider.
         *
         * @param  string  $name
         * @return void
         */
        public function __construct($name = null)
        {
            if (!empty($name) && $name !== null && is_string($name))
                $this->name = $name;
            else
                $this->name = null;
        }
    
        /**
         * Provide services.
         *
         * @return void
         */
        public function provide()
        {
            if (isset($this->groups[$this->name])) {
                $services = $this->groups[$this->name];
                
                foreach ($services as $service) {
                    $obj = new $service();

                    if (!($obj instanceof ServiceProviderInterface))
                        throw new Exception("The middlware isn't valid!");

                    $obj->register();
                    $obj->boot();
                }
            } else
                throw new Exception("Service provider group <b>'{$this->name}'</b> doesn't exist!");
        }

        /**
         * Built-in opening service provider.
         * 
         * @return void
         */
        protected function builtinServiceProvider()
        {
            global $config;

            // Check the application key length
            if (strlen($config['key']) < 16)
                throw new Exception("Please specify a more secure application key that must be at least 16 characters long!");
        }
        
        /**
         * Provide opening service providers.
         *
         * @return void
         */
        public function opening()
        {
            $this->builtinServiceProvider();

            $this->name = null;
            if (isset($this->opening)) {
                $services = $this->opening;
                
                foreach ($services as $service) {
                    $obj = new $service();

                    if (!($obj instanceof ServiceProviderInterface))
                        throw new Exception("The middlware isn't valid!");

                    $obj->register();
                    $obj->boot();
                }
            } else {
                throw new Exception("We can't find the opening service providers!");
            }
        }
        
        /**
         * Provide closing service providers.
         *
         * @return void
         */
        public function closing()
        {
            $this->name = null;
            if (isset($this->closing)) {
                $services = $this->closing;
                
                foreach ($services as $service) {
                    $obj = new $service();

                    if (!($obj instanceof ServiceProviderInterface))
                        throw new Exception("The middlware isn't valid!");

                    $obj->register();
                    $obj->boot();
                }
            } else
                throw new Exception("We can't find the closing service providers!");
        }
    
    }
