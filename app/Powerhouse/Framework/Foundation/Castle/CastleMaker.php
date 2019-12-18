<?php

    namespace Powerhouse\Foundation\Castle;

    use Wester;
    use Exception;
    use Powerhouse\Storage\FileSystem;
    use Powerhouse\Castles\Storage;

    abstract class CastleMaker
    {

        /**
         * Build the castle class.
         * 
         * @param  string  $class
         * @return void
         */
        public static function build($class)
        {
            $name = self::createName($class);
            $file = Storage::explore('app')->setDir('Storage')->setDir('Castles');

            if (self::castleExists($file, $name) === false)
                self::createCastle($file, $name, $class);

            self::includeCastle($name);
        }

        /**
         * Create an encrypted name.
         * 
         * @param  string  $class
         * @return string
         */
        protected static function createName($class)
        {
            return md5($class);
        }

        /**
         * Create a new castle class.
         * 
         * @param  FileSystem  $file
         * @param  string  $name
         * @return FileSystem
         */
        protected static function createCastle(FileSystem $file, $name, $class)
        {
            if (file_exists(Wester::getClassPath(self::getAncestorFullName($class))) === false)
                throw new Exception("Castle class '<b>{$class}</b>' doesn't exist!");

            $content = self::getContent($class);
            return $file->put($name, $content);
        }

        /**
         * Get the content of the new castle.
         * 
         * @param  string  $name
         * @param  string  $class
         * @return string
         */
        protected static function getContent($class)
        {
            $name = self::getName($class);
            $namespace = self::getNamespace($class);

            $content = file_get_contents('./app/Powerhouse/Framework/Foundation/Castle/Raw/Castle.raw');
            $content = self::replace('namespace', $namespace, $content);
            $content = self::replace('name', $name, $content);
            $content = self::replace('ancestor', self::getAncestorFullName($class), $content);

            return $content;
        }

        /**
         * Replace strings.
         * 
         * @param  string  $old
         * @param  string  $new
         * @param  string  $string
         * @return string
         */
        protected static function replace($old, $new, $string)
        {
            return str_replace('{'.$old.'}', $new, $string);
        }

        /**
         * Get the full name of the ancestor.
         * 
         * @param  string  $class
         * @return string
         */
        protected static function getAncestorFullName($class)
        {
            return str_replace('Castle\\', '', $class);
        }

        /**
         * Get the name of the component.
         * 
         * @param  string  $class
         * @return  string
         */
        protected static function getName($class)
        {
            return basename($class);
        }

        /**
         * Get the namespace of the component.
         * 
         * @param  string  $class
         * @return string
         */
        protected static function getNamespace($class)
        {
            $name = self::getName($class);
            return str_replace("\\{$name}", "", $class);
        }

        /**
         * Determine whether the castle exists.
         * 
         * @param  FileSystem  $file
         * @param  string  $name
         * @return bool
         */
        protected static function castleExists(FileSystem $filesystem, $name)
        {
            return $filesystem->exists($name);
        }

        /**
         * Include the castle.
         * 
         * @param  string  $name
         * @return void
         */
        protected static function includeCastle($name)
        {
            require_once('app\\Storage\\Castles\\' . $name);
        }

    }
