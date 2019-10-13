<?php

	namespace Powerhouse\Localization;

	abstract class DynamicText
	{

		/**
		 * Replace variable tags with the array of values.
		 * 
		 * @param  string $translation
		 * @param  array  $array
		 * @return string
		 */
		protected function replaceTags($translation, array $array = [])
		{
			if ($translation === null || count($array) === 0)
				return $translation;

			$translation = preg_replace_callback('/{:(.*?)}/', function ($matches) use ($array) {

				return $array[$matches[1]] ?? $matches[0];

			}, $translation);


			return $translation;
		}

		/**
		 * Choose the matching sentence or word.
		 * 
		 * @param  string  $translation
		 * @param  string  $selection
		 * @return string|null
		 */
		protected function chooseSentence($translation, $selection = null)
		{
			if ($translation === null || $selection === null)
				return $translation;

			$strings = explode('|', $translation);

			foreach ($strings as $string) {
				preg_match('/^(?|{(.*?)}|\[([0-9]+),([0-9]+|[*])\])/', $string, $matches);
				if (count($matches) === 2)
					if ($matches[1] == $selection)
						return $this->pluckRange($matches[0], $string);

				if (count($matches) === 3) {
					$first = $matches[1];
					$second = $matches[2];

					if ($second === '*') {
						if ($first <= $selection)
							return $this->pluckRange($matches[0], $string);
						else
							return null;
					} else {
						if ($first <= $selection && $second >= $selection)
							return $this->pluckRange($matches[0], $string);
					}

				}
			}

			return null;
		}

		/**
		 * Get rid of the range in the string.
		 * 
		 * @param  string  $range
		 * @param  string  $string
		 * @return string
		 */
		protected function pluckRange($range, $string)
		{
			return trim(str_replace($range, '', $string));
		}

	}
