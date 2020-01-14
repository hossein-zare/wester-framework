<?php

    namespace Powerhouse\Foundation\Auth\Listeners;

    use Powerhouse\Castles\Mail;

    class SendVerificationMail
    {

        /**
        * Handle the event.
        *
        * @param  object  $event
        * @return void
        */
        public function handle($event)
        {
            // Send the verification mail
            if ($event->verificationRequired === true) {
                Mail::to($event->email)->view('emails/auth/verification')->with('serial', $event->serial)->send();
            }
        }

    }
