<?php

use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;
use WinTenDev\Utils\Arrays;
use WinTenDev\Utils\Inputs;

require_once __DIR__ . '/vendor/autoload.php';

$bots = include __DIR__ . "/Resources/Config/bots.php";
$input = Inputs::get("id");

foreach (glob('Resources/*/*.php') as $files) {
	include_once $files;
}

$filtered = Arrays::arrayFilter($bots, ['id' => $input]);

if (count($filtered) == 1) {
	global $bot_username;
	global $bot_token;
	global $is_beta;
	global $is_restricted;
	
	$bot_username = $filtered[0]['bot_username'];
	$bot_token = $filtered[0]['bot_token'];
	$is_beta = $filtered[0]['is_beta'];
	$is_restricted = $filtered[0]['is_restricted'];
	
	echo "<br>BotUsername: $bot_username";
	echo "<br>Token: $bot_token";
	
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

//	$commands_paths = include __DIR__ .'/Resources/Config/path-commands.php';
	
	try {
		$telegram = new Telegram($bot_token, $bot_username);
		
		// Set custom Upload and Download paths
		$telegram->setDownloadPath(__DIR__ . '/Data/Download');
		$telegram->setUploadPath(__DIR__ . '/Data/Upload');
		
		// Handle telegram webhook request
		$telegram->addCommandsPaths($commands_paths);
		
		// Handle Webhook Request
		$telegram->handle();
		
		// Enable Limiter
		$telegram->enableLimiter();
		
	} catch (TelegramException $e) {
		$error = [
			'Status'     => $e->getCode(),
			'Error'      => $e->getMessage(),
			'Stacktrace' => $e->getTrace(),
		];
		Arrays::toJson($error);
	}

} else {
	echo "<br>Something went wrong";
}
