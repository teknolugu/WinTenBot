<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 04/08/2018
 * Time: 22.47
 */

// Load composer
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/Waktu.php';
require_once __DIR__ . '/app/Terjemah.php';

$bot_token = '363445341:AAE25Ahnl5Z2DmavrzxFRqqbEkcS_susLOI';
$bot_username = 'AzheBot';

$commands_paths = [
    __DIR__ . '/Commands/',
];

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($bot_token, $bot_username);

    // Set custom Upload and Download paths
    $telegram->setDownloadPath(__DIR__ . '/Download');
    $telegram->setUploadPath(__DIR__ . '/Upload');

    // Handle telegram webhook request
    $telegram->addCommandsPaths($commands_paths);

    // Enable admin users
    $telegram->enableAdmins([
        '236205726'
    ]);

    // Logging (Error, Debug and Raw Updates)
    // Longman\TelegramBot\TelegramLog::initErrorLog(__DIR__ . "/{$bot_username}_error.log");
    // Longman\TelegramBot\TelegramLog::initDebugLog(__DIR__ . "/{$bot_username}_debug.log");
    // Longman\TelegramBot\TelegramLog::initUpdateLog(__DIR__ . "/{$bot_username}_update.log");

    // Handle Webhook Request
    $telegram->handle();

} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // Silence is golden!
    // log telegram errors
    echo $e->getMessage();
} catch (\Longman\TelegramBot\Exception\TelegramLogException $e) {
    echo $e->getMessage();
}