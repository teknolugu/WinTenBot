<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 06/08/2018
 * Time: 21.14
 */

namespace src\Utils;

use Exception;
use GuzzleHttp\Client;
use Sastrawi\Stemmer\StemmerFactory;
use Summarizer\Summarizer;

class Words
{
	public static function substrteks($text, $limit, $end = '...')
	{
		if (mb_strwidth($text, 'UTF-8') <= $limit) {
			return $text;
		}
		
		return rtrim(mb_strimwidth($text, 0, $limit, '', 'UTF-8')) . $end;
	}
	
	public static function substrkata($text, $maxchar, $end = '...')
	{
		if (strlen($text) > $maxchar || $text == '') {
			$words = preg_split('/\s/', $text);
			$output = '';
			$i = 0;
			while (1) {
				$length = strlen($output) + strlen($words[$i]);
				if ($length > $maxchar) {
					break;
				} else {
					$output .= ' ' . $words[$i];
					++$i;
				}
			}
			$output .= $end;
		} else {
			$output = $text;
		}
		return $output;
	}
	
	/**
	 * @param     $teks
	 * @param int $index
	 * @return mixed
	 */
	public static function extrlink($teks, $index = 0)
	{
		$pattern = '~[a-z]+://\S+~';
		preg_match_all($pattern, $teks, $out);
		return $out[0][$index];
		//return explode(' ', strstr($teks, 'https://'))[$index];
	}
	
	public static function extrlinkArr($teks)
	{
		$pattern = '~[a-z]+://\S+~';
		preg_match_all($pattern, $teks, $out);
		return $out[0];
	}
	
	public static function addhttp($url)
	{
		if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
			$url = "http://" . $url;
		}
		return $url;
	}
	
	public static function shortUrl($url)
	{
		return json_decode(file_get_contents("http://api.bit.ly/v3/shorten?login=" .
			bitly_username . "&apiKey=" . bitly_token . "&longUrl=" . urlencode($url) .
			"&format=json"))->data->url;
	}
	
	public static function isSameWith($message, $word)
	{
		$result = false;
		$mssgArr = explode(' ', $message);
		if (is_array($word)) {
			foreach ($word as $anu) {
				if (in_array($anu, $mssgArr)) {
					$result = true;
				}
			}
		} elseif ($message === $word) {
			$result = true;
		}
		return $result;
	}
	
	public static function isContain($message, $word)
	{
		$result = false;
		if (is_array($word)) {
			foreach ($word as $anu) {
				if (strpos($message, $anu) !== false) {
					$result = true;
				}
			}
		} elseif (strpos($message, $word) !== false) {
			$result = true;
		}
		return $result;
	}
	
	public static function isBadword($pesan)
	{
		$apesan = explode(' ', $pesan);
		foreach ($apesan as $anu) {
			foreach (self::listBadword() as $kata) {
				if (self::isSameWith($anu, $kata['kata'])) {
					return true;
				}
			}
		}
	}
	
	public static function listBadword()
	{
		$file = botData . 'badword.json';
		$json = file_get_contents($file);
		return json_decode($json, true)['message'];
	}
	
	public static function allBadword()
	{
		$data = '';
		foreach (self::listBadword() as $kata) {
			$data .= '<code>' . $kata['kata'] . ' -> ' . $kata['kelas'] . '</code>, ';
		}
		return $data;
	}
	
	public static function tambahKata($datas)
	{
		$uri = winten_api . 'kata/?api_token=' . winten_key;
		$client = new Client();
		$response = $client->request('POST', $uri, [
			'form_params' => $datas,
		]);
		
		return $response->getBody();
	}
	
	public static function hapusKata($kata)
	{
		$uri = winten_api . 'kata/' . $kata . '?api_token=' . winten_key;
		$client = new Client(['base_url' => $uri]);
		$response = $client->delete($uri);
		return $response->getBody();
	}
	
	public static function simpanJson()
	{
		$file = botData . 'badword.json';
		$url = winten_api . 'kata/?api_token=' . winten_key;
		$json = file_get_contents($url);
		file_put_contents($file, $json);
	}
	
	public static function cleanAlpaNum($teks)
	{
		return preg_replace('/[^[:alnum:]]/', '', $teks);
	}
	
	public static function resolveVariable($string, $replacement)
	{
		$string_processed = preg_replace_callback(
			'~\{\$(.*?)\}~si',
			function ($match) use ($replacement) {
				return str_replace($match[0], isset($replacement[$match[1]])
					? $replacement[$match[1]]
					: $match[0], $match[0]);
			},
			$string);
		return $string_processed;
	}
	
	public static function randomizeCase($str)
	{
		for ($i = 0, $c = strlen($str); $i < $c; $i++)
			$str[$i] = (rand(0, 10) > 5
				? strtoupper($str[$i])
				: strtolower($str[$i]));
		
		return $str;
	}
	
	public static function multiexplode($delimiters, $string)
	{
		$ready = str_replace($delimiters, $delimiters[0], $string);
		$launch = explode($delimiters[0], $ready);
		return $launch;
	}
	
	public static function clearAlphaNum($text)
	{
		return preg_replace("/[^a-zA-Z0-9\s]+/", "", $text);
	}
	
	public static function stemText($text)
	{
		$stemmerFactory = new StemmerFactory();
		$stemmer = $stemmerFactory->createStemmer();
		$result = $stemmer->stem($text);
		return $result;
	}
	
	public static function rangkumText($text)
	{
		$summarizer = new Summarizer();
		$sentences = $summarizer->summarize($text);
		return $sentences;
	}

//	public static function
}
