<?php

namespace src\Model;

use Cache;

class Caches
{
	public static function write($pathCache, $keyCache, $valueCache)
	{
		$cache = new Cache(cache_php);
		$cache->setCache($pathCache); // generate new file
		$cache->store($keyCache, $valueCache); // store data string
	}
	
	public static function read($pathCache, $keyCache)
	{
		$cache = new Cache(cache_php);
		$cache->setCache($pathCache); // generate new file
		return $cache->retrieve($keyCache);
	}
}
