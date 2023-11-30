<?php
namespace Sigbuck\LaravelSignalbucket\Facades;
use Illuminate\Support\Facades\Facade;
use Sigbuck\LaravelSignalbucket\Classes\SignalBucketer as SigBuck;

class SignalBucket extends Facade
{
    protected static function getFacadeAccessor(): string
	{
        return SigBuck::class;
    }
}
