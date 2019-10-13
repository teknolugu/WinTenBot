<?php

namespace WinTenDev\Utils;

use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Folder
{
	/**
	 * @param $dir
	 * @return bool
	 */
	public static function deleteDir($dir)
	{
		try {
//		$dir = 'samples' . DIRECTORY_SEPARATOR . 'sampledirtree';
			$it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
			$files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
			foreach ($files as $file) {
				if ($file->isDir()) {
					rmdir($file->getRealPath());
				} else {
					unlink($file->getRealPath());
				}
			}
			rmdir($dir);
			$delete = true;
		} catch (Exception $ex) {
//			$delete = $ex->getMessage();
			$delete = false;
		}
		return $delete;
	}
	
	/**
	 * @param $fileName
	 * @return bool
	 */
	public static function deleteFile($fileName)
	{
		if (file_exists($fileName)) {
			$deleted = true;
			unlink($fileName);
		} else {
			$deleted = false;
		}
		return $deleted;
	}
	
	/**
	 * @param $dir
	 * @return bool
	 */
	public static function isExist($dir)
	{
		return file_exists($dir);
	}
}
