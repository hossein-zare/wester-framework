<?php

    namespace Powerhouse\Foundation\Auth\Listeners;

    use Powerhouse\Castles\Mail;

    class SendPasswordVerificationMail
    {

        /**
        * Handle the event.
        *
        * @param  object  $event
        * @return void
        */
        public function handle($event)
        {
            Mail::to($event->email)->view('emails/auth/reset')->with('serial', $event->serial)->send();
        }

    }
