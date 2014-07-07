<?php namespace Pingpong\Facebook\Facades;

use Illuminate\Support\Facades\Facade;

class Facebook extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'pingpong.facebook';
	}
}