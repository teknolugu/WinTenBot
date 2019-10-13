<?php

namespace WinTenDev\Utils;

use DirectoryIterator;

class Caches
{
	/**
	 * Write cache
	 *
	 * @param $path
	 * @param $key
	 * @param $data
	 * @return bool|int
	 */
	public function writeCache($path, $key, $data)
	{
		$path = botData . $path;
		$this->createDir($path);
		$json = \GuzzleHttp\json_encode($data, 128);
		$fileName = $path . '/' . $key . '.json';
		return file_put_contents($fileName, $json);
	}
	
	/**
	 * Read cache
	 *
	 * @param $path
	 * @param $key
	 * @return array
	 */
	public function readCache(string $path, string $key)
	{
		$json = [];
		$fileName = botData . $path . '/' . $key . '.json';
		if (file_exists($fileName)) {
			$json = file_get_contents($fileName);
		}
		
		if(is_array($json)) {
			return null;
		}
		
		return \GuzzleHttp\json_decode($json, true);
	}
	
	function createDir($dir)
	{
		if (!file_exists($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
			throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
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
