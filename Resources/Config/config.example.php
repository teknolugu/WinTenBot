<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 04/08/2018
 * Time: 22.50
 */

date_default_timezone_set('Asia/Jakarta');


define('descBot',"ℹ Official Telegram bot based on <b>WinTen API</b>.\nfor management & utility group");
define('descBeta',"\n<b>I'm still beta, maybe contains bug and unstable. not recomended for your group</b>");

define('botData','./Data/');

// WinTen API
define('winten_api', "https://api.winten.space/");
define('winten_key', "YOUR API TOKEN HERE");

// API key Thumbnail.ws
define('thumbws_token', 'YOUR API TOKEN HERE');

// API Bitly
define('bitly_username', 'azhe403');
define('bitly_token', 'YOUR API TOKEN HERE');

// Give special fully granted access for your bot
define('sudoer',[
    '236205726', // Azhe Kun
    '1234567890' // Your Sudoer
]);

// Allow your bot for this place only
define('restrictArea',[
    '-1001387872546', // TgBotID
    // Add more group here
]);

// DB Configuration
define('db_data', [
    'database_type' => 'mysql',
    'database_name' => 'your_db_bot',
    'server' => 'localhost',
    'username' => 'root',
    'password' => 'uvuvevwe_ossass',
]);


// Note: Incorrect configuration may causing some bot function not work properly
