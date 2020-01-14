<?php

	namespace Powerhouse\Support;

	use Countable;
	use JsonSerializable;
	use ArrayIterator;
	use IteratorAggregate;
	use ArrayAccess;

	class Collection implements Countable, JsonSerializable, IteratorAggregate, ArrayAccess
	{

		/**
		 * The list of items.
		 * 
		 * @var array
		 */
		protected $items = [];

		/**
		 * Create a new instance
		 * 
		 * @param  array  $items
		 */
		public function __construct($items = [])
		{
			$this->items = $items;
		}

		/**
		 * Count the items.
		 * 
		 * @return int
		 */
		public function count()
		{
            if ($this->items === false)
                return 0;
            
            if (isset($this->items[0]))
                return count($this->items);

            return count($this->items) > 0 ? 1 : 0;
		}

		/**
		 * Get the sum of the array of numbers.
		 * 
		 * @return float
		 */
		public function sum()
		{
			return array_sum($this->items);
		}

		/**
		 * Get the difference of the array of numbers.
		 * 
		 * @return float
		 */
		public function difference()
		{
			return array_reduce($this->items, function ($a, $b) {
				if ($a === null)
					return $b;

				return $a - $b;
			}, null);
		}

		/**
		 * An alias of the difference method.
		 * 
		 * @return float
		 */
		public function diff()
		{
			return $this->difference();
		}

		/**
		 * Get the average of the array of number.
		 * 
		 * @return float
		 */
		public function avg()
		{
			return array_sum($this->items) / $this->count();
		}

		/**
		 * Get the maximum number of the array.
		 * 
		 * @return float
		 */
		public function max()
		{
			return max($this->items);
		}

		/**
		 * Get the minimum number of the array.
		 * 
		 * @return float
		 */
		public function min()
		{
			return min($this->items);
		}

		/**
		 * Divide the numbers.
		 * 
		 * @return float
		 */
		public function divide()
		{
			return array_reduce(array_filter($this->items), function ($a, $b) {
				if ($a === null)
					return $b;

				return $a / $b;
			}, null);
		}

		/**
		 * Multiplicate the numbers.
		 * 
		 * @return float
		 */
		public function multiplicate()
		{
			return array_reduce($this->items, function ($a, $b) {
				if ($a === null)
					return $b;

				return $a * $b;
			}, null);
		}

		/**
		 * Sort items by frequency.
		 * 
		 * @return array
		 */
		protected function sortByFrequency()
		{
			$items = arsort(array_count_values($this->items));
			return array_keys($items);
		}

		/**
		 * Get the nth most common item.
		 * 
		 * @param  int  $n
		 * @return float
		 */
		public function frequest($n = 1)
		{
			return $this->sortByFrequency()[$n - 1];
		}

		/**
		 * Get the nth least common item.
		 * 
		 * @param  int  $n
		 * @return float
		 */
		public function rare($n = 1)
		{
			return array_reverse($this->sortByFrequency())[$n - 1];
		}

		/**
		 * Map the items.
		 * 
		 * @param  callable  $callback
		 * @return $this
		 */
		public function map($callback)
		{
			$this->items = array_map($callback, $this->items);
			return $this;
		}

		/**
		 * Reject items.
		 * 
		 * @param  callable  $callback
		 * @return $this
		 */
		public function reject($callback)
		{
			$list = [];
			foreach ($this->items as $item) {
				if ($callback($item) === false)
					$list[] = $item;
			}

			$this->items = $list;
			unset($list);
			
			return $this;
		}

		/**
		 * Attract items.
		 * 
		 * @param  callable  $callback
		 * @return $this
		 */
		public function attract($callback)
		{
			$this->items = array_filter($this->items, $callback);
			return $this;
		}

		/**
		 * Serialize the object.
		 * 
		 * @return array
		 */
		public function jsonSerialize()
		{
			return $this->items;
		}

		/**
		 * Get the iterator.
		 * 
		 * @return \ArrayIterator
		 */
		public function getIterator()
		{
			return new ArrayIterator($this->items);
		}

		/**
		 * Get the items.
		 * 
		 * @param  string  $item
		 * @return array
		 */
		public function get($item = null)
		{
			if ($item === null)
				return $this->items;

			return $this->items[$item];
		}

		/**
		 * Add new item.
		 * 
		 * @param  array  $items
		 * @return void
		 */
		public function add($items)
		{
			$this->items = array_merge($this->items, $items);
		}

		/**
		 * Merge items.
		 * 
		 * @param  array|object  $collection
		 * @return void
		 */
		public function merge($collection)
		{
			if (is_array($collection))
				$items = $collection;
			elseif ($collection instanceof Collection)
				$items = $collection->get();

			$this->add($items);
		}

		/**
		 * {@inheritdoc}
		 */
	    public function offsetSet($offset, $value) {
	        if (is_null($offset)) {
	            $this->items[] = $value;
	        } else {
	            $this->items[$offset] = $value;
	        }
	    }

	    /**
		 * {@inheritdoc}
		 */
	    public function offsetExists($offset) {
	        return isset($this->items[$offset]);
	    }

	    /**
		 * {@inheritdoc}
		 */
	    public function offsetUnset($offset) {
	        unset($this->items[$offset]);
	    }

	    /**
		 * {@inheritdoc}
		 */
	    public function offsetGet($offset) {
	        return isset($this->items[$offset]) ? $this->items[$offset] : null;
	    }

		/**
		 * Get properties.
		 * 
		 * @param  string  $item
		 * @return string
		 */
		public function __get($item)
		{
			return $this->get($item);
		}

	}
