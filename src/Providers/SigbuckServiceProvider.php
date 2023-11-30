<?php


namespace Sigbuck\LaravelSignalbucket\Providers;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Sigbuck\LaravelSignalbucket\Facades\Signalbucket;

class SigbuckServiceProvider extends ServiceProvider
{
	/**
	 * Register services.
	 */
	public function register(): void
	{
		$this->app->bind('Signalbucket', function () {
			return new SignalBucket();
		});
		$this->app->alias(Signalbucket::class, 'SignalBucket');
	}

	/**
	 * Bootstrap services.
	 */
	public function boot(): void
	{
		$this->setupConfig($this->app);
	}


	/**
	 * Setup the config.
	 *
	 * @param \Illuminate\Contracts\Container\Container $app
	 *
	 * @return void
	 */
	protected function setupConfig(Container $app): void
	{
		$source = realpath($raw = __DIR__ . '/../config/signalbucket.php') ?: $raw;
		$this->publishes([$source => config_path('signalbucket.php')]);
		$this->mergeConfigFrom($source, 'signalbucket');
	}
}
