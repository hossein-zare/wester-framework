<?php

    namespace Powerhouse\Console;

    use Powerhouse\Castles\DB;
    use Powerhouse\Console\Console;

    abstract class Commands
    {

        /**
         * The Path of Samples.
         * 
         * @var string
         */
        protected $samples = ".\\public\\app\\Powerhouse\\Framework\\Console\\Samples\\";

        /**
         * The list of commands.
         * 
         * @param  array  $argv
         * @return void
         */
        protected function commands(array $argv)
        {
            $command = $this->toMethod($argv[1]);
            $arguments = array_slice($argv, 2);

            $this->$command(...$arguments);
        }

        /**
         * Create a new console command.
         * 
         * @param  string  $path
         * @param  string  $name
         * @param  string  $error
         * @param  string  $succss
         * @return void
         */
        protected function newCommand($path, $name, $sample, $error, $success)
        {
            $this->isString($name);

            if (strpos($name, " ") !== false || preg_match("/[^A-Za-z]/", $name) > 0) {
                $this->shutdown("Please enter a valid string without any white spaces and non-english letters!", 'red');
            }

            $name = ucfirst($name);

            $path = $path . $name . '.php';
            if (file_exists($path) === true) {
                $this->shutdown($error, 'red');
            }

            $sample = file_get_contents($this->samples . $sample);
            $sample = str_replace('{:name}', $name, $sample);

            // Create the file
            $this->fileStream($path, 'w+', function ($file) use ($sample) {
                fwrite($file, $sample);
            });

            $this->shutdown($success, 'green');
        }

        /**
         * Create a controller.
         * 
         * @param  string  $name
         * @return void
         */
        protected function createController($name)
        {
            $path = ".\\public\\app\\Transit\\Http\\Controllers\\";
            $sample = 'Controller.sam';
            $error = "A controller has already been created with this name!\n\tPlease specify another name.";
            $success = "Controller created successfully!";

            $this->newCommand($path, $name, $sample, $error, $success);
        }

        /**
         * Create a model.
         * 
         * @param  string  $name
         * @return void
         */
        protected function createModel($name)
        {
            $path = ".\\public\\app\\Transit\\Models\\";
            $sample = 'Model.sam';
            $error = "A model has already been created with this name!\n\tPlease specify another name.";
            $success = "Model created successfully!";

            $this->newCommand($path, $name, $sample, $error, $success);
        }

        /**
         * Create a middleware.
         * 
         * @param  string  $name
         * @return void
         */
        protected function createMiddleware($name)
        {
            $path = ".\\public\\app\\Transit\\Http\\Middleware\\";
            $sample = 'Middleware.sam';
            $error = "A middleware has already been created with this name!\n\tPlease specify another name.";
            $success = "Middleware created successfully!";

            $this->newCommand($path, $name, $sample, $error, $success);
        }

        /**
         * Create a request.
         * 
         * @param  string  $name
         * @return void
         */
        protected function createRequest($name)
        {
            $path = ".\\public\\app\\Transit\\Http\\Requests\\";
            $sample = 'Request.sam';
            $error = "A request has already been created with this name!\n\tPlease specify another name.";
            $success = "Request created successfully!";

            $this->newCommand($path, $name, $sample, $error, $success);
        }

        /**
         * Create a mail.
         * 
         * @param  string  $name
         * @return void
         */
        protected function createMail($name)
        {
            $path = ".\\public\\app\\Transit\\Mails\\";
            $sample = 'Mail.sam';
            $error = "A mail has already been created with this name!\n\tPlease specify another name.";
            $success = "Mail created successfully!";

            $this->newCommand($path, $name, $sample, $error, $success);
        }

        /**
         * Create a service provider.
         * 
         * @param  string  $name
         * @return void
         */
        protected function createServiceProvider($name)
        {
            $path = ".\\public\\app\\Transit\\Providers\\Services\\";
            $sample = 'ServiceProvider.sam';
            $error = "A Service Provider has already been created with this name!\n\tPlease specify another name.";
            $success = "Service Provider created successfully!";

            // Add suffix
            $name.= 'ServiceProvider';

            $this->newCommand($path, $name, $sample, $error, $success);
        }

        /**
         * Create an event.
         * 
         * @param  string  $name
         * @return void
         */
        protected function createEvent($name)
        {
            $path = ".\\public\\app\\Transit\\Events\\";
            $sample = 'Event.sam';
            $error = "An event has already been created with this name!\n\tPlease specify another name.";
            $success = "Event created successfully!";

            $this->newCommand($path, $name, $sample, $error, $success);
        }

        /**
         * Create an listener.
         * 
         * @param  string  $name
         * @return void
         */
        protected function createListener($name)
        {
            $path = ".\\public\\app\\Transit\\Listeners\\";
            $sample = 'Listener.sam';
            $error = "A listener has already been created with this name!\n\tPlease specify another name.";
            $success = "Listener created successfully!";

            $this->newCommand($path, $name, $sample, $error, $success);
        }

        /**
         * Delete cached views.
         * 
         * @param  string  $name
         * @return void
         */
        protected function deleteCaches($name)
        {
            if ($name === 'views' || $name === 'all') {
                $files = glob(".\\public\\app\\Storage\\Views\\*");
                foreach($files as $file){
                    if(is_file($file))
                        unlink($file);
                }
            }

            if ($name === 'routes' || $name === 'all') {
                if (file_exists(".\\public\\app\\Storage\\Routes\\cache.json"))
                    unlink(".\\public\\app\\Storage\\Routes\\cache.json");
            }

            if ($name !== 'views' && $name !== 'routes' && $name !== 'all') {
                $this->shutdown("You can only delete cached views & routes or all!", 'red');
            }

            $this->shutdown("The cached files were successfully deleted!", 'green');
        }

        /**
         * Create an authentication system.
         * 
         * @return void
         */
        protected function createAuth()
        {
            $this->importAuthDB();

            $files = [
                // Controllers
                [
                    'source' => "Auth\\Controllers\\Auth\\ChangePassword.php",
                    'destination' => ".\\public\\app\\Transit\\Http\\Controllers\\Auth\\"
                ],
                [
                    'source' => "Auth\\Controllers\\Auth\\Login.php",
                    'destination' => ".\\public\\app\\Transit\\Http\\Controllers\\Auth\\"
                ],
                [
                    'source' => "Auth\\Controllers\\Auth\\Logout.php",
                    'destination' => ".\\public\\app\\Transit\\Http\\Controllers\\Auth\\"
                ],
                [
                    'source' => "Auth\\Controllers\\Auth\\Register.php",
                    'destination' => ".\\public\\app\\Transit\\Http\\Controllers\\Auth\\"
                ],
                [
                    'source' => "Auth\\Controllers\\Auth\\Reset.php",
                    'destination' => ".\\public\\app\\Transit\\Http\\Controllers\\Auth\\"
                ],
                [
                    'source' => "Auth\\Controllers\\Auth\\Verify.php",
                    'destination' => ".\\public\\app\\Transit\\Http\\Controllers\\Auth\\"
                ],

                // Models
                [
                    'source' => "Auth\\Models\\AuthReset.php",
                    'destination' => ".\\public\\app\\Transit\\Models\\"
                ],
                [
                    'source' => "Auth\\Models\\AuthVerification.php",
                    'destination' => ".\\public\\app\\Transit\\Models\\"
                ],
                [
                    'source' => "Auth\\Models\\AuthSession.php",
                    'destination' => ".\\public\\app\\Transit\\Models\\"
                ],
                [
                    'source' => "Auth\\Models\\User.php",
                    'destination' => ".\\public\\app\\Transit\\Models\\"
                ],

                // Providers
                [
                    'source' => "Auth\\Providers\\AuthServiceProvider.php",
                    'destination' => ".\\public\\app\\Transit\\Providers\\Services\\",
                    'overwrite' => true
                ],

                // Views : auth
                [
                    'source' => "Auth\\Views\\auth\\change-password.spark.php",
                    'destination' => ".\\public\\app\\Resources\\Views\\auth\\"
                ],
                [
                    'source' => "Auth\\Views\\auth\\login.spark.php",
                    'destination' => ".\\public\\app\\Resources\\Views\\auth\\"
                ],
                [
                    'source' => "Auth\\Views\\auth\\register.spark.php",
                    'destination' => ".\\public\\app\\Resources\\Views\\auth\\"
                ],
                [
                    'source' => "Auth\\Views\\auth\\reset.spark.php",
                    'destination' => ".\\public\\app\\Resources\\Views\\auth\\"
                ],

                // Views : emails
                [
                    'source' => "Auth\\Views\\emails\\auth\\reset.spark.php",
                    'destination' => ".\\public\\app\\Resources\\Views\\emails\\auth\\"
                ],
                [
                    'source' => "Auth\\Views\\emails\\auth\\verification.spark.php",
                    'destination' => ".\\public\\app\\Resources\\Views\\emails\\auth\\"
                ]
            ];

            foreach ($files as $file) {
                if (!is_dir($file['destination'])) {
                    mkdir($file['destination']);
                }

                $filename = $file['destination'] . basename($file['source']);
                if (!file_exists($filename) || (isset($file['overwrite']) && $file['overwrite'] === true)) {
                    copy($this->samples . $file['source'], $file['destination'] . basename($file['source']));
                }
            }

            $this->shutdown("The authentication system has been imported to your application!", 'green');
        }

        /**
         * Import the auth database.
         * 
         * @return void
         */
        protected function importAuthDB()
        {
            $tables = file_get_contents($this->samples . "Auth\\Tables\\table.sql");

            DB::exec($tables);
        }

        /**
         * Create key.
         */
        protected function createKey()
        {
            $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@#%&*?!^()';
            $key = substr(str_shuffle($permitted_chars), 0, 16);

            $file = ".\\public\\app\\Config\\Web.php";
            $data = file_get_contents($file);
            $data = preg_replace_callback('/\$config\[(\'|")key\1\](.+);/', function ($match) use ($key) {
                return '$config[\'key\'] = \''. $key .'\';';
            }, $data);

            file_put_contents($file, $data);
            
            $this->shutdown("The application key has been generated!", 'green');
        }

        /**
         * Run server.
         */
        protected function runServer($port = 8000)
        {
            $this->publish(Console::green(" Server started: http://localhost:{$port} ", 'light_gray', 'reverse'));
            $this->publish(Console::dim(' Version: 1.0', 'blink'));
            $this->publish(Console::dim(' Docs:    https://framework.wester.ir/docs/1.0', 'blink'));
            $this->publish(Console::dim(' Issues:  https://github.com/hossein-zare/wester/issues', 'blink'). "\n");
            $this->publish(Console::blue(' Note: ', 'light_gray', 'reverse'));
            $this->publish("This web server was designed to aid application development.", 'warning');
            $this->publish("It is not intended to be a full-featured web server.", 'warning');
            $this->publish("It should not be used on a public network.\n", 'warning');
            
            passthru("php -S 0.0.0.0:{$port} -t public");
        }
    }
