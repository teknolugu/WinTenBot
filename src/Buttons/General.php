<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 2/9/2019
 * Time: 9:24 PM
 */

define('BTN_OK_ONLY', [
	['text' => 'OK', 'callback_data' => 'general_ok'],
]);

define('BTN_OK_NO_CANCEL', [
	['text' => 'OK', 'callback_data' => 'general_ok'],
	['text' => 'No', 'callback_data' => 'general_no'],
	['text' => 'Cancel', 'callback_data' => 'general_cancel'],
]);
