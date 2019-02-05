<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 8/24/2018
 * Time: 11:24 PM
 */

namespace src\Utils;

class Files
{
    /**
     *  Get the file size of any remote resource (using get_headers()),
     *  either in bytes or - default - as human-readable formatted string.
     *
     * @author  Stephan Schmitz <eyecatchup@gmail.com>
     * @license MIT <http://eyecatchup.mit-license.org/>
     * @url     <https://gist.github.com/eyecatchup/f26300ffd7e50a92bc4d>
     *
     * @param   Words   $url Takes the remote object's URL.
     * @param   boolean $formatSize Whether to return size in bytes or formatted.
     * @param   boolean $useHead Whether to use HEAD requests. If false, uses GET.
     * @return  Words                 Returns human-readable formatted size
     *                                  or size in bytes (default: formatted).
     */
    public static function getRemoteFilesize($url, $formatSize = true, $useHead = true)
    {
        if (false !== $useHead) {
            stream_context_set_default(array('http' => array('method' => 'HEAD')));
        }
        $head = array_change_key_case(get_headers($url, 1));
        // content-length of download (in bytes), read from Content-Length: field
        $clen = isset($head['content-length']) ? $head['content-length'] : 0;

        // cannot retrieve file size, return "-1"
        if (!$clen) {
            return -1;
        }

        if (!$formatSize) {
            return $clen; // return size in bytes
        }

        $size = $clen;
        switch ($clen) {
            case $clen < 1024:
                $size = $clen . ' B';
                break;
            case $clen < 1048576:
                $size = round($clen / 1024, 2) . ' KB';
                break;
            case $clen < 1073741824:
                $size = round($clen / 1048576, 2) . ' MB';
                break;
            case $clen < 1099511627776:
                $size = round($clen / 1073741824, 2) . ' GB';
                break;
        }

        return $size; // return formatted size
    }
}
