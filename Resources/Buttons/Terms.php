<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 1/20/2019
 * Time: 11:26 PM
 */

$urlStart = 'https://t.me/' . bot_username . '?start=';

$terms_prefix = 'start_terms_';

define('BTN_TERMS_WITHOUT_CALLBACK', [
	['text' => 'End User License', 'url' => $urlStart . 'eula'],
	['text' => 'Privacy Policy', 'url' => $urlStart],
	['text' => 'Terms Of Use', 'url' => $urlStart],
	['text' => 'Open Source', 'url' => $urlStart . 'opensource'],
	['text' => 'Documentation', 'url' => $urlStart],
	['text' => 'How to set Username', 'url' => $urlStart . 'username'],
]);

define('BTN_TERMS_WITH_CALLBACK', [
	['text' => 'End User License', 'callback_data' => $terms_prefix . 'eula'],
	['text' => 'Privacy Policy', 'callback_data' => $terms_prefix],
	['text' => 'Terms Of Use', 'callback_data' => $terms_prefix],
	['text' => 'Open Source', 'callback_data' => $terms_prefix . 'opensource'],
	['text' => 'Documentation', 'callback_data' => $terms_prefix],
	['text' => 'How to set Username', 'callback_data' => $terms_prefix . 'username'],
]);
