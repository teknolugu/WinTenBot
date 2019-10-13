<?php

namespace WinTenDev\Utils;

class Unicode
{
	public static function unicode_decode($str)
	{
		return utf8_decode(preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $str));
	}
	
	public static function escape_sequence_decode($str)
	{
		// [U+D800 - U+DBFF][U+DC00 - U+DFFF]|[U+0000 - U+FFFF]
		$regex = '/\\\u([dD][89abAB][\da-fA-F]{2})\\\u([dD][c-fC-F][\da-fA-F]{2})
              |\\\u([\da-fA-F]{4})/sx';
		
		return preg_replace_callback($regex, function ($matches) {
			if (isset($matches[3])) {
				$cp = hexdec($matches[3]);
			} else {
				$lead = hexdec($matches[1]);
				$trail = hexdec($matches[2]);
				
				// http://unicode.org/faq/utf_bom.html#utf16-4
				$cp = ($lead << 10) + $trail + 0x10000 - (0xD800 << 10) - 0xDC00;
			}
			
			// https://tools.ietf.org/html/rfc3629#section-3
			// Characters between U+D800 and U+DFFF are not allowed in UTF-8
			if ($cp > 0xD7FF && 0xE000 > $cp) {
				$cp = 0xFFFD;
			}
			
			// https://github.com/php/php-src/blob/php-5.6.4/ext/standard/html.c#L471
			// php_utf32_utf8(unsigned char *buf, unsigned k)
			
			if ($cp < 0x80) {
				return chr($cp);
			} elseif ($cp < 0xA0) {
				return chr(0xC0 | $cp >> 6) . chr(0x80 | $cp & 0x3F);
			}
			
			return html_entity_decode('&#' . $cp . ';');
		}, $str);
	}
	
	public static function unicode2html($str)
	{
		$i = 65535;
		while ($i > 0) {
			$hex = dechex($i);
			$str = str_replace("\u$hex", "&#$i;", $str);
			$i--;
		}
		return $str;
	}
	
	private static function replace_unicode_escape_sequence($match)
	{
		return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
	}
}
