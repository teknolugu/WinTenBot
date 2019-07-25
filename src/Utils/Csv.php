<?php
/**
 * Created by IntelliJ IDEA.
 * User: Azhe
 * Date: 1/3/2019
 * Time: 9:54 PM
 */

namespace src\Utils;

class Csv
{
	public static function tulis($data)
	{
		$f = fopen('../Data/spell.csv', 'w');
		fputcsv($f, $data);
		fclose($f);
	}
	
	/**
	 * @param $file
	 * @param $writeTo
	 * @return false|string
	 */
	public static function ReadCsv($file, $writeTo)
	{
//		$lines = file($fn); // read file directly as an array of lines
//		array_pop($lines); // you can remove the last empty line (if required)
//		$json = json_encode(array_map('str_getcsv', $lines), JSON_NUMERIC_CHECK);
//		$csv= file_get_contents($fn);
//		$array = array_map('str_getcsv', explode("\n", $csv));
//		$json = json_encode($array);
		
		$fh = fopen($file, "r");
//
//		Setup a PHP array to hold our CSV rows.
		$csvData = [];
//
//		Loop through the rows in our CSV file and add them to
//		the PHP array that we created above.
		while (($row = fgetcsv($fh, 0, ",")) !== false) {
			$csvData[] = $row;
		}
//
//		Finally, encode our array into a JSON string format so that we can print it out.
		$json = json_encode($csvData, 128);

//		$csv= file_get_contents($file);
//		$array = array_map('str_getcsv', explode("\n", $csv));
//		$json = json_encode($array,128);
//
		file_put_contents($writeTo, $json);
		return $json;
	}
	
	public static function ConvertJson($fileName)
	{
		if (($handle = fopen($fileName, "r")) !== false) {
			$csvs = [];
			while (!feof($handle)) {
				$csvs[] = fgetcsv($handle);
			}
			$datas = [];
			$column_names = [];
			foreach ($csvs[0] as $single_csv) {
				$column_names[] = $single_csv;
			}
			
			foreach ($csvs as $key => $csv) {
				if ($key === 0) {
					continue;
				}
				foreach ($column_names as $column_key => $column_name) {
					$datas[$key - 1][$column_name] = $csv[$column_key];
				}
			}
			$json = json_encode($datas, 128);
			fclose($handle);
		}
		return $json;
	}
}
