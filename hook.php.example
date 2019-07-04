<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 04/08/2018
 * Time: 22.47
 */

require_once __DIR__ . '/vendor/autoload.php';

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
    __DIR__ . '/Commands/UserCommands/FedBan',
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

//     Logging (Error, Debug and Raw Updates)
//     Longman\TelegramBot\TelegramLog::initDebugLog(__DIR__ . '/{bot_username}_debug.log');
//     Longman\TelegramBot\TelegramLog::initErrorLog(__DIR__ . '/{bot_username}_error.log');
//     Longman\TelegramBot\TelegramLog::initUpdateLog(__DIR__ . '/{bot_username}_update.log');
	
	// Handle Webhook Request
	$telegram->handle();
	
	// Enable Limiter
	$telegram->enableLimiter();
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
	// Silence is golden!
	
}

echo 'Hook Worked!';
