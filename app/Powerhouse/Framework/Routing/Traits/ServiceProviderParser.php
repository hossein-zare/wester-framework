<?php

	namespace Powerhouse\Routing\Traits;

	use Powerhouse\Services\ServiceProvider;

	trait ServiceProviderParser
	{

		/**
		 * Parse the services.
		 * 
		 * @param  array  $services
		 * @return bool
		 */
		public function parseServices(array $services)
		{
			foreach ($services as $service) {
				$provider = new ServiceProvider($service);
				$provider->provide();
			}
		}

	}
