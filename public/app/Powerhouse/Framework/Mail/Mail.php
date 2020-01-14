<?php

    namespace Powerhouse\Mail;

    use Exception;

    class Mail
    {
        
        /**
         * The sender's email address.
         * 
         * @var string
         */
        protected $senderAddress = '';

        /**
         * The sender's name.
         * 
         * @var string|null
         */
        protected $senderName;

        /**
         * The receiver's email address.
         * 
         * @var string
         */
        protected $receiverAddress = '';

        /**
         * The subject of the email.
         * 
         * @var string
         */
        protected $subject = 'No subject';

        /**
         * The view name.
         * 
         * @var string|null
         */
        protected $viewName;

        /**
         * The view arguments.
         * 
         * @var array
         */
        protected $arguments = [];

        /**
         * The plain message.
         * 
         * @var string
         */
        protected $plainMessage = '';

        /**
         * Mail type.
         * 
         * @var string
         */
        protected $mailType = 'plain';

        /**
         * Initialize the mail sender.
         * 
         * @return void
         */
        public function __construct()
        {
            global $config_mail;

            $this->senderAddress = $config_mail['from']['address'];
            $this->senderName = $config_mail['from']['name'];
        }

        /**
         * Set the sender information.
         * 
         * @param  string  $address
         * @param  string|null  $name
         * @return $this
         */
        public function from($address, $name = null)
        {
            $this->senderAddress = $address;
            $this->senderName = $name;

            return $this;
        }

        /**
         * Set the receiver information.
         * 
         * @param  string  $address
         * @return $this
         */
        public function to($address)
        {
            $this->receiverAddress = $address;
            return $this;
        }

        /**
         * Set the subject.
         * 
         * @param  string  $subject
         * @return $this
         */
        public function subject($subject)
        {
            $this->subject = $subject;
            return $this;
        }

        /**
         * Set the view.
         * 
         * @param  string  $view
         * @return $this
         */
        public function view($name)
        {
            $this->type(false); // HTML
            $this->viewName = $name;
            return $this;
        }

        /**
         * Set the arguments.
         * 
         * @param  array  $args
         * @return this
         */
        public function with($args, $value = null)
        {
            if ($value === null)
                $this->arguments = $args;
            else
                $this->arguments = [
                    $args => $value
                ];

            return $this;
        }

        /**
         * Configure the type.
         * 
         * @param  bool  $plain
         * @param $this
         */
        protected function type($plain = false)
        {
            $this->mailType = $plain ? 'plain' : 'html';
            return $this;
        }

        /**
         * Set the plain message.
         * 
         * @param  string  $message
         * @return $this
         */
        public function plain($message)
        {
            $this->plainMessage = $message;
            return $this;
        }

        /**
         * Build the template.
         * 
         * @return string
         */
        protected function buildTemplate()
        {
            if ($this->mailType === 'html') {
                $this->arguments['subject'] = $this->subject;
                return (new \Powerhouse\View\Make())->make([], $this->viewName, $this->arguments);
            } else
                return $this->plainMessage;
        }

        /**
         * Get headers.
         * 
         * @return string
         */
        protected function getHeaders()
        {
            $headers = '';
            if ($this->mailType === 'html') {
                $headers .= "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            }
            
            // More headers
            $headers .= 'From: ' . $this->senderName . ' <' . $this->senderAddress . '>' . "\r\n";

            return $headers;
        }

        /**
         * Send the mail.
         * 
         * @return bool
         */
        public function send()
        {
            $message = $this->buildTemplate();
            $headers = $this->getHeaders();
            return mail($this->receiverAddress, $this->subject, $message, $headers);
        }

    }
