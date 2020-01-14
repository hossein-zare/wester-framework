<?php

    namespace Powerhouse\View;

    use Powerhouse\Castles\View;
    use Powerhouse\Castles\Storage;
    use Powerhouse\Footprint\Flash;

    class Engine extends CompileFile
    {

        /**
         * Get view contents.
         * 
         * @return string
         */
        protected function renderContents()
        {
            $view = $this->getViewPath();
            $view = $this->compile($this->getContents($view));
            $view = $this->cache($view);

            // Composer
            View::compose($this->getName());

            $contents = $this->evaluateView($view);
            return $contents;
        }

        /**
         * Cache view files.
         * 
         * @param  string  $content
         * @return string
         */
        protected function cache($content)
        {
            global $config;

            $name = md5($this->getName() . $this->getSuffix());
            $cachePath = './app/Storage/Views/';

            $file = Storage::explore('app')->setDir('Storage')->setDir('Views');

            if ($file->exists($name) === false || $config['debug'] === true) {
                $file->put($name, $content);
            }

            return $cachePath . $name;
        }

        /**
         * Evaluate view.
         * 
         * @param  string  $cachedView
         * @return string
         */
        protected function evaluateView($cachedView)
        {
            ob_start();

            extract($this->arguments, EXTR_SKIP);

            // Get the flashed messages
            $errors = Flash::get('errors');

            // HTTP Request
            $request = request();

            include $cachedView;

            return trim(ob_get_clean());
        }

    }
