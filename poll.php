<?php
/**
 * Created by IntelliJ IDEA.
 * User: Azhe
 * Date: 3/12/2019
 * Time: 9:38 PM
 */

// Load composer
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Resources/Config/config.php';
require_once __DIR__ . '/Resources/Words/kosakata.php';

// Load all in /Resources dir
foreach (glob('Resources/*/*.php') as $files) {
	include_once $files;
}

// load all under folder src
foreach (glob('src/*/*.php') as $files) {
	include_once $files;
}

$commands_paths = [
	__DIR__ . '/Commands/SystemCommands',
	__DIR__ . '/Commands/UserCommands/Additional',
	__DIR__ . '/Commands/UserCommands/Bot',
	__DIR__ . '/Commands/UserCommands/CekResi',
	__DIR__ . '/Commands/UserCommands/Chat',
	__DIR__ . '/Commands/UserCommands/FedBan',
	__DIR__ . '/Commands/UserCommands/GitTools',
	__DIR__ . '/Commands/UserCommands/Group',
	__DIR__ . '/Commands/UserCommands/Labs',
	__DIR__ . '/Commands/UserCommands/Member',
	__DIR__ . '/Commands/UserCommands/Security',
	__DIR__ . '/Commands/UserCommands/Sudoer',
	__DIR__ . '/Commands/UserCommands/Tagging',
	__DIR__ . '/Commands/UserCommands/Texting',
];

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram(bot_token, bot_username);

    // Set custom Upload and Download paths
    $telegram->setDownloadPath(__DIR__ . '/Download');
    $telegram->setUploadPath(__DIR__ . '/Upload');

    // Handle telegram webhook request
    $telegram->addCommandsPaths($commands_paths);

    $telegram->useGetUpdatesWithoutDatabase();
//     Logging (Error, Debug and Raw Updates)
//     Longman\TelegramBot\TelegramLog::initDebugLog(__DIR__ . '/{bot_username}_debug.log');
//     Longman\TelegramBot\TelegramLog::initErrorLog(__DIR__ . '/{bot_username}_error.log');
//     Longman\TelegramBot\TelegramLog::initUpdateLog(__DIR__ . '/{bot_username}_update.log');

    while(true) {
        // Handle telegram getUpdates request
        $server_response = $telegram->handleGetUpdates();
//        if ($server_response->isOk()) {
//            $update_count = count($server_response->getResult());
//            echo date('Y-m-d H:i:s', time()) . ' - Processed ' . $update_count . ' updates';
//        } else {
//            echo date('Y-m-d H:i:s', time()) . ' - Failed to fetch updates' . PHP_EOL;
//            echo $server_response->printError();
//        }
    }

} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // Silence is golden!

}
