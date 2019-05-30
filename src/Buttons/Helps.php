<?php
/**
 * Created by PhpStorm.
 * User: Azhe
 * Date: 2/16/2019
 * Time: 11:00 PM
 */

define('BTN_HELP_HOME', [
	['text' => 'Core', 'callback_data' => 'help_core'],
	['text' => 'Tambahan', 'callback_data' => 'help_additional'],
	['text' => 'Grup', 'callback_data' => 'help_group'],
	['text' => 'Federasi Ban', 'callback_data' => 'help_fedban'],
	['text' => 'Anggota', 'callback_data' => 'help_member'],
	['text' => 'Keamanan', 'callback_data' => 'help_security'],
	['text' => 'Olah Tag', 'callback_data' => 'help_tagging'],
	['text' => 'Naskah', 'callback_data' => 'help_texting'],
	['text' => 'Beranda', 'callback_data' => 'help_home'],
]);

define('BTN_HELP_CORE', [
	['text' => 'Debug', 'callback_data' => 'help_core/debug'],
	['text' => 'Bot Info', 'callback_data' => 'help_core/info'],
	['text' => 'Ping', 'callback_data' => 'help_core/ping'],
	['text' => 'Perjanjian Lisensi', 'callback_data' => 'help_core/terms'],
	['text' => 'Open source', 'callback_data' => 'help_core/src'],
	['text' => 'Beranda', 'callback_data' => 'help_home'],
]);

define('BTN_HELP_INFO', [
	['text' => '👥 WinTen Group', 'url' => 'https://t.me/WinTenGroup'],
	['text' => '❤ by WinTenDev', 'url' => 'https://t.me/WinTenChannel'],
	['text' => '👥 Redmi 5A (Riva) ID', 'url' => 'https://t.me/Redmi5AID'],
	['text' => '👥 Telegram Bot API', 'url' => 'https://t.me/TgBotID'],
	['text' => '💽 Source code', 'url' => 'https://github.com/WinTenDev/WinTenBot'],
	['text' => '🏗 Akmal Projext', 'url' => 'https://t.me/AkmalProjext'],
	['text' => 'Kembali', 'callback_data' => 'help_core'],
]);

define('BTN_HELP_MEMBER', [
	['text' => 'Ban', 'callback_data' => 'help_member/ban'],
	['text' => 'Kick', 'callback_data' => 'help_member/kick'],
	['text' => 'Promote', 'callback_data' => 'help_member/promote'],
	['text' => 'Demote', 'callback_data' => 'help_member/demote'],
	['text' => 'Beranda', 'callback_data' => 'help_home'],
]);

define('BTN_HELP_GROUP', [
	['text' => 'Admin', 'callback_data' => 'help_group/admin'],
	['text' => 'Sematkan', 'callback_data' => 'help_group/pin'],
	['text' => 'Polling', 'callback_data' => 'help_group/poll'],
	['text' => 'Rules', 'callback_data' => 'help_group/rules'],
	['text' => 'Pengaturan', 'callback_data' => 'help_group/setting'],
	['text' => 'Welcome', 'callback_data' => 'help_group/welcome'],
	['text' => 'Beranda', 'callback_data' => 'help_home'],
]);

define('BTN_HELP_TAGGING', [
	['text' => 'Tag', 'callback_data' => 'help_tag/tag'],
	['text' => 'UnTag', 'callback_data' => 'help_tag/untag'],
	['text' => 'Tags', 'callback_data' => 'help_tag/tags'],
	['text' => 'Refactoring', 'callback_data' => 'help_tag/refactor'],
	['text' => 'Export', 'callback_data' => 'help_tag/export'],
	['text' => 'Beranda', 'callback_data' => 'help_home'],
]);

define('BTN_HELP_TEXTING', [
	['text' => 'Panjang Teks', 'callback_data' => 'help_texting/length'],
	['text' => 'Pemendek tautan', 'callback_data' => 'help_texting/short'],
	['text' => 'Pengejaan', 'callback_data' => 'help_texting/spelling'],
	['text' => 'Terjemahan', 'callback_data' => 'help_texting/translation'],
	['text' => 'Beranda', 'callback_data' => 'help_home'],
]);

define('BTN_HELP_FEDBAN', [
	['text' => 'Registrasi FedBan', 'callback_data' => 'help_fedban/reg'],
	['text' => 'Unregistrasi FedBan', 'callback_data' => 'help_fedban/unreg'],
	['text' => 'Ban Federasi', 'callback_data' => 'help_fedban/ban'],
	['text' => 'UnBan Federasi', 'callback_data' => 'help_fedban/unban'],
	['text' => 'Beranda', 'callback_data' => 'help_home'],
]);

define('BTN_HELP_ADDITIONAL', [
	['text' => 'Cek Resi', 'callback_data' => 'help_additional/cekresi'],
	['text' => 'Generate Qr', 'callback_data' => 'help_additional/qrgen'],
	['text' => 'Gambar kucing', 'callback_data' => 'help_additional/kucing'],
	['text' => 'Web Screenshot', 'callback_data' => 'help_additional/web/screenshot'],
	['text' => 'Beranda', 'callback_data' => 'help_home'],
]);
