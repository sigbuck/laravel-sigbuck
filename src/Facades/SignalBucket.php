<?php

namespace Sigbuck\LaravelSignalbucket\Facades;

use Illuminate\Support\Facades\Facade;
use Sigbuck\LaravelSignalbucket\Classes\SignalBucketer as SigBuck;

/**
 * @method static SignalBucketer success(string $message, string $title = "")
 * @method static SignalBucketer info(string $message, string $title = "")
 * @method static SignalBucketer broadcast(string $message, string $title = "")
 * @method static SignalBucketer warning(string $message, string $title = "")
 * @method static SignalBucketer critical(string $message, string $title = "")
 * @method static SignalBucketer withURL(string $url, string $btn_text)
 * @method static SignalBucketer withData(array $arr)
 */
class SignalBucket extends Facade
{
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor(): string
	{
		return SigBuck::class;
	}
}