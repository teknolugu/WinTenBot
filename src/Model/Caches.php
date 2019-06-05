<?php

namespace src\Model;

use Cache;
use DirectoryIterator;

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
	
	/**
	 * Write cache
	 *
	 * @param [type] $key
	 * @param [type] $data
	 * @return void
	 */
	public function writeCache($path, $key, $data)
	{
		$path = botData . $path;
		$this->createDir($path);
		$json = \GuzzleHttp\json_encode($data, 128);
		$fileName = $path . '/' . $key . '.json';
		file_put_contents($fileName, $json);
	}
	
	/**
	 * Read cache
	 *
	 * @param $path
	 * @param $key
	 * @return mixed
	 */
	public function readCache($path, $key)
	{
		$json = [];
		$fileName = botData . $path . '/' . $key . '.json';
		if (file_exists($fileName)) {
			$json = file_get_contents($fileName);
		}
		return \GuzzleHttp\json_decode($json, true);
	}
	
	function createDir($dir)
	{
		if (!file_exists($dir)) {
			mkdir($dir, 0777, true);
		}
	}
	
	function rrmdir($path)
	{
		// Open the source directory to read in files
		if (file_exists($path)) {
			$i = new DirectoryIterator($path);
			foreach ($i as $f) {
				if ($f->isFile()) {
					unlink($f->getRealPath());
				} elseif (!$f->isDot() && $f->isDir()) {
					rrmdir($f->getRealPath());
				}
			}
			rmdir($path);
		}
	}
	
}
