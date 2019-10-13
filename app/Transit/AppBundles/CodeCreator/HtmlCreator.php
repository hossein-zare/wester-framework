<?php

	namespace AppBundles\CodeCreator;

	class HtmlCreator
	{

		/**
		 * Get attributes as string.
		 * 
		 * @param  array  $attributes
		 * @return string
		 */
		protected function getAttributes(array $attributes)
		{
			$attrs = null;
			foreach ($attributes as $name => $value)
			{
				$attrs.= "{$name}=\"{$value}\"";
			}

			return $attrs !== null ? ' '. $attrs : null;
		}

		/**
		 * Create an element.
		 * 
		 * @param  string  $tag
		 * @param  string  $content
		 * @param  array  $attributes
		 * @return string
		 */
		public function createElem(string $tag, $content, $attributes = [])
		{
			$attributes = $this->getAttributes($attributes);
			return "<{$tag}{$attributes}>{$content}</{$tag}>";
		}

		/**
		 * Create a self-closing element.
		 * @param  string  $tag
		 * @param  array  $attributes
		 * @return string
		 */
		public function createElemSelfClosing(string $tag, $attributes = [])
		{
			$attributes = $this->getAttributes($attributes);
			return "<{$tag}{$attributes} />";
		}

		/**
		 * Print the given variable value.
		 * 
		 * @param  mixed  $output
		 * @return string
		 */
		public function output($output)
		{
			print $output;
		}

	}
