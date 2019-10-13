<?php

    namespace Powerhouse\Http;

    class Root
    {

        /**
         * Check branchOf with an array.
         * 
         * @param  string  $current
         * @param  array  $roots
         * @return bool
         */
        public function arrBranchOf(string $current, array $roots)
        {
            foreach ($roots as $root)
                if ($this->branchOf($current, $root) === true)
                    return true;

            return false;
        }

        /**
         * Determine whether the current string is a branch of the root.
         * 
         * @param  string  $current
         * @param  string  $root
         * @return bool
         */
        public function branchOf(string $current, string $root)
        {
            $current = trim($current, '/');
            $root = trim($root, '/');

            $star = strpos($root, '*');
            if ($star === 0)
                return true;

            if ($star !== false) {
                if (substr_compare($current, $root, 0, $star) === 0)
                    return true;
                return false;
            } else {
                if ($current === $root)
                    return true;
                return false;
            }

            return false;
        }

    }