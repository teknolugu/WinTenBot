<?php

namespace WinTenDev\Utils;

class Format
{
	/**
	 * @param     $bytes
	 * @param int $precision
	 * @return string
	 */
	public static function formatBytes($bytes, $precision = 2)
	{
		$units = ['B', 'KB', 'MB', 'GB', 'TB'];
		
		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);
		
		// Uncomment one of the following alternatives
		// $bytes /= pow(1024, $pow);
		// $bytes /= (1 << (10 * $pow));
		
		return round($bytes, $precision) . ' ' . $units[$pow];
	}
	
	/**
	 * @param     $size
	 * @param int $precision
	 * @return string
	 */
	public static function formatSize($size, $precision = 2)
	{
		$base = log($size, 1024);
		$suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];
		
		return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
	}
}
