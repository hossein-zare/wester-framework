<?php

    namespace Powerhouse\Event;

    use App\Transit\Providers\Services\EventServiceProvider;
    use Powerhouse\Interfaces\Event\EventInterface;

    class Event extends EventServiceProvider
    {

        /**
         * The event instance.
         * 
         * @var object
         */
        protected $event;

        /**
         * Create a new event instance.
         * 
         * @param  object  $event
         */
        public function __construct($event)
        {
            $this->event = $event;
        }

        /**
         * Run the listeners of the event.
         * 
         * @return void
         */
        public function injectListeners()
        {
            // Get the listeners of the event
            $name = get_class($this->event);
            $listeners = $this->listen[$name];
            
            // Run and set the listeners
            $this->event->listeners($listeners);
        }

    }
