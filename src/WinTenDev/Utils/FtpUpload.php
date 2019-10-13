<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 06/08/2018
 * Time: 14.37
 */

namespace WinTenDev\Utils;

class FtpUpload
{
//    public static function unggah($file){
//        try {
//            $ftp = new Ftp;
//            $ftp->connect($this->host);
//            $ftp->login($this->user,$this->pass);
//            $ftp->put($this->dir,$file, FTP_BINARY);
//            $ftp->close();
//        } catch (\Exception $e) {
//            // Apa
//        }
//    }

    /**
     * @param $file
     * @return int
     */
    public static function aplod($file)
    {
        $ftp = ftp_connect(host, 21, 30);
        ftp_login($ftp, user, pass);

        $ret = ftp_nb_put($ftp, dir, $file, FTP_BINARY, FTP_AUTORESUME);

        while ($ret === FTP_MOREDATA) {
            $ret = ftp_nb_continue($ftp);
        }

        return $ret;

    }


}
