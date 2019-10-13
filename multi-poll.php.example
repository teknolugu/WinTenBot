<?php

use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;
use WinTenDev\Utils\Arrays;
use WinTenDev\Utils\Console;

require_once 'vendor/autoload.php';

Console::println('Execute autoload');

Console::println('Loading bot configuration');
$bots = include __DIR__ . '/Resources/Config/bots.php';

Console::println('Get input param from CLI');
$input = $argv[1];

if($input == ''){
	throw new Exception('Parameter ID is needed.', 1);
}
Console::println('Loaded => ' . json_encode($input));

Console::println('Getting ready..');

foreach (glob('Resources/*/*.php') as $files) {
	Console::println("Loading $files");
	include_once $files;
}

$filtered = Arrays::arrayFilter($bots, ['id' => $input]);

if (count($filtered) == 1) {
	global $bot_username;
	global $bot_name;
	global $bot_token;
	global $is_beta;
	global $is_restricted;
	
	$bot_username = $filtered[0]['bot_username'];
	$bot_name = $filtered[0]['bot_name'];
	$bot_token = $filtered[0]['bot_token'];
	$is_beta = $filtered[0]['is_beta'];
	$is_restricted = $filtered[0]['is_restricted'];
	
	Console::println("BotUsername: $bot_username");
	
	$path_commands = [
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
		Console::println('Creating instance..');
		$telegram = new Telegram($bot_token, $bot_username);
		
		// Set custom Upload and Download paths
		$telegram->setDownloadPath(__DIR__ . '/Data/Download');
		$telegram->setUploadPath(__DIR__ . '/Data/Upload');
		
		// Handle telegram webhook request
		Console::println('Loading Bot command');
		$telegram->addCommandsPaths($path_commands);
		
		$telegram->useGetUpdatesWithoutDatabase();
		
		// Enable Limiter
		$telegram->enableLimiter();
		
		// Handle Poll Request
		Console::println('Bot is must ready!');
		while (true) {
			$telegram->handleGetUpdates();
		}
		
	} catch (TelegramException $e) {
		echo '<br>Error: ' . $e->getMessage();
		echo '<br>Stacktrace: ' . $e->getTraceAsString();
//		Logs::toChannel($e->getMessage());
	}
} else {
	Console::println('Something went wrong..');
}
